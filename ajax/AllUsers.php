<?php
	include("../Includes/db.php");

	$query = "SELECT ID,username,last_login,active,email,nicename,hex FROM users ORDER BY nicename ASC";
	$results = mysqli_query($db, $query);
	$userCount = mysqli_num_rows($results);
?>
<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">Users (<?php echo $userCount;?>)</h4>
	<a href="#" title="Refresh" onclick="loadSection('AllUsers');" class="btn btn-sm" style="float:right;margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
		<i class="fas fa-sync"></i>
	</a>
	<button type="button" style="margin:5px;background:<?php echo $siteSettings['theme']['Color 1'];?>;float:right;color:#fff" data-toggle="modal" data-target="#userModal" class="btn-sm btn btn-light" title="Add User">
		 <i class="fas fa-plus"></i> Add User
	</button>	

<hr/>
 <div style="overflow:auto;width:100%;">
	<table style="line-height:20px;overflow:hidden;font-size:14px;margin-top:8px;" class="table table-striped table-hover">
	 <col width="50">
	 <col width="200">
	 <col width="250">
	 <col width="100">
	 <col width="200">
	 <col width="80">
	 <col width="100">
	 <thead class="thead-dark">
		<tr>
		  <th scope="col">ID</th>
		  <th scope="col">Name</th>
		  <th scope="col">Email</th>
		  <th scope="col">Username</th>
		  <th scope="col">Last Login</th>
		  <th scope="col">Status</th>
		  <th scope="col"></th>
		</tr>
	  </thead>
	  <tbody>
		<?php
			//Fetch Results
			$count = 0;
			while($user = mysqli_fetch_assoc($results)){
				$count++;
				if($user['active']=="1"){
					$status="Active";
				}else{
					$status="Inactive";
				}						
			?>
			<tr>
			  <td><b><?php echo $user['ID'];?></b></td>
			  <td><b><?php echo ucwords($user['nicename']);?></b></td>
			  <td><a href="mailto:<?php echo strtolower(crypto('decrypt', $user['email'], $user['hex']));?>"><?php echo textOnNull(strtolower(crypto('decrypt', $user['email'], $user['hex'])),"No Email");?></a></td>
			  <td><?php echo strtolower($user['username']);?></td>
			  <td><?php echo textOnNull(gmdate("m/d/Y\ h:i A", $user['last_login']),"Never");?></td>
			  <td><?php echo $status;?></td>
			  <td>
				   <form action="index.php" method="POST">
						<input type="hidden" name="type" value="DeleteUser"/>
						<input type="hidden" name="ID" value="<?php echo $user['ID']; ?>"/>
						
						<?php if($user['active']=="1"){ ?>
							<input type="hidden" value="0" name="active"/>
							<button <?php if($user['ID']=="1") echo "disabled"; ?> type="submit" title="Remove User" style="border:none;" class="btn btn-danger btn-sm">
								<i class="fas fa-trash" ></i>				
							</button>
						<?php }else{ ?>
							<input type="hidden" value="1" name="active"/>
							<button type="submit" title="Add User" style="border:none;" class="btn btn-success btn-sm">
								<i class="fas fa-plus" ></i>
							</button>
						<?php } ?>
						
						<a href="#" data-toggle="modal" data-target="#userModal" onclick="editUser('<?php echo $user['ID'];?>','<?php echo $user['username'];?>','<?php echo $user['nicename'];?>','<?php echo crypto('decrypt', $user['email'], $user['hex']); ?>')" title="Edit User" style="border:none;" class="btn btn-dark btn-sm">
							<i class="fas fa-pencil-alt"></i>
						</a>
					</form>
				</td>
			</tr>
		<?php }?>
		<?php if($count == 0){?>
			<tr>
				<td colspan="7"><center>No users</center></td>
			</tr>
		<?php }?>
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