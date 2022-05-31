@extends('layouts.popdown')
 @section('content')
<div class="row">
    {!! Form::open(['route'=>['settings.accountingplan.update',$account->id],'method'=>'PUT']) !!}

		@include('settings.accountingplan.form')
	
    {!! Form::submit(trans('general.save'), array('class' => 'btn btn-primary'))!!}
    {!! Form::close() !!}
  </div>
@endsection