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
                        <td><input name="quantity[]" value="1" class="form-control text-center"></td>
                        <td>{{ $item['price'] }}</td>
                        <td>{{ $item['price'] }}</td>
                        <td>
                            <div class="text-right">
                                <button  
                                    class="btn btn-primary btn-sm " 
                                    data-toggle="tooltip" 
                                    title="Update Item">
                                    <i class="fas fa-wrench"></i>
                                </button>
                                <button  
                                    class="btn btn-danger btn-sm " 
                                    data-toggle="tooltip" 
                                    title="Remove Item">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </td>
                    </tr> 
                @endforeach
              
            </table>
        </div>
    </div>
</div>

@endsection
