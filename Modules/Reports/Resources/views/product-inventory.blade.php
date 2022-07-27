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
                <th>{{ $purchaseBefore->where('item_qty', '>', 0 )->sum('item_qty') }}</th>
                <th>{{ $purchaseBefore->where('item_qty', '<', 0 )->sum('item_qty') }}</th>
                <th>{{ number_format($purchaseBefore->sum('item_qty')) }}</th>
            </tr>

                @php
                    $total = 0;
                @endphp

                @foreach ($purchases as $purchase)

                    @php
                        $total =  $total + $purchases->sum('item_qty');
                    @endphp

                    <tr>
                        <td>{{ $purchase->created_at->format('d/m/Y') }}</td>
                        <td>{{ $purchase->id }}</td>
                        <td>{!! $purchase->item_comment !!}</td>
                        <td>{{ ($purchase->item_qty > 0) ? $purchase->item_qty : '' }}</td>
                        <td>{{ ($purchase->item_qty < 0) ? $purchase->item_qty : '' }}</td>
                        <td>{{ number_format($total)  }}</td>
                    </tr>
                @endforeach

            <tr>
                <th colspan="3">Total Movement</th>
                <th>{{ $purchases->where('item_qty', '>', 0 )->sum('item_qty') }}</th>
                <th>{{ $purchases->where('item_qty', '<', 0 )->sum('item_qty') }}</th>
                <th>{{ number_format($total) }}</th>
            </tr>
        </table>
    </div>
</div>

@endsection
