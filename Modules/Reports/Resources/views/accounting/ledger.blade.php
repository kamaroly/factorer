@extends('backend.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Grand livre (ledger)</h3>
    </div>

    <div class="card-body">

        <table class="table table-striped">

            <thead class="thead-dark">
                <th>Date</th>
                <th>Record ID</th>
                <th>Wording</th>
                <th>Entree</th>
                <th>Sortie</th>
                <th>Amount</th>
            </thead>
        </table>
    </div>
</div>

@endsection
