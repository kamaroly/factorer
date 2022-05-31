 <table >
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number_of_member_without_emergency') }}</th>
        <th> {{ trans('member.uploaded_fee') }}</th>
  	 	</tr>
   	 </thead>
 <tbody>

 @foreach ($members as $member)
   {{-- populate the table --}}
   <tr>
  <td>{!! $member[0]!!}</td>
  <td>{!! $member[1]!!}</td>
</tr>

 @endforeach
 </tbody>
</table>