<table class="table table-bordered">
	<caption>
		<h4 class="markdown mb-6 px-6 max-w-3xl mx-auto lg:ml-0 lg:mr-auto xl:mx-0 xl:px-12 xl:w-3/4">
			{{ trans('general.add_another_refund_to_this_transaction') }}
		</h4>
	</caption>
  	 <thead>
  	 	<tr>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.refund_fees') }}</th>
        <th> {{ trans('member.contract_number') }}</th>
  	 		<th></th>
  	 	</tr>
   	 </thead>
	{!! Form::open(array('route' => array('corrections.refund.add'), 'method' => 'POST')) !!}
	<tr>
		<td>{!! Form::text('adhersion_id', null, ['class'=>'form-control','size'=>'2']) !!}</td>
		<td>{!! Form::text('amount', 0  , ['class'=>'form-control','size'=>'2'])!!}</td>
		<td>{!! Form::text('contract_number', 0  , ['class'=>'form-control','size'=>'2'])!!}</td>
		<td>
			{!! csrf_field() !!}
			<button  class="btn btn-success">
				<i class="fa fa-plus"></i>
			</button>
		</td>
	</tr>
	{!! Form::close() !!}