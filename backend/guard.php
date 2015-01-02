<?php
/**
 *	Global reachable class for session-managment, login, etc.
 *	Usage: Guard::instance()->getVar("test");
 * 	
 *	@author J. Völker
 */

	class Guard implements iSession{
		private static $instance;
		
		private function __construct(){
			$this->start();
			if(!isset($_SESSION["messagesystem"])) 
				$_SESSION["messagesystem"] = new MessageSystem();
		}
		public static function instance(){
			if(!isset(self::$instance)) self::$instance = new Guard();
			return self::$instance;
		}
		public static function hashPwd($pwd){ return @md5(sha1($pwd)); }
		public function start(){ session_start(); }
		
		public function isLogin($save = true){
			return ($this->issetVar("logged_in") && $this->getVar("logged_in"));
		}
		
		public function setVar($key, $value){ $_SESSION[$key] = $value; }
		public function unsetVar($key){ unset($_SESSION[$key]); }
		public function issetVar($key){ return isset($_SESSION[$key]); }
		public function getVar($key){ if(isset($_SESSION[$key])) return $_SESSION[$key]; else return false; }
		
		public function getVars()
		{
			return $_SESSION;
		}
		
		public function login($user, $pwd){
			if($user == USER_LOGIN && $pwd == USER_PASSWORD) {
				$this->setVar("logged_in", true);
				return true;
			} else {
				return false; 
			}
		}
		
		public function logout(){
			// Retten des MessageSystem
			$messagesystem = $_SESSION["messagesystem"];
			$mobile=null;
			if(isset($_SESSION["mobile"])) $mobile = $_SESSION["mobile"];
			
			Presence::logout(self::getVar("userId"));
			
			// Löschen aller Session-Variablen.
			$_SESSION = array();

			// Falls die Session gelöscht werden soll, löschen Sie auch das
			// Session-Cookie.
			// Achtung: Damit wird die Session gelöscht, nicht nur die Session-Daten!
			if (ini_get("session.use_cookies")) {
				$params = session_get_cookie_params();
				setcookie(session_name(), '', time() - 42000, $params["path"],
					$params["domain"], $params["secure"], $params["httponly"]
				);
			}
			// Zum Schluß, löschen der Session.
			session_destroy();
			
			// Neue, Ausgeloggte Session
			session_start();
			$_SESSION["messagesystem"] = $messagesystem;
			if(!empty($mobile)) $_SESSION["mobile"] = $mobile;
			$messagesystem->output("ME_logout_success");
		}
		public function destroy(){ return $this->logout(); }
	}
	function GetMessageSystem(){ return Guard::instance()->getVar("messagesystem"); }
?>