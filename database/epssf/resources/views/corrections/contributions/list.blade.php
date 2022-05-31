<table class="table table-bordered">
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.member') }}</th>
        <th> {{ trans('member.institution') }}</th>
        <th> {{ trans('contribution.transactionid') }}</th>
        <th> {{ trans('member.fee') }}</th>
  	 		<th><i class="fa fa-gear"></i></th>
  	 	</tr>
   	 </thead>
 <tbody>
 	@foreach ($contributions as $contribution)

 	{!! Form::open(array('route' => array('corrections.contributions.edit', $contribution['adhersion_id']), 'method' => 'PUT')) !!}

		@if ($contribution['action'] === 'REMOVE')
			<?php continue; ?>
		@endif

		<tr class="@if($contribution['action'] == 'ADD') bg-green-300 @endif">
			<td>{!! $contribution['adhersion_id'] !!}</td>
			<td>
				<strong>{!! $contribution['names'] !!}</strong> <br/>
				<small><strong>{{trans('member.nid')}}:</strong></small><small>{!! $contribution['nid'] !!}</small>
			 </td>
			<td>{!! $contribution['institution'] !!}</td>
			<td>{!! $contribution['transactionid'] !!}</td>
			<td>{!! Form::text('amount',  $contribution['amount'] , ['class'=>'form-control','size'=>'2'])!!}</td>
			<td>
				{!! Form::hidden('adhersion_id', $contribution['adhersion_id']) !!}
				{!! Form::hidden('contribution[]', $contribution['id']) !!}
				{!! csrf_field() !!}
				<button  class="btn btn-primary">
					<i class="fa fa-edit"></i>
				</button>
			<a  class="btn btn-danger" href="{!! route('corrections.contributions.remove',$contribution['adhersion_id']) !!}"
				 onclick="return confirm('{{ trans('general.are_you_sure_you_want_to_remove_this_item') }}');">
					<i class="fa fa-times"></i>
				</a>
			</td>
		</tr>
	 {!! Form::close() !!}
 	@endforeach
 </tbody>
</table>