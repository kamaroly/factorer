<tr>
 	<td>{{ $member->adhersion_id }}</td>
 	<td>{{ $member->first_name }} {{ $member->last_name }}</td>
	<td>{{ $member->service }}</td>
	<td>{{ number_format($montlyfee = $member->monthly_fees) }}</td>
	<td>{{ number_format($mergencyfee = $member->emergency_fees) }}</td>
	<td>{{ number_format($montlyfee+$mergencyfee) }}</td>
</tr>