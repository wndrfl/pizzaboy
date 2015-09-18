<?php
namespace Pizzaboy\PaymentOption;

use Pizzaboy\PaymentOption\PaymentOption;

class CreditCard extends PaymentOption
{
	private
		$_expiration,
		$_number,
		$_postalCode,
		$_securityCode,
		$_type;
		
	public function expiration()
	{
		return $this->_expiration;
	}
	
	public function number()
	{
		return $this->_number;
	}
	
	public function postalCode()
	{
		return $this->_postalCode;
	}
	
	public function securityCode()
	{
		return $this->_securityCode;
	}
	
	public function setExpiration($expiration)
	{
		$this->_expiration = $expiration;
		return $this;
	}
	
	public function setNumber($number)
	{
		$this->_number = $number;
		return $this;
	}
	
	public function setPostalCode($postalCode)
	{
		$this->_postalCode = $postalCode;
		return $this;
	}
	
	public function setSecurityCode($securityCode)
	{
		$this->_securityCode = $securityCode;
		return $this;
	}
	
	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}
	
	public function type()
	{
		return $this->_type;
	}
}