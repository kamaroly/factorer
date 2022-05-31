@extends('layouts.default')

@section('content_title')
		{{ trans('general.reconcilate_refunds') }}
@endsection

@section('content')
{!! Form::open(array('route'=>'reconciliations.refunds.upload','method'=>'POST', 'files'=>true)) !!}
        <h4><u>{{ trans('Import csv to reconcile') }}</u></h4>
        {!! Form::file('refunds-csv',
        				['style'=>'float:left;margin-left:10px;','class'=>'btn btn-default']) 
        !!} 
        

        <button class="btn btn-success">
            <i class="fa fa-upload"></i> {{ trans('refunds.upload') }}
        </button>
        
        <a href="{{ route('contributions.sample.csv') }}" class="btn btn-default">
            <i class="fa fa-download"></i> {{ trans('general.download_sample') }}
        </a>
@endsection