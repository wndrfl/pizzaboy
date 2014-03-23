<?php
/**
 * Dominos API Wrapper
 * version 1.0.0
 * 
 * Copyright 2013 Wonderful Co.
 * Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
 **/
namespace Dominos;

use Dominos\Lang\Lang as Lang;
use Dominos\Order\Address;
use Dominos\Order\Order;
use Dominos\PaymentOption\CreditCard;
use Dominos\PaymentOption\SavedCreditCard;
use Dominos\Product\Pizza;
use Dominos\Store\Coupon;
use Dominos\Store\Store;
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
			'STORE_MENU' => 'power/store/%s/menu',
			'STORE_PROFILE' => 'power/store/%d/profile',
			'VALIDATE_ORDER' => 'power/validate-order'
		);
		
	private
		$_testMode = false;	

	public function __construct($testMode=false)
	{
		$this->_testMode = (bool) $testMode;
	}
	
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
		/*
		$coupons = array();
		foreach($order->coupons() as $coupon) {
			$coupons[] = array(
				'Code' => $coupon->code(),
				'ID' => 1,
				'Qty' => 1
			);
		}
		*/
		
		$request = array(
			'Order' => array(
				'Address' => array(),
				//'Coupons' => $coupons,
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
	
	private function _sendRequest($url,$method='POST',$params=array(),array $authentication=array(),array $headers=array())
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
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, 'https://order.dominos.com/en/pages/order/payment.jsp');
		
		if($headers) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		if($authentication) {
			curl_setopt($ch, CURLOPT_USERPWD, $authentication['username'] . ":" . $authentication['password']);
		}

		$response = curl_exec($ch);

		$responseCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $headerSize);
		$responseBody = substr($response, $headerSize);
		$headerInfo = curl_getinfo($ch);

		return array(
			'CODE' => $responseCode,
			//'RAW' => $response,
			'RESPONSE' => $responseBody,
			'SUCCESS' => ($responseCode == 200) ? true : false,
			'HEADER' => $headerInfo
		);
	}
	
	private function _setupOrderRequestAddress(Order $order,$request)
	{
		$request['Order']['Address'] = array(
			'Street' => strtoupper($order->address()->street()),
			'City' => strtoupper($order->address()->city()),
			'Region' => $order->address()->region(),
			'PostalCode' => '90036-3402',
			//'PostalCode' => $order->address()->postalCode(),
			'Type' => 'Apartment'
		);
		
		return $request;
	}
	
	private function _setupOrderRequestPayments(Order $order,$request)
	{
		$request['Order']['Payments'] = array();
		
		if($order->paymentOption()) {
			
			$paymentOption = $order->paymentOption();
			
			if($paymentOption instanceof SavedCreditCard) {
				$request['Order']['Payments'][] = array(
					'Type' => 'CreditCard',
					'Amount' => $order->amount(),
					'CardID' => $paymentOption->id()
				);
				
			}elseif($paymentOption instanceof CreditCard) {
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
		$request['Order']['CustomerID'] = $order->user()->id();
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
		return new Pizza;
	}
	
	/**
	 * Create a new User object.
	 **/
	public function createUser()
	{
		return new User($this);
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
	
	public function getStoreCoupons($id)
	{
		$menu = $this->getStoreMenu($id);
		
		$coupons = array();
		foreach($menu['Coupons']['Data'] as $data) {
			$coupon = new Coupon();

			$coupon->setSortSeq($data[0]);
			$coupon->setCode($data[1]);
			$coupon->setGroupCodes($data[2]);
			$coupon->setTags($data[3]);
			$coupon->setName($data[4]);
			$coupon->setDescription($data[5]);
			$coupon->setPrice($data[6]);
			$coupon->setImageCode($data[7]);
			$coupon->setLargeSizeImageUrl($data[8]);
			$coupon->setThumbNailSizeImageUrl($data[9]);
			
			$coupons[] = $coupon;
		}
		
		return $coupons;
	}
	
	public function getStoreMenu($id)
	{
		$endpoint = $this->_buildEndpoint('STORE_MENU',intval($id));
		$response = $this->_sendRequest($endpoint,'GET');
		
		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			
			return $responseBody;
			
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
			$user->setFirstName($responseBody['FirstName']);
			$user->setId($responseBody['CustomerID']);
			$user->setLastName($responseBody['LastName']);
			$user->setPassword($password);
			$user->setPhone($responseBody['Phone']);
			
			return $user;
			
		}else{
			throw new InvalidLoginException();
		}
	}
	
	/**
	 * Create a new Order object.
	 **/
	public function newOrder()
	{
		return new Order($this);
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

		if($this->_testMode === false) {
			$response = $this->_sendRequest($endpoint,'POST',$request,array(
				'username' => $order->user()->email(),
				'password' => $order->user()->password()
			),array(
				"Accept: application/vnd.dominos.customer.card+json;version=1.0",
				"Content-Type: application/json"
			));
		
		}else{
			return true;
		}
		
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
		$response = $this->_sendRequest($endpoint,'POST',$request,array(
			'username' => $order->user()->email(),
			'password' => $order->user()->password()
		),array(
			"Accept: application/vnd.dominos.customer.card+json;version=1.0",
			"Content-Type: application/json"
		));
		
		if($response['SUCCESS']) {
			$responseBody = json_decode($response['RESPONSE'],true);
			$order->setId($responseBody['Order']['OrderID']);
			
			return ($responseBody['Status'] == 1) ? true : false;
		}
		
		return false;
	}
}