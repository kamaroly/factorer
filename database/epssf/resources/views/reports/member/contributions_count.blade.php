@include('partials.landscapecss')

{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption class="text-2xl text-gray-700 font-semibold"> {{ trans('reports.contribution_count_this_year') }} </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
	     	<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.service') }}</th>
			<!--<th>{{ trans('member.savings') }}</th>-->
			<th>{{ trans('member.status') }}</th>
			<th>{{ trans('member.contribution_count') }}</th>
	  	</tr>
   	 </thead>
 <tbody>
	@foreach ($members as $member)
		<tr>
		 	<td>{{ $member->adhersion_id }}</td>
		 	<td>{{ $member->first_name }} {{ $member->last_name }}</td>
		 	<td>{{ $member->institution_name }}</td>
			<td>{{ $member->service }}</td>
			<!--<td>{{ number_format($member->savings) }}</td>-->
			<td>{{ trans('member.'.$member->status) }}</td>
			<td>{{ $member->current_year_contributions_count }}</td>
		</tr>
	@endforeach
	
 </tbody>
</table>

