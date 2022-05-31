<div class="col-xs-12 col-md-12">
    <div class="form-group">
    <label for="bank_name">
         {{ trans('bank.bankname') }}
         </label>
           {!! Form::input('text', 'bank_name', $bank->bank_name, ['class'=>'form-control']) !!}
    </div>
</div>
