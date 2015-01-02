<?php
/**
 *	This object stores all data, that is specified in datafields.json.
 * 	
 *	@author J. Völker
 */

	class PersonalData extends PersistentData{
	
		/*** change this for your own data-class ***/
		public static function getDataTypes(){
			return array(  "id" => "",
							"time" => "INT",
							"ident_key" => "CHAR(255)",
							"json" => "TEXT",
							"active" => "BOOLEAN DEFAULT 1");
		}
		public static function getTableName(){
			return "kbb_personal_data";
		}
		public static function newData($data=null){ return new PersonalData($data); }
		
		
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
		
		/*** individual functions ***/
		public function getJSON()
		{		
			return json_decode($this->data["json"], true);
		}
	}
?>