<?php
namespace Dominos\Product;

class Pizza
{	
	private
		$_cheesePortion,
		$_cheeseWeight,
		$_hasCheese,
		$_hasSauce,
		$_sauceType,
		$_toppings = array(),
		$_type;
		
	protected function _addOption($alias,$value)
	{
		$this->_options[$alias] = $value;
		return $this;
	}
		
	public function addTopping($topping,$portion,$weight)
	{
		$this->_toppings[$topping] = array(
			'code' => $topping,
			'portion' => $portion,
			'weight' => $weight
		);
		return $this;
	}
	
	public function cheesePortion()
	{
		return $this->_cheesePortion;
	}
	
	public function cheeseWeight()
	{
		return $this->_cheeseWeight;
	}
	
	public function hasCheese()
	{
		return $this->_hasCheese;
	}
	
	public function hasSauce()
	{
		return $this->_hasSauce;
	}
	
	public function options()
	{
		return $this->_options;
	}
	
	public function sauceType()
	{
		return $this->_sauceType;
	}
	
	public function sauceWeight()
	{
		return $this->_sauceWeight;
	}
	
	public function setCheesePortion($cheesePortion)
	{
		$this->setHasCheese(true);
		$this->_cheesePortion = $cheesePortion;
		return $this;
	}
	
	public function setCheeseWeight($cheeseWeight)
	{
		$this->setHasCheese(true);
		$this->_cheeseWeight = $cheeseWeight;
		return $this;
	}
	
	public function setHasCheese($hasCheese)
	{
		$this->_hasCheese = (bool) $hasCheese;
		return $this;
	}
	
	public function setHasSauce($hasSauce)
	{
		$this->_hasSauce = (bool) $hasSauce;
		return $this;
	}
	
	public function setSauceType($sauceType)
	{
		$this->setHasSauce(true);
		$this->_sauceType = $sauceType;
		return $this;
	}
	
	public function setSauceWeight($sauceWeight)
	{
		$this->setHasSauce(true);
		$this->_sauceWeight = $sauceWeight;
		return $this;
	}
	
	public function setToppings(array $toppings=array())
	{
		$this->_toppings = $toppings;
		return $this;
	}
	
	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}
	
	public function toppings()
	{
		return $this->_toppings;
	}
	
	public function type()
	{
		return $this->_type;
	}
}