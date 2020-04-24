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

	
	$json = getComputerData($result['hostname'], array("WMI_OptionalFeatures"), $showDate);

?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">
	Optional Features (<?php echo count($json['WMI_OptionalFeatures']); ?>)
</h4>
<?php if($showDate == "latest"){?>
	<span style="font-size:12px;color:#666;"> 
		- Last Update: <?php echo ago($json['WMI_OptionalFeatures_lastUpdate']);?>
	</span>
<?php }else{?>
	<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		History: <?php echo date("l, F jS g:i A", strtotime($json['WMI_OptionalFeatures_lastUpdate']));?>
	</span>
<?php }?>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('OptionalFeatures');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<a href="#" title="Select Date" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		<i class="far fa-calendar-alt"></i>
	</a>
</div>

<hr/>
<div class="card card-sm">
	<div class="card-body row no-gutters align-items-center">
		<div style="margin-top:10px;padding-right:10px" class="col-auto">
			<i class="fas fa-search h5 text-body"></i>
		</div>
		<div class="col">
			<input id="searchInputOptf" value="<?php echo $search; ?>" name="search" class="form-control form-control-md form-control-borderless" type="search" placeholder="Search By Feature Name">
		</div>
		<div class="col-auto">
			<button class="btn btn-md" style="border-radius:0px 4px 4px 0px;background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff" type="button" onclick="search($('#searchInputOptf').val(),'OptionalFeatures','<?php echo $computerID; ?>');">
				<i class="fas fa-search"></i>
			</button>
		</div>
	</div>
</div>
<div style="overflow:auto;width:100%">
	<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px" class="table table-striped">
	  <thead class="thead-dark">
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Name</th>
		  <th scope="col">Caption</th>
		  <th scope="col">Description</th>
		  <th scope="col">Install State</th>
		</tr>
	  </thead>
	  <tbody>
		<?php
			$installState = array(
				"1"=>array("state"=>"Enabled", "color"=>$siteSettings['theme']['Color 4']), 
				"2"=>array("state"=>"Disabled", "color"=>$siteSettings['theme']['Color 3']), 
				"3"=>array("state"=>"Absent", "color"=>"maroon"),
				"4"=>array("state"=>"Unknown", "color"=>"maroon")
			);
			$OptionalFeatures = $json['WMI_OptionalFeatures'];
			$error = $json['WMI_OptionalFeatures_error'];
			
			//Sort The array by Name ASC
			usort($OptionalFeatures, function($a, $b) {
				return $a['Name'] <=> $b['Name'];
			});
			
			foreach($OptionalFeatures as $key=>$feature){
				if($search!=""){ 
					if(stripos($feature['Name'], $search) !== false){ }else{ continue; }		
				}
				$count++;
				$state = $installState[$feature['InstallState']];
		?>
			<tr>
			  <th scope="row"><?php echo $count;?></th>
			  <td><?php echo textOnNull($feature['Name'],"N/A");?></td>
			  <td><?php echo textOnNull($feature['Caption'],"N/A");?></td>
			  <td><?php echo textOnNull($feature['Description'],"N/A");?></td>
			  <td style="color:<?php echo $state['color'];?>;">
				<b><?php echo textOnNull($state['state'],"Unknown");?></b>
			  </td>
			</tr>
		<?php }
				if($count == 0){ ?>
				<tr>
					<td colspan=5><center><h5>No Optional Features Found.</h5></center></td>
				</tr>
		<?php }?>
	   </tbody>
	</table>
</div>
<script>
	$('#searchInputOptf').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			search($('#searchInputOptf').val(),'OptionalFeatures','<?php echo $computerID; ?>');
		}
	});
</script>