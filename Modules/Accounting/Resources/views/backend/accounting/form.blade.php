@if (!Request::is('accounting*'))
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans('accounting.accounting') }}</h3>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        @include('accounting::backend.accounting.debit')
    </div>
    <!-- START OF CREDIT ACCOUNT -->
    <div class="col-md-6">
        @include('accounting::backend.accounting.credit')
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

<div class="row">

    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
        <a class="col-12 col-sm-12 col-md-12 col-lg-12 btn btn-lg btn-warning">
            Annuler
        </a>
    </div>
    <div class="col-6 col-sm-6 col-md-6 col-lg-6">
        <button role="submit" id="js-complete-button"
            onclick="return confirm('Etes-vous sûr que vous voulez que cette action \ cette action ne peut pas être inversée')"
            class="col-12 col-sm-12 col-md-12 col-lg-12 btn btn-lg btn-success">
            Enrgistre
        </button>
    </div>

</div>
@push('after-scripts')
    <script type="text/javascript">
        (function($) {

            $countFormsDebits = 1;
            $countFormsCredits = 1;

            @if (isset($defaultAccounts['debits']))
                @if (!collect($defaultAccounts['debits'])->isEmpty())
                    $countFormsDebits = {!! collect($defaultAccounts['debits'])->count() !!};
                @endif
            @endif

            @if (isset($defaultAccounts['credits']))
                @if (!collect($defaultAccounts['credits'])->isEmpty())
                    $countFormsCredits = {!! collect($defaultAccounts['credits'])->count() !!};
                @endif
            @endif

            $debitAmountSum = 0;
            $creditAmountSum = 0;

            var accounts = {!! json_encode($accounts) !!};

            $accountsOptions = '';
            $.each(accounts, function(index, val) {
                $accountsOptions += '<option value="' + index + '">' + val + '</option>';
            });

            /** GENERATING DEBIT ACCOUNTS FORM */
            $.fn.addDebitForms = function() {
                var myform = "<div class='form-group row' >" +
                    "     <div class='col-6'>" +
                    "      <select class='form-control account' name='debit_accounts[" + $countFormsDebits + "]'>" +
                    $accountsOptions + "</select></td>" +
                    "     </div>" +
                    "     <div class='col-4'>" +
                    "        <input class='form-control accountAmount debit-amount' name='debit_amounts[" +
                    $countFormsDebits + "]' type='numeric' value='0'>" +
                    "     </div>" +
                    "     <div class='col-2'>" +
                    "        <button class='btn btn-danger'><i class='fa fa-times'></i></button> " +
                    "     </div>" +
                    "</div>";

                myform = $("<div>" + myform + "</div>");
                $("button", $(myform)).click(function() {
                    $(this).parent().parent().remove();
                });

                $(this).append(myform);
                $countFormsDebits++;
            };

            /** GENERATING CREDIT ACCOUNT FORM */
            $.fn.addCreditForms = function() {
                var myform = "<div class='form-group row' >" +
                    "     <div class='col-6'>" +
                    "      <select class='form-control account' name='credit_accounts[" + $countFormsCredits +
                    "]'>" + $accountsOptions + "</select></td>" +
                    "     </div>" +
                    "     <div class='col-4'>" +
                    "        <input class='form-control credit-amount' name='credit_amounts[" + $countFormsCredits +
                    "]' type='numeric' value='0'>" +
                    "     </div>" +
                    "     <div class='col-2'>" +
                    "        <button class='btn btn-danger'><i class='fa fa-times'></i></button> " +
                    "     </div>" +
                    "</div>";

                myform = $("<div>" + myform + "</div>");
                $("button", $(myform)).click(function() {
                    $(this).parent().parent().remove();
                });

                $(this).append(myform);
                $countFormsCredits++;
            };

            $getSum = function(item) {
                var items = $(item);
                var total = 0;
                /** TRY TO GET THE SUM */
                $.each(items, function(index, val) {
                    total += parseInt(val.value);
                });
                return total;
            };

        })(jQuery);

        $(function() {

            $("#add-debit-account").bind("click", function() {
                $("#debit-accounts-container").addDebitForms();
            });

            $("#add-credit-account").bind("click", function() {
                $("#credit-accounts-container").addCreditForms();
            });

            $('.debit-amount').on('click keyup keydown keypress change', function(event) {
                $('.total-debit').html('total debit ' + $getSum('.debit-amount'));
            });
            $('.total-debit').html('total debit ' + $getSum('.debit-amount'));
            $(".debit-amount").on('click keyup keydown keypress change', function(event) {
                console.log(event);
            });
            $('.credit-amount').on('click keyup keydown keypress change', function(event) {
                $('.total-credit').html('total credit ' + $getSum('.credit-amount'));
            });
            $('.total-credit').html('total credit ' + $getSum('.credit-amount'));
        });
    </script>
@endpush
