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
                            placeholder="<?php echo display('shipping_name') ?>"
                            value="<?php echo set_value('shipping', (!empty($intinfo->shipping_method)?$intinfo->shipping_method:null)) ?>">
                        <?php echo form_error('shipping', '<div class="text-danger">', '</div>'); ?>
                    </div>
                </div>

                <!-- Rate Type -->
                <div class="form-group row">
                    <label for="rate_type" class="col-sm-5 col-form-label">Rate Type *</label>
                    <div class="col-sm-7">
                        <select name="rate_type" id="rate_type" class="form-control">
                            <option value=""><?php echo display('select_option');?></option>
                            <option value="amount" <?php if(!empty($intinfo) && $intinfo->rate_type=='amount'){echo "selected";} ?>>Amount</option>
                            <option value="percentage" <?php if(!empty($intinfo) && $intinfo->rate_type=='percentage'){echo "selected";} ?>>Percentage</option>
                        </select>
                        <?php echo form_error('rate_type', '<div class="text-danger">', '</div>'); ?>
                    </div>
                </div>

                <!-- Shipping Rate -->
                <div class="form-group row">
                    <label for="shippingrate" class="col-sm-5 col-form-label"><?php echo display('shippingrate') ?> *</label>
                    <div class="col-sm-7">
                        <input name="shippingrate" class="form-control" type="number" min="0" step="0.01"
                            placeholder="Add <?php echo display('shippingrate') ?>"
                            value="<?php echo set_value('shippingrate', (!empty($intinfo->shippingrate)?$intinfo->shippingrate:null)) ?>">
                        <?php echo form_error('shippingrate', '<div class="text-danger">', '</div>'); ?>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-group row">
                    <label for="paymentmethod" class="col-sm-5 col-form-label"><?php echo display('payment_add') ?> *</label>
                    <div class="col-sm-7">
                        <select name="paymentmethod[]" class="form-control" multiple="multiple">
                            <?php 
                            if (!empty($paymentinfo)) {
                                $slpayment = explode(',', $intinfo->payment_method);
                                foreach ($paymentinfo as $payment) {
                                    $selected = in_array($payment->payment_method_id, $slpayment) ? "selected" : "";
                                    echo "<option value='{$payment->payment_method_id}' {$selected}>{$payment->payment_method}</option>";
                                }
                            }
                            ?>
                        </select>
                        <?php echo form_error('paymentmethod[]', '<div class="text-danger">', '</div>'); ?>
                    </div>
                </div>

                <!-- Shipping Type -->
                <div class="form-group row">
                    <label for="shippintype" class="col-sm-5 col-form-label"><?php echo display('shipping_type');?></label>
                    <div class="col-sm-7">
                        <select name="shippintype" class="form-control">
                            <option value=""><?php echo display('select_option');?></option>
                            <option value="3" <?php if(!empty($intinfo) && $intinfo->shiptype==3){echo "selected";} ?>><?php echo display('home') ?></option>
                            <option value="2" <?php if(!empty($intinfo) && $intinfo->shiptype==2){echo "selected";} ?>><?php echo display('pickup') ?></option>
                            <option value="1" <?php if(!empty($intinfo) && $intinfo->shiptype==1){echo "selected";} ?>><?php echo display('dine_in') ?></option>
                        </select>
                    </div>
                </div>

                <!-- Status -->
                <div class="form-group row">
                    <label for="status" class="col-sm-5 col-form-label"><?php echo display('status') ?></label>
                    <div class="col-sm-7">
                        <select name="status" class="form-control">
                            <option value=""><?php echo display('select_option');?></option>
                            <option value="1" <?php if(!empty($intinfo) && $intinfo->is_active==1){echo "selected";} ?>><?php echo display('active')?></option>
                            <option value="0" <?php if(!empty($intinfo) && $intinfo->is_active==0){echo "selected";} ?>><?php echo display('inactive')?></option>
                        </select>
                        <?php echo form_error('status', '<div class="text-danger">', '</div>'); ?>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('update') ?></button>
                </div>
                <?php echo form_close() ?>

            </div>
        </div>
    </div>
</div>
