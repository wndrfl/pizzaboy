<?php

// Start Dominos - vroom
require 'src/autoload.php';
$dominos = new \Dominos\Dominos();

if(isset($_POST['sub_order'])) {
	
	// Create your order
	$order = $dominos->createOrder();

	// Login and set user info
	$user = $dominos->login($_POST['email'],$_POST['password']);
	$order->setUser($user);

	// Set a delivery address
	$address = $dominos->createAddress();
	$address->setStreet($_POST['street']);
	$address->setCity($_POST['city']);
	$address->setRegion($_POST['state']);
	$address->setPostalCode($_POST['postal_code']);
	$order->setAddress($address);

	// Add a pizza to this order
	$pizza = $dominos->createPizza();
	$pizza->setType(\Dominos\Dominos::SIZE_14_HAND);
	$pizza->setCheesePortion(\Dominos\Dominos::CHEESE_PORTION_WHOLE);
	$pizza->setCheeseWeight(\Dominos\Dominos::CHEESE_WEIGHT_NORMAL);
	$pizza->setSauceType(\Dominos\Dominos::SAUCE_TOMATO);
	$pizza->setSauceWeight(\Dominos\Dominos::SAUCE_WEIGHT_NORMAL);
	$pizza->addTopping(\Dominos\Dominos::TOPPING_PEPPERONI,\Dominos\Dominos::TOPPING_PORTION_WHOLE,\Dominos\Dominos::TOPPING_WEIGHT_NORMAL);
	$pizza->addTopping(\Dominos\Dominos::TOPPING_PINEAPPLES,\Dominos\Dominos::TOPPING_PORTION_WHOLE,\Dominos\Dominos::TOPPING_WEIGHT_NORMAL);
	$order->addPizza($pizza);

	// Set store
	$store = $dominos->findClosestStore($address);
	$order->setStore($store);
	
	// Get coupons
	$coupons = $dominos->getStoreCoupons($store->id());
	$order->addCoupon($coupons[0]);

	// Price the order
	$dominos->priceOrder($order);
	
	$dominos->validateOrder($order);
	
	// Set a payment option
	$creditCard = $dominos->getPrimaryCreditCard($user);
	$order->setPaymentOption($creditCard);

	// Place the order
	if($dominos->placeOrder($order)) {
		echo "Your order was placed!";
	}else{
		echo "Sorry, there was a problem.";
	}
}
?>
<!DOCTYPE html>
<html>
<body>
	<style>
		body { 
			background-color:#f4f4f4;
		}
		
		fieldset {
			margin-bottom:20px;
		}
		
		.main-content {
			background-color:#fff;
			width:960px;
			margin:0 auto;
			padding:20px;
		}
		
		.order-form {
			border:1px #ccc solid;
			padding:20px;
		}
	</style>
	
	<div class="main-content">
		
		<h1>Order From Dominos</h1>

		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="order-form">
			<fieldset>
				<legend>Your Dominos Login</legend>
				<div class="form-section">
					<label>Email</label>
					<div>
						<input type="text" name="email" placeholder="Your email..." />
					</div>
				</div>
				<div class="form-section">
					<label>Password</label>
					<div>
						<input type="password" name="password" placeholder="Your password..." />
					</div>
				</div>
			</fieldset>
			
			<fieldset>
				<legend>Delivery Address</legend>
				<div class="form-section">
					<label>Street</label>
					<div>
						<input type="text" name="street" placeholder="555 Pepperoni Pl." />
					</div>
				</div>
				<div class="form-section">
					<label>City</label>
					<div>
						<input type="text" name="city" placeholder="Pizzaland" />
					</div>
				</div>
				<div class="form-section">
					<label>State</label>
					<div>
						<select name="state">
							<option value="AL">Alabama</option>
							<option value="AK">Alaska</option>
							<option value="AZ">Arizona</option>
							<option value="AR">Arkansas</option>
							<option value="CA">California</option>
							<option value="CO">Colorado</option>
							<option value="CT">Connecticut</option>
							<option value="DE">Delaware</option>
							<option value="DC">District Of Columbia</option>
							<option value="FL">Florida</option>
							<option value="GA">Georgia</option>
							<option value="HI">Hawaii</option>
							<option value="ID">Idaho</option>
							<option value="IL">Illinois</option>
							<option value="IN">Indiana</option>
							<option value="IA">Iowa</option>
							<option value="KS">Kansas</option>
							<option value="KY">Kentucky</option>
							<option value="LA">Louisiana</option>
							<option value="ME">Maine</option>
							<option value="MD">Maryland</option>
							<option value="MA">Massachusetts</option>
							<option value="MI">Michigan</option>
							<option value="MN">Minnesota</option>
							<option value="MS">Mississippi</option>
							<option value="MO">Missouri</option>
							<option value="MT">Montana</option>
							<option value="NE">Nebraska</option>
							<option value="NV">Nevada</option>
							<option value="NH">New Hampshire</option>
							<option value="NJ">New Jersey</option>
							<option value="NM">New Mexico</option>
							<option value="NY">New York</option>
							<option value="NC">North Carolina</option>
							<option value="ND">North Dakota</option>
							<option value="OH">Ohio</option>
							<option value="OK">Oklahoma</option>
							<option value="OR">Oregon</option>
							<option value="PA">Pennsylvania</option>
							<option value="RI">Rhode Island</option>
							<option value="SC">South Carolina</option>
							<option value="SD">South Dakota</option>
							<option value="TN">Tennessee</option>
							<option value="TX">Texas</option>
							<option value="UT">Utah</option>
							<option value="VT">Vermont</option>
							<option value="VA">Virginia</option>
							<option value="WA">Washington</option>
							<option value="WV">West Virginia</option>
							<option value="WI">Wisconsin</option>
							<option value="WY">Wyoming</option>
						</select>
					</div>
				</div>
				<div class="form-section">
					<label>Postal Code</label>
					<div>
						<input type="text" name="postal_code" placeholder="55555" />
					</div>
				</div>
			</fieldset>
			
			<div class="form-section">
				<input type="submit" name="sub_order" value="Place order" />
			</div>
		</form>
		
	</div>

</body>
</html>