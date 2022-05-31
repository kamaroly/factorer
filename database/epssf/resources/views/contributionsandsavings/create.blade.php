@extends('layouts.default')

@section('content_title')
 {{ trans('contribution.add_new_contribution') }}
@stop

@section('content')
{!! Form::open(array('route' => 'contributions.store','class'=>'ui form','files' => true )) !!}
	@include('contributionsandsavings.form',['button'=>trans('contribution.add_new')])
{!! Form::close() !!}
@stop