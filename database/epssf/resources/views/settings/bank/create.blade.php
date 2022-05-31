@extends('layouts.popdown')
 @section('content')
<div class="row">
    {!! Form::open(['url'=>route('settings.bank.store')]) !!}

		@include('settings.bank.form')
	
    {!! Form::submit(trans('general.save'), array('class' => 'btn btn-primary'))!!}
    {!! Form::close() !!}
  </div>
@endsection