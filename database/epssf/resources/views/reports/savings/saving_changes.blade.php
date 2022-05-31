<h4>{{ ucfirst(trans('report.titre_contribution_changes'))}} {{ Request::segment(4).' Et '.Request::segment(5) }}</h4>
<table class="pure-table pure-table-bordered">
	<thead>
		<tr>
			<th>{{ trans('member.adhersion_id') }}</th>
			<th>{{ trans('member.names') }}</th>
			<th>{{ trans('member.institution') }}</th>
			<th>{{ trans('member.service') }}</th>
			<th>{{ trans('member.cotisation') }}</th>
		</tr>
	</thead>

	<tbody>
	<?php
	 $currentSection = null;
	 $currentInstitution = null;
	?>
	@foreach ($contributions as $contribution)
		<?php
		 if ($contribution->monthly_fee > $contribution->amount) {
		 	$section = 'Augmentation';
		 }
		 if ($contribution->monthly_fee < $contribution->amount) {
		 	$section = 'Diminution';
		 }
	 	 if ($contribution->monthly_fee == $contribution->amount) {
		 	$section = 'new';
		 }
	  ?>

		@if ($contribution->institution  !== $currentInstitution)
		<?php $currentInstitution = $contribution->institution ?>
			<tr>
				<td colspan="5" style="color: red;font-weight: 700;border-bottom: 1px solid red">
					{{ $currentInstitution }}
				</td>
			</tr>
		@endif
		@if ($section !== $currentSection)
		<?php $currentSection = $section ?>
			<tr>
				<td colspan="5" style="color: green;font-weight: 700;border-bottom: 2px dashed green">
					{{ trans('report.'.strtolower($currentSection)) }}
				</td>
			</tr>
		@endif

		<tr>
			<td>{!! $contribution->adhersion_id !!}</td>
			<td>{!! $contribution->first_name !!} {!! $contribution->last_name !!}</td>
			<td>{!! $contribution->institution !!}</td>
			<td>{!! $contribution->service !!}</td>
			<td>{!! number_format($contribution->amount) !!}</td>
		</tr>
	@endforeach
	</tbody>
</table>