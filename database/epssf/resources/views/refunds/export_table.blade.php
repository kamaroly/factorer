 <table >
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.names') }}</th>
        <th> {{ trans('member.institution') }}</th>
        <th> {{ trans('member.employee_id') }}</th>
        <th> {{ trans('member.refund_fee') }}</th>
        <th> {{ trans('member.expected_loan_refund') }}</th>
  	 	</tr>
   	 </thead>
 <tbody>

 @foreach ($members as $member)
   {{-- populate the table --}}
  <tr>
    <td>{!! $member->adhersion_id!!}</td>
    <td>{!! $member->first_name !!} {!! $member->last_name !!}</td>
    <td>{!! $member->institution_name !!}</td> 
    <td>{!! $member->employee_id !!}</td>
    <td>{!! $member->refund_fee!!}</td>
    <td>{!! $member->loanMonthlyRefundFee!!}</td>
  </tr>
 @endforeach
 </tbody>
</table>