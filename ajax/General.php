<?php
	include("../Includes/db.php");
	
	$computerID = (int)$_GET['ID'];
	$showDate = $_GET['Date'];
	$query = "SELECT teamviewer, ID, hostname, CompanyID, name, phone, email, computerType, agent_version FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$result = mysqli_fetch_assoc($results);
	$query = "SELECT name, phone, email,address,comments,date_added FROM companies WHERE CompanyID='".$result['CompanyID']."' LIMIT 1";
	$companies = mysqli_query($db, $query);
	$company = mysqli_fetch_assoc($companies);
	$json = getComputerData($result['hostname'], array("*"), $showDate);
	
	//Update Recents
	if (in_array( $computerID, $_SESSION['recent'])){
		if (($key = array_search($computerID, $_SESSION['recent'])) !== false) {
			unset($_SESSION['recent'][$key]);
		}
		array_push($_SESSION['recent'], $result['ID']);
		$query = "UPDATE users SET recents='".implode(",", $_SESSION['recent'])."' WHERE ID=".$_SESSION['userid'].";";
		$results = mysqli_query($db, $query);
	}else{
		if(end($_SESSION['recent']) != $computerID){
			array_push($_SESSION['recent'], $result['ID']);
			$query = "UPDATE users SET recents='".implode(",", $_SESSION['recent'])."' WHERE ID=".$_SESSION['userid'].";";
			$results = mysqli_query($db, $query);
		}
	}
	if($result['hostname']==""){ exit("<br><center><h4>No Computer Selected</h4><p>To Select A Computer, Please Visit The <a class='text-dark' href='index.php'><u>Dashboard</u></a></p></center><hr>"); }
	
	$online = $json['Online'];
	$lastPing = $json['Ping'];
	
	if(!$online) {
		$alert = "This Computer Is Currently Offline";
		$alertType = "danger";
	}
	$hostname = textOnNull(strtoupper($result['hostname']),"No Device Selected");
?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">
	General Information
	<?php if($showDate != "latest"){?>
		<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
			History: <?php echo date("l, F jS", strtotime($showDate));?>
		</span>
	<?php }?>
	<div style="float:right;">
		<a href="#" title="Refresh" onclick="loadSection('General');" class="btn btn-sm" style="margin:3px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
			<i class="fas fa-sync"></i>
		</a>
		<a href="#" title="Select Date" class="btn btn-sm" style="margin:3px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#historicalDateSelection_modal">
			<i class="far fa-calendar-alt"></i>
		</a>
	</div>
</h4>
<hr>
<?php if($alert!=""){ ?>
	<div class="alert alert-<?php echo $alertType; ?>" role="alert">
		<b><?php echo $alert; ?></b>
	</div>
<?php } ?>
<div class="row" style="color:#383838;border: 1px solid #dedede;text-align:center;margin-bottom:10px;margin-top:10px;border-radius:3px;overflow:hidden;padding:5px;background-color:#fff;heigsht:85px">
	<div style="font-size:32px;border-radius:6px;margin-top:15px" class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
		<b>
			<?php $alertCount = count($json['Alerts']);?>
			<?php if($alertCount > 0){?>
				<span title="<?php echo $alertCount;?> Issues" class="text-danger" data-toggle="modal" data-target="#computerAlerts" style="cursor:pointer;padding-left:15px;" onclick="computerAlertsModal('This PC','<?php echo $json['Alerts_raw'];?>');">
					<i title="<?php echo $alertCount;?> Issues" class="fa fa-exclamation-triangle" aria-hidden="true"></i>
					<h5 style="display:inline;"><?php echo $alertCount;?> Issue<?php if($alertCount>1){echo"s"; } ?></h5>
				</span>
			<?php }else{?>
				<span class="text-success" title="No Issues" data-toggle="modal" data-target="#computerAlerts" style="cursor:pointer;padding-left:15px;" onclick="computerAlertsModal('this PC');">
					<i class="fas fa-thumbs-up"></i> <h5 style="display:inline;">No Issues</h5>
				</span>
			<?php }?>
		</b>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
		<p style="text-align:center;">
			<h4><span title="ID: <?php echo $result['ID'];?>"><?php echo $hostname;?></span>
				<br>
				<span style="font-size:14px;">
					<?php echo textOnNull($json['WMI_ComputerSystem'][0]['Domain'], "None"); ?>
				</span>
			</h4>
		</p>
	</div>
	<div class="col-xs-12 col-sm-12 col-md-7 col-lg-7" style="margin-top:10px">
		<div style="color:#383838;" class="row">
			<div class="col-sm-2" onclick="loadSection('Printers');" style="cursor:pointer;max-width:33%;">
				<i style="font-size:26px;color:#696969;" class="fas fa-print"></i>
				<span style="font-size:26px;"> <b><?php echo count($json['WMI_Printers']);?></b></span><br>
				<span style="font-size:18px;">Printers</span>
			</div>
			<div class="col-sm-2" onclick="loadSection('Programs');" style="cursor:pointer;max-width:33%;">
				<i style="font-size:26px;color:#696969;" class="fab fa-app-store-ios"></i>
				<span style="font-size:26px;"> <b><?php echo count($json['WMI_Product']);?></b></span><br>
				<span style="font-size:18px;">Programs</span>
			</div>
			<div class="col-sm-2" onclick="loadSection('Disks');" style="cursor:pointer;max-width:33%;">
				<i style="font-size:26px;color:#696969;" class="fas fa-hdd"></i>
				<span style="font-size:26px;"> <b><?php echo count($json['WMI_LogicalDisk']);?></b></span><br>
				<span style="font-size:18px;">Disks</span>
			</div>
			<div class="col-sm-2" onclick="loadSection('Proccesses');" style="cursor:pointer;max-width:33%;">
				<i style="font-size:26px;color:#696969;" class="fas fa-microchip"></i>
				<span style="font-size:26px;"> <b><?php echo count($json['WMI_Processes']);?></b></span><br>
				<span style="font-size:18px;">Processes</span>
			</div>
			<div class="col-sm-3" onclick="loadSection('Services');" style="cursor:pointer;max-width:33%;">
				<i style="font-size:26px;color:#696969;" class="fas fa-sync-alt"></i>
				<span style="font-size:26px;"> <b><?php echo count($json['WMI_Services']);?></b></span><br>
				<span style="font-size:18px;">Services</span>
			</div>
		</div>
	</div>
</div>
<!--<div class="row" style="margin-bottom:20px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;padding:5px;border-radius:6px;border: 1px solid #dedede;">
<canvas class="col-xs-11 col-sm-11 col-md-11 col-lg-11" id="myChart" width="400" height="100"></canvas>
</div>
<script>
var ctx = document.getElementById('myChart');
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
			fill:false,
          data: [{
				x: 100,
				y: 200
			}, {
				x: 15,
				y: 10
			}],       
            borderColor: [
                'green'  
            ],
            borderWidth: 1
        }]
    },
    options: {	
        scales: {
            yAxes: [{				
					stacked: true
            }]
        }
    }
});
Chart.defaults.global.defaultFontColor='#fff';
</script>-->
<div class="row">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:5px;">
		<div class="card">
		  <div class="card-body" style="padding:20px;">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<h4>
						<?php echo ($result['name']!="" ? ucwords($result['name'])." with " : ""); ?>
						<a href="#" style="color:<?php echo $siteSettings['theme']['Color 5']; ?>" data-toggle="modal" data-target="#companyMoreInfo">
							<?php echo textOnNull(($company['name']!="N/A" ? $company['name'] : ""), "No Company Info"); ?>
						</a>
					</h4>
					<span style="color:#666;font-size:14px;"><?php echo textOnNull(phone($result['phone']), "No Company Phone"); ?> |
						<a href="mailto:<?php echo $result['email']; ?>">
							<?php echo textOnNull(phone($result['email']), "No Company Email"); ?>
						</a>
					</span>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="text-align:center;"><hr>
					<?php if($online){ ?>
						<button class="btn btn-danger btn-sm" onclick='sendCommand("shutdown", "-s -t 30", "Shutdown Computer");' style="margin:3px;">
							<i class="fas fa-power-off"></i> Shutdown
						</button>
						<button class="btn btn-warning btn-sm" onclick='sendCommand("shutdown", "-r -t 30", "Reboot Computer");' style="margin:3px;color:#fff;background:#ffa500;">
							<i class="fas fa-redo"></i> Reboot
						</button>
						<button class="btn btn-sm" type="button" style="margin:3px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#actionsModal">
							<i class="fas fa-terminal"></i> Actions
						</button>
					<?php } ?>
					<?php if(trim($result['teamviewer']) != ""){ ?>
						<a target="_BLANK" href="https://start.teamviewer.com/device/<?php echo $result['teamviewer'];?>/authorization/password/mode/control" class="btn btn-sm" style="margin:3px;color:#fff;background:rgba(194,194,204,0.54);" title="<?php echo $result['teamviewer'];?>">
							<img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/90/TeamViewer_logo.svg/800px-TeamViewer_logo.svg.png" height="22px;"/>
						</a>
					<?php } ?>
				</div>
			</div>
		  </div>
		</div>
	</div>
	<?php if(count($json['WMI_ComputerSystem'][0]) > 1) {?>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding:5px;">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:20px;">
				<h5>Hardware Stats</h5>
				<div class="row">
					<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3" style="text-align:center;cursor:pointer;max-width:50%;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'TotalMemory');">
						<?php
							$totalMemory = round($json['WMI_ComputerSystem'][0]['TotalPhysicalMemory'] / 1024 /1024/1024,1); //GB
							//Determine Warning Level
							if($totalMemory < $siteSettings['Alert Settings']['Memory']['Total']['Danger'] ){
								$pbColor = "red"; //Danger
							}elseif($totalMemory < $siteSettings['Alert Settings']['Memory']['Total']['Warning']){
								$pbColor = "#ffa500"; //Warning
							}else{ $pbColor = $siteSettings['theme']['Color 4']; }
						?>
						<span style="font-size:30px;color:<?php echo $pbColor;?>;">
							<?php echo $totalMemory;?> GB
						</span><br/>
						<span>Total Memory</span>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3" style="text-align:center;cursor:pointer;max-width:50%;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'FreeMemory');">
						<?php
							$freeMemory_mb = (int)$json['WMI_OS'][0]['FreePhysicalMemory'] / 1024; //MB
							$freeMemory = ($freeMemory_mb>=1024 ? round($freeMemory_mb/1024,1)." GB" : round($freeMemory_mb)." MB");
							//Determine Warning Level
							if($freeMemory_mb < $siteSettings['Alert Settings']['Memory']['Free']['Danger'] ){
								$pbColor = "red"; //Danger
							}elseif($freeMemory_mb < $siteSettings['Alert Settings']['Memory']['Free']['Warning']){
								$pbColor = "#ffa500"; //Warning
							}else{ $pbColor = $siteSettings['theme']['Color 4']; }
						?>
						<span style="font-size:30px;color:<?php echo $pbColor;?>;">
							<?php echo $freeMemory;?>
						</span><br/>
						<span >Free Memory</span>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3" style="text-align:center;cursor:pointer;max-width:50%;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'Disk');">
						<?php
							$freeSpace = $json['WMI_LogicalDisk'][0]['FreeSpace'];
							$size = $json['WMI_LogicalDisk'][0]['Size'];
							$used = $size - $freeSpace ;
							$usedPct = round(($used/$size) * 100);
							$status = round((int)$used/ 1024 /1024 /1024)." GB"." of ".round((int)$json['WMI_LogicalDisk'][0]['Size']/ 1024 /1024 /1024)." GB Used";
							//Determine Warning Level
							if($usedPct > $siteSettings['Alert Settings']['Disk']['Danger'] ){
								$pbColor = "red"; //Danger
							}elseif($usedPct > $siteSettings['Alert Settings']['Disk']['Warning']){
								$pbColor = "#ffa500"; //Warning
							}else{ $pbColor = $siteSettings['theme']['Color 4']; }
						?>
						<span style="color:<?php echo $pbColor;?>;font-size:30px;" title="<?php echo $status;?>">
							<?php echo $usedPct;?>%
						</span><br/>
						<span>Storage Used</span>
					</div>
					<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3" style="text-align:center;cursor:pointer;max-width:50%;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'CPUUsage');">
						<?php
							$cpu = round($json['CPUUsage']['Value']);
							if($cpu > $siteSettings['Alert Settings']['Processor']['Danger'] ){
								$pbColor = "red"; //Danger
							}elseif($cpu > $siteSettings['Alert Settings']['Processor']['Warning']){
								$pbColor = "#ffa500"; //Warning
							}else{ $pbColor = $siteSettings['theme']['Color 4']; }
						?>
						<span style="font-size:30px;color:<?php echo $pbColor;?>">
							<?php echo textOnNull($cpu, "N/A"); ?>%
						</span><br/>
						<span>Processor Load</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Computer Type</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-21">
						&nbsp;&nbsp;<a href="#" onclick="loadSection('Edit');" style="font-size:16px;"><?php echo textOnNull($result['computerType'], "Not Set");?></a>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<?php if((int)$json['WMI_Battery'][0]['BatteryStatus']>0){ ?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Battery Status</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="font-size:16px;">
						&nbsp;&nbsp;
						<?php 								
							$statusArray = [
							"1" => ["Text" => "Discharging", "Color" => "red"],
							"2" => ["Text" => "Unknown", "Color" => "red"],
							"3" => ["Text" => "Fully Charged", "Color" => "green"],
							"4" => ["Text" => "Low", "Color" => "red"],
							"5" => ["Text" => "Critical", "Color" => "red"],
							"6" => ["Text" => "Charging", "Color" => "green"],
							"7" => ["Text" => "Charging And High", "Color" => "green"],
							"8" => ["Text" => "Charging And Low", "Color" => "green"],
							"9" => ["Text" => "Charging And Critical", "Color" => "yellow"],
							"10" =>["Text" => "Undefined", "Color" => "red"],
							"11" =>["Text" => "Partially Charged", "Color"=>"yellow"]];
							$statusInt = $json['WMI_Battery'][0]['BatteryStatus'];						
						?>
						<?php echo textOnNull($json['WMI_Battery'][0]['EstimatedChargeRemaining'], "Unknown");?>%
						(<span style="color:<?php echo $statusArray[$statusInt]['Color']; ?>"><?php echo $statusArray[$statusInt]['Text']; ?></span>)		
					</div>
				</div>
			  </div>
			</div>
		</div>
		<?php } ?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Processor</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px">
							<?php echo textOnNull(str_replace("(R)","",str_replace("(TM)","",$json['WMI_Processor'][0]['Name'])), "N/A");?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'OperatingSystem');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Operating System</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						&nbsp;&nbsp;<span style="font-size:16px;"><?php echo textOnNull(str_replace("Microsoft","",$json['WMI_OS'][0]['Caption']), "N/A");?></span>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Architecture</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					&nbsp;&nbsp;<span style="font-size:16px;"><?php echo textOnNull($json['WMI_ComputerSystem'][0]['SystemType'], "N/A");?></span><br/>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'PCModel');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Computer Make/Model</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;">
							&nbsp;&nbsp;<?php echo textOnNull($json['WMI_ComputerSystem'][0]['Manufacturer']." ".$json['WMI_ComputerSystem'][0]['Model'], "N/A");?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'LoggedInUser');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Current Logged In User</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;">
							&nbsp;&nbsp;<?php echo textOnNull(basename($json['WMI_ComputerSystem'][0]['UserName']), "Unknown");?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Domain</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;">
							&nbsp;&nbsp;<?php echo textOnNull($json['WMI_ComputerSystem'][0]['Domain'], "N/A");?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'BIOSVersion');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>BIOS Version</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;">
							&nbsp;&nbsp;<?php echo textOnNull($json['WMI_BIOS'][0]['Version'], "N/A");?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
	<?php }?>
	<?php
		$lastBoot = explode(".", $json['WMI_OS'][0]['LastBootUpTime'])[0];
		$cleanDate = date("m/d/Y h:i A", strtotime($lastBoot));
	?>
	<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;" title="<?php echo $cleanDate;?>">
		<div class="card" style="height:100%;">
		  <div class="card-body" style="padding:10px;">
			<h5>Last Known Uptime</h5>
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<span style="font-size:16px;">
						&nbsp;&nbsp;<?php if($lastBoot!=""){ echo str_replace(" ago", "", textOnNull(ago($lastBoot), "N/A")); }else{ echo"N/A"; }?>
					</span>
				</div>
			</div>
		  </div>
		</div>
	</div>
	<?php if(count($json['Firewall']) > 0) {?>
		<?php
			$status = $json['Firewall']['Status'];
			$color = (($status == "True" || $status == "Enabled") ? "text-success" : "text-danger");
		?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'Firewall');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Firewall Status</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;" class="<?php echo $color;?>">
							&nbsp;&nbsp;<?php echo $status; ?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
	<?php } ?>
	<?php if(count($json['IPAddress']) > 0) {?>
		<?php
			$status = $json['IPAddress']['Value'];
		?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'IPAddress');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Local IP Address</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;" class="">
							&nbsp;&nbsp;<?php echo $status;?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
	<?php } ?>
	<?php if(count($json['WindowsActivation']) > 0) {?>
		<?php
			$status = $json['WindowsActivation']['Value'];
			$color = ($status == "Activated" ? "text-success" : "text-danger");
		?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'WindowsActivation');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Windows Activation Status</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;" class="<?php echo $color;?>">
							&nbsp;&nbsp;<?php echo $status ;?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
	<?php } ?>
	<?php if(count($json['Antivirus']) > 0) {?>
		<?php
			$status = $json['Antivirus']['Value'];
			$color = ($status == "No Antivirus" ? "text-danger" : "text-success");
		?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'Antivirus');">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Antivirus</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;" class="<?php echo $color;?>">
							&nbsp;&nbsp;<?php echo $status ;?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
	<?php } ?>
	<?php if(count($json['Ransomware']) > 0) {?>
		<?php
			$status = $json['Ransomware']['Status'];
			$color = ($status == "True" ? "text-success" : "text-danger");
		?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Ransomware Status</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;" class="<?php echo $color;?>">
							&nbsp;&nbsp;<?php echo ($status == "True" ? "Enabled" : "Disabled");?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
	<?php } ?>
	<?php if(count($json['WMI_ComputerSystem'][0]) > 1) {?>
		<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;" title="Time since service has pushed changes.">
			<div class="card" style="height:100%;">
			  <div class="card-body" style="padding:10px;">
				<h5>Last Update</h5>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;">
							&nbsp;&nbsp;<?php echo ago($lastPing);?>
						</span>
					</div>
				</div>
			  </div>
			</div>
		</div>
	<?php } 
		if(isset($json['SQLUsername']['Value'])){?>
	<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;" title="The Current MySQL User On This Computers Agent">
		<div class="card" style="height:100%;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'SQLUsername');">
		  <div class="card-body" style="padding:10px;">
			<h5>Curent Database User</h5>
			<?php
				if(isset($json['SQLUsername']['Value'])){
					$agentVersion = $json['SQLUsername']['Value'];
				}else{
					$agentVersion = $result['SQLUsername'];
				}
			?>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;">
							&nbsp;&nbsp;<?php echo textOnNull($agentVersion, "Unknown");?>
						</span>
					</div>
				</div>
		  </div>
		</div>
	</div>
	<?php } ?>
	<div class="col-xs-6 col-sm-6 col-md-3 col-lg-3" style="padding:5px;" title="Agent version that is installed on the computer.">
		<div class="card" style="height:100%;cursor:pointer;" onclick="loadHistoricalData('<?php echo $result['hostname'];?>', 'AgentVersion');">
		  <div class="card-body" style="padding:10px;">
			<h5>Agent Version</h5>
			<?php
				if(isset($json['AgentVersion']['Value'])){
					$agentVersion = $json['AgentVersion']['Value'];
				}else{
					$agentVersion = $result['agent_version'];
				}
			?>
			<?php if($agentVersion != $siteSettings['general']['agent_latest_version']){?>
					<button class="btn-sm btn btn-success" onclick='sendCommand("C:\\\\Open-RMM\\\\Update.bat", "", "Update Agent", 2);' style="background:<?php echo $siteSettings['theme']['Color 4'];?>;float:right;display:inline;" title="Update to <?php echo $siteSettings['general']['agent_latest_version'];?>">
						<i class="fas fa-cloud-upload-alt"></i> Update
					</button>
				<?php $latest="<b> (Outdated)</b>"; }else{ $latest="<b> (Latest)</b>"; } ?>
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<span style="font-size:16px;">
							&nbsp;&nbsp;<?php echo textOnNull($agentVersion, "Unknown").$latest;?>
						</span>
					</div>
				</div>
		  </div>
		</div>
	</div>
	
</div>
<!-------------------------------MODALS------------------------------------>
<div id="companyMoreInfo" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h4 class="modal-title"><?php echo textOnNull($company['name'], "No Company Info"); ?></h4>
	  </div>
	  <div class="modal-body">
		<ul class="list-group">
			<li class="list-group-item">
				<b>Phone:</b>
				<?php echo textOnNull(phone($company['phone']), "No Company Phone"); ?>
			</li>
			<li class="list-group-item">
				<b>Email:</b>
				<a href="mailto:<?php echo $company['email']; ?>">
					<?php echo textOnNull($company['email'], "No Company Email"); ?>
				</a>
			</li>
			<li class="list-group-item">
				<b>Address:</b>
				<?php echo textOnNull($company['address'], "No Company Address"); ?>
			</li>
			<li class="list-group-item">
				<b>Comments:</b><br>
				<span style="margin-left:10px;">
					<?php echo textOnNull($company['Comments'], "No Comments"); ?>
				</span>
			</li>
		</ul>
		<span style="color:#696969;float:right;font-size:10px;">
			Added <?php echo gmdate("m/d/Y\ h:i:s", $company['date_added']); ?>
		</span>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn" style="background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>
<script>
	$(".sidebarComputerName").text("<?php echo strtoupper($result['hostname']);?>");
</script>