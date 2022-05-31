<table class="pure-table pure-table-bordered">
<caption>	
		{{ trans('reports.'.request()->segment(5).'_loans') }}  
		{{ trans('general.between') }} 
			{!! date('d/M/Y',strtotime(request()->segment(3))) !!} 
		{{ trans('general.and') }} 
			{!! date('d/M/Y',strtotime(request()->segment(4))) !!}
</caption>
	<thead>
		<tr>
			<th>#</th>
			<th>Noms et prenoms</th>
			<th>Institution</th>
			<th>Nº Adhesion</th>
			<th>Epargne</th>
			<th>Dette</th>
			<th>Solde</th>
			<th>CM</th>
			<th>Droit</th>
			<th>Souhait</th>
			<th>Taux</th>
			<th>Ech</th>
			<th>Tranche</th>
			<th>P A Remb</th>
			<th>Nouv dette</th>
			<th>Facteur</th>
			<th>Interets</th>
			<th>Int P O U</th>
			<th>N A T</th>
			<th>N P DRT</th>
		</tr>
	</thead>
	<tbody>
		
		<?php $number = 1; ?>
		<?php $loanType = null; ?>
		@foreach ($loans as $loan)

			@if ($loanType !== $loan->operation_type)
			<tr>
				<?php $loanType = $loan->operation_type; ?>
				<th colspan="20">{!! trans('loans.'.$loanType) !!}</th>
			</tr>
			<?php $number = 1; ?>
			@endif

			@include('reports.loans.item',compact('loan','number'))
			<?php $number++; ?>
		@endforeach

	</tbody>
</table>	
