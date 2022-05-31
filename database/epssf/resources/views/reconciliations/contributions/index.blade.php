@extends('layouts.default')

@section('content_title')
		{{ trans('general.reconcilate_contributions') }}
@endsection

@section('content')
<form action="{{ route('reconciliations.contributions.find') }}"> 
   @include('reconciliations.transaction-finder')
</form>
@endsection