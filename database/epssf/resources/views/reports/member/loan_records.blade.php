@include('partials.landscapecss')
{{-- Start by pulling this member profile --}}
@if (!empty($loans))
    <?php $member = (new \Ceb\Models\User)->findByAdhersion($loans->first()->adhersion_id) ;?>
	@include('reports.member.partials.profile',compact('member')) 
@endif

<style type="text/css">
	      .markdown-body table th, .markdown-body table td {
          padding: 6px 6px;
          border: 1px solid #ddd;
      }
</style>
{{-- Show select inputs --}}
@include('reports.member.date_range',['url' => 'reports/members/loanrecords/{startDate}/{endDate}/0/'.$loans->first()->adhersion_id])

<table class="pure-table pure-table-bordered text-center">
	<caption class="text-gray-700 text-3xl font-semibold leading-tight"> 
		{{ trans('reports.member_loan_records_file') }} 
	@if ($previousLoanBalance > 0)
		<span class="bg-yellow-500 text-yellow-900 px-2 py-1 font-light text-xl rounded-lg">
			{{ trans('general.previous_loan_balance') }} : {{ $previousLoanBalance }}
		</span>
	@endif
			<span class="bg-yellow-500 text-yellow-900 px-2 py-1 font-light text-xl rounded-lg">
			{{ trans('general.previous_loan_balance') }} : {{ $previousLoanBalance }}
		</span>	
	</caption>
  	 <thead>
  	 	<tr class="text-sm">
	  	 	<th>{{ trans('general.date') }}</th>
	     	<th>{{ trans('loan.nature') }}</th>
			<th>{{ trans('loan.operation_type') }}</th>
			<th>{{ trans('loan.wording') }}</th>
			<th>{{ trans('loan.loan') }}</th>
	        <th>{{ trans('loan.interests') }}</th>
	        <th>{{ trans('loan.installment_payments') }}</th>
	        <th>{{ trans('loan.installements') }}</th>
	        <th>{{ trans('general.balance') }}</th>
	  	</tr>
   	 </thead>
 <tbody class="text-sm">
		<?php	$loan_to_repay = 0; ?>
		<?php	$interest      = 0; ?>
		<?php	$monthly_fees  = 0; ?>
		<?php	$tranches      = 0; ?>
		<?php   $loan_contract = 0 ;?>
		<?php   $balance       = 0 ;?>

  <?php $first_loan = $loans->first()->id; ?>
  @foreach ($loans as $loan)

	<?php	$balance  += ($loan->loan_amount-$loan->tranches); ?>
  	{{-- HEADER TABLE ONLY SHOW HEADERS FOR ORDINARY LOAN--}}
  	@if ((strpos($loan->operation_type,'ordinary_loan') !== FALSE || strpos($loan->operation_type,'emergency_loan') !== FALSE) && $loan->is_regulation == false)
  		{{-- SUMMARY TABLE --}}
		@if ($loan->id !== $first_loan)
			@include('reports.member.item_loan_record_summary')
			     	<?php	$loan_to_repay = 0; ?>
					<?php	$interest 	   = 0; ?>
					<?php	$monthly_fees  = 0; ?>
					<?php	$tranches  	   = 0; ?>
					<?php	$balance       = $loan->loan_amount; ?>

		@endif

  		<?php $tranches = 0; ?>
		@include('reports.member.item_loan_record_header')
  	@endif
	
	<tr>
		<td>{!! date('Y-m-d',strtotime($loan->created_at)) !!}</td>
	 	<td>{!! trans('loan.'.trim($loan->record_type)) !!}</td>
		<td>{!! trans('loan.'.$loan->operation_type)  !!}

			   <?php
			     $cautions = Ceb\Models\MemberLoanCautionneur::byTransaction($loan->transactionid)->get(); 
			     if($loan->transactionid == '20200212102'){
			     	dd($loan);
			     }
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
		<td>{!! str_replace('EMERGENCY_LOAN_',trans('general.emergency_loan'),$loan->wording) !!} </td>
		<td>{!! ($loan->operation_type == 'installments')?'': number_format((int) $loan->loan_amount) !!} </td>
	    <td>{!! number_format((int) $loan->interests) !!} </td>
	    <td>{!! number_format((int) $loan->monthly_fees) !!} </td>
	    <td>{!! number_format((int) $loan->tranches) !!} </td>
	    <td class="text-yellow-700">{!! number_format((int) ( $balance )) !!} </td>
	</tr>

  	<?php $loan_to_repay += $loan->loan_amount; ?>
	<?php $interest 	 += $loan->interests; ?>
	<?php $monthly_fees  += $loan->monthly_fees; ?>
	<?php $tranches  	 += $loan->tranches; ?>	


 
  @endforeach

   @include('reports.member.item_loan_record_summary')
   
 </tbody>
</table>
<table class="pure-table pure-table-bordered text-sm">
  	 	<tr>
  	 	<td>
	  	 		<strong>{{ trans('report.beneficiaire') }}</strong><br/>
	  	 		<?php $member = (new \Ceb\Models\User)->findByAdhersion($loans->first()->adhersion_id) ;?>
				{!!$member->first_name!!} {!! $member->last_name !!}
	  	 	</td>
	  	 	<td>
	  	 		<strong>{{ trans('report.done_by') }}</strong><br/>
	  	 		<?php $user = Sentry::getUser(); ?>
				{!!  $user->first_name !!} {!! $user->last_name !!}
	  	 	</td>
	     	<td><strong>{{ trans('report.authorized') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.gerant') !!}
			</td>
			<td colspan="2"><strong>{{ trans('report.audited') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.controller') !!}
				</td>
			<td><strong>{{ trans('report.approved_CA') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.president') !!}
				</td>
			<!--<td><strong>{{ trans('report.tresorien') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.tresorien') !!}
				</td>  -->
			
			<!--<td><strong>{{ trans('report.administrator') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.administrator') !!}
				</td> -->
	  	</tr>
	  	<tr style="height: 50px;">
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  		<!--<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td> -->
	  		<td colspan="2"><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  	</tr>
 </table>



