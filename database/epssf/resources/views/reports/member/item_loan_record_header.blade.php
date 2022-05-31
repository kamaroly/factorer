<tr class="green" style="border-top: 1px solid #fff;font-weight: 800;color: green">
	<td colspan="3" >
		{!! trans('loan.contract_number') !!} : {!! $loan->loan_contract !!}
	</td>
	<?php // $counter  = 1 ;?>
	<?php // $loan = \Ceb\Models\Loan::find($loan->loan_id);?>
{{-- 	@foreach ($loan->cautions as $caution)
	<td colspan="{!! $counter == 1 ? '2': '4' !!}" >  	
		{!! trans('loan.cautionneur'.$counter) !!} 
		  :{!! $caution->cauttionneur->names !!} ({!! $caution->cautionneur_adhresion_id !!})
	</td>
	@endforeach --}}
</tr>