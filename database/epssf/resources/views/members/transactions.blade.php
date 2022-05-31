@extends('layouts.default')

@section('content_title')
  {{ trans('member.transactions_and_other_withdrawals_on_savings') }}
@endsection
@section('content')
@if (!is_null($transactionid) && $transactionid !=false)
  <script type="text/javascript">
    OpenInNewTab("{!! route('piece.disbursed.saving',['transactionid'=>$transactionid]) !!}")
  </script>
@endif
            <!-- CREDIT CARD FORM STARTS HERE -->
  <div class="panel panel-default credit-card-box">
 
      <div class="panel-heading display-table" >
          <div class="row display-tr" style="text-align: center;" >
                      <div class="col-md-1">
                        <div style="width:70px;border:2px solid rgba(0,0,0,0.8);">
                          @include('files.show',['filename'=>$member->photo])
                        </div>
                      </div>
                        <div class="col-md-3">
                            <div class="form-group">
                             <label>{{ trans('member.adhersion_number') }}</label>
                             <br/>
                              {!! $member->adhersion_id !!}
                            </div>
                        </div>
                          <div class="col-md-3">
                            <div class="form-group">
                             <label>{{ trans('member.names') }}</label>
                             <br/>
                              {!! $member->names !!}
                            </div>
                        </div>
                      <div class="col-md-3">
                        <div class="form-group">
                         <label>{{ trans('member.institution') }}</label>
                         <br/>
                          {!! $member->institution_name !!}
                        </div>
                        </div>
                  </div>
              </div>       
      </div>
<div class="panel-body">
<div class="notifications"></div>
{!! Form::open(['method'=>'POST','url'=>  route('members.completetransaction',['memberId'=>$member->id])]) !!}
    <div class="row">
        <div class="col-xs-4 col-md-4">
            <div class="form-group">
                <label for="movement_type">
                   {{ trans('member.movement_type') }}
                 </label>
                    {!! Form::select('movement_type',$transactionTypes , $movement_type, ['class'=>'form-control movement_type']) !!}
            </div>
        </div>
       <div class="col-xs-4 col-md-4">
            <div class="form-group">
                <label for="operation_type">
                   {{ trans('member.operation_type') }}
                 </label>
                    {!! Form::select('operation_type', $memberTransactions[$movement_type], $operation_type, ['class'=>'form-control operation_type','id'=>'operation_type']) !!}
            </div>
        </div>
       <div class="col-xs-4 col-md-4">
            <div class="form-group">
                <label for="amount">
                   {{ trans('member.amount') }}
                 </label>
                    {!! Form::input('number', 'amount', 0, ['class'=>'form-control amount']) !!}
            </div>
        </div>
        @if ($movement_type == 'withdrawal')
          <div class="col-xs-4 col-md-4">
            <div class="form-group">
             <label>{{ trans('member.charges') }}</label>
            <?php $administration_fees=0;  ?>
            @if ((new Ceb\Models\Setting)->hasKey('loan.administration.fee'))
            <?php $administration_fees =  \Ceb\Models\Setting::keyValue('loan.administration.fee'); ?>
            @endif
           
            <?php $urgent_fees=0;  ?>
            @if ((new Ceb\Models\Setting)->hasKey('urgent.administration.fee'))
            <?php $urgent_fees =  \Ceb\Models\Setting::keyValue('urgent.administration.fee'); ?>
            @endif
              {!! Form::select('charges',

                                [0     => trans('general.select'),
                                 $urgent_fees => trans('general.urgent_administration_fee',
                                ['charges'=>$urgent_fees]),
                                $administration_fees=>trans('loan.charging_administration_fees',
                                ['charges'=>$administration_fees]),],null,['class' => 'form-control','id'=>'transaction-charges']) !!}                        

         </div>
         </div>
         {{-- Show charges amount --}}
        <div class="col-xs-2 col-md-2 charges-section" style="display: none">
            <div class="form-group">
                <label for="charges_amounts">
                 {{ trans('member.charges_amounts') }}
                 </label>
                 {!! Form::input('text', 'charges-amount', null, ['class'=>'form-control charges-amount','placeholder'=>trans('general.charges-amount')]) !!}
            </div>
        </div>
       @endif
        <div class="col-xs-4 col-md-4 cheque-section">
            <div class="form-group">
                <label for="cheque_number">
                   {{ trans('member.cheque_number') }}
                 </label>
                    {!! Form::input('text', 'cheque_number', null, ['class'=>'form-control','placeholder'=>trans('general.cheque_number')]) !!}
            </div>
        </div>
       <div class="col-xs-4 col-md-4">
            <div class="form-group">
                <label for="bank">
                   {{ trans('member.bank') }}
                 </label>
                    {!! Form::select('bank', $banks, null, ['class'=>'form-control']) !!}
            </div>
        </div>
       
        <div class="col-xs-12 col-md-12" id="contract_number">
            <div class="form-group">
                <label for="contract_number">
                 {{ trans('loan.contract_number') }}
                 </label>
                   {!! Form::input('text', 'contract_number', null, ['class'=>'form-control','placeholder'=>trans('general.contract_number')]) !!}
            </div>
        </div>
   
        <div class="{!! $movement_type == 'withdrawal' ? 'col-xs-12 col-md-12 ' : 'col-xs-4 col-md-4'!!}">
            <div class="form-group">
                <label for="wording">
                 {{ trans('member.wording') }}
                 </label>
                   {!! Form::input('text', 'wording', null, ['class'=>'form-control','placeholder'=>trans('general.write_your_wording')]) !!}
            </div>
        </div>
    </div>
   
     @include('accounting.form')
    
        <div class="col-xs-12">
            <button class="btn btn-success btn-lg btn-block submit-member-transaction-button" type="submit">{{ trans('general.save') }}</button>
        </div>
    </div>
                        <div class="row" style="display:none;">
                            <div class="col-xs-12">
                                <p class="payment-errors"></p>
                            </div>
                        </div>
                    </form>
                </div>
            <!-- CREDIT CARD FORM ENDS HERE -->
@endsection