<?php
	include("../Includes/db.php");

	unset($_SESSION['userid']);
	unset($_SESSION['recent']);
	unset($_SESSION['recentedit']);
	unset($_SESSION['username']);

	session_regenerate_id();
	session_destroy();
	header("location: ../login.php"); 
?>
Logging You Out Securely...