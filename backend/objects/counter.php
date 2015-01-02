<?php	
/**
 *	Counter-class to count all site-visits.
 * 	
 *	@author J. Völker
 */

	class Counter extends PersistentData{	
		public static function getDataTypes(){
			return array(   "id" => "INTEGER", 
							"lastedit" => "INTEGER", 
							"createDate" => "INTEGER",
							"type" => "TEXT", 		//Browser
							"stayFor" => "INTEGER",
							"ipKey" => "TEXT", 
							"requests" => "INTEGER",
							"dbChanges" => "INTEGER",
							"comment" => "TEXT",	//Error
							"referer" => "TEXT");
		}
		public static function getTableName(){
			return "kbb_counter";
		}
		public static function newData($data=null){
			return new Counter($data);
		}
		
		
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
		
		
		/*** further data specific functions ***/
		public function __set($name, $value){
			if($name=="start_time") $this->start_time = $value;
			else parent::__set($name, $value);
		}
		public function __get($name){
			if($name=="start_time") return $this->start_time;
			else return parent::__get($name);
		}
		
		//Get latest Counter
		public static function getLastEntry($ipKey){
			$counter = self::getTable("ipKey='".escstr($ipKey)."'", "lastedit desc limit 0, 1");
			if(isset($counter[0])) return $counter[0];
			return false;
		}
		public static function count($dbChange=0, $comment=""){
			$ipKey=sha1($_SERVER["REMOTE_ADDR"]);
			$entry=self::getLastEntry($ipKey);
			if($entry instanceof Counter && (int)($entry->lastedit) > strtotime("-30 minutes") ){
				$entry->requests += 1;
				if($dbChange!=0) $entry->dbChanges += 1;
				$entry->stayFor = (int)($entry->lastedit) - (int)($entry->createDate);
				$entry->comment .= addslashes(strip_tags(trim($comment)));
				$entry->lastedit = time();
				$entry->save();
				
				$entry->start_time = microtime(true);
				return $entry;
			}
			else{
				//$type=addslashes(strip_tags(trim($type)));
				$type=$_SERVER["HTTP_USER_AGENT"];
				$comment=addslashes(strip_tags(trim($comment)));
				
				$new_counter = Counter::create( array(
					"createDate" => time(), "lastedit"=>time(), 
					"type"=>$type, "stayFor"=>0, "ipKey"=>$ipKey, 
					"requests"=>1, "dbChanges"=>$dbChange, "comment"=>$comment) );
				$new_counter->start_time = microtime(true);
				return $new_counter;
			}
		}
		
		public static function db_change(){
			self::count(1);
		}
		
		public static function getNumberOfCounts($start=0, $end=0 ){
			if($end==0) $end = time()+1;
			$WHERE = "createDate > ".(int)($start)." AND createDate < ".(int)($end);
			$list = DataManager::instance()->select("COUNT(*)", "counter_visitor", $WHERE, "");
			if(!empty($list[0]["COUNT(*)"])){
				return $list[0]["COUNT(*)"];
			}
			else return 0;
		}
		public static function sumClicks($start=0, $end=0){
			if($end==0) $end=time();
			$result= Counter::getTable("createDate > ".(int)($start)." AND createDate < ".(int)($end));
			$sum=0;
			foreach($result as $row){
				$sum += $row->requests;
			}
			return $sum;
		}
		public static function avgClicks($start=0, $end=0){
			if($end==0) $end=time();
			$result= self::getTable("createDate > ".(int)($start)." AND createDate < ".(int)($end));
			$count=0; $sum=0;
			foreach($result as $row){
				$sum+=$row->requests;
				++$count;
			}
			if($count==0) return 0;
			else return ((float)$sum)/((float)$count);
		}
		public static function avgStayFor($start=0, $end=0){
			if($end==0) $end=time();
			$result= self::getTable("createDate > ".(int)($start)." AND createDate < ".(int)($end));
			$count=0; $sum=0;
			foreach($result as $row){
				$sum+=$row->stayFor;
				++$count;
			}
			if($count==0) return 0;
			else return ((float)$sum)/((float)$count);
		}
		public static function sumDBChanges($start=0, $end=0){
			if($end==0) $end=time();
			$result= self::getTable("createDate > ".(int)($start)." AND createDate < ".(int)($end));
			$sum=0;
			foreach($result as $row){
				$sum+=$row->dbChanges;
			}
			return $sum;
		}
	}
?>