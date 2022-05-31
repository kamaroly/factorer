@extends('layouts.default')

@section('content_title')
  {{ trans('navigations.correction_approvals') }}	
@endsection

@section('content')
{{ $approvals->render() }}
	@if (!$approvals->isEmpty())
	<table class="table">
		<thead>
			<tr>
				<th>{{ trans('general.datetime') }}</th>				
				<th>{{ trans('general.transactionid') }}</th>
				<th>{{ trans('general.nature') }}</th>
				<th>{{ trans('general.type') }}</th>
				<th>{{ trans('general.status') }}</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach ($approvals as $approval)
				<tr>
					<td>{{ $approval->updated_at }}</td>
					<td>{{ $approval->transactionid }}</td>
					<td>{{ $approval->nature }}</td>
					<td>{{ $approval->type }}</td>
					<td>{{ $approval->status }}</td>
					<th>
						<a class="text-teal-600" target="_blank" href="{{ route('corrections.approvals.show',$approval->id) }}">
						   {{ trans('general.review') }}
						</a>
					</th>
				</tr>
			@endforeach
		</tbody>
	</table>
	@endif
@endsection