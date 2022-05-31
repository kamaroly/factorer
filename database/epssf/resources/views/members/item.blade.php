<tr>
	<td>{!! $member->adhersion_id!!}</td>
	<td>
		<img class="direct-chat-img" src="{{route('files.get', $member->photo)}}" align="left">
		<strong>{!! $member->first_name !!} {!! $member->last_name !!}</strong> <br/>
		<small><strong>{{trans('member.nid')}}:</strong></small><small>{!! $member->member_nid !!}</small>
	</td>
	<td>{!! $member->institution?$member->institution->name:null !!}</td>
	<td>{{ trans('member.'.$member->status) }} </td>
	<td>{!! $member->district !!}</td>
	<td>{!! $member->created_at->format('Y-m-d') !!}</td>
	<td>
{!! Form::open(array('route' => array('members.destroy', $member->id),
					'method' => 'delete'))
			 !!}
		<a href="{!! route('members.edit',$member->id) !!}" class="btn btn-primary">
			<i class="fa fa-edit"></i>
		</a>
        <button type="submit" class="btn btn-danger "onclick="return confirm('{!! trans('general.are_you_sure_you_want_to_delete_this_item') !!}')">
        		<i class="fa fa-remove"></i>
        </button>
    {!! Form::close() !!}
	</td>
</tr>