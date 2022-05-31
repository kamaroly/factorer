<?php $member = $loan->member; ?>
<tr>
	<td>
		{{ $member->first_name.' '.$member->last_name }} - {{ $member->adhersion_id }}
	</td>
	<td>{{ $member->institution->name }} </td>
	<td class="text-green-600 text-2xl">
			{{ number_format($member->total_contribution) }}
	</td>
	<td>{{ number_format($member->Loan_balance) }} </td>
	<td>{{ number_format($loan->right_to_loan) }} </td>
	<td>{{ trans('loans.'.$loan->operation_type) }} </td>
	<td>{{ $loan->rate }}</td>
	<td>{{ number_format($loan->tranches_number) }}</td>
	<td>{{ number_format($loan->monthly_fees) }} </td>
	<td>{{ number_format($loan->loan_to_repay) }}</td>
	<td>{{ number_format($loan->interests) }}</td>
	<td>{{ number_format($loan->urgent_loan_interests) }}</td>
	<td>{{ number_format($loan->amount_received) }}</td>
	<td>{{ $loan->is_regulation==1?trans('general.yes'):trans('general.no') }}</td>
	<td>
	<?php $popdown = ($loan->is_regulation == true && $loan->regulation_type == 'installments') ? null : 'popdown' ;?>
	<?php $route = ($loan->is_regulation == true && $loan->regulation_type == 'installments') ? 'loan.unblock.store' : 'loan.unblock.form' ;?>

		<a href="{!! route($route,['loanId'=> $loan->id]) !!}" class="btn btn-success">
			<i class="fa fa-unlock-alt"></i> {{ trans('loan.unblock') }}
		</a>
	</td>
</tr>
