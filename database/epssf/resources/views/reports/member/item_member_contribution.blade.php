<tr>
 	<td>{{ $member->adhersion_id }}</td>
 	<td>{{ $member->first_name }} {{ $member->last_name }}</td>
 	<td>{{ $member->name }}</td>
	<td>{{ $member->service }}</td>
	<td>{{ trans('member.'.$member->status) }}</td>
	<td>{{ number_format($member->monthly_fee) }}</td>
</tr>