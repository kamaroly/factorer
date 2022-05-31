<div class="col-lg-9">
 <div class="text-center text-4xl text-gray-900 font-light leading-tight">{!! ucfirst(trans('loans.emergency_loan_details')) !!}</div>
<table class="table-auto w-full border-none">
  <thead>
    <tr>
      <th class="px-4 py-2 text-gray-800 text-2xl text-center">{{ trans('loans.emergency_loan_balance') }}</th>
      <th class="px-4 py-2 text-gray-800 text-2xl text-center">{{ trans('loans.emergency_loan_monthly_fees') }}</th>
      <th class="px-4 py-2 text-gray-800 text-2xl text-center">{{ trans('loans.emergency_loan_refund') }}</th>
      <th class="px-4 py-2 text-gray-800 text-2xl text-center"> {{ trans('loans.emergency_loan_remaining_tranches') }}</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="bg-gray-500 px-4 py-2 text-4xl text-center text-gray-900 font-light leading-tight">
            {!! number_format($balance = $emergencyLoan->loan_to_repay - $emergencyLoan->refund_amount) !!} RWF
      </td>
      <td class="bg-blue-400 px-4 py-2 text-4xl text-center text-blue-900 font-light leading-tight">
            {!! number_format($monthly_fees = $emergencyLoan->monthly_fees) !!} RWF
      </td>
      <td class="bg-green-700 px-4 py-2 text-4xl text-center text-green-100 font-light leading-tight">
            {!! number_format($emergencyLoan->refund_amount) !!} RWF
      </td>
      <td class="bg-yellow-500 px-4 py-2 text-4xl text-center text-yellow-900 font-light leading-tight">
            {!! ($balance/$emergencyLoan->monthly_fees)  !!}
      </td>
    </tr>
  </tbody>
</table>