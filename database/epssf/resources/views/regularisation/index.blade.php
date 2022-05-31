@extends('layouts.default')
@section('content_title')
  {{ trans('navigations.regularisation') }}
@stop

@section('content')

	@if (!empty($loanId))
    {{-- We have completed the transaction let's print the invoice --}}
	<script type="text/javascript">
	function print(url) {
	  var win = window.open(url, '_blank');
	  win.focus();
	}
	
	 // print('{!! route('reports.members.contracts.loan',['loanId' => $loanId,'excel'=>0]) !!}');
	</script>
	@endif

	{{-- @include('regularisation.index_buttons') --}}
   {!! Form::open(['method'=>'POST','url'=>route('regularisation.complete'),'id'=>'regularisationForm']) !!}
	@include('regularisation.client_information_form')
    
	{{-- 
		If the member has been selected then check if he has 
		 active loan if he does display active loans information
	 --}}

	@if (!empty($member))
		@if ($member->has_active_loan == true)
			@include('regularisation.previous_loan_details',['member'=>$member])
		@endif
	@endif
    @include('regularisation.ordinary_loan_form')
	
	{{--Add cautionneur  only if balance is higher than contributions--}}
	{{-- @if ($member->loan_balance > $member->total_contribution) --}}
		 @include('regularisation.caution_form')
	{{-- @endif --}}
	

	<?php $wording = isset($wording) ? $wording : trans('loan.regulating_loan_to',['loantype'=>trans('loan.'.$loanInputs['operation_type']),'names'=>$member->names]) ?>
	@include('partials.wording')
	@include('accounting.form')
@stop


@section('content_footer')
    @include('partials.buttons',['completeRoute'=>'regularisation.complete','cancelRoute'=>'regularisation.cancel'])
    {!! Form::close() !!}
@stop