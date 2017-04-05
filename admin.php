<?php

	function __autoload($class_name){ // loads the classes necessar for admin.php
		// gets the files needed 
		require_once "./classes/$class_name.class.php";
		require_once "LIB_project1.php";
	}
	
	// navbar to show footer is the active page
	$navbar = '<nav>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="cart.php">Cart</a></li>
				<li><a href="admin.php" id="selected">Admin</a></li>
			</ul>
		</nav>';
	
	$db = new DB(); // creates a db variable from the database class
	$editForm = ""; // to store the update from
	
	$header = new Header(); // header variable creates a new header class
	// echos the header for the admin page
	echo $header->html_header("Admin Page - The Pop Shop", $navbar);
	
	if(isset($_POST['editForm'])) { // once a dropdown item is selected go here
		$selPost = $_POST['valOp']; // gets the value number from the dropdown
		// the value is now an argument in order to get the item selected
		$editForm = $db->getOldSodaAll($selPost);
	}
	
	if(isset($_POST['updateForm'])) { // when the form to update posts go here
		$pCheck = $db->getUser($_POST['password']); // gets the user from the password entered
		$updateImg = ""; // to store the img that will be included in the update
		$msg = ""; // the message show if the upload didnt work
		
		if($pCheck == 'admin'){ // checks to see if the admin user was pulled
			// checks to see if an image was entered or if and error occurred
			if(!empty($_FILES['soda_Img']) && $_FILES['soda_Img']['error'] == 0){
			// check the file size and type
			$filename = basename($_FILES['soda_Img']['name']);
			$ext = substr($filename, strrpos($filename,'.') + 1);
				
				// makes sure the extension and type are correct
				if(($ext == "gif" || $ext == "jpeg" || $ext == "png") && (
							 $_FILES['soda_Img']['type'] = "image/png" || 
							 $_FILES['soda_Img']['type'] = "image/jpeg" ||
							 $_FILES['soda_Img']['type'] = "image/gif"
							) &&
							 $_FILES['soda_Img']['size'] < 350000 // also checks the file size
							){
					// moves it to the image folder
					$updateImg = "./images/$filename";
					if(move_uploaded_file($_FILES['soda_Img']['tmp_name'], $updateImg)){
						chmod($updateImg, 0644); // changes the permission so it can be read		
						$msg = "It's done! the file has been saved";
					}
					else{
						$msg = "Error: an error occurred during upload.";
					}
				}
			}	
			
			// makes sure each inputted variable has the correct characters
			if(alphabeticNumericPunct($_POST['fullName']) === 1 &&
			   alphabeticNumericPunct($_POST['description']) === 1 &&
			   decimal($_POST['price']) === 1 &&
			   decimal($_POST['salePrice']) === 1 &&
			   integer($_POST['qos']) === 1) {
			   	
			   	// gets rid of any html or php tags that could mess with the site
				$stripName = strip_tags($_POST['fullName']);
			    $stripDescrip = strip_tags($_POST['description']);
				
				// finally we update the values 
				$db->updateSoda($stripName,$stripDescrip,$_POST['price'],$_POST['qos'],$_POST['salePrice'],
							$updateImg, $_POST['valId']);
				echo "<h1>Item has been updated in the inventory</h1>";
			}
			else{ // if it has the wrong characters we display a message
				echo "<h1>Sorry. One of your inputs was invalid. Try Again.</h1>";
			}
		}
		else{ // if the password was incorrect we display a message
			echo "<h1>Item was not updated to the inventory. Invalid Password!!</h1>";
			echo $msg;
		}
	}

	if(isset($_POST['addForm'])) { // This checks if the form to add items is set
		$pCheck = $db->getUser($_POST['password']); // gets the user of the password entered
		$newImg = "";
		$msg = "";
		
		if($pCheck == 'admin'){	// checks if the user has admin privileges

			if(!empty($_FILES['soda_Img']) && $_FILES['soda_Img']['error'] == 0){ // checks if the image upload went right
			// check the file size and type
			$filename = basename($_FILES['soda_Img']['name']);
			$ext = substr($filename, strrpos($filename,'.') + 1);
				
				// see if the appropriate extension and the right type is used
				if(($ext == "gif" || $ext == "jpeg" || $ext == "png") && (
							 $_FILES['soda_Img']['type'] = "image/png" || 
							 $_FILES['soda_Img']['type'] = "image/jpeg" ||
							 $_FILES['soda_Img']['type'] = "image/gif"
							) &&
							 $_FILES['soda_Img']['size'] < 350000
							){
					// move the uploaded image to the images directory
					$newImg = "./images/$filename";
					if(move_uploaded_file($_FILES['soda_Img']['tmp_name'], $newImg)){
						chmod($newImg, 0644);		
						$msg = "It's done! the file has been saved";
					}
					else{
						$msg = "Error: an error occurred during upload.";
					}
				}
			}
			
			// checks if the right characters were used
			if(alphabeticNumericPunct($_POST['fullName']) === 1 &&
			   alphabeticNumericPunct($_POST['description']) === 1 &&
			   decimal($_POST['price']) === 1 &&
			   decimal($_POST['salePrice']) === 1 &&
			   integer($_POST['qos']) === 1) {
				
				// strips html and php tags that could be harmful
				$stripName = strip_tags($_POST['fullName']);
			    $stripDescrip = strip_tags($_POST['description']);
			
				// insert the rest of the values entered from the form
				$db->insertToSoda($stripName,$stripDescrip,$_POST['price'],$_POST['qos'],$_POST['salePrice'],
							  $newImg);
				echo "<h1>Item has been added to inventory</h1>";
			}
			else{ // if the wrong characters were used then go gere
				echo "<h1>Sorry. One of your inputs was invalid. Try Again.</h1>";
			}
		}
		else{ // if the wrong password was entered we go here
			echo "<h1>Item was not added to the inventory. Invalid Password!!</h1>";
			echo $msg;
		}
	}
?>

<h1>Administrator Inventory Page</h1>

<?php
	echo $db->getSodaAll(); // creates the dropdown values
	echo $editForm; // creates the form for updating
?>

<div class="styleDiv">
	<h2>Add Soda:</h2>
	<form class="styleForm" action="admin.php" method="post" enctype='multipart/form-data'>
		<label>Name:</label><input type="text" name="fullName"/>
		<label>Description:</label><textarea name="description" rows="3" cols="50"></textarea>
		<label>Price:</label><input type="text" name="price"/>
		<label>Quantity:</label><input type="text" name="qos"/>
		<label>Sale Price:</label><input type="text" name="salePrice" value="0"/>
		<label>Upload Image:</label><input type="file" name="soda_Img"/>
		<strong>Your Password:</strong><input type="password" name="password"/>
		<input type="reset" value="Reset"/>
		<input type="submit" value="Submit" name="addForm"/> 
	</form>
</div>

<?php
	// creates footer variable to show the footer
	$footer = new Footer();
	echo $footer->html_footer();
?>