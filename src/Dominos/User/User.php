<?php
namespace Dominos\User;

use Dominos\Dominos;
use Dominos\User\Address;

class User
{	

	private 
		$_address,
		$_email,
		$_firstName,
		$_id,
		$_lastActive,
		$_lastName,
		$_password,
		$_phone;
	
	private
		$_dominos;
	
	public function __construct(Dominos $dominos)
	{
		$this->_dominos = $dominos;
	}
	
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
	
	public function id()
	{
		return $this->_id;
	}
	
	public function lastName()
	{
		return $this->_lastName;
	}
	
	public function newOrder()
	{
		$order = $this->_dominos->newOrder();
		$order->setUser($this);
		return $order;
	}
	
	public function password()
	{
		return $this->_password;
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
	
	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}
	
	public function setLastName($lastName)
	{
		$this->_lastName = $lastName;
		return $this;
	}
	
	public function setPassword($password)
	{
		$this->_password = $password;
		return $this;
	}

	public function setPhone($phone)
	{
		$this->_phone = $phone;
		return $this;
	}
}