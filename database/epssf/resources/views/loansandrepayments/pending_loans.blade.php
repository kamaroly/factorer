@extends('layouts.default')
@section('content_title')
	{{ trans('loan.process_pending_loans') }}
@endsection
@section('content')
{!! method_exists(get_class($loans),'render') ? $loans->render() : null !!}
<table class="table table-bordered table-striped">
	<thead>
		<th>{{ trans('member.names') }} </th>
		<th>{{ trans('member.institution') }}</th>
		<th>{{ trans('member.contributions') }}</th>
		<th>{{ trans('member.balance_of_loan') }} </th>
		<th>{{ trans('member.right_to_loan') }} </th>
		<th>{{ trans('loan.wished_amount') }} </th>
		<th>{{ trans('loan.net_to_receive') }}</th>
		<th>{{ trans('loan.rate') }}</th>
		<th>{{ trans('loan.number_of_installments') }}</th>
		<th>{{ trans('loan.monthly_installments') }}</th>
		<th>{{ trans('loan.loan_to_repay') }}</th>
		<th>{{ trans('loan.interests') }}</th>
    	<th>{{ trans('loan.is_regulation') }}</th>
 		<th style="width: 170px;"><i class="fa fa-gear"></i>{{ trans('general.action') }}</th>
	</thead>

	<tbody>
		@each ('loansandrepayments.pending_loans_item', $loans, 'loan', 'partials.no-item')
	</tbody>
</table>	
{!! method_exists(get_class($loans),'render') ? $loans->render() : null !!}
@endsection