<div class="row debit-accounts">
  <label class="col-md-11">{{ trans('general.accounts_to_reconcile') }} </label>
  <div class="btn btn-success col-md-1 pull-right" id="add-debit-account">+</div>
</div>

<div class="row">
  <div class="form-group" >
        <div class="col-md-7">
          <label>{{ trans('accounting.accounting_debit_account') }}</label>
        </div>
        <div class="col-md-3">
          <label>
              @if (request()->is('reconciliations/account/bank*'))
                  {{ trans('accounting.amount_in_bank') }}
              @else
                  {{ trans('general.expected_amount') }}
              @endif
           
          </label>
        </div>
         <div class="col-md-2">
         </div>
    </div>
  </div>

  <div class="row">
       <div class="form-group">
          <div id="debit-accounts-container">
            <div class="form-group account-row" >
              <div class="col-md-7">
                {!! Form::select('accounts[]', $accounts,null, ['class'=>'form-control account'])!!}
              </div>
              <div class="col-md-3">
                <input class="form-control amount" id="amounts" name="amounts[]" type="numeric" value="0">
              </div>
              <div class="col-md-2">
                <div class='btn btn-danger'><i class='fa fa-times'></i></div> 
              </div>
            </div>
          </div>       
      </div>    
      </div>