<?php
	require("Includes/db.php");

	$search = strip_tags(urldecode($_GET['search']));
	$limit = intval(base64_decode($_GET['l']));
	$query = "SELECT username,nicename FROM users WHERE ID='".$_SESSION['userid']."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$user = mysqli_fetch_assoc($results);
	$username = $user['username'];

	if(isset($_POST)){
		//Edit Computer (Edit.php)
		if($_POST['type'] == "EditComputer"){
			$ID = (int)$_POST['ID'];
			$name = clean($_POST['name']);
			$comment = clean($_POST['comment']);
			$phone = clean($_POST['phone']);
			$company = clean($_POST['company']);
			$type = clean($_POST['PctType']);
			$email = strip_tags($_POST['email']);
			$TeamID = (int)$_POST['TeamID'];
			$show_alerts = (int)$_POST['ShowAlerts'];

			//Edit Recents
			$query = "UPDATE users SET recentedit='".implode(",", $_SESSION['recentedit'])."' WHERE ID=".$_SESSION['userid'].";";
			if (in_array($ID, $_SESSION['recentedit'])){
				if (($key = array_search($ID, $_SESSION['recentedit'])) !== false) {
					unset($_SESSION['recentedit'][$key]);
				}
				array_push($_SESSION['recentedit'],ID);
				$results = mysqli_query($db, $query); //Update
			}else{
				if(end($_SESSION['recentedit']) != $ID){
					array_push($_SESSION['recentedit'], $ID);
					$results = mysqli_query($db, $query); //Update
				}
			}
			//Update Computer Data
			$query = "UPDATE computerdata SET show_alerts='".$show_alerts."', teamviewer='".$TeamID."', computerType='".$type."', comment='".$comment."', name='".$name."', phone='".$phone."', CompanyID='".$company."', email='".$email."' WHERE ID='".$ID."';";
			$results = mysqli_query($db, $query);
		}

		//Delete computer modal on edit.php
		if($_POST['type'] == "DeleteComputer"){
			$ID = (int)$_POST['ID'];
			$hostname = clean($_POST['hostname']);
			if($ID > 0){
				$query = "UPDATE computerdata SET active='0' WHERE ID='".$ID."';";
				$results = mysqli_query($db, $query);
				$query = "DELETE FROM wmidata WHERE Hostname='".$hostname."';";
				$results = mysqli_query($db, $query);
				header("location: index.php");
			}
		}

		//Add Computers To Company
		if($_POST['type'] == "CompanyComputers"){
			$computers = $_POST['computers'];
			$companies = $_POST['companies'];
			foreach($computers as $computer) {
				$query = "UPDATE computerdata SET CompanyID='".$companies."' WHERE ID='".$computer."';";
				$results = mysqli_query($db, $query);
				echo $computer;
			}
			header("location: index.php");
		}

		//Add Edit/User
		if($_POST['type'] == "AddEditUser"){
			if(isset($_POST['username'])){
				$salt = getSalt(40);
				$user_ID = (int)$_POST['ID'];
				$username = clean($_POST['username']);
				$name = clean($_POST['name']);
				$email = crypto('encrypt', $_POST['email'], $salt);
				$password = clean($_POST['password']);
				$password2 = clean($_POST['password2']);
				$encryptedPassword = crypto('encrypt', $password, $salt);
				if($password === $password2){
					if($user_ID == 0){
						$query = "INSERT INTO users (username, password, hex, nicename , email)
								  VALUES ('".$username."', '".$encryptedPassword."','".$salt."','".$name."','".$email."')";
					}else{
						$query = "SELECT password, hex FROM users WHERE ID='".$user_ID."' LIMIT 1";
						$results = mysqli_query($db, $query);
						$result = mysqli_fetch_assoc($results);
						if($password==""){
							$encryptedPassword = crypto('decrypt', $result['password'], $result['hex']);
							$encryptedPassword = crypto('encrypt', $encryptedPassword, $salt);
						}
						$query = "UPDATE users SET username='".$username."',nicename='".$name."', email='".$email."', password='".$encryptedPassword."', hex='".$salt."' WHERE ID='".$user_ID."'";
					}
					$results = mysqli_query($db, $query);
					if($results){
						echo '<script>window.onload = function() { pageAlert("User Settings", "User settings changed successfully.","Success"); };</script>';
					}
				}else{ //passwords do not match
					echo '<script>window.onload = function() { pageAlert("User Settings", "Password change failed, passwords do not match.","Danger"); };</script>';
				}
			}
		}

		//Add Edit/Company
		if($_POST['type'] == "AddEditCompany"){
			if(isset($_POST['name'], $_POST['phone'], $_POST['address'], $_POST['email'])){
				$ID = (int)$_POST['ID'];
				$name = clean($_POST['name']);
				$phone = clean($_POST['phone']);
				$address = clean($_POST['address']);
				$comments = clean($_POST['comments']);
				$email = str_replace("'", "", $_POST['email']);
				if($ID == 0){
					$query = "INSERT INTO companies (name, phone, address, comments, email, date_added)
							  VALUES ('".$name."', '".$phone."', '".$address."', '".$comments."', '".$email."','".time()."')";
				}else{
					$query = "UPDATE companies SET name='".$name."', phone='".$phone."', address='".$address."', email='".$email."', comments='".$comments."'
							  WHERE CompanyID='".$ID."' LIMIT 1";
				}
				$results = mysqli_query($db, $query);
				header("location: index.php?page=AllCompanies");
			}
		}

		//Delete Company
		if($_POST['type'] == "DeleteCompany"){
			$ID = (int)$_POST['ID'];
			$active = (int)$_POST['active'];
			$query = "UPDATE companies SET active='".$active."' WHERE CompanyID='".$ID."';";
			$results = mysqli_query($db, $query);
			header("location: index.php?page=AllCompanies");
		}

		//Delete User
		if($_POST['type'] == "DeleteUser"){
			$ID = (int)$_POST['ID'];
			$active = (int)$_POST['active'];
			$query = "UPDATE users SET active='".$active."' WHERE ID='".$ID."';";
			$results = mysqli_query($db, $query);
			header("location: index.php?page=AllUsers");
		}

		//Delete Command
		if($_POST['type'] == "CompanyUpdateAll"){
			$ID = $_POST['ID'];
			$active = (int)$_POST['active'];
			$query = "UPDATE commands SET command='Deleted' WHERE ComputerID='".$ID."';";
			$results = mysqli_query($db, $query);
			header("location: index.php?page=AllCompanies");
		}

		//Create Note
		if(isset($_POST['note'])){
			$newnote = clean($_POST['note']);
			$query = "SELECT notes FROM users WHERE ID='".$_SESSION['userid']."'";
			$results = mysqli_query($db, $query);
			$oldnote = mysqli_fetch_assoc($results);
			$note = $oldnote['notes'].$newnote."|";
			$query = "UPDATE users SET notes='".$note."' WHERE ID='".$_SESSION['userid']."';";
			$results = mysqli_query($db, $query);
			header("location: index.php");
		}

		//Commands
		if($_POST['type'] == "SendCommand"){
			$ID = (int)$_POST['ID'];
			$commands = $_POST['command'];
			$args = $_POST['args'];
			$expire_after = (int)$_POST['expire_after'];
			$exists = 0;
			if(trim($commands)!=""){
				$query = "SELECT hostname FROM computerdata WHERE ID='".$ID."'";
				$results = mysqli_query($db, $query);
				$computer = mysqli_fetch_assoc($results);
				$query = "SELECT ID, expire_time FROM commands WHERE ComputerID='".$computer['hostname']."' AND status='Sent' AND command='".$commands."' AND userid='".$_SESSION['userid']."' ORDER BY ID DESC LIMIT 1";
				$results = mysqli_query($db, $query);
				$existing = mysqli_fetch_assoc($results);
				if($existing['ID'] != ""){
					if(strtotime(date("m/d/Y H:i:s")) <= strtotime($existing['expire_time'])){
						$exists = 1;
					}
				}
				if($exists == 0){
					//Generate expire time
					$expire_time = date("m/d/Y H:i:s", strtotime('+'.$expire_after.' minutes', strtotime(date("m/d/y H:i:s"))));
					$query = "INSERT INTO commands (ComputerID, userid, command, arg, expire_after, expire_time, status)
							  VALUES ('".$computer['hostname']."', '".$_SESSION['userid']."', '".$commands."', '".$args."', '".$expire_after."', '".$expire_time."', 'Sent')";
					$results = mysqli_query($db, $query);
				}
			}
			header("location: index.php?page=General");
		}

		//Update Company Agents
		if($_POST['type'] == "CompanyUpdateAll"){
			$ID = (int)$_POST['CompanyID'];
			$commands = "C:\\\\Open-RMM\\\\Update.bat";
			$args = "";
			$expire_after = 5;
			$exists = 0;
			$query = "SELECT ID, hostname FROM computerdata WHERE CompanyID='".$ID."' AND active='1'";
			$results = mysqli_query($db, $query);
			while($computer = mysqli_fetch_assoc($results)){
				$query = "SELECT ID, expire_time FROM commands WHERE ComputerID='".$computer['hostname']."' AND status='Sent' AND command='".$commands."' AND userid='".$_SESSION['userid']."' ORDER BY ID DESC LIMIT 1";
				$results = mysqli_query($db, $query);
				$existing = mysqli_fetch_assoc($results);
				if(isset($existing['ID'])){
					if(strtotime(date("m/d/Y H:i:s")) <= strtotime($existing['expire_time'])){
						$exists = 1;
					}
				}
				if($exists == 0){
					//Generate expire time
					$expire_time = date("m/d/Y H:i:s", strtotime('+'.$expire_after.' minutes', strtotime(date("m/d/y H:i:s"))));
					$query = "INSERT INTO commands (ComputerID, userid, command, arg, expire_after, expire_time, status)
							  VALUES ('".$computer['hostname']."', '".$_SESSION['userid']."', '".$commands."', '".$args."', '".$expire_after."', '".$expire_time."', 'Sent')";
					$results = mysqli_query($db, $query);
				}
			}
		}

		//Alert Config Modal
		if($_POST['type'] == "AlertSettings"){
			$alert_settings = "";
			$email = $_POST['alert_settings_email'];
			foreach($siteSettings['Alert Settings'] as $type=>$alert){
				foreach($alert as $option=>$options){
					 if(count($options) > 1){ //Contains Sub Options
						 foreach($options as $subOptionKey=>$subOptionValue){
							$keyName = $type."_".$option."_".$subOptionKey;
							$alert_settings .= $keyName.":".(int)$_POST['alert_settings_'.$keyName].",";
						}
					 }else{
						$keyName = $type."_".$option;
						$alert_settings .= $keyName.":".(int)$_POST['alert_settings_'.$keyName].",";
					 }
				}
			}
			$alert_settings = trim($alert_settings, ",");
			$query = "UPDATE users SET alert_settings='".$alert_settings."' WHERE ID='".$_SESSION['userid']."';";
			$results = mysqli_query($db, $query);
			if($results){
				echo '<script>window.onload = function() { pageAlert("Alert Settings", "Alert Settings Saved Successfully","Success"); };</script>';
			}
		}

		//Delete Version
		if(isset($_POST['version'])){
			$version = clean($_POST['version']);
			unlink("downloads/".$version);
			header("location: index.php?page=Versions");
		}

		//Get Site Settings
		if($_POST['type'] == "getSiteSettings"){
			exit(file_get_contents("Includes/config.php"));
		}

		//Save Site Settings
		if($_POST['type'] == "saveSiteSettings"){
			$settings = "$siteSettingsJson = '".trim($_POST['settings'])."';";
			$configFile = "Includes/config.php";
			file_put_contents($configFile, $settings);
			exit();
		}

		//Upload or download new agent file
		if(isset($_POST['agentFile']) or isset($_POST['companyAgent'])){
			$agentVersion = clean($_POST['agentVersion']);
			if($_POST['agentVersion']==""){
				$agentVersion= $siteSettings['general']['agent_latest_version'];
			}
			$company = $_POST['companyAgent'];
			$uploaddir = 'Includes/agentFiles/bin/';
			$uploaddir2 = 'Includes/update/Open-RMM.exe';
			$uploadfile = $uploaddir.$_FILES['agentUpload']['name'];
			$uploadfile2 = "Includes/agentFiles/bin/Open-RMM.exe";
			if($company==""){
				move_uploaded_file($_FILES['agentUpload']['tmp_name'], $uploadfile);
				copy($uploadfile2, $uploaddir2);
			}
			ini_set('max_execution_time', 600);
			ini_set('memory_limit','1024M');
			$myfile = fopen("Includes/agentFiles/company.txt", "w") or die("Unable to open file!");
			fwrite($myfile, $company);
			echo $rootPath = realpath('Includes/agentFiles/');
			$zip = new ZipArchive();
			$zip->open('Open-RMM('.$agentVersion.').zip', ZipArchive::CREATE | ZipArchive::OVERWRITE );
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
			foreach ($files as $name => $file){
				if (!$file->isDir()){
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($rootPath) + 1);
					$zip->addFile($filePath, $relativePath);
				}
			}
			$zip->close();
			copy("Open-RMM(".$agentVersion.").zip", "downloads/Open-RMM(".$agentVersion.").zip");
			unlink("Open-RMM(".$agentVersion.").zip");
			if($company==""){
				$query = "UPDATE general SET agent_latest_version='".$agentVersion."' WHERE ID='1';";
				$results = mysqli_query($db, $query);
				echo '<script>window.onload = function() { pageAlert("File Upload", "File Uploaded Successfully","Success"); };</script>';
			}else{
				echo '<script>window.onload = function() { pageAlert("File Upload", "Download Started For Company Agent","Default"); };</script>';
				header("location: ../../download/index.php?company=".$company);
			}
		}
	}

	//Get Stats
	$query = "SELECT CompanyID FROM companies where active='1'";
	$results = mysqli_query($db, $query);
	$companyCount = mysqli_num_rows($results);

	//Get active user count
	$query = "SELECT ID FROM users where active='1'";
	$results = mysqli_query($db, $query);
	$userCount = mysqli_num_rows($results);

	//Get active computer count
	$query = "SELECT ID FROM computerdata where active='1'";
	$results = mysqli_query($db, $query);
	$resultCount = mysqli_num_rows($results);
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Open-RMM | Management</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!--- Font Awesome --->
		<link rel="stylesheet" href="css/all.min.css"/>
		<script src="js/all.min.js"></script>
		<script src="https://cdn.tiny.cloud/1/kdsnrszwoxdwc9f80ckwo8skp7ltlz1io93n9l8a9j2hrkvb/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><!--- REMOVE CDN, KEEP LOCAL! -->
		<!-- jquery-->
		<script src="js/tagsinput.js"></script>
		<script src="js/jquery.js"></script>
		<!--- Bootstap --->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script><!--- REMOVE CDN, KEEP LOCAL! -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css"/><!--- REMOVE CDN, KEEP LOCAL! -->
		<link rel="stylesheet" href="css/tagsinput.css"/>
		<link rel="stylesheet" href="css/bootstrap.min.css"/>
		<script src="js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="css/custom.css"/>
		<style>
			a { color:#003366; }
			.calert { margin-left:5px;font-size:12px;width:46%;margin-right:5px;float:left;min-height:60px; }
			@media screen and (max-width: 850px) {
				.calert { height: 120px; }
				.headall { display: none; }
			}
		</style>
	</head>
	<body style="background-color:#f7f8fa;height:100%;">
		<div style="background-color:<?php echo $siteSettings['theme']['Color 1'];?>;color:#fff;text-align:center;padding:10px;padding-left:20px;position:fixed;top:0px;width:100%;z-index:99;">
			<h5>
				<div style="float:left;">
					<button type="button" style="background:#e6e7e8;border:2px solid #e6e7e8;" class="btn-sm sidebarCollapse btn btn-light" title="Show/Hide Sidebar">
						<i class="fas fa-align-left"></i>
					</button>
					<button type="button" id="secbtnSiteSettings" onclick="loadSection('SiteSettings');" style="background:#e6e7e8;border:2px solid #e6e7e8;box-shadow:none;" class="btn-sm secbtn btn btn-light" title="Site Settings">
						<i class="fas fa-cog"></i>
					</button>
				</div>
				<div style="float:right;">
					<button type="button" style="background:#e6e7e8;border:2px solid #e6e7e8;" data-toggle="modal" data-target="#alertModal" class="btn-sm btn btn-light" title="Configure Alerts">
						&nbsp;<i class="fas fa-bell"></i>&nbsp;
					</button>
					<button type="button" style="background:#e6e7e8;border:2px solid #e6e7e8;box-shadow:none;" onclick="loadSection('NewComputers');" class="secbtn btn btn-sm btn-light" id="secbtnNewComputers" title="View New Computers">
						<i class="fas fa-desktop"></i> <i class="fas fa-eye"></i>
					</button>
				</div>
			</h5>
		</div>
		<div class="wrapper">
			<!-- Sidebar -->
			<nav style="overflow:auto;width:200px;" id="sidebar">
				<ul class="list-unstyled components" style="padding:20px;margin-top:25px;">
					<li onclick="loadSection('Dashboard');" id="secbtnDashboard" class="secbtn">
						<i style="font-size:16px;float:left;" class="fas fa-home"></i> Dashboard
					</li>
					<li onclick="loadSection('AllCompanies');" id="secbtnAllCompanies" class="secbtn">
						<i style="font-size:16px;float:left;" class="fas fa-briefcase"></i> Companies
					</li>
					<li onclick="loadSection('AllUsers');" id="secbtnAllUsers" class="secbtn">
						<i style="font-size:16px;float:left;" class="fas fa-users"></i> Users
					</li>
					<hr>
					<div id="sectionList" style="display:none;">
						<h5 class="sidebarComputerName" style="text-align:center;"></h5>
						<li onclick="loadSection('Edit');" id="secbtnEdit" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-pencil-alt"></i> Edit Computer
						</li>
						<li onclick="loadSection('General');" id="secbtnGeneral" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-edit"></i> General
						</li>
						<li onclick="loadSection('Network');" id="secbtnNetwork" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-network-wired"></i> Network
						</li>
						<li onclick="loadSection('Programs');" id="secbtnPrograms" class="secbtn">
							<i style="font-size:16px;float:left;" class="fab fa-app-store-ios"></i> Programs
						</li>
						<li onclick="loadSection('DefaultPrograms');" id="secbtnDefaultPrograms" class="secbtn">
							<i style="font-size:16px;float:left;" class="fab fa-app-store-ios"></i> Default Programs
						</li>
						<li onclick="loadSection('Services');" id="secbtnServices" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-cogs"></i> Services
						</li>
						<li onclick="loadSection('Proccesses');" id="secbtnProccesses" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-microchip"></i> Processes
						</li>
						<li onclick="loadSection('Printers');" id="secbtnPrinters" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-edit"></i> Printers
						</li>
						<li onclick="loadSection('Disks');" id="secbtnDisks" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-hdd"></i> Disks
						</li>
						<li onclick="loadSection('Memory');" id="secbtnMemory" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-memory"></i> Memory
						</li>
						<li onclick="loadSection('AttachedDevices');" id="secbtnAttachedDevices" class="secbtn">
							<i style="font-size:16px;float:left;" class="fab fa-usb"></i>Attached Devices
						</li>
						<li onclick="loadSection('OptionalFeatures');" id="secbtnOptionalFeatures" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-list"></i> Optional Features
						</li>
						<li onclick="loadSection('Users');" id="secbtnUsers" class="secbtn">
							<i style="font-size:16px;float:left;" class="fas fa-users"></i> User Accounts
						</li>
						<li></li>
					</div>
					<a href="logout.php" style="font-size:14px;color:#fff;">
						<li style="background:maroon;color:#fff;border-radius:6px;">
							<i style="font-size:16px;float:left;" class="fas fa-sign-out-alt"></i> Logout, <?php print_r($poste); echo ucwords($user['username']);?>
						</li>
					</a>
					<hr>
					<div class="recents" id="recents" style="margin-top:20px;"><!-- Load for ajax/recents.php --></div>
					<div style="margin-top:20px;text-align:center;" class="notes">
							<h6>
								<b>Notes</b>
								<button data-toggle="modal" data-target="#noteModal" title="Create New Note" style="float:right;margin-top:-5px;" class="btn btn-sm">
									<i class="fas fa-edit"></i>
								</button>
							</h6>
							<?php
							$count = 0;
							$query = "SELECT ID, notes FROM users where ID='".$_SESSION['userid']."'";
							$results = mysqli_query($db, $query);
							$data = mysqli_fetch_assoc($results);
							$notes = $data['notes'];
							if($notes!=""){
								$allnotes = explode("|",$notes);
								foreach(array_reverse($allnotes) as $note) {
									if($note==""){ continue; }
									if($count>=5){ break; }
									$count++;
								?>
									<li style="font-size:14px;cursor:default" class="bg-light list-group-item">
										<i style="float:left;font-size:16px;" class="far fa-sticky-note"></i>
										<?php echo ucwords($note);?>
									</li>
								<?php }?>
							<?php }?>
							<?php if($count==0){ ?>
								<li class="list-group-item">No Notes</li>
							<?php } ?>
					</div>
				</ul>
				<div style="height:500px;">&nbsp;</div>
			</nav>
			<!-- Page Content -->
			<div id="content" style="margin-top:15px;padding:30px;width:100%;">
				<div class="row">
					<div style="background-color:<?php echo $siteSettings['theme']['Color 2'];?>;padding:10px;height:50px;color:#fff;font-size:20px;text-align:center;" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<a style="color:#fff;cursor:pointer;" onclick="loadSection('Dashboard');">
							<div>
								<i class="fas fa-desktop" style="font-size:28px;float:left;"></i>
								<b><?php echo $resultCount; ?></b>
								<span style="font-size:20px;"><b>Computers</b></span>
							</div>
						</a>
					</div>
					<div style="background-color:<?php echo $siteSettings['theme']['Color 3'];?>;padding:10px;height:50px;color:#fff;font-size:20px;text-align:center;" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<a style="color:#fff;cursor:pointer;" onclick="loadSection('AllCompanies');">
							<div>
								<i class="fas fa-building" style="font-size:28px;float:left;"></i>
								<b><?php echo $companyCount;?></b>
								<span style="font-size:20px;"><b>Companies</b></span>
							</div>
						</a>
					</div>
					<div style="background-color:<?php echo $siteSettings['theme']['Color 4'];?>;padding:10px;height:50px;color:#fff;font-size:20px;text-align:center;" class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
						<a style="color:#fff;cursor:pointer;" onclick="loadSection('AllUsers');">
							<div>
								<i class="fas fa-user" style="font-size:28px;float:left;"></i>
								<b><?php echo $userCount;?></b>
								<span style="font-size:20px;"><b>Users</b></span>
							</div>
						</a>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:20px;">
						<div class="loadSection">
							<!------ Loads main data from jquery ------>
							<center>
								<h1 style="margin-top:40px;">
									<i class="fas fa-spinner fa-spin"></i>
								</h1>
							</center>
						</div>
						<div style="height:50px;" class="clearfix">&nbsp;</div>
					</div>
					<!-- Footer -->
					<footer style="z-index:999;padding:5px;height:30px;position:fixed;left:0;bottom:0;width:100%;color:#fff;text-align:center;background:<?php echo $siteSettings['theme']['Color 1'];?>;" class="page-footer font-small black">
						<div class="footer-copyright text-center">© <?php echo date('Y');?> Copyright
							<a style="color:#fff;" target="_blank" href="#"> Open-RMM</a>
							<a style="font-size:12px;cursor:pointer;float:left;padding-right:10px;color:#fff" onclick="loadSection('Versions');"><u>Previous Agent Versions</u></a>
						</div>
					</footer>
				</div>
			</div>
		</div>

		<!-------------------------------MODALS------------------------------------>
		<!--------------- Configure Alerts Modal ------------->
		<div id="alertModal" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
			<div class="modal-content" >
			  <div class="modal-header">
				<h6 class="modal-title"><b>Configure Alerts</b></h6>
			  </div>
			  <form method="POST">
				  <input type="hidden" name="type" value="AlertSettings"/>
				  <div class="modal-body">
					<p>Get notified when computers go offline, have low memory and more.</p>
					<div class="row">
						<div class="col-sm-6" style="padding:5px;">
							<?php
							$count = 0;
							foreach($siteSettings['Alert Settings'] as $type=>$alert){
								$count++;
								if($count % 2 == 0){continue;}else{}
							?>
								<div class="card" style="margin-bottom:5px;padding:5px;">
								   <div class="form-gsroup">
									<h6><b><?php echo $type; ?>:</b></h6>
									<div style="font-size:12px" class="col-sm-offset-2 col-sm-10">
										<?php foreach($alert as $option=>$options){ ?>
											<?php if(count($options) > 1){ //Contains Sub Options?>
												<b><?php echo $option;?></b>
												<?php foreach($options as $subOptionKey=>$subOptionValue){ ?>
													<div class="checkbox" style="margin-left:15px;font-size:12px;">
														<label>
															<input type="checkbox" name="alert_settings_<?php echo $type."_".$option."_".$subOptionKey;?>" value="1"> <?php echo $subOptionKey; ?>
														</label>
													</div>
												<?php }?>
											<?php }else{?>
												<div class="checkbox"style="font-size:12px;">
													<label>
														<input type="checkbox" name="alert_settings_<?php echo $type."_".$option;?>" value="1"> <?php echo $option; ?>
													</label>
												</div>
											<?php }?>
										<?php }?>
									</div>
								  </div>
								</div>
							<?php } ?>
						</div>
						<div class="col-sm-6" style="padding:5px;">
							<?php
							$count = 0;
							foreach($siteSettings['Alert Settings'] as $type=>$alert){
								$count++;
								if($count % 2 == 0){  }else{continue;}
							?>
								<div class="card" style="margin-bottom:5px;padding:5px;">
								   <div class="form-gsroup">
									<h6><b><?php echo $type; ?>:</b></h6>
									<div class="col-sm-offset-2 col-sm-10">
										<?php foreach($alert as $option=>$options){ ?>
											<?php if(count($options) > 1){ //Contains Sub Options?>
												<b><?php echo $option;?></b>
												<?php foreach($options as $subOptionKey=>$subOptionValue){ ?>
													<div class="checkbox" style="margin-left:15px;font-size:12px;">
														<label>
															<input type="checkbox" name="alert_settings_<?php echo $type."_".$option."_".$subOptionKey;?>" value="1"> <?php echo $subOptionKey; ?>
														</label>
													</div>
												<?php }?>
											<?php }else{?>
												<div class="checkbox" style="font-size:12px;">
													<label>
														<input type="checkbox" name="alert_settings_<?php echo $type."_".$option;?>" value="1"> <?php echo $option; ?>
													</label>
												</div>
											<?php }?>
										<?php }?>
									</div>
								  </div>
								</div>
							<?php } ?>
						</div>
					</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
					<button type="submit" style="background:<?php echo $siteSettings['theme']['Color 2'];?>;color:#fff;" class="btn btn-sm">
						<i class="fas fa-check"></i> Save
					</button>
				  </div>
			 </form>
			</div>
		  </div>
		</div>

		<!--------------- User Modal ------------->
		<div id="userModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h6><b>Add/Edit User</b></h6>
			  </div>
			  <form id="user" method="POST">
				  <input type="hidden" name="type" value="AddEditUser"/>
				  <input type="hidden" name="ID" id="editUserModal_ID"/>
				  <div class="modal-body">
					<p>This will configure a new user and will allow them access to this platform.</p>
					<div class="form-group">
						<input placeholder="Name" type="text" name="name" class="form-control" id="editUserModal_name"/>
					</div>
					<div class="form-group">
						<input placeholder="Email"  type="email" name="email" class="form-control" id="editUserModal_email"/>
					</div>
					<div class="form-group">
						<input placeholder="Username"  required type="text" name="username" class="form-control" id="editUserModal_username"/>
					</div>
					<div class="input-group">
						<input placeholder="Password" style="display:inline;" type="password" id="editUserModal_password" name="password" class="form-control"/>
						  <span class="input-group-btn">
							<a style="border-radius:0px;padding:6px;pointer:cursor;background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" class="btn btn-md" onclick="generate();" >Generate</a>
					   </span>
					</div>		<br>
					<div class="form-group">
						<input placeholder="Confirm Password" type="password" id="editUserModal_password2" name="password2" class="form-control"/>
					</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
					<button type="submit" style="background:<?php echo $siteSettings['theme']['Color 5'];?>;color:#fff;" class="btn btn-sm">
						<i class="fas fa-check"></i> Save
					</button>
				  </div>
			  </form>
			</div>
		  </div>
		</div>

		<!--------------- Version Modal ------------->
		<div id="versionModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h6><b>Delete Version</b></h6>
			  </div>
			  <form id="user" method="POST">
				  <input type="hidden" name="version" value="" id="delVersion_ID"/>
				  <div class="modal-body">
					<p>This will delete this agent version. Are you sure?</p>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" style="color:#fff" class="btn btn-sm btn-danger">
						<i class="fas fa-trash"></i> Delete
					</button>
				  </div>
			  </form>
			</div>
		  </div>
		</div>

		<!--------------- Version Modal ------------->
		<div id="noteModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h6><b>Create A New Note</b></h6>
			  </div>
			  <form id="note" method="POST">
				  <div class="modal-body">
					<p>This will create a new note that only you can see.</p>
					<textarea required maxlength="300" name="note" class="form-control"></textarea>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" style="background:<?php echo $siteSettings['theme']['Color 5'];?>;color:#fff;" class="btn btn-sm">
						<i class="fas fa-save"></i> Save
					</button>
				  </div>
			  </form>
			</div>
		  </div>
		</div>

		<!---------- Company Modal ------------>
		<div id="companyModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h6><b>Add/Edit Company</b></h6>
			  </div>
			  <form method="POST">
				  <input type="hidden" name="type" value="AddEditCompany"/>
				  <input type="hidden" name="ID" value="" id="editCompanyModal_ID"/>
				  <div class="modal-body">
					<p>This will add company information to better assist and organize content.</p>
					<div class="form-group">
						<input placeholder="Name" type="text" name="name" class="form-control" id="editCompanyModal_name"/>
					</div>
					<div class="form-group">
						<input placeholder="Address" type="text" name="address" class="form-control" id="editCompanyModal_address"/>
					</div>
					<div class="form-group">
						<input placeholder="Phone" type="phone" name="phone" class="form-control" id="editCompanyModal_phone"/>
					</div>
					<div class="form-group">
						<input placeholder="Email" type="email" name="email" class="form-control" id="editCompanyModal_email"/>
					</div>
					<div class="form-group">
						<textarea placeholder="Additional Info" style="resize:vertical;" name="comments"
							class="form-control" placeholder="Optional" id="editCompanyModal_comments"></textarea>
					</div>
				  </div>
				  <div class="modal-footer">
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
					<button type="submit" style="background:<?php echo $siteSettings['theme']['Color 5']; ?>;color:#fff;" class="btn btn-sm">
						<i class="fas fa-check"></i> Save
					</button>
				  </div>
			  </form>
			</div>
		  </div>
		</div>

		<!----------- Terminal ---------------->
		<div id="terminalModal" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
			<div class="modal-content">
			  <div class="modal-header">
				<h6><b>Terminal</b></h6>
			  </div>
			  <div class="modal-body" style="background-color:#000;color:#fff;font-family: 'Courier New', Courier, monospacepadding:20px;">
				<div style="max-height:400px;margin-bottom:10px;min-height:100px;overflow:auto;">
					<div id="terminalResponse" style="color:#fff;font-family:font-family:monospace;">
						Microsoft Windows [Version 10.0.<?php echo rand(100000,9999999);?>]<br/>
						(c) <?php echo date("Y");?> Microsoft Corporation. All rights reserved.
						<br/><br/>
					</div>
				</div>
				<div style="min-height:50px;">
					<?php echo strtoupper($data['hostname']);?>> <input type="text" id="terminaltxt" style="outline:none;border:none;background:#000;width:300px;color:#fff;font-family:font-family:monospace;"/>
				</div>
			  </div>
			</div>
		  </div>
		</div>

		<!------------- Alerts ------------------->
		<div id="confirm" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
			<div class="modal-content">
			  <div class="modal-header">
				<h6 id="computerAlertsHostname"><b>Confirm Action</b></h6>
			  </div>
			  <div class="modal-body">
				<p>Are you sure you would like to complete this action?</p>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" data-dismiss="modal">
					Close
				</button>
				<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;" data-dismiss="modal">Confirm</button>
			  </div>
			</div>
		  </div>
		</div>

		<!------------- Alerts ------------------->
		<div id="computerAlerts" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
			<div class="modal-content">
			  <div class="modal-header">
				<h6 id="computerAlertsHostname"><b>Alerts</b></h6>
			  </div>
			  <div class="modal-body">
				<div  id="computerAlertsModalList"></div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;" data-dismiss="modal">
					Close
				</button>
			  </div>
			</div>
		  </div>
		</div>

		<!------------- Page Errors ------------------->
		<div id="pageAlert" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title" id="pageAlert_title">Message From Webpage</h5>
				<h6 id="pageAlert_title"><b>Message from webpage</b></h6>
			  </div>
			  <div class="modal-body">
				<div id="pageAlert_message" class="alert">No Message</div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;" data-dismiss="modal">Close</button>
			  </div>
			</div>
		  </div>
		</div>

		<!------------- Historical ------------------->
		<div id="historicalData_modal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title">Historical Data</h5>
			  </div>
			  <div class="modal-body">
				<div id="historicalData" style="overflow:auto;max-height:400px;"></div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;" data-dismiss="modal">
					Close
				</button>
			  </div>
			</div>
		  </div>
		</div>

		<!------------- Historical Date Selection  ------------------->
		<div id="historicalDateSelection_modal" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title">Historical Data</h5>
			  </div>
			  <div class="modal-body" style="overflow:auto;max-height:400px;">
				<table class="table table-striped">
					<tr>
						<td>Latest</td>
						<td>
							<button type="button" onclick="loadSectionHistory();" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 1']; ?>;color:#fff;">
								Select
							</button>
						</td>
					</tr>
					<?php
						$showLast = $siteSettings['Max_History_Days']; //Show last 31 days
						$count = 0;
						while($count <= $showLast){ $count++;
						 $niceDate = date("l, F jS", strtotime("-".$count." day"));
						 $formatedDate = date("n/j/Y", strtotime("-".$count." day"));
					?>
						<tr>
							<td><?php echo $niceDate; ?></td>
							<td>
								<button type="button" onclick="loadSectionHistory('<?php echo $formatedDate;?>');" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 1']; ?>;color:#fff;">Select</button>
							</td>
						</tr>
					<?php }?>
				</table>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;" data-dismiss="modal">Close</button>
			  </div>
			</div>
		  </div>
		</div>

		<!------------- Actions ------------------->
		<div id="actionsModal" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-md">
			<div class="modal-content">
			  <div class="modal-header">
				<h5 class="modal-title">Actions</h5>
			  </div>
			  <div class="modal-body">
				<p>This will send a command to the selected client</p>
				<hr>
				<button class="btn btn-sm" data-dismiss="modal" type="button" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;width:47%;border:none;" data-toggle="modal" data-target="#terminalModal">
					<i class="fas fa-terminal" style="margin-top:3px;float:left;"></i> Terminal
				</button>
				<hr>
				<div>
					<button data-dismiss="modal" class="btn btn-primary btn-sm" type="button" style="display:inline;margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;width:47%;border:none;" onclick='sendCommand("reg", "add \"HKEY_LOCAL_MACHINE\\\\SYSTEM\\\\CurrentControlSet\\\\Control\\\\Terminal Server\" /v fDenyTSConnections /t REG_DWORD /d 0 /f", "Enable Remote Desktop");'>
						<i class="fas fa-desktop" style="float:left;margin-top:3px;"></i> Enable Remote Desktop
					</button>
					<button data-dismiss="modal" class="btn btn-danger btn-sm" type="button" style="display:inline;margin:5px;color:#fff;width:47%;border:none" onclick='sendCommand("reg", "add \"HKEY_LOCAL_MACHINE\\\\SYSTEM\\\\CurrentControlSet\\\\Control\\\\Terminal Server\" /v fDenyTSConnections /t REG_DWORD /d 1 /f", "Disable Remote Desktop");'>
						<i class="fas fa-desktop" style="float:left;margin-top:3px;"></i> Disable Remote Desktop
					</button>
				</div>
				<hr>
				<div>
					<button data-dismiss="modal" class="btn btn-primary btn-sm" type="button" style="display:inline;margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;width:47%;border:none;" onclick="sendCommand('Netsh', 'Advfirewall set allprofiles state on', 'Enable Firewall');">
						<i class="fas fa-fire-alt" style="float:left;margin-top:3px;"></i> Enable Firewall
					</button>
					<button data-dismiss="modal" class="btn btn-danger btn-sm" type="button" style="display:inline;margin:5px;color:#fff;width:47%;border:none" onclick="sendCommand('Netsh', 'Advfirewall set allprofiles state off', 'Disable Firewall');">
						<i class="fas fa-fire-alt" style="float:left;margin-top:3px;"></i> Disable Firewall
					</button>
				</div>
			  </div>
			  <div class="modal-footer">
				<button data-dismiss="modal" title="Show Existing Commands" onclick="loadSection('Commands');" class="btn btn-sm" style="float:left;color:#fff;background:<?php echo $siteSettings['theme']['Color 3'];?>;">
					<i class="fas fa-eye"></i>&nbsp;All Commands
				</button>
				<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 1']; ?>;color:#fff;" data-dismiss="modal">Close</button>
			  </div>
			</div>
		  </div>
		</div>

		<!------------- Upload .exe File ------------------->
		<div id="agentUpload" class="modal fade" role="dialog">
		  <div class="modal-dialog modal-lg">
			<div class="modal-content">
			  <div class="modal-header">
				<h6><b>Upload New Agent (.exe)</b></h6>
			  </div>
			 <form enctype="multipart/form-data" method="POST">
			  <div class="modal-body">
			  <p>This Will Create A Downloadable .Zip File. It Will Also Rewrite The Existing Update Directory.</p>
				<div class="input-group">
				  <div class="input-group-prepend">
					<span class="input-group-text">Agent Version</span>
				  </div>
				  <input type="text" name="agentVersion" required minlength=7 class="form-control" placeholder="ex. 1.0.0.4" value="<?php echo $siteSettings['general']['agent_latest_version']; ?>"/>&nbsp;
				  <div class="input-group-prepend">
					<span class="input-group-text">Upload .exe</span>
				  </div>
				  <div class="custom-file">
				    <input required="" type="hidden" value="true" name="agentFile">
					<input required="" accept=".exe" type="file" name="agentUpload" class="custom-file-input" id="agentUpload"/>
					<label class="custom-file-label" for="agentUpload">Choose file</label>
				  </div>
				</div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-sm"  data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;">Upload</button>
			  </div>
			</form>
			</div>
		  </div>
		</div>
		<!---------------------------------End MODALS------------------------------------->
	</body>

	<script src="js/extra.js"></script>
	<script>
		//Load Page
		if (document.cookie.indexOf('section') === -1 ) {
			setCookie("section", "Dashboard", 365);
		}
		var computerID = getCookie("ID");
		var currentSection = getCookie("section");
		var sectionHistoryDate = "latest";

		//Load Pages
		function loadSection(section=currentSection, ID=computerID, date=sectionHistoryDate){
			document.cookie = "section=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			document.cookie = "ID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
			setCookie("section", section, 365);
			setCookie("ID", ID, 365);
			computerID = ID;
			currentSection = section;
			$(".loadSection").html("<center><h3 style='margin-top:40px;'><i class='fas fa-spinner fa-spin'></i> Loading</h3></center>");
			$(".loadSection").load("ajax/"+section+".php?ID="+ID+"&Date="+date);
			$(".secbtn").css({"background-color":"#e6e7e8", "color":"#000"});

			$("#secbtn"+section).css({"background-color":"<?php echo $siteSettings['theme']['Color 1'];?>","color":"#fff"});
			$(".recents").load("ajax/recent.php?ID="+ID);
			if(section == "Dashboard" || section == "AllUsers" || section == "AllCompanies" || section == "NewComputers" || section == "Versions" || section == "SiteSettings"){
				$('#sectionList').slideUp(400);
			}else if($('#sectionList').css("display")=="none"){
				$('#sectionList').slideDown(400);
			}
			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
				$('#sidebar').removeClass('active');
			}
		}

		//Load historical section, Network, Programs...
		function loadSectionHistory(date="latest"){
			sectionHistoryDate = date;
			$(".loadSection").html("<center><h3 style='margin-top:40px;'><i class='fas fa-spinner fa-spin'></i> Loading</h3></center>");
			$(".loadSection").load("ajax/"+currentSection+".php?ID="+computerID+"&Date="+date);
			$("#historicalDateSelection_modal").modal("hide");
		}

		<?php if($_GET['page']==""){ ?>
			loadSection(currentSection, computerID);
		<?php }else{ ?>
			loadSection("<?php echo ucfirst($_GET['page']);?>", "<?php echo (int)$_GET['ID'];?>");
		<?php }?>

		//Sidebar
		$(document).ready(function () {
			$('.sidebarCollapse').on('click', function () {
				$('#sidebar').toggleClass('active');
			});
		});

		//Search
		function search(text, page="Dashboard", ID=0, filters="", limit=25){
			$('body').removeClass('modal-open');
			$('.modal-backdrop').remove();
			$(".loadSection").html("<center><h3 style='margin-top:40px;'><i class='fas fa-spinner fa-spin'></i> Loading Results</h3></center>");
			$(".loadSection").load("ajax/"+page+".php?limit="+limit+"&search="+encodeURI(text)+"&ID="+ID+"&filters="+encodeURI(filters)+"&Date="+sectionHistoryDate);
		}

		//Terminal
		$('#terminaltxt').keypress(function(event){
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				$("#terminalResponse").html("Sending Command: "+$('#terminaltxt').val()+" <i class='fas fa-spinner fa-spin'></i>");
				$.post("ajax/terminal.php", {
				  id: computerID,
				  command: $('#terminaltxt').val()
				},
				function(data, status){
				  $("#terminalResponse").html(data);
				});
			}
		});

		//Alerts Modal
		function computerAlertsModal(title, delimited='none', showHostname = false){
			$("#computerAlertsHostname").html("<b>Alerts for "+title+"</b>");
			if(delimited=="none"){
				$("#computerAlertsModalList").html("<div class='alert alert-success' style='font-size:12px' role='alert'><b><i class='fas fa-thumbs-up'></i> No Issues</b></div>");
				return;
			}
			$("#computerAlertsModalList").html("")
			var alerts = delimited.split(",");
			var hostname = "";
			for(alert in alerts){
				var alertData = alerts[alert].split("|");
				if(alertData[0].trim()==""){
					continue;
				}
				if(showHostname == true){
					hostname = alertData[3];
				}
				$("#computerAlertsModalList").html($("#computerAlertsModalList").html() + "<div class='calert alert alert-"+alertData[2]+"' role='alert'><b><i class='fas fa-exclamation-triangle text-"+alertData[2]+"'></i> "+ hostname + " " + alertData[0]+"</b> - " + alertData[1] + "</div>");
			}
		}

		//Random password
		function randomPassword(length) {
			var chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+<>ABCDEFGHIJKLMNOP1234567890";
			var pass = "";
			for (var x = 0; x < length; x++) {
				var i = Math.floor(Math.random() * chars.length);
				pass += chars.charAt(i);
			}
			return pass;
		}

		//Set random passwords to inputs
		function generate() {
			var pass = randomPassword(8);
			$('#editUserModal_password').prop('type', 'text').val(pass);
			$('#editUserModal_password2').prop('type', 'text').val(pass);
		}

		//Page Alerts, replaces alert()
		function pageAlert(title, message, type="Default"){
			var types = {Default:"alert-primary", Success:"alert-success", Warning:"alert-warning", Danger:"alert-danger"};
			if(title.trim() == ""){
				title = "Message From Webpage";
			}
			$("#pageAlert_message").removeClass().addClass("alert").addClass(types[type]);
			$("#pageAlert").modal("show");
			$("#pageAlert_title").text(title);
			$("#pageAlert_message").html(message);
		}

		//Load Historical Data
		function loadHistoricalData(hostname, type){
			$("#historicalData").html("<center><h3 style='margin-top:40px;'><i class='fas fa-spinner fa-spin'></i></h3></center>");
			$("#historicalData_modal").modal("show");
			$.post("ajax/LoadHistorical.php", {
			  hostname: hostname,
			  type: type
			},
			function(data, status){
			  $("#historicalData").html(data);
			});
		}

		//Send CMD Command to agent
		function sendCommand(command, args, prompt, expire_after=5){
			if(confirm("Are you sure you would like to "+prompt+"?")){
				$.post("index.php", {
				  type: "SendCommand",
				  ID: computerID,
				  command: command,
				  args: args,
				  expire_after: expire_after
				},
				function(data, status){
					$("#pageAlert").modal("show");
					$("#pageAlert_title").text("Request Action");
					$("#pageAlert_message").html("Your Request Has Been Sent.");
				});
			}
		}

		//Toggle ?
		function toggle(source) {
		  checkboxes = document.getElementsByName('computers[]');
		  for(var i=0, n=checkboxes.length;i<n;i++) {
		    checkboxes[i].checked = source.checked;
		  }
		}
		<?php
			if($_SESSION['showModal']=="true" && 1==1){
				$title = "New Ideas/Bug Fixes";
				$message = "Add Ransomware Detector In .NET";
				$message .= "<br>Get Remote Desktop Status<br>Exclude Computer From Alerts<br>Site Alert For Conflicting Hostnames. <br>Alert if .pdf is not set to Adobe, only if Adobe installed<br>New Program Installed/Removed<br><br> Version 1.0.1.6, updates are broke";
				//show modal once after login
				echo 'pageAlert("'.$title.'", "'.$message.'");';
				$_SESSION['showModal'] = "";
			}
		?>
	</script>
</html>