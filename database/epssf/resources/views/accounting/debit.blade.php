<div class="row debit-accounts">
    <label>{{ trans('loan.debit_account') }}</label>
    <div class="btn btn-success pull-right" style="margin-right: 5%;" id="add-debit-account">+</div>
    <div class="form-group" >
        <div class="col-xs-6">
        <label>{{ trans('accounting.debit_account') }}</label>
        </div>
        <div class="col-xs-4">
        <label>{{ trans('accounting.amount') }}</label>
        </div>
         <div class="col-xs-2">
         </div>
    </div>
    @if (!isset($defaultAccounts['debits']))   
      <?php $defaultAccounts['debits']=[]; ?>
    @endif
 <?php $count = 0; ?>
    @forelse ($defaultAccounts['debits'] as $id=>$account)
    <div class="form-group" >
        <div id="debit-accounts-container">
          <div class="form-group account-row" >
            <div class="col-xs-6">

              {!! Form::select('debit_accounts[]', $accounts,$id, ['class'=>'form-control account'])!!}
            
            </div>
            <div class="col-xs-4">
              <input class="form-control debit-amount" id="debit_amount_{!! $count++ !!}" name="debit_amounts[]" type="numeric" value="{{isset($amount)?$amount:0}}">
            </div>
            <div class="col-xs-2">
              <div class='btn btn-danger'><i class='fa fa-times'></i></div> 
            </div>
          </div>
        </div>
    </div>
    @empty
        <div class="form-group" >
        <div id="debit-accounts-container">
          <div class="form-group account-row" >
            <div class="col-xs-6">

              {!! Form::select('debit_accounts[]', $accounts,isset($accountId)?$accountId :null, ['class'=>'form-control account'])!!}
            
            </div>
            <div class="col-xs-4">
              <input class="form-control debit-amount" id="debit_amount_{!! $count++ !!}" name="debit_amounts[0]" type="numeric" value="{{isset($amount)?$amount:0}}">
            </div>
            <div class="col-xs-2">
              <div class='btn btn-danger'><i class='fa fa-times'></i></div> 
            </div>
          </div>
        </div>
    </div>
    @endforelse

  </div>