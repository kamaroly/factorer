<tr>
	<td>{!! $account->account_number !!}</td>
	<td>{!! $account->entitled !!}</td>
	<td>{!! $account->account_nature !!}</td>
	<td>
	{!! Form::open(array('route' => array('settings.accountingplan.destroy', $account->id),
					'method' => 'delete'))
			 !!}
		<a href="{!! route('settings.accountingplan.edit',$account->id) !!}" class="btn btn-primary popdown">
			<i class="fa fa-edit"></i>
		</a>
        <button type="submit" class="btn btn-danger "onclick="return confirm('{!! trans('general.are_you_sure_you_want_to_delete_this_item') !!}')">
        		<i class="fa fa-remove"></i>
        </button>
    {!! Form::close() !!}
    </td>
</tr>