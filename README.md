# Pizzaboy

## 1. Authenticate a user with their Dominos account credentials

```php
$user = $pizzaboy->login(<email>,<password>);
```

## 2. Create a new order

```php
$order = $user->newOrder();
```

## 3. Add a pizza to this order

```php
$order
	->newPizza('medium','hand-tossed')
	->setCheese('whole','normal')
	->setSauce('tomato','normal')
	->addPepperoni()
	->addPineapple()
	->save();
```

## 4. Set a delivery address for this order

```php
$order
	->address()
	->setStreet($_POST['street'])
	->setCity($_POST['city'])
	->setRegion($_POST['state'])
	->setPostalCode($_POST['postal_code'])
	->save();
```

## 5. Place the order!

```php
if($order->place()) {
	echo "Order placed!";
}else{
	echo "Order was not placed.";
}
```