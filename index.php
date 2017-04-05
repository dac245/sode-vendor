<?php
	function __autoload($class_name){ // loads the classes necessary for index.php
		// need to use filename path structure
		require_once "./classes/$class_name.class.php";
		require_once "LIB_project1.php";
	}
	
	$db = new DB();	// database variable to use the functions in our DB class
	
	// our navbar to show which page which is currently selected
	$navbar = '<nav>
			<ul>
				<li><a href="index.php" id="selected">Home</a></li>
				<li><a href="cart.php">Cart</a></li>
				<li><a href="admin.php">Admin</a></li>
			</ul>
		</nav>';
	
	$header = new Header();	// header variable gets our header class
	echo $header->html_header("Home Page - The Pop Shop", $navbar);	// outputs our header tags
	
	if(isset($_GET['page'])) {	// checks to see if the page variable has a value
		$pageNum = $_GET['page'];	// if true we add the value to the pageNum variable
	}
	else{
		$pageNum = "1";	// if it's not set give it an initial value of 1
	}
	
	if(isset($_POST['addCart'])) {	// checks our post to see if we need to add an item to cart
		if($_POST['sodaQuantity'] == 0){	// if the quantity of our item is 0 echo a response
			echo "<h1>Sorry, there is no more of that item in stock.</h1>";
		}
		else{	// if there is still items left go here
			$newQuantity = $_POST['sodaQuantity'] - 1;	// removes an item from quantity
		
			$db->updateSodaQuantity($newQuantity, $_POST['sodaName']); // updates the quantity with the soda name
			// inserts the values from the sodas table to the cart table
			$db->insertToCart($_POST['sodaName'],$_POST['sodaDescrip'],$_POST['sodaPrice'],$_POST['sodaQuantity'],$_POST['sodaSalePrice'],
							  $_POST['sodaImg']);
			echo "<h1>Item has been added to the cart</h1>";
		}
	}
	
?>

<h1>Pop for Sale</h1>
<?php
echo $db->getSodaSale(); // gets all the items from sodas that are on sal
?>

<h1>Pop Catalog</h1>
<?php
	echo $db->getSodaReg($pageNum);	// gets all items from soda with regualar price
?>

<?php
	// the create a footer variable from the footer class and echos it out
	$footer = new Footer();
	echo $footer->html_footer();
?>