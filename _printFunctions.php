<?php
/**
 *	This file collects some useful methods, 
 *	that can be called from each site.
 * 	
 *	@author J. Völker
 */
	
	function getLocalPath()
	{
		$idx = strrpos(__FILE__, "/");
		if($idx === false)
		{
			$idx = strrpos(__FILE__, "\\");
			if($idx !== false)
			{
				return substr(__FILE__, 0, $idx);
			} else {
				GetMessageSystem().log("ER_get_datafields", array("path" => __FILE__, "file" => __FILE__, "line" => __LINE__));
				GetMessageSystem().output("WA_failed");
				return false;
			}
		}
	}
	
	function readJSON(){
		$path = getLocalPath();
		
		if(empty($path)) $path = ".";
		$file = file_get_contents($path . "/config/datafields.json");
		if($file === false)
			$file = file_get_contents("../config/datafields.json");
		if($file === false)
		{
			GetMessageSystem()->writeLog("2", "ER_FILE", "Could not load file: " . getLocalPath()."/config/datafields.json");
			GetMessageSystem()->error("Leider ist ein Fehler aufgetreten... Bitte informiere den Admin der Seite darüber.");
			return false;
		}
		
		$file = trim($file);
		$json = json_decode($file, true);
		
		if($json === NULL){		
			GetMessageSystem()->log("ER_json_parse", array("json_last_error" => json_last_error_msg(), "file" => __FILE__, "line" => __LINE__));
			GetMessageSystem()->error("Leider ist ein Fehler aufgetreten... Bitte informiere den Admin der Seite darüber.");
			return false;
		} else {
			return $json;
		}
	}

	function printHttpHeader(){
		require_once "backend/interfaces.php";
		require_once "backend/persistentdata.php";
		require_once "backend/iDatabase/databasesql.php";
		require_once "backend/messagesystem.php";

		require_once "config/config.php";
				
		require_once "backend/objects/counter.php";
		require_once "backend/objects/log.php";
		require_once "backend/objects/personal_data.php";
		
		include_once "backend/datamanager.php";
		include_once "backend/guard.php";
		
		header('Content-Type: text/html; charset=utf-8');
		Counter::count();
		Guard::instance();
		
		Guard::instance()->unsetVar("datafields");
		
		if(!Guard::instance()->issetVar("datafields"))
			Guard::instance()->setVar("datafields", readJSON());
	}
	
	function printHtmlHeader(){
?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" ><!-- must be the first in head!!! -->
		<link rel='stylesheet' type='text/css' href='style.css' />
		<link rel="icon" href="images/favicon.ico" type="image/ico" />
		<link rel="shortcut icon" href="images/favicon.ico" />
		<meta name="description" content="" />
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="content-language" content="de" />
		<meta name="keywords" content="" />
		<meta name="page-topic" content="">
		<meta name="language" content="Deutsch" />
		<meta name="revisit-after" content="14 days" />
		<meta name="robots" content="INDEX,FOLLOW" />
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<title><?php echo (defined('TITLE') !== false ? TITLE : "TITLE"); ?></title>
<?php
	}

	function printTop(){
?>
		<div id='top'>	
			<h1><?php echo (defined('TITLE') !== false ? TITLE : "TITLE"); ?></h1>
		</div>
		
<?php
	}

	function printNav(){
		echo "<div id='nav'>";
		?>
			<div><ul>
				<li><a href='index.php'>Willkommen</a></li>
				<li><a href='booking.php'>Anmeldung</a></li>
				<li><a href='contact.php'>Kontakt</a></li>
			</ul></div>
		<?php
		echo "</div>";
	}
	
	function printFooter(){
		echo "<div id='footer'>"
			."Stand 16.12.2014"
			."<a href='impressum.php' style='float:right;'>Impressum</a>"
		."</div>";
	}
	
	function getPHPInfo(){
		$string = "<h2>PHP Info</h2>";
		$string .= "<b>PHP Version: </b>".phpversion()."<br />";
		$string .= "<b>PHP Extensions: </b>";
		foreach(get_loaded_extensions() as $ext) $string.= $ext.", ";
		
		$string .= "<br /><b>PHP ini: </b><br />";
		$string .= 'error_reporting = ' . ini_get('error_reporting') . "<br />";
		$string .= 'register_globals = ' . ini_get('register_globals') . "<br />";
		$string .= 'post_max_size = ' . ini_get('post_max_size') . "<br />";
		$string .= 'upload_max_filesize = ' . ini_get('upload_max_filesize') . "<br />";
		$string .= 'memory_limit = ' . ini_get('memory_limit') . "<br />";
		$string .= 'allow_url_include = ' . ini_get('allow_url_include') . "<br />";

		return $string;
	}
	
	function printSelect(array $data, $name, $selected = "", $required = false){
		echo "<select name='".$name."' id='".$name."' ".($required ? "required" : "") .">";
		echo "<option value=''>BITTE WÄHLEN</option>";
		
		foreach($data as $key => $value){
			$sel = "";
			if($key == "" || is_numeric($key)) $key = $value;
			if($key == $selected)
				$sel = "selected";
			echo "<option value='".$key."' ".$sel.">".$value."</option>";
		}
		echo "</select>";
	}
?>