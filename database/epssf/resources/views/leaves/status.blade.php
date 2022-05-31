@extends('layouts.default')
@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <h2>{{ trans('leave.Leave_Status') }}: <?php switch ($leave->status){
            case 1: 
                echo 'Pending';
                break;
            case 2: 
                echo 'Approved';
                break;
            case 3: 
                echo 'Rejected';
                break;
        } ?></h2>
    </div>
</div>
@stop