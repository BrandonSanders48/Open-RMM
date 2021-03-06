<?php
	include("../Includes/db.php");

	$computerID = (int)$_GET['ID'];
	$query = "SELECT ID, show_alerts, teamviewer,hostname, CompanyID, phone, email, name, comment,computerType FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$data = mysqli_fetch_assoc($results);
	$query = "SELECT CompanyID, name, phone, address, email, comments FROM companies WHERE CompanyID='".$data['CompanyID']."' LIMIT 1";
	$companys = mysqli_query($db, $query);
	$company = mysqli_fetch_assoc($companys);
	$json = getComputerData($result['hostname']);
?>
<?php if($data['hostname']==""){ ?>
	<br>
	<center>
		<h4>No Computer Selected</h4>
		<p>
			To Select A Computer, Please Visit The
			<a class='text-dark' href='index.php'><u>Dashboard</u></a>
		</p>
	</center>
	<hr>
<?php exit; }?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">Edit Computer
	<a href="#" title="Refresh" onclick="loadSection('Edit');" class="btn btn-sm" style="float:right;margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
</h4>
<hr>
<div style="width:100%;background:#fff;padding:15px;">
	<p class="lead">
	   <small class="text-muted"> Here You Can Add Information About The Computer, Client And The Company It's Assigned To.</small>
	</p>
	<hr />
<form method="POST" action="index.php">
	<div class="row">
		<div class="col-sm-8">
			   <input type="hidden" name="type" value="EditComputer"/>
			   <input type="hidden" name="ID" value="<?php echo $data['ID']; ?>"/>
			   <div class="form-group float-label-control">
				  <label for="">Company:</label>
				  <select name="company" class="form-control">
					<option value="<?php echo $company['CompanyID']; ?>"><?php echo textOnNull($company['name'],"Select A Company"); ?></option>
					<?php
						$query = "SELECT CompanyID, name FROM companies WHERE active='1' ORDER BY CompanyID ASC";
						$results = mysqli_query($db, $query);
						while($result = mysqli_fetch_assoc($results)){ ?>
							<option value='<?php echo $result['CompanyID'];?>'><?php echo $result['name'];?></option>
					<?php }?>
				  </select>
				  <br>
				  <label for="">Computer Type:</label>
				  <select name="pctype" class="form-control">
					<option value="<?php echo $data['computerType']; ?>"><?php echo textOnNull($data['computerType'],"Select A Computer Type"); ?></option>
					<option value="Laptop">Laptop</option>
					<option value="Desktop">Desktop</option>
					<option value="All-in-One">All-in-One</option>
					<option value="Tablet">Tablet</option>
					<option value="Server">Server</option>
					<option value="Other">Other</option>
				  </select>
				</div>
				<div class="form-group float-label-control">
					<label for="">TeamViewer ID:</label>
					<input type="text" name="TeamID" value="<?php echo $data['teamviewer']; ?>" class="form-control" placeholder="Teamviewer ID?">
				</div>
				<hr>
				<h4 class="page-header">Client Information</h4><br>
				<div class="form-group float-label-control">
					<label for="">Client Name:</label>
					<input type="text" name="name" value="<?php echo $data['name']; ?>" class="form-control" placeholder="What's Their Name?">
				</div>
				<div class="form-group float-label-control">
					<label for="">Client Phone:</label>
					<input type="text" name="phone" value="<?php echo $data['phone']; ?>" class="form-control" placeholder="What's Their Phone Number?">
				</div>
				<div class="form-group float-label-control">
					<label for="">Client Email Address:</label>
					<input type="email" name="email" value="<?php echo $data['email']; ?>" class="form-control" placeholder="What's Their Email Address?">
				</div>
				<div class="form-group float-label-control">
					<textarea rows=12 style="resize:vertical" placeholder="Any Comments?" name="comment" class="form-control" ><?php echo $data['comment']; ?></textarea>
				</div>
				<div style="margin-top:30px;" class="form-group float-label-control">
					<input style="background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff" type="submit" class="form-control" value="Save Details">
				</div>

		</div>
		 <div class="col-sm-4">
			<div class="panel panel-default" style="height:auto;color:#fff;background:<?php echo $siteSettings['theme']['Color 1']; ?>;color#000;padding:20px;border-radius:6px;margin-bottom:20px;">
				<center>
					<a style="width:56%;margin-top:-3px;border:none;" class="btn btn-danger btn-md" data-toggle="modal" data-target="#delModal" href="#">
						<i class="fas fa-trash"></i> Delete Computer
					</a>
				</center>
			</div>
			<hr>
			<div class="panel panel-default">
			   <div class="panel-heading">
					<h4 class="panel-title">
						Computer Settings
					</h4>
				</div>
				<div  class="panel-body">
				  <div class="form-check" style="border-radius:6px;margin-bottom:10px;padding:10px;padding-left:50px;background:<?php echo $siteSettings['theme']['Color 1']; ?>;color:#fff;">
					<input value="1" <?php if($data['show_alerts']=="1"){ echo "checked"; } ?>  name="show_alerts" type="checkbox" class="form-check-input" id="noalerts">
					<label class="form-check-label" for="show_alerts">Show Alerts For This Computer</label>
				  </div>
				</div>
			</div>
		</form>
			<div style="margin-top:20px;" class="panel panel-default">
			   <div class="panel-heading">
					<h4 class="panel-title">
						Company Information
					</h4>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<li class="list-group-item"><b>Company Name:</b>
							<a href="#" onclick="searchItem('<?php echo textOnNull($company['name'],"N/A"); ?>');" title="Search Company">
								<?php echo textOnNull($company['name'],"N/A"); ?>
							</a>
						</li>
						<li class="list-group-item"><b>Email:</b>
							<a href="mailto:<?php echo $company['email']; ?>">
								<?php echo textOnNull(ucfirst($company['email']),"N/A"); ?>
							</a>
						</li>
						<li class="list-group-item"><b>Phone:</b> <?php echo textOnNull(phone($company['phone']),"N/A"); ?></li>
						<li class="list-group-item"><b>Address:</b> <?php echo textOnNull($company['address'],"N/A"); ?></li>
						<li class="list-group-item"><b>Additional Info:</b> <?php echo textOnNull(ucfirst($company['comments']),"None"); ?></li>
					</ul>
				</div>
			</div>
			<div style="height:40px;" class="clearfix">&nbsp;</div>
			<div class="panel panel-default">
			   <div style="margin-bottom:20px;">
					<h4>Recently Edited</h4>
					<ul class="list-group">
						<?php
						$count = 0;
						$recentedit = array_slice($_SESSION['recentedit'], -4, 4, true);
						foreach(array_reverse($recentedit) as $item) {
							if($item==""){continue;}
							$query = "SELECT * FROM computerdata where ID='".$item."'";
							$results = mysqli_query($db, $query);
							$data = mysqli_fetch_assoc($results);
							if($data['hostname']==""){continue;}
							$count++;
						?>
							<a href="#" class="text-dark" onclick="loadSection('Edit', '<?php echo $data['ID']; ?>');">
								<li class="list-group-item">
									<i class="fas fa-desktop"></i>&nbsp;
									<?php echo strtoupper($data['hostname']);?>
								</li>
							</a>
						<?php } ?>
						<?php if($count==0){ ?>
							<li class="list-group-item">No Recent Computers</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<!-----------------------------------------modal------------------------------->
<div id="delModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h4 class="modal-title">Delete Computer</h4>
	  </div>
	  <div class="modal-body">
		<p>Are You Sure You Would Like To Delete This Computer? This Cannot Be Undone.</p>
	  </div>
	  <div class="modal-footer">
		  <form action="index.php" method="POST">
			<input type="hidden" name="type" value="DeleteComputer"/>
			<input type="hidden" name="ID" value="<?php echo $data['ID'];?>"/>
			<input type="hidden" name="hostname" value="<?php echo $data['hostname'];?>"/>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			<button type="submit" class="btn btn-danger">Confirm</button>
		  <form>
	  </div>
	</div>
  </div>
</div>