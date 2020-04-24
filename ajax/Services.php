<?php
	include("../Includes/db.php");
	$computerID = (int)$_GET['ID'];
	$search = $_GET['search'];
	$showDate = $_GET['Date'];

	$query = "SELECT hostname FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$result = mysqli_fetch_assoc($results);
	$exists = (bool)mysqli_num_rows($results);

	if(!$exists){ exit("<br><center><h4>No Computer Selected</h4><p>To Select A Computer, Please Visit The <a class='text-dark' href='index.php'><u>Dashboard</u></a></p></center><hr>"); }

	$json = getComputerData($result['hostname'], array("WMI_Services"), $showDate);

	$services = $json['WMI_Services'];
	$error = $json['WMI_Services_error'];

?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">
	Services (<?php echo count($services);?>)
</h4>
<?php if($showDate == "latest"){?>
	<span style="font-size:12px;color:#666;">
		- Last Update: <?php echo ago($json['WMI_Services_lastUpdate']);?>
	</span>
<?php }else{?>
	<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		History: <?php echo date("l, F jS g:i A", strtotime($json['WMI_Services_lastUpdate']));?>
	</span>
<?php }?>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('Services');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<a href="#" title="Select Date" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		<i class="far fa-calendar-alt"></i>
	</a>
</div>

<hr/>
<div class="card card-sm">
	<div class="card-body row no-gutters align-items-center">
		<div style="margin-top:10px;padding-right:10px;" class="col-auto">
			<i class="fas fa-search h5 text-body"></i>
		</div>
		<div class="col">
			<input id="searchInputServices" value="<?php echo $search; ?>" name="search" class="form-control form-control-md form-control-borderless" type="search" placeholder="Search By Service Name">
		</div>
		<div class="col-auto">
			<button class="btn btn-md" style="border-radius:0px 4px 4px 0px;background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" type="button" onclick="search($('#searchInputServices').val(),'Services','<?php echo $computerID; ?>');">
				<i class="fas fa-search"></i>
			</button>
		</div>
	</div>
</div>
<div style="overflow:auto;width:100%;">
	<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px;" class="table table-striped table-hover">
		  <col width="50">
		  <col width="80">
		  <col width="100">
		  <col width="880">
		  <col width="80">
		  <col width="150">
	  <thead class="thead-dark">
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Name</th>
		  <th scope="col">Display Name</th>
		  <th scope="col">Description</th>
		  <th scope="col"></th>
		</tr>
	  </thead>
	  <tbody>
		<?php
			foreach($services as $key=>$service){
				$name= explode("|",$service['Name']);
				$state = $name[0];
				if($state=="Running")$color=$siteSettings['theme']['Color 4'];
				if($state=="Stopped")$color="maroon";
				if($search!=""){
					if(stripos($name[1], $search) !== false){ }else{ continue; }
				}
				$count++;
		?>
			<tr>
			  <th scope="row"><?php echo $count;?></th>
			  <td><?php echo textOnNull($name[1], "[No Name]");?></td>
			  <td><?php echo textOnNull(substr($service['DisplayName'],0,35), "Not Set");?></td>
			  <td><?php echo textOnNull($service['Description'], "None");?></td>
			  <td>
				  <?php if($state=="Stopped"){ ?>
					<button title="Start Sevice" class="btn btn-sm btn-success" style="margin-top:-2px;padding:5px;padding-top:2px;padding-bottom:2px;border:none;" onclick='sendCommand("cmd", "net start <?php echo $name[1]; ?>", "Kill <?php echo $proc['Name']; ?> service");'>
						<i style="font-size:12px;" class="fas fa-play"></i> Start
					</button>
				  <?php }elseif($state="Running"){ ?>
					<button title="Stop Service" class="btn btn-sm btn-danger" style="margin-top:-2px;padding:5px;padding-top:2px;padding-bottom:2px;background:maroon;border:none;" onclick='sendCommand("net", "stop <?php echo $name[1]; ?> /y", "Kill <?php echo $proc['Name']; ?> service");'>
						<i style="font-size:12px;" class="fas fa-times"></i> Stop
					</button>
				  <?php } ?>
			  </td>
		<?php }
			if($count == 0){ ?>
				<tr>
					<td colspan=5>
						<center><h5>No Services found.</h5></center>
					</td>
				</tr>
		<?php }?>
	   </tbody>
	</table>
</div>
<script>
	$('#searchInputServices').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			search($('#searchInputServices').val(),'Services','<?php echo $computerID; ?>');
		}
	});
</script>