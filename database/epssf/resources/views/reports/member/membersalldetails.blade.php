{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption><h3><u> {{ trans('report.member_with_all_details') }}</u></h3> </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
	     	<th>{{ trans('member.names') }}</th>
	     	<th>{{ trans('member.member_nid') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.telephone') }}</th>
            <th>{{ trans('member.attorney') }}</th>
            <th>{{ trans('member.bank') }}</th>
            <th>{{ trans('member.bank_account') }}</th>
			<th>{{ trans('member.savings') }}</th>
			<th>{{ trans('member.status') }}</th>
			<th>{{ trans('general.this_year_contribution_amount') }}</th>
			<th>{{ trans('general.this_year_contribution_count') }}</th>
	  	</tr>
   	 </thead>
 <tbody>
	
	@foreach ($members as $member)
		<tr>
		 	<td>{{$member->adhersion_id }}</td>
		 	<td>{{ $member->first_name }} {{ $member->last_name }}</td>
		 	<td>{{ $member->member_nid }}</td>
		 	<td>{{ $member->institution }}</td>
			<td>{{ $member->telephone }}</td>
			<td>{{ $member->attorney }}</td>
			<td>{{ $member->bank_name }}</td>
			<td>{{ $member->bank_account }}</td>
			<td>{{ number_format($member->savings) }}</td>
			<td>{{ trans('member.'.$member->status) }}</td>
			<td>{{ number_format($member->current_year_contribution_amount) }}</td>
			<td>{{ number_format($member->current_year_contribution_count) }}</td>
		</tr>
	@endforeach

   <tr>
	  	 	<th colspan="4">{{ trans('member.savings_total') }}</th>
			<th>{!! number_format(abs($members->sum('savings'))) !!}</th>
	  	</tr>
 </tbody>
</table>
<table class="pure-table pure-table-bordered">
  	 	<tr>
	  	 	<td>
	  	 		<strong>{{ trans('report.done_by') }}</strong> <br/>
	  	 		<?php $user = Sentry::getUser(); ?>
				{!!  $user->first_name !!} {!! $user->last_name !!}
	  	 	</td>
	     	<td><strong>{{ trans('report.gerant') }}</strong> <br/>
				{!! (new Ceb\Models\Setting)->get('general.gerant') !!}
			</td>
	  	</tr>

	  	<tr style="height: 50px;">
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  	</tr>
 </table>