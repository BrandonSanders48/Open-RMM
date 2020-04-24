<?php
	include("../Includes/db.php");
	$computerID = (int)$_GET['ID'];
	$query = "SELECT ID, hostname FROM computerdata WHERE ID='".$computerID."' LIMIT 1";
	$results = mysqli_query($db, $query);
	$result = mysqli_fetch_assoc($results);
	$query = "SELECT ID, time_received,command, arg, expire_after,status,time_sent FROM commands WHERE status='Sent' or status='Received' AND ComputerID='".$result['hostname']."' ORDER BY ID DESC LIMIT 100";
	$results = mysqli_query($db, $query);
	$commandCount = mysqli_num_rows($results);
?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">Commands (<?php echo $commandCount;?>)</h4>
	<a href="#" title="Refresh" onclick="loadSection('Commands');" class="btn btn-sm" style="float:right;margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
<hr/>
 <div style="overflow:auto;width:100%">
	<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px" class="table table-striped table-hover">
	  <thead class="thead-dark">
		<tr>
		  <th scope="col">ID</th>
		  <th scope="col">Command</th>
		  <th scope="col">Argument</th>
		  <th scope="col">Expire Time</th>
		  <th scope="col">Time Sent</th>
		  <th scope="col">Time Received</th>
		  <th scope="col">Status</th>
		  <th scope="col"></th>
		</tr>
	  </thead>
	  <tbody>
		<?php
			//Fetch Results
			while($command = mysqli_fetch_assoc($results)){
				$count++;
			?>
			<tr>
			  <td><b><?php echo $command['ID'];?></b></td>
			  <td><b><?php echo ucwords($command['command']);?></b></td>
			  <td><?php echo textOnNull($command['arg'],"None");?></td>
			  <td><?php echo strtolower($command['expire_after']);?> Minutes</td>
			  <td><?php echo $command['time_sent'];?></td>
			  <td>
				  <?php if($command['time_received']!=""){
					  		echo  $command['time_received'];
					   }else{
						   echo "N/A";
					   } ?>
			  </td>
			  <td><b><?php echo $command['status'];?></b></td>
			   <td>
				   <form action="index.php" method="POST">
						<input type="hidden" name="type" value="DeleteCommand"/>
						<input type="hidden" name="ID" value="<?php echo $command['ID']; ?>"/>
							<button type="submit" title="Delete Command" style="border:none;" class="btn btn-danger btn-sm">
								<i class="fas fa-trash" ></i>
							</button>
					</form>
				</td>
			</tr>
		<?php }?>
		<?php if($count==0){ ?>
			<tr>
				<td colspan=30><center><h5>No Commands Found.</h5></center></td>
			</tr>
		<?php } ?>
	   </tbody>
	</table>
</div>
<script>
	//Edit User
	function editUser(ID, username, name, email){
		$("#editUserModal_ID").val(ID);
		$("#editUserModal_username").val(username);
		$("#editUserModal_name").val(name);
		$("#editUserModal_email").val(email);
		$("#editUserModal_password").prop('type', 'password').val("");
		$("#editUserModal_password2").prop('type', 'password').val("");
	}
</script>