@extends('backend.layouts.app')

@push('after-styles')
    <style>
        body{margin-top:20px;
    color: #2e323c;
    background: #f5f6fa;
    position: relative;
    height: 100%;
}
.invoice-container {
    padding: 1rem;
}
.invoice-container .invoice-header .invoice-logo {
    margin: 0.8rem 0 0 0;
    display: inline-block;
    font-size: 1.6rem;
    font-weight: 700;
    color: #2e323c;
}
.invoice-container .invoice-header .invoice-logo img {
    max-width: 130px;
}
.invoice-container .invoice-header address {
    font-size: 0.8rem;
    color: #9fa8b9;
    margin: 0;
}
.invoice-container .invoice-details {
    margin: 1rem 0 0 0;
    padding: 1rem;
    line-height: 180%;
    background: #f5f6fa;
}
.invoice-container .invoice-details .invoice-num {
    text-align: right;
    font-size: 0.8rem;
}
.invoice-container .invoice-body {
    padding: 1rem 0 0 0;
}
.invoice-container .invoice-footer {
    text-align: center;
    font-size: 0.7rem;
    margin: 5px 0 0 0;
}

.invoice-status {
    text-align: center;
    padding: 1rem;
    background: #ffffff;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    margin-bottom: 1rem;
}
.invoice-status h2.status {
    margin: 0 0 0.8rem 0;
}
.invoice-status h5.status-title {
    margin: 0 0 0.8rem 0;
    color: #9fa8b9;
}
.invoice-status p.status-type {
    margin: 0.5rem 0 0 0;
    padding: 0;
    line-height: 150%;
}
.invoice-status i {
    font-size: 1.5rem;
    margin: 0 0 1rem 0;
    display: inline-block;
    padding: 1rem;
    background: #f5f6fa;
    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
}
.invoice-status .badge {
    text-transform: uppercase;
}

@media (max-width: 767px) {
    .invoice-container {
        padding: 1rem;
    }
}


.custom-table {
    border: 1px solid #e0e3ec;
}
.custom-table thead {
    background: #007ae1;
}
.custom-table thead th {
    border: 0;
    color: #ffffff;
}
.custom-table > tbody tr:hover {
    background: #fafafa;
}
.custom-table > tbody tr:nth-of-type(even) {
    background-color: #ffffff;
}
.custom-table > tbody td {
    border: 1px solid #e6e9f0;
}


.card {
    background: #ffffff;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    border: 0;
    margin-bottom: 1rem;
}

.text-success {
    color: #00bb42 !important;
}

.text-muted {
    color: #9fa8b9 !important;
}

.custom-actions-btns {
    margin: auto;
    display: flex;
    justify-content: flex-end;
}

.custom-actions-btns .btn {
    margin: .3rem 0 .3rem .3rem;
}

@media print {
  button.close {display:none !important;}
}

    </style>



@endpush

@section('content')


@php
    $orderDetails = $orders->first();
    $client = $orderDetails->client;

@endphp


<script>

    /**
     * Handle auto filter upon selecting a different
     * status
     */
    function handleStatusChange(element)
    {
        window.location.href ='/admin/order/{{ $orderDetails->order_transaction_id }}?change_order_status_to=' + element.value;
    }


</script>

    <div class="card">
<a class="navbar-brand" href="/">
 <img src="{{asset('img/logo-with-text.jpg')}}" height="140"> </a>

    <div class="card-body" id="js-receipt">
<div class="row gutters">
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="card">
				<div class="card-body p-0">
					<div class="invoice-container">
						<div class="invoice-header">
                            	<!-- Row start -->
							<div class="row gutters">
								<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
									<div class="custom-actions-btns mb-5">
										<a href="#" class="btn btn-secondary" onclick="window.print();">
											<i class="icon-printer"></i> Print
										</a>
									</div>
								</div>
							</div>
							<!-- Row end -->
							<!-- Row end -->
							<!-- Row start -->
                            @if ($client)
							<div class="row gutters">
								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">

                                        <a href="/admin/client/{{ $client->id }}/edit" class="invoice-logo">
                                            {{ $client->last_name }} {{ $client->first_name }}
                                        </a>

                                        <div class="p-2">

                                        </div>

                                        <select
                                            name="order_status"
                                            onchange="handleStatusChange(this)"
                                            id = 'js-select-status'
                                            class=" btn btn-{{ $orderDetails->color }}">

                                        @foreach (config('order.statuses') as $key => $status)
                                            <option value="{{ $key }}" {{ $key === $orderDetails->status ? 'SELECTED' : '' }}> {{ $status }}</option>
                                        @endforeach
                                    </select>

								</div>
								<div class="col-lg-6 col-md-6 col-sm-6">
									<address class="text-right">
										{{ $client->company_name }}, {{ $client->telephone }}.<br>
										{{ $client->district }}, {{ $client->province }} .<br>
									</address>
								</div>
							</div>
                            @endif
							<!-- Row end -->
							<!-- Row start -->

								<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
									<div class="invoice-details" >
										<address><b>
											UZIMA BORA<br>
											RCCM : GOM/RCCM /19_B_00079<br>
                                            Idnat:19_F4300_N65920Y<br>
                                            N" IMPORT:A2026344H<br>
                                            +243 977 980 199 , +243 995 346 277 </b>
										</address>
									</div>
								</div>
                      			<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
									<div class="invoice-details">
										<div class="invoice-num">
											<div>Facture - {{ $orderDetails->id }}</div>
											<div>{{ $orderDetails->created_at }}</div>
										</div>
									</div>
								</div>
							</div>
							<!-- Row end -->
						</div>
						<div class="invoice-body">
							<!-- Row start -->
							<div class="row gutters">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<div class="table-responsive">
										<table class="table custom-table m-0">
											<thead>
												<tr>
													<th>Designation</th>
													<th>Quantite</th>
													<th>Prix Unitaire</th>
													<th>Total </th>
												</tr>
											</thead>
											<tbody>
                                                @foreach ($orders as $orderItem)
                                                    <tr>
                                                        <td>
                                                            {{ $orderItem->item_name }}
                                                        </td>
                                                        <td>{{ $orderItem->quantity }}</td>
                                                        <td>{{ $orderItem->unit_price }}</td>
                                                        <td>{{ $orderItem->total_price }}</td>
                                                    </tr>
                                                @endforeach

												<tr>
													<td colspan="3">
														<h5 class="text-success"><strong>Grand Total</strong></h5>
													</td>
													<td>
														<h5 class="text-success"><strong>{{ number_format($orders->sum('total_price')) }}</strong> $</h5>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- Row end -->
						</div> <center>
						<div class="invoice-footer"><br><br>
							Merci pour  votre visite. <br>
                            Les Marchandises Vendues ne sont ni reprises ni echangees
						</div></center>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



@endsection
