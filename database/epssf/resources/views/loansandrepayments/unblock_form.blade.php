@extends('layouts.default')

@section('content_title')
  {!! $title !!}
@endsection

@section('content')
{!! Form::open(['route'=>'loan.unblock.store','method'=>'POST']) !!}

{!! Form::hidden('loanid', $loanid) !!}

<div class="row">
<div class="col-md-12">
  <table class="table table-bordered table-striped text-center">
    <caption> 

      <div class="w-full p-2 mx-1 my-1 bg-green-300">
        <span class="text-4xl font-semibold text-green-900"> {{ $member->first_name.' '.$member->last_name }} </span> 
        <span class="h-full text-4xl text-green-100"> | </span>
        <span class="text-4xl text-green-900"> {{ $member->adhersion_id }} </span> 
        <span class="h-full text-4xl text-green-100"> | </span>
        <span class="text-4xl text-green-900">
          <span class="font-light text-2xl"> Contributions: </span> 
          {{ number_format($member->total_contribution) }} RWF
        </span>
       </div>
     </caption>
  <thead>
    <th>{{ trans('member.institution') }}</th>
    <th>{{ trans('member.balance_of_loan') }} </th>
    <th>{{ trans('member.right_to_loan') }} </th>
    <th>{{ trans('loan.operation_type') }} </th>
    <th>{{ trans('loan.rate') }}</th>
    <th>{{ trans('loan.number_of_installments') }}</th>
    <th>{{ trans('loan.monthly_installments') }}</th>
    <th>{{ trans('loan.loan_to_repay') }}</th>
    <th>{{ trans('loan.interests') }}</th>
    <th>{{ trans('loan.administration_fees') }}</th>
    <th>{{ trans('loan.net_to_receive') }}</th>
  
  </thead>

  <tbody>
    <tr>
      <td>{{ $member->institution->name }} </td>
      <td>{{ number_format($member->Loan_balance) }} </td>
      <td>{{ number_format($loan->right_to_loan) }} </td>
      <td>{{ trans('loans.'.$loan->operation_type) }} </td>
      <td>{{ $loan->rate }}</td>
      <td>{{ number_format($loan->tranches_number) }}</td>
      <td>{{ number_format($loan->monthly_fees) }} </td>
      <td class="bg-yellow-200">{{ number_format($loan->loan_to_repay) }}</td>
      <td>{{ number_format($loan->interests) }}</td>
      <td>{{ number_format($loan->urgent_loan_interest) }}</td>
      <td>{{ number_format($loan->amount_received) }}</td>
    </tr>
  </tbody>
</table>  
</div>
    <div class="col-md-12">
  <div class="form-group">
   <label>{{ trans('loan.number_of_cheque') }}</label>
  
{!! $errors->first('cheque_number','<label class="has-error">:message</label>') !!} 
  {!! Form::input('text', 'cheque_number',null,['class'=>'form-control','id'=>'cheque_number'])
    !!}
  </div>
  </div>
  <div class="col-md-12">
  <div class="form-group">
   <label>{{ trans('loan.bank') }}</label>
    {!! Form::select('bank_id',$banks,null,['class'=>'form-control loan-select','id'=>'bank']
      )
  !!}
  </div>
  </div>

{{-- Cautionneur are being filled on the loan form --}}
  @if ($show_caution_form == true)
  {{--     
    <div class="col-md-12">
      @include('loansandrepayments.caution_form')
    </div>
   --}}
  @endif
  <div class="col-md-12">
    <button class="btn btn-success col-md-12"><i class="fa fa-unlock-alt"></i> {{ trans('loan.unblock') }}</button>
  </div>
</div>

{!! Form::close() !!}

@endsection

@section('scripts')

<script type="text/javascript">
   $('#js-search-cautionneur').click(function(event) {
      // Prevent the default event action 
      event.preventDefault();

      var cautionneur = $(this).parent().find('input');
      console.log(cautionneur);
      
      // Check if this input has at least some data
      if(cautionneur.val() === ""){
          alert('Please enter adhersion_id for guarantor before continuing');
      }
      // If we reach here it means we have all we need to continue
      window.location.href = window.location.protocol+'//'+window.location.host+'/loans/setcautionneur'+'?'+cautionneur.attr('name')+'='+cautionneur.val();
      });
</script>
@endsection