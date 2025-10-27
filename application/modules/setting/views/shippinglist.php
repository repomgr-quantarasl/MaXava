<div class="form-group text-right">
 <?php if($this->permission->method('setting','create')->access()): ?>
<button type="button" class="btn btn-primary btn-md" data-target="#add0" data-toggle="modal"  ><i class="fa fa-plus-circle" aria-hidden="true"></i>
<?php echo display('shipping_add')?></button> 
<?php endif; ?>

</div>
<div id="add0" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <strong><?php echo display('shipping_add');?></strong>
            </div>
            <div class="modal-body">
           
<div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="panel">
               
                <div class="panel-body">

<?php echo form_open('setting/shippingmethod/create') ?>
<?php echo form_hidden('ship_id', (!empty($intinfo->ship_id)?$intinfo->ship_id:null)) ?>

<div class="form-group row">
    <label for="shipping" class="col-sm-5 col-form-label"><?php echo display('shipping_name') ?> *</label>
    <div class="col-sm-7">
        <input name="shipping" class="form-control" type="text" 
               placeholder="Add <?php echo display('shipping_name') ?>" 
               id="shipping" value="<?php echo set_value('shipping'); ?>">
        <?php echo form_error('shipping', '<div class="text-danger">', '</div>'); ?>
    </div>
</div>

<div class="form-group row">
    <label for="rate_type" class="col-sm-5 col-form-label">Rate Type *</label>
    <div class="col-sm-7">
        <select name="rate_type" id="rate_type" class="form-control">
            <option value=""><?php echo display('select_option');?></option>
            <option value="amount" <?php echo set_select('rate_type', 'amount'); ?>>Amount</option>
            <option value="percentage" <?php echo set_select('rate_type', 'percentage'); ?>>Percentage</option>
        </select>
        <?php echo form_error('rate_type', '<div class="text-danger">', '</div>'); ?>
    </div>
</div>

<div class="form-group row">
    <label for="shippingrate" class="col-sm-5 col-form-label"><?php echo display('shippingrate') ?> *</label>
    <div class="col-sm-7">
        <input name="shippingrate" class="form-control" type="number" min="0" step="0.01"
               placeholder="Add <?php echo display('shippingrate') ?>" 
               id="shippingrate" value="<?php echo set_value('shippingrate'); ?>">
        <?php echo form_error('shippingrate', '<div class="text-danger">', '</div>'); ?>
    </div>
</div>

<div class="form-group row">
    <label for="paymentmethod" class="col-sm-5 col-form-label"><?php echo display('payment_add') ?> *</label>
    <div class="col-sm-7">
        <select name="paymentmethod[]" class="form-control" multiple="multiple">
            <?php if (!empty($paymentinfo)) {
                foreach ($paymentinfo as $payment) { ?>
                    <option value="<?php echo $payment->payment_method_id; ?>" 
                        <?php echo set_select('paymentmethod[]', $payment->payment_method_id); ?>>
                        <?php echo $payment->payment_method; ?>
                    </option>
            <?php } } ?>
        </select>
        <?php echo form_error('paymentmethod[]', '<div class="text-danger">', '</div>'); ?>
    </div>
</div>

<div class="form-group row">
    <label for="shippintype" class="col-sm-5 col-form-label"><?php echo display('shipping_type');?></label>
    <div class="col-sm-7">
        <select name="shippintype" class="form-control">
            <option value=""><?php echo display('select_option');?></option>
            <option value="3" <?php echo set_select('shippintype','3'); ?>><?php echo display('home') ?></option>
            <option value="2" <?php echo set_select('shippintype','2'); ?>><?php echo display('pickup') ?></option>
            <option value="1" <?php echo set_select('shippintype','1'); ?>><?php echo display('dine_in') ?></option>
        </select>
    </div>
</div>

<div class="form-group row">
    <label for="status" class="col-sm-5 col-form-label"><?php echo display('status') ?> *</label>
    <div class="col-sm-7">
        <select name="status" class="form-control">
            <option value=""><?php echo display('select_option');?></option>
            <option value="1" <?php echo set_select('status','1'); ?>><?php echo display('active')?></option>
            <option value="0" <?php echo set_select('status','0'); ?>><?php echo display('inactive')?></option>
        </select>
        <?php echo form_error('status', '<div class="text-danger">', '</div>'); ?>
    </div>
</div>

<div class="form-group text-right">
    <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo display('reset') ?></button>
    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('add') ?></button>
</div>

<?php echo form_close() ?>


                </div>  
            </div>
        </div>
    </div>
             
    
   
    </div>
     
            </div>
            <div class="modal-footer">

            </div>

        </div>

    </div>

<div id="edit" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <strong><?php echo display('shipping_edit');?></strong>
            </div>
            <div class="modal-body editinfo">
            
    		</div>
     
            </div>
            <div class="modal-footer">

            </div>

        </div>

    </div>
<div class="row">
    <!--  table area -->
    <div class="col-sm-12">

        <div class="panel panel-default thumbnail"> 

            <div class="panel-body">
                <table width="100%" class="datatable table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th><?php echo display('Sl') ?></th>
                            <th><?php echo display('shipping_name') ?></th>
                            <th><?php echo display('shippingrate') ?></th>
                            <th><?php echo display('status') ?></th>
                            <th><?php echo display('action') ?></th> 
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($shippinglist)) { ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($shippinglist as $shipping) { ?>
                                <tr class="<?php echo ($sl & 1)?"odd gradeX":"even gradeC" ?>">
                                    <td><?php echo $sl; ?></td>
                                    <td><?php echo $shipping->shipping_method; ?></td>
                                                                       <td>
    <?php 
        if ($shipping->rate_type == 'percentage') {
            echo $shipping->shippingrate . '%';
        } else {
            $currency_icon = (isset($this->storecurrency) && !empty($this->storecurrency)) ? $this->storecurrency->curr_icon : '$';
            echo $currency_icon . $shipping->shippingrate;
        }
    ?>
</td>

                                    <td><?php if($shipping->is_active==1){echo display('active');}else{echo display('inactive');} ?></td>
                                   <td class="center">
                                    <?php if($this->permission->method('setting','update')->access()): ?>
                                    <input name="url" type="hidden" id="url_<?php echo $shipping->ship_id; ?>" value="<?php echo base_url("setting/shippingmethod/updateintfrm") ?>" />
                                        <a onclick="editinfoshiping('<?php echo $shipping->ship_id; ?>')" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="<?php echo display('update')?>"><i class="fa fa-pencil" aria-hidden="true"></i></a> 
                                         <?php endif; 
										 if($this->permission->method('setting','delete')->access()): ?>
                                        <a href="<?php echo base_url("setting/shippingmethod/delete/$shipping->ship_id") ?>" onclick="return confirm('<?php echo display("are_you_sure") ?>')" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="<?php echo display('delete')?> "><i class="fa fa-trash-o" aria-hidden="true"></i></a> 
                                         <?php endif; ?>
                                    </td>
                                    
                                </tr>
                                <?php $sl++; ?>
                            <?php } ?> 
                        <?php } ?> 
                    </tbody>
                </table>  <!-- /.table-responsive -->
            </div>
        </div>
    </div>
</div>

