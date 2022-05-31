
<div class="box-body">
<div class="box-header with-border">
  <h3 class="box-title">{{ trans('loan.special_loan') }}</h3>
  <div class="loan-notifications">
   </div>
</div>
<div class="row" style="background-color: #99C68E">
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.previous_loan_contract_number') }}</label>
    {!! Form::input('text', 'current_loan_contract',$activeLoan->loan_contract,
                  ['class'=>'form-control'])
    !!}
  </div>
  </div>
  <div class="col-md-2">
  <div class="form-group">
   <label>{{ trans('loan.previous_number_of_installments') }}</label>
    {!! Form::input('text', 'current_number_of_installments',$activeLoan->tranches_number,
                  ['class'=>'form-control'])
    !!}
  </div>
  </div>
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.current_loan_to_repay') }}</label>
    {!! Form::input('text', 'current_loan_to_repay',$activeLoan->loan_to_repay,
                  ['class'=>'form-control'])
    !!}
  </div>
  </div>
  <div class="col-md-2">
  <div class="form-group">
   <label>{{ trans('loan.current_monthly_fees') }}</label>
       {!! Form::input('text', 'current_monthly_fees',$activeLoan->monthly_fees,
                  ['class'=>'form-control','id'=>'current_monthly_fees'])
    !!}
   </div>
  </div>
    <div class="col-md-2">
  <div class="form-group">
   <label>{{ trans('loan.previous_loan_balance') }}</label>
       {!! Form::input('text', 'previous_loan_balance',($member->exists) ?$member->loanBalance() : 0,
                  ['class'=>'form-control'])
    !!}
   </div>
  </div>
</div>
<div class="row">

  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_number_of_installments') }}</label>
	{!! Form::selectRange('tranches_number', 1, env('LOAN_MAXIMUM_INSTALLMENT',72),
                          isset($loanInputs['tranches_number'])?$loanInputs['tranches_number']:null,
                         ['class'=>'form-control loan-select','id'=>'numberOfInstallment'])
    !!}
  </div>
  </div>
  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_wished_amount') }}</label>
  	{!! Form::input('text', 'wished_amount',isset($loanInputs['wished_amount'])?$loanInputs['wished_amount']:null,
                  ['class'=>'form-control loan-input','id'=>'wished_amount'])
    !!}
  </div>
  </div>
  <div class="col-md-4">
  <div class="form-group">

   <label>{{ trans('loan.special_operation_type') }}</label>
   {!! Form::select('operation_type',
                   $loanTypes,
                   isset($loanInputs['operation_type'])?$loanInputs['operation_type']:null,
                  ['class'=>'form-control loan-select','id'=>'operation_type'])
  !!}
   </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_loan_to_repay') }}</label>
  {!! Form::input('text', 'loan_to_repay',isset($loanInputs['loan_to_repay'])?$loanInputs['loan_to_repay']:null,
                  ['class'=>'form-control loan-input','id'=>'loanToRepay'])
    !!}
  </div>
  </div>
  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_monthly_installments') }}</label>
  {!! Form::input('text', 'monthly_fees',isset($loanInputs['monthly_fees'])?$loanInputs['monthly_fees']:null,
                  ['class'=>'form-control loan-input','id'=>'monthlyInstallments'])
    !!}
  </div>
  </div>
  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_interests') }}</label>
  {!! Form::input('text', 'interests',isset($loanInputs['interests'])?$loanInputs['interests']:null,
                  ['class'=>'form-control loan-input','id'=>'interests'])
    !!}
  </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_net_to_receive') }}</label>
  {!! Form::input('text', 'amount_received',isset($loanInputs['net_to_receive'])?$loanInputs['net_to_receive']:null,
                  ['class'=>'form-control loan-input','id'=>'netToReceive'])
    !!}
  </div>
  </div>
  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_number_of_cheque') }}</label>   
  {!! $errors->first('cheque_number','<label class="has-error">:message</label>') !!} 
  {!! Form::input('text', 'cheque_number',isset($loanInputs['cheque_number'])?$loanInputs['cheque_number']:null,
                  ['class'=>'form-control loan-input','id'=>'cheque_number'])
    !!}
  </div>
  </div>
  <div class="col-md-4">
  <div class="form-group">
   <label>{{ trans('loan.special_bank') }}</label>
    {!! Form::select('bank_id',
                   ['BK'=>'Bank of Kigali',
                   'BCR'=>'Bank Commercial du Rwanda'],
                   isset($loanInputs['bank'])?$loanInputs['bank']:null,
                  ['class'=>'form-control loan-select','id'=>'bank']
      )
  !!}
  </div>
  </div>
</div>
</div>