<?php
namespace Pizzaboy\Store;

use Pizzaboy\ChildSavableInterface;
use Pizzaboy\ParentSavableInterface;

class Store implements ChildSavableInterface
{
	private
		$_address,
		$_allowDeliveryOrders,
		$_deliveryIsOpen,
		$_id,
		$_isDeliveryStore,
		$_isOpen,
		$_phone;
	
	private
		$_parent;
	
	public function __construct(ParentSavableInterface $parent=null)
	{
		if($parent) {
			$this->_parent = $parent;
		}
	}
	
	public function address()
	{
		return $this->_address;
	}
	
	public function allowDeliveryOrders()
	{
		return $this->_allowDeliveryOrders;
	}
	
	public function deliveryIsOpen()
	{
		return $this->_deliveryIsOpen;
	}
	
	public function id()
	{
		return $this->_id;
	}
	
	public function isDeliveryStore()
	{
		return $this->_isDeliveryStore;
	}
	
	public function isOpen()
	{
		return $this->_isOpen;
	}
	
	public function phone()
	{
		return $this->_phone;
	}
	
	public function save()
	{
		if($this->_parent) {
			$this->_parent->saveChild($this);
		}
		
		return $this;
	}
	
	public function setAddress($address)
	{
		$this->_address = $address;
		return $this;
	}	
	
	public function setAllowDeliveryOrders($bool)
	{
		$this->_allowDeliveryOrders = (bool)$bool;
		return $this;
	}
	
	public function setDeliveryIsOpen($bool)
	{
		$this->_deliveryIsOpen = (bool)$bool;
		return $this;
	}
	
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	
	public function setIsDeliveryStore($bool)
	{
		$this->_isDeliveryStore = (bool)$bool;
		return $this;
	}
	
	public function setIsOpen($bool)
	{
		$this->_isOpen = (bool)$bool;
		return $this;
	}
	
	public function setPhone($phone)
	{
		$this->_phone = $phone;
		return $this;
	}
}