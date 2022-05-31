{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption> {{ trans('report.new_member_title') }} </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
	     	<th>{{ trans('member.names') }}</th>
	     	<th>{{ trans('member.adhere') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.service') }}</th>
			<th>{{ trans('member.status') }}</th>
			<th>{{ trans('member.savings') }}</th>
	  	</tr>
   	 </thead>
 <tbody>
   @each ('reports.member.item_newmember', $members, 'member', 'members.no-items')
   <tr>
	  	 	<th colspan="6">{{ trans('member.service') }}</th>
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