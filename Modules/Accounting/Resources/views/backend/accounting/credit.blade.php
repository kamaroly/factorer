<div class="row credit-accounts">
<label>{{ trans('loan.credit_account') }} </label>
      <div class="btn btn-success pull-right" style="margin-right: 5%;" id="add-credit-account">+</div>
      <div class="form-group" >
          <div class="col-xs-6">
          <label>{{ trans('accounting.creditt_account') }}</label>
          </div>
          <div class="col-xs-4">
          <label>{{ trans('accounting.amount') }}</label>
          </div>
           <div class="col-xs-2">
           </div>
      </div>
      @if (!isset($defaultAccounts['credits']))   
        <?php $defaultAccounts['credits']=[]; ?>
      @endif
      <?php $count = 0; ?>
      @forelse ($defaultAccounts['credits'] as $id=>$account)        
        <div class="form-group" >
          <div id="credit-accounts-container">
            <div class="form-group account-row" >
              <div class="col-xs-6">
                {!! Form::select('credit_accounts[]', $accounts,$id, ['class'=>'form-control account'])!!}
              </div>
              <div class="col-xs-4">
                <input class="form-control credit-amount" id="credit_amounts_{!! $count++ !!}" name="credit_amounts[]" type="numeric" value="{{isset($amount)?$amount:0}}">
              </div>
              <div class="col-xs-2">
                <div class='btn btn-danger'><i class='fa fa-times'></i></div> 
              </div>
            </div>
          </div>       
      </div>

      @empty

          <div class="form-group" >
          <div id="credit-accounts-container">
            <div class="form-group account-row" >
              <div class="col-xs-6">
                {!! Form::select('credit_accounts[]', $accounts,isset($accountId)?$accountId :null, ['class'=>'form-control account'])!!}
              </div>
              <div class="col-xs-4">
                <input class="form-control credit-amount" id="credit_amounts_{!! $count++ !!}" name="credit_amounts[0]" type="numeric" value="{{isset($amount)?$amount:0}}">
              </div>
              <div class="col-xs-2">
                <div class='btn btn-danger'><i class='fa fa-times'></i></div> 
              </div>
            </div>
          </div>       
      </div>
      @endforelse
 
</div>