<?php
	include("../Includes/db.php");
	$computerID = (int)$_GET['ID'];
	$showDate = $_GET['Date'];
	
	$query = "SELECT hostname FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$result = mysqli_fetch_assoc($results);
	$exists = (bool)mysqli_num_rows($results);
	
	if(!$exists){ exit("<br><center><h4>No Computer Selected</h4><p>To Select A Computer, Please Visit The <a class='text-dark' href='index.php'><u>Dashboard</u></a></p></center><hr>"); }
	
	$json = getComputerData($result['hostname'], array("WMI_Printers"), $showDate);
	
	$printers = $json['WMI_Printers'];
	$error = $json['WMI_Printers_error'];
?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>;">
	Printers (<?php echo count($printers);?>)
</h4>
<?php if($showDate == "latest"){?>
	<span style="font-size:12px;color:#666;"> 
		- Last Update: <?php echo ago($json['WMI_Printers_lastUpdate']);?>
	</span>
<?php }else{?>
	<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		History: <?php echo date("l, F jS g:i A", strtotime($json['WMI_Printers_lastUpdate']));?>
	</span>
<?php }?>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('Printers');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<a href="#" title="Select Date" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		<i class="far fa-calendar-alt"></i>
	</a>
</div>

<hr/>
<div style="overflow:auto;width:100%;">
	<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px;" class="table table-striped table-hover">
	  <thead class="thead-dark">
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Name</th>
		  <th scope="col">Port</th>
		  <th scope="col">Shared</th>
		</tr>
	  </thead>
	  <tbody>
		<?php
			foreach($printers as $key=>$print){
				$count++;
		?>
			<tr>
			  <th scope="row"><?php echo $count;?></th>
			  <td>
				<?php echo textOnNull($print['Caption'], "[No Name]");?>
				<?php 
					if($print['Default'] == "True"){echo "<b>(Default)</b>";}
				?>
			  </td>
			  <td><?php echo textOnNull(substr($print['PortName'],0,25), "Not Set");?></td>
			  <td><?php echo textOnNull($print['Shared'], "False");?></td>
			</tr>
		<?php }
			if(count($printers) == 0){ ?>
				<tr>
					<td colspan=4><center><h5>No Printers found.</h5></center></td>
				</tr>
		<?php }?>
	   </tbody>
	</table>
</div>