{!! Form::open(array('route' => array('contributions.update', $member['id']), 'method' => 'PUT')) !!}
<tr>
	<td>{!! Form::checkbox('memberIds[]', $member['id'], true) !!}</td>
	<td>{!! $member['adhersion_id']!!}</td>
	<td>{!! $member['first_name'] !!} {!! $member['last_name'] !!}</td>
	<td>{!! $member['institution']?$member['institution']:null !!}</td>	
	<td>{!! $member['employee_id'] !!}</td>
	<td>{!! Form::text('monthly_fee',  $member['monthly_fee'] , ['class'=>'form-control','size'=>'2'])!!}</td>
	<td>
	{!! Form::hidden('adhersion_number', $member['adhersion_id']) !!}
		<button  class="btn btn-primary">
			<i class="fa fa-edit"></i>
		</button>
		<a  class="btn btn-danger" href="{!! route('contributions.remove.member',$member['adhersion_id']) !!}"
		 onclick="return confirm('{{ trans('general.are_you_sure_you_want_to_delete_this_item') }}');">
			<i class="fa fa-times"></i>
		</a>
	</td>
</tr>
{!! Form::close() !!}
