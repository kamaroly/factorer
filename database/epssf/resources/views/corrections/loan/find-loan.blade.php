@extends('layouts.default')

@section('content')
@section('content_title')
  {{ trans('navigations.loan_corrections') }}	
@endsection

<form action="{{ route('corrections.loan.show') }}">
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
@section('scripts')
<script type="text/javascript">
	$(function() {
	$(".search-error").hide();
		$("#find-loan").click(function() {
			 var transactionId 	  = $('#loansearch').val();

			 if(undefined === transactionId || '' == transactionId){
			 	$(".search-error").show(400);
			 	return;
			 	exit();
			 }
		});
	});
</script>
@endsection