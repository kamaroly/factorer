<table class="pure-table pure-table-bordered">
<caption>	
		{{ trans('reports.loan_payback') }}  
		{{ trans('general.between') }} 
			{!! date('d/M/Y',strtotime(request()->segment(4))) !!} 
		{{ trans('general.and') }} 
			{!! date('d/M/Y',strtotime(request()->segment(5))) !!}
</caption>
	<thead>
		<tr>
			
			<th>{{ trans('member.adhersion_id') }}</th>
			<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.loan_contract') }}</th>
			<th>{{ trans('member.operation_type') }}</th>
			<th>{{ trans('member.refund') }}</th>
			<th>{{ trans('member.balance') }}</th>
		</tr>
	</thead>
	<tbody>
		@forelse ($loans as $item)
			<tr>
				
				<td>{!! $item->adhersion_id !!}</td>
				<td>{!!$item->first_name!!} {!!$item->last_name!!}</td>
				<td>{!! $item->institution !!}</td>
				<td>{!! $item->loan_contract !!}</td>
		        <td>{!! trans('member.'.$item->operation_type) !!}</td>
				<td>{!! $item->fin_dette !!}</td>
				<td>{!! $item->balance !!}</td>
		</tr>
		@empty
			nothing to show here
		@endforelse
	</tbody>
</table>	
