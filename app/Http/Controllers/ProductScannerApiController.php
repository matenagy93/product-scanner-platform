<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductScannerApiController extends Controller
{
    #API datas
	private $api_link;
	private $api_key;
	
	#Constructor: store api properties
	public function __construct()
	{
		$this->api_link = env('PRODUCT_SCANNER_API_LINK');
		$this->api_key = env('PRODUCT_SCANNER_API_KEY');
	}
	
	#Get product list
	public function getProductList($order_by = NULL, $price_min = NULL, $price_max = NULL)
	{		
		$variables = [];
		if(!empty($order_by)) { $variables['order_by'] = $order_by; }
		if(!empty($price_min)) { $variables['price_min'] = $price_min; }
		if(!empty($price_max)) { $variables['price_max'] = $price_max; }
		
		return $this->curlGet('products', $variables);
	}
	
	#Start scanning
	public function startScanning()
	{		
		return $this->curlPost('scan-start');
	}
	
	#Scan product
	public function scanProduct($scan_token, $product_ean, $product_quantity = 1)
	{		
		$variables = [
			'scan_token' => $scan_token,
			'product_ean' => $product_ean,
		];
		if($product_quantity > 0) { $variables['product_quantity'] = $product_quantity; }
		
		return $this->curlPost('scan-product', $variables);
	}
	
	#End scanning
	public function endScanning($scan_token)
	{		
		return $this->curlPost('scan-end', ['scan_token' => $scan_token]);
	}
	
	#Get scanning details
	public function getScanningDetails($scan_token, $products = 1, $discounts = 1)
	{		
		$variables = [
			'scan_token' => $scan_token,
		];
		if($products) { $variables['products'] = 1; }
		if($discounts) { $variables['discounts'] = 1; }
		
		return $this->curlGet('scan-details', $variables);
	}
	
	#HTTP GET METHOD
	private function curlGet($url, $variables = [])
	{
		$variables['api_key'] = $this->api_key;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_link.$url."?".http_build_query($variables));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($response, true);
	}
	
	#HTTP POST METHOD
	private function curlPost($url, $variables = [])
	{
		$variables['api_key'] = $this->api_key;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_link.$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($variables));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);
		
		return json_decode($response, true);
	}
	
	#Check response's value for API error
	public function hasResponseApiError($response)
	{
		$return = false;
		if($response !== NULL)
		{
			if(is_array($response) AND count($response) > 0 AND array_keys($response)[0] == 'error') { $return = true; }
		}
		
		return $return;
	}
}
