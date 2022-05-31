@extends('layouts.default')

@section('content_title')
		{{ trans('general.reconcilate_refunds') }}
@endsection

@section('content')
<form action="{{ route('reconciliations.refunds.find') }}"> 
   @include('reconciliations.transaction-finder')
</form>
@endsection