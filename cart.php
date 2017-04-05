<?php
	function __autoload($class_name){
		// need to use filename path structure
		require_once "./classes/$class_name.class.php";
		require_once "LIB_project1.php";
	}
	
	$db = new DB(); // creates a database variable from the db class
	
	// navbar to show cart.php is the page we're on
	$navbar = '<nav>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="cart.php" id="selected">Cart</a></li>
				<li><a href="admin.php">Admin</a></li>
			</ul>
		</nav>';
		
	if(isset($_POST['emptyCart'])) { // go here to empty the cart
		$db->deleteCart();
	}
	
	// creates a header variable to show the initial header
	$header = new Header();
	echo $header->html_header("Cart - The Pop Shop", $navbar);
?>

<h1>Items In Your Cart</h1>

<?php
echo $db->getCart(); // gets the items in the cart 
?>

<?php
	// creates footer variable to show the footer
	$footer = new Footer();
	echo $footer->html_footer();
?>