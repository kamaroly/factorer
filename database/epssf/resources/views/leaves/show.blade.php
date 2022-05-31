@extends('layouts.default')
@section('content_title')
{{ trans('leave.Leaves_History') }}
@stop
@section('content')
<div class="row">
    <div class="col-md-12 col-md-offset-0">
        <?php if ($leaves) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ trans('leave.Start_Date') }}</th>
                        <th>{{ trans('leave.end_Date') }}</th>
                        <th>{{ trans('leave.days') }}</th>
                        <th>{{ trans('leave.backup') }}</th>
                        <th>{{ trans('leave.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($leaves as $leave) {
                            ?>
                    <tr>
                            <th scope="row"><?php echo $leave['id'] ?></th>
                            <td>{{ date("d F Y",strtotime($leave->start)) }}</td>
                            <td>{{ date("d F Y",strtotime($leave->end)) }}</td>
                            <td>{{ $leave->days }}</td>
                            <td>{{ $leave->backup }}</td>
                            <td>{{ $leave->status }}</td>
                    </tr>
                        <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            {{ trans('leave.No_Leaves') }}
        <?php } ?>
    </div>
    <div class="col-md-12 col-md-offset-4">
        <a class="btn btn-primary popdown" href="{{ route('leaves.create')}}">{{ trans('leave.Apply_for_leave') }}</a>
    </div>
</div>
@stop