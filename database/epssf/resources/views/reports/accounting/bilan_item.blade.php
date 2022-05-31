<table class="pure-table pure-table-bordered">
<caption>{!! $accounts->first()->account_nature !!}</caption>
	<thead>
		<tr>
			<th>{{ trans('account.account_number') }}</th>
			<th>{{ trans('account.entitled') }}</th>
			{{-- <th>{{ trans('account.debit') }}</th>  --}}
			{{-- <th>{{ trans('account.credit') }}</th>  --}}
			<th>{{ trans('account.balance') }}</th>
		</tr>
	</thead>
	<tbody>
	<?php $debits = 0; $credits = 0;?>
	<?php $accountNature = null; ?>
	 <!--startdate  has been  hard  coded  because  ceb postings  start in 2017 no need  to choose  between  two date -->
	<?php $startDate = '2017-01-01 00:00:00'; ?>
	<?php $endDate = Request::segment(4); ?>
	@forelse ($accounts as $account)
	<?php try { ?>
	<tr>
		<td>{!! $account->account_number !!}</td>
		<td>{!! $account->entitled !!}</td>
		<?php $debit =  $account->debits()->betweenDates($startDate,$endDate)->sum('amount') ?>
	    {{-- <td style="text-align: center">{!! number_format(abs($debit)) !!}</td>  --}}
		<?php  $credit = $account->credits()->betweenDates($startDate,$endDate)->sum('amount') ?>
	    {{-- <td style="text-align: center">{!! number_format(abs($credit))  !!}</td> --}}
		<td style="text-align: center">

		@if ($accountType == 'passif' || $accountType == 'produits')
		    <?php $balance =  abs($credit) - abs($debit);?>
		@else
			<?php $balance =  abs($debit) - abs($credit);?>
		@endif
		
		@if ($balance < 0)
			({!! number_format(abs($balance))  !!})
		@else 
			{!! number_format(abs($balance))  !!}
		@endif
		</td>

		<?php $debits+=$debit; $credits+=$credit; ?>
	</tr>
	<?php 
	} catch (Exception $e) {
			
	} ?>
	@empty
		{{-- empty expr --}}
	@endforelse

	BILAN_RESULTS

	BILAN_TOTAL

	<?php  $balance = abs(abs($credits) - abs($debits)); ?>
	<?php session()->put('total',$balance); ?>

	</tbody>
</table>