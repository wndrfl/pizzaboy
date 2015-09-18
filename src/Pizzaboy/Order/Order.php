<?php
namespace Pizzaboy\Order;

use Pizzaboy\Pizzaboy as Pizzaboy;
use Pizzaboy\ParentSavableInterface;
use Pizzaboy\PaymentOption\PaymentOption;
use Pizzaboy\Product\Pizza as Pizza;
use Pizzaboy\Store\Coupon;
use Pizzaboy\Store\Store as Store;
use Pizzaboy\Order\Address as Address;
use Pizzaboy\User\User;

class Order implements ParentSavableInterface
{
	private	
		$_address,
		$_amount,
		$_coupons = array(),
		$_pizzaboy,
		$_id,
		$_paymentOption,
		$_products = array(),
		$_store,
		$_stores = false,
		$_user;
	
	public function __construct(Pizzaboy $pizzaboy)
	{
		$this->_pizzaboy = $pizzaboy;
		
		$this->_address = new Address($this);
		$this->_store = new Store($this);
	}

	public function addCoupon(Coupon $coupon)
	{
		$this->_coupons[] = $coupon;
		$this->resetPrice();
		return $this;
	}
		
	public function addPizza(Pizza $pizza)
	{
		$this->_products[] = $pizza;
		$this->resetPrice();
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
	
	public function coupons()
	{
		return $this->_coupons;
	}
	
	public function findStores()
	{
		$this->stores = $this->_pizzaboy->findStores($this->address());
		$this->setStore($this->stores[0]);
		return $this;
	}
	
	public function getPrice()
	{
		$this->_pizzaboy->priceOrder($this);
		return $this;
	}
	
	public function id()
	{
		return $this->_id;
	}
	
	public function newPizza($size='medium', $type='hand-tossed')
	{
		return new Pizza($size,$type,$this);
	}
	
	public function paymentOption()
	{
		return $this->_paymentOption;
	}
	
	public function phone()
	{
		return $this->_phone;
	}
	
	public function place()
	{
		if(!$this->amount()) {
			$this->getPrice();
		}
		
		$this->usePrimaryCreditCard();
		
		return $this->_pizzaboy->placeOrder($this);
	}
	
	public function productQuantity()
	{
		return count($this->_products);
	}
	
	public function products()
	{
		return $this->_products;
	}
	
	public function resetPrice()
	{
		$this->_amount = null;
		return $this;
	}
	
	public function saveChild($child)
	{
		if($child instanceof Address) {
			$this->setAddress($child);
			$this->resetPrice();
			
		} elseif($child instanceof Pizza) {
			$this->addPizza($child);
			$this->resetPrice();
			
		}
		
		return $this;
	}
	
	public function setAddress(Address $address)
	{
		$this->_address = $address;
		$this->resetPrice();
		$this->findStores();
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
		$this->resetPrice();
		return $this;
	}
	
	public function setStore(Store $store)
	{
		$this->_store = $store;
		$this->resetPrice();
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
	
	public function stores()
	{
		if(!$this->_stores) {
			$this->findStores();
		}
		
		return $this->_stores;
	}
	
	public function usePrimaryCreditCard()
	{
		if($card = $this->_pizzaboy->getPrimaryCreditCard($this->user())) {
			$this->setPaymentOption($card);
			return $this;	
		}
		// TODO EXCEPTION
	}
	
	public function user()
	{
		return $this->_user;
	}
}