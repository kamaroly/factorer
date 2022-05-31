<table class="pure-table pure-table-bordered" style="border: 1px solid #000">
   <caption class="text-2xl text-gray-800 font-semibold"> 
	{{ ucfirst(trans('reports.reports_journal') )}} {{ Request::segment(4).' / '.Request::segment(5) }}
   </caption>
	<thead>
		<tr>
			<th>{{ trans('general.transactionid') }}</th>
			<th>{{ trans('posting.date') }}</th>
			<th>{{ trans('posting.wording') }}</th>
			<th>{{ trans('posting.account_number') }}</th>		
			<th>{{ trans('posting.debit') }}</th>
			<th>{{ trans('posting.credit') }}</th>
			<th>{{ trans('account.balance') }}</th>
		</tr>
	</thead>
	<tbody>
	<?php $transactionid = null; ?>
	<?php $date = null; ?>
 	<?php $balance = 0; ?>
	<?php $rowspan = 0; ?>
	<?php $latestRow = false ; ?>
	@forelse ($postings as $posting)
		
		<?php try { ?>
		{{-- Debit and credit to get balance --}}
		<?php $balance += $posting->credit_amount; ?>
		<?php $balance -= $posting->debit_amount; ?>

		@if ($date != date('Y-m-d',strtotime($posting->created_at)))
		<?php $date = date('Y-m-d',strtotime($posting->created_at)); ?>
		<tr style="border-bottom: 1px solid #000">
			<td class="account-details" colspan="6" style="font-weight: bold">{!! $date !!}</td>
		</tr>
			<?php $latestRow = true; ?>
		@endif

		<tr>
			{{-- control the display of the transactions --}}
			@if ($transactionid != $posting->transactionid)
				<?php $transactionid = $posting->transactionid ?> 
				{{-- detrmine how many rows do we have to span as per affected account for this transaction --}}
				<?php $rowspan = $postings->where('transactionid',$transactionid)->count(); ?>

	
				<?php $latestRow = false; ?>
				<td rowspan="{!! $rowspan !!} ">
				    {!! $posting->transactionid !!}
				 </td>
				<td rowspan="{!! $rowspan !!}">
				    {!! date('Y-m-d',strtotime($posting->created_at)) !!}
				</td>
				<td rowspan="{!! $rowspan !!}">{!! $posting->wording !!}</td>
			@else

			<?php $latestRow = true; ?>
			@endif
				<td>

					{!! $posting->account_number  !!} 
					  - 
					{!! $posting->account->entitled  !!} 
				</td>
				<td>{!! number_format($posting->debit_amount) !!}</td>
				<td>{!! number_format($posting->credit_amount) !!}</td>
				<td>{!! number_format(abs($balance)) !!}</td>
			</tr>
		<?php 
		} catch (Exception $e) {
			
		}
		?>

	@empty
		{{-- empty expr --}}
	@endforelse
	</tbody>
</table>