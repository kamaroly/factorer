@extends('backend.layouts.app')
@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection
@section('content')

<div class="card">

    <div class="card-header">
        <div class="row">
            <div class="col-4">
                  {{-- Filter statuses --}}
                  <select
                    name="order_status"
                    onchange="handleStatusChange(this)"
                    id = 'js-select-status'
                    class="form-control pull-left">
                    <option value="" > All </option>
                    @foreach (config('order.statuses') as $key => $status)
                        <option value="{{ $key }}" > {{ $status }}</option>
                    @endforeach
                </select>

            </div>
            <div class="col-6">
                {!! $orders->links() !!}
            </div>
            <div class="float-right">

                    <a href="{{ route('backend.order.create', []) }}"
                        class="btn btn-success"
                        data-toggle="tooltip"
                        title="Create a New Order"
                        data-original-title="Create Order">
                            <i class="fas fa-plus-circle"></i>

                    </a>

            </div>
        </div>
    </div>


    <div class="card-body">
        <div class="row">
            <table class="table">
                <tr>
                    <th width="10%">Order #</th>
                    <th width="10%">Quantity</th>
                    <th width="10">Total</th>
                    <th width="10">Client</th>
                    <th width="10">Date Time</th>
                    <th width="10">Status</th>
                </tr>

                 @foreach ($orders as $order)


                    <tr>
                        <td>{{ $order->order_transaction_id }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ $order->total_price }}</td>
                        <td>{{ $order->client ? $order->client->names : "Guest" }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td >
                            <span class="btn btn-{{ $order->color }}">{{ $order->status }}</span>

                            <a
                                class="btn btn-default"
                                href="{{ route('backend.order.receipt', [$order->order_transaction_id]) }}">
                                <span > <i class="fas fa-print"></i></span>
                            </a>
                        </td>

                    </tr>
                @endforeach


            </table>
        </div>
    </div>

    </form>
</div>

<script>

    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function removeCurrenRow(element){
        var td = element.parentNode; // Get clicked element TD
        var tr = td.parentNode; // Get the TR of the clicked row
        tr.parentNode.removeChild(tr); // Remove the table row from
    }

    function handleQuantityUpdate(element, price)
    {
        const quantity  = element.value;
        const elementId = element.id.substr(-1);
        const totalPrice = quantity * price;

        document.getElementById("total-" + elementId).innerHTML = numberWithCommas(totalPrice);
    }

    /**
     * Handle auto filter upon selecting a different
     * status
     */
    function handleStatusChange(element)
    {
        window.location.href ='/admin/order/?order_status=' + element.value;
    }

</script>

@endsection
