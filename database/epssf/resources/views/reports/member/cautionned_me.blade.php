{{-- Start by pulling this member profile --}}
@if (!is_null($member))
	@include('reports.member.partials.profile',['member'=>$member])
@endif
<table class="pure-table pure-table-bordered">
<caption> {!! $title !!} </caption>
  	 <thead>
  	 	<tr>
	  	 	<th>{{ trans('member.adhersion_id') }}</th>
	     	<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.service') }}</th>
			<th>{{ trans('member.total_contribution') }}</th>
			<th>{{ trans('member.amount') }}</th>
			<th>{{ trans('member.paid_back_amount') }}</th>
			<th>{{ trans('member.balance') }}</th>
			<th>{{ trans('member.transactionid') }}</th>
			<th>{{ trans('member.letter_date') }}</th>
			<th>{{ trans('member.status') }}</th>	
	  	</tr>
   	 </thead>
 <tbody>
 @forelse ( $cautions as $caution)
 	@include('reports.member.item_cautionned_me',['caution'=>$caution,'type'=>$type])
 @empty
 	  @include('members.no-items')
 @endforelse
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