<?php
require("Includes/db.php");
//$_SESSION['userid'] = "1"; //Bypass Login

if(isset($_POST['username'], $_POST['password'])){
	//Connect to DB, this is treated diffrent to always show a nice front end.
	$db = mysqli_connect($siteSettings['MySQL']['host'], $siteSettings['MySQL']['username'], $siteSettings['MySQL']['password'], $siteSettings['MySQL']['database']);
	if(!$db){ //Unable to login to DB, Show error after attempting to login.
		$message = " <span style='color:red'>Error establishing a database connection.</span>";
	}else{
		//Continue Login
		$username = mysqli_escape_string($db, clean(strip_tags($_POST['username'])));
		$password = mysqli_escape_string($db, strip_tags($_POST['password']));
		
		$query = "SELECT * FROM users WHERE active='1' AND username='".$username."'";
		$results = mysqli_query($db, $query);
		$count = mysqli_num_rows($results);
		$data = mysqli_fetch_assoc($results);
		$dbPassword = crypto('decrypt', $data['password'], $data['hex']);
		
		if($password !== $dbPassword || $dbPassword == ""){
			$count = 0;
		}

		if($count > 0){
			$query = "UPDATE users SET last_login='".time()."' WHERE ID=".$data['ID'].";";
			$results = mysqli_query($db, $query);
			$_SESSION['userid'] = $data['ID'];
			$_SESSION['username'] = $data['username'];
			$_SESSION['showModal'] = "true";
			$_SESSION['recent'] = explode(",", $data['recents']);
			if($data['recents'] == ""){ 
				$_SESSION['recent'] = array(); 
			}
			$_SESSION['recentedit'] = explode(",", $data['recentedit']);
			if($data['recentedit'] == ""){ 
				$_SESSION['recentedit'] = array(); 
			}
			header("location: index.php");
		}else{
			$message = " <span style='color:red'>Incorrect Login Info.</span>";
		}
	}
}
if($_SESSION['userid'] != ""){ 
	header("location: index.php");
	exit;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Open-RMM | Login</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" >
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="css/all.min.css"/>
		<script src="js/all.min.js"></script>
		<script src="js/jquery.js" ></script>
		<link rel="stylesheet" href="css/bootstrap.min.css"/>
		<script src="js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="css/custom.css"/>
	</head>
	<body style="background-color:#f7f8fa;">
		<div style="background-color:<?php echo $siteSettings['theme']['Color 1'];?>;color:#fff;text-align:center;padding:10px;padding-left:20px;position:fixed;top:0px;width:100%;z-index:99;">
			<h5>
				<a href="index.php" style="color:#fff;text-decoration:none;">
					<span title="#">Open-RMM</span> | <span title="Remote Monitoring And Management">R.M.M.</span>
				</a>
			</h5>
		</div>
		<div>
		<div class="row" style="text-align:center;margin-bottom:10px;margin-top:10px;border-radius:3px;overflow:hidden;padding:5px;">
			<div style="margin-bottom:100px;" class="sidenav col-xs-4 col-sm-4 col-md-4 col-lg-4">
			 <div class="login-main-text">
				<h2>Remote Monitoring & Management Platform</h2><br>
				<p>Remote Management is managing a computer or a network from a
				remote location. It involves installing software and managing all activities on the systems/network, workstations,
				servers or endpoints of a client, from a remote location.</p>
			 </div>
		  </div>
		  <div style="padding:5px;margin-top:20%;margin-bottom:100px;" class="main col-xs-8 col-sm-8 col-md-8 col-lg-8">
			 <div >
				<div class=" col-xs-6 col-sm-6 col-md-6 col-lg-6">
				<div style="margin-top:-100px;padding-bottom:50px;">
					<h2>Open-RMM</h2>
					<p>Login From Here To Access Our Remote Monitoring & Management Platform. <?php echo $message; ?></p>
				</div>
				   <form method="post" class="form-signin">
					  <div style="text-align:left;" class="form-label-group">
						<label  for="inputEmail"><b>Username:</b></label>
						<input maxlength="25" minlength="4" type="text" name="username" id="inputEmail" class="form-control" placeholder="Username" required autofocus>
					  </div><br>
					  <div style="text-align:left;" class="form-label-group">
						<label for="inputPassword"><b>Password:</b></label>
						<input maxlength="25" minlength="4" type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
					  </div><br>
					  <button style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;" class="btn btn-lg btn-block text-uppercase" type="submit">
						<i class="fas fa-sign-in-alt"></i> Sign in
					  </button>
					</form>
				</div>
			 </div>
		  </div>
		 </div>
		  <footer style="z-index:999;padding:5px;height:30px;position:fixed;left:0;bottom:0;width:100%;color:#fff;text-align:center;background:<?php echo $siteSettings['theme']['Color 1'];?>;" class="page-footer font-small black">
			<div class="footer-copyright text-center ">© <?php echo date('Y');?> Copyright
				<a style="color:#fff;" href="#"> Open-RMM</a>
			</div>
		  </footer>
	</body>
</html>