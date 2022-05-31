<?php $loan_to_repay = 0; ?>
<?php $interest = 0; ?>
<?php $monthly_fees = 0; ?>
<?php $tranches = 0; ?>

<tr class="green" style="border-top: 1px solid #fff;font-weight: 800;color: green">
	<td colspan="3" >
		{!! trans('loan.contract_number') !!} : {!! $loan->loan_contract !!}
	</td>
	
	<td colspan="2" >  	
		{!! trans('loan.cautionneur1') !!} : {!! (empty($loan->cautionneur1) == false )?$loan->cautionneur1->cautionneur_adhresion_id:null !!}
	</td>
    <td colspan="4" >   	
		{!! trans('loan.cautionneur2') !!} : {!! (empty($loan->cautionneur2)==false )?$loan->cautionneur2->cautionneur_adhresion_id:null;  !!}
	</td>
</tr>
<tr>
	<td>{!! $loan->letter_date->format('Y-m-d') !!}</td>
 	<td>{{  trans('loan.loan') }}</td>
	<td>{!! $loan->operation_type !!}</td>
	<td>{!! $loan->comment !!} </td>
	<td>{!! number_format((int) $loan->loan_to_repay) !!} </td>
    <td>{!! number_format((int) $loan->interests) !!} </td>
    <td>{!! number_format((int) $loan->monthly_fees) !!} </td>
    <td>0</td>
</tr>
<?php $loan_to_repay += $loan->loan_to_repay; ?>
<?php $interest += $loan->interests; ?>
<?php $monthly_fees += $loan->monthly_fees; ?>
<?php $tranches += 0; ?>

@foreach ($loan->refunds as $refund)
<tr>
	<td>{!! $refund->created_at->format('Y-m-d') !!}</td>
 	<td>{{ trans('loan.refund') }}</td>
	<td>{{ trans('loan.refund') }}</td>
	<td>{{ $refund->wording }} </td>
    <td></td>
	<td></td>
    <td>{!! number_format((int)$refund->monthly_fees) !!} </td>
    <td>{!! number_format((int)$refund->amount) !!}</td>
</tr>
<?php $tranches += $refund->amount; ?>
@endforeach
<tr>
	<td colspan="4" style="text-align: center;font-weight: 600;">{{ trans('loan.movement_total') }}</td>
 	<td style="text-align: center;font-weight: 600;">{{ number_format((int) $loan_to_repay) }}</td>
	<td style="text-align: center;font-weight: 600;">{{ number_format((int) $interest) }}</td>
	<td style="text-align: center;font-weight: 600;">{{ number_format((int) $monthly_fees) }} </td>
    <td style="text-align: center;font-weight: 600;">{{ number_format((int) $tranches) }} </td>
</tr>
<tr style="border-bottom: 1px solid #000;">
	<td colspan="6"></td>
	<td class="orange" style="text-align: right;font-weight: 600;text-decoration: underline;">{{ trans('loan.loan_balance') }}</td>
	<td class="orange" style="text-align: center;font-weight: 600;text-decoration: underline;">{{ number_format((int) $loans->sum('loan_to_repay') - $tranches) }} </td>
</tr>

