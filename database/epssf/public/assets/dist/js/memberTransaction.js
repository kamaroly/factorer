jQuery(document).ready(function($) {
  // $('#contract_number').hide();
    /** SET Operation type if it is changed */
    $(".movement_type").change(function(event) {
      var movement_type = $(this);
      var operationType = $(".operation_type");
      window.location.href = window.location.protocol+'//'+window.location.host+window.location.pathname+'?movement_type='+movement_type.val()+'&operation_type='+operationType.val();
    });
   
     /** SET Operation type if it is changed */
     $(".operation_type").change(function(event) {
       var operationType = $(this);
       var movement_type = $(".movement_type");
       window.location.href = window.location.protocol+'//'+window.location.host+window.location.pathname+'?movement_type='+movement_type.val()+'&operation_type='+operationType.val();
      });

     //  When transactions charges select is changed
     //  Then determine the action to be done.
     $('#transaction-charges').change(function(event) {
       /* Act on the event */
       var percentage = parseInt($(this).val());



       // Do the following only if we have
       // additional charges
       if (percentage > 0 ) {

        // 1. Resize charges boxes
        $('.cheque-section').removeClass('col-xs-4 col-md-4');
        $('.cheque-section').addClass('col-xs-2 col-md-2');
        $('.charges-section').show('400', function() {
 
       });

        // 2. Calculate the fees based on the provided amount
        computeDebitCredit();
       
        return;
       }

      $('.cheque-section').removeClass('col-xs-2 col-md-2');
      $('.cheque-section').addClass('col-xs-4 col-md-4');
      $('.charges-section').hide('400', function() {
         // Clean the content
          $(this).val(0);
      });

      computeDebitCredit();
      
     });
    
     // if  amount entry do  the calculation and display on account  interface
  
     $('.amount').on('input',function(e){
         computeDebitCredit();
     });


     function computeDebitCredit () {
        var amount = parseInt($('.amount').val());
        var transactionCharges = parseInt($('#transaction-charges').val());
       
        // if we don't have transaction charges set it to 0
        if (isNaN(transactionCharges)) {
          transactionCharges = 0;
        };

        var chargedAmount = amount * (transactionCharges / 100);
        var creditAmount = amount - chargedAmount;

        // Now we are done with calculating charges
        // Set charges input and let the user
        // be aware of the charges
        $('.charges-amount').val(Math.round(chargedAmount));
        
        // Assign debit and credit amount
        $('#debit_amount_0').val(amount);
        $('#credit_amounts_0').val(Math.round(creditAmount));
        $('#credit_amounts_1').val(Math.round(chargedAmount));
     }


    /** UPDATE ACCOUNTS AMOUNT */
    /** @todo map the right amount to the right account */
 /*   if ($('#operation_type').val()=='saving') {
      $('#debit_amount_0').val($('#additional_amount').val());
      $('#debit_amount_2').val($('#additional_amount').val());

      $('#credit_amounts_0').val($('#netToReceive').val());
      $('#credit_amounts_2').val($('#interests_to_pay').val());
      $('#credit_amounts_4').val($('#additinal_charges').val());
    };

    if ($('#operation_type').val()=='withdrawal') {
      $('#debit_amount_0').val($('#additional_amount').val());
      $('#debit_amount_2').val($('#additional_amount').val());

      $('#credit_amounts_0').val($('#netToReceive').val());
      $('#credit_amounts_2').val($('#interests_to_pay').val());
      $('#credit_amounts_4').val($('#additinal_charges').val());
    };*/
  });
  /**
   * Implement validation
   */
  (function($,W,D)
{
    var JQUERY4U = {};

    JQUERY4U.UTIL =
    {
        setupFormValidation: function()
        {
            //form validation rules
            $("#member-transaction-form").validate({
                rules: {
                    movement_type: "required",
                    operation_type:  "required",
                wording: {
                        required: true,
                        minlength: 10
                    },
                cheque_number: {
                        required: true,
                        minlength: 5
                    },
                amount: {
                        required: true,
                        minlength: 4
                    }
                },
                messages: {
                    movement_type: "Please select movement type",
                    operation_type: "Please select operation type",
                    bank: {
                        required: "Please provide a bank",
                        minlength: "Your bank must be at least 2 characters long"
                    },
                    wording: "Please enter a valid wording",
                    cheque_number: "Please enter a valid cheque_number",
                    amount: "Please enter a valid amount"
                },
                submitHandler: function(form) {
                    $('.submit-member-transaction-button').attr('disabled', true);
                    $('.submit-member-transaction-button').html('<img src="/assets/dist/img/loading.gif" />');

                    //Submit form with Ajax
                    $.ajax({
                     type: "POST",
                     url:  $("#member-transaction-form").attr('action'),
                     data:  $("#member-transaction-form").serialize(), // serializes the form's elements.
                     success: function(data)
                     {
                     
                      $('.popdown').close_popdown();

                      swal.setDefaults({ confirmButtonColor: '#5bb75b' });
                      swal({
                        title:"Transaction went well.",
                        text : data.responseText,
                        type :"success",
                        html :true
                      });

                      $('.submit-member-transaction-button').html("{{ trans('general.save') }}");
                      $('.submit-member-transaction-button').removeAttr('disabled');
                      $('#member-transaction-form :input').removeAttr('disabled');
                     },
                    error: function (error) {
                      var errorMessages = JSON.parse(error.responseText);
                      var errorNotifications = '';
                      $.each(errorMessages, function(index, val) {
                         /* build notification */
                         errorNotifications +='<p style="color:#fff;">'+val+'</p>';
                      });
                     errorNotifications = '<div data-alert class="alert alert-error radius">'+errorNotifications+'</div>';
                      swal.setDefaults({ confirmButtonColor: '#d9534f' });
                      swal({
                        title:"Validation error",
                        text : errorNotifications,
                        type :"error",
                        html :true
                      });

                      $('.submit-member-transaction-button').html("{{ trans('general.save') }}");
                      $('.submit-member-transaction-button').removeAttr('disabled');
                      $('#member-transaction-form :input').removeAttr('disabled');
                    }
                   });
                }
            });
        }
    }

    //when the dom has loaded setup form validation rules
    $(D).ready(function($) {
        JQUERY4U.UTIL.setupFormValidation();
    });

})(jQuery, window, document);