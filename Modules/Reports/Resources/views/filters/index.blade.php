@extends('backend.layouts.app')

@push('after-styles')

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.css" rel="stylesheet">
@endpush

@section('content')

    <div class="card " style="z-index: 999">
        <div class="card-header">
            <h4>Select Filter To see Reports</h4>
        </div>

        <div class="card-body">
            <form action="#" action="_blank">
                <div class="container">
                    <div class="row">

                        <div class="col-sm">
                            <strong>Start Date:</strong>
                            <input class="date form-control" type="text" name="start_date">
                        </div>

                        <div class="col-sm">
                            <strong>End Date:</strong>
                            <input class="date form-control" type="text" name="end_date">
                        </div>

                        <div class="col-sm"></div>
                        <div class="col-sm"></div>
                        <div class="col-sm"></div>
                    </div>

                    <div class="row">
                        <div class="col-sm mt-2">
                            <button class="btn btn-success">Submit</button>
                        </div>
                    </div>
                  </div>
            </form>
        </div>
    </div>

@endsection

@push('after-scripts')

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.js"></script>

    <script type="text/javascript">
        $('.date').datepicker({
                format: 'mm-dd-yyyy',
                showOtherMonths: true,
                selectOtherMonths: true,
                autoclose: true,
                changeMonth: true,
                changeYear: true,
                orientation: "bottom left"
         });
    </script>
@endpush
