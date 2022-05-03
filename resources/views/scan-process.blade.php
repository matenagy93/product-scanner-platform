@extends('frame')

@section('content')
	<h3>Choose the product you want to "scan":</h3>
	<form method="post" onsubmit="return false;" id="form-scan-product">
		<div class="form-row">
			<div class="col-md-6 col-12 pt-2">
				<select name="product_ean" class="form-control" id="form-scan-input-ean">
					<option value="">(Please choose a product!)</option>
					@if($products !== NULL)
						@foreach($products AS $product)
							<option value="{{ $product['ean'] }}">{{ $product['name'] }} [{{ env('PRICES_CURRENCY_SIGNAL').number_format($product['unit_price_net'], env('PRICES_CURRENCY_DECIMALS')) }}]</option>
						@endforeach
					@endif
				</select>
			</div>
			<div class="col-md-3 col-12 pt-2">
				<div class="input-group">
					<div class="input-group-prepend"><div class="input-group-text">Quantity:</div></div>
					<input type="number" name="product_quantity" id="form-scan-input-quantity" class="form-control" min="1" value="1">
				</div>	
			</div>
			<div class="col-md-3 col-12 pt-2">
				<button type="button" class="btn btn-danger" onclick="scanProduct()">Scan!</button>
			</div>
		</div>
	</form>
	
	<h3 class="pt-4">Scanned products</h3>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th style="width: 200px;">EAN</th>
					<th>Product's name</th>
					<th class="text-right" style="width: 150px;">Unit price (net)</th>
					<th class="text-right" style="width: 150px;">Quantity</th>
				</tr>
			</thead>
			<tbody id="products-table">
				@if($details !== NULL AND is_array($details) AND count($details['products']) > 0)
					@foreach($details['products'] AS $product)
						<tr>
							<td>{{ $product['ean'] }}</td>
							<td>{{ $product['name'] }}</td>
							<td class="text-right">{{ env('PRICES_CURRENCY_SIGNAL').number_format($product['unit_price_net'], env('PRICES_CURRENCY_DECIMALS')) }}</td>
							<td class="text-right">{{ $product['quantity'] }} pcs.</td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
	</div>
	
	<h3 class="pt-4">End scanning</h3>
	<p>Click on the button below to finish the current scanning and calculate the prices!</p>
	<form method="post" action="{{ route('scan-end') }}">
		@csrf
		<button type="submit" class="btn btn-lg btn-success">I'M DONE</button>
	</form>
	
	<script>
		// Error messages
		var eanEmptyError = 'You need to choose a product first!';
		var quantityEmptyError = 'Quantity must be greater than 1!';
		var scanResponseNullOrError = 'Something went wrong with the scan. Please try again!';
		var unexpectedError = 'Unexpected error! Please try again!';
		
		// Scanning process
		function scanProduct()
		{
			// Store EAN and quantity input values
			ean = $("#form-scan-input-ean").val();
			quantity = $("#form-scan-input-quantity").val();
			
			// Check for error
			if(ean == "") { alert(eanEmptyError); }
			else if(quantity < 1) { alert(quantityEmptyError); }
			else
			{
				// Send datas to PHP processing
				$.ajax({
					type: "POST",
					url: "{{ route('scan-product') }}",
					headers: {"X-CSRF-TOKEN": $("[name='_token']").val()},
					data: $("#form-scan-product").serialize(),
					dataType: "json",
					// AJAX call was succesful
					success: function(response) {
						// Although the AJAX call was succesful, the response is an error message
						if(response.type == 'error')
						{
							// console.log(response);
							
							if(response.msg == 'ean-empty') { errorMsg = eanEmptyError; }
							else if(response.msg == 'quantity-empty') { errorMsg = quantityEmptyError; }
							else if(response.msg == 'scan-response-null') { errorMsg = scanResponseNullOrError; }
							else if(response.msg == 'scan-response-error') { errorMsg = scanResponseNullOrError; }
							else { errorMsg = unexpectedError; }
							
							alert(errorMsg);
						}
						// Everything is okay --> append HTML table row
						else
						{
							// console.log(response.datasForTableRow);
							
							htmlContentToAppend = '<tr>';
								htmlContentToAppend += '<td>' + response.datasForTableRow.ean + '</td>';
								htmlContentToAppend += '<td>' + response.datasForTableRow.name + '</td>';
								htmlContentToAppend += '<td class="text-right">' + response.datasForTableRow.unitPrice + '</td>';
								htmlContentToAppend += '<td class="text-right">' + response.datasForTableRow.quantity + '</td>';
							htmlContentToAppend += '</tr>';
							
							$(htmlContentToAppend).appendTo($("#products-table"));
						}						
					},
					// AJAX call ended with error
					error: function(request, status, error) {
						// console.log(request.responseText);
						alert(unexpectedError);
					},
				});
			}
		}
	</script>
@endsection