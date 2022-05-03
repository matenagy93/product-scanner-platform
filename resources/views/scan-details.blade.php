@extends('frame')

@section('content')
	<h3>Scan Details</h3>
	<div class="row">
		<div class="col-lg-5 col-md-8 col-sm-10">
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tr>
						<td>Token:</td>
						<td class="text-right">{{ $details['token'] }}</td>
					</tr>
					<tr>
						<td>Scanning started at:</td>
						<td class="text-right">{{ $details['started_at'] }}</td>
					</tr>
					<tr>
						<td>Scanning ended at:</td>
						<td class="text-right">{{ $details['ended_at'] }}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	
	<h3 class="pt-4">Price calculation</h3>
	<div class="row">
		<div class="col-lg-5 col-md-8 col-sm-10">
			<div class="table-responsive">
				<table class="table table-striped table-hover">
					<tr>
						<td>Sum price of products (net):</td>
						<td class="text-right">{{ env('PRICES_CURRENCY_SIGNAL').number_format($details['sum_products_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
					</tr>
					<tr>
						<td>Sum value of applied discounts (net):</td>
						<td class="text-right">-{{ env('PRICES_CURRENCY_SIGNAL').number_format($details['sum_discounts_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
					</tr>
					<tr class="font-weight-bold">
						<td>Total (net):</td>
						<td class="text-right">{{ env('PRICES_CURRENCY_SIGNAL').number_format($details['total_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
					</tr>
					<tr>
						<td>Sales tax:</td>
						<td class="text-right">{{ number_format($details['sales_tax'] * 100, 0) }}%</td>
					</tr>
					<tr class="font-weight-bold text-primary">
						<td>Total (gross):</td>
						<td class="text-right">{{ env('PRICES_CURRENCY_SIGNAL').number_format($details['total_gross'], 4) }}</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	
	<h3 class="pt-4">Scanned products</h3>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th style="width: 200px;">EAN</th>
					<th>Product's name</th>
					<th class="text-right" style="width: 150px;">Unit price (net)</th>
					<th class="text-right" style="width: 150px;">Quantity</th>
					<th class="text-right" style="width: 150px;">Sum price (net)</th>
				</tr>
			</thead>
			<tbody>
				@if(count($details['products']) > 0)
					@foreach($details['products'] AS $product)
						<tr>
							<td>{{ $product['ean'] }}</td>
							<td>{{ $product['name'] }}</td>
							<td class="text-right">{{ env('PRICES_CURRENCY_SIGNAL').number_format($product['unit_price_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
							<td class="text-right">{{ $product['quantity'] }} pcs.</td>
							<td class="text-right">{{ env('PRICES_CURRENCY_SIGNAL').number_format($product['sum_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
	
	<h3 class="pt-4">Applied discounts</h3>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th style="width: 200px;">Code</th>
					<th>Discount's name</th>
					<th class="text-right" style="width: 150px;">Unit value (net)</th>
					<th class="text-right" style="width: 150px;">Quantity</th>
					<th class="text-right" style="width: 150px;">Sum value (net)</th>
				</tr>
			</thead>
			<tbody>
				@if(count($details['discounts']) > 0)
					@foreach($details['discounts'] AS $discount)
						<tr>
							<td>{{ $discount['code'] }}</td>
							<td>{{ $discount['name'] }}</td>
							<td class="text-right">-{{ env('PRICES_CURRENCY_SIGNAL').number_format($discount['unit_value_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
							<td class="text-right">{{ $discount['quantity'] }}x</td>
							<td class="text-right">-{{ env('PRICES_CURRENCY_SIGNAL').number_format($discount['sum_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
	
	<a href="{{ route('home') }}" class="btn btn-primary mt-5 mb-5">BACK TO HOME</a>
@endsection