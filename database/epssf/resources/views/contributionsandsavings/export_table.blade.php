 <table >
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.names') }}</th>
        <th> {{ trans('member.institution') }}</th>
        <th> {{ trans('member.employee_id') }}</th>
        <th> {{ trans('member.monthly_fee') }}</th>
  	 	</tr>
   	 </thead>
 <tbody>
 @foreach ($members as $member)
   {{-- populate the table --}}
   <tr>
  <td>{!! $member['adhersion_id']!!}</td>
  <td>{!! $member['first_name'] !!} {!! $member['last_name'] !!}</td>
  <td>{!! $member['institution']?$member['institution']:null !!}</td> 
  <td>{!! $member['employee_id'] !!}</td>
  <td>{!! $member['monthly_fee']!!}</td>

</tr>

 @endforeach
 </tbody>
</table>