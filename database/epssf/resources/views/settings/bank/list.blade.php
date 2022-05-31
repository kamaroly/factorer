@extends('layouts.default')
@section('content_title')
	{{ trans('navigations.bank_title') }}
@endsection
@section('content')
<div class="row">
<div class="col-xs-2 col-md-2 member-add-button">
   <a class="popdown btn btn-primary" href="{{ route('settings.bank.create')}}">
  <i class="fa fa-plus"></i>
  {{ trans('bank.add') }}
  </a>
  </div>
  <div class="col-xs-8 col-md-8">{!! $banks->render() !!}</div>
</div>
 <table class="ui table">
 	<thead>
 		<tr>
 			<th>{{ trans('general.id') }}</th>
 			<th>{{ trans('general.name') }}</th>
 			<th>{{ trans('general.action') }}</th>
 		</tr>
 	</thead>
 	<tbody> 		
		@each ('settings.bank.item', $banks, 'bank', 'partials.no-item')
 	</tbody>
 </table>
@stop