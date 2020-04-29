<?php
	//Load Config
	include("config.php");
	$siteSettings = json_decode($siteSettingsJson, true);
	
	//Set Timezone, need to add this to config.
	date_default_timezone_set($siteSettings['general']['Timezone']);
	$serverPages = array("cron.php", "LoadHistorical.php");
	
	//Only do this if login is required
	if(!in_array(basename($_SERVER['SCRIPT_NAME']), $serverPages)){
		ini_set('session.gc_maxlifetime', 3600);
		ini_set('display_errors', 0);
		SESSION_START();
	}
	
	##############################################################################################################
	########################################### Connect To Database ##############################################
	##############################################################################################################
	
	if(strtolower(basename($_SERVER['REQUEST_URI'])) != "login.php"){
		$db = mysqli_connect($siteSettings['MySQL']['host'], $siteSettings['MySQL']['username'], $siteSettings['MySQL']['password'], $siteSettings['MySQL']['database']);
		if(!$db){ //Produce HTML Error
			echo "<center>";
			echo "<div style='font-family:Arial;font-size:20px;'>";
			echo "<h1>Open-RMM</h1>";
			echo "<h2 style='color:red;'>Error establishing a database connection.</h2>";
			echo "<p> This issue can be caused by incorrect database information in your config.php file, corrupt database, or an irresponsive database server.</p>";
			echo "</div>";
			exit("</center>");
		}
	}
	
	##############################################################################################################
	############################################ Verifiy Logged In ###############################################
	##############################################################################################################
	if($nologin == false){
		if($_SESSION['userid']=="" && strtolower(basename($_SERVER['REQUEST_URI'])) != "login.php" && !in_array(basename($_SERVER['SCRIPT_NAME']), $serverPages)){
			if(strpos(strtolower($_SERVER['SCRIPT_NAME']),"/ajax/")!==false){ //fix for ajax pages
				echo "<center><h3>Error loading page, please make sure you are logged in.</h3></center>";
				exit("<script>location.href='index.php';</script>");
			}else{
				echo "Redirecting you to the login page...";
				header("location: login.php");
				exit;
			}
		}
	}
	
	//Load general settings from DB
	function loadGeneralFromDB(){
		global $db;
		$query = "SELECT * FROM general WHERE ID='1' LIMIT 1";
		$results = mysqli_query($db, $query);
		$general = mysqli_fetch_assoc($results);
		return $general;
	}
	array_push($siteSettings['general'], loadGeneralFromDB());
	
	##########################################################################################################
	###################################### Get PC Data From Database #########################################
	##########################################################################################################
	function getComputerData($hostname, $fields = array("*"), $date = "latest"){
		global $db, $siteSettings;
		$retResult = array();
		
		if(in_array("*", $fields)){
			$query = "SELECT WMI_Name, WMI_Data, last_update FROM wmidata WHERE Hostname='".$hostname."'";
		}else{
			$query = "SELECT WMI_Name, WMI_Data, last_update FROM wmidata WHERE (";
			//Only get wanted fields
			foreach($fields as $field){
				$query .= " WMI_Name = '".$hostname."|".$field."' OR";
			}
			$query = trim($query, "OR");
			$query .= ") AND Hostname='".$hostname."'";
		}
		
		//DateTime
		if($date != "latest"){
			$query .= " AND last_update LIKE '".clean($date)."%'";
		}
		$query .= " ORDER BY ID DESC";
		$results = mysqli_query($db, $query);
		while($row = mysqli_fetch_assoc($results)){
			$data = explode("|", $row['WMI_Name']);
			if(isset($retResult[$data[1]])){continue;}
			if($data[1]!="Ping"){
				$decoded = jsonDecode($row['WMI_Data'], true);
				$retResult[$data[1]] = $decoded['json'];
				$retResult[$data[1]."_raw"] = $row['WMI_Data'];
				$retResult[$data[1]."_error"] = $decoded['error'];
			}else{
				$retResult[$data[1]] = $row['WMI_Data'];
			}
			$retResult[$data[1]."_lastUpdate"] = $row['last_update'];
		}
		
		//Online/Offline
		if(strtotime($retResult['Ping']) < strtotime("-".$siteSettings['Online Threshold'])) {
			$retResult["Online"] = false;
		}else{
			$retResult["Online"] = true;
		}
		$getAlerts = getComputerAlerts($hostname, $retResult);
		$retResult["Alerts"] = $getAlerts[0];
		$retResult["Alerts_raw"] = $getAlerts[1];
		return $retResult;
	}
	
	##########################################################################################################
	############################################## PC Alerts #################################################
	##########################################################################################################
	function getComputerAlerts($hostname, $json){
		global $siteSettings;
		$alertArray = array();
		$alertDelimited = "";
		//Memory
		//Total
		$totalMemory = round((int)$json['WMI_ComputerSystem'][0]['TotalPhysicalMemory'] /1024 /1024 /1024,1); //GB
		if($totalMemory < $siteSettings['Alert Settings']['Memory']['Total']['Danger']){
			$alertName = "memory_total_danger";
			$newAlert = array(
				"subject"=>"Memory",
				"message"=>"Total memory is real low (Current: ".$totalMemory." GB)",
				"type"=>"danger",
				"hostname"=>$hostname,
				"alertName"=>$alertName
			);
			$alertArray[] = $newAlert;
			$alertDelimited .= implode("|", $newAlert).",";
		}elseif($totalMemory < $siteSettings['Alert Settings']['Memory']['Total']['Warning']){
			$alertName = "memory_total_warning";
			$newAlert = array("subject"=>"Memory",
				"message"=>"Total memory is getting low (Current: ".$totalMemory." GB)",
				"type"=>"warning",
				"hostname"=>$hostname,
				"alertName"=>$alertName
			);
			$alertArray[] = $newAlert;
			$alertDelimited .= implode("|", $newAlert).",";
		}
		//Free
		$freeMemory = round($json['WMI_OS'][0]['FreePhysicalMemory'] / 1024,1); //MB
		if($freeMemory < $siteSettings['Alert Settings']['Memory']['Free']['Danger']){
			$alertName = "memory_free_danger";
			$newAlert = array(
				"subject"=>"Memory",
				"message"=>"Free memory is real low (Current: ".$freeMemory." MB)",
				"type"=>"danger",
				"hostname"=>$hostname,
				"alertName"=>$alertName
			);
			$alertArray[] = $newAlert;
			$alertDelimited .= implode("|", $newAlert).",";
		}elseif($freeMemory < $siteSettings['Alert Settings']['Memory']['Free']['Warning']){
			$alertName = "memory_free_warning";
			$newAlert = array(
				"subject"=>"Memory",
				"message"=>"Free memory is getting low (Current: ".$freeMemory." MB)",
				"type"=>"warning",
				"hostname"=>$hostname,
				"alertName"=>$alertName
			);
			$alertArray[] = $newAlert;
			$alertDelimited .= implode("|", $newAlert).",";
		}
		//Disk Space
		$disks = $json['WMI_LogicalDisk'];
		foreach($disks as $disk){
			$freeSpace = $disk['FreeSpace'];
			$size = $disk['Size'];
			$used = $size - $freeSpace ;
			$usedPct = round(($used/$size) * 100);
			if($usedPct > $siteSettings['Alert Settings']['Disk']['Danger']){
				$alertName = "disk_warning";
				$newAlert = array(
					"subject"=>"Disk",
					"message"=>$disk['Caption']." is real low on space (".(100-$usedPct)." GB free)",
					"type"=>"danger",
					"hostname"=>$hostname,
					"alertName"=>$alertName
				);
				$alertArray[] = $newAlert;
				$alertDelimited .= implode("|", $newAlert).",";
			}elseif($usedPct > $siteSettings['Alert Settings']['Disk']['Warning']){
				$alertName = "disk_danger";
				$newAlert = array(
					"subject"=>"Disk",
					"message"=>$disk['Caption']." is getting low on space (".(100-$usedPct)." GB free)",
					"type"=>"warning",
					"hostname"=>$hostname,
					"alertName"=>$alertName
				);
				$alertArray[] = $newAlert;
				$alertDelimited .= implode("|", $newAlert).",";
			}
		}
		
		//Check agent version
		if($siteSettings['general']['agent_latest_version'] != $json['AgentVersion']['Value']){
			$alertName = "agent_version";
			$newAlert = array(
				"subject"=>"Agent Version",
				"message"=>"Agent is out of date. Currently installed: ".textOnNull($json['AgentVersion']['Value'], "Unknown"),
				"type"=>"warning",
				"hostname"=>$hostname,
				"alertName"=>$alertName
			);
			$alertArray[] = $newAlert;
			$alertDelimited .= implode("|", $newAlert).",";
		}
		
		//Windows Activation
		if($json['WindowsActivation']['Value'] != "Activated"){
			$alertName = "windows_activation";
			$newAlert = array(
				"subject"=>"Windows Activation",
				"message"=>"Not Activated",
				"type"=>"warning",
				"hostname"=>$hostname,
				"alertName"=>$alertName
			);
			$alertArray[] = $newAlert;
			$alertDelimited .= implode("|", $newAlert).",";
		}
		return array($alertArray, trim($alertDelimited, ","));
	}

	##########################################################################################################
	######################################### Core Functions #################################################
	##########################################################################################################

	//Fix Empty Text
	function textOnNull($text, $onnull=""){
		return (trim($text)=="" ? $onnull : $text);
	}
	
	//For DB use
	function clean($string) {
		$remove = array("'");
		$replaceWith = array("");
		return str_replace($remove, $replaceWith, $string);
	}
	
	//Clean Phone
	function phone($number) {
		if(ctype_digit($number) && strlen($number) == 10) {
		$number = "(".substr($number, 0, 3) .') '. substr($number, 3, 3).'-'. substr($number, 6);
		} else {
			if(ctype_digit($number) && strlen($number) == 7) {
				$number = substr($number, 0, 3) .'-'. substr($number, 3, 4);
			}
		}
		return $number;
	}
	
	//Time Ago
	function ago($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	
	//Encrypt And Decrypt With Salt
	function crypto($action, $string, $salt) {
		return $string;
	}
	
	/* Seem to be having issues with this, disabled 4/25/20
	$salt = base64_decode($siteSettings['general']['Crypto_salt']);
	function crypto($action, $string, $salt) {
		global $siteSettings;
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = base64_decode($siteSettings['general']['Crypto_key']);
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $salt), 0, 16);
		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if( $action == 'decrypt' ) {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
	*/
	
	//Get Random Salt
	function getSalt($n = 40) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $n; $i++) {
			$index = rand(0, strlen($characters) - 1);
			$randomString .= $characters[$index];
		}
		return $randomString;
	}
	//Custom JsonDecode with error handling
	function jsonDecode($json, $assoc = false) {
		$ret = json_decode(utf8_encode($json), $assoc);
		if ($error = json_last_error()){
			$errorReference = [
				JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded.',
				JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON.',
				JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded.',
				JSON_ERROR_SYNTAX => 'Syntax error.',
				JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded.',
				JSON_ERROR_RECURSION => 'One or more recursive references in the value to be encoded.',
				JSON_ERROR_INF_OR_NAN => 'One or more NAN or INF values in the value to be encoded.',
				JSON_ERROR_UNSUPPORTED_TYPE => 'A value of a type that cannot be encoded was given.'
			];
			$err = isset($errorReference[$error]) ? $errorReference[$error] : "Unknown error ($error)";
		}
		return array("json"=>$ret, "error"=>$err);
	}
?>