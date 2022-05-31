@extends('layouts.default')

@section('content_title')
  @if (request()->has('emergencies'))
     {{ trans('member.member_with_emergency_loans') }}
  @else
    {{ trans('navigations.members') }}
  @endif
@stop

@section('content')
<div class="row">
<div class="col-xs-2 col-md-2 member-add-button">
   <a class="btn btn-primary" href="{{ route('members.create')}}">
  <i class="fa fa-plus"></i>
  {{ trans('member.add') }}
  </a>
  </div>
  <div class="col-xs-8 col-md-8">{!! $members->render() !!}</div>
</div>
    
      <table class="table table-bordered table-striped">
         <thead>
          <tr>
          <th>{{ trans('member.adhersion_number') }}</th>
            <th>{{ trans('member.names') }}</th>
            <th>{{ trans('member.institution') }}</th>
            <th>{{ trans('member.status') }}</th>
            <th>{{ trans('member.district') }}</th>
            <th>{{ trans('member.adhersion_date') }}</th>

            <th style="width: 170px;"><i class="fa fa-gear"></i>{{ trans('general.action') }}</th>
          </tr>
         </thead>
     <tbody>
       @each ('members.item', $members, 'member', 'members.no-items')
     </tbody>
     </table>
      
@stop
