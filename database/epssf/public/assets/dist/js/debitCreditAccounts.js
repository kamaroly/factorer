
/**
 * DYNAMIC FORM
 * @param   
 * @return 
 */
$(document).ready(function(){
    /**
     * Deal with debit accounts dynamic form
     */
     $('.debit-accounts').each(function() {      
     // Number of the debit account we currently have 
     function countDebits(){
       return $('.debit-accounts > .account-row').length;  
     }

    function hideShowFirstRemoveButton(){
    if(countDebits() ==1){
          $('.credit-accounts:first').find('.remove-button').html('');
          $('.credit-accounts:first').find('.remove-button').css({"background-color":"black !important","color":"white"});
        }else{
          $('.credit-accounts:first').find('.remove-button').html('-');
        }
     }
    // Listening to the click event of the remove button
    $('.remove-button').click(function(event) {
      event.preventDefault();
      // First verify how many debit accounts we have at the moment
      // if we have more than 1 then we are allowed to remove one
      // otherwise we need only to keep on input in the debit 
      // account     
      if(countDebits()>1){
        // We are allowed to remove this input because 
        // we have more than 1 input therefore we 
        // can remove this one
        $(this).parent().parent().remove(); 
      }
      hideShowFirstRemoveButton();
    });

    $('.add-button').click(function(event){
      event.preventDefault();
      // var lastInput = $('.form-group:last',$debitAccounts).clone(true).insertBefore(this).find('input');
      $(this).parent().find('.form-group:last-child').clone(true).insertBefore(this).find('input').val('');      
    });
  });

    /**
     * Deal with credit accounts dynamic form
     */
     $('.credit-accounts').each(function() {      
     // Number of the credit account we currently have 
     function countCredits()
     {
       return $('.credit-accounts > .account-row').length;  
     }

     function hideShowFirstRemoveButton(){
      
      if(countCredits() ==1){
          $('.credit-accounts:first').find('.remove-button').html('');
          $('.credit-accounts:first').find('.remove-button').css({"background-color":"#000 !important","color":"white"});
        }else{
          $('.credit-accounts:first').find('.remove-button').html('-');
        }
     }

    // Listening to the click event of the remove button
    $('.remove-button').click(function(event) {
      event.preventDefault();
      // First verify how many credit accounts we have at the moment
      // if we have more than 1 then we are allowed to remove one
      // otherwise we need only to keep on input in the credit 
      // account     
      
      if(countCredits()>1){
        // We are allowed to remove this input because 
        // we have more than 1 input therefore we 
        // can remove this one
        $(this).parent().parent().remove(); 
      }
      hideShowFirstRemoveButton();
    });

    /**
     * update debit accounts details at the backend
     * @return mixed
     */
    function submitDebitAccounts(){
      var debitAccounts = $("select[name='debit_accounts[]']").serializeArray();
      var debitAmounts  = $("input[name='debit_amounts[]']").serializeArray();
      var parameters ={debitAccounts,debitAmounts};
      ajaxCall(parameters);
    }
    /**
     * update credit accounts details at the backend
     * @return mixed
     */
    function submitCreditAccounts(){
      var  creditAccounts = $("select[name='credit_accounts[]']").serializeArray();
      var  creditAmounts  = $("input[name='credit_amounts[]']").serializeArray();
      var parameters     = {creditAccounts,creditAmounts};

      ajaxCall(parameters);
    }

    $('.accountAmount').keyup(function(event) {
      // event.preventDefault();
      submitDebitAccounts();
      submitCreditAccounts();
    });

    $('.account').change(function(event) {
      // event.preventDefault();
      submitDebitAccounts();
      submitCreditAccounts();
    });
    $('.add-button').click(function(event){
      event.preventDefault();
      var $creditAccounts = $(this).parent();

       var lastInput = $('.form-group:last',$creditAccounts).clone(true).insertBefore(this).find('input').val('');
       
       // At this level submit the data
       submitDebitAccounts();
       submitCreditAccounts();

       hideShowFirstRemoveButton();
    });

      /**
     * Send ajax calls to the back end
     * @param  json array data this contains the array of json object
     * @return mixed  
     */
    function ajaxCall(data){
      $.ajax({
        url: '/ajax/loans/accounting',
        type: 'POST',
        data: data,
      })
      .done(function(data) {
        console.log(data);
        console.log("success");
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    }

  });
});