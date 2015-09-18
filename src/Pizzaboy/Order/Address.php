<?php
namespace Pizzaboy\Order;

use Pizzaboy\ChildSavableInterface;
use Pizzaboy\ParentSavableInterface;

class Address implements ChildSavableInterface
{
	private
		$_city,
		$_id,
		$_order,
		$_postalCode,
		$_region,
		$_street,
		$_type,
		$_userId;
		
	private
		$_parent;
	
	public function __construct(ParentSavableInterface $parent=null)
	{
		if($parent) {
			$this->_parent = $parent;
		}
	}
	
	public function city()
	{
		return $this->_city;
	}
	
	public function id()
	{
		return $this->_id;
	}
		
	public function postalCode()
	{
		return $this->_postalCode;
	}
	
	public function region()
	{
		return $this->_region;
	}
	
	public function save()
	{
		if($this->_parent) {
			$this->_parent->saveChild($this);
		}
		return $this;
	}
	
	public function setCity($city)
	{
		$this->_city = $city;
		return $this;
	}
	
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	
	public function setPostalCode($postalCode)
	{
		$this->_postalCode = $postalCode;
		return $this;
	}
	
	public function setRegion($region)
	{
		$this->_region = $region;
		return $this;
	}
	
	public function setStreet($street)
	{
		$this->_street = $street;
		return $this;
	}
	
	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}
	
	public function setUserId($userId)
	{
		$this->_userId = $userId;
		return $this;
	}

	public function street()
	{
		return $this->_street;
	}
	
	public function type()
	{
		return $this->_type;
	}
	
	public function userId()
	{
		return $this->_userId;
	}
}