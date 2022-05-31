@extends(config('sentinel.layout'))

{{-- Web site Title --}}
@section('title')
@parent
Home
@stop

{{-- Content --}}
@section('content')
	<?php

	    // Determine the edit profile route
	    if (($user->email == Sentry::getUser()->email) ) 
	    {
	        $editAction = route('sentinel.profile.edit');
	    } 
	    else 
	    {
	        $editAction =  route('sentinel.users.edit', [$user->hash]);
	    }

	?>

	<h4>Account Profile</h4>
	
	    <div class="small-6 columns">
		    @if ($user->first_name)
		    	<p><strong>First Name:</strong> {{ $user->first_name }} </p>
			@endif
			@if ($user->last_name)
		    	<p><strong>Last Name:</strong> {{ $user->last_name }} </p>
			@endif
		    <p><strong>Email:</strong> {{ $user->email }}</p>
		    
			<p>
				<em>
					Account created: {{ $user->created_at }} <br />
					Last Updated: {{ $user->updated_at }}
				</em>
			</p>
			<button class="btn btn-success shadow-lg" onClick="location.href='{{ $editAction }}'">Edit Profile</button>
		</div>

		<div class="small-6 columns">
			<h4>Group Memberships:</h4>
			<?php $userGroups = $user->getGroups(); ?>
		    <ul>
		    	@if (count($userGroups) >= 1)
			    	@foreach ($userGroups as $group)
						<li>{{ $group['name'] }}</li>
					@endforeach
				@else 
					<li>No Group Memberships.</li>
				@endif
		    </ul>

		</div>

@stop
