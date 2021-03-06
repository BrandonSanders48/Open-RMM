<?php
	include("../Includes/db.php");
	
	$computerID = (int)$_GET['ID'];
	$showDate = $_GET['Date'];
	
	$query = "SELECT hostname FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$result = mysqli_fetch_assoc($results);
	$exists = (bool)mysqli_num_rows($results);
	
	if(!$exists){ exit("<br><center><h4>No Computer Selected</h4><p>To Select A Computer, Please Visit The <a class='text-dark' href='index.php'><u>Dashboard</u></a></p></center><hr>"); }
	
	$json = getComputerData($result['hostname'], array("WMI_USBHub", "WMI_DesktopMonitor", "WMI_Keyboard", "WMI_PointingDevice", "WMI_SoundDevice", "WMI_SerialPort", "WMI_PnPEntity"), $showDate);

?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>;">
	Attached Devices
</h4>
<?php if($showDate == "latest"){?>
	<span style="font-size:12px;color:#666;"> 
		- Last Update: <?php echo ago($json['WMI_USBHub_lastUpdate']);?>
	</span>
<?php }else{?>
	<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		History: <?php echo date("l, F jS g:i A", strtotime($json['WMI_USBHub_lastUpdate']));?>
	</span>
<?php }?>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('AttachedDevices');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<a href="#" title="Select Date" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		<i class="far fa-calendar-alt"></i>
	</a>
</div>
<hr/>
<div style="margin-left:20px">
	<h6 style="color:<?php echo $siteSettings['theme']['Color 1'];?>"><b>Displays</b></h6>
	<div class="row" style="margin-bottom:10px;padding-left:40px;">
		<?php
			$monitors = $json['WMI_DesktopMonitor'];
			$error = $json['WMI_DesktopMonitor_error'];
			foreach($monitors as $device){		
		?>
			<div class="col-md-2" style="padding:5px;">
				<div class="card" style="height:100%;padding:5px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;">
				  <div style="text-align:center;color:#fff;">
					<h6 class="card-title">
						<?php echo $device['Name'];?>
					</h6>
					<!--<p><?php echo $device['Description'];?></p> They all seem to match name -->
				  </div>
				</div>
			</div>
		<?php }?>
		<?php if(count($monitors) == 0){?>
			<div class="col-md-12" style="padding:5px;margin-left:30px;">
				No monitors found.
			</div>
		<?php }?>
	</div>
	<hr/>
	<h6 style="color:<?php echo $siteSettings['theme']['Color 1'];?>"><b>USB Hubs</b></h6>
	<div class="row" style="margin-bottom:10px;padding-left:40px;">
		<?php
			$hubs = $json['WMI_USBHub'];
			$error = $json['WMI_USBHub_error'];
			foreach($hubs as $hub){	
		?>
			<div class="col-md-2" style="padding:3px;">
				<div class="card" style="height:100%;padding:5px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;font-size:14px;">
				  <div style="text-align:center;">
					<h6 class="card-title">
						<?php echo $hub['Name'];?>
					</h6>
				  </div>
				</div>
			</div>
		<?php }?>
		<?php if(count($hubs) == 0){?>
			<div class="col-md-12" style="padding:5px;margin-left:30px;">
				No USB hubs found.
			</div>
		<?php }?>
	</div>
	<hr>
	<h6 style="color:<?php echo $siteSettings['theme']['Color 1'];?>"><b>Keyboards</b></h6>
	<div class="row" style="margin-bottom:10px;padding-left:40px;">
		<?php
			$keyboards = $json['WMI_Keyboard'];
			$error = $json['WMI_Keyboard_error'];
			foreach($keyboards as $device){	
		?>
			<div class="col-md-3" style="padding:5px;">
				<div class="card" style="height:100%;padding:5px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;">
				  <div style="text-align:center;">
					<h6 class="card-title">
						<b><?php echo $device['Caption'];?></b>
						<p><?php echo $device['Description'];?></p>
					</h6>
					
				  </div>
				</div>
			</div>
		<?php }?>
		<?php if(count($keyboards) == 0){?>
			<div class="col-md-12" style="padding:5px;margin-left:30px;">
				No keyboards found.
			</div>
		<?php }?>
	</div>
	<hr>
	<h6 style="color:<?php echo $siteSettings['theme']['Color 1'];?>"><b>Pointing Devices</b></h6>
	<div class="row" style="margin-bottom:10px;padding-left:40px;">
		<?php
			$pointingDevices = $json['WMI_PointingDevice'];
			$error = $json['WMI_PointingDevice_error'];
			foreach($pointingDevices as $device){	
		?>
			<div class="col-md-3" style="padding:5px;">
				<div class="card" style="height:100%;padding:5px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;">
				  <div style="text-align:center;">
					<h6 class="card-title">
						<?php echo $device['Name'];?>
					</h6>
					<!--<p><?php echo $device['Description'];?></p> They all seem to match name -->
				  </div>
				</div>
			</div>
		<?php }?>
		<?php if(count($pointingDevices) == 0){?>
			<div class="col-md-12" style="padding:5px;margin-left:30px;">
				No pointing devices found.
			</div>
		<?php }?>
	</div>
	<hr>
	<h6 style="color:<?php echo $siteSettings['theme']['Color 1'];?>"><b>Sound</b></h6>
	<div class="row" style="margin-bottom:10px;padding-left:40px;">
		<?php
			$SoundDevices = $json['WMI_SoundDevice'];
			$error = $json['WMI_SoundDevice_error'];
			foreach($SoundDevices as $device){	
		?>
			<div class="col-md-3" style="padding:5px;">
				<div class="card" style="height:100%;padding:5px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;">
				  <div style="text-align:center;">
					<h6 class="card-title">
						<?php echo $device['Name'];?>
					</h6>
					<!--<p><?php echo $device['Description'];?></p> They all seem to match name -->
				  </div>
				</div>
			</div>
		<?php }?>
		<?php if(count($SoundDevices) == 0){?>
			<div class="col-md-12" style="padding:5px;margin-left:30px;">
				No sound cards found.
			</div>
		<?php }?>
	</div>
	<hr>
	<h6 style="color:<?php echo $siteSettings['theme']['Color 1'];?>"><b>Serial Ports</b></h6>
	<div class="row" style="margin-bottom:10px;padding-left:40px;">
		<?php
			$SerialPorts = $json['WMI_SerialPort'];
			$error = $json['WMI_SerialPort_error'];
			foreach($SerialPorts as $device){	
		?>
			<div class="col-md-3" style="padding:5px;">
				<div class="card" style="height:100%;padding:5px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;">
				  <div style="text-align:center;">
					<h6 class="card-title">
						<?php echo $device['DeviceID'];?>
					</h6>
					<p><?php echo $device['Description'];?></p>
				  </div>
				</div>
			</div>
		<?php }?>
		<?php if(count($SerialPorts) == 0){?>
			<div class="col-md-12" style="padding:5px;margin-left:30px;">
				No serial devices found.<br><br>
			</div>
		<?php }?>
	</div>
</div>
	<hr>
	<h6 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">
		<b>Plug And Play Devices (<?php echo count($json['WMI_PnPEntity']); ?>)</b>
	</h6>
	<div style="overflow:auto;width:100%;">
		<table style="line-height:20px;overflow:scroll;font-size:14px;margin-top:8px;" class="table table-striped table-hover">
		  <thead class="thead-dark">
			<tr>
			  <th scope="col">#</th>
			  <th scope="col">Name</th>
			  <th scope="col">Manufacturer</th>
			  <th scope="col">Description</th>
			  <th scope="col">PNP Class</th>
			</tr>
		  </thead>
		  <tbody>
			<?php
				$PnPEntity = $json['WMI_PnPEntity'];
				$error = $json['WMI_PnPEntity_error'];
			
				//Sort The array by Name ASC
				usort($PnPEntity, function($a, $b) {
					return $a['PNPClass'] <=> $b['PNPClass'];
				});
			
				$key  = 0;
				foreach($PnPEntity as $device){
					if(trim($device['Caption'])==""){continue;}
					$key++;
			?>
				<tr>
				  <th scope="row"><?php echo $key;?></th>
				  <td><?php echo $device['Caption'];?></td>
				  <td><?php echo $device['Manufacturer'];?></td>
				  <td><?php echo $device['Description'];?></td>
				  <td><?php echo $device['PNPClass'];?></td>
				</tr>
				<?php }
				if($key == 0){ ?>
					<tr>
						<td colspan=5><center><h5>No PNP Devices found.</h5></center></td>
					</tr>
			<?php }?>
		 </tbody>
		</table>
	</div>
</div>