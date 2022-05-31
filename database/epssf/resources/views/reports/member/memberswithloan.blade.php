{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption class="text-center text-2xl font-semibold text-gray-800"> 
		{{ trans('reports.montly_refunds_per_institution',['institution'=>$institution]) }} 
</caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
	     	<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.service') }}</th>
			<th>{{ trans('member.monthly_fees') }}</th>			
			<th>{{ trans('member.emergency_fees') }}</th>				
			<th>{{ trans('member.total') }}</th>
	  	</tr>
   	 </thead>
 <tbody>
   @each ('reports.member.item_memberwithloan', $members, 'member', 'members.no-items')
 </tbody>
</table>
<table class="pure-table pure-table-bordered">
  	 	<tr>
	  	 	<td>
	  	 		<strong>{{ trans('report.done_by') }}</strong>
				<br/>
	  	 		<?php $user = Sentry::getUser(); ?>
				{!!  $user->first_name !!} {!! $user->last_name !!}
	  	 	</td>
	     	<td><strong>{{ trans('report.gerant') }}</strong>
				<br/>
				{!! (new Ceb\Models\Setting)->get('general.gerant') !!}
			</td>
	  	</tr>

	  	<tr style="height: 50px;">
	  		<td><strong>Signature:</strong></td>
	  		<td><strong>Signature:</strong></td>
	  	</tr>
 </table>