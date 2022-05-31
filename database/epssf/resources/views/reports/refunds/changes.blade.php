<table class="pure-table pure-table-bordered">
<caption>	
		{{ trans('reports.refund_changes') }}  
		{{ trans('general.between') }} 
			{!! date('d/M/Y',strtotime(request()->segment(4))) !!} 
		{{ trans('general.and') }} 
			{!! date('d/M/Y',strtotime(request()->segment(5))) !!}
</caption>
	<thead>
		<tr>			
		    <th>{{ trans('member.date') }}</th>
			<th>{{ trans('member.adhersion_id') }}</th>			
			<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.operation_type') }}</th>
			<th>{{ trans('member.monthly_fees') }}</th>
		</tr>
	</thead>
	<tbody>
	@foreach ($members as $member)
		<tr>
			<td>{!! $member->date !!}</td>
			<td>{!! $member->adhersion_id !!}</td>
			<td>{!! $member->names !!}</td>
			<td>{!! $member->institution !!}</td>
			<td>{!! trans('loan.'.$member->operation_type)!!}</td>
		    <td>{!! number_format($member->monthly_fees) !!}</td>
		</tr>
	@endforeach

	</tbody>
</table>