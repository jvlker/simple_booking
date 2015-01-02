<?php
	class DatabaseLite implements iDatabase{
		protected $path;
		protected $user;
		protected $password;
		protected $dbname;
		protected $db;
		
		public function __construct($path, $dbname){
			$this->path=$path;
			$this->dbname=$dbname;
			$this->openDB();
		}
		
		protected function query($query){
			$var=@$this->db->query($query);
			if($this->db->lastErrorCode()==1){
				throw new DBException($this->db->lastErrorCode(), $query, $this->db->lastErrorMsg());
			}
			
			return $var;
		}
		
		/***** implement iDatabase *****/
		public function openDB(){
			if(!isset($this->db)) $this->db=new SQLite3($this->path.$this->dbname);
		}
		public function createDB(){
			$this->query("CREATE DATABASE ".$this->dbname);
		}
		function closeDB(){
			//$this->db->close();
		}
		public function insertData(iData $data){
			$query=""; $keys=""; $values="";
			foreach( $data->getData() as $key => $value ){
				if($keys!="") $keys.=", ";
				if($values!="") $values.=", ";
				if($key=="id"){ }//$keys.="id"; $value.="null"; }
				else{
					if(isset($value)){
						if(is_string($value)){ $keys.="$key"; $values.="'".$this->escape_string($value)."'"; }
						else{ $keys.="$key"; $values.="$value"; }
					}
				}
			}
			$query="INSERT INTO ".$data->getTableName()." ($keys) VALUES ($values);";
			$result=$this->query($query);
			$id=$this->db->lastInsertRowID();
		
			if(!$result) return false;
			return $id;
		}
		public function updateData(iData $data){
			$query="UPDATE ".$data->getTableName()." SET ";
			foreach($data->getData() as $key => $value){
				if($key=="id") $query.="$key=$value";
				else{ 
					if(isset($value)){
						if(is_string($value)) $query.=", $key='".$this->escape_string($value)."' ";
						else $query.=", $key=$value ";
					}
				}
			}
			$query.="where id=". $data->id.";";
			$result=$this->query($query);
			
			if(!$result) return false;
			else return true;
		}
		public function deleteData(iData $data){
			$id= $data->id;
			$query="DELETE FROM ".$data->getTableName()." WHERE id=".$data->id;
			$result=$this->query($query);
			if(!$result) return false;
			return $id;
		}
		public function createTable(iData $data){
			$query="CREATE TABLE ".$data->getTableName()." (";
			foreach($data->getDataTypes() as $key => $value){
				if($key=="id") $query.="$key INTEGER PRIMARY KEY";
				else $query.=", $key $value";
			}
			$query.=");";
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
			
			while($row = $result->fetchArray()){
				$res_array[] = $data->newdata($row);
			}
			return $res_array;
		}
		public function select($select, $from, $where, $order=""){
			$query="SELECT $select FROM $from ";
			if($where!="") $query.= " WHERE $where ";
			if($order!="") $query.= " ORDER BY $order";
			$result=$this->query($query);
			$res_array = array();
			
			while($row = $result->fetchArray()){
				$res_array[] = $row;
			}
			return $res_array;
		}
		
		public function delete($from, $where){
			$query = "DELETE FROM $from WHERE $where";
			return $this->query($query);
		}
		public function escape_string($string){ return $this->db->escapeString($string); }
	}