@extends(config('sentinel.layout'))

{{-- Web site Title --}}
@section('title')
@parent
Create Group
@stop

{{-- Content --}}
@section('content')
<section class="content">
<div class="box">
<form method="POST" action="{{ route('sentinel.groups.store') }}" accept-charset="UTF-8">
   
        <div class="small-6 large-centered columns">
            
            <h2>Create New Group</h2>
    
           
                <div class="small-2 columns">
                    <label for="right-label" class="right inline">Name</label>
                </div>
                <div class="small-10 columns {{ ($errors->has('name')) ? 'error' : '' }}">
                    <input placeholder="Name" name="name" type="text" class="form-control">
                    {{ ($errors->has('name') ? $errors->first('name', '<small class="error">:message</small>') : '') }}
                </div>
           
           
                <label for="Permissions">Permissions</label>
                @include('sentinel.groups.permissions')
            </div>

            <div class="row">
                <div class="small-10 small-offset-2 columns">
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input class="btn btn-primary col-md-12" value="Create New Group" type="submit">
                </div>
            
            
        </div>
    </div>
</form>
</div>
</div>
@stop