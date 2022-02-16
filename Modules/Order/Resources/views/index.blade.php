@extends('backend.layouts.app')
@section('breadcrumbs')
<x-backend-breadcrumbs>
    <x-backend-breadcrumb-item type="active" icon='{{ $module_icon }}'>{{ $module_title }}</x-backend-breadcrumb-item>
</x-backend-breadcrumbs>
@endsection
@section('content')

<div class="card">

    <form action="{{ route('backend.order.create') }}" method="POST">
    <div class="card-header d-inline-flex">
       
            <select name="client_id" class="form-control">
                <option value="0" selected>Select A Client</option>
                @foreach ($clients as $client)
                <option value="{{  $client->id  }}" >{{ $client->last_name }} {{ $client->first_name }}</option>                    
                @endforeach
            </select>
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
                
                    @csrf
                @foreach (config('order.products') as $item)

                        <input type="hidden" name="id[]" value="{{ $item['price'] }}">
                        <input type="hidden" name="name[]" value="{{ $item['name'] }}">
                        <input type="hidden" name="price[]" value="{{ $item['price'] }}">

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
                        <td>{{ $item['price'] }}
                        </td>
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

                <tr>
                    <th ></th>
                    <th ></th>
                    <th ></th>
                    <th ></th>
                    <th ></th>
                    <th width="10">
                        <button class="btn btn-success">
                            PLACE ORDER    
                        </button>
                  </th>
                </tr>
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

</script>

@endsection