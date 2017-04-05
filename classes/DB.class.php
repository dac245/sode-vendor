<?php
class DB{
	private $dbh; // our database variable for this class
	
	// connects to the database from the dbinfo.php file
	function __construct(){
		require_once("../../../dbinfo.php");
		try{
			$this->dbh = new PDO("mysql:host=$host; dbname=$db;", $user, $pass);
			
			// change error reporting
			$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	}
	
	// gets the sodas that are on sale
	function getSodaSale(){
		$data = $this->getSodasOnSale("0"); // gets the data from the sodas table
		if(count($data) > 0){	// if there is data go here which creates an output to echo
			$bigString = "<div class='styleDiv'>\n";
			foreach($data as $row){ // loops through all the sodas that are on sale
				// creates html for the data and use hidden to get the values
				$bigString .= "<form method='post' action='index.php'>
							   <h2>{$row['SodaName']}</h2>
							   <img class='imgEdit' src='{$row['SodaImg']}' alt='Picture of Soda'/>
							   <p>{$row['SodaDescription']}</p>
							   <p>Sale Price: &#36;{$row['SodaSalePrice']} (Regular: &#36;{$row['SodaPrice']})</p>
							   <p>Quantity: {$row['SodaQuantity']}</p>
							   <input type='hidden' name='sodaName' value='{$row['SodaName']}'/>
							   <input type='hidden' name='sodaDescrip' value='{$row['SodaDescription']}'/>
							   <input type='hidden' name='sodaPrice' value='{$row['SodaPrice']}'/>
							   <input type='hidden' name='sodaSalePrice' value='{$row['SodaSalePrice']}'/>
							   <input type='hidden' name='sodaQuantity' value='{$row['SodaQuantity']}'/>
							   <input type='hidden' name='sodaImg' value='{$row['SodaImg']}'/>
							   <input type='submit' value='Add to cart' name='addCart'/>
							   <hr />
							   </form>\n";
			}
			$bigString .= "</div>\n";
		}
		else{ // if there is no item on sale we provide a message for output
			$bigString = "<div class='styleDiv'><h2>Seems to be no Sale going on.</h2></div>";
		}
		
		return $bigString;
	} // end of getSodaSale
	
	// is used with the getSodaSale function. This gets the sale data
	function getSodasOnSale($sale){
		try{
			$data = array(); // data array to store the data
			$stmt = $this->dbh->prepare("SELECT * FROM soda WHERE SodaSalePrice > :sale"); // query for sale
			$stmt->execute(array('sale'=>$sale)); // adds our variable we passed through
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){	// uses the PDO fetch_assoc to get each data from the query
				$data[] = $row;
			}
			
			return $data;
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	} // end of getSodasOnSale
	
	// this gets the sodas that are on regular price
	function getSodaReg($page){
		$data = $this->getSodasReg("0", $page); // passes through a number for the sale and the page number
		$itemId = 1;
		if(count($data) > 0){
			$bigString = "<div class='styleDiv'>\n";
			foreach($data as $row){ // loops through each data for html out and adds them to hidden inputs to add to cart
				$bigString .= "<form method='post' action='index.php'>
							   <h2>{$row['SodaName']}</h2>
							   <img class='imgEdit' src='{$row['SodaImg']}' alt='Picture of Soda'/>
							   <p>{$row['SodaDescription']}</p>
							   <p>Price: &#36;{$row['SodaPrice']}</p>
							   <p>Quantity: {$row['SodaQuantity']}</p>
							   <input type='hidden' name='sodaName' value='{$row['SodaName']}'/>
							   <input type='hidden' name='sodaDescrip' value='{$row['SodaDescription']}'/>
							   <input type='hidden' name='sodaPrice' value='{$row['SodaPrice']}'/>
							   <input type='hidden' name='sodaSalePrice' value='{$row['SodaSalePrice']}'/>
							   <input type='hidden' name='sodaQuantity' value='{$row['SodaQuantity']}'/>
							   <input type='hidden' name='sodaImg' value='{$row['SodaImg']}'/>
							   <input type='submit' value='Add to cart' name='addCart'/>
							   <hr />
							   </form>\n";
				$itemId++;
			}
			$bigString .= "</div>\n";
			// based on the page number selected we go to the one that we're on
			if($page == "1"){
				$bigString .= "<div id='movePages'>
						       <a href='index.php?page=1'>[1]</a>
							   <a href='index.php?page=2'>2</a>
							   <a href='index.php?page=3'>3</a>
							   <a href='index.php?page=2'>></a>
							   <a href='index.php?page=3'>>></a>
							   </div>";
			}
			if($page == "2"){
				$bigString .= "<div id='movePages'>
							   <a href='index.php?page=1'><<</a>
							   <a href='index.php?page=1'><</a>
						       <a href='index.php?page=1'>1</a>
							   <a href='index.php?page=2'>[2]</a>
							   <a href='index.php?page=3'>3</a>
							   <a href='index.php?page=2'>></a>
							   <a href='index.php?page=3'>>></a>
							   </div>";
			}
			if($page == "3"){
				$bigString .= "<div id='movePages'>
							   <a href='index.php?page=1'><<</a>
							   <a href='index.php?page=2'><</a>
						       <a href='index.php?page=1'>1</a>
							   <a href='index.php?page=2'>2</a>
							   <a href='index.php?page=3'>[3]</a>
							   </div>";
			}
		}
		else{ // if there is no items in regular price we provide a message for output
			$bigString = "<div class='styleDiv'><h2>We seem to be out of stock. Sorry!</h2></div>";
		}
		
		return $bigString;
	}// end of getSodaReg
	
	// is used with the getSodaReg function. This gets the regular price data
	function getSodasReg($sale, $pageNum){
		try{
			$data = array();
			// based on the page number we output the data in multiples of 5. We use limit to get 5 items and the ones we want
			if($pageNum == "1"){
				$stmt = $this->dbh->prepare("SELECT * FROM soda WHERE SodaSalePrice = :sale LIMIT 5");
				$stmt->execute(array('sale'=>$sale));
			}
			if($pageNum == "2"){
				$stmt = $this->dbh->prepare("SELECT * FROM soda WHERE SodaSalePrice = :sale LIMIT 5,5");
				$stmt->execute(array('sale'=>$sale));
			}
			if($pageNum == "3"){
				$stmt = $this->dbh->prepare("SELECT * FROM soda WHERE SodaSalePrice = :sale LIMIT 10,5");
				$stmt->execute(array('sale'=>$sale));
			}
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){	// uses the PDO fetch_assoc to get each data from the query
				$data[] = $row;
			}
			
			return $data;
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	} // end of getSodaReg
	
	function getSodaAll(){ // outputs the dropdown for the items in the soda table
		$data = $this->getSodasData(); // we get the soda data here
		if(count($data) > 0){
			$bigString = "<div class='styleDiv'>
						  <form action='admin.php' method='post'>
						  <label>Choose an Item to edit:</label>
						  <select name='valOp'>\n";
			foreach($data as $row){ // each option has the value of the ID and has the name shown
				$bigString .= "<option value='{$row['SodaID']}'>{$row['SodaName']}</option>\n";
			}
			$bigString .= "	</select>
							<input type='submit' value='Submit' name='editForm'/>
							</form>
							</div>\n";
		}
		else{
			$bigString = "";
		}
		
		return $bigString;
	}// end of getSodaAll
	
	// is used with the getSodaAll function. This gets all the data from the soda table
	function getSodasData(){
		try{
			$data = array();
			$stmt = $this->dbh->prepare("SELECT * FROM soda");
			$stmt->execute();
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){	// uses the PDO fetch_assoc to get each data from the query
				$data[] = $row;
			}
			
			return $data;
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	} // end of getSodasData
	
	// this function displays the update form with the data of the selected item. passes the id for the query
	function getOldSodaAll($sodaId){
		$data = $this->getOldSodasData($sodaId); // get the data with the id used
		$i = 1;
		if(count($data) > 0){
		foreach($data as $row){ // shows the data of the update form 
			$bigString = "<div class='styleDiv'>
				<h2>Edit Soda:</h2>
				<form class='styleForm' action='admin.php' method='post' enctype='multipart/form-data'>
					<label>Name:</label><input type='text' value='{$row['SodaName']}' name='fullName'/>
					<label>Description:</label><textarea name='description' rows='3' cols='50'>{$row['SodaDescription']}</textarea>
					<label>Price:</label><input type='text' value='{$row['SodaPrice']}' name='price'/>
					<label>Quantity:</label><input type='text' value='{$row['SodaQuantity']}' name='qos'/>
					<label>Sale Price:</label><input type='text' value='{$row['SodaSalePrice']}' name='salePrice' value='0'/>
					<label>Upload Image:</label><input type='file' name='soda_Img'/>
					<strong>Your Password:</strong><input type='password' name='password'/>
					<input type='hidden' value='$sodaId' name='valId'/>
					<input type='reset' value='Reset'/>
					<input type='submit' value='Submit' name='updateForm'/> 
				</form>
			</div>";
		}
		
		}
		else{
			$bigString = "";
		}
		
		return $bigString;
	}// end of getOldSodaAll
	
	// is used with the getOldSodaAll function. Returns only the data with the id number associated with it
	function getOldSodasData($id){
		try{
			$data = array();
			$stmt = $this->dbh->prepare("SELECT * FROM soda WHERE SodaID = :id");
			$stmt->execute(array('id'=>$id));
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){	// uses the PDO fetch_assoc to get each data from the query
				$data[] = $row;
			}
			
			return $data;
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	} // end of getOldSodasData
	
	// this function displays the value we get from the cart table
	function getCart(){
		$data = $this->getCartItems(); // get the data from the cart table
		$priceSum = 0;
		if(count($data) > 0){
			$bigString = "<div class='styleDiv'>\n";
			foreach($data as $row) { // displays each item in the table
				$bigString .= "<h2>{$row['ProductName']}</h2>
							   <p>{$row['ProductDescription']}</p>";
							   
				// if statements adds either the sale price or the regular price
				if($row['ProductSalePrice'] == 0){
					$priceSum += $row['ProductPrice'];
					$bigString .= "<p>Regular: &#36;{$row['ProductPrice']}</p>
								   <hr />\n";
				}
				if($row['ProductSalePrice'] > 0){
					$priceSum += $row['ProductSalePrice'];
					$bigString .= "<p>Sale Price: &#36;{$row['ProductSalePrice']} (Was: &#36;{$row['ProductPrice']})</p>
								   <hr />\n";
				}
			}
			// the button to empty the table
			$bigString .= "<form method='post' action='cart.php'>
						   <input type='submit' value='Empty cart' name='emptyCart'/>
						   </form></div>\n";
			
			$bigString .= "<div id='cartStyle'>Your cart total: &#36;$priceSum</div>";
		}
		else{ // if there is nothing in the cart we display this message
			$bigString = "<div class='styleDiv'><h1>There is nothing in your cart!</h1></div>";
		}
		
		return $bigString;
	}// end of getCart
	
	// is used with the getCart function. Gets all the data from the cart table
	function getCartItems(){
		try{
			$data = array();
			$stmt = $this->dbh->prepare("SELECT * FROM cart");
			$stmt->execute();
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){	// uses the PDO fetch_assoc to get each data from the query
				$data[] = $row;
			}
			
			return $data;
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	}
	
	// the function to insert the values we get from a post into the cart table
	function insertToCart($name, $description, $price, $quantity, $saleprice, $img="tbd"){
		$insertString = "INSERT INTO cart (ProductName,ProductDescription,ProductPrice,ProductQuantity,
										  ProductSalePrice,ProductImg) values (:name, :descrip, :price, :quantity, :saleprice, :img)";
		
		// adds the data we passed through to the insert statement
		if($stmt = $this->dbh->prepare($insertString)){
			$stmt->execute(array('name'=>$name,
								 'descrip'=>$description,
								 'price'=>$price,
								 'quantity'=>$quantity,
								 'saleprice'=>$saleprice,
								 'img'=>$img));
		}
	}	// end of insertToCart
	
	// the function to insert the values from the add post into the soda table
	function insertToSoda($name, $description, $price, $quantity, $saleprice, $img="tbd"){
		$insertString = "INSERT INTO soda (SodaName,SodaDescription,SodaPrice,SodaQuantity,
										  SodaSalePrice,SodaImg) values (:name, :descrip, :price, :quantity, :saleprice, :img)";
		
		if($stmt = $this->dbh->prepare($insertString)){
			$stmt->execute(array('name'=>$name,
								 'descrip'=>$description,
								 'price'=>$price,
								 'quantity'=>$quantity,
								 'saleprice'=>$saleprice,
								 'img'=>$img));
		}
	}	// end of insertToSoda
	
	// the function to update the soda table with the data passed through
	function updateSoda($name, $descrip, $price, $quantity, $saleprice, $img="tbd", $id){
		$updateString = "UPDATE soda SET SodaName = :name,
										SodaDescription = :descrip,
										SodaPrice = :price,
										SodaQuantity = :quantity,
										SodaSalePrice = :saleprice,
										SodaImg = :img
									WHERE SodaId = :id";
		
		// adds the values passed through to the update statement
		if($stmt = $this->dbh->prepare($updateString)){
			$stmt->execute(array(':name'=>$name,
								 ':descrip'=>$descrip,
								 ':price'=>$price,
								 ':quantity'=>$quantity,
								 ':saleprice'=>$saleprice,
								 ':img'=>$img,
								 ':id'=>$id));
		}
	}	// end of updateSoda
	
	// the function to update the soda quantity based on the add to cart post
	function updateSodaQuantity($newQuantity, $name){ // passes through the new quantity and item name
		$updateString = "UPDATE soda SET SodaQuantity = :newQuantity
									WHERE SodaName = :name";
		
		// adds the values used for the update statement
		if($stmt = $this->dbh->prepare($updateString)){
			$stmt->execute(array(':newQuantity'=>$newQuantity,
								 ':name'=>$name));
		}
	}	// end of updateSodaQuantity
	
	// the function to delete all the items in the cart 
	function deleteCart(){
		$deleteString = "TRUNCATE TABLE cart"; // to make sure to fully delete the cart
		if($stmt = $this->dbh->prepare($deleteString)){
			$stmt->execute();
		}
	}	// end of deleteCart
	
	// this is used to get the user of the password associated with it
	function getUser($pword){
		$data = $this->checkPassword($pword); // passes the password for verification
		if(count($data) > 0){
			foreach($data as $row){ // gets the name of the user
				$bigString = $row['user'];
			}
		}
		else{ // there it is not correct add nothing
			$bigString = " ";
		}
		
		return $bigString;
	}// end of getUser
	
	// is used with getUser. gets the user data
	function checkPassword($pword){
		try{
			$data = array();
			// checks if the password is correct using encryption
			$stmt = $this->dbh->prepare("SELECT user FROM verify WHERE password = SHA1(:pword)");
			$stmt->execute(array('pword'=>$pword));
			
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){	// uses the PDO fetch_assoc to get each data from the query
				$data[] = $row;
			}
			
			return $data;
		}
		catch(PDOException $e){
			echo $e->getMessage();
			die();
		}
	} // end of checkPassword
} // end of DB