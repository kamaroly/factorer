@extends('backend.layouts.app')

@section('content')

<div class="card">
    <div class="card-header">
        <h3>Rapport de stock produit finie(entre et sortie)</h3>
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


            <tr>
                <th colspan="3">Balance du jour precedant</th>
                <th>10,000</th>
                <th>4,500</th>
                <th>5,500</th>
            </tr>

            <tr>
                <td>{{ date('d/m/Y') }}</td>
                <td>1</td>
                <td>Venue Due furnisseur</td>
                <td>700</td>
                <td>0</td>
                <td>6,200</td>
            </tr>

        </table>
    </div>
</div>

@endsection
