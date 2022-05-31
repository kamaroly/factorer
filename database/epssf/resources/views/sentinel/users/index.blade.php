@extends(config('sentinel.layout'))

{{-- Page Title --}}
@section('content_title')
 {{ trans('navigations.members') }}
@stop
@section('content_descriptions')
  Member modules
@stop

{{-- Content --}}
@section('content')

                <div class="box-header">
                  <a class='btn btn-primary' href="{{ route('sentinel.users.create') }}">
                    {{ trans('member.add_new') }}
                  </a>
                  {!! Form::open(['route'=>'ceb.settings.users.index','method'=>'GET']) !!}
                  <div class="box-tools">
                    <div class="input-group">
                      <input type="text" name="query" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search">
                      <div class="input-group-btn">
                        <button class="btn btn-sm btn-success"><i class="fa fa-search"></i></button>
                      </div>
                    </div>
                  </div>
                  {!! Form::close() !!}
                </div><!-- /.box-header -->
                <div class="box-body table-responsive no-padding">
{!! $users->appends($_GET)->render() !!}
<table class="table table-bordered">
    <thead>
      <th>{{ trans('member.email') }}</th>
      <th>{{ trans('member.names') }}</th>
      <th>{{ trans('user.status') }}</th>
      <th>{{ trans('general.options') }}</th>
    </thead>
    <tbody>
      @foreach ($users as $user)
        <tr>
          <td><a href="{{ route('sentinel.users.show', array($user->hash)) }}">{{ $user->email }}</a></td>
          <td>{{ $user->first_name }}  {{ $user->last_name }} </td>
          <td>{{ $user->status }} </td>
          <td>
             <button class="btn btn-default" type="button" onClick="location.href='{{ action('\\Sentinel\Controllers\UserController@edit', array($user->hash)) }}'">Edit</button>
                        @if ($user->status != 'Suspended')
                            <button class="btn btn-default" type="button" onClick="location.href='{{ action('\\Sentinel\Controllers\UserController@suspend', array($user->hash)) }}'">Suspend</button>
                        @else
                            <button class="btn btn-default" type="button" onClick="location.href='{{ action('\\Sentinel\Controllers\UserController@unsuspend', array($user->hash)) }}'">Un-Suspend</button>
                        @endif
                        @if ($user->status != 'Banned')
                            <button class="btn btn-default" type="button" onClick="location.href='{{ action('\\Sentinel\Controllers\UserController@ban', array($user->hash)) }}'">Ban</button>
                        @else
                            <button class="btn btn-default" type="button" onClick="location.href='{{ action('\\Sentinel\Controllers\UserController@unban', array($user->hash)) }}'">Un-Ban</button>
                        @endif
                     <a href="{{ route('sentinel.users.destroy', array($user->hash)) }}" class="btn btn-danger" 
                    onclick="event.preventDefault();
                             document.getElementById('delete-form-{{ $user->hash }}').submit();">
                  Delete
                </a>
                {!! 
                  Form::open(['method' => 'DELETE', 'route' => ['sentinel.users.destroy', $user->hash],'id'=>'delete-form-'. $user->hash]) 
                !!}

                {!! Form::close() !!}
                                             </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  {!! $users->render() !!}
   </div><!-- /.box-body -->
  @stop