<div class="col-xs-12 col-md-12">
    <div class="form-group">
    <label for="entitled">
         {{ trans('account.account_number') }}
         </label>
           {!! Form::input('text', 'account_number', $account->account_number, ['class'=>'form-control']) !!}
    </div>
</div>
<div class="col-xs-12 col-md-12">
    <div class="form-group">
    <label for="entitled">
      	 {{ trans('account.entitled') }}
         </label>
       	 	 {!! Form::input('text', 'entitled', $account->entitled, ['class'=>'form-control']) !!}
    </div>
</div>
<div class="col-xs-12 col-md-12">
    <div class="form-group">
        <label for="accounting_nature">
      	 {{ trans('account.accounting_nature') }}
         </label>
       	 {!! Form::select('account_nature', $accountNatures, null, ['class'=>'form-control']) !!}
    </div>
</div>