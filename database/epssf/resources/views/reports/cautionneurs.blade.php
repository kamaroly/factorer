{{-- @if($loan->cautions->isEmpty() == false) --}}
<div dir="ltr" style="margin-left:-5.75pt;">
<table class="pure-table pure-table-bordered">
	<tbody>
		<tr style="height:0px">
			<th>Nr d'adhésion</th>
			<th>
			 Noms 
			</th>
			<th>
			Nr CI 
			</th>
			<th>
			District
			</th>
			<th>
			 Signature
			</th>
		</tr>
		<?php $count = 0; ?>
		@foreach ($loan->cautions as $caution)
		<tr >
			<td> Cautionneur.{!! ++$count!!} ({!! $caution->cautionneur_adhresion_id !!})</p></td>
			<td>{!! $caution->cauttionneur->names !!}</td>
			<td>{!! $caution->cauttionneur->member_nid !!}</td>
			<td>{!! $caution->cauttionneur->district !!}</td>
			<td></td>
		</tr>
		@endforeach
	</tbody>
</table>
</div>

{{-- @endif --}}