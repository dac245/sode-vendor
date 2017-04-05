<?php
class Header{
	static function html_header($title, $navbar){
		$htmlString = <<<END
<!DOCTYPE html>
<html>
	<head>
		<title>$title</title>
		<link rel="stylesheet" type="text/css" href="main.css" />
	</head>
<body>
	<h1 id="header">The Pop Shop</h1>
	$navbar
		
		

END;
		return $htmlString;
	}
}