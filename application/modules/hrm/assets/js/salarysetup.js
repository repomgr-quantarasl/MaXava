"use strict";

// Calculate gross salary
//Basic = 1000
//Additions = 10
//Deductions = 10
//Tax % = 10
//Your gross salary will show 900.
//Tax = ((Basic + Additions - Deductions) * Tax%
//Gross = (Basic + Additions - Deductions) - Tax

function summary() {
    var basic = parseFloat($('#basic').val()) || 0;

    // Sum of additions
    var add = 0;
    $(".addamount").each(function() {
        add += parseFloat($(this).val()) || 0;
    });

    // Sum of deductions
    var deduct = 0;
    $(".deducamount").each(function() {
        deduct += parseFloat($(this).val()) || 0;
    });

    // Tax %
    var taxPercent = parseFloat($('#taxinput').val()) || 0;

    // Taxable amount
    var taxableAmount = basic + add - deduct;

    // Tax amount
    var tax = (taxableAmount * taxPercent) / 100;

    // Gross salary
    var gross = taxableAmount - tax;

    $('#grsalary').val(gross.toFixed(2));
}

// Handle Tax Manager checkbox
function handletax(checkbox) {
    if (checkbox.checked) {
        // If you want to use Ajax to calculate tax externally
        var basic = parseFloat($('#basic').val()) || 0;
        var add = 0;
        var deduct = 0;

        $(".addamount").each(function() {
            add += parseFloat($(this).val()) || 0;
        });
        $(".deducamount").each(function() {
            deduct += parseFloat($(this).val()) || 0;
        });

        var taxableAmount = basic + add - deduct;
        var tax = parseFloat($('#taxinput').val()) || 0;

        var csrf = $('#csrfhashresarvation').val();

        $.ajax({
            url: basicinfo.baseurl+'hrm/Payroll/salarywithtax/',
            method: 'POST',
            dataType: 'json',
            data: {
                'amount': taxableAmount,
                'tax': tax,
                'csrf_test_name': csrf
            },
            success: function(data) {
                // data should return final tax
                var gross = taxableAmount - data;
                $('#grsalary').val(gross.toFixed(2));
                $('#taxinput').val('');
            },
            error: function() {
                alert('Error getting tax from server');
            }
        });
    } else {
        // Just recalc normally
        summary();
    }
}

// When employee changes, reset fields
function employechange(id) {
    var csrf = $('#csrfhashresarvation').val();
    $.ajax({
        url: basicinfo.baseurl+"hrm/Payroll/employeebasic/",
        method: 'POST',
        dataType: 'json',
        data: { 'employee_id': id, 'csrf_test_name': csrf },
        success: function(data) {
            $('#basic').val(data.rate);
            $('#sal_type').val(data.rate_type);
            $('#sal_type_name').val(data.stype);
            $('#grsalary').val('');

            if (data.rate_type == 1) {
                $('#taxinput').prop('disabled', true);
                $('#taxmanager').prop('checked', true).prop('disabled', true);
            } else {
                $('#taxinput').prop('disabled', false);
                $('#taxmanager').prop('checked', false).prop('disabled', false);
            }

            // Clear all addition/deduction inputs
            $('#add input.addamount').val('');
            $('#dduct input.deducamount').val('');
        },
        error: function() {
            alert('Error fetching employee data');
        }
    });
}

// Optional: datepicker code
$(function() {
    $("#start_date").datepicker({ dateFormat: 'yy-mm-dd' });
    $("#end_date").datepicker({ dateFormat: 'yy-mm-dd' }).bind("change", function() {
        var minValue = $.datepicker.parseDate("yy-mm-dd", $(this).val());
        minValue.setDate(minValue.getDate());
        $("#end_date").datepicker("option", "minDate", minValue);
    });
});
