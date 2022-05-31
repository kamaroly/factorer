 <table >
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.monthly_fee') }}</th>
        <th> {{ trans('member.error') }}</th>
  	 	</tr>
   	 </thead>
 <tbody>

 @foreach ($members as $member)
   {{-- populate the table --}}
   <tr>
  <td>{!! $member[0]!!}</td>
  <td>{!! $member[1]!!}</td>
  <td>{!! $member[2]!!}</td>
</tr>

 @endforeach
 </tbody>
</table>