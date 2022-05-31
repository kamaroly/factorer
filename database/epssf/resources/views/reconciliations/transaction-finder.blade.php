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