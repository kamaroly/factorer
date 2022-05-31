<tr>
	<td>{!! $refund->created_at->format('Y-m-d') !!}</td>
 	<td>{{ trans('loan.refund') }}</td>
	<td>{{ trans('loan.refund') }}</td>
	<td>{{ $refund->wording }} </td>
    <td></td>
	<td></td>
    <td>{!! $refund->monthly_fees ? number_format((int)$refund->monthly_fees):'' !!} </td>
    <td>{!! number_format((int)$refund->amount) !!}</td>
</tr>