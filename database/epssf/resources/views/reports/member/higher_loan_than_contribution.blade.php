@include('partials.landscapecss')

{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption class="text-2xl text-gray-700 font-semibold"> {{ trans('reports.higher_contribution_than_contribution') }} </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
	     	<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.status') }}</th>
			<th>{{ trans('member.savings') }}</th>
			<th>{{ trans('member.loan_balance') }}</th>
			<th>{{ trans('member.solde_balance') }}</th>
	  	</tr>
   	 </thead>
 <tbody>
	@foreach ($members as $member)
		<tr>
		 	<td>{{ $member->adhersion_id }}</td>
		 	<td>{{ $member->first_name }} {{ $member->last_name }}</td>
		 	<td>{{ $member->institution }}</td>
		 	<td>{{ $member->statuss}}</td>
			<td>{{ number_format($member->savings) }}</td>
			<td>{{ number_format($member->balance) }}</td>
			<td>{{ number_format($member->solde) }}</td>
		</tr>
	@endforeach
	
 </tbody>
</table>

