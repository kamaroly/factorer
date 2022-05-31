@extends('layouts.default')

@section('content_title')
 {{ trans('member.add_new_member') }}
@stop

@section('content')
{!! Form::open(array('route' => 'members.store','class'=>'ui form rahasi-form','files' => true )) !!}
	@include('members.form',['button'=>trans('member.add_new')])
{!! Form::close() !!}
@stop