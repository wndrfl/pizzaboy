<?php
if(isset($_POST['sub_order'])) {
	
	require 'src/autoload.php';

	$dominos = new \Dominos\Dominos();
	
	$user = $dominos->login('johnnietheblack@gmail.com','Pooper1224!');
	$creditCard = $dominos->getPrimaryCreditCard($user);
	die();

	// Create your order
	$order = $dominos->createOrder();

	// Set user info
	$user = $dominos->createUser();
	$order->setUser($user);

	// Set a payment option
	$creditCard = $dominos->createCreditCard();
	$creditCard->setNumber($_POST['cc']);
	$creditCard->setExpiration($_POST['expiration_month'].$_POST['expiration_year']);
	$creditCard->setSecurityCode($_POST['security_code']);
	$creditCard->setPostalCode($_POST['billing_zip']);
	$creditCard->setType($_POST['card_type']);
	$order->setPaymentOption($creditCard);

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
	$store = $dominos->findClosestStore($address);
	$order->setStore($store);

	// Price the order
	$dominos->priceOrder($order);

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
						<select>
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

			<fieldset>
				<legend>Billing Information</legend>
				<div class="form-section">
					<label>Credit Card</label>
					<div>
						<input type="text" name="cc" placeholder="No spaces..." />
					</div>
				</div>
				<div class="form-section">
					<label>Security Code</label>
					<div>
						<input type="text" name="security_code" placeholder="Three digits..." />
					</div>
				</div>
				<div class="form-section">
					<label>Expiration</label>
					<div>
						<select name="expiration_month">
							<?php for($i=1;$i<=12;$i++) { ?>
							<option value="<?php echo sprintf("%02s", $i); ?>"><?php echo $i; ?></option>
							<?php } ?>
						</select>
						<select name="expiration_year">
							<?php for($i=13;$i<=20;$i++) { ?>
							<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-section">
					<label>Card Type</label>
					<div>
						<select name="card_type">
							<option value="VISA">Visa</option>
							<option value="AMEX">Amex</option>
						</select>
					</div>
				</div>
				<div class="form-section">
					<label>Billing Zip</label>
					<div>
						<input type="text" name="billing_zip" placeholder="Five digits..." />
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