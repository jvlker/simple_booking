<?php
require_once "../_printFunctions.php";
printHttpHeader();

error_reporting(E_ERROR | E_WARNING);

$string = "";
$e = PersonalData::getTable("active = 1","id DESC");
	
$string .= "'Datum';'Zeit';";
$datafields = Guard::instance()->getVar("datafields");
foreach($datafields AS $category => $fields)
{
	foreach($fields AS $key => $field)
	{
		$string .= "'" . $field["display"] . "';";
	}
}
$string .="\n\r";

if(!empty($e)) 
{
	foreach($e as $entry){	
		$string .= "'" . date("d.m.Y",$entry["time"])."';'".date("H:i",$entry["time"])."';";
		$data = $entry->getJSON();
		foreach($datafields AS $category => $fields)
		{
			foreach($fields AS $key => $field)
			{
				$string .= "'" . $data[$key] . "';";
			}
		}

		$string .= ";\n";
	}
}
else $string.="leer...";

header("content-encoding: utf-8");
header("content-type: text/csv; charset=utf-8");
header("content-length: ".strlen($string));
header("content-disposition: attachment; filename=\"bookings.csv\"");
echo "\xEF\xBB\xBF".$string; 

	
?>