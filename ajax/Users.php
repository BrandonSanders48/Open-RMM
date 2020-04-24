<?php
	include("../Includes/db.php");
	$computerID = (int)$_GET['ID'];
	$showDate = $_GET['Date'];

	$query = "SELECT hostname FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$result = mysqli_fetch_assoc($results);
	$exists = (bool)mysqli_num_rows($results);

	if(!$exists){ exit("<br><center><h4>No Computer Selected</h4><p>To Select A Computer, Please Visit The <a class='text-dark' href='index.php'><u>Dashboard</u></a></p></center><hr>"); }

	$json = getComputerData($result['hostname'], array("WMI_UserAccount"), $showDate);

?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">
	User Accounts (<?php echo count($json['WMI_UserAccount']);?>)
</h4>
<?php if($showDate == "latest"){?>
	<span style="font-size:12px;color:#666;">
		- Last Update: <?php echo ago($json['WMI_UserAccount_lastUpdate']);?>
	</span>
<?php }else{?>
	<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		History: <?php echo date("l, F jS g:i A", strtotime($json['WMI_UserAccount_lastUpdate']));?>
	</span>
<?php }?>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('Users');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<a href="#" title="Select Date" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		<i class="far fa-calendar-alt"></i>
	</a>
</div>

<hr/>
<div class="row">
	<?php
		$users = $json['WMI_UserAccount'];
		$error = $json['WMI_UserAccount_error'];
		foreach($users as $user){
	?>
		<div class="col-md-3" style="margin-left:-15px;">
			<div style="height:100%;">

				<table style="font-size:14px;" class="table table-striped table-hover">
					<tbody>
						<tr class="bg-dark" style="color:#fff;">
						  <!--<th>Username</th>-->
						  <th colspan=2><b><?php echo textOnNull(ucfirst($user['Name']), "N/A");?></b></th>
						</tr>
						<tr>
						  <th>Full Name</th>
						  <td><?php echo textOnNull($user['FullName'], "N/A");?></td>
						</tr>
						<tr>
						  <th>Disabled</th>
						  <td><?php echo $user['Disabled'];?>
							<?php if($user['Disabled']=="True"){ ?>
								<button onclick='sendCommand("net", "user <?php echo $user['Name']; ?> /active:yes", "Enable The Account For <?php echo $user['Name']; ?>");' style="float:right" title="Enable User?" class="btn btn-sm btn-success"><i class="fas fa-toggle-on"></i></button>
							<?php }else{ ?>
								<button onclick='sendCommand("net", "user <?php echo $user['Name']; ?> Passw0rd!", "Reset Password For <?php echo $user['Name']; ?>");' style="float:right;margin-left:5px;" title="Reset To Simple Password?" class="btn btn-sm btn-primary"><i class="fas fa-star-of-life"></i></button>&nbsp;
								<button onclick='sendCommand("net", "user <?php echo $user['Name']; ?> /active:no", "Disable The Account For <?php echo $user['Name']; ?>");' style="float:right" title="Disable User?" class="btn btn-sm btn-danger"><i class="fas fa-toggle-off"></i></button>
							<?php } ?>
						  </td>
						</tr>
						<tr>
						  <th>Password</th>
						  <td><?php echo textOnNull($user['PasswordRequired'], "N/A");?></td>
						</tr>
						<tr>
						  <th>Local</th>
						  <td><?php echo textOnNull($user['LocalAccount'], "N/A");?></td>
						</tr>
						<tr>
						  <th>Domain</th>
						  <td><?php echo textOnNull($user['Domain'], "N/A");?></td>
						</tr>
						<tr>
						  <th>Description</th>
						  <td><?php echo textOnNull($user['Description'], "None");?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<?php }
		if(count($users) == 0){ ?>
			<div class="col-md-12" style="padding:5px;">
				<center><h5>No Users found.</h5></center>
			</div>
	<?php }?>
</div>