<?php
	include("../Includes/db.php");

	$settings = new RecursiveIteratorIterator(
	new RecursiveArrayIterator($siteSettings),
	RecursiveIteratorIterator::SELF_FIRST);
	$count=0;
	foreach ($settings as $key => $val) {
		if(is_array($val)) {
			$count=0;
			$text .= "<h4>$key:</h4>";
		} else {
			$count++;
			if($count>1){ $text .= ", "; }
			$text .= "$key => $val";
			
		}
	}
?>

<h4 style="color:<?php echo $siteSettings['theme']['Color 1']; ?>">Site Settings</h4>
<hr>
<div style="width:100%;background:#fff;padding:15px;">
	<form method="POST" action="index.php">
		<input type="hidden" name="type" value="saveSiteSettings"/>
		<textarea name="settings" id="siteSettingsTextArea" style="max-width:600px;width:100%;min-height:800px;"><?php echo $text;  ?></textarea>		

		<div style="margin-top:30px;" class="form-group float-label-control">                 
			<input style="background:<?php echo $siteSettings['theme']['Color 4']; ?>;color:#fff;" type="submit" class="form-control" value="Save Details"/>
		</div>
	</form>
</div>
<script>
	function getSiteSettings(){
		var retdata;
		$.post("index.php", {
		  type: "getSiteSettings",
		},
		function(data, status){
		  $("#siteSettingsTextArea").val(prettyJson(data));
		});	
		return retdata;
	}
	
	function prettyJson(json) {
		var ugly = json
		var obj = JSON.parse(ugly);
		var pretty = JSON.stringify(obj, undefined, 4);
		return pretty;
	}

	getSiteSettings();

    tinymce.init({
      selector: 'textarea',
	  browser_spellcheck: false,
      plugins: 'a11ychecker advcode casechange formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
      toolbar: 'a11ycheck addcomment showcomments casechange checklist code formatpainter pageembed permanentpen table',
      toolbar_mode: 'floating',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'SMG_RMM',
    });
</script>