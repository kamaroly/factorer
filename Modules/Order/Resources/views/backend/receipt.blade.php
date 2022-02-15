@extends('backend.layouts.app')

@section('content')

@php
    $orderDetails = $orders->first();
    $client = $orderDetails->client;

@endphp
    <div class="card">
        
    <div class="card-body">
<div class="row gutters">
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
			<div class="card">
				<div class="card-body p-0">
					<div class="invoice-container">
						<div class="invoice-header">
							<!-- Row end -->
							<!-- Row start -->
							<div class="row gutters">
								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
									<a href="/admin/client/{{ $client->id }}/edit" class="invoice-logo">
                                    {{ $client->last_name }} {{ $client->first_name }}
									</a>
								</div>
								<div class="col-lg-6 col-md-6 col-sm-6">
									<address class="text-right">
										{{ $client->company_name }}, {{ $client->telephone }}.<br>
										{{ $client->district }}, {{ $client->province }} .<br>
									</address>
								</div>
							</div>
							<!-- Row end -->
							<!-- Row start -->
							<div class="row gutters">
								<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
									<div class="invoice-details">
										<address>
											FACTORER<br>
											150-600 Church Street, Goma, DRC
										</address>
									</div>
								</div>
								<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
									<div class="invoice-details">
										<div class="invoice-num">
											<div>Invoice - {{ $orderDetails->id }}</div>
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
													<th>Items</th>
													<th>Quantity</th>
													<th>Unit Price</th>
													<th>Sub Total</th>
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
													<td>&nbsp;</td>
													<td colspan="2">
														<h5 class="text-success"><strong>Grand Total</strong></h5>
													</td>			
													<td>
														<h5 class="text-success"><strong>{{ number_format($orders->sum('total_price')) }}</strong></h5>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<!-- Row end -->
						</div>
						<div class="invoice-footer">
							Thank you for your Business.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@endsection