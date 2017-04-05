<?php
function alphabeticNumericPunct($value) {	// checks if there are numbers, letters and special characters
	$reg = "/^[A-Za-z0-9 _.,!?\"'-]+$/";
	return preg_match($reg,$value);
}

function integer($value) {	// checks if it is an integer vale
	$reg = "/(^-?\d\d*$)/";
	return preg_match($reg,$value);
}

function decimal($value) {	// checks if it is decimal or can be an integer also
	// $reg = "/^[0-9]*\.[0-9]+$/";
	$reg = "/(\d+(\.\d+)?)/";
	return preg_match($reg,$value);
}