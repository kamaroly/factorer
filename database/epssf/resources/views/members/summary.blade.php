<table  class="table-auto">
    <caption class="text-blue-700 text-center text-3xl text-teal-500 font-light leading-tight">Summary</caption>
        {{-- EMERGENCY NUMBERS --}}
    <tr class="bg-gray-900 text-gray-100 font-light leading-tight">
        <th class="px-4 py-2">{{ trans('member.total_contribution') }}</th>
        <td class="border px-4 py-2">
            {!! moneyFormat($member->totalContributions(), 0, "", ",") !!}RWF
        </td>
    </tr>
    <tr class="bg-gray-700 text-gray-100 font-light leading-tight text-2xl">
        <th class="px-4 py-2">
            {{ trans('member.loan_as_of_today') }}
        </th>
        <td class="border px-4 py-2">
            {!! moneyFormat( $member->loan_balance, 0, null, ",") !!}RWF
        </td>
    </tr>
    <tr class="bg-gray-900 text-gray-100 font-light leading-tight">
        <th class="px-4 py-2">
            {{ trans('member.active_loan_monthly_fees_payments') }}
        </th>
        <td class="border px-4 py-2">
            {!! moneyFormat($member->loan_monthly_fee, 0, null, ",") !!}RWF
        </td>
    </tr>
    <tr class="bg-gray-700 text-gray-100 font-light leading-tight text-2xl">
        <th class="px-4 py-2">
            {{ trans('member.remaining_installments') }}
        </th>
        <td class="border px-4 py-2">
            {!! $member->remaining_tranches !!}
        </td>
    </tr>
    <tr class="bg-gray-900 text-gray-100 font-light leading-tight">
        <th class="px-4 py-2">
            {{ trans('member.total_cautions_amount') }}
        </th>
        <td class="border px-4 py-2">
            {!! moneyFormat($member->caution_balance) !!}
        </td>
    </tr>       
</table>