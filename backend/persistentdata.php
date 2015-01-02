<?php
	require_once "interfaces.php";
	require_once "datamanager.php";

	abstract class PersistentData implements iData, iDataSubject, ArrayAccess{		
		public static $observer;
		protected $data;
		
		// auf diese weise auch andere spalten zu primary key
		//const ID_GEN = "int unsigned not null auto_increment primary key";
		
		public final function __construct($data=null){
			if($data==null){ 
				$this->data = array();
				foreach($this->getDataTypes() as $key => $value){
                                        $val="";
					if($value=="INTEGER" || $value=="INT") $val=0; 
					$this->data[$key] = $val;
				}
			}
			else $this->data = $data;		//nicht auch �ber setData() ?
			
			// attach observer
			$this->getObserver();
		}
		
		/*** getter ***/
		public function __get($name){
			if(isset($this->data[$name])){
				return htmlspecialchars(unescstr($this->data[$name]),ENT_QUOTES);
			}
			else{ 
				GetMessageSystem()->log("WA_key_not_exists", array("table_name" => $this->getTableName(), "name" => $name, "file" => __FILE__, "line" => __LINE__));
				return "";
			}
		}
		public function getData(){ return $this->data; }
		
		
		/*** setter ***/
		public function __set($name, $value){
			if ( isset($this->data[$name]) ){
				$this->data[$name] = $value;
				return true;
			}
			else{
				GetMessageSystem()->log("WA_key_not_exists", array("table_name" => $this->getTableName(), "name" => $name, "file" => __FILE__, "line" => __LINE__));
				return false; 
			}
		}
		public function setData($data){
			if(!is_array($data)) throw new Exception("expect an array, get something else");
			foreach($data as $key => $value){
				if ( isset($this->data[$key]) ){
					$this->data[$key] = $value;
				}
				else{
					GetMessageSystem()->log("WA_key_not_exists", array("table_name" => $this->getTableName(), "name" => $key, "file" => __FILE__, "line" => __LINE__));
				}
			}
		}
		
		public function __toString(){
			$ret="<br />".get_class($this).":<br />";
			foreach($this->getData() as $key => $value)
				$ret.= $key ." => ". $value. "<br />";
			return $ret;
		}

		
		/***** implements iDataSubject *****/
		public function save(){
			return self::$observer->updateData($this);
		}
		public function insert(){
			return $this->data["id"]= self::$observer->insertData($this);
		}
		public function delete(){
			$result = self::$observer->deleteData($this);
			unset($this->data);
			return $result;
		}
		public static function getObserver(){
			if(!isset(self::$observer)) self::$observer = DataManager::instance();
			return self::$observer;
		}
		
		/****** implements ArrayAccess ******/
		public function offsetExists($offset){
			if(isset($this->data[$offset])){
				return true;
			}
			else return false;
		}
		public function offsetGet($offset){
			return $this->$offset;
		}
		public function offsetSet($offset, $value){
			$this->$offset = $value;
			return;
		}
		public function offsetUnset($offset){ /*not implemented*/ }
	}
?>