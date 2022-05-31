<table>
	<caption class="text-gray-700 text-2xl font-semibold leading-tight uppercase"> {{ trans('report.cash-flow-statement') }} between {{ $startDate }} and  {{ $endDate }} </caption>
	<thead>
		<tr>
			<!--<th class="uppercase">{{ trans('report.flow') }}</th>-->
			<th class="uppercase">{{ trans('report.account_number') }}</th>
			<th class="uppercase">{{ trans('report.account_name') }}</th>
			<th class="uppercase">{{ trans('report.amount') }}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th colspan="4">{{ trans('report.operating_activities') }}</th> 
		</tr>
		<tr>
			<!--<td></td>-->
			<td>{{  config('ceb.resultant_de_la_period','8750') }}</td>
			<td>{{ trans('report.resultat_de_la_periode') }}</td>
			<td 
			{{-- Turn red negative balance --}}
			 @if ($netIncome < 0)
				class="text-red-600"
			 @endif
			>{{ number_format($netIncome) }}</td>
		</tr>
		{{-- operating_activities --}}
		@foreach ($accounts->where('activity','operating_activities') as $account)

			<tr>
				<!--<td>{{ $account->flow }}</td>-->
				<td>{{ $account->account_number }}</td>
				<td>{{ $account->entitled }}</td>
				<td 
				{{-- Turn red negative balance --}}
				 @if ($account->balance < 0)
					class="text-red-600"
				 @endif
				>{{ number_format($account->balance)}}</td>
			</tr>
		@endforeach
		<tr> 
			<th colspan="2"> </th>
			<th colspan="1" class="bg-yellow-600 text-gray-900"> {{ number_format($netIncome + $totalOperations = $accounts->where('activity','operating_activities')->sum('balance')) }} </th>
		</tr>
		
		<tr>
			<th colspan="3">{{ trans('report.investiment_activities') }}</th> 
		</tr>
		
		@foreach ($accounts->where('activity','investiment_activities') as $account)
			<tr>
				<!--<td>{{ $account->flow }}</td>-->
				<td>{{ $account->account_number }}</td>
				<td>{{ $account->entitled }}</td>
				<td 
				{{-- Turn red negative balance --}}
				 @if ($account->balance < 0)
					class="text-red-600"
				 @endif
				>{{ number_format($account->balance) }}</td>
			</tr>
		@endforeach
		<tr> 
			<th colspan="2"> </th>
			<th colspan="1"  class="bg-yellow-600 text-gray-900"> {{ number_format($totalInvestiments = $accounts->where('activity','investiment_activities')->sum('balance'),0) }} </th>
		</tr>
		<tr>
			<th colspan="3">{{ trans('report.financing_activities') }}</th> 
		</tr>
		@foreach ($accounts->where('activity','financing_activities') as $account)
			<tr>
				<!--<td>{{ $account->flow }}</td>-->
				<td>{{ $account->account_number }}</td>
				<td>{{ $account->entitled }}</td>
				<td 
				{{-- Turn red negative balance --}}
				 @if ($account->balance < 0)
					class="text-red-600"
				 @endif
				>{{ number_format($account->balance) }}</td>
			</tr>
		@endforeach
		<tr> 
			<th colspan="2"> </th>
			<th colspan="1"  class="bg-yellow-600 text-gray-900"> {{ number_format($totalFinancing = $accounts->where('activity','financing_activities')->sum('balance'),0) }} </th>
		</tr>
	     <tr> 
			<th colspan="2"> </th>
		</tr>
        <tr> 
			<th colspan="2" class="bg-green-600 text-gray-900"> {{ trans('report.net_cash_increase') }} </th>
			<th colspan="1"  class="bg-green-600 text-gray-900"> {{ number_format(($netIncome + $totalFinancing + $totalInvestiments  + $totalOperations),0) }} </th>
		</tr>
	</tbody>
</table>