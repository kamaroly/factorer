<div class="row header-container">
<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
 <label>{{ trans('contribution.institutions') }}</label>
  {!! Form::select('institutions', $institutions, $institution, ['class'=>'form-control','id'=>'institutions']) !!}
</div>
<div class="col-xs-1 col-sm-2 col-md-1 col-lg-2" >
<label>{{ trans('contribution.month') }}</label>

	{!! Form::selectMonth('month',$month,['class'=>'form-control','id'=>'month']) !!}
</div>

<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
<label>{{ trans('refund.refund_type') }}</label>
	{!! Form::select('refund_type', $refundTypes, $refundType, ['class'=>'form-control','id'=>'refundType']) !!}
</div>
<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
<label>{{ trans('contribution.totalAmount') }}</label>

	{!! Form::input('text', 'totalAmount', number_format($totalRefunds), ['class'=>'form-control contribution-total','disabled']) !!}
</div>
<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
<label>{{ trans('contribution.debit_account') }}</label>

{!! Form::select('debit_account', $accounts,$debitAccount, ['class'=>'form-control','id'=>'debit_account'])!!}
</div>
<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
<label>{{ trans('contribution.credit_account') }}</label>
{!! Form::select('credit_account', $accounts,$creditAccount, ['class'=>'form-control','id'=>'credit_account'])!!}
</div>
</div>