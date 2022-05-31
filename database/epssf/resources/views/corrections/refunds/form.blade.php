@extends('layouts.default')

@section('content_title')
  <a href="{{ route('corrections.refund.index') }}"
  	 class="text-left text-xl font-semibold bg-gray-800 text-gray-100 rounded-lg hover:text-gray-200 p-3 mr-4">
  	<< {{ trans('general.start_again') }}
  </a>
   
  {{ trans('navigations.refund_corrections') }}	
@endsection

@section('content')
	  @include('corrections.refunds.add-form')
  	  @include('corrections.refunds.buttons')
	  @include('corrections.refunds.list')
@endsection