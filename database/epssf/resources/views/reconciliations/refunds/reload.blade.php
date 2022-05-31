@extends('layouts.default')

@section('content_title')
		<a href="{{ route('reconciliations.contributions.index') }}"
		  	 class="text-left text-xl font-semibold bg-gray-800 text-gray-100 rounded-lg hover:text-gray-200 p-3 mr-4">
		  	<< {{ trans('general.start_again') }}
		</a>
		
		{{ trans('general.reconcilate_refunds') }}
@endsection

@section('content')

<a href="{{ route('reconciliations.refunds.download','matching') }}" class="btn bg-green-700 text-green-200 hover:text-green-100">
	Matching
	{{ $inBoth->count() }} 
</a>
<a href="{{ route('reconciliations.refunds.download','not-matching') }}" class="btn bg-red-700 text-red-200"> 
	Not Matching in Amount
	{{ $notMatchingRefund->count() }}
</a>
<a href="{{ route('reconciliations.refunds.download','in-system-not-csv') }}" class="btn bg-gray-700 text-gray-200"class="btn">
	Exists in System but Not In Csv Matching
	{{ $onlyInDb->count() }}
</a>
<a href="{{ route('reconciliations.refunds.download','in-csv-not-system') }}" class="btn bg-indigo-700 text-indigo-200"class="btn">
	Exists in Csv not in the system
	{{ $onlyInCsv->count() }}

</a>
<table class="table table-bordered">
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.member') }}</th>
        <th> {{ trans('member.institution') }}</th>
        <th> {{ trans('refund.transactionid') }}</th>
        <th> {{ trans('member.refund_fees') }}</th>
  	 		<th>{{ trans('general.amount_in_csv') }}</th>
  	 	</tr>
   	 </thead>
 <tbody>
 @foreach ($inBoth as $refund)
	<tr>
			<td>{!! $refund['adhersion_id'] !!}</td>
			<td>
				<strong>{!! $refund['names'] !!}</strong> <br/>
				<small><strong>{{trans('member.nid')}}:</strong></small><small>{!! $refund['nid'] !!}</small>
			 </td>
			<td>{!! $refund['institution'] !!}</td>
			<td>{!! $refund['transactionid'] !!}</td>
			<td>{!! Form::text('amount',  $refund['amount'] , ['class'=>'form-control','size'=>'2'])!!}</td>
			<td>
				  <?php $color = ($refund['amount'] !== $refund['amount_in_csv'] )  ? 'border border-yellow-300': ''; ?> 
				{!! Form::text('amount',  $refund['amount_in_csv'] , ['class'=>'form-control '.$color,'readonly'=>true,'size'=>'2'])!!}
			</td>
		</tr>
@endforeach
</tbody>
</table>

@endsection