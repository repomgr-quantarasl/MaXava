<?php if (!empty($seoterm)) {
    $seoinfo = $this->db->select('*')->from('tbl_seoption')->where('title_slug', $seoterm)->get()->row();
} ?>

<div class="page_header">
    <div class="container wow fadeIn">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="page_header_content">
                    <ul class="m-0 nav">
                        <li><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li><i class="fa fa-angle-right"></i></li>
                        <li class="active"><a>M-Pesa Payment</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="payment_area sect_pad">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="payment-form-container">
                    <div class="payment-header text-center mb-4">
                        <img src="<?php echo base_url(); ?>assets/img/mpesa-logo.png" alt="M-Pesa" class="mpesa-logo mb-3" style="height: 60px;">
                        <h3>Complete your M-Pesa Payment</h3>
                        <p class="text-muted">Pay securely using your M-Pesa mobile wallet</p>
                    </div>

                    <!-- Order Summary -->
                    <div class="order-summary mb-4">
                        <h5>Order Summary</h5>
                        <div class="summary-card p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Order ID:</strong> #<?php echo $orderinfo->order_id; ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Customer:</strong> <?php echo $customerinfo->customer_name; ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <strong>Total Amount:</strong>
                                </div>
                                <div class="col-sm-6">
                                    <strong class="text-success"><?php echo $this->storecurrency->curr_icon; ?><?php echo number_format($orderinfo->totalamount, 2); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- M-Pesa Payment Form -->
                    <div class="mpesa-form">
                        <form id="mpesaPaymentForm">
                            <input type="hidden" name="order_id" value="<?php echo $orderinfo->order_id; ?>">
                            <input type="hidden" name="amount" value="<?php echo $orderinfo->totalamount; ?>">
                            
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    <i class="fa fa-mobile"></i> M-Pesa Phone Number
                                </label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       class="form-control form-control-lg" 
                                       placeholder="258841234567" 
                                       value="<?php echo $customerinfo->customer_phone; ?>"
                                       required>
                                <small class="form-text text-muted">
                                    Enter your M-Pesa registered phone number (e.g., 258841234567)
                                </small>
                            </div>

                            <div class="payment-instructions mb-4">
                                <div class="alert alert-info">
                                    <h6><i class="fa fa-info-circle"></i> How to complete your payment:</h6>
                                    <ol class="mb-0">
                                        <li>Click "Pay with M-Pesa" button below</li>
                                        <li>You will receive an SMS prompt on your phone</li>
                                        <li>Enter your M-Pesa PIN when prompted</li>
                                        <li>Confirm the payment</li>
                                        <li>You will receive a confirmation SMS</li>
                                    </ol>
                                </div>
                            </div>

                            <div class="payment-buttons text-center">
                                <button type="button" 
                                        id="payBtn" 
                                        class="btn btn-success btn-lg px-5">
                                    <i class="fa fa-mobile"></i> Pay with M-Pesa
                                </button>
                                <br><br>
                                <a href="<?php echo base_url('menu'); ?>" class="btn btn-outline-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Menu
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Payment Processing Modal -->
<div class="modal fade" id="processingModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Processing...</span>
                </div>
                <h5>Processing your M-Pesa payment...</h5>
                <p class="text-muted mb-0">Please check your phone and enter your M-Pesa PIN</p>
            </div>
        </div>
    </div>
</div>

<style>
.payment-form-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    padding: 30px;
}

.mpesa-logo {
    max-height: 60px;
    width: auto;
}

.summary-card {
    border: 1px solid #e0e0e0;
}

.form-control-lg {
    font-size: 18px;
    padding: 15px;
}

.payment-buttons .btn {
    min-width: 200px;
    font-weight: 600;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<script>
$(document).ready(function() {
    $('#payBtn').click(function() {
        var phone = $('#phone').val().trim();
        
        if (!phone) {
            alert('Please enter your M-Pesa phone number');
            return;
        }
        
        // Validate phone number format
        var phoneRegex = /^(258)?[0-9]{9}$/;
        if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
            alert('Please enter a valid Mozambique phone number (e.g., 258841234567)');
            return;
        }
        
        // Show processing modal
        $('#processingModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        
        // Process payment
        $.ajax({
            url: '<?php echo base_url("hungry/process_mpesa"); ?>',
            type: 'POST',
            data: $('#mpesaPaymentForm').serialize(),
            dataType: 'json',
            success: function(response) {
                $('#processingModal').modal('hide');
                
                if (response.success) {
                    alert('Payment initiated successfully! Please complete the payment on your phone.');
                    
                    // Poll for payment status (optional)
                    checkPaymentStatus(response.transaction_id);
                    
                } else {
                    alert('Payment failed: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                $('#processingModal').modal('hide');
                alert('Connection error. Please try again.');
                console.error('Payment error:', error);
            }
        });
    });
    
    function checkPaymentStatus(transactionId) {
        // This is optional - you could implement a status checking mechanism
        setTimeout(function() {
            window.location.href = '<?php echo base_url("hungry/mpesa_successful/" . $orderinfo->order_id . "/" . ($page ?? 1)); ?>';
        }, 10000); // Redirect after 10 seconds
    }
});
</script>