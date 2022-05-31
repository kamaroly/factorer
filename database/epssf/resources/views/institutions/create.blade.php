@extends('layouts.popdown')
 @section('content')
<div class="row">
    <div class="col-md-12 col-md-offset-0">

        <h2>{{ trans('institution.create') }}</h2>
        {!!  Form::open(array('route' => 'settings.institution.store', 'method' => 'POST')) !!}
            @include('institutions.form')
        {!! Form::submit(trans('general.save'), array('class' => 'btn btn-primary'))!!}
        {!!  Form::close() !!}
    </div>
</div>
@stop