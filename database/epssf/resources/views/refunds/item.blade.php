{!! Form::open(array('route' => array('refunds.update', $member->id), 'method' => 'PUT')) !!}
<tr>
	<td>{!! Form::checkbox('memberIds[]', $member->id, true) !!}</td>
	<td>{!! $member->adhersion_id !!}</td>
	<td>
		{{-- <img class="direct-chat-img" src="{{route('files.get', $member->photo)}}" align="left"> --}}
		<strong>{!! $member->first_name !!} {!! $member->last_name !!}</strong> <br/>
		<small><strong>{{trans('member.nid')}}:</strong></small><small>{!! $member->member_nid !!}</small>
	 </td>
	<td>{!! $member->institution?$member->institution->name:null !!}</td>
	<td>{!! $member->employee_id !!}</td>
	<td>{!! Form::text('monthly_fee',  $member->refund_fee , ['class'=>'form-control','size'=>'2'])!!}</td>
	<td>
		{!! Form::hidden('adhersion_number', $member['adhersion_id']) !!}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<button  class="btn btn-primary">
			<i class="fa fa-edit"></i>
		</button>
	<a  class="btn btn-danger" href="{!! route('refunds.remove.member',$member['adhersion_id']) !!}"
		 onclick="return confirm('{{ trans('general.are_you_sure_you_want_to_remove_this_item') }}');">
			<i class="fa fa-times"></i>
		</a>
	</td>
</tr>
{!! Form::close() !!}
