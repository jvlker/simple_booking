<?php
	class DataManager implements iDataObserver{
		protected static $instance = null;
		protected $db;
		
		/***** implements Singleton *****/
		protected function __construct(){
			if(!defined("DB_HOST") || !defined("DB_USER") || !defined("DB_PASSWORD") || !defined("DB_NAME") || !defined("DB_PORT"))
				die("database not configured");
		
			$this->db = new DatabaseSQL(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
		}
		
		public static function instance(){
			if(self::$instance == null){
				self::$instance = new DataManager();
			}
			return self::$instance;
		}
		
		
		public function getData(iData $data, $where, $order){
			$array = array();
			try{
				$array = $this->db->getData($data, $where, $order);
			}
			catch(Exception $e){
				GetMessageSystem()->output("ER_exc_catch",array("file"=>__FILE__, "line"=>__LINE__));
			}
			return $array;
		}
		
		/***** other functions *****/
		public function select($select, $from, $where, $order){
			try{
				return $this->db->select($select, $from, $where, $order);
			}
			catch(Exception $d){
				GetMessageSystem()->output("ER_exc_catch",array("file"=>__FILE__, "line"=>__LINE__));
				return array();
			}
			
		}
		public function delete($from, $where){
			try{
				return $this->db->delete($from, $where);
			}
			catch(Exception $d){
				GetMessageSystem()->output("ER_exc_catch",array("file"=>__FILE__, "line"=>__LINE__));
				return array();
			}
		}
		
		public function insertData(iData $data){
			try{
				return $this->db->insertData($data);
			}
			catch(Exception $e){
				GetMessageSystem()->output("ER_exc_catch",array("file"=>__FILE__, "line"=>__LINE__));
				return false;
			}
		}
		
		public function createTable(iData $data){
			try{
				$this->db->createTable($data);
				return true;
			}
			catch(Exception $e){
				GetMessageSystem()->output("ER_exc_catch",array("file"=>__FILE__, "line"=>__LINE__));
				return false;
			}
		}
		
		public function updateData(iData $data){
			try{
				return $this->db->updateData($data);
			}
			catch(Exception $e){
				GetMessageSystem()->output("ER_exc_catch",array("file"=>__FILE__, "line"=>__LINE__));
				return false;
			}	
		}
		
		public function deleteData(iData $data){
			try{
				return $this->db->deleteData($data);
			}
			catch(Exception $e){
				GetMessageSystem()->output("ER_exc_catch",array("file"=>__FILE__, "line"=>__LINE__));
				return false;
			}
		}
		
		public function escape_string($string){
			return $this->db->escape_string($string);
		}
	}
	function escstr($string){ return Datamanager::instance()->escape_string($string); }
	function unescstr($string){ return str_replace("\\r\\n","\r\n", $string); }
	
	
	class DBException extends Exception{ 
		public $desc;
		function __construct($msg, $query, $error){ 
			parent::__construct($msg); 
			$this->desc = "<b>Query:</b> <i>".$query."</i><br/>".$error; 
			GetMessageSystem()->log("FE_db_exception", array("description" => $this, "file" => __FILE__, "line" => __LINE__));
		}
		function __toString(){ return "Exception: ".$this->desc.", <b>Errormessage</b>: ".parent::__toString(); }
	}

?>