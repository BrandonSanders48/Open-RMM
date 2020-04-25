<?php
	$nologin = true;
	include("../Includes/db.php");
	
	if($_SERVER['HTTP_REFERER']==""){
		$user = true;
		echo "<title>Agent Download</title>";
		$url = "../";
	}
?>
	<h4 style="color:<?php echo $siteSettings['theme']['Color 1'];?>">All Agent Versions
		<a href="#" title="Refresh" onclick="loadSection('Versions');" class="btn btn-sm" style="float:right;margin:5px;color:#fff;background:<?php echo $siteSettings['theme']['Color 1'];?>;">
			<i class="fas fa-sync"></i>
		</a>
	</h4>
	<p>Downloading Older Agent Versions May Expose The Client To Bugs Or Have Less Features Available. However, Older Versions May Help With Compatibility.</p>
	<hr>
	<h6 style="font-size:16px">
		Latest Version:
		<b>
			<a href="../../download/">
				<?php echo textOnNull($siteSettings['general']['agent_latest_version'], "Unknown");?>
			</a>
		</b>
	</h6>
	<hr>
	<div style="overflow:auto;width:100%;">
		<table style="line-height:20px;overflow:hidden;font-size:14px" class="table table-striped table-hover">
		  <thead class="thead-dark">
			<tr>
			  <th scope="col">#</th>
			  <th scope="col">Filename</th>
			  <th scope="col">Date Published</th>
			  <th scope="col">Actions</th>
			</tr>
		  </thead>
		  <tbody>
			<?php
			$key = 0;
			if ($handle = opendir('../downloads/')) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
					$key++;
					?>
					<tr <?php if($user==true){?> style="text-align:center;" <?php }?>>
					  <th scope="row"><?php echo $key;?></th>
					  <td>
						<?php echo $entry; ?>
						<?php echo (strpos($entry, $siteSettings['general']['agent_latest_version'])!==false ? " <b>- Latest</b>" : "");?>
					  </td>
					  <td><?php echo gmdate("m/d/Y",filemtime("../downloads/".$entry)); ?></td>
					  <td>
						<a class="btn btn-sm" <?php if($user==false){ echo 'style="color:#fff;background:'.$siteSettings['theme']['Color 4'].'"'; }else echo '"'; ?> href="../../download/index.php?file=<?php echo urlencode($entry); ?>">
							<?php if($user==false){ echo "<i class='fas fa-download'>&nbsp;</i>"; }else{ echo "Download"; } ?>
						</a>
						<?php if($user==false) { ?>
							<a style="color:#fff;" class="btn btn-danger btn-sm" href="#" data-toggle="modal" data-target="#versionModal" onclick="delVersion('<?php echo $entry; ?>')">
								<i class="fas fa-trash">&nbsp;</i>
							</a>
						<?php } ?>
					  </td>
					</tr>
				<?php
					}
				}
				closedir($handle);
			}
			if($key == 0){ ?>
				<tr>
					<td colspan=3><center><h5>No Files Found</h5></center></td>
				</tr>
			<?php }?>
		   </tbody>
		</table>
	</div>
	<script>
		function delVersion(Version){
			$("#delVersion_ID").val(Version);
		}
	</script>