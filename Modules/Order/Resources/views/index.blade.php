@extends('backend.layouts.app')
@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection
@section('content')

<div class="card">

    <div class="card-header d-inline-flex">
        <div class="col-2">
            <select name="customer_id" class="form-control">
                <option value="0">Select Customer</option>
            </select>
        </div>
        or
        <div class="col-2 d-inline">
            <button class="btn btn-success">Create New Customer</button>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <table class="table">
                <tr>
                    <th width="10%">#</th>
                    <th width="50%">Item</th>
                    <th width="10%">Quantity</th>
                    <th width="10%">U.Price</th>
                    <th width="10">Total</th>
                    <th width="10">Total</th>
                </tr>
                @foreach (config('order.products') as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>
                            <input
                                onkeyup="handleQuantityUpdate(this, {{ $item['price'] }})"
                                id="quantity-{!! $item['id'] !!}" 
                                name="quantity[]" 
                                value="1" 
                                class="form-control text-center">
                        </td>
                        <td>{{ $item['price'] }}</td>
                        <td id="total-{!! $item['id'] !!}">{{ $item['price'] }}</td>
                        <td class="text-right">
                            <button  
                                    onclick="removeCurrenRow(this)"
                                    class="btn btn-danger btn-sm " 
                                    data-toggle="tooltip" 
                                    title="Remove Item">
                                    <i class="fas fa-minus"></i>
                                </button>
                        </td>
                    </tr> 
                @endforeach
              
            </table>
        </div>
    </div>
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

</script>

@endsection