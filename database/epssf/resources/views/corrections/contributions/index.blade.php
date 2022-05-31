@extends('layouts.default')

@section('content_title')
  {{ trans('navigations.contribution_corrections') }}	
@endsection

@section('content')
	  @if (!$contributions->isEmpty())
	  	  @include('corrections.contributions.add-form')
	  	  @include('corrections.contributions.buttons')
		  @include('corrections.contributions.list')
	  @endif
@endsection