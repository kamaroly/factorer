<tr>
	<td>{!! $institution->id !!}</td>
	<td>{!! $institution->name !!}</td>
	<td>{!! Form::open(array('route' => array('settings.institution.destroy', $institution->id),
					'method' => 'delete'))
			 !!}
		<a href="{!! route('settings.institution.edit',$institution->id) !!}" class="btn btn-primary popdown">
			<i class="fa fa-edit"></i>
		</a>
        <button type="submit" class="btn btn-danger "onclick="return confirm('{!! trans('general.are_you_sure_you_want_to_delete_this_item') !!}')">
        		<i class="fa fa-remove"></i>
        </button>
    {!! Form::close() !!}</td>
</tr>
