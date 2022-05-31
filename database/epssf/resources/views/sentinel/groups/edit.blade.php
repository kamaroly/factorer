@extends(config('sentinel.layout'))

<!-- Web site Title -->
@section('title')
@parent
Edit Group
@stop

<!--- Content -->
@section('content')
<form method="POST" action="{!! route('sentinel.groups.update', $group->hash) !!}" accept-charset="UTF-8">
  
        <div class="small-6 large-centered columns">
            <h3>{{ trans('edit.groups') }}</h3>
              <div class="row">
                    <div class="col-md-2">
                    <label for="right-label" class="right inline">{{ trans('group.name') }}</label>
                    </div>
                <div class="col-md-4 {!! ($errors->has('name')) ? 'error' : '' !!}">
                    <input placeholder="Name" name="name" value="{!! Input::old('name') ? Input::old('name') : $group->name !!}" type="text" class="form-control">
                    {!! ($errors->has('name') ? $errors->first('name', '<small class="error">:message</small>') : '') !!}
                </div>
                
              </div>
                <label for="Permissions">{{ trans('groups.permissions') }}</label>
                   <p>{{ trans('groups.select_ceb_view_own_profile_if_you_want_people_of_this_group_to_only_see_their_profile') }}</p>
                    @include('sentinel.groups.permissions')

                <div class="row">
                  <div class="col-md-12">
                                <input name="id" value="{!! $group->hash !!}" type="hidden">
                                <input name="_method" value="PUT" type="hidden">
                                <input name="_token" value="{!! csrf_token() !!}" type="hidden">
                                <input class="btn btn-lg btn-success col-md-12" value="Save Changes" type="submit">
                </div>
            </div>
        </div>
    </div>
{!! Form::close() !!}
</div>
</div>
@stop