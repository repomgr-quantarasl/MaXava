<?php $webinfo= $this->webinfo;
$storeinfo=$this->settinginfo;
$currency=$this->storecurrency;
$activethemeinfo=$this->themeinfo;
$acthemename=$activethemeinfo->themename;
?>
<link href="<?php echo base_url('application/views/themes/'.$acthemename.'/assets_web/css/vieworder.css') ?>" rel="stylesheet" type="text/css"/>
        <div class="container wow fadeIn">
            <div class="row">
                <!-- Back Button Section -->
                <div class="col-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?php echo $back_url; ?>" class="btn btn-primary">
                            <i class="fa fa-arrow-left"></i> <?php echo $back_text; ?>
                        </a>
                        <div>
                            <span class="badge badge-<?php echo ($orderinfo->order_status == 4) ? 'success' : (($orderinfo->order_status == 1) ? 'warning' : 'info'); ?> p-2">
                                <?php 
                                switch($orderinfo->order_status) {
                                    case 1: echo 'Pending'; break;
                                    case 2: echo 'Processing'; break;
                                    case 3: echo 'Ready'; break;
                                    case 4: echo 'Completed'; break;
                                    case 5: echo 'Cancelled'; break;
                                    default: echo 'Unknown'; break;
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="panel panel-bd">
                        <div class="panel-footer text-right">
                            <a class="btn btn-info" onclick="printDiv('printableArea')" title="Print">
                                <span class="fa fa-print"></span> Print Invoice
                            </a>
                        </div>
                        <div id="printableArea">
                            <div class="panel-body">
                                <!-- Invoice Header -->
                                <div class="row">
                                    <div class="col-xl-6 col-md-6">
                                        <img src="<?php echo base_url();?><?php echo $storeinfo->logo?>" class="img img-responsive vieworder_mr_b" alt="" >
                                        <br>
                                        <span class="label label-success-outline m-r-15 p-10"><?php echo display('billing_from') ?></span>
                                        <address class="vieworder_mr_top">
                                            <strong><?php echo $storeinfo->storename;?></strong><br>
                                            <?php echo $storeinfo->address;?><br>
                                            <abbr><?php echo display('mobile') ?>:</abbr> <?php echo $storeinfo->phone;?><br>
                                            <abbr><?php echo display('email') ?>:</abbr> 
                                            <?php echo $storeinfo->email;?><br>
                                        </address>
                                    </div>
                                    <div class="col-xl-6 col-md-6 text-right">
                                        <h2 class="m-t-0"><?php echo display('invoice') ?></h2>
                                        <div><strong>Order ID:</strong> <?php echo $orderinfo->order_id;?></div>
                                        <div><strong><?php echo display('invoice_no') ?>:</strong> <?php echo $orderinfo->saleinvoice;?></div>
                                        <div class="m-b-15"><strong><?php echo display('billing_date') ?>:</strong> <?php echo date('d M Y, h:i A', strtotime($orderinfo->order_date));?></div>
                                        <?php if(!empty($paymentmethod)): ?>
                                        <div class="m-b-15"><strong>Payment Method:</strong> 
                                            <span class="label label-info"><?php echo $paymentmethod->payment_method;?></span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <!-- M-Pesa Transaction Info -->
                                        <?php if(!empty($mpesa_transaction)): ?>
                                        <div class="m-b-15">
                                            <strong>M-Pesa Transaction:</strong><br>
                                            <small>ID: <?php echo $mpesa_transaction->mpesa_transaction_id; ?></small><br>
                                            <small>Phone: <?php echo $mpesa_transaction->phone_number; ?></small>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <span class="label label-success-outline m-r-15"><strong><?php echo display('billing_to') ?></strong></span>
                                        <address class="vieworder_mr_top">  
                                            <?php echo $customerinfo->customer_name;?><br>
                                            <abbr><?php echo display('address') ?>:</abbr>
                                            <c class="vieworder_mr_w"><?php echo $customerinfo->customer_address;?></c><br>
                                            <abbr><?php echo display('mobile') ?>:</abbr><?php echo $customerinfo->customer_phone;?></abbr>
                                            <br>
                                            <abbr><?php echo display('email') ?>:</abbr><?php echo $customerinfo->customer_email;?>
                                        </address>
                                    </div>
                                </div> 
                                <hr>

                                <!-- Order Items Table -->
                                <div class="table-responsive m-b-20">
                                    <table class="table table-fixed table-bordered table-hover bg-white" id="orderTable">
                                        <thead class="thead-light">
                                             <tr>
                                                    <th class="text-center">Item</th>
                                                    <th class="text-center">Size</th>
                                                    <th class="text-center vieworder_w_100px">Unit Price</th> 
                                                    <th class="text-center vieworder_w_100px">Qty</th> 
                                                    <th class="text-center">Total Price</th> 
                                                </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i=0; 
                                              $totalamount=0;
                                                  $subtotal=0;
                                                  $total=$orderinfo->totalamount;
                                            foreach ($iteminfo as $item){
                                                $i++;
                                                $itemprice= $item->price*$item->menuqty;
                                                $discount=0;
                                                $adonsprice=0;
                                                if(!empty($item->add_on_id)){
                                                    $addons=explode(",",$item->add_on_id);
                                                    $addonsqty=explode(",",$item->addonsqty);
                                                    $x=0;
                                                    foreach($addons as $addonsid){
                                                            $adonsinfo=$this->hungry_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
                                                            $adonsprice=$adonsprice+$adonsinfo->price*$addonsqty[$x];
                                                            $x++;
                                                        }
                                                    $nittotal=$adonsprice;
                                                    }
                                                else{
                                                    $nittotal=0;
                                                    $text='';
                                                    }
                                                 $totalamount=$totalamount+$nittotal;
                                                 $subtotal=$subtotal+$item->price*$item->menuqty;
                                            ?>
                                            <tr>
                                                <td>
                                             	<?php echo $item->ProductName;?>
                                                </td>
                                                <td>
                                                <?php echo $item->variantName;?>
                                                </td>
                                                <td class="text-right"><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php echo number_format($item->price, 2);?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </td>
                                                <td class="text-right"><?php echo $item->menuqty;?></td>
                                                <td class="text-right"><strong><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php echo number_format($itemprice, 2);?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </strong></td>
                                             </tr>
                                            <?php 
                                            if(!empty($item->add_on_id)){
                                                $y=0;
                                                    foreach($addons as $addonsid){
                                                            $adonsinfo=$this->hungry_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
                                                            $adonsprice=$adonsprice+$adonsinfo->price*$addonsqty[$y];?>
                                                            <tr>
                                                                <td colspan="2" style="padding-left: 30px;">
                                                                <i class="fa fa-plus text-muted"></i> <?php echo $adonsinfo->add_on_name;?>
                                                                </td>
                                                                <td class="text-right"><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php echo number_format($adonsinfo->price, 2);?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </td>
                                                                <td class="text-right"><?php echo $addonsqty[$y];?></td>
                                                                <td class="text-right"><strong><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php echo number_format($adonsinfo->price*$addonsqty[$y], 2);?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </strong></td>
                                                             </tr>
                                            <?php $y++;
                                                        }
                                                 }
                                            }
                                             $itemtotal=$totalamount+$subtotal;
                                             $calvat=0;
                                             if(!empty($billinfo)) {
                                                 $calvat=$billinfo->VAT;
                                             }
                                             ?>
                                            <!-- Order Summary -->
                                            <tr style="background-color: #f8f9fa;">
                                            	<td class="text-right" colspan="4"><strong>Subtotal</strong></td>
                                                <td class="text-right"><strong><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php echo number_format($itemtotal, 2);?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </strong></td>
                                            </tr>
                                            <tr>
                                            	<td class="text-right" colspan="4"><strong>Discount</strong></td>
                                                <td class="text-right"><strong><?php if($currency->position==1){echo $currency->curr_icon;}?>  <?php $discount=0; if(empty($billinfo)){ echo number_format($discount, 2);} else{echo number_format($discount=$billinfo->discount, 2);} ?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </strong></td>
                                            </tr>
                                            <tr>
                                            	<td class="text-right" colspan="4"><strong>Service Charge</strong></td>
                                                <td class="text-right"><strong><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php $servicecharge=0; if(empty($billinfo)){ echo number_format($servicecharge, 2);} else{echo number_format($servicecharge=$billinfo->service_charge, 2);} ?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </strong></td>
                                            </tr>
                                            <tr>
                                            	<td class="text-right" colspan="4"><strong>VAT</strong></td>
                                                <td class="text-right"><strong><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php echo number_format($calvat, 2); ?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </strong></td>
                                            </tr>
                                            <tr style="background-color: #e8f5e8; font-size: 1.1em;">
                                            	<td class="text-right" colspan="4"><strong>Grand Total</strong></td>
                                                <td class="text-right"><strong><?php if($currency->position==1){echo $currency->curr_icon;}?> <?php echo number_format($calvat+$itemtotal+$servicecharge-$discount, 2);?> <?php if($currency->position==2){echo $currency->curr_icon;}?> </strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Order Status and Actions -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <h5>Order Status</h5>
                                        <div class="progress mb-3">
                                            <div class="progress-bar <?php echo ($orderinfo->order_status >= 4) ? 'bg-success' : (($orderinfo->order_status >= 2) ? 'bg-warning' : 'bg-info'); ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo ($orderinfo->order_status * 25); ?>%">
                                                <?php echo ($orderinfo->order_status * 25); ?>%
                                            </div>
                                        </div>
                                        <ul class="list-unstyled">
                                            <li><i class="fa fa-check <?php echo ($orderinfo->order_status >= 1) ? 'text-success' : 'text-muted'; ?>"></i> Order Placed</li>
                                            <li><i class="fa fa-check <?php echo ($orderinfo->order_status >= 2) ? 'text-success' : 'text-muted'; ?>"></i> Processing</li>
                                            <li><i class="fa fa-check <?php echo ($orderinfo->order_status >= 3) ? 'text-success' : 'text-muted'; ?>"></i> Ready</li>
                                            <li><i class="fa fa-check <?php echo ($orderinfo->order_status >= 4) ? 'text-success' : 'text-muted'; ?>"></i> Completed</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <h5>Order Information</h5>
                                        <p><strong>Order Date:</strong> <?php echo date('d M Y, h:i A', strtotime($orderinfo->order_date)); ?></p>
                                        <?php if(!empty($orderinfo->table_no)): ?>
                                        <p><strong>Table No:</strong> <?php echo $orderinfo->table_no; ?></p>
                                        <?php endif; ?>
                                        <p><strong>Order Type:</strong> 
                                            <?php echo ($orderinfo->cutomertype == 1) ? 'Dine In' : (($orderinfo->cutomertype == 2) ? 'Takeaway' : (($orderinfo->cutomertype == 3) ? 'Delivery' : 'Online')); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Thank You Message -->
                                <div class="text-center mt-4 p-3" style="background-color: #f8f9fa; border-radius: 5px;">
                                    <h4 class="text-success">Thank You for Your Order!</h4>
                                    <p class="mb-0">We appreciate your business and hope you enjoyed your meal.</p>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>

<!-- Print functionality -->
<script>
function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>

<style>
.vieworder_mr_b { margin-bottom: 15px; }
.vieworder_mr_top { margin-top: 10px; }
.vieworder_mr_w { word-wrap: break-word; }
.vieworder_w_100px { width: 100px; }

@media print {
    .btn, .no-print { display: none !important; }
    .panel-footer { display: none !important; }
    body { background: white !important; }
    .container { max-width: none !important; }
}

.label-success-outline {
    color: #5cb85c;
    border: 1px solid #5cb85c;
    background: transparent;
    padding: 4px 8px;
    border-radius: 3px;
}

.badge { font-size: 0.9em; }
.progress { height: 20px; }
</style>