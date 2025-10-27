<!--Start Login Area-->
<section class="menu_area sect_pad">
    <div class="container wow fadeIn">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel-body">

                    <!-- Flash messages -->
                    <?php if ($this->session->flashdata('message')) { ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= $this->session->flashdata('message') ?>
                        </div>
                    <?php } ?>

                    <?php if ($this->session->flashdata('exception')) { ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= $this->session->flashdata('exception') ?>
                        </div>
                    <?php } ?>

                    <?php if (validation_errors()) { ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <?= validation_errors() ?>
                        </div>
                    <?php } ?>

                    <p>Please enter your details below.</p>
                    <div class="rrtt">
                        <?= form_open_multipart('hungry/submitregister', 'class="row" id="registerForm"') ?>

                            <!-- Name -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="user_name">Name*</label>
                                    <input type="text" id="user_name" class="form-control" name="user_name" placeholder="Enter your Name" value="<?= set_value('user_name') ?>" required>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="user_email">Email*</label>
                                    <input type="email" id="user_email" class="form-control" name="user_email" placeholder="Enter your Email" value="<?= set_value('user_email') ?>" required>
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="u_pass">Password <abbr class="required">*</abbr></label>
                                    <div class="input-group">
                                        <input type="password" id="u_pass" class="form-control" name="u_pass" placeholder="Enter your Password" required>
                                        <span class="input-group-text" onclick="togglePassword('u_pass')">👁</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="confirm_pass">Confirm Password <abbr class="required">*</abbr></label>
                                    <div class="input-group">
                                        <input type="password" id="confirm_pass" class="form-control" name="confirm_pass" placeholder="Enter your Password" required>
                                        <span class="input-group-text" onclick="togglePassword('confirm_pass')">👁</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Phone -->
                            <div class="col-sm-6">
                                <label class="control-label" for="phone">Phone Number*</label>
                                <div class="form-group">
                                    
                                    <input type="tel" id="phone" name="phone" class="form-control" placeholder="Enter phone number" value="<?= set_value('phone') ?>" required>
                                    <input type="hidden" name="country_code" id="country_code" value="<?= set_value('country_code') ?>">
                                </div>
                            </div>
                            <!-- Picture -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="UserPicture">Picture</label>
                                    <input name="UserPicture" id="UserPicture" type="file" style="width:100%;" />
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="address">Address</label>
                                    <textarea name="address" class="form-control" cols="30" rows="2"><?= set_value('address') ?></textarea>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="col-sm-12">
                                <input type="submit" class="btn btn-success btn-sm search" value="Register Now">&nbsp; 
                                OR &nbsp;<a href="<?= base_url('mylogin') ?>" class="btn btn-success btn-sm search">Login</a>
                            </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--End Login Area-->

<!-- intl-tel-input JS & CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
// Password toggle
function togglePassword(fieldId) {
    var input = document.getElementById(fieldId);
    input.type = input.type === "password" ? "text" : "password";
}

// Initialize intl-tel-input
var phoneInput = document.querySelector("#phone");
var iti = window.intlTelInput(phoneInput, {
    initialCountry: "lk",
    separateDialCode: true,
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
});

// Form submit validation
document.getElementById('registerForm').addEventListener('submit', function (e) {
    var pass = document.getElementById('u_pass').value;
    var confirmPass = document.getElementById('confirm_pass').value;
    var email = document.getElementById('user_email').value.trim();

    if (pass !== confirmPass) {
        e.preventDefault();
        alert("Passwords do not match!");
        return;
    }

    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        e.preventDefault();
        alert("Please enter a valid email address!");
        return;
    }

    if (!iti.isValidNumber()) {
        e.preventDefault();
        alert("Please enter a valid phone number!");
        return;
    }

    // Set country code hidden field
    document.getElementById('country_code').value = iti.getSelectedCountryData().dialCode;

    // Store digits-only phone number
    var fullNumber = iti.getNumber(intlTelInputUtils.numberFormat.E164); // e.g., +94712345678
    var dialCode = iti.getSelectedCountryData().dialCode; // e.g., 94
    var localNumber = fullNumber.replace("+" + dialCode, "");
    document.getElementById('phone').value = localNumber;
});
</script>
