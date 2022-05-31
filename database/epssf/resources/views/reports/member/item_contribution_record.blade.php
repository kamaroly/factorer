<tr>
 	<td>{{ $contribution->created_at->format('Y-m-d') }}</td>
 	<td>{{ trans('contribution.'.strtolower($contribution->transaction_type)) }}</td>
	<td>{{ trans('contribution.'.$contribution->transaction_reason)}}</td>
	<td>{{ str_replace('contributions.contribution_for_the_month_of',trans('contributions.contribution_for_the_month_of'),strtolower($contribution->wording) ) }}</td>
	<td>{{ (strtolower($contribution->transaction_type) == 'saving')?  moneyFormat($contribution->amount) :  0 }}</td>
	<td>{{ (strtolower($contribution->transaction_type) == 'withdrawal')? moneyFormat($contribution->amount)  : 0 }}</td>
	<td>{{ moneyFormat($balance) }}</td>
</tr>
