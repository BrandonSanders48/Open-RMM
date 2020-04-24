<?php
	include("../Includes/db.php");
	$computerID = $_GET['ID'];
	//show recents on sidebar
	$recent = array_slice($_SESSION['recent'], -8, 8, true);
?>
	<h6 style="text-align:center;"><b>Recently Viewed</b></h6>	
		<?php
		$count = 0;
		foreach(array_reverse($recent) as $item) {
			$query = "SELECT ID, hostname FROM computerdata where ID='".$item."'";
			$results = mysqli_query($db, $query);
			$data = mysqli_fetch_assoc($results);
			if($data['ID']==""){ continue; }
			$count++;
		?>
			<a style="font-size:14px" class="text-dark" href="#" onclick="loadSection('General', '<?php echo $data['ID']; ?>');">
				<li class="bg-light list-group-item">
					<i style="float:left;font-size:16px;" class="fas fa-desktop"></i>
					<?php echo strtoupper($data['hostname']);?>
				</li>
			</a>
		<?php } ?>
		<?php if($count==0){ ?>
			<li class="list-group-item">No Recent Computers</li> 
		<?php } ?>