<style type="text/css">
	li{
		padding: 5px;
	}
</style>
<table class="pure-table pure-table-bordered text-center">
<caption class="text-gray-700 text-3xl font-semibold leading-tight"> {{ trans('reports.loans_at_risk') }} 

	<div class="text-xs font-semibold mt-3 mb-3 block">
		<span class="bg-red-300 p-2 text-red-900">{{ trans('general.more_than_months') }} 6</span>
		<span class="bg-orange-300 p-2 text-orange-900">{{ trans('general.more_than_months') }} 4 - 6</span>
		<span class="bg-yellow-300 p-2 text-yellow-900">{{ trans('general.more_than_months') }} 3</span>
	</div>

</caption>
  	 <thead>
  	 	<tr class="text-sm">
	  	 	<th>{{ trans('general.date') }}</th>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
			<th>{{ trans('loan.operation_type') }}</th>
			<th>{{ trans('member.names') }}</th>
	        <th>{{ trans('member.institution') }}</th>
			<th>{{ trans('loan.last_refunded_at') }}</th>			
			<th>{{ trans('general.risk_level') }}</th>
			<th>{{ trans('loan.loan') }}</th>
		    <th>{{ trans('loan.loan_contract') }}</th>
	        <th>{{ trans('loan.installment_payments') }}</th>
	        <th>{{ trans('general.balance') }}</th>
	  	</tr>
   	 </thead>
 <tbody class="text-sm">
 @foreach ($loans as $loan)
    <?php 
    	$color = '';
    ?>
 	<tr >
		<td>{!! date('Y-m-d',strtotime($loan->loan_date)) !!}</td>
	 	<td>{!! trim($loan->adhersion_id) !!}</td>
		<td>{!! trans('loan.'.$loan->operation_type)  !!}

			   <?php
			   	 $cautions = collect([]);
			     //   $cautions = Ceb\Models\MemberLoanCautionneur::byTransaction($loan->transactionid)->get(); 
			   ?>
				@if (! $cautions->isEmpty())
					{{-- SHOW CAUTIONNEURS --}}
					<table class="bg-yellow-100 text-yellow-700">
						<caption class="bg-yellow-700 font-semibold text-xs text-yellow-100">
						  {{ trans('loan.cautionneurs') }}
						</caption>
						<tr class="bg-yellow-700 text-xs text-yellow-700">
						@foreach ($cautions as $guarantor)
							<td>
								<span class="block w-full bg-gray-100 text-xs font-semibold">{{ $guarantor->cautionneur_adhresion_id  }}</span> 
								<span class="block w-full bg-gray-100 text-xs">{{ number_format($guarantor->amount)  }} RWF</span>
							</td>
						@endforeach
						</tr>
					</table>
				@endif
		</td>
	
		<td>
		{{ $loan->name }}</td>
 	    <td>{{ $loan->institution }}</td>
 	   
		<td class="font-semibold">{{ $loan->last_refunded_at }}</td>
		<td class="{{ $loan->class }} font-semibold">{{ $loan->risk_level }}</td>
		<td>{!! number_format((int) $loan->loan_to_repay) !!} </td>
	    <td>{!! $loan->loan_contract !!} </td>
	    <td>{!! number_format((int) $loan->monthly_fees) !!} </td>
	    <td class="text-yellow-700">{!! number_format((int) abs($loan->balance)) !!} </td>
 	</tr>
 @endforeach
 </tbody>
</table>