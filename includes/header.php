<?php
session_start();
if(!isset($_SESSION['isLogged'])){
	$_SESSION['isLogged'] = false;
}
include dirname(__FILE__).'/functions.php';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?=$pageTitle;?></title>
</head>
<body>

