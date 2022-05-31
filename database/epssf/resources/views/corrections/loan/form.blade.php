@extends('layouts.default')

@section('content_title')
<a href="{{ route('corrections.loan.index') }}"
  	 class="text-left text-xl font-semibold bg-gray-800 text-gray-100 rounded-lg hover:text-gray-200 p-3 mr-4">
  	<< {{ trans('general.start_again') }}
  </a>
    {{ trans('navigations.loan_correction') }} [{{ $loan->transactionid }}]
@stop

@section('content')

<form action="{{ route('corrections.loan.update',$loan->transactionid) }}" method="POST">

	<?php echo csrf_field(); ?>
	<input type="hidden" name="transactionid" value="{{ $loan->transactionid }}">
	<input type="hidden" name="loan_id" value="{{ $loan->id }}">
	
	@include('loansandrepayments.client_information_form')
    @include('loansandrepayments.ordinary_loan_form')
	@include('partials.wording')
	@include('corrections.loan.accounting')


	<div class="row">
	{{-- If loan don't have refund allow this loan to be delete --}}
	@if ($loan->refunds->isEmpty())
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			<input type="submit"
			   class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-green-700 text-green-200
			   hover:text-green-100 px-2 py-4 rounded-sm text-center font-semibold leading-tight uppercase"
			   value="{{ trans('general.request_loan_cancelling') }}"
			   id="adjust-button">

		</div>
	@endif
	
	@if (!$loan->refunds->isEmpty())
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" >
			<a href="{{ route('corrections.loan.index') }}"
			   class="col-xs-12 col-sm-12 col-md-12 col-lg-12 bg-yellow-700 text-yellow-200 hover:text-yellow-100 px-2 py-4 rounded-sm text-center font-semibold leading-tight uppercase">
			   {{ trans('general.you_cannot_request_loan_cancelling_that_has_payments') }}
			</a>
		</div>
	@endif
	</div>
</form>
@endsection


{{-- Validate value before submitting them --}}
@section('scripts')
<script type="text/javascript">
	jQuery(document).ready(function($) {
		// Hide alert if there is no error
	    var debitTotal = 0;
	    $('.debit-amount').each(function () {
	        debitTotal = debitTotal +  parseFloat( $( this ).val() ) || 0;
	    });
	    $('#loan-total-debit').html(debitTotal +' RWF');

	    var creditTotal = 0;
	    $('.credit-amount').each(function () {
	        creditTotal = creditTotal +  parseFloat( $( this ).val() ) || 0;
	    });
	    $('#loan-total-credit').html(creditTotal +' RWF');

	    var loanTotal = parseFloat($('#loanToRepay').val());

		$("#adjust-button").click(function(event) {

		    var debitTotal = 0;
		    $('.debit-amount').each(function () {
		        debitTotal = debitTotal +  parseFloat( $( this ).val() ) || 0;
		    });
		    $('#loan-total-debit').html(debitTotal +' RWF');

		    var creditTotal = 0;
		    $('.credit-amount').each(function () {
		        creditTotal = creditTotal +  parseFloat( $( this ).val() ) || 0;
		    });
		    $('#loan-total-credit').html(creditTotal +' RWF');

		    var loanTotal = parseFloat($('#loanToRepay').val());
			console.log(creditTotal,loanTotal,debitTotal);
		    if ((loanTotal !== creditTotal) || (creditTotal !== debitTotal)) {
		    	alert('Ensure amount on accounts are matching with loan amount');
		    	// Stop form submittion
				event.preventDefault();
		    }

		});



	});
</script>
@endsection