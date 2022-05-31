@extends('layouts.default')
@section('content_title')
{{ trans('leave.Leaves_Management') }}
@stop
@section('content')
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <div class="col-xs-2 col-md-2 member-add-button">
           <a class="btn btn-primary popdown" href="{{ route('leaves.create') }}">
             <i class="fa fa-plus"></i>
               {{ trans('leave.new_leave') }}
          </a>
        </div>
</div>
</div>

<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <?php if ($leaves && count($leaves)) { ?>
          <br/>
          {!! $leaves->render() !!}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('leave.user') }}</th>
                        <th>{{ trans('leave.backup') }}</th> 
                        <th>{{ trans('leave.phone') }}</th>
                        <th>{{ trans('leave.days') }}</th>
                        <th>{{ trans('leave.Start_Date') }}</th>
                        <th>{{ trans('leave.end_date') }}</th>
                        <th>{{ trans('leave.status') }}</th>
                        <th>{{ trans('leave.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $leave) {
                        ?>
                        <tr>
                            <th scope="row">{!! $leave->id !!}</th>
                            <th >
                                <img class="direct-chat-img" src="{{route('files.get', $leave->user->photo)}}" align="left">
                                 {!! $leave->user->first_name !!} {!! $leave->user->last_name !!}
                            </th>          
                            <td>{{ $leave->backup }}</td>
                            <td>{{ $leave->phone }}</td>
                            <td>{{ $leave->days }}</td>
                            <td>{{ date("d F Y",strtotime($leave->start)) }}</td>
                            <td>{{ date("d F Y",strtotime($leave->end)) }}</td>
                            <td>{{ $leave->status }}</td>
                            <td>
                                <a class="btn btn-primary" href="<?php echo  route('leaves.approve', array('leave'=>$leave->id)); ?>">
                                <i class="fa fa-check text-white"></i>
                                {{ trans('leave.approve') }}</a>
                                <a class="btn btn-warning" href="<?php echo  route('leaves.reject', array('leave'=>$leave->id)); ?>">
                                <i class="fa fa-close text-white"></i>
                                {{ trans('leave.reject') }}</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            {{ trans('leave.No_Pending_Queue') }}
        <?php } ?>
    </div>
</div>
@stop