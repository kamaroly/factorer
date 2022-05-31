@extends(config('sentinel.layout'))

{{-- Web site Title --}}
@section('title')
@parent
Groups
@stop
@section('description')
Click here for <a href="{!! route('sentinel.users.index') !!}" class="btn btn-success">Users</a>
@stop

{{-- Content --}}
@section('content')
<section class="content">
<div class="box">

	<div class="col-md-6 columns">
		<h1>Available Groups</h1>
	</div>
	<div class="col-md-6 columns right">
		<button class="btn btn-primary" onClick="location.href='{{ route('sentinel.groups.create') }}'">New Group</button>
	</div>
</div>


	<table class="table table-striped">
		<thead>
			<th>Name</th>
			<th>Permissions</th>
			<th>Options</th>
		</thead>
		<tbody>
		@foreach ($groups as $group)
			<tr>
				<td>
					<a href="groups/{{ $group->hash }}">{{ $group->name }}</a>
				</td>
				<td>
					<?php
						$permissions = $group->getPermissions();
						$keys = array_keys($permissions);
						$last_key = end($keys);
					?>
					@foreach ($permissions as $key => $value)
						{{ ucfirst($key) . ($key == $last_key ? '' : ', ') }}
					@endforeach
				</td>
				<td>
					<button class="btn btn-warning pull-left" onClick="location.href='{{ route('sentinel.groups.edit', array($group->hash)) }}'">Edit</button>
				 	<button class="btn btn-danger action_confirm {{ ($group->hash == 2) ? 'disabled' : '' }}" type="button" data-token="{{ Session::getToken() }}" data-method="delete" href="{{ route('sentinel.groups.destroy', [$group->hash]) }}">Delete</button>
				 </td>
			</tr>	
		@endforeach
		</tbody>
	</table> 
</div>
</div>
<!--  
	The delete button uses Resftulizer.js to restfully submit with "Delete".  The "action_confirm" class triggers an optional confirm dialog.
	Also, I have hardcoded adding the "disabled" class to the Admin group - deleting your own admin access causes problems.
-->
@stop

