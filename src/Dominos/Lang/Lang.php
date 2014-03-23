<?php
namespace Dominos\Lang;

class Lang
{
	public static
		$translations = array(
			
			'portion'	 => array(
				'left' 			=> 'PORTION_LEFT',
				'right' 		=> 'PORTION_RIGHT',
				'whole' 		=> 'PORTION_WHOLE'
			),
			'sauce' 	=> array(
				'tomato' 		=> 'SAUCE_TOMATO',
				'marinara'	 	=> 'SAUCE_MARINARA',
				'bbq' 			=> 'SAUCE_BBQ',
				'white'			=> 'SAUCE_WHITE'
			),
			'size'		 => array(
				'small' 		=> 10,
				'medium' 		=> 12,
				'large' 		=> 14,
				'x-large' 		=> 16
			),
			'type' 		=> array(
				'gluten-free' 	=> 'GLU',
				'hand-tossed' 	=> 'HAND',
				'pan' 			=> 'PAN',
				'thin-crust'	=> 'THIN',
				'brooklyn' 		=> 'BROOKLYN'
			),
			'weight' 	=> array(
				'light' 		=> 'WEIGHT_LIGHT',
				'normal' 		=> 'WEIGHT_NORMAL',
				'extra' 		=> 'WEIGHT_EXTRA',
				'double' 		=> 'WEIGHT_DOUBLE',
				'triple' 		=> 'WEIGHT_TRIPLE'
			)
		);
		
	// Pizza types
	public static
		$TYPE_10_GLU 				= 'P10IGFZA',
		$TYPE_10_HAND 				= '10SCREEN',
		$TYPE_12_HAND 				= '12SCREEN',
		$TYPE_14_HAND 				= '14SCREEN',
		$TYPE_12_PAN  				= 'P12IPAZA',
		$TYPE_10_THIN 				= '10THIN',
		$TYPE_12_THIN 				= '12THIN',
		$TYPE_14_THIN 				= '14THIN',
		$TYPE_14_BROOKLYN 			= 'PBKIREZA',
		$TYPE_16_BROOKLYN 			= 'P16IBKZA';

	// Portions
	public static
		$PORTION_LEFT				= '1/2',
		$PORTION_WHOLE				= '1/1',
		$PORTION_RIGHT				= '2/2';
	
	// Weight
	public static	
		$WEIGHT_LIGHT				= '0.5',
		$WEIGHT_NORMAL				= '1',
		$WEIGHT_EXTRA				= '1.5',
		$WEIGHT_DOUBLE				= '2',
		$WEIGHT_TRIPLE				= '3';	
		
	// Cheese portions
	public static
		$CHEESE_PORTION_LEFT		= '1/2',
		$CHEESE_PORTION_WHOLE		= '1/1',
		$CHEESE_PORTION_RIGHT		= '2/2';

	// Cheese weight
	public static
		$CHEESE_WEIGHT_LIGHT		= '0.5',
		$CHEESE_WEIGHT_NORMAL		= '1',
		$CHEESE_WEIGHT_EXTRA		= '1.5',
		$CHEESE_WEIGHT_DOUBLE		= '2',
		$CHEESE_WEIGHT_TRIPLE		= '3';

	// Toppings
	public static
		$TOPPING_PEPPERONI			= 'P',
		$TOPPING_ITALIAN_SAUSAGE 	= 'S',
		$TOPPING_BEEF 				= 'B',
		$TOPPING_HAM 				= 'H',
		$TOPPING_BACON 				= 'K',
		$TOPPING_PHILLY_STEAK 		= 'Pm',
		$TOPPING_GREEN_PEPPERS 		= 'G',
		$TOPPING_BLACK_OLIVES 		= 'R',
		$TOPPING_PINEAPPLES 		= 'N',
		$TOPPING_MUSHROOMS 			= 'M',
		$TOPPING_ONIONS 			= 'O',
		$TOPPING_JALAPENOS 			= 'J',
		$TOPPING_BANANA_PEPPERS 	= 'Z',
		$TOPPING_CHEDDAR_CHEESE 	= 'E',
		$TOPPING_PROVOLONE_CHEESE 	= 'Cp',
		$TOPPING_GREEN_OLIVES 		= 'V',
		$TOPPING_DICED_TOMATOES 	= 'Td';	

	// Toppings weight
	public static
		$TOPPING_WEIGHT_LIGHT		= '0.5',
		$TOPPING_WEIGHT_NORMAL		= '1',
		$TOPPING_WEIGHT_EXTRA		= '1.5';	

	// Sauces
	public static
		$SAUCE_TOMATO 				= 'X',
		$SAUCE_MARINARA				= 'Xm',
		$SAUCE_BBQ					= 'Bq',
		$SAUCE_WHITE				= 'Xw';

	// Sauce weight
	public static
		$SAUCE_WEIGHT_LIGHT			= '0.5',
		$SAUCE_WEIGHT_NORMAL		= '1',
		$SAUCE_WEIGHT_EXTRA			= '1.5';
	
	// TRANSLATORS
	public static function translationFor($section,$key)
	{
		if(!isset(self::$translations[$section][$key])) {
			// TODO EXCEPTION
			return false;
		}
		
		return self::$translations[$section][$key];
	}
	
	public static function portion($portion)
	{
		$var = self::translationFor('portion',$portion);
		return self::$$var;
	}
	
	public static function sauce($sauce)
	{
		$var = self::translationFor('sauce',$sauce);
		return self::$$var;
	}
	
	public static function size($size)
	{
		$inches = self::translationFor('size',$size);
		return $inches;
	}
	
	public static function type($size,$type)
	{
		$size = self::size($size);
		$type = self::translationFor('type',$type);
		$var = 'TYPE_'.$size.'_'.$type;
		
		if(!isset(self::$$var)) {
			// TODO EXCEPTION
			return false;
		}
		
		return self::$$var;
	}
	
	public static function weight($weight)
	{
		$var = self::translationFor('weight',$weight);
		return self::$$var;
	}
	
	// TOPPINGS
		
	public static function pepperoni()
	{
		return self::$TOPPING_PEPPERONI;
	}
	
	public static function italianSausage()
	{
		return self::$TOPPING_ITALIAN_SAUSAGE;
	}
	
	public static function beef()
	{
		return self::$TOPPING_BEEF;
	}
	
	public static function ham()
	{
		return self::$TOPPING_HAM;
	}
	
	public static function bacon()
	{
		return self::$TOPPING_BACON;
	}
	
	public static function phillySteak()
	{
		return self::$TOPPING_PHILLY_STEAK;
	}
	
	public static function greenPeppers()
	{
		return self::$TOPPING_GREEN_PEPPERS;
	}
	
	public static function blackOlives()
	{
		return self::$TOPPING_BLACK_OLIVES;
	}

	public static function pineapples()
	{
		return self::$TOPPING_PINEAPPLES;
	}
	
	public static function mushrooms()
	{
		return self::$TOPPING_MUSHROOMS;
	}
	
	public static function onions()
	{
		return self::$TOPPING_ONIONS;
	}
	
	public static function jalapenos()
	{
		return self::$TOPPING_JALAPENOS;
	}
	
	public static function bananaPeppers()
	{
		return self::$TOPPING_BANANA_PEPPERS;
	}
	
	public static function cheddarCheese()
	{
		return self::$TOPPING_CHEDDAR_CHEESE;
	}
	
	public static function provoloneCheese()
	{
		return self::$TOPPING_PROVOLONE_CHEESE;
	}
	
	public static function greenOlives()
	{
		return self::$TOPPING_GREEN_OLIVES;
	}
	
	public static function dicedTomatoes()
	{
		return self::$TOPPING_DICED_TOMATOES;
	}
}