@extends('layouts.default')
    @section('content_title')
        {{ trans('items.item') }}
    @endsection
@section('content')
<div class="col-sm-12">
    <a class="btn btn-success" href="{!! route('items.add') !!}">
      <i class="fa fa-plus"></i>
        {!! trans('item.add') !!}
    </a>

    @if( isset( $items ) & !empty( $items ) )
    {!! $items->render() !!}

    <table class="table table-bordered table-striped table-hover ">
        <thead>
            <th>{{ trans('item.id') }}</th>
            <th>{{ trans('item.name') }}</th>
            <th>{{ trans('item.price') }}</th>
            <th>{{ trans('item.quantity') }}</th>
            <th>{{ trans('item.description') }}</th>
            <th><i class="fa fa-gear"></i> {{ trans('general.actions') }}</th>
        </thead>
        <tbody>
          @each ('items.item', $items, 'item', 'partials.no-item')
        </tbody>
    </table>
    @endif
    <a class="btn btn-success" href="{!! route('items.add') !!}">
      <i class="fa fa-plus"></i>
        {!! trans('item.add') !!}
    </a>
</div>

@endsection