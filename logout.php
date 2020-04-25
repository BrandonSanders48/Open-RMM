<?php
	require("Includes/db.php");
	unset($_SESSION['userid']);
	session_unset();
	session_destroy();
	header("Location: login.php");
	exit("Redirecting");
?>Logging you out securely...