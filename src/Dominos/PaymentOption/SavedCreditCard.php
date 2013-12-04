<?php
namespace Dominos\PaymentOption;

use Dominos\PaymentOption\PaymentOption;

class SavedCreditCard extends PaymentOption
{
	private
		$_billingZip,
		$_expirationMonth,
		$_expirationYear,
		$_id,
		$_isDefault,
		$_lastFour,
		$_nickName,
		$_type;
	
	public function billingZip()
	{
		return $this->_billingZip;
	}
			
	public function expirationMonth()
	{
		return $this->_expirationMonth;
	}
	
	public function expirationYear()
	{
		return $this->_expirationYear;
	}
	
	public function id()
	{
		return $this->_id;
	}
	
	public function isDefault()
	{
		return $this->_isDefault;
	}
	
	public function lastFour()
	{
		return $this->_lastFour;
	}
	
	public function nickName()
	{
		return $this->_nickName;
	}
	
	public function setBillingZip($billingZip)
	{
		$this->_billingZip = $billingZip;
		return $this;
	}
	
	public function setExpirationMonth($expirationMonth)
	{
		$this->_expirationMonth = $expirationMonth;
		return $this;
	}
	
	public function setExpirationYear($expirationYear)
	{
		$this->_expirationYear = $expirationYear;
		return $this;
	}
	
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	
	public function setIsDefault($isDefault)
	{
		$this->_isDefault = $isDefault;
		return $this;
	}
	
	public function setLastFour($lastFour)
	{
		$this->_lastFour = $lastFour;
		return $this;
	}
	
	public function setNickName($nickName)
	{
		$this->_nickName = $nickName;
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