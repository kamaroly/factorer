<tr>
 	<td>{{$member->adhersion_id }}</td>
 	<td>{{ $member->first_name }} {{ $member->last_name }}</td>
 	<td>{{ $member->member_nid }}</td>
 	<td>{{ $member->institution }}</td>
	<td>{{ $member->telephone }}</td>
	<td>{{ $member->attorney }}</td>
	<td>{{ $member->bank_name }}</td>
	<td>{{ $member->bank_account }}</td>
	<td>{{ number_format($member->savings) }}</td>
	<td>{{ trans('member.'.$member->status) }}</td>
	.<!--<td>{{ number_format($member->loan_balance) }}</td> -->
	<td>{{ number_format($member->total_contribution) }}</td>
</tr>
