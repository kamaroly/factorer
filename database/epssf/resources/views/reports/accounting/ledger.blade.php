@if (!$postings->isEmpty())
<?php $account = $postings->first()->account; ?>
<div class="text-center font-light">
	<b> {{ ucfirst(trans('reports.grand_livre') )}} </b>
	<strong class="text-green-800">{{ trans('account.account_number') }} : </strong>{!! $account->account_number !!}, 
	<strong class="text-green-800">{{ trans('account.entitled') }} :</strong> {!! $account->entitled !!}, 
	<strong class="text-green-800">{{ trans('accounting_nature.account_nature') }} :</strong> {!! $account->account_nature !!}, 
	<strong class="text-green-800">{{ trans('general.between') }} :</strong> {!! request()->segment(4) !!} {{ trans('general.and') }} {!! request()->segment(5) !!}
</center>
<hr/>
<table class="pure-table pure-table-bordered">
<caption class="text-gray-700 font-semibold text-2xl"> {{ trans('report.ledger') }} </caption>
	<thead>
		<tr>
			<th>{{ trans('account.date') }}</th>
			<th>{{ trans('posting.transactionid') }}</th>
			<th>{{ trans('account.wording') }}</th>
			<th>{{ trans('account.debit') }}</th>
			<th>{{ trans('account.credit') }}</th>
			<th>{{ trans('account.balance') }}</th>
		</tr>
	</thead>
	
   
     @if  (in_array(strtolower($account->account_nature),['asset','expenditure']))
	<tbody>
		<tr>
			<th colspan="3">{{ trans('general.opening_balance') }}</th>
			<th>{{ moneyFormat($debits) }}</th>
			<th>{{ moneyFormat($credits) }}</th>
			<th class="text-green-700">{{ moneyFormat($debits - $credits) }}</th>
		</tr>

	<?php $totalDebit  = 0; ?>
	<?php $totalCrebit = 0; ?>
	<?php $balance     = $debits - $credits; ?>
	@forelse ($postings as $posting)
	<tr>
		<td>{!! $posting->created_at->format('Y-m-d') !!}</td>
		<td>{!! $posting->transactionid !!}</td>
		<td>{!! $posting->wording !!}</td>
		<?php $totalDebit += abs($posting->debit_amount); ?>
		<?php $balance  += abs($posting->debit_amount);?>

		<?php $totalCrebit+= abs($posting->credit_amount); ?>
		<?php $balance  -= abs($posting->credit_amount);?>

		<td>{!! number_format(abs($posting->debit_amount)) !!}</td>
		<td>{!! number_format(abs($posting->credit_amount)) !!}</td>
		<td>{!! number_format($balance) !!}</td>
	</tr>
	@empty
		{{-- empty expr --}}
	@endforelse
	<tr>
			<th colspan="3"><b>{{ trans('posting.movement_total') }}:</b></th>
			<th>{!! number_format($totalDebit) !!}</th>
			<th>{!! number_format($totalCrebit) !!}</th>
			<th>{!! number_format($balance) !!}</th>
		</tr>
	</tbody>
</table>

@elseif  (in_array(strtolower($account->account_nature),['liabilities','income']))
	<tbody>
		<tr>
			<th colspan="3">{{ trans('general.opening_balance') }}</th>
			<th>{{ moneyFormat($debits) }}</th>
			<th>{{ moneyFormat($credits) }}</th>
			<th class="text-green-700">{{ moneyFormat($credits - $debits) }}</th>
		</tr>
	<?php $totalDebit  = 0; ?>
	<?php $totalCrebit = 0; ?>
	<?php $balance     = $credits - $debits; ?>
	@forelse ($postings as $posting)
	<tr>
		<td>{!! $posting->created_at->format('Y-m-d') !!}</td>
		<td>{!! $posting->transactionid !!}</td>
		<td>{!! $posting->wording !!}</td>
		<?php $totalDebit += abs($posting->debit_amount); ?>
		<?php $balance  -= abs($posting->debit_amount);?>

		<?php $totalCrebit+= abs($posting->credit_amount); ?>
		<?php $balance  += abs($posting->credit_amount);?>

		<td>{!! number_format(abs($posting->debit_amount)) !!}</td>
		<td>{!! number_format(abs($posting->credit_amount)) !!}</td>
		<td>{!! number_format(abs($balance)) !!}</td>
	</tr>
	@empty
		{{-- empty expr --}}
	@endforelse
	<tr>
			<th colspan="3"><b>{{ trans('posting.movement_total') }}:</b></th>
			<th>{!! number_format($totalDebit) !!}</th>
			<th>{!! number_format($totalCrebit) !!}</th>
			<th>{!! number_format(abs($balance)) !!}</th>
		</tr>
	</tbody>
</table>
@endif
@endif