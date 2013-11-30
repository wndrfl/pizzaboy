<?php
namespace Dominos\User;

use Dominos\User\Address;

class User
{	

	private 
		$_address,
		$_email,
		$_firstName,
		$_lastActive,
		$_lastName,
		$_phone;
	
	public function address()
	{
		return $this->_address;
	}
	
	public function email()
	{
		return $this->_email;
	}
	
	public function firstName()
	{
		return $this->_firstName;
	}
	
	public function lastName()
	{
		return $this->_lastName;
	}
	
	public function phone()
	{
		return $this->_phone;
	}
	
	public function setAddress(Address $address)
	{
		$this->_address = $address;
		return $this;
	}
	
	public function setEmail($email)
	{
		$this->_email = $email;
		return $this;
	}
	
	public function setFirstName($firstName)
	{
		$this->_firstName = $firstName;
		return $this;
	}
	
	public function setLastName($lastName)
	{
		$this->_lastName = $lastName;
		return $this;
	}

	public function setPhone($phone)
	{
		$this->_phone = $phone;
		return $this;
	}
}