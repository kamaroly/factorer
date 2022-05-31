<div class="box-body">
<div class="box-header with-border">
  <h3 class="box-title">{{ ucfirst(trans('loans.'.$loanInputs['operation_type'])) }} </h3>
  <div class="loan-notifications">
   </div>
</div>
<div class="row">
  @if (isset($loanInputs['tranches_number']) && strpos($loanInputs['tranches_number'],'ordinary_loan'))
    {{-- Show contract number when this one is not an ordinary loan --}}
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.loan_contract_number') }}</label>
  {!! Form::input('text', 'loan_contract',isset($loanInputs['loan_contract'])?$loanInputs['loan_contract']:null,
                  ['class'=>'form-control loan-input'])
    !!}
  </div>
  </div>
 @endif

  @if (strpos($loanInputs['operation_type'],'ordinary_loan') !== false || strpos($loanInputs['operation_type'],'emergency_loan') !== false)
  <div class="col-md-3">
  <div class="form-group">

  <?php $maximumInstallments = $setting->keyValue('loan.maximum.installments'); ?>
  {{-- If we are dealing with emergency loan, Maximum installments needs to be less or equal to 3 --}}
  @if (strpos($loanInputs['operation_type'],'emergency_loan') !== false)
    <?php $maximumInstallments = $setting->keyValue('loan.emergency.installments');?>
  @endif
  
   <label>{{ trans('loan.number_of_installments') }}</label>
  {!! Form::selectRange('tranches_number', 1, $maximumInstallments ,
                          isset($loanInputs['tranches_number'])?$loanInputs['tranches_number']:null,
                         ['class'=>'form-control loan-select','id'=>'numberOfInstallment'])
    !!}
  </div>
  </div>
  @else
   {!! Form::hidden('tranches_number', $member->remaining_tranches,['class'=>'form-control loan-select','id'=>'numberOfInstallment']) !!}
  @endif
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.wished_amount') }}</label>
    {!! Form::input('text', 'wished_amount',isset($loanInputs['wished_amount'])?$loanInputs['wished_amount']:null,
                  ['class'=>'form-control loan-input','id'=>'wished_amount'])
    !!}
  </div>
  </div>
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.operation_type') }}</label>
   {{-- remove ordinary loan if this member has active loans --}} 
   <?php
    // Does this member has a loan ?
    // if yes inspect the loan details
    $latestLoan = $member->latestLoanWithEmergency(); 
    
    // if the latest taken loan is emergency then treat it in a special way
    $latestLoan = !is_null($latestLoan)?$latestLoan->is_umergency:0;
   ?>
   @if ($member->has_active_loan && $latestLoan!=1)     
     <?php unset($loanTypes['ordinary_loan']); ?>
     <?php unset($loanTypes['urgent_ordinary_loan']); ?>
   @endif
   <!-- If the customer has active emergency loan, remove it also -->
   @if ($member->has_active_emergency_loan)
     <?php unset($loanTypes['emergency_loan']); ?>
   @endif
   
   {!! Form::select('operation_type',
                   $loanTypes,
                   isset($loanInputs['operation_type'])?$loanInputs['operation_type']:null,
                  ['class'=>'form-control loan-select','id'=>'operation_type'])
  !!}
   </div>
  </div>
  @if (strtolower($loanInputs['operation_type']) == 'urgent_ordinary_loan')
  <?php $administration_fees =  \Ceb\Models\Setting::keyValue('loan.administration.fee'); ?>
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.administration_fees') }}</label>
     {!! Form::select('administration_fees',
                       [0 => trans('general.select'),
                         $administration_fees=>trans('loan.charging_administration_fees',['charges'=>$administration_fees]),
                        ],null,['class' => 'form-control','id'=>'administration_fees']) 
    !!} 
  </div>
  </div>
  @endif
  
  {{-- If this loan is social loan make sure we ask the user to provider the reason  --}}
  @if (strtolower($loanInputs['operation_type']) == 'social_loan')
  <div class="col-md-3">
  <div class="form-group">
     <label> {{ trans('loan.social_loan_motives') }} </label>
     {!! Form::select('reason', $socialLoanReasons, null, ['class' => 'form-control']) !!} 
  </div>
  </div>
  @endif
{{-- </div>

<div class="row"> --}}
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.loan_to_repay') }}</label>
  {!! Form::input('text', 'loan_to_repay',isset($loanInputs['loan_to_repay'])?$loanInputs['loan_to_repay']:null,
                  ['class'=>'form-control loan-input','id'=>'loanToRepay'])
    !!}
  </div>
  </div>
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.monthly_installments') }}</label>
  {!! Form::input('text', 'monthly_fees',isset($loanInputs['monthly_fees'])?$loanInputs['monthly_fees']:null,
                  ['class'=>'form-control loan-input','id'=>'monthlyInstallments'])
    !!}
  </div>
  </div>
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.interests') }}</label>
  {!! Form::input('text', 'interests',isset($loanInputs['interests'])?$loanInputs['interests']:null,
                  ['class'=>'form-control loan-input','id'=>'interests'])
    !!}
  </div>
  </div>
  @if (strtolower($loanInputs['operation_type']) == 'urgent_ordinary_loan')
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.interest_on_urgently_loan ') }}</label>
  {!! Form::input('text', 'interest_on_urgently_loan',isset($loanInputs['interest_on_urgently_loan'])?$loanInputs['interest_on_urgently_loan']:null,
                  ['class'=>'form-control loan-input','id'=>'interest_on_urgently_loan','disabled'=>true])
    !!}
  </div>
  </div>
    @endif
{{-- </div>

<div class="row"> --}}
  <div class="col-md-3">
  <div class="form-group">
   <label>{{ trans('loan.net_to_receive') }}</label>
  {!! Form::input('text', 'amount_received',isset($loanInputs['net_to_receive'])?$loanInputs['net_to_receive']:null,
                  ['class'=>'form-control green-input dloan-input','id'=>'netToReceive'])
    !!}
  </div>
  </div>
</div>
</div>