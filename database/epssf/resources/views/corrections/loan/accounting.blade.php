<div class="row">
    <div class="col-md-6 debit-accounts border-r border-indigo-500">
    	 <label class="text-yellow-700 font-semibold">{{ trans('loan.credit_account') }}</label>
    	@foreach ($debitPostings as $posting)
    		@include('corrections.loan.account',['name' => 'debit',$posting])
    	@endforeach
    </div>
    <!-- START OF CREDIT ACCOUNT -->
    <div class="col-md-6 credit-accounts border-l">
        	<label class="text-green-700 font-semibold">{{ trans('loan.debit_account') }}</label>
        	@foreach ($creditPostings as $posting)
        		@include('corrections.loan.account',['name' => 'credit',$posting])
        	@endforeach
    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group" >
          <div class="col-xs-6"> </div>
          <div class="col-xs-6">
            <span class="text-yellow-700 text-3xl underline" id="loan-total-debit"></span>
        </div>
    </div>

    <div class="col-md-6 form-group" >
          <div class="col-xs-6"> </div>
          <div class="col-xs-6">
            <span class="text-green-700 text-3xl underline" id="loan-total-credit"></span>
        </div>
  </div>
</div>