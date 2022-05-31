@extends('layouts.default')

@section('content_title')
	{{$member->first_name }} 's {{ trans('member.details') }}
@stop
@section('content')
{!! Form::open(['route' => ['members.update',$member->id],
				'class'=>'ui form rahasi-form ',
				'method'=>'PUT',
				'files' => true 
				])
 !!}
	@include('members.layouts',['button'=>trans('member.edit')])
{!! Form::close() !!}
@stop

