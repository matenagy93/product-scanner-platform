<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Session;

class CashRegisterController extends Controller
{
    #Current scanning's token from session
	protected $scan_token;
	
	#Constructor: store scanning's token from session
	public function __construct()
	{
		if(request()->session()->exists('scan_token')) { $this->scan_token = request()->session()->get('scan_token'); }
	}
	
	#Page: Home
	public function home()
	{
		if($this->scan_token !== NULL) { return redirect()->route('scan'); }
		else { return view('home'); }
	}
	
	#Process: start scanning
	public function startScanning()
	{
		#Call API method
		$psc = new ProductScannerApiController();
		$response = $psc->startScanning();
		
		#Check if response is NULL
		if($response === NULL) { return redirect()->route('home', ['error' => 'start-failure']); }	
		#Check if response has API error
		elseif($psc->hasResponseApiError($response)) { return redirect()->route('home', ['error' => 'start-failure']); }
		#Everything is okay --> redirect to next step
		else 
		{ 
			$this->storeScanToken($response['token']);
			return redirect()->route('scan');
		}
	}
	
	#Page: scanning
	public function scan()
	{
		if($this->scan_token === NULL) { return redirect()->route('home'); }
		else
		{
			#Get product list
			$psc = new ProductScannerApiController();
			$products = $psc->getProductList();
			
			#Get scan details (to show earlier scanned products)
			$details = $psc->getScanningDetails($this->scan_token, 1, 0);
			
			#View
			return view('scan-process', ['products' => $products, 'details' => $details]);
		}
	}
	
	#Process: scan a product (ajax call!)
	public function scanProduct(Request $request)
	{
		#Return base array
		$return = [
			'type' => 'error',
			'msg' => NULL,
			'scanResponse' => NULL,
			'datasForTableRow' => NULL,
		];
		
		#Check required parameters
		$ean = $request->input('product_ean');
		$quantity = $request->input('product_quantity');
		
		if(empty($ean)) { $return['msg'] = 'ean-empty'; }
		elseif(empty($quantity) OR $quantity < 1) { $return['msg'] = 'quantity-empty'; }
		#Required parameters are okay
		else
		{
			#Call API method
			$psc = new ProductScannerApiController();
			$response = $psc->scanProduct($this->scan_token, $ean, $quantity);
			
			#Check if response is NULL
			if($response === NULL) { $return['msg'] = 'scan-response-null'; }	
			#Check if response has API error
			elseif($psc->hasResponseApiError($response)) { $return['msg'] = 'scan-response-error'; }
			#Everything is okay --> redirect to summary page (details)
			else 
			{ 
				$return['type'] = 'success';
				$return['scanResponse'] = $response;
				$return['datasForTableRow'] = [
					'ean' => $response['ean'],
					'name' => $response['name'],
					'unitPrice' => env('PRICES_CURRENCY_SIGNAL').number_format($response['unit_price_net'], env('PRICES_CURRENCY_DECIMALS')),
					'quantity' => $quantity.' pcs.',
				];
			}
		}
		
		#Return JSON
		return response()->json($return);
	}
	
	#Process: start scanning
	public function endScanning()
	{
		#Call API method
		$psc = new ProductScannerApiController();
		$response = $psc->endScanning($this->scan_token);
		
		#Check if response is NULL
		if($response === NULL) { return redirect()->route('scan', ['error' => 'end-failure']); }	
		#Check if response has API error
		elseif($psc->hasResponseApiError($response)) { return redirect()->route('scan', ['error' => 'end-failure']); }
		#Everything is okay --> redirect to summary page (details)
		else 
		{ 
			$scan_token = $this->scan_token;
			$this->forgetScanToken();
			return redirect()->route('scan-details', ['scan_token' => $scan_token]);
		}
	}
	
	#Page: details of scanning
	public function scanDetails($scan_token)
	{
		if($this->scan_token !== NULL) { return redirect()->route('scan'); }
		else
		{
			#Get details of given scanning
			$psc = new ProductScannerApiController();
			$details = $psc->getScanningDetails($scan_token);
			
			#Check if details is NULL
			if($details === NULL) { return redirect()->route('home', ['error' => 'scan-details']); }	
			#Check if details has API error
			elseif($psc->hasResponseApiError($details)) { return redirect()->route('home', ['error' => 'scan-details']); }
			#Everything is okay
			else { return view('scan-details', ['details' => $details]); }
		}
	}
	
	#Store scanning's token into session and property
	public function storeScanToken($scan_token)
	{
		request()->session()->put('scan_token', $scan_token); 
		$this->scan_token = $scan_token;
	}
	
	#Forget scanning's token
	public function forgetScanToken()
	{
		if(request()->session()->exists('scan_token')) { request()->session()->forget('scan_token'); }
		$this->scan_token = NULL;
	}
}
