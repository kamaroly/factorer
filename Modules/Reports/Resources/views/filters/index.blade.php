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
            <form action="{{ route(request('report-route'), ['id'=>1]) }}" action="_blank">
                <div class="container">

                    {{-- Filter using date --}}
                    @include('reports::filters.date-filters')

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

