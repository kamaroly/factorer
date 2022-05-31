<table class="table table-bordered">
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.member') }}</th>
        <th> {{ trans('member.institution') }}</th>
        <th> {{ trans('refund.transactionid') }}</th>
        <th> {{ trans('member.refund_fees') }}</th>
  	 		<th><i class="fa fa-gear"></i></th>
  	 	</tr>
   	 </thead>
 <tbody>
 	@foreach ($refunds as $refund)
 	{!! Form::open(array('route' => array('corrections.refund.edit', $refund['id']), 'method' => 'PUT')) !!}

		@if ($refund['action'] === 'REMOVE')
			<?php continue; ?>
		@endif

		<tr class="@if($refund['action'] == 'ADD') bg-green-300 @endif">
			<td>{!! $refund['adhersion_id'] !!}</td>
			<td>
				<strong>{!! $refund['names'] !!}</strong> <br/>
				<small><strong>{{trans('member.nid')}}:</strong></small><small>{!! $refund['nid'] !!}</small>
			 </td>
			<td>{!! $refund['institution'] !!}</td>
			<td>{!! $refund['transactionid'] !!}</td>
			<td>{!! Form::text('amount',  $refund['amount'] , ['class'=>'form-control','size'=>'2'])!!}</td>
			<td>
				{!! Form::hidden('adhersion_id', $refund['adhersion_id']) !!}
				{!! Form::hidden('refund[]', $refund['id']) !!}
				{!! csrf_field() !!}
				<button  class="btn btn-primary">
					<i class="fa fa-edit"></i>
				</button>
			<a  class="btn btn-danger" href="{!! route('corrections.refund.remove',$refund['id']) !!}"
				 onclick="return confirm('{{ trans('general.are_you_sure_you_want_to_remove_this_item') }}');">
					<i class="fa fa-times"></i>
				</a>
			</td>
		</tr>
	 {!! Form::close() !!}
 	@endforeach
 </tbody>
</table>