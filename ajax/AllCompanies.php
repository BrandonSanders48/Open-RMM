<?php
	include("../Includes/db.php");

	$query = "SELECT CompanyID,name,phone,email,address,comments,active FROM companies WHERE CompanyID<>'1' ORDER BY active,name ASC";
	$results = mysqli_query($db, $query);
	$companyCount = mysqli_num_rows($results);
?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">Companies (<?php echo $companyCount;?>)</h4>

<div style="float:right;">
	<a href="#" title="Refresh" onclick="loadSection('AllCompanies');" class="btn btn-sm" style="margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<button type="button" style="margin:5px;background:<?php echo $siteSettings['theme']['Color 1'];?>;color:#fff;" data-toggle="modal" data-target="#companyModal" class="btn-sm btn btn-light" title="Add User">
		<i class="fas fa-plus"></i> Add Company
	</button>
</div>

<hr/>
  <div style="overflow:auto;width:100%;">
	<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px;" class="table table-striped table-hover">
	  <col width="30">
	  <col width="220">
	  <col width="150">
	  <col width="150">
	  <col width="230">
	  <col width="150">
	  <col width="200">
	  <col width="200">
	 <thead class="thead-dark">
		<tr>
		  <th scope="col">ID</th>
		  <th scope="col">Name</th>
		  <th scope="col">Alerts</th>
		  <th scope="col">Phone</th>
		  <th scope="col">Email</th>
		  <th scope="col">Address</th>
		  <th scope="col">Comments</th>
		  <th scope="col"></th>
		</tr>
	  </thead>
	  <tbody>
		<?php
			//Fetch Results
			while($company = mysqli_fetch_assoc($results)){
				$computersWithAlerts = 0;
				$aggrigateAlerts = "";

				$query = "SELECT hostname FROM computerdata WHERE CompanyID='".$company['CompanyID']."'";
				$computerResults = mysqli_query($db, $query);
				$computerCount = mysqli_num_rows($computerResults);

				while($computerData = mysqli_fetch_assoc($computerResults)){
					$getWMI = array("WMI_LogicalDisk", "WMI_OS", "WMI_ComputerSystem");
					$data = getComputerData($computerData['hostname'], $getWMI);
					if(count($data['Alerts']) > 0){
						$computersWithAlerts++;
						$aggrigateAlerts .= $data['Alerts_raw'].",";
					}
				}
		?>
			<tr>
			  <td>
				  <b><?php echo $company['CompanyID'];?></b>
			  </td>
			  <td>
				<a href="#" onclick="searchItem('<?php echo $company['name'];?>');" title="Search Company">
					<b><?php echo $company['name'];?></b>
					&nbsp;(<?php echo $computerCount;?>)
				</a>
			  </td>
			  <td>
				<?php if($computersWithAlerts > 0){?>
					<a href="#" class="text-danger" data-toggle="modal" data-target="#computerAlerts" onclick="computerAlertsModal('<?php echo $company['name'];?>','<?php echo $aggrigateAlerts;?>', true);">
						<i title="Priority" class="text-danger fa fa-exclamation-triangle" aria-hidden="true"></i>
						<?php echo $computersWithAlerts;?> <?php echo ($computersWithAlerts > 1 ? "computers" : "computer");?>
					</a>
				<?php }else{?>
					<span class="text-success" data-toggle="modal" data-target="#computerAlerts" style="cursor:pointer;" onclick="computerAlertsModal('<?php echo strtoupper($company['name']);?>');">
						<i class="fas fa-thumbs-up"></i> No Issues
					</span>
				<?php }?>
			  </td>
			  <td>
				<?php echo textOnNull(phone($company['phone']),"No Phone");?>
			  </td>
			  <td>
				<a href="mailto:<?php echo $company['email'];?>">
					<?php echo textOnNull(ucfirst($company['email']),"No Email");?>
				</a>
			  </td>
			  <td>
				<?php echo textOnNull($company['address'],"No Address");?>
			  </td>
			  <td>
				<?php echo textOnNull(ucfirst($company['comments']), "No Comments");?>
			  </td>
			   <td>
				   <form action="index.php" style="display:inline;" method="POST">
						<input type="hidden" name="type" value="DeleteCompany"/>
						<input type="hidden" name="ID" value="<?php echo $company['CompanyID'];?>"/>
						<?php if($company['active']=="1"){ ?>
							<input type="hidden" name="active" value="0"/>
							<button type="submit" title="Remove Company" style="border:none;" class="btn btn-danger btn-sm">
								<i class="fas fa-trash"></i>
							</button>
						<?php }else{ ?>
							<input type="hidden" name="active" value="1"/>
							<button type="submit" title="Add Company" style="border:none;" class="btn btn-success btn-sm">
								<i class="fas fa-plus"></i>
							</button>
						<?php }?>

						<a href="#" data-toggle="modal" data-target="#companyModal" onclick="editCompany('<?php echo $company['CompanyID'];?>','<?php echo $company['name'];?>','<?php echo $company['address'];?>','<?php echo phone($company['phone']);?>','<?php echo ucfirst($company['email']);?>','<?php echo ucfirst($company['comments']);?>')" title="Edit Company" style="border:none;" class="btn btn-dark btn-sm">
							<i class="fas fa-pencil-alt"></i>
						</a>
					</form>
					<form action="index.php" method="post" style="display:inline;">
						<input type="hidden" value="<?php echo $company['CompanyID'];?>" name="companyAgent">
						<button type="submit" title="Download Company Agent" style="border:none;" class="btn btn-dark btn-sm">
							<i class="fas fa-download"></i>
						</button>
					</form>
					<form action="index.php" method="post" style="display:inline;">
						<input type="hidden" value="CompanyUpdateAll" name="type">
						<input type="hidden" value="<?php echo $company['CompanyID'];?>" name="CompanyID">
						<button type="submit" title="Update Company Agent" style="border:none;" class="btn btn-dark btn-sm">
							<i class="fas fa-cloud-upload-alt"></i>
						</button>
					</form>
				</td>
			</tr>
		<?php }?>
		<?php if($companyCount == 0){?>
			<tr>
				<td colspan="8"><center><h4>No companies</h4></center></td>
			</tr>
		<?php }?>
	   </tbody>
	</table>
</div>
<script>
	//Edit Company
	function editCompany(ID, name, address, phone, email, comments){
		$("#editCompanyModal_ID").val(ID);
		$("#editCompanyModal_name").val(name);
		$("#editCompanyModal_address").val(address);
		$("#editCompanyModal_phone").val(phone);
		$("#editCompanyModal_email").val(email);
		$("#editCompanyModal_comments").val(comments);
	}

	function searchItem(text, page="Dashboard", ID=0, filters="", limit=25){
		$(".loadSection").html("<center><h3 style='margin-top:40px;'><i class='fas fa-spinner fa-spin'></i> Loading "+text+"</h3></center>");
		$(".loadSection").load("ajax/"+page+".php?limit="+limit+"&search="+encodeURI(text)+"&ID="+ID+"&filters="+encodeURI(filters));
	}

</script>
