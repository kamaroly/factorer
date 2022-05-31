@extends('layouts.default') 

@section('content_title')
    {{ trans('items.items_inventory') }}
@endsection

@section('content')
<div class="container col-sm-12">
    {!! Form::open(array('url' => request()->url(),'method'=>'POST', 'role' => 'form' ,'class' => 'form-horizontal')) !!}

    {!! Form::hidden('id', $item->id) !!}
    <div class="form-group">
        {!! Form::label('name', trans('item.name') ) !!}
        <div >
            {!! Form::text('name', (count(old('name')) > 0) ? old('name') : $item->name  , array('class' => 'form-control') ) !!}
        </div>
        {!! $errors->first('name') !!}
    </div>
    <div class="form-group">
        {!! Form::label('name', trans('item.price') ) !!}
        <div >
            {!! Form::text('price', (count(old('price')) > 0) ? old('price') : $item->price  , array('class' => 'form-control') ) !!}
        </div>
        {!! $errors->first('price') !!}
    </div>
    <div class="form-group">
        {!! Form::label('name', trans('item.quantity') ) !!}
        <div >
            {!! Form::text('quantity', (count(old('quantity')) > 0) ? old('quantity') : $item->quantity  , array('class' => 'form-control') ) !!}
        </div>
        {!! $errors->first('quantity') !!}
    </div>
    <div class="form-group">
        {!! Form::label('description', trans('item.description')) !!}
        <div >
            {!! Form::textarea('description', (count(old('description')) > 0) ? old('description') : $item->description  , array('rows'=>'4','class'=>'form-control') ) !!}
        </div>
        {!! $errors->first('description') !!}
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            {!! Form::submit(trans('item.add') , array('class' => 'col-sm-5 btn btn-success form-control')  ) !!}
        </div> 
        
    </div>
    {!! Form::close() !!}
</div>
@endsection