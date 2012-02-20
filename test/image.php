<?php

	ini_set('display_errors', 1); 
	error_reporting(E_ALL);
	
	include_once('../lib/ImageURL.php');

	$url = str_replace(dirname($_SERVER['SCRIPT_NAME']).'/', '', $_SERVER['REQUEST_URI']);
		
	$img = new ImageURL($url);
	$img->get(true);

?>