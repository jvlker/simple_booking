<?php
	require_once "../_printFunctions.php";
	printHttpHeader();
	
	if(isset($_GET) && isset($_GET["action"])) switch($_GET["action"]) {
		case "remove_entry":{
			$id = (isset($_GET["id"]) ? $_GET["id"] : 0);
			$e = PersonalData::getById($id);
			$e["active"] = 0;
			if($e->save())
				GetMessageSystem()->output("ME_remove_success");
			else
				GetMessageSystem()->output("ER_save_failed");
			
			header('Location: index.php');
			exit;
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel='stylesheet' type='text/css' href='../style.css' />
	<?php printHtmlHeader(); ?>
	
	<style type="text/css">
	<!--
		table.admin_table{ background-color: #ddd; padding: 5px; border: 1px solid #bbb; }
		.admin_table tr{ background-color: #eee; }
		.admin_table tr:hover{ background-color: #fff; }
		.admin_table td, .admin_table th{ padding: 3px 5px; vertical-align: top; max-height: 100px; overflow: auto; }
		.admin_table .highlight { background-color: white; }
		
		.admin_list{ padding: 5px; list-style: none; font-size: 11pt; }	
		.admin_list small{ color:#777; }
		.admin_list > li{ background-color: #eee; padding: 5px; margin-bottom: 5px; border: 1px solid #aaa;  }
		.admin_list ul{ padding: 3px 5px; margin-left:20px; margin-top:5px; font-size:10pt; }

		.admin_nav{  display:block; margin:0px; padding:0px;}
		.admin_nav li{ float:left; list-style:none; margin:0px;}
		.admin_nav li a{ float:left; background-color:#eee; color:black; text-decoration:none; font-weight:bold; text-align:center; padding:5px 8px; border: 1px solid #ccc;}
		.admin_nav li a:hover{ background-color: #ddd; }
		
		table.admin_container td{ vertical-align: top; }
		
	-->
	</style>
</head>
<body>
	<?php printTop(); ?>
	<div id='main' style='width: 1100px;'>
		<div id='content' style='width: 100%;'>	
			<?php GetMessageSystem()->printMessagesAndErrors(); ?>
			<h1>Admin</h1>			
			<hr />
			<p style='float:right;'>Export: <a href='res_csv.php'>csv</a> | <a href='res_json.php'>json</a></p>
			<div style='width: 100%; overflow: auto;'>
			<?php echo getBookings(); ?>
			</div>
			<hr />
			<?php echo getLog(); ?>
			<hr />
			<?php echo getPHPInfo(); ?>
		</div>
		<?php printFooter(); ?>
	</div>
</body>
</html>

<?php
	/** @return a table with all personal-data entries and all json-fields. */
	function getBookings(){
		$string = "<h2>Buchungen</h2>";
		if(Guard::instance()->getVar("datafields") === false)
			return $string . "<p>no data found</p>";
			
		$no_cols = 1;
		$distribution = array();
		$string .= "<table class='admin_table' style='width:100%; font-size: 10pt;'><tr class='highlight'><th>Datum</th>";
		
		/** - print table head */
		foreach(Guard::instance()->getVar("datafields") AS $category => $fields)
		{
			foreach($fields AS $key => $field)
			{
				$string .= "<th>" . $field["display"] . "</th>";
				$no_cols++;
				if(isset($field["distribution"]) && $field["distribution"] === true)
					$distribution[$key] = array();
			}
		}
		$string .= "<th>Aktionen</th></tr>";
			
		$e = PersonalData::getTable("active = 1","id DESC");
		if(!empty($e)) 
		{
			/** - print entries */
			foreach($e as $entry){
				$string .= "<tr><td>" . date("d.m.Y", $entry["time"]). "<br />" . date("H:i", $entry["time"]) . "</td>";
				$data = $entry->getJSON();		
				if($data === null)
				{
					$string .= "<td colspan='" . ($no_cols-1) . "'>json parse error on entry #" . $entry["id"] . " (error: " . json_last_error_msg() . ")</td><tr>";
					continue;
				}
				foreach(Guard::instance()->getVar("datafields") AS $category => $fields)
				{
					foreach($fields AS $key => $field)
					{
						$d = "<i>unknown</i>";
						if(isset($data[$key]))
							$d = htmlspecialchars($data[$key], ENT_QUOTES);	
						$string .= "<td title='" . $d . "'>" . $d . "</td>";
						
						if(array_key_exists($key, $distribution))
							if(array_key_exists($d, $distribution[$key]))
								$distribution[$key][$d] += 1;
							else
								$distribution[$key][$d] = 1;
					}
				}
				$string .= "<td><a href='?action=remove_entry&id=" . $entry->id . "' onclick='return confirm(\"Diesen Eintrag wirklich löschen? (Dabei wird der Eintrag nur archiviert und kann von einem Admin auch wiederhergestellt werden.)\");'>löschen</a></td>";
				$string .= "</tr>";
			}
			
			/** - print distribution */
			$string .= "<tr class='highlight'><td></td>";
			foreach(Guard::instance()->getVar("datafields") AS $category => $fields)
			{
				foreach($fields AS $key => $field)
				{
					$string .= "<td class='textXS'>";
					if(isset($field["distribution"]) && $field["distribution"] === true)
					{
						$string .= "<table>";
						foreach($distribution[$key] AS $dk => $dv)
						{
							if(empty($dk))
								$dk = "<i>leer</i>";
							$string .= "<tr><td>" . $dk . "</td><td>" . $dv . "</td></tr>";
						}
						$string .= "</table>";
					}	
					$string .= "</td>";
				}	
			}
			$string .= "<td></td></tr>";
		}
		else $string.="<tr><td colspan='".$no_cols."'>keine Einträge</td></tr>";
		
		return $string . "</table>";
	}
	
	/** @return the log-table */
	function getLog(){
		$string = "<h2>Logfile</h2>";

		$string .= "<table class='admin_table' style='width:100%;'>"
			."<tr><th>Info</th><th>Beschreibung</th></tr>";
		$logs = Log::getTable("1", "lo_time DESC LIMIT 0,50");
		if(!empty($logs)) {
			foreach($logs as $log){
				$string .= "<tr><td class='textM'><b>".date("d.m.Y, H:i:s", $log["lo_time"])."</b>, Code: <b>".$log["lo_code"]."</b><br />"
					.$log["lo_message"]."</td><td><div style='max-height:150px; overflow:auto;'>".htmlspecialchars_decode($log["lo_description"])."</div></td></tr>";
			}
		} else {
			$string .= "<tr><td colspan='2'><i>keine Einträge</i></td></tr>";
		}
		$string .= "</table>";
		return $string;
	}
	
?>