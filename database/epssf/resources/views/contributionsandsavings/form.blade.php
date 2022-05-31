<div class="row header-container">
<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >

 <label>{{ trans('contribution.institutions') }}</label>
  {!! Form::select('institutions', $institutions, $institutionId, ['class'=>'form-control','id'=>'institutions']) !!}
</div>

{{-- If we are inclosing display Recurrence --}}
@if (request()->is('closing'))
	<div class="col-xs-1 col-sm-2 col-md-1 col-lg-2" >
		<label>{{ trans('contribution.recurrence') }}</label>
    	<select class="form-control" id="recurrence" name="recurrence">
		    <option value="monthly">{{  trans('general.Monthly') }}</option>
		    <option value="yearly">{{ trans('general.Yearly') }}</option>
		</select>
	</div>
@endif

<div class="col-xs-1 col-sm-2 col-md-1 col-lg-2" >
	@if ($contributionType == 'ANNUAL_INTEREST' || $contributionType == 'BULK_WITHDRAW')
		<label>{{ trans('contribution.year_of_interest') }}</label>
		{!! Form::select('interest_year', get_years(), $interestYear, ['class' => 'form-control','id'=>'interest-year']) !!}
	@else
		<label>{{ trans('contribution.month') }}</label>
		{!! Form::selectMonth('month',$month,['class'=>'form-control','id'=>'month']) !!}	
	@endif
</div>

<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" >
<label>{{ trans('contribution.totalAmount') }}</label>
	{!! Form::input('text', 'totalAmount', number_format($total), ['class'=>'form-control contribution-total','disabled']) !!}
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