<?php
	include("../Includes/db.php");
		$_SESSION['postSettings']="";	
		$_SESSION['postSettings']=array();	
?>
<h3 style="color:<?php echo $siteSettings['theme']['Color 1']; ?>">Site Settings</h3>
<hr>
<div style="width:100%;background:#fff;padding:15px;">
	<form method="POST" action="index.php">
		<div class="row">			
			<div class="col-sm-6">
				<?php 
				foreach($siteSettings as $title=>$settings) {
					$count++;						
					if($title=="Alert Settings" or $title=="Search Filters") { continue; }
					array_push($_SESSION['postSettings'], $settings);
				?>					
					<h4><?php echo ucwords($title); ?></h4>				
					<?php if(count($settings) == 1) {?>
						<div class="form-group float-label-control">
							<input type="text" name="<?php echo $settings; ?>" value="<?php echo $settings; ?>" class="form-control"/>
						</div>
						<?php continue;?>
					<?php }?>					
					<?php 
					foreach($settings as $item=>$setting) { 
						array_push($_SESSION['postSettings'], $item);
						if (count($settings) == count($settings, COUNT_RECURSIVE)){  ?>
							<div class="form-group float-label-control">
								<label><?php echo ucwords($item); ?>:</label>
								<input type="<?php if($item=="password"){echo "password"; }else{ echo "text"; } ?>" name="<?php echo $item; ?>" value="<?php echo $setting; ?>" class="form-control"/>
							</div>											
						<?php }else{ ?>
							<div class="form-group float-label-control">
								<label><?php echo ucwords($item); ?>:</label>
								<textarea style="text-align:left" name="<?php echo $item; ?>" class="form-control"><?php 										
									foreach($setting as $item2) { 									
										$count2 = 0;
										foreach($item2 as $item22=>$setting22) {
											$count2++;
											if($count2 > 1 ){ echo ", "; }
											echo $setting22;
										}										
									} 
								?></textarea>
							</div>											
						<?php } ?>										
					<?php } ?> 
					<?php if(count($settings) == 0){ echo "No Settings"; } ?>
				<?php } ?>				
			</div>
			
			<div class="col-sm-6">
				<h4>Search Filters</h4>			
				<?php 
				$count = 0;			
				foreach($siteSettings['Search Filters'] as $title=>$settings) {
					$count=0;
					array_push($_SESSION['postSettings'], $title);
					$value="";
					foreach($siteSettings['Search Filters'][$title]['options'] as $title2=>$settings2) {
						$count++;
						if($count>1){ $value .= ", ";}
						$value .= $settings2;						
					}
				?>
				<div class="form-group float-label-control">
					<label><?php echo ucwords($title); ?></label>
					<input type="text" name="<?php echo $title; ?>" value="<?php echo $value; ?>" class="form-control"/>
				</div>				
			<?php if(count($settings) == 0){ echo "<br>No Settings<br><br>"; } ?>
			<?php } ?>	
			</div>
		</div>
		<h4>Alert Settings</h4>
		<div class="row">		
			<?php 
			$count = 0;			
			foreach($siteSettings['Alert Settings'] as $title=>$settings) {
				$count=0;		
				$value="";
				array_push($_SESSION['postSettings'], $title);
				foreach($siteSettings['Alert Settings'][$title] as $title2=>$settings2) {
					if($title=="Disk"){
						$count++;
						if($count>1){ $value .= ", ";}
						$value .= $title2."=".$settings2;						
					}
					if($title=="Memory"){
						$count=0;
						$value .= $title2.": ";
						foreach($siteSettings['Alert Settings'][$title][$title2] as $title22=>$settings22) {
							$count++;								
							if($count>1){ $value .= ", ";}
							$value .= $title22." = ".$settings22;						
						}
						$value .= PHP_EOL;						
					}											
				}
			?>
			<div class="col-sm-3">
				<div class="form-group float-label-control">
					<label><?php echo ucwords($title); ?></label>
					<textarea rows=6 type="text" name="<?php echo $title; ?>" class="form-control"><?php echo $value; ?></textarea>
				</div>	
			</div>
			<?php if(count($settings) == 0){ echo "<br>No Settings<br><br>"; } ?>
			<?php } ?>	
		</div>
			<div style="margin-top:30px;" class="form-group float-label-control">                 
				<input style="background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" type="submit" class="form-control" value="Save Details"/>
			</div>
	</form>
</div>