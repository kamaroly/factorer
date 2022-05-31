<h4>{{ ucfirst(trans('report.refundlatestpayment')) }}</h4>
<table class="pure-table pure-table-bordered">
	<thead>
		<tr>
			<th>{{ trans('member.adhersion_id') }}</th>
			<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.service') }}</th>
			<th>{{ trans('member.monthly_fees') }}</th>
			<th>{{ trans('member.balance') }}</th>
		</tr>
	</thead>

	<tbody>
	@foreach ($refunds as $refund)
		<tr>
			<td>{!! $refund->adhersion_id !!}</td>
			<td>{!! $refund->first_name !!} {!! $refund->last_name !!}</td>
			<td>{!! $refund->service !!}</td>
			<td>{!! number_format($refund->monthly_fees) !!}</td>
			<td>{!! number_format($refund->balance) !!}</td>
		</tr>
	@endforeach
	</tbody>
</table>