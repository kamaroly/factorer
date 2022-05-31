<table class="bg-yellow-100 text-yellow-700">
	<caption class="bg-yellow-700 font-semibold text-xs text-yellow-100">
	  {{ trans('loan.cautionneurs') }}
	</caption>
	<tr class="bg-yellow-700 text-xs text-yellow-700">
	@foreach ($cautions as $guarantor)
		<td>
			<span class="block w-full bg-gray-100 text-xs font-semibold">{{ $guarantor->cautionneur_adhresion_id  }}</span> 
			<span class="block w-full bg-gray-100 text-xs">{{ number_format($guarantor->amount)  }} RWF</span>
		</td>
	@endforeach
	</tr>
</table>