@include('partials.landscapecss')

{{-- Start by pulling this member profile --}}
@if (!$contributions->isEmpty())
	@include('reports.member.partials.profile',['member'=>$contributions->last()->member])
@endif 
<table class="pure-table pure-table-bordered ">
<caption class="text-xl text-gray-700 font-semibold"> {{ trans('reports.member_contribution_file') }} {{ Request::segment(4).' Et '.Request::segment(5) }} </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('general.date') }}</th>
	     	<th>{{ trans('loan.nature') }}</th>
			<th>{{ trans('contributions.operation_type') }}</th>
			<th>{{ trans('loan.wording') }}</th>
			<th>{{ trans('loan.saving') }}</th>
	        <th>{{ trans('loan.withdrawal') }}</th>
	        <th>{{ trans('general.balance') }}</th>
	  	</tr>
   	 </thead>
 <tbody class="text-sm">

{{-- Get balance per transactions --}}
<?php $balance = 0; ?>
 @foreach ($contributions as $contribution)
   <?php $balance += (strtolower($contribution->transaction_type) == 'saving') ? $contribution->amount : -1*$contribution->amount; ?>

   @include ('reports.member.item_contribution_record', compact('contribution','balance'))   
 @endforeach
   <tr>
 	<th colspan="4">{{ trans('general.summary') }}</th>

	<th class="text-green-600">{{ moneyFormat($total_savings) }}</th>
	<th class="text-yellow-600">{{ moneyFormat($total_withdrawal) }}</th>
	<th class="text-green-800">{{ moneyFormat($balance) }}</th>
</tr>

 </tbody>
</table>

<table class="pure-table pure-table-bordered text-sm">
  	 	<tr>
  	 	<td>
	  	 		<strong>{{ trans('report.beneficiaire') }}</strong><br/>
				{!!  $contributions->first()->member->names !!}
	  	 	</td>
	  	 	<td>
	  	 		<strong>{{ trans('report.done_by') }}</strong><br/>
	  	 		<?php $user = Sentry::getUser(); ?>
				{!!  $user->first_name !!} {!! $user->last_name !!}
	  	 	</td>
	     	<td><strong>{{ trans('report.authorized') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.gerant') !!}
			</td>
			
			<!--<td><strong>{{ trans('report.tresorien') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.tresorien') !!}
				</td> -->
			<td colspan="2"><strong>{{ trans('report.audited') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.controller') !!}
				</td>

				<td><strong>{{ trans('report.approved_CA') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.president') !!}
				
				</td>
			<!--<td><strong>{{ trans('report.administrator') }}</strong><br/>
				{!! (new Ceb\Models\Setting)->get('general.administrator') !!}
				</td> -->
	  	</tr>
	  	<tr style="height: 50px;">
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  		<!--<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>-->
	  		<td colspan="2"><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  	</tr>
 </table>
