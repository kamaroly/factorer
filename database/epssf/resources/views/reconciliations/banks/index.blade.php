@extends('layouts.default')

@section('content_title')
  @if (request()->is('reconciliations/account/bank*'))
      {{ trans('general.bank_reconciliations') }}
  @else
      {{ trans('general.account_reconciliations') }}
  @endif
@endsection

@section('content')

    {{-- UPLOAD BANK FILE --}}
    @if (isset($transactions) === FALSE)  
      <div class="col-md-12 bg-gray-200 p-2"> 
        {!! Form::open(array('route'=>'reconciliations.bank.reconcile','method'=>'POST', 'files'=>true,'class'=>'row')) !!}
           {{-- Dates --}}
            @include('reconciliations.dates')
            
            {{-- Account to reconcile --}}
          <div class="col-md-3">
              <label>{{ trans('account.account') }}</label>
             {!! Form::select('account', $accounts,null, ['class'=>'form-control account'])!!}
          </div>
             
            {{-- Upload cvs --}}
            <div class="col-md-2">  
                <label>{{ trans('reconciliation.upload_bank_file') }}</label>
                <input type="file" name="bank-csv-file" class="form-control col-md-12" accept=".csv">
            </div>
            <div class="col-md-2">  
                <label>&nbsp;</label>
               <button class="btn btn-success col-md-5 form-control">
                  <i class="fa fa-upload"></i> {{ trans('general.upload') }}
                </button>
            </div>          
            {!! Form::close() !!}
      </div>
    @endif

      <div class="col-md-12 bg-gray-200">

        <form action="{{ route('reconciliations.bank.reconciliations.download') }}" method="GET" target="_blank">
          {{ csrf_field() }}
          @if (isset($uploadedData))
            <div class="col-md-12">
                  <input  type="submit" name="download_type"  class="btn bg-green-600 text-green-100 font-semibold" 
                          value="{{ trans('general.export_matching_bank') }}">
                  <input  type="submit" name="download_type" class="btn bg-green-200 text-green-900 font-semibold" 
                          value="{{ trans('general.export_not_in_bank') }}">
                  <input  type="submit" name="download_type" class="btn bg-yellow-700 text-yellow-100 font-semibold" 
                          value="{{ trans('general.export_matching_ceb') }}">
                  <input  type="submit" name="download_type" class="btn bg-yellow-200 text-yellow-800 font-semibold" 
                          value="{{ trans('general.export_not_in_ceb') }}">
            </div>

            <div class="col-md-6">
                  @include('reconciliations.banks.bank-results')
            </div>
          @endif
          
            @if (isset($transactions))  
              <div class="col-md-6 bg-yellow-100">
                  @include('reconciliations.banks.account-transactions')
              </div>
            @endif
        </form>
      </div>

@endsection

@section('scripts')
		<script type="text/javascript">

        var accounts = {!! json_encode($accounts) !!};

        $accountsOptions = '';
         $.each(accounts, function(index, val) {
           $accountsOptions += '<option value="' +  index + '">' + val + '</option>';
        });

	        /** GENERATING DEBIT ACCOUNTS FORM */
         $.fn.addDebitForms = function(){
          var myform = "<div class='form-group account-row' >"+
                       "     <div class='col-xs-7'>"+
                       "      <select class='form-control account' name='accounts[]'>"+$accountsOptions+"</select></td>"+
                       "     </div>"+
                       "     <div class='col-xs-3'>"+
                       "        <input class='form-control amount' name='amounts[]' type='numeric' value='0'>"+
                       "     </div>"+         
                       "     <div class='col-xs-2'>"+
                       "        <button class='btn btn-danger'><i class='fa fa-times'></i></button> " +
                       "     </div>"+ 
                       "</div>";

           myform = $("<div>"+myform+"</div>");
           $("button", $(myform)).click(function(){ $(this).parent().parent().remove(); });

           $(this).append(myform);

        };

        /** GENERATING CREDIT ACCOUNT FORM */
        $.fn.addCreditForms = function(){
          var myform = "<div class='form-group account-row' >"+
                       "     <div class='col-xs-4'>"+
                       "      <select class='form-control account' name='credit_accounts[]'>"+$accountsOptions+"</select></td>"+
                       "     </div>"+
                       "     <div class='col-xs-3'>"+
                       "        <input class='form-control credit-amount' name='credit_amounts[]' type='numeric' value='0'>"+
                       "     </div>"+         
                       "     <div class='col-xs-2'>"+
                       "        <button class='btn btn-danger'><i class='fa fa-times'></i></button> " +
                       "     </div>"+ 
                       "</div>";

           myform = $("<div>"+myform+"</div>");
           $("button", $(myform)).click(function(){ $(this).parent().parent().remove(); });

           $(this).append(myform);

        }

     $(function(){
      $("#add-debit-account").bind("click", function(){
        $("#debit-accounts-container").addDebitForms();
      });

      $("#add-credit-account").bind("click", function(){
        $("#credit-accounts-container").addCreditForms();
      });
    });

       /**
   * Sort an HTML TABLE IN DOM element
   * @usage 1) add #sortable-table-input id to your input and listen to onkeyup event
               Example: <input  id="sortable-table-input" onkeyup="sortTable()" />
            2) Add #sortable-table id to your HTML table
   * @return 
   */
function sortTable(input, sortTableID) {
  // Declare variables
  var input, filter, table, tableRow, index, rowTextValue;
  input    = input;
  filter   = input.value.toUpperCase();
  table    = document.getElementById(sortTableID);
  tableRow = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tableRow.length; i++) {

    // If this row exists then perform search
    if (tableRow[i]) {
        // Get inner text of the table first
        rowTextValue = tableRow[i].textContent || tableRow[i].innerText;
        // If inner text has same text as what is in input then 
        // filter it out
        if (rowTextValue.toUpperCase().indexOf(filter) > -1) {
            tableRow[i].style.display = "";
        } else {
            tableRow[i].style.display = "none";
        }
    }
  }
}
		</script>
@endsection

