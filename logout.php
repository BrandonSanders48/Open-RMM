<?php
	require("Includes/db.php");

	unset($_SESSION['userid']);
	session_unset();
	session_destroy();
	sleep(1);
	
	if(isset($_SESSION['userid'])){
		exit("We had trouble logging you out... It's our fault not yours.");
	}else{
		header("Location: login.php");
		exit("Logging you out securely...");
	}
	
	

