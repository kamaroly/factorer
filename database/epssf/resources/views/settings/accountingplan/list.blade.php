@extends('layouts.default')
@section('content_title')
	{{ trans('navigations.accounting_plan') }}
@endsection
@section('content')
<div class="row">
<div class="col-xs-2 col-md-2 member-add-button">
   <a class="popdown btn btn-primary" href="{{ route('settings.accountingplan.create')}}">
  <i class="fa fa-plus"></i>
  {{ trans('accountingplan.add') }}
  </a>
  </div>
  <div class="col-xs-8 col-md-8">{!! $accounts->render() !!}</div>
</div>
<table class="ui table">
	<thead>
		<tr>
			<th>{{ trans('accounting.accounting_number') }}   </th>
			<th>{{ trans('accounting.entitled') }} </th>
			<th>{{ trans('accounting.accounting_nature') }}</th>
			<th>{{ trans('general.action') }}</th>
		</tr>
	</thead>
	<tbody>
		@each ('settings.accountingplan.item', $accounts, 'account', 'partials.no-item')
	</tbody>
</table>
@stop