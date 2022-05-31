{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption><h3> {{ trans('report.list_institutions') }} </h3></caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.id') }}</th>
	     	<th>{{ trans('member.nom') }}</th>
			<th>{{ trans('member.created_at') }}</th>
			
	  	</tr>
   	 </thead>
 <tbody>
   @each ('reports.member.item_institution', $members, 'member', 'members.no-items')
   
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