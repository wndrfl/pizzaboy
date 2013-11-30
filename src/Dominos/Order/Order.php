<?php
namespace Dominos\Order;

use Dominos\Product\Pizza;
use Dominos\Store\Store;
use Dominos\User\Address;
use Dominos\User\User;
/**
 * 	
use PizzaParty\Application\Entity\Order\OrderPreset;
use PizzaParty\Application\Entity\PaymentOption\PaymentOption;
**/

class Order
{
	private	$_address;
	
	private $_amount;
	
	private $_id;
	
	private $_paymentOption;
	
	private $_products = array();
	
	private $_store;
	
	private $_user;
	
	public function addPizza(Pizza $pizza)
	{
		$this->_products[] = $pizza;
		return $this;
	}
	
	public function address()
	{
		return $this->_address;
	}
	
	public function amount()
	{
		return $this->_amount;
	}
	
	public function id()
	{
		return $this->_id;
	}
	
	public function paymentOption()
	{
		return $this->_paymentOption;
	}
	
	public function phone()
	{
		return $this->_phone;
	}
	
	public function productQuantity()
	{
		return count($this->_products);
	}
	
	public function products()
	{
		return $this->_products;
	}
	
	public function setAddress(Address $address)
	{
		$this->_address = $address;
		return $this;
	}
	
	public function setAmount($amount)
	{
		$this->_amount = $amount;
		return $this;
	}
	
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	
	public function setPaymentOption(PaymentOption $paymentOption)
	{
		$this->_paymentOption = $paymentOption;
		return $this;
	}
	
	public function setPhone($phone)
	{
		$this->_phone = $phone;
		return $this;
	}
	
	public function setProducts(array $products=array())
	{
		$this->_products = $products;
		return $this;
	}
	
	public function setStore(Store $store)
	{
		$this->_store = $store;
		return $this;
	}
	
	public function setUser(User $user)
	{
		$this->_user = $user;
		return $this;
	}
	
	public function store()
	{
		return $this->_store;
	}
	
	public function user()
	{
		return $this->_user;
	}
}