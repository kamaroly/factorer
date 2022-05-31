@extends('layouts.default')

@section('content_title')

<a class="col-md-4 px-2 py-4 mb-5 
		  bg-yellow-700 text-yellow-200 
		  hover:text-yellow-100 hover:bg-yellow-800 rounded-lg" 
   href="{{ route('corrections.approvals.update',[$approval->id,'rejected']) }}">
	<span>&#x274C;</span> {{ trans('general.reject_loan_cancelling_request') }}
</a>

<a class="col-md-4 px-2 py-4 mb-5 
		  bg-green-700 text-green-200 
		  hover:text-green-100 hover:bg-green-800 rounded-lg " 
  onclick="return confirm('This loan will be cancelled and cannot be revorked')" 
  href="{{ route('corrections.approvals.update',[$approval->id,'approved']) }}">
	<span>&#10003;</span> {{ trans('general.approve_loan_cancelling_request') }}
</a>
@endsection
@section('content')
		{!! $content !!}
@endsection