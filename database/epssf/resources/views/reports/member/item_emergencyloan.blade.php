<tr>
 	<td>{{ $member->adhersion_id }}</td>
 	<td>{{ $member->first_name }} {{ $member->last_name }}</td>
 	<td>{{ $member->name }}</td>
	<td>{{ trans('member.'.$member->operation_type) }}</td>
	<td>{{ $member->created_at }}</td>
	<td>{{ number_format($member->loan) }}</td>


</tr>