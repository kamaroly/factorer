@extends('layouts.default')

@section('content_title')
  {{ trans('navigations.refund_corrections') }}	
@endsection

@section('content')
<form action="{{ route('corrections.refund.show') }}" method="GET">
	{!! csrf_field() !!}
	<table class="table">
		<tr>
			<th>{{ trans('general.transactionid') }} </th>
			<td><input type="text" name="transactionid" class="form-control"></td>
			<td>
				<button class="btn btn-success">
					{{ trans('general.find_transaction') }}
				</button>
			</td>
		</tr>
	</table>
</form>


@endsection