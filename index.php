<?php
session_start();

require 'vendor/autoload.php';
use Dominos\Dominos;

$dominos = new Dominos(true); // TRUE | FALSE for test mode

if(isset($_POST['sub_order'])) {

	// Login and set user info
	$user = $dominos->login($_POST['email'],$_POST['password']);
	
	// Create a new order
	$order = $user->newOrder();

	// Add a pizza to this order
	$order
		->newPizza('medium','hand-tossed')
		->setCheese('whole','normal')
		->setSauce('tomato','normal')
		->addPepperoni()
		->addPineapple()
		->save();
		
	// Set a delivery address
	$order
		->address()
		->setStreet($_POST['street'])
		->setCity($_POST['city'])
		->setRegion($_POST['state'])
		->setPostalCode($_POST['postal_code'])
		->save();
	
	// Get coupons
	/*
	$coupons = $dominos->getStoreCoupons($store->id());
	$order->addCoupon($coupons[0]);
	*/
	
	if($order->place()) {
		echo "Order placed!";
	}else{
		echo "Order was not placed.";
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