<?php $member = $loan->member; ?>
<tr>
	<td>{{ $member->first_name.' '.$member->last_name }}</td>
	<td>{{ $member->institution->name }} </td>
	<td class="text-green-600 text-2xl">
			{{ number_format($member->total_contribution) }}
	</td>
	<td>{{ number_format($member->loan_balance) }} </td>
	<td>{{ number_format($loan->right_to_loan) }} </td>
	<td class="text-center">{{ number_format($loan->wished_amount) }} 
		<span class="text-green-700 px-2 py-2 mx-1 mx-2 text-xs font-semibold">
			{{ ucfirst($loan->operation_type) }}
		</span>
	</td>
	<td>{{ number_format($loan->amount_received) }}</td>
	<td>{{ $loan->rate }}</td>
	<td>{{ $loan->tranches_number }}</td>
	<td>{{ number_format($loan->monthly_fees) }} </td>
	<td>{{ number_format($loan->loan_to_repay) }}</td>
	<td>{{ number_format($loan->interests) }}</td>
	<td>{{ $loan->is_regulation==1?trans('general.yes'):trans('general.no') }}</td>
	<td>
		<a href="{!! route('loan.process',['loanId'=> $loan->id,'status'=>'approved']) !!}" class="btn btn-success">
			<i class="fa fa-check"></i>
		</a>
		<a href="{!! route('loan.process',['loanId'=> $loan->id,'status'=>'rejected']) !!}" class="btn btn-danger">
			<i class="fa fa-close"></i>
		</a>
	</td>
</tr>
