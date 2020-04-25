<?php
	require("Includes/db.php");
	$_SESSION['userid'] = "";
	session_unset();
	session_destroy();
	header("Location: login.php");
	exit("Redirecting");
	if(!isset($_SESSION['userid'])){
		http_response_code(404);
		die();
	}
?>Logging you out securely...