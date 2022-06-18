@if (!Request::is('accounting*'))
  <div class="box-header with-border">
  <h3 class="box-title">{{ trans('accounting.accounting') }}</h3>
</div>
@endif

<div class="row">
    <div class="col-md-6">
     @include('accounting.debit')
  </div>
    <!-- START OF CREDIT ACCOUNT -->
    <div class="col-md-6">
      @include('accounting.credit')
    </div>
     <!-- END OF CREDIT ACCOUNT -->
</div>
<div class="row">
    <div class="col-md-6 total-debit">
      
  </div>
    <!-- START OF CREDIT ACCOUNT -->
    <div class="col-md-6 total-credit">
    </div>
     <!-- END OF CREDIT ACCOUNT -->
</div>

@section('scripts')
@if (Request::is('loan*') )
  {{-- Loan below javascripts only when requests are for loan  --}}
  <script type="text/javascript" src="{{route('assets.js.loanform')}}"></script>
@endif
@if (Request::is('regularisation*'))
  {{-- Loan below javascripts only when requests are for regularisation --}}
 <script type="text/javascript" src="{{route('assets.js.regularisationform')}}"></script> 
@endif

@if (Request::is('members*transacts'))
  {{-- Loan below javascripts only when requests are for regularisation --}}
  <script type="text/javascript" src="{!! url('assets/dist/js/memberTransaction.js') !!}"></script>
 <!-- <script type="text/javascript" src="{{route('assets.js.regularisationform')}}"></script> -->
@endif

<script type="text/javascript">

    (function($){    

        $countFormsDebits = 1;
        $countFormsCredits = 1;
        
        @if (isset($defaultAccounts['debits']))
          @if (! collect($defaultAccounts['debits'])->isEmpty())
            $countFormsDebits = {!! collect($defaultAccounts['debits'])->count() !!};
          @endif
        @endif

         @if (isset($defaultAccounts['credits']))
          @if (! collect($defaultAccounts['credits'])->isEmpty())
            $countFormsCredits = {!! collect($defaultAccounts['credits'])->count() !!};
          @endif
        @endif

        $debitAmountSum = 0;
        $creditAmountSum = 0;

        var accounts = {!! json_encode($accounts) !!};

        $accountsOptions = '';
         $.each(accounts, function(index, val) {
           $accountsOptions += '<option value="' +  index + '">' + val + '</option>';
        });

        /** GENERATING DEBIT ACCOUNTS FORM */
         $.fn.addDebitForms = function(){
          var myform = "<div class='form-group account-row' >"+
                       "     <div class='col-xs-6'>"+
                       "      <select class='form-control account' name='debit_accounts["+$countFormsDebits+"]'>"+$accountsOptions+"</select></td>"+
                       "     </div>"+
                       "     <div class='col-xs-4'>"+
                       "        <input class='form-control accountAmount debit-amount' name='debit_amounts["+$countFormsDebits+"]' type='numeric' value='0'>"+
                       "     </div>"+         
                       "     <div class='col-xs-2'>"+
                       "        <button class='btn btn-danger'><i class='fa fa-times'></i></button> " +
                       "     </div>"+ 
                       "</div>";

           myform = $("<div>"+myform+"</div>");
           $("button", $(myform)).click(function(){ $(this).parent().parent().remove(); });

           $(this).append(myform);
           $countFormsDebits++;
        };

        /** GENERATING CREDIT ACCOUNT FORM */
        $.fn.addCreditForms = function(){
          var myform = "<div class='form-group account-row' >"+
                       "     <div class='col-xs-6'>"+
                       "      <select class='form-control account' name='credit_accounts["+$countFormsCredits+"]'>"+$accountsOptions+"</select></td>"+
                       "     </div>"+
                       "     <div class='col-xs-4'>"+
                       "        <input class='form-control credit-amount' name='credit_amounts["+$countFormsCredits+"]' type='numeric' value='0'>"+
                       "     </div>"+         
                       "     <div class='col-xs-2'>"+
                       "        <button class='btn btn-danger'><i class='fa fa-times'></i></button> " +
                       "     </div>"+ 
                       "</div>";

           myform = $("<div>"+myform+"</div>");
           $("button", $(myform)).click(function(){ $(this).parent().parent().remove(); });

           $(this).append(myform);
           $countFormsCredits++;
        };

       $getSum = function(item){
         var items = $(item);
         var total = 0;
         /** TRY TO GET THE SUM */
         $.each(items, function(index, val) {
           total += parseInt(val.value);
         });
         return total;
       };

    })(jQuery);   

     $(function(){

      $("#add-debit-account").bind("click", function(){
        $("#debit-accounts-container").addDebitForms();
      });

      $("#add-credit-account").bind("click", function(){
        $("#credit-accounts-container").addCreditForms();
      });
      
      $('.debit-amount').on('click keyup keydown keypress change',function(event) {
        $('.total-debit').html('total debit '+$getSum('.debit-amount'));
      });
 $('.total-debit').html('total debit '+$getSum('.debit-amount'));
     $(".debit-amount").on('click keyup keydown keypress change',function(event) {
       console.log(event);
    });
      $('.credit-amount').on('click keyup keydown keypress change',function(event) {
        $('.total-credit').html('total credit '+$getSum('.credit-amount'));
      });
      $('.total-credit').html('total credit '+$getSum('.credit-amount'));
    });
    
</script>

@stop