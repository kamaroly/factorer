@extends('layouts.popdown')
 @section('content')
<div class="row">
    {!! Form::open(['url'=>route('settings.accountingplan.store')]) !!}

		@include('settings.accountingplan.form')
	
    {!! Form::submit(trans('general.save'), array('class' => 'btn btn-primary'))!!}
    {!! Form::close() !!}
  </div>
@endsection