@extends('layouts.default')

@section('content_title')
  {{ trans('navigations.reporting') }}
@stop
@section('content')
	@include('reports.layout')
@stop
