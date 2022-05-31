{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption> {{ trans('reports.emergencyloanyear') }} </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
	     	<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.operation_type') }}</th>
			<th>{{ trans('member.created') }}</th>
			<th>{{ trans('member.loan') }}</th>
	  	</tr>
   	 </thead>
 <tbody>
   @each ('reports.member.item_ordinaryloan', $members, 'member', 'members.no-items')
   <tr>
	  	 	<th colspan="5">{{ trans('member.total') }}</th>
			<th>{!! number_format(abs($members->sum('loan'))) !!}</th>
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