<div class="row ">
  <h4 class="card-header text-green-500 col-md-8 text-center">{{ trans('general.bank-results') }} </h4>
</div>
<table class="table table-auto">
	<thead class="bg-blue-200">
		<tr>
			<th colspan="2">{{ trans('account.transaction_number') }}</th>
			<td colspan="2">
				<span id="js-bank-transaction-number" class="text-blue-800 text-3xl font-semibold">{{ number_format($uploadedData->count(),0) }}</span>
			</td>
		</tr>
		<tr>
			<th colspan="2">{{ trans('account.redit') }}</th>
			<td colspan="2">
				<span id="js-bank-credit" class="text-blue-800 text-3xl font-semibold">{{ number_format($uploadedData->sum('credit'),0) }}</span>
			</td>
		</tr>
		<tr>
			<th colspan="2">{{ trans('account.debit') }}</th>
			<td colspan="2">
				<span id="js-bank-debit" class="text-blue-800 text-3xl font-semibold">{{ number_format($uploadedData->sum('debit'),0) }}</span>
			</td>
		</tr>
		<tr>
			<th colspan="2">{{ trans('general.balance') }}</th>
			<td colspan="2">
				<span id="js-bank-balance" class="text-blue-800 text-3xl font-semibold">{{ number_format(($uploadedData->sum('credit') - $uploadedData->sum('debit')),0) }}</span>
			</td>
		</tr>
	</thead>
</table>
<table class="table table-auto bg-blue-100" id="js-sortable-table-bank-file"> 
	<caption>
		<input type="text" class="form-control rounded-lg" placeholder="Search Transaction" 
				onkeyup="sortTable(this,'js-sortable-table-bank-file')">
	</caption>
		<thead> 
		<tr>
			<th></th>
			<th>{{ trans('account.wording') }}</th>
			<th>{{ trans('account.debit') }}</th>
			<th>{{ trans('account.credit') }}</th>
		</tr>
	</thead>
	<tbody> 
	 @foreach($uploadedData as $key => $transaction)
		<tr >
			<td>
				<input type="checkbox" name="matching_bank[]" value="{{ $key }}"
					  onclick="reconcileBank(this,{{ $transaction['amount'] }},'{{ $transaction['transaction_type'] }}')">
			</td>
			<td><div class="block text-gray-900 font-semibold">{{ $transaction['date'] }}</div> {{ $transaction['libelle'] }}</td>
			<td>{{ $transaction['debit'] }}</td>
			<td>{{ $transaction['credit'] }}</td>
		 </tr>
     @endforeach
	</tbody>
</table>

<script type="text/javascript">
	var bankTransactionCount   = document.getElementById('js-bank-transaction-number');
	var bankCredit             = document.getElementById('js-bank-credit');
	var bankDebit              = document.getElementById('js-bank-debit');
	var bankBalance            = document.getElementById('js-bank-balance');

	/**
	 * Update Pannel based on the checked transactions
	 * @param  checkbox         
	 * @param  $transactionType 
	 * @return  
	 */
	function reconcileBank(checkBox,amount,transactionType) {
		var rowAmount        = parseInt(checkBox.value);

		var transactionCount = parseInt(bankTransactionCount.innerText.replace(/,/g, ""));
		var creditAmount     = parseInt(bankCredit.innerText.replace(/,/g, ""));
		var debitAmount      = parseInt(bankDebit.innerText.replace(/,/g,""))
		var balanceAmount    = parseInt(bankBalance.innerText.replace(/,/g,""))

		// If this is checked, remove amount and 
		// reduce bankTransaction Number and amount
		 if (checkBox.checked == true){
		 	switch (transactionType) {
		 		case 'debit':
		 			// Update Debit
		 			debitAmount = debitAmount - rowAmount;
		 			bankDebit.innerText   = (debitAmount).toLocaleString();
		 			bankBalance.innerText = (creditAmount - debitAmount).toLocaleString(); 
		 			break;
		 		case 'credit':
		 			// update Credit
					creditAmount         = creditAmount - rowAmount;
					bankCredit.innerText  = (creditAmount).toLocaleString();
					bankBalance.innerText = (creditAmount - debitAmount).toLocaleString(); 
		 			break;
		 	}
		 	// Update Transaction Count also
	 	 	bankTransactionCount.innerText = (transactionCount - 1).toLocaleString();
			return;
		}

		// If we reach here it means checkbox. is not checked.
		// Increase value on the pannel for the person to see
		// latest number
	 	switch (transactionType) {
	 		case 'debit':
	 			// Update Debit
	 			debitAmount = debitAmount + rowAmount;
	 			bankDebit.innerText   = (debitAmount).toLocaleString();
	 			bankBalance.innerText = (creditAmount + debitAmount).toLocaleString(); 
	 			break;
	 		case 'credit':
	 			// update Credit
				creditAmount         = creditAmount + rowAmount;
				bankCredit.innerText  = (creditAmount).toLocaleString();
				bankBalance.innerText = (creditAmount + debitAmount).toLocaleString(); 
	 			break;
	 	}

 	// Finally update transaction count
 	bankTransactionCount.innerText = (transactionCount + 1).toLocaleString();
	}
</script>