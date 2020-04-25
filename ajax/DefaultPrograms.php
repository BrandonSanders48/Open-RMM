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

	$json = getComputerData($result['hostname'], array("DefaultPrograms"), $showDate);

	$programs = $json['DefaultPrograms'];
	$error = $json['DefaultPrograms_error'];

?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>;">
	Default Programs (<?php echo count($programs);?>)
</h4>
<?php if($showDate == "latest"){?>
	<span style="font-size:12px;color:#666;">
		- Last Update: <?php echo ago($json['DefaultPrograms_lastUpdate']);?>
	</span>
<?php }else{?>
	<span class="badge badge-warning" style="font-size:12px;cursor:pointer;" data-toggle="modal" data-target="#historicalDateSelection_modal">
		History: <?php echo date("l, F jS g:i A", strtotime($json['DefaultPrograms_lastUpdate']));?>
	</span>
<?php }?>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('DefaultPrograms');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
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
			<input id="searchInputDefaultPrograms" value="<?php echo $search; ?>" name="search" class="form-control form-control-md form-control-borderless" type="search" placeholder="Search By File Extension">
		</div>
		<div class="col-auto">
			<button class="btn btn-md" style="border-radius:0px 4px 4px 0px;background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" type="button" onclick="search($('#searchInputDefaultPrograms').val(),'DefaultPrograms','<?php echo $computerID; ?>');">
				<i class="fas fa-search"></i> Search
			</button>
		</div>
	</div>
</div>
<div style="overflow:auto;width:100%;">
	<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px;" class="table table-striped table-hover">
	  <thead class="thead-dark">
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Program</th>
		  <th scope="col">File Extension</th>
		  <th scope="col">Clear Default  (May Not Work)</th>
		</tr>
	  </thead>
	  <tbody>
		<?php
			$count = 0;

			//Sort The array by Name ASC
			usort($programs, function($a, $b) {
				return $a['Program'] <=> $b['Program'];
			});

			foreach($programs as $key=>$program){

				if($search!=""){
					if(stripos($program['Ext'], $search) !== false){ }else{ continue; }
				}
				$count++;
		?>
			<tr>
			  <th scope="row"><?php echo $count;?></th>
			  <td><?php echo textOnNull($program["Program"],"N/A");?></td>
			  <td><?php echo textOnNull($program["Ext"],"N/A");?></td>
			  <td>
				<button onclick='sendCommand("cmd", "assoc <?php echo $program['Ext']; ?>=", "Clear default for <?php echo $program['Ext']; ?> filetype");' class="btn btn-sm"
					style="margin-top:-2px;padding:5px;padding-top:2px;padding-bottom:2px;background:maroon;color:#fff;" title="May Require A Reboot">
					<i class="fas fa-eraser"></i>
				</button>
			  </td>
			</tr>
		<?php }
			if($count == 0){ ?>
				<tr>
					<td colspan=4><center><h5>No Default Programs Found.</h5></center></td>
				</tr>
		<?php }?>
	   </tbody>
	</table>
</div>
<script>
	$('#searchInputDefaultPrograms').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			search($('#searchInputDefaultPrograms').val(),'DefaultPrograms','<?php echo $computerID; ?>');
		}
	});
</script>
