<?php
include("../Includes/db.php");
	$computerID = (int)$_GET['ID'];
unset($_SESSION['userid']);
unset($_SESSION['recent']);
unset($_SESSION['recentedit']);
unset($_SESSION['username']);
session_regenerate_id();
session_destroy();
header("location: ../login2.php"); 

?>
Logging You Out Securely...

<?php 