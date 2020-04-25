<?php
	include("../Includes/db.php");
	$limit = intval($_GET['limit']);
	if($limit == 0){
		$limit = 20;
	}
	$add = 20;
	$count = 0;

	$search = $_GET['search'];
	$filters = $_GET['filters'];
	
	$query = "SELECT username,nicename FROM users WHERE ID='".$_SESSION['userid']."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$user = mysqli_fetch_assoc($results);
	$username = $user['username'];
	
	//Get Welcome Text
	function welcome(){
	   if(date("H") < 12){
	     return "Good Morning";
	   }elseif(date("H") > 11 && date("H") < 18){
	     return "Good Afternoon";
	   }elseif(date("H") > 17){
	     return "Good Evening";
	   }
   }
?>
	<div class="row" style="margin-bottom:10px;margin-top:-8px;border-radius:3px;overflow:hidden;padding:0px;">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding:5px;padding-bottom:20px;padding-top:1px;border-radius:6px;">
				<div class="d-none d-md-block ">	<p style="font-size:22px;display:inline;"><?php echo welcome().", ".ucwords($username);?>!</p>
					<a href="#" title="Refresh" onclick="loadSection('Dashboard');" class="btn btn-sm" style="float:right;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;margin-left:5px;">
						<i class="fas fa-sync"></i>
					</a>
					<a href="../../download/" style="display:inline;float:right;background:<?php echo $siteSettings['theme']['Color 4'];?>;color:#fff;" class="btn btn-sm"><i class="fas fa-download"></i> Download Agent</a>&nbsp;
					<a href="#"  data-toggle="modal" data-target="#agentUpload" style="margin-right:5px;display:inline;float:right;background:<?php echo $siteSettings['theme']['Color 5'];?>;color:#fff;" class="btn btn-sm"><i class="fas fa-upload"></i> Upload Agent</a>
				</div>
			   <div style="margin-top:5px;padding:15px;margin-bottom:8px;" class="card card-sm">
					<div style="padding:5px" class="input-group">
					  <div class="custom-file group-inline search-field-dashboard">
						<input id="searchInput" value="<?php echo $search; ?>" name="search" class="form-control form-control-md form-control-borderless" type="text" style="min-width:100px;" placeholder="Search By Hostname, Company Or Client Name"/>
						<button class="btn btn-md" id="search" style="margin-left:-4px;border-radius:0px 4px 4px 0px;background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" type="button" onclick="search($('#searchInput').val(), 'Dashboard', '', $('#filterInput').val());">
							<i class="fas fa-search"></i>
						</button>
					  </div>
					  <div class="input-group-append search-field-dashboard">
						&nbsp;<input id="filterInput" data-role="tagsinput" value="<?php echo $filters; ?>" name="filters" class="form-control form-control-md form-control-borderless" type="text" placeholder="Selected Filters"/>
						<button class="btn btn-md" style="margin-left:-4px;border-radius:0px 4px 4px 0px;background:<?php echo $siteSettings['theme']['Color 1']; ?>;color:#fff;" type="button" data-toggle="modal" data-target="#searchFilterModal">
							<i class="fas fa-cog"></i>
						</button>
					  </div>
					</div>
			   </div>
			   <form method="post" action="index.php">
				   <div id="printTable" style="overflow:auto;width:100%;">
					<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px;" class="table table-striped table-hover">
					  <col width="30">
					  <col width="30">
					  <col width="280">
					  <col width="120">
					  <col width="100">
					  <col width="180">
					  <col width="80">
					  <col width="180">
					  <col width="100">
					  <col width="70">
					  <thead class="thead-dark">
						<tr>
						  <th scope="col">
							<input onclick="toggle(this);" id="allcomputers" value="<?php echo $result['ID']; ?>" style="display:inline;appearance:none;" type="checkbox"/>
						  </th>
						  <th scope="col">#</th>
						  <th scope="col">Hostname</th>
						  <th style="height:2px" scope="col"></th>
						  <th scope="col">Logged In</th>
						  <th scope="col">Windows Version</th>
						  <th scope="col">Arch</th>
						  <th scope="col">Company</th>
						  <th scope="col">Disk Space</th>
						  <th scope="col">Actions</th>
						</tr>
					  </thead>
					  <tbody>
						<?php
							//Get Total Count
							$query = "SELECT ID FROM computerdata where active='1'";
							$results = mysqli_query($db, $query);
							$resultCount = mysqli_num_rows($results);
							$getFilters = explode(",", trim($filters, ","));
							if($search!=""){
								$query = "SELECT computerdata.show_alerts,computerdata.ID,companies.name,computerdata.hostname,computerdata.computerType FROM computerdata
										LEFT JOIN companies ON companies.CompanyID = computerdata.CompanyID
										WHERE computerdata.active='1' AND
										(computerdata.hostname LIKE '%".$search."%' OR companies.name LIKE '%".$search."%' OR computerdata.name LIKE '%".$search."%') 
										AND computerdata.active='1' ORDER BY computerdata.hostname ASC LIMIT 500";
							}else{
								$query = "SELECT * FROM computerdata
										  LEFT JOIN companies ON companies.CompanyID = computerdata.CompanyID
										  WHERE computerdata.active='1'
										  ORDER BY computerdata.hostname ASC Limit ".$limit;
							}
							//Fetch Results
							$count = 0;
							$results = mysqli_query($db, $query);
							while($result = mysqli_fetch_assoc($results)){
								if($search==""){
									$getWMI = array("WMI_LogicalDisk", "WMI_OS", "WMI_ComputerSystem", "Ping");
								}else{
									$getWMI = array("*");
								}
								$data = getComputerData($result['hostname'], $getWMI);
								//Filters
								if(count($getFilters) > 0 && $filters!=""){
									foreach($getFilters as $search){
										$filter = explode(":", trim($search));
										$filterType = trim($filter[0]);
										$filterValue = trim($filter[1]);
										//verify filter type
										if($siteSettings['Search Filters'][$filterType]['WMI_Name']!=""){
											$filter = $siteSettings['Search Filters'][$filterType];
											$WMI_Name = $filter['WMI_Name'];
											$WMI_Key = $filter['WMI_Key'];
											//validate value
											if(in_array($filterValue, $filter['options']) || in_array("*", $filter['options'])){
												if(strpos(strtolower($data[$WMI_Name."_raw"]), strtolower($filterValue))!==false){}else{continue(2);}
											}
										}
									}
								}
								//Determine Warning Level
								$freeSpace = $data['WMI_LogicalDisk'][0]['FreeSpace'];
								$size = $data['WMI_LogicalDisk'][0]['Size'];
								$used = $size - $freeSpace;
								$usedPct = round(($used/$size) * 100);
								if($usedPct > $siteSettings['Alert Settings']['Disk']['Danger'] ){
									$pbColor = "red";
								}elseif($usedPct > $siteSettings['Alert Settings']['Disk']['Warning']){
									$pbColor = "#ffa500";
								}else{ $pbColor = $siteSettings['theme']['Color 4']; }
								$count++;
						?>
					  	<tr>
							  <td>
								<input class="computerChkBox" name="computers[]" value="<?php echo $result['ID']; ?>" style="display:inline;appearance:none;" type="checkbox"/>
							  </td>
							  <th scope="row">
								<?php if($count==$limit - 10){?>
									<div id="l"></div>
								<?php } ?>
								<?php echo $count; ?>
							  </th>
							  <td>
								<?php
									$icons = array("desktop"=>"desktop","server"=>"server","laptop"=>"laptop");
									if(in_array(strtolower($result['computerType']), $icons)){
										$icon = $icons[strtolower($result['computerType'])];
									}else{
										$icon = "desktop";
									}
								?>
							  <?php echo $json['DefaultPrograms'][10]['Program']; ?>
								<a style="color:<?php echo $siteSettings['theme']['Color 1']; ?>" href="#" onclick="loadSection('General', '<?php echo $result['ID']; ?>');">
									<?php if(!$data['Online']) {?>
										<i class="fas fa-<?php echo $icon;?>" style="color:#666;font-size:16px;" title="Offline"></i>
									<?php }else{?>
										<i class="fas fa-<?php echo $icon;?>" style="color:green;font-size:16px;" title="Online"></i>
									<?php }?>
									&nbsp;<?php echo strtoupper($result['hostname']);?> <?php echo strtoupper($result['Hostname']);?>
								</a>
							  </td>
							  <td>
								<?php $alertCount = count($data['Alerts']);?>
								<?php if($result['show_alerts']=="1"){ ?>
									<?php if($alertCount > 0){?>
										<span style="cursor:pointer;font-size:14px;" data-toggle="modal" data-target="#computerAlerts" style="cursor:pointer;" onclick="computerAlertsModal('<?php echo strtoupper($result['hostname']);?>','<?php echo $data['Alerts_raw'];?>');">
											<i title="Warning" class="text-warning fa fa-circle" aria-hidden="true"></i>
											<?php echo $alertCount+2;?>
										</span>   
										<span style="cursor:pointer;margin-left:15px;" data-toggle="modal" data-target="#computerAlerts" style="cursor:pointer;" onclick="computerAlertsModal('<?php echo strtoupper($result['hostname']);?>','<?php echo $data['Alerts_raw'];?>');">
											<i title="Priority" class="text-danger fa fa-circle" aria-hidden="true"></i>
											<?php echo $alertCount;?>
										</span>
									<?php }else{?>
										<span class="" data-toggle="modal" data-target="#computerAlerts" style="cursor:pointer;" onclick="computerAlertsModal('<?php echo strtoupper($result['hostname']);?>');">
											<i class="text-success fas fa-circle"></i> 
										</span>
									<?php } ?>
								<?php}?>
							  </td>
							  <?php
									$username = textOnNull($data['WMI_ComputerSystem'][0]['UserName'], "Unknown");
							  ?>
							  <td style="cursor:pointer;" onclick="searchFilterAdd('LoggedIn: <?php echo ucwords((strpos($username, "\\")!==false ? explode("\\", $username)[1] : $username));?>');">
								<u>
									<?php echo ucwords((strpos($username, "\\")!==false ? explode("\\", $username)[1] : $username));	?>
								</u>
							  </td>
							  <td style="cursor:pointer;" onclick="searchFilterAdd('WinVer: <?php echo $data['WMI_OS'][0]['Caption'];?>');"><u><?php echo textOnNull(str_replace('Microsoft', '',$data['WMI_OS'][0]['Caption']), "Microsoft Windows");?></u></td>
							  <td style="cursor:pointer;" onclick="searchFilterAdd('Arch: <?php echo $data['WMI_OS'][0]['OSArchitecture'];?>');"><u><?php echo textOnNull($data['WMI_OS'][0]['OSArchitecture']);?></u></td>
							  <td>
								<a style="color:#000;" href="#" onclick="searchItem('<?php echo textOnNull($result['name'], "N/A");?>');">
									<u><?php echo textOnNull($result['name'], "Not Assigned");?></u>
								</a>
							  </td>
							  <td>
								<div class="progress" style="margin-top:5px;height:10px;background:<?php echo $siteSettings['theme']['Color 3']; ?>;" title="<?php echo $usedPct;?>%">
									<div class="progress-bar" role="progressbar" style=";background:<?php echo $pbColor;?>;width:<?php echo $usedPct;?>%" aria-valuenow="<?php echo $usedPct;?>" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							  </td>
							  <td>
								<a href="#" onclick="loadSection('Edit', '<?php echo $result['ID']; ?>');" title="Edit Client" style="margin-top:-2px;padding:5px;padding-top:2px;padding-bottom:2px;border:none;" class="form-inline btn btn-dark btn-sm">
									<i class="fas fa-pencil-alt"></i>
								</a>
								<a title="View Client" style="margin-top:-2px;padding:5px;padding-top:2px;padding-bottom:2px;border:none;background:<?php echo $siteSettings['theme']['Color 4'];?>" onclick="loadSection('General', '<?php echo $result['ID']; ?>');" href="#" class="form-inline btn btn-primary btn-sm">
									<i class="fas fa-eye"></i>
								</a>
							  </td>
							</tr>
						<?php }?>
						<?php  if($count==0){ ?>
							<tr>
								<td colspan=9>
									<p style="text-align:center;font-size:18px;">
										<b>No Computers To Display</b>
									</p>
								</td>
							</tr>
						<?php } ?>
					  </tbody>
					</table>
				</div>
				<button data-toggle="modal" type="button" data-target="#companyComputersModal2" style="background:<?php echo $siteSettings['theme']['Color 1'];?>;color:#fff" class="btn btn-sm">
					Add Selected To..
				</button>
				<button onclick="printData();" title="Export As CSV File" class="btn btn-sm" style="float:left;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;margin-right:5px;">
					<i class="fas fa-file-csv"></i> Export Table
				</button>
				
				<!------------- Add Company Computers ------------------->
				<div id="companyComputersModal2" class="modal fade" role="dialog">
				  <div class="modal-dialog modal-sm">
					<div class="modal-content">
					  <div class="modal-header">
						<h5 class="modal-title" id="pageAlert_title">Add Computers</h5>
					  </div>
					  <div class="modal-body">
						<h6 id="pageAlert_title">Select The Company You Would Like To Add These Computers Too</h6>
						<?php							
							$query = "SELECT CompanyID, name FROM companies ORDER BY CompanyID DESC LIMIT 100";
							$results = mysqli_query($db, $query);
							$commandCount = mysqli_num_rows($results);
							while($command = mysqli_fetch_assoc($results)){		
						?>
						  <div class="form-check">
							<input type="radio" required name="companies" value="<?php echo $command['CompanyID']; ?>" class="form-check-input" id="CompanyCheck">
							<label class="form-check-label" for="CompanyCheck"><?php echo $command['name']; ?></label>
						  </div>
						<?php } ?>
					  </div>
					  <div class="modal-footer">
						<input type="hidden" name="type" value="CompanyComputers">
						<button type="button" class="btn btn-sm" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;">Add</button>
					  </div>
					</div>
				  </div>
				</div>
			</form>
			<hr>
			<?php if($resultCount > $count && $search == ""){ ?>
				<center>
					<button  onclick="search($('#searchInput').val(),'Dashboard','','','<?php echo $limit + $add; ?>');" style="width:200px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;" class="btn">
						Load More
					</button>
					<button  data-toggle="modal" data-target="#confirmModal" style="background:<?php echo $siteSettings['theme']['Color 2'];?>;color:#fff;" class="btn btn-sm">
						Load All
					</button>
				</center>
			<?php } ?>
			<?php if($resultCount <= $count and $search==""){ ?>
				<div>
					<center>
						<a style="width:150px;background:<?php echo $siteSettings['theme']['Color 3'];?>;color:#fff;" class="btn">
							No More Results
						</a>
					</center>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<!--------------------------------------modals---------------------------------------------->
<div id="confirmModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		<h6 class="modal-title">Show All Computers?</h6>
	  </div>
	  <form method="post">
		  <div class="modal-body">
			<p style="font-size:14px;">This Will List All Computers. This Could Take A While.</p>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancel</button>
			<button type="button" onclick="search($('#searchInput').val(),'Dashboard','','','<?php echo $resultCount; ?>');" data-dismiss="modal" class="text-white btn-sm btn bg-danger">Confirm</button>
		  </div>
	  </form>
	</div>
  </div>
</div>

<div id="searchFilterModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
	<div class="modal-content">
	  <div class="modal-header">
		<h6 class="modal-title">Filter Search</h6>
	  </div>
	  <div class="modal-body">
		<p style="font-size:14px;">Select A Tag Or Multiple Tags To Refine Your Search. Please Note, Tags Are Not Case-Sensitive.</p>
		<div class="row">
			<div class="col-md-6" style="padding:5px;">
				<?php
				$count=0;
				foreach($siteSettings['Search Filters'] as $type=>$filter){
					$count++;
					if($count % 2 == 0){
					?>
						<div class="card" style="margin-bottom:5px;padding:5px;">
							<h6 style="font-size:14px;"><?php echo $filter['Nicename'];?></h6>
							<div class="row" style="margin-left:5px;">
								<?php foreach($filter['options'] as $option){?>
									<?php if($option == "*"){?>
										<?php $id = "sr".rand(100000,1000000000);?>
										<br>
										<div style="margin-left:-10px;margin-top:0px;" class="col-md-8 input-group mbs-3">
										  <input style="height:31px;font-size:14px;" type="text" class="form-control" placeholder="Custom" id="<?php echo $id;?>">
										  <div class="input-group-append">
											<button style="height:31px;font-size:12px;;border-color:#696969;color:#696969;" class="btn btn-outline btn-sm" type="button" id="button-addon2" onclick="searchFilterAdd('<?php echo $type;?>: '+$('#<?php echo $id;?>').val());">
												Go
											</button>
										  </div>
										</div>
									<?php }else{?>
										<button onclick="searchFilterAdd('<?php echo $type.": ".$option;?>');" style="border-radius:6px;margin-right:2px;margin-bottom:8px;padding:5px;font-size:12px;border-color:#696969;color:#696969" class="btn-outline btn btn-sm">
											<?php echo $option;?>
										</button>
									<?php }?>
								<?php }?>
							</div>
						</div>
					<?php } ?>
				<?php }?>
			</div>
			<div class="col-md-6" style="padding:5px;">
				<?php $count = 0;
				foreach($siteSettings['Search Filters'] as $type=>$filter){
					$count++;
					if($count % 2 == 0){ }else{ ?>
					<div class="card" style="margin-bottom:5px;padding:5px;">
							<h6 style="font-size:14px;"><?php echo $filter['Nicename'];?></h6>
								<div class="row" style="margin-left:5px;">
								<?php foreach($filter['options'] as $option){?>
									<?php if($option == "*"){?>
										<?php $id = "sr".rand(100000,1000000000);?>
										<br>
											<div style="margin-left:-10px;margin-top:0px;" class="col-md-8 input-group mbs-3">
											  <input style="height:31px;font-size:14px;" type="text" class="form-control" placeholder="Custom" id="<?php echo $id;?>">
											  <div class="input-group-append">
												<button style="height:31px;font-size:12px;border-color:#696969;color:#696969;" class="btn btn-outline btn-sm" type="button" id="button-addon2" onclick="searchFilterAdd('<?php echo $type;?>: '+$('#<?php echo $id;?>').val());">Go</button>
											  </div>
											</div>
									<?php }else{?>
										<button onclick="searchFilterAdd('<?php echo $type.": ".$option;?>');" style="border-radius:6px;margin-right:2px;margin-bottom:8px;padding:5px;font-size:12px;border-color:#696969;color:#696969" class="btn-sm btn-outline btn">
											<?php echo $option;?>
										</button>
									<?php }?>
								<?php }?>
							</div>
						</div>
					<?php } ?>
				<?php }?>
			</div>
		</div>
	  </div>
	  
	  <script>
	    //need to check if part after colon is empty or not. otherwise can filter ex. WinVer: (blank)
		function searchFilterAdd(filter){
			if ($("#filterInput").val().indexOf(filter) !== -1) {
				$("#searchFilterModal").modal('hide');
				$('body').removeClass('modal-open');
				$('.modal-backdrop').remove();
			}else{
				$("#filterInput").val($("#filterInput").val() + ", " + filter);
				$("#filterInput").val($("#filterInput").val().replace(/(^,)|(,$)/g, ""));
				$("#searchFilterModal").modal('hide');
				$('body').removeClass('modal-open');
				$('.modal-backdrop').remove();
				search($('#searchInput').val(),'Dashboard','', $('#filterInput').val());
			}
		}
	  </script>
	  
	  <div class="modal-footer">
		<button type="button" class="btn btn-sm" style="background:<?php echo $siteSettings['theme']['Color 2']; ?>;color:#fff;" data-dismiss="modal">Close</button>
	  </div>
	</div>
  </div>
</div>
<!---------------------------------End MODALS------------------------------------->

<script>
	function printData(filename) {
		var csv = [];
		var rows = document.querySelectorAll("#printTable table tr");
		for (var i = 0; i < rows.length; i++) {
			var row = [], cols = rows[i].querySelectorAll("td, th");
			for (var j = 0; j < cols.length; j++)
				row.push(cols[j].innerText.replace("Disk Space","").replace("Actions","").replace(/[^\w\s]/gi,"").replace(/\s/g,""));
			csv.push(row.join(","));
		}
		downloadCSV(csv.join("\n"), "page.csv");
	}
	
	function downloadCSV(csv, filename) {
		var csvFile;
		var downloadLink;
		csvFile = new Blob([csv], {type: "text/csv"});
		downloadLink = document.createElement("a");
		downloadLink.download = filename;
		downloadLink.href = window.URL.createObjectURL(csvFile);
		downloadLink.style.display = "none";
		document.body.appendChild(downloadLink);
		downloadLink.click();
	}
	
	function searchItem(text, page="Dashboard", ID=0, filters="", limit=25){
		$(".loadSection").load("ajax/"+page+".php?limit="+limit+"&search="+encodeURI(text)+"&ID="+ID+"&filters="+encodeURI(filters));
	}
	
	$('#searchInput').keypress(function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			searchItem($('#searchInput').val(),'Dashboard','', $('#filterInput').val());
		}
	});
</script>
<script src="js/tagsinput.js"></script>