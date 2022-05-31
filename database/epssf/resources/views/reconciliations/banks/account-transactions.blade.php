<div class="row ">
  <h4 class="card-header text-green-500 col-md-8 text-center">{{ trans('general.ceb') }} </h4> 
</div>
<table  class="table">
	<thead class="bg-green-300">
		<tr>
			<th colspan="2">{{ trans('account.transaction_number') }}</th>
			<td colspan="2">
				<span id="js-account-transaction-number" class="text-green-800 text-3xl font-semibold">
					{{ number_format($transactions->count(),0) }}
				</span>
			</td>
		</tr>
		<tr>
			<th colspan="2">{{ trans('account.credit') }}</th>
			<td colspan="2">
				<span id="js-account-credit" class="text-green-800 text-3xl font-semibold">
					{{ number_format($transactions->where('transaction_type','credit')->sum('amount'),0) }}</span>
			</td>
		</tr>
		<tr>
			<th colspan="2">{{ trans('account.debit') }}</th>
			<td colspan="2">
				<span id="js-account-debit" class="text-green-800 text-3xl font-semibold">
					{{ number_format($transactions->where('transaction_type','debit')->sum('amount'),0) }}</span>
			</td>
		</tr>
		<tr>
			<th colspan="2">{{ trans('general.balance') }}</th>
			<td colspan="2">
				<span id="js-account-balance" class="text-green-800 text-3xl font-semibold">

				{{ number_format(($transactions->where('transaction_type','credit')->sum('amount') - $transactions->where('transaction_type',
					'debit')->sum('amount')),0) }}
				</span>
			</td>
		</tr>
	</thead>
</table>
<table class="table" id="js-sortable-table-ceb"> 
	<caption>
		<input type="text" class="form-control rounded-lg"  placeholder="Search Transaction"  
				onkeyup="sortTable(this,'js-sortable-table-ceb')">
	</caption>
	
	<thead> 
		<tr>
			<th>{{ trans('general.id') }}</th>
			<th>{{ trans('account.transaction_id') }}</th>
			<th>{{ trans('account.wording') }}</th>
			<th>{{ trans('general.debited_amount') }}</th>
			<th>{{ trans('general.credited_amount') }}</th>
		</tr>
	</thead>
	<tbody> 
	 @foreach($transactions as $transaction)
		<tr >
			<td>
				<input type="checkbox" name="matching_ceb[]" 
					   onclick="reconcileAccount(this,{{ $transaction->amount }},'{{ $transaction->transaction_type }}')" 
					   value="{{ $transaction->id }}">
			</td>
			<td>{{ $transaction->transactionid }}</td>
			<td>{{ $transaction->wording }}</td>
			<td>
				@if ($transaction->transaction_type == 'debit')
					{{ $transaction->amount }}
				@else
					{{ 0 }}
				@endif
			</td>
			<td>
				@if ($transaction->transaction_type == 'credit')
					{{ $transaction->amount }}
				@else
					{{ 0 }}
				@endif
			</td>
		 </tr>
     @endforeach
	</tbody>
</table>

<script type="text/javascript">
	var cebTransactionCount = document.getElementById('js-account-transaction-number');
	var cebCredit             = document.getElementById('js-account-credit');
	var cebDebit              = document.getElementById('js-account-debit');
	var cebBalance            = document.getElementById('js-account-balance');

	/**
	 * Update Pannel based on the checked transactions
	 * @param  checkbox         
	 * @param  $transactionType 
	 * @return  
	 */
	function reconcileAccount(checkBox,amount,transactionType) {
		var rowAmount        = parseInt(amount);

		var transactionCount = parseInt(cebTransactionCount.innerText.replace(/,/g, ""));
		var creditAmount     = parseInt(cebCredit.innerText.replace(/,/g, ""));
		var debitAmount      = parseInt(cebDebit.innerText.replace(/,/g,""))
		var balanceAmount    = parseInt(cebBalance.innerText.replace(/,/g,""))

		// If this is checked, remove amount and 
		// reduce cebTransaction Number and amount
		 if (checkBox.checked == true){
		 	switch (transactionType) {
		 		case 'debit':
		 			// Update Debit
		 			debitAmount = debitAmount - rowAmount;
		 			cebDebit.innerText   = (debitAmount).toLocaleString();
		 			cebBalance.innerText = (creditAmount - debitAmount).toLocaleString(); 
		 			break;
		 		case 'credit':
		 			// update Credit
					creditAmount         = creditAmount - rowAmount;
					cebCredit.innerText  = (creditAmount).toLocaleString();
					cebBalance.innerText = (creditAmount - debitAmount).toLocaleString(); 
		 			break;
		 	}
		 	// Update Transaction Count also
	 	 	cebTransactionCount.innerText = (transactionCount - 1).toLocaleString();
			return;
		}

		// If we reach here it means checkbox. is not checked.
		// Increase value on the pannel for the person to see
		// latest number
	 	switch (transactionType) {
	 		case 'debit':
	 			// Update Debit
	 			debitAmount = debitAmount + rowAmount;
	 			cebDebit.innerText   = (debitAmount).toLocaleString();
	 			cebBalance.innerText = (creditAmount + debitAmount).toLocaleString(); 
	 			break;
	 		case 'credit':
	 			// update Credit
				creditAmount         = creditAmount + rowAmount;
				cebCredit.innerText  = (creditAmount).toLocaleString();
				cebBalance.innerText = (creditAmount + debitAmount).toLocaleString(); 
	 			break;
	 	}

 	// Finally update transaction count
 	cebTransactionCount.innerText = (transactionCount + 1).toLocaleString();
	}
</script>