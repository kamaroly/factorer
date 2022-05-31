{{-- Start by pulling this member profile --}}
<table class="pure-table pure-table-bordered">
<caption><h3><u> {{ trans('reports.report_monthlycontribution',['institution'=>$institution]) }} </u></h3> </caption>
     <thead>
      <tr>
        <th>{{ trans('member.adhersion_id') }}</th>
        <th>{{ trans('member.names') }}</th>
        <th>{{ trans('member.institution') }}</th>
        <th>{{ trans('member.service') }}</th>       
        <th>{{ trans('member.status') }}</th>
        <th>{{ trans('member.monthly_fee') }}</th>
      </tr>
     </thead>
 <tbody>
   @each ('reports.member.item_member_contribution', $members, 'member', 'members.no-items')
   <tr>
        <th colspan="5">{{ trans('member.monthly_fee') }}</th>
      <th>{!! number_format(abs($members->sum('monthly_fee'))) !!}</th>
      </tr>
 </tbody>
</table>
<table class="pure-table pure-table-bordered">
      <tr>
        <td>
          <strong>{{ trans('report.done_by') }}</strong> <br/>
          <?php $user = Sentry::getUser(); ?>
        {!!  $user->first_name !!} {!! $user->last_name !!}
        </td>
        <td><strong>{{ trans('report.gerant') }}</strong> <br/>
        {!! (new Ceb\Models\Setting)->get('general.gerant') !!}
      </td>
      </tr>

      <tr style="height: 50px;">
        <td><strong>Signature:</strong></td>
        <td><strong>Signature:</strong></td>
      </tr>
 </table>