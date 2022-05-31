<?php $account = $posting->account; ?>
@if ( !empty($account))
	<tr>
		<td>{!! $account->account_number !!}</td>
		<td>{!! $account->entitled !!}</td>
		<td>{!! (strtolower($posting->transaction_type) == 'debit') ? number_format($posting->amount) : null !!}</td>
		<td>{!! (strtolower($posting->transaction_type) == 'credit') ? number_format($posting->amount) : null !!}</td>
	</tr>
@endif