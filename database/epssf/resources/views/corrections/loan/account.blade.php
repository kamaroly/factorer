<div class="form-group" >
      <div id="debit-accounts-container">
        <div class="form-group account-row" >
          <div class="col-xs-6">
            {!! Form::select($name.'_accounts[]', $accounts,$posting->account_id, ['class'=>'form-control account']) !!}  
        </div>
        <div class="col-xs-4">
          <input class="{{ $name }}-amount form-control" name="{{ $name }}_amounts[]" type="numeric"
           value="{{ $posting->amount }}">
        </div>
      </div>
    </div>
</div>