@extends('layouts.default')

{{-- Display this title for normal contribution --}}
@section('content_title')
    {{ trans('navigations.'.$contributionType) }}
@stop

@section('content')
<script language="JavaScript">
function toggle(source) {
 checkboxes = document.getElementsByName('memberIds[]');
  for(var i=0, n=checkboxes.length;i<n;i++) {
    checkboxes[i].checked = source.checked;
  }
}
</script>

@if (!is_null($transactionid) && $transactionid !=false)
  <script type="text/javascript">
    OpenInNewTab("{!! route('piece.disbursed.saving',['transactionid'=>$transactionid]) !!}")
  </script>
@endif

  @include('contributionsandsavings.form',['institutions'=>$institutions,'institutionId'=>$institutionId])
  @include('partials.batch_upload',['route'=>'contributions.batch'])
  
  @if(!$members->isEmpty())
  {!! Form::open(array('route'=>'contributions.complete','method'=>'POST')) !!}
  <label>{{ trans('contribution.libelle') }}</label>
  {!! Form::text('wording', 
        (!empty($wording)) ? $wording : trans('contributions.contribution_for_the_month_of').' '.$month.date('/Y'),
        ['class'=>'form-control wording','placeholder'=>'contibution for this month ....']) !!}
    @include('contributionsandsavings.buttons',['cancelRoute'=>'contributions.cancel'])
  {!! Form::close() !!}

  {!! str_replace('href="/?page=', 'href="'.Request::url().'?'.$_SERVER['QUERY_STRING'].'&page=', $pageLinks->render()) !!}


  <table class="table table-bordered">
  	 <thead>
  	 	<tr>
        <th> <input type="checkbox" onClick="toggle(this)" checked/> </th>
        <th> {{ trans('member.adhersion_number') }}</th>
        <th> {{ trans('member.names') }}</th>
        <th> {{ trans('member.institution') }}</th>
        <th> {{ trans('member.employee_id') }}</th>
        <th> {{ trans('member.monthly_fee') }}</th>
  	 		<th><i class="fa fa-gear"></i></th>
  	 	</tr>
   	 </thead>
 <tbody>
   @each ('contributionsandsavings.item', $members, 'member', 'contributionsandsavings.no-items')
 </tbody>
  </table>
@endif
@endsection
