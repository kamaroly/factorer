@include('partials.landscapecss')

<table class="pure-table pure-table-bordered">
<caption><h3><u>{{ trans('reports.monthly_fees_inventory') }}
{{ trans('general.between') }} 
      {!! date('d/M/Y',strtotime(request()->segment(4))) !!} 
    {{ trans('general.and') }} 
      {!! date('d/M/Y',strtotime(request()->segment(5))) !!} </h3></u>


 </caption>
     <thead>
      <tr>
        <th>{{ trans('general.date') }}</th>
      {{-- <th>{{ trans('loan.operation_type') }}</th> --}}
      <th>{{ trans('member.adhersion_id') }}</th>
      <th>{{ trans('member.names') }}</th>
       <th>{{ trans('member.status') }}</th>
      <th>{{ trans('member.institution') }}</th>
          <th>{{ trans('member.service') }}</th>
          <th>{{ trans('member.monthly_fees') }}</th>
      </tr>
     </thead>
 <tbody>
    @foreach ($history as $item)
     <tr>
      <td>{!! $item->dates !!}</td>
      {{-- <td>{!! $item->type !!}</td> --}}
      <td>{!! $item->adhersion_id !!}</td>
      <td>{!! $item->first_name !!} {!! $item->last_name !!}</td>
       <td>{!! $item->status !!}</td>
      <td>{!! $item->institution !!}</td>
      <td>{!! $item->service !!}</td>
      <td>{!! $item->amount !!}</td>
     </tr>
    @endforeach
 </tbody>
</table>