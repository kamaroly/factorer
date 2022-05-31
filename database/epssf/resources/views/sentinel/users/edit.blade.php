@extends(config('sentinel.layout'))

{{-- Web site Title --}}
@section('title')
@parent
Edit Profile
@stop

{{-- Content --}}
@section('content')

<?php
    // Pull the custom fields from config
    $isProfileUpdate = ($user->email == Sentry::getUser()->email);
    $customFields = config('sentinel.additional_user_fields');

    // Determine the form post route
    if ($isProfileUpdate) {
        $profileFormAction = route('sentinel.profile.update');
        $passwordFormAction = route('sentinel.profile.password');
    } else {
        $profileFormAction =  route('sentinel.users.update', $user->hash);
        $passwordFormAction = route('sentinel.password.change', $user->hash);
    }
?>

<h1>Edit 
@if ($isProfileUpdate)
    Your
@else 
    {{ $user->email }}'s 
@endif 
Account</h1>

<?php $customFields = config('sentinel.additional_user_fields'); ?>

@if (! empty($customFields))
<div>
    <h4>Profile</h4>
    <form method="POST" action="{{ $profileFormAction }}" accept-charset="UTF-8" class="form-horizontal" role="form">

        @foreach(config('sentinel.additional_user_fields') as $field => $rules)

          {{-- If this column is language then load languages using drop box and continue --}}
                @if ($field=='language')
                    <div class="small-3 columns">
                     <label for="right-label" class="right inline">{!! ucwords(str_replace('_',' ',$field)) !!}</label>
                    </div>
                     <div class="small-9 columns {!! ($errors->has($field)) ? 'has-error' : '' !!}">
                         {!! Form::select($field,  
                            [
                            'en'    =>  'English',
                            'fr'    =>  'French',
                            'kin'   =>  'Kinyarwanda',
                            ], Input::old($field) ? Input::old($field) : $user->$field,['class' => 'form-control']) !!}
                            {!! ($errors->has($field) ? $errors->first($field, '<small class="error">:message</small>') : '') !!}
                    </div>
                    <?php continue; ?>
                @endif
        <p>
            <label for="{{ $field }}">{{ ucwords(str_replace('_',' ',$field)) }}</label>
            {{-- If a user is not admin then don't allow him to update his names --}}
            <?php $allowEdit = 'disabled="disabled"'; ?>
            @if (Sentry::getUser()->hasAccess('admin'))
            <?php $allowEdit = null; ?>
            @endif

            <input class="form-control" name="{{ $field }}" type="text" 
            value="{{ Input::old($field) ? Input::old($field) : $user->$field }}"
            {{ $allowEdit }}
            >

            {{ ($errors->has($field) ? $errors->first($field) : '') }}
        </p>
        @endforeach

        <p>         
            <input name="_method" value="PUT" type="hidden">
            <input name="_token" value="{{ csrf_token() }}" type="hidden">
            <input class="btn btn-success" value="Submit Changes" type="submit">
        </p>

    </form>
</div>
<hr />
@endif

@if (Sentry::getUser()->hasAccess('admin') && ($user->hash != Sentry::getUser()->hash))

    <h4>Group Memberships</h4>
    <form method="POST" action="{{ route('sentinel.users.memberships', $user->hash) }}" accept-charset="UTF-8" 
       class="form-inline" role="form">

        @foreach($groups as $group)
     
        <label class="checkbox-inline">
          <p>
            <input type="checkbox" name="groups[{{ $group->name }}]" value="1" {{ ($user->inGroup($group) ? 'checked' : '') }}> {{ $group->name }}
                </p>
        </label>
      
        @endforeach
    <p>
        <input name="_token" value="{{ csrf_token() }}" type="hidden">
        <input value="Update Memberships" type="submit" class="btn btn-warning">
    </p>
    </form>

<hr />
@endif

<h4>Change Password</h4>
<form method="POST" action="{{ $passwordFormAction }}" accept-charset="UTF-8" class="form-inline" role="form">
        
    @if(! Sentry::getUser()->hasAccess('admin'))
    <p>
       <label for="oldPassword">Old Password</label>
       <input class="form-control" placeholder="Old Password" name="oldPassword" value="" id="oldPassword" type="password">
       {{ ($errors->has('oldPassword') ? '<br />' . $errors->first('oldPassword') : '') }}
    </p>
    @endif

    <p>
        <label for="newPassword">New Password</label>
        <input class="form-control" placeholder="New Password" name="newPassword" value="" id="newPassword" type="password">
        {{ ($errors->has('newPassword') ?  '<br />' . $errors->first('newPassword') : '') }}
    </p>

    <p>
        <label for="newPassword_confirmation">Confirm New Password</label>
        <input class="form-control" placeholder="Confirm New Password" name="newPassword_confirmation" value="" id="newPassword_confirmation" type="password">
        {{ ($errors->has('newPassword_confirmation') ? '<br />' . $errors->first('newPassword_confirmation') : '') }}
    </p>

    <input name="_token" value="{{ csrf_token() }}" type="hidden">
    <input class="btn btn-danger" value="Change Password" type="submit">

</form>

@stop