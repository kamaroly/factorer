<table class="table table-bordered" style="background-color: #fff;width: 100%;">
<caption class="text-center text-green-900 text-3xl bg-green-200 font-bold">{!! $title !!}</caption>
 <thead>
 	<tr>
      <th>{{ trans('member.adhersion_number') }}</th>
	 		<th>{{ trans('member.names') }}</th>
	 		<th>{{ trans('member.institution') }}</th>
	 		{{-- <th>{{ trans('member.service') }}</th> --}}
        <th>{{ trans('member.amount') }}</th>
        <th>{{ trans('member.refunded_amount') }}</th>
        <th>{{ trans('member.balance') }}</th>
  	 </tr>
   	 </thead>
 <tbody>
  @forelse ($cautions as $caution)
    @include('members.cautionneurs.item',compact('caution','type'))
  @empty
    @include('members.no-items')
  @endforelse
  <tr>
  <th colspan="3">{{ trans('general.summary') }}</th>
  
  <th>{!! number_format(abs($cautions->sum('amount'))) !!}</th>
  <th>{!! number_format(abs($cautions->sum('refunded_amount')))!!}</th>
  <th>{!! number_format($cautions->sum('amount') - $cautions->sum('refunded_amount'))  !!}</th>
</tr>
 </tbody>
</table>