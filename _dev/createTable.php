<?php
/** 
 *	Development script to create all needed db-tables. Don't upload this to production-server.
 *
 * 	@author J. Völker
 */

require_once "../backend/interfaces.php";
require_once "../backend/persistentdata.php";
require_once "../backend/iDatabase/databasesql.php";
require_once "../backend/messagesystem.php";

require_once "../config/config.php";

require_once "../backend/objects/counter.php";
require_once "../backend/objects/log.php";
require_once "../backend/objects/personal_data.php";

try{
	DataManager::instance()->createTable(new PersonalData());
	DataManager::instance()->createTable(new Counter());
	DataManager::instance()->createTable(new Log());
	echo "success";
} catch (Exception $e) {
	echo "failed: " . $e;

}


