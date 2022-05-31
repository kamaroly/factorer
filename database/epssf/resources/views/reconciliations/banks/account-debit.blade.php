<div class="row debit-accounts">
  <label class="col-xs-10">{{ trans('loan.debit_account') }} </label>
  <div class="btn btn-success col-xs-2 pull-right" id="add-debit-account">+</div>
</div>

<div class="row">
  <div class="form-group" >
        <div class="col-xs-7">
          <label>{{ trans('accounting.accounting_debit_account') }}</label>
        </div>
        <div class="col-xs-3">
          <label>{{ trans('accounting.amount') }}</label>
        </div>
         <div class="col-xs-2">
         </div>
    </div>
  </div>

  <div class="row">
       <div class="form-group">
          <div id="debit-accounts-container">
            <div class="form-group account-row" >
              <div class="col-xs-7">
                {!! Form::select('debit_accounts[]', $accounts,null, ['class'=>'form-control account'])!!}
              </div>
              <div class="col-xs-3">
                <input class="form-control debit-amount" id="debit_amounts" name="debit_amounts[]" type="numeric" >
              </div>
              <div class="col-xs-2">
                <div class='btn btn-danger'><i class='fa fa-times'></i></div> 
              </div>
            </div>
          </div>       
      </div>    
      </div>