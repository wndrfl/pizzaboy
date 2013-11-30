<?php
require 'src/autoload.php';

$dominos = new \Dominos\Dominos();

// Create your order
$order = $dominos->createOrder();

// Set user info
$user = $dominos->createUser();
$order->setUser($user);

// Set a delivery address
$address = $dominos->createAddress();
$address->setStreet('1225 E. Alta Vista');
$address->setCity('Tucson');
$address->setRegion('Arizona');
$address->setPostalCode('85719');
$order->setAddress($address);

// Add a pizza to this order
$pizza = $dominos->createPizza();
$pizza->setType(\Dominos\Dominos::SIZE_12_HAND);
$pizza->setCheesePortion(\Dominos\Dominos::CHEESE_PORTION_WHOLE);
$pizza->setCheeseWeight(\Dominos\Dominos::CHEESE_WEIGHT_NORMAL);
$pizza->setSauceType(\Dominos\Dominos::SAUCE_TOMATO);
$pizza->setSauceWeight(\Dominos\Dominos::SAUCE_WEIGHT_NORMAL);
$pizza->addTopping(\Dominos\Dominos::TOPPING_PEPPERONI,\Dominos\Dominos::TOPPING_PORTION_WHOLE,\Dominos\Dominos::TOPPING_WEIGHT_NORMAL);
$order->addPizza($pizza);

// Set store
$store = $dominos->closestStore('1225 E. Alta Vista','Tucson','Arizona','85719');
$order->setStore($store);

// Price the order
$dominos->priceOrder($order);

var_dump($order->amount());