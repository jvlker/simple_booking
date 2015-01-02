<?php

	/**
	 *	Represents one log-entry.
	 * 	
	 *	@author J. Völker
	 */
	class Log extends PersistentData{
	
		/*** change this for your own data-class ***/
		public static function getDataTypes(){
			return array(   "id" => "",
							"lo_time" => "INT NOT NULL",
							"lo_code" => "CHAR(1)",
							"lo_message" => "CHAR(255) NOT NULL",
							"lo_description" => "TEXT",
							"lo_person_fk" => "INT");						
		}
		public static function getTableName(){
			return "kbb_log";
		}
		public static function newData($data=null){ return new Log($data); }
		
		
		/*** copy this to your own data-class ***/
		public static function create($data){
			if(!is_array($data)){ 
				GetMessageSystem()->log("ER_no_array", array("file" => __FILE__, "line" => __LINE__));
				return false; 
			}
			$newdata = self::newData();
			$newdata->setData($data);
			if($newdata->insert()!==false) return $newdata;
			else return false;
		}
		public static function getTable($where="", $order=""){
			return self::getObserver()->getData(self::newData(), $where, $order);
		}
		public static function getById($id){
			$array = self::getObserver()->getData(self::newData(), "id = ".(int)($id), "");
			if(isset($array[0])) return $array[0];
			return false;
		}
		public static function getOne($where){
			$array = self::getObserver()->getData(self::newData(), $where, "");
			if(isset($array[0])) return $array[0];
			return false;
		}
	}
?>