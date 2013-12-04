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
use Dominos\PaymentOption\SavedCreditCard;
use Dominos\Product\Pizza;
use Dominos\Store\Store;
use Dominos\User\Address;
use Dominos\User\User;

class Dominos
{
	// API endpoints
	static
		$ENDPOINT_ROOT 	= 'https://order.dominos.com/',
		$ENDPOINTS		= array(
			'CUSTOMER_CARD' => 'power/customer/%s/card',
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
	
	private function _sendRequest($url,$method='POST',$params=array(),array $authentication=array())
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
		
		if($authentication) {
			curl_setopt($ch, CURLOPT_USERPWD, $authentication['username'] . ":" . $authentication['password']);
		}

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
	
	/**
	 * Create a new Address object.
	 **/
	public function createAddress()
	{
		$address = new Address();
		return $address;
	}
	
	/**
	 * Create a new CreditCard object.
	 **/
	public function createCreditCard()
	{
		$creditCard = new CreditCard();
		return $creditCard;
	}
	
	/**
	 * Create a new Pizza object.
	 **/
	public function createPizza()
	{
		$pizza = new Pizza();
		return $pizza;
	}
	
	/**
	 * Create a new Order object.
	 **/
	public function createOrder(array $pizzas=array())
	{
		$order = new Order();
		
		foreach($pizzas as $pizza) {
			$order->addPizza($pizza);
		}
		
		return $order;
	}
	
	/**
	 * Create a new User object.
	 **/
	public function createUser()
	{
		$user = new User();
		return $user;
	}
	
	/**
	 * Finds the store branch closest to the address.
	 * 
	 * Runs a search of store branches around the provided
	 * address and returns the first on the list (assumed the closest)
	 * 
	 * @return Object Returns a Store object or false if none found
	 **/
	public function findClosestStore(Address $address)
	{
		$stores = $this->findStores($address);
		
		if($stores) {
			return $stores[0];
		}
		
		return false;
	}
	
	/**
	 * Find stores around a provided address.
	 * 
	 * Use Dominos API to search for stores within range
	 * of the provided address.
	 * 
	 * @return Array An array of Store objects
	 **/
	public function findStores(Address $address)
	{
		$endpoint = $this->_buildEndpoint('STORE_LOCATOR');
		$response = $this->_sendRequest($endpoint,'GET',array(
			'type' => 'Delivery',
			'c' => strtoupper($address->city().', '.$address->region().' '.$address->postalCode()),
			's' => strtoupper($address->street())
		));
		
		$stores = array();
		
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
		}
		
		return $stores;
	}
	
	public function getPrimaryCreditCard(User $user)
	{
		$endpoint = $this->_buildEndpoint('CUSTOMER_CARD',$user->id());
		$response = $this->_sendRequest($endpoint,'GET',array(),array(
			'username' => $user->email(),
			'password' => $user->password()
		));
		
		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			
			foreach($responseBody as $card) {
				if($card['isDefault'] == true) {
					$creditCard = new SavedCreditCard();
					$creditCard->setId($card['id']);
					$creditCard->setType($card['cardType']);
					$creditCard->setLastFour($card['lastFour']);
					$creditCard->setExpirationMonth($card['expirationMonth']);
					$creditCard->setExpirationYear($card['expirationYear']);
					$creditCard->setBillingZip($card['billingZip']);
					$creditCard->setIsDefault($card['isDefault']);
					$creditCard->setNickName($card['nickName']);
					
					return $creditCard;
				}
			}
			
		}
		
		return false;
	}
	
	/**
	 * Find a store by its id.
	 * 
	 * Use Dominos API to find a store by its
	 * Dominos store id.
	 * 
	 * @return Object A Store object
	 * @return Boolean
	 **/
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
	
	/**
	 * Log in to a Dominos account.
	 * 
	 * Authenticate a user by their Dominos
	 * credentials (user and password).
	 * 
	 * @todo Return and throw exceptions
	 **/
	public function login($email,$password)
	{
		$endpoint = $this->_buildEndpoint('LOGIN');
		$response = $this->_sendRequest($endpoint,'POST',array(
			'u' => $email,
			'p' => $password
		));
		
		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			$user = $this->createUser();
			$user->setEmail($email);
			$user->setPassword($password);
			$user->setId($responseBody['CustomerID']);
			
			return $user;
			
		}else{
			throw new InvalidLoginException();
		}
	}
	
	/**
	 * Calculate an estimated price for an order.
	 * 
	 * Send the order to Dominos to determine a price
	 * based on delivery address, branch, and products in 
	 * the order.
	 * 
	 * @todo Throw exception when unsuccessful.
	 * 
	 * @return Object An Order object (with the price added to it)
	 * @return Boolen False when unsuccessful
	 **/
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
	
	/**
	 * Submit an order for payment / delivery.
	 * 
	 * Send an order to Dominos, including credit card
	 * information, for purchase. If successful, the order will
	 * be placed, the credit card will be charge, and the delivery
	 * process will start.
	 * 
	 * @todo Throw exceptions when unsuccessful
	 * 
	 * @return Boolean True or false
	 **/
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
		
		return false;
	}

	/**
	 * Validate an order against the Dominos API.
	 * 
	 * Use the Dominos API to determine whether or not
	 * a current order request is valid.
	 * 
	 * @todo Throw exceptions when invalid
	 * 
	 * @return Boolean True or false
	 **/
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