<?php
require_once "../_printFunctions.php";
printHttpHeader();

$e = PersonalData::getTable("active = 1","id DESC");
$json_list = array();
if(!empty($e)) 
{
	foreach($e as $entry)
	{
		$json_list[] = $entry->getJSON();
	}
}
$string = json_encode($json_list, JSON_PRETTY_PRINT);

header("content-encoding: utf-8");
header("content-type: text/text; charset=utf-8");
header("content-length: ".(strlen($string) + 3));
header("content-disposition: attachment; filename=\"bookings.json\"");
echo "\xEF\xBB\xBF".$string; 
	
?>