<?php
	include("../Includes/db.php");
	
	$computerID = (int)$_GET['ID'];
	$showDate = $_GET['Date'];

	$query = "SELECT hostname FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$result = mysqli_fetch_assoc($results);
	$exists = (bool)mysqli_num_rows($results);

	if(!$exists){ exit("<br><center><h4>No Computer Selected</h4><p>To Select A Computer, Please Visit The <a class='text-dark' href='index.php'><u>Dashboard</u></a></p></center><hr>"); }

	$json = getComputerData($result['hostname'], array("WMI_NetworkAdapters"), $showDate);

	$adapters = $json['WMI_NetworkAdapters'];
	$error = $json['WMI_NetworkAdapters_error'];

?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">
	Network Adapters (<?php echo count($adapters);?>)
</h4>
<?php if($showDate == "latest"){?>
	<span style="font-size:12px;color:#666;">
		- Last Update: <?php echo ago($json['WMI_NetworkAdapters_lastUpdate']);?>
	</span>
<?php }else{?>
	<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		History: <?php echo date("l, F jS g:i A", strtotime($json['WMI_NetworkAdapters_lastUpdate']));?>
	</span>
<?php }?>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('Network');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<a href="#" title="Select Date" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		<i class="far fa-calendar-alt"></i>
	</a>
</div>
<hr/>

<div class="row">
	<?php
		foreach($adapters as $key=>$adapter){
	?>
	<div class="col-md-4" style="padding:5px;">
		<div class="card" style="height:95%">
		  <div>
			<table style="line-height:20px;overflow:hidden;font-size:14px;" class="table table-striped table-hover">
				<tbody>
					<tr class="bg-dark" style="color:#fff;">
						<th colspan=2><b><?php echo textOnNull($adapter['Description'],"None");?></b></th>
					</tr>
					<tr>
						<th>DHCP</th>
						<td><?php echo textOnNull($adapter['DHCPEnabled'],"None");?></td>
					</tr>
					<tr>
						<th>MAC Address</th>
						<td><?php echo textOnNull($adapter['MACAddress'],"None"); ?></td>
					</tr>
					<tr>
						<th>DHCP Server</th>
						<td><?php echo textOnNull($adapter['DHCPServer'],"None"); ?></td>
					</tr>
				</tbody>
			</table>
		  </div>
		</div>
	</div>
	<?php }
		if(count($adapters) == 0){ ?>
			<div class="col-md-12" style="padding:5px;">
				<center><h5>No Network Adapters found.</h5></center>
			</div>
	<?php }?>
</div>