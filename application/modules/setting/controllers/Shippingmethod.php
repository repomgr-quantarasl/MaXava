<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shippingmethod extends MX_Controller {

    public $settinginfo = '';
    public $storecurrency = '';

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SESSION sql_mode = ""');
        $this->load->model(array(
            'shipping_model',
            'logs_model'
        ));
        
        // Load currency settings for displaying rates
        $this->settinginfo = $this->db->select('*')->from('setting')->get()->row();
        $this->storecurrency = $this->db->select('*')->from('currency')->where('currencyid', $this->settinginfo->currency)->get()->row();
    }

    public function index($id = null)
    {
        $this->permission->method('setting','read')->redirect();
        $data['title'] = display('shipping_list');

        // Pagination setup
        $config["base_url"]   = base_url('setting/shippingmethod/index');
        $config["total_rows"] = $this->shipping_model->countlist();
        $config["per_page"]   = 25;
        $config["uri_segment"] = 4;

        $config["last_link"]  = "Last"; 
        $config["first_link"] = "First"; 
        $config['next_link']  = 'Next';
        $config['prev_link']  = 'Prev';  

        $config['full_tag_open']  = "<ul class='pagination pull-right'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open']   = '<li>';
        $config['num_tag_close']  = '</li>';
        $config['cur_tag_open']   = "<li class='active'><a href='#'>";
        $config['cur_tag_close']  = "</a></li>";
        $config['next_tag_open']  = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open']  = "<li>";
        $config['prev_tag_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tag_close'] = "</li>";
        $config['last_tag_open']  = "<li>";
        $config['last_tag_close'] = "</li>";

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

        $data["shippinglist"] = $this->shipping_model->read($config["per_page"], $page);
        $data["links"]        = $this->pagination->create_links();
        $data['paymentinfo']  = $this->shipping_model->read_all('*', 'payment_method', 'payment_method_id', '', 'is_active', '1');

        if (!empty($id)) {
            $data['title']   = display('shipping_edit');
            $data['intinfo'] = $this->shipping_model->findById($id);
        }

        $data['module'] = "setting";
        $data['page']   = "shippinglist";   
        echo Modules::run('template/layout', $data); 
    }

    public function create($id = null)
    {
        $this->permission->method('setting','create')->redirect();
        $data['title'] = display('shipping_add');

        // Form validation
        $this->form_validation->set_rules('shipping', display('payment_name'), 'required|max_length[50]');
        $this->form_validation->set_rules('shippingrate', display('shippingrate'), 'required|numeric');
        $this->form_validation->set_rules('rate_type', 'Rate Type', 'required');
        $this->form_validation->set_rules('status', display('status'), 'required');

        $pmethod      = $this->input->post('paymentmethod', true);
        $allpayments  = !empty($pmethod) ? implode(', ', $pmethod) : null;
        $rate_type    = $this->input->post('rate_type', true);
        $shippingrate = $this->input->post('shippingrate', true);

        // Custom validation: percentage must be between 0-100
        if ($rate_type == 'percentage' && ($shippingrate < 0 || $shippingrate > 100)) {
            $this->session->set_flashdata('exception', 'Shipping rate must be between 0 and 100 for percentage type');
            redirect("setting/shippingmethod/index");
        }

        $postData = [
            'ship_id'         => $this->input->post('ship_id'),
            'shipping_method' => $this->input->post('shipping', true),
            'shippingrate'    => $shippingrate,
            'rate_type'       => $rate_type,
            'payment_method'  => $allpayments,
            'shiptype'        => $this->input->post('shippintype', true),
            'is_active'       => $this->input->post('status', true),
        ];

        if ($this->form_validation->run()) {
            if (empty($this->input->post('ship_id'))) {
                // Insert
                $this->permission->method('setting','create')->redirect();
                if ($this->shipping_model->create($postData)) { 
                    $this->_save_log("Insert Data", "New Shipping Method Created");
                    $this->session->set_flashdata('message', display('save_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
            } else {
                // Update
                $this->permission->method('setting','update')->redirect();
                if ($this->shipping_model->update($postData)) {
                    $this->_save_log("Update Data", "Shipping Method Updated");
                    $this->session->set_flashdata('message', display('update_successfully'));
                } else {
                    $this->session->set_flashdata('exception', display('please_try_again'));
                }
            }
            redirect("setting/shippingmethod/index");  
        } else {
            if (!empty($id)) {
                $data['title']   = display('shipping_edit');
                $data['intinfo'] = $this->shipping_model->findById($id);
            }
            $data['module'] = "setting";
            $data['page']   = "shippinglist";   
            echo Modules::run('template/layout', $data); 
        }
    }

    public function updateintfrm($id)
    {
        $this->permission->method('setting','update')->redirect();
        $data['title']       = display('shipping_edit');
        $data['intinfo']     = $this->shipping_model->findById($id);
        $data['paymentinfo'] = $this->shipping_model->read_all('*', 'payment_method', 'payment_method_id', '', 'is_active', '1');
        $data['module']      = "setting";  
        $data['page']        = "shippingedit";
        $this->load->view('setting/shippingedit', $data);   
    }

    public function delete($id = null)
    {
        $this->permission->module('setting','delete')->redirect();
        if ($this->shipping_model->delete($id)) {
            $this->_save_log("Delete Data", "Shipping Method Deleted");
            $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }
        redirect('setting/shippingmethod/index');
    }

    private function _save_log($action, $remarks)
    {
        $logData = [
            'action_page' => "Shipping Method List",
            'action_done' => $action,
            'remarks'     => $remarks,
            'user_name'   => $this->session->userdata('fullname'),
            'entry_date'  => date('Y-m-d H:i:s'),
        ];
        $this->logs_model->log_recorded($logData);
    }
}
