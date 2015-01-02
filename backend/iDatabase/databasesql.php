<?php
	
	class DatabaseSQL implements iDatabase{
		protected $path;
		protected $user;
		protected $password;
		protected $dbname;
		protected $db;
		protected $port;
		
		public function __construct($path, $user, $pwd, $dbname, $port){
				$this->path=$path; 
				$this->user = $user; 
				$this->password = $pwd;	
				$this->dbname=$dbname; 
				$this->port=$port;
				
				$this->openDB();
		}
		public function __destruct(){
			$this->closeDB();
		}
		
		protected function query($query){
//echo "<p style='color:blue;'><b>".$query."</b></p>";
			$var=@$this->db->query($query);
			if($this->db->sqlstate!="00000"){
				throw new DBException($this->db->error, $query, $this->db->sqlstate);
			}
			
			return $var;
		}
		
		/***** implement iDatabase *****/
		public function openDB(){
			if(!isset($this->db)) $this->db=new mysqli($this->path, $this->user, $this->password, $this->dbname, $this->port);
			$this->db->set_charset("utf8");
		}
		public function createDB(){
			$this->query("CREATE DATABASE ".$this->dbname);
		}
		function closeDB(){
			$this->db->close();
		}
		public function insertData(iData $data){
			$query="INSERT INTO ".$data->getTableName()." SET ";
			foreach($data->getData() as $key => $value){
				if($key=="id") $query.="id = NULL";
				else{ 
					if(isset($value)){
						if(is_string($value)) $query.=", $key='".$this->escape_string($value)."' ";
						else $query.=", $key=".$value;
					}
				}
			}
			$query.=";";
			$result=$this->query($query);
			if(!$result) return 0;
			else return $this->db->insert_id;
		}
		public function updateData(iData $data){
			$query="UPDATE ".$data->getTableName()." SET ";
			foreach($data->getData() as $key => $value){
				if($key=="id") $query.="$key=$value";
				else{ 
					if(isset($value)){
						if(is_string($value)) $query.=", $key='".$this->escape_string($value)."' ";
						else $query.=", $key=".$value;
					}
				}
			}
			$query.=" where id=". $data->id.";";
			
			$result=$this->query($query);
			if(!$result) return false;
			else return true;
		}
		public function deleteData(iData $data){
			$id = $data->id;
			$query="DELETE FROM ".$data->getTableName()." WHERE id=".$this->db->escape_string($data->id);
			$result=$this->query($query);
			if(!$result) return 0;
			return $id;
		}
		public function createTable(iData $data){
			$query="CREATE TABLE IF NOT EXISTS ".$data->getTableName()." (";
			foreach($data->getDataTypes() as $key => $value){
				if($key=="id") $query.="$key int unsigned not null auto_increment primary key";
				else $query.=", $key $value";
			}
			$query.=") default character set 'UTF8' ENGINE = MyISAM;";
			//echo $query;
			$result=$this->query($query);
			if(!$result) return false;
			else return true;
		}
		public function getData(iData $data, $where, $order=""){
			$query="SELECT * FROM ".$data->getTableName();
			if($where!="") $query.= " WHERE $where ";
			if($order!="") $query.= " ORDER BY $order";
			$result=$this->query($query);
			$res_array = array();
			while($row = $result->fetch_assoc()){
				$res_array[] = $data->newData($row);
			}
			
			return $res_array;
		}
		public function select($select, $from, $where, $order=""){
			$query="SELECT $select FROM $from ";
			if($where!="") $query.= " WHERE $where ";
			if($order!="") $query.= " ORDER BY $order";
			$result=$this->query($query);
			$res_array = array();
			
			//  ersatzlösung für php 5.2
			while($row = $result->fetch_assoc()){
				$res_array[] = $row;
			}
			return $res_array;
			
			// funktioniert nur mit php 5.3
			//return $result->fetch_all(MYSQLI_ASSOC);
		}
		
		public function delete($from, $where){
			$query = "DELETE FROM $from WHERE $where";
			return $this->query($query);
		}
		
		public function escape_string($string){
			return $this->db->real_escape_string($string);
		}
	}