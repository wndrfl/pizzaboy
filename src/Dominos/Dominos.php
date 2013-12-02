<?php
/**
 * Dominos API Wrapper
 * version 1.0.0
 * 
 * Copyright 2013 Wonderful Co.
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 **/
namespace Dominos;

use Dominos\Order\Order;
use Dominos\PaymentOption\CreditCard;
use Dominos\Product\Pizza;
use Dominos\Store\Store;
use Dominos\User\Address;
use Dominos\User\User;

class Dominos
{
	static
		$ENDPOINT_ROOT 	= 'https://order.dominos.com/',
		$ENDPOINTS		= array(
			'LOGIN' => 'power/login',
			'PLACE_ORDER' => 'power/place-order',
			'PRICE_ORDER' => 'power/price-order',
			'STORE_LOCATOR' => 'power/store-locator',
			'STORE_PROFILE' => 'power/store/%d/profile',
			'VALIDATE_ORDER' => 'power/validate-order'
		);

	// Pizza sizes
	const
		SIZE_10_GLU 				= 'P10IGFZA',
		SIZE_10_HAND 				= '10SCREEN',
		SIZE_12_HAND 				= '12SCREEN',
		SIZE_14_HAND 				= '14SCREEN',
		SIZE_12_PAN  				= 'P12IPAZA',
		SIZE_10_THIN 				= '10THIN',
		SIZE_12_THIN 				= '12THIN',
		SIZE_14_THIN 				= '14THIN',
		SIZE_14_BROOKLYN 			= 'PBKIREZA',
		SIZE_16_BROOKLYN 			= 'P16IBKZA';

	// Cheese portions
	const
		CHEESE_PORTION_LEFT			= '1/2',
		CHEESE_PORTION_WHOLE		= '1/1',
		CHEESE_PORTION_RIGHT		= '2/2';

	// Cheese weight
	const
		CHEESE_WEIGHT_LIGHT			= '0.5',
		CHEESE_WEIGHT_NORMAL		= '1',
		CHEESE_WEIGHT_EXTRA			= '1.5',
		CHEESE_WEIGHT_DOUBLE		= '2',
		CHEESE_WEIGHT_TRIPLE		= '3';

	// Toppings
	const
		TOPPING_PEPPERONI			= 'P',
		TOPPING_ITALIAN_SAUSAGE 	= 'S',
		TOPPING_BEEF 				= 'B',
		TOPPING_HAM 				= 'H',
		TOPPING_BACON 				= 'K',
		TOPPING_PHILLY_STEAK 		= 'Pm',
		TOPPING_GREEN_PEPPERS 		= 'G',
		TOPPING_BLACK_OLIVES 		= 'R',
		TOPPING_PINEAPPLES 			= 'N',
		TOPPING_MUSHROOMS 			= 'M',
		TOPPING_ONIONS 				= 'O',
		TOPPING_JALAPENOS 			= 'J',
		TOPPING_BANANA_PEPPERS 		= 'Z',
		TOPPING_CHEDDAR_CHEESE 		= 'E',
		TOPPING_PROVOLONE_CHEESE 	= 'Cp',
		TOPPING_GREEN_OLIVES 		= 'V',
		TOPPING_DICED_TOMATOES 		= 'Td';

	// Toppings portions
	const
		TOPPING_PORTION_LEFT		= '1/2',
		TOPPING_PORTION_WHOLE		= '1/1',
		TOPPING_PORTION_RIGHT		= '2/2';	

	// Toppings weight
	const
		TOPPING_WEIGHT_LIGHT			= '0.5',
		TOPPING_WEIGHT_NORMAL			= '1',
		TOPPING_WEIGHT_EXTRA			= '1.5';	

	// Sauces
	const
		SAUCE_TOMATO 				= 'X',
		SAUCE_MARINARA				= 'Xm',
		SAUCE_BBQ					= 'Bq',
		SAUCE_WHITE					= 'Xw';

	// Sauce weight
	const
		SAUCE_WEIGHT_LIGHT			= '0.5',
		SAUCE_WEIGHT_NORMAL			= '1',
		SAUCE_WEIGHT_EXTRA			= '1.5';	

	private function _buildEndpoint($alias/*, $arg, $arg... */)
	{	
		if(!isset(self::$ENDPOINTS[$alias])) {
			return false;
		}
		
		$endpoint = self::$ENDPOINT_ROOT.self::$ENDPOINTS[$alias];
		
		$args = func_get_args();
		array_shift($args);
		array_unshift($args,$endpoint);
		return call_user_func_array('sprintf',$args);
	}
	
	private function _cleanRequest($request)
	{
		return str_replace('\\/', '/', json_encode($request));
	}
	
	private function _createBasicOrderRequest(Order $order)
	{
		$request = array(
			'Order' => array(
				'Address' => array(),
				'Coupons' => array(),
				'CustomerID' => '',
				'Email' => '',
				'Extension' => '',
				'FirstName' => '',
				'LastName' => '',
				'LanguageCode' => 'en',
				'OrderChannel' => 'OLO',
				'OrderID' => $order->id(),
				'OrderMethod' => 'Web',
				'OrderTaker' => null,
				'Payments' => array(),
				'Phone' => '',
				'Products' => array(),
				'ServiceMethod' => 'Delivery',
				'SourceOrganizationURI' => 'order.dominos.com',
				'StoreID' => $order->store()->id(),
				'Tags' => (object)array(),
				'Version' => '1.0',
				'NoCombine' => true,
				'Partners' => (object)array()
			)
		);
		
		return $request;
	}
	
	private function _sendRequest($url,$method='POST',$params=array())
	{
		$ch = curl_init();
		
		if(is_array($params)) {
			$paramsStr = '';
			foreach($params as $key=>$value) { $paramsStr .= $key.'='.urlencode($value).'&';}
			rtrim($paramsStr,'&');
		}else{
			$paramsStr = $params;
		}
		
		$transformedMethod = strtoupper($method);
		if($transformedMethod == 'POST') {
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $paramsStr);

		}elseif($transformedMethod == 'GET') {
			$url .= '?'.$paramsStr;
		}
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

		$response = curl_exec($ch);

		$responseCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $headerSize);
		$responseBody = substr($response, $headerSize);

		return array(
			'CODE' => $responseCode,
			//'RAW' => $response,
			'RESPONSE' => $responseBody,
			'SUCCESS' => ($responseCode == 200) ? true : false,
		);
	}
	
	private function _setupOrderRequestAddress(Order $order,$request)
	{
		$request['Order']['Address'] = array(
			'Street' => $order->address()->street(),
			'City' => $order->address()->city(),
			'Region' => $order->address()->region(),
			'PostalCode' => $order->address()->postalCode(),
			'Type' => $order->address()->type()
		);
		
		return $request;
	}
	
	private function _setupOrderRequestPayments(Order $order,$request)
	{
		$request['Order']['Payments'] = array();
		
		if($order->paymentOption()) {
			$request['Order']['Payments'][] = array(
				'Type' => 'CreditCard',
				'Amount' => $order->amount(),
				'Number' => $order->paymentOption()->number(),
				'CardType' => $order->paymentOption()->type(),
				'Expiration' => $order->paymentOption()->expiration(),
				'SecurityCode' => $order->paymentOption()->securityCode(),
				'PostalCode' => $order->paymentOption()->postalCode()
			);
		}
		
		return $request;
	}
	
	private function _setupOrderRequestProducts(Order $order,$request)
	{
		foreach($order->products() as $product) {
			
			$options = array();
			
			// cheese
			if($product->hasCheese()) {
				$options['C'] = array(
					$product->cheesePortion() => $product->cheeseWeight()
				);
			}else{
				$options['C'] = '0';
			}
			
			// sauce
			if($product->hasSauce()) {
				if($product->sauceType() == 'X') {
					$options['X'] = array(
						'1/1' => $product->sauceWeight()
					);
				}else{
					$options['X'] = '0';
					$options[$product->sauceType()] = array(
						'1/1' => $product->sauceWeight()
					);
				}
			}else{
				$options['X'] = '0';
			}
			
			// toppings
			foreach($product->toppings() as $option) {
				$code = $option['code'];
				$portion = $option['portion'];
				$weight = $option['weight'];
				$options[$code] = array(
					$portion => $weight
				);
			}
			
			$request['Order']['Products'][] = array(
				'Code' => $product->type(),
				'Qty' => 1,
				'ID' => 1,
				'isNew' => true,
				'Options' => $options
			);
		}
		
		return $request;
	}
	
	private function _setupOrderRequestUser(Order $order,$request)
	{
		$request['Order']['Email'] = $order->user()->email();
		$request['Order']['FirstName'] = $order->user()->firstName();
		$request['Order']['LastName'] = $order->user()->lastName();
		$request['Order']['Phone'] = $order->user()->phone();

		return $request;
	}
	
	public function closestStore($street,$city,$state,$postal)
	{
		$stores = $this->findStores($street,$city,$state,$postal);
		
		if($stores) {
			return $stores[0];
		}
		
		return false;
	}
	
	public function createAddress()
	{
		$address = new Address();
		return $address;
	}
	
	public function createCreditCard()
	{
		$creditCard = new CreditCard();
		return $creditCard;
	}
	
	public function createPizza()
	{
		$pizza = new Pizza();
		return $pizza;
	}
	
	public function createOrder(array $pizzas=array())
	{
		$order = new Order();
		
		foreach($pizzas as $pizza) {
			$order->addPizza($pizza);
		}
		
		return $order;
	}
	
	public function createUser()
	{
		$user = new User();
		return $user;
	}
	
	public function findStores($street,$city,$state,$postal)
	{
		$endpoint = $this->_buildEndpoint('STORE_LOCATOR');
		$response = $this->_sendRequest($endpoint,'GET',array(
			'type' => 'Delivery',
			'c' => strtoupper($city.', '.$state.' '.$postal),
			's' => strtoupper($street)
		));
		
		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			
			$stores = array();
			foreach($responseBody['Stores'] as $storeData) {
				$store = new Store();
				$store->setAddress($storeData['AddressDescription']);
				$store->setAllowDeliveryOrders($storeData['AllowDeliveryOrders']);
				$store->setDeliveryIsOpen($storeData['ServiceIsOpen']['Delivery']);
				$store->setId($storeData['StoreID']);
				$store->setIsDeliveryStore($storeData['IsDeliveryStore']);
				$store->setIsOpen($storeData['IsOpen']);
				$store->setPhone($storeData['Phone']);
				
				$stores[] = $store;
			}
			
			return $stores;
			
		}else{
			
		}
	}
	
	public function getStore($id)
	{
		$endpoint = $this->_buildEndpoint('STORE_PROFILE',intval($id));
		$response = $this->_sendRequest($endpoint,'GET');
		
		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			
			$store = new Store();
			$store->setAddress($responseBody['AddressDescription']);
			$store->setAllowDeliveryOrders($responseBody['AllowDeliveryOrders']);
			$store->setId($responseBody['StoreID']);
			$store->setIsOpen($responseBody['IsOpen']);
			$store->setPhone($responseBody['Phone']);
			
			return $store;
			
		}
		
		return false;
	}
	
	public function login($user,$pass)
	{
		$endpoint = $this->_buildEndpoint('LOGIN');
		$response = $this->_sendRequest($endpoint,'POST',array(
			'u' => $user,
			'p' => $pass
		));
		
		if($response['SUCCESS']) {
			var_dump($response['SUCCESS']);
		}else{
			throw new InvalidLoginException();
		}
	}
	
	public function priceOrder(Order $order)
	{
		$request = $this->_createBasicOrderRequest($order);
		$request = $this->_setupOrderRequestAddress($order,$request);
		$request = $this->_setupOrderRequestProducts($order,$request);
		$request = $this->_cleanRequest($request);

		$endpoint = $this->_buildEndpoint('PRICE_ORDER');
		$response = $this->_sendRequest($endpoint,'POST',$request);

		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			$order->setAmount($responseBody['Order']['Amounts']['Payment']);
			return $order;
		}
		
		return false;
	}
	
	public function placeOrder(Order $order)
	{
		$request = $this->_createBasicOrderRequest($order);
		$request = $this->_setupOrderRequestAddress($order,$request);
		$request = $this->_setupOrderRequestPayments($order,$request);
		$request = $this->_setupOrderRequestProducts($order,$request);
		$request = $this->_setupOrderRequestUser($order,$request);
		$request = $this->_cleanRequest($request);
		
		$endpoint = $this->_buildEndpoint('PLACE_ORDER');

		$response = $this->_sendRequest($endpoint,'POST',$request);
		
		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			return ($responseBody['Status'] == 1) ? true : false;
		}
	}

	public function validateOrder(Order $order)
	{
		$request = $this->_createBasicOrderRequest($order);
		$request = $this->_setupOrderRequestAddress($order,$request);
		$request = $this->_setupOrderRequestPayments($order,$request);
		$request = $this->_setupOrderRequestProducts($order,$request);
		$request = $this->_setupOrderRequestUser($order,$request);
		$request = $this->_cleanRequest($request);

		$endpoint = $this->_buildEndpoint('VALIDATE_ORDER');
		$response = $this->_sendRequest($endpoint,'POST',$request);

		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			return ($responseBody['Status'] == 1) ? true : false;
		}
		
		return false;
	}
}