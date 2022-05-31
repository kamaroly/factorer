{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption><h3><u> {{ trans('report.all_guarantors') }}</u></h3> </caption>
     <thead>
      <tr>
        <th>{{ trans('guarantors.memeber_id') }}</th>
        <th>{{ trans('guarantors.names') }}</th>
         <th>{{ trans('guarantors.date') }}</th>
       <th>{{ trans('guarantors.guarantor') }}</th>
      <th>{{ trans('guarantors.names_gurantors') }}</th>
      <th>{{ trans('guarantors.loan_id') }}</th>
      <th>{{ trans('guarantors.loan_contract') }}</th>
      <th>{{ trans('guarantors.bonded_amount') }}</th>
          <th>{{ trans('guarantors.refunded_amount') }}</th>
          <th>{{ trans('guarantors.release_status') }}</th>
          <th>{{ trans('guarantors.realesed_date') }}</th>
      </tr>
     </thead>
 <tbody>
    @foreach ($members as $item)
     <tr>
      <td>{!! $item->memberid !!}</td>
        <td>{!! $item->names !!}</td>
         <td>{!! $item->date !!}</td>
      <td>{!! $item->guarantor !!}</td>
        <td>{!! $item->names_guarantors !!}</td>
      <td>{!! trans('guarantors.'.$item->operation_type) !!}</td>
        <td>{!! $item->loan_contract!!}</td>
      <td>{!! $item->bonded_amount !!}</td>
      <td>{!! $item->refunded_amount !!}</td>
      <td>{!! $item->release_status !!}</td>
      <td>{!! $item->realesed_date !!}</td>
     </tr>
    @endforeach
 </tbody>
</table>