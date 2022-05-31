@extends('layouts.popdown')
 @section('content')
<div class="row">
    {!! Form::open(['route'=>['settings.bank.update',$bank->id],'method'=>'PUT']) !!}

		@include('settings.bank.form')
	
    {!! Form::submit(trans('general.save'), array('class' => 'btn btn-primary'))!!}
    {!! Form::close() !!}
  </div>
@endsection