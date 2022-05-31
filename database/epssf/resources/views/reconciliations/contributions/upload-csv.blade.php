@extends('layouts.default')

@section('content_title')
		{{ trans('general.reconcilate_contributions') }}
@endsection

@section('content')
{!! Form::open(array('route'=>'reconciliations.contributions.upload','method'=>'POST', 'files'=>true)) !!}
        <h4><u>{{ trans('Import csv to reconcile') }}</u></h4>
        {!! Form::file('contributions-csv',
        				['style'=>'float:left;margin-left:10px;','class'=>'btn btn-default']) 
        !!} 
        

        <button class="btn btn-success">
            <i class="fa fa-upload"></i> {{ trans('contributions.upload') }}
        </button>
        
        <a href="{{ route('contributions.sample.csv') }}" class="btn btn-default">
            <i class="fa fa-download"></i> {{ trans('general.download_sample') }}
        </a>
@endsection