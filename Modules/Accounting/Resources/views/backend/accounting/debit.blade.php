<div class=" debit-accounts">

    <div class="row">
        <label class="col-10">{{ trans('loan.dedit_account') }} </label>

        <div class="btn btn-success col-1"  id="add-debit-account">
            <i class="fas fa-plus"></i>
         </div>
    </div>

    <div class="row">
        <div class="col-6">Account</div>
        <div class="col-4">Amount</div>
    </div>

    @if (!isset($defaultAccounts['debits']))
        <?php $defaultAccounts['debits'] = []; ?>
    @endif
    <?php $count = 0; ?>

    @forelse ($defaultAccounts['debits'] as $id=>$account)
        <div class="form-group">

            <div id="debit-accounts-container">
                <div class="form-group row">
                    <div class="col-6">
                        {!! Form::select('debit_accounts[]', $accounts, $id, ['class' => 'form-control account']) !!}
                    </div>
                    <div class="col-4">
                        <input class="form-control debit-amount" id="debit_amount_{!! $count++ !!}"
                            name="debit_amounts[]" type="numeric" value="{{ isset($amount) ? $amount : 0 }}">
                    </div>
                    <div class="col-2">
                        <div class='btn btn-danger'><i class='fa fa-times'></i></div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="form-group">
            <div id="debit-accounts-container">
                <div class="form-group row">
                    <div class="col-6">

                        {!! Form::select('debit_accounts[]', $accounts, isset($accountId) ? $accountId : null, ['class' => 'form-control account']) !!}

                    </div>
                    <div class="col-4">
                        <input class="form-control debit-amount" id="debit_amount_{!! $count++ !!}"
                            name="debit_amounts[0]" type="numeric" value="{{ isset($amount) ? $amount : 0 }}">
                    </div>
                    <div class="col-2">
                        <div class='btn btn-danger'><i class='fa fa-times'></i></div>
                    </div>
                </div>
            </div>
        </div>
    @endforelse

</div>
