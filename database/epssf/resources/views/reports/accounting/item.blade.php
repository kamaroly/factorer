<tr>
	<td>{!! $posting->account->account_number !!}</td>
	<td>{!! $posting->account->entitled !!}</td>
	<td>{!! number_format($posting->debit_amount) !!}</td>
	<td>{!! number_format($posting->credit_amount) !!}</td>
</tr>