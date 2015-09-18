<?php
namespace Pizzaboy\Product;

use Pizzaboy\ChildSavableInterface;
use Pizzaboy\ParentSavableInterface;
use Pizzaboy\Lang\Lang as Lang;
use Pizzaboy\Order\Order as Order;

class Pizza implements ChildSavableInterface
{	
	private
		$_cheesePortion,
		$_cheeseWeight,
		$_hasCheese,
		$_hasSauce,
		$_sauceType,
		$_toppings = array(),
		$_type;
		
	private
		$_parent;
	
	public function __construct($size='medium',$type='hand-tossed',ParentSavableInterface $parent=null)
	{
		$this->setType($size,$type);
		
		if($parent) {
			$this->_parent = $parent;
		}
	}
			
	protected function _addOption($alias,$value)
	{
		$this->_options[$alias] = $value;
		return $this;
	}

	public function addBacon($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::bacon(),$portion,$weight);
	}

	public function addBananaPeppers($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::bananaPeppers(),$portion,$weight);
	}
		
	public function addBeef($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::beef(),$portion,$weight);
	}
	
	public function addBlackOlives($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::blackOlives(),$portion,$weight);
	}
	
	public function addCheddarCheese($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::cheddarCheese(),$portion,$weight);
	}
	
	public function addDicedTomatoes($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::dicedTomatoes(),$portion,$weight);
	}

	public function addGreenOlives($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::greenOlives(),$portion,$weight);
	}
	
	public function addGreenPeppers($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::greenPeppers(),$portion,$weight);
	}
	
	public function addHam($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::ham(),$portion,$weight);
	}
	
	public function addItalianSausage($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::italianSausage(),$portion,$weight);
	}
	
	public function addJalapenos($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::jalapenos(),$portion,$weight);
	}
	
	public function addMushrooms($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::mushrooms(),$portion,$weight);
	}
	
	public function addOnions($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::onions(),$portion,$weight);
	}
		
	public function addPepperoni($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::pepperoni(),$portion,$weight);
	}
	
	public function addPhillySteak($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::phillySteak(),$portion,$weight);
	}
	
	public function addPineapple($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::pineapples(),$portion,$weight);
	}
	
	public function addProvolineCheese($portion='whole',$weight='normal')
	{
		return $this->addTopping(Lang::provoloneCheese(),$portion,$weight);
	}
		
	public function addTopping($topping,$portion='whole',$weight='normal')
	{
		$this->_toppings[$topping] = array(
			'code' => $topping,
			'portion' => Lang::portion($portion),
			'weight' => Lang::weight($weight)
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
	
	public function save()
	{
		return $this->_parent->saveChild($this);
	}
	
	public function setCheese($portion, $weight='normal')
	{
		if(!$portion) {
			$this->setHasCheese(false);
			return $this;
		}
		
		$this->setCheesePortion($portion);
		$this->setCheeseWeight($weight);
		return $this;
	}
	
	public function setCheesePortion($portion)
	{
		$this->setHasCheese(true);
		$this->_cheesePortion = Lang::portion($portion);
		return $this;
	}
	
	public function setCheeseWeight($weight)
	{
		$this->setHasCheese(true);
		$this->_cheeseWeight = Lang::weight($weight);
		return $this;
	}
	
	public function setHasCheese($hasCheese)
	{
		$this->_hasCheese = (bool) $hasCheese;
		if(!$hasCheese) {
			$this->_cheesePortion = null;
			$this->_cheeseWeight = null;
		}
		return $this;
	}
	
	public function setHasSauce($hasSauce)
	{
		$this->_hasSauce = (bool) $hasSauce;
		if(!$hasSauce) {
			$this->_sauceType = null;
			$this->_sauceWeight = null;
		}
		return $this;
	}
	
	public function setSauce($type,$weight='normal')
	{
		if(!$type) {
			$this->setHasSauce(false);
			return $this;
		}
		
		$this->setSauceType($type);
		$this->setSauceWeight($weight);
		return $this;
	}
	
	public function setSauceType($type)
	{
		$this->setHasSauce(true);
		$this->_sauceType = Lang::sauce($type);
		return $this;
	}
	
	public function setSauceWeight($weight)
	{
		$this->setHasSauce(true);
		$this->_sauceWeight = Lang::weight($weight);
		return $this;
	}
	
	public function setToppings(array $toppings=array())
	{
		$this->_toppings = $toppings;
		return $this;
	}
	
	public function setType($size,$type)
	{
		$this->_type = Lang::type($size,$type);
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