<style type="text/css">
	li{
		padding: 5px;
	}
</style>
<table class="pure-table pure-table-bordered">
<caption class="text-gray-700 text-3xl font-semibold leading-tight"> {{ trans('report.trial_balance') }} 

	<div class="text-xs font-semibold mt-3 mb-3 block">
		<span class="bg-green-300 p-2 text-green-900">{{ Request::segment(4)}} </span>
	</div>

</caption>
  <thead>
		<tr>
			<th>{{ trans('account.account_number') }}</th>
			<th>{{ trans('account.entitled') }}</th>
			<th>{{ trans('account.debit') }}</th> 
			<th>{{ trans('account.credit') }}</th> 
		</tr>
	</thead>
	<tbody>
	<?php $debits = 0; $credits = 0;?>
	<?php $accountNature = null; ?>
	<?php $startDate = '2017-01-01 00:00:00'; ?>
	<?php $endDate = Request::segment(4); ?>
	@forelse ($accounts as $account)
	<?php try { ?>
	<tr>
		<td>{!! $account->account_number !!}</td>
		<td>{!! $account->entitled !!}</td>

		
		@if(ends_with($endDate,"12-31"))
		
		<?php $debit =  $account->debitWithoutCloture($endDate)->betweenDates($startDate,$endDate)->sum('amount') ?>
		<?php $credit = $account->creditWithoutCloture($endDate)->betweenDates($startDate,$endDate)->sum('amount') ?>
			
		@else 
			<?php $debit =  $account->debits($endDate)->betweenDates($startDate,$endDate)->sum('amount') ?>
		    <?php $credit = $account->credits($endDate)->betweenDates($startDate,$endDate)->sum('amount') ?>

		@endif
				
		<!--@if (in_array(strtolower($account->account_nature),['passif','charges']))
			{{-- PASSIF & CHARGES SHOULD SHOW IN CEBIT --}}
		    <?php $balance =  abs($credit) - abs($debit);?>
		    <td style="text-align: center">{!! moneyFormat($balance) !!}</td> 
		    <td style="text-align: center"></td>

		@elseif(in_array(strtolower($account->account_nature),['actif','produits']))
			{{-- ACTIF & PRODUIT SHOULD SHOW IN DEBIT --}}
			<?php $balance =  abs($debit) - abs($credit);?>
		    <td style="text-align: center"></td> 
		    <td style="text-align: center">{!! moneyFormat($balance) !!}</td>
		@endif
	    -->


		@if  (in_array(strtolower($account->account_nature),['passif','produits']))
		{{-- ACTIF & PRODUIT SHOULD SHOW IN DEBIT --}}
			<?php $balance =  abs($credit) - abs($debit);?>
			<?php $debits +=  $balance;?>
		    <td style="text-align: center"></td> 
		    <td style="text-align: center">{!! moneyFormat($balance) !!}</td>
			
		@elseif(in_array(strtolower($account->account_nature),['actif','charges']))
			{{-- PASSIF & CHARGES SHOULD SHOW IN CREBIT --}}
		    <?php $balance =  abs($debit) - abs($credit);?>
			<?php $credits +=  $balance;?>

		    <td style="text-align: center">{!! moneyFormat($balance) !!}</td> 
		    <td style="text-align: center"></td>

		@endif
   	</tr
	<?php 
	} catch (Exception $e) {
			
	} ?>
	@empty
		{{-- empty expr --}}
	@endforelse
	<tr>
			<td colspan="2" class="text-center bg-green-100 text-green-900 font-semibold text-xl">{{ trans('general.total') }}</td>
		    <td class="text-center bg-green-700 text-green-100 font-light text-xl">{!! moneyFormat($debits) !!}</td> 
		    <td class="text-center bg-green-700 text-green-100 font-light text-xl">{!! moneyFormat($credits) !!}</td>
	</tr>


	<?php  $balance = abs(abs($credits) - abs($debits)); ?>
	<?php session()->put('total',$balance); ?>

	</tbody>
</table>