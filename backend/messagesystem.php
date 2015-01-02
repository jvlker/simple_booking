<?php
	class MessageSystem implements iLog{
		private $messages = array();
		private $errors = array();
		
		public function __construct(){ }
		
		
		public function writeLog($code, $text, $description=""){
			Log::create(array("lo_time" => time(), "lo_code" => $code, "lo_message" => $text, "lo_description" => $description));
		}

		public function error($text){
			$this->errors[] = $text;
		}
		public function message($text){
			$this->messages[] = $text;
		}
		
		public function printMessagesAndErrors(){
			foreach($this->errors as $error) echo "<p class='ms_error'>".$error."</p>";
			foreach($this->messages as $msg) echo "<p class='ms_message'>".$msg."</p>";
			$this->errors = array();
			$this->messages = array();
		}
		
		public function log($key, $args = array() ){
			$s = "";
			switch($key){
				case "FE_table_not_exist": $s = "Die Tabelle '".$args["table_name"]."' exisitiert nicht, neu erstellen..."; break;
				case "FE_db_exception": $s = $args["description"]; break;
				case "ER_image_invalid_type": $s = "Kein gültiger Bildtyp übergeben"; break;
				case "FE_exc_catch": $s = "Exception abgefangen! ".$args["file"].":".$args["line"]; break;
				case "ER_no_array": $s = "Kein Array übergeben"; break;
				case "ER_json_parse": $s = "JSON parse failed (".$args["json_last_error"].")"; break;
				case "ER_get_datafields": $s = "datafields not found path: ".$args["path"]; break;
				case "WA_key_not_exists": $s = "this key doesn't exists in ".$args["table_name"]."::__get()/__set(): ".$args["name"]; break;
				case "FE_me_not_found": $s = "Keine Person mit der id ".$args["id"]." gefunden"; break;
			}
			switch(substr($key,0,2)){
				case "ME": $this->writeLog(0, $key, $s); break;
				case "WA": $this->writeLog(1, $key, $s); break;
				case "ER": $this->writeLog(2, $key, $s." in ".$args["file"].": ".$args["line"]); break;
				case "FE": $this->writeLog(3, $key, $s." in ".$args["file"].": ".$args["line"]); break;
				default:  $this->writeLog(1, "Keine Passende Aktion für '".$key."' gefunden", __FILE__.": ".__LINE__); break;
			}
		}
		
		public function output($key, $args = array() ){
			$s = "";
			switch($key){
				case "WA_not_logged_in": $s = "Bitte einloggen, um diese Seite anzuschauen"; break;
				case "WA_invalid_action": $s = "Keine gültige Aktion gewählt"; break;
				case "WA_moderator_access": $s = "Zugriff nur für Moderatoren"; break;
				case "WA_admin_access": $s = "Zugriff nur für Administratoren"; break;
				case "WA_created_more_than_one_admins": $s = "Es wurden mehrere Administratoren erstellt"; break;
				case "WA_is_already_admin": $s = "Person ist bereits Administrator"; break;
				case "WA_login_failed": $s = "Benutzername und Passwort stimmen nicht überein!<br /><small><a href='pwd_recover.php' class='link'>Passwort vergessen?</a></small>"; break;
				case "WA_login_mail_not_valid": $s = "Diese Mailadresse ist noch nicht überprüft<br /><small><a href='index.php?action=new_validation_mail&amp;mail=".$args["mail"]."' class='link'>Neue Mail zur Überprüfung verschicken</a></small>"; break;
				case "WA_login_empty_fields": $s = "Mail und Passwort dürfen nicht leer sein<small><a href='pwd_recover.php'>Passwort vergessen?</a></small>"; break;
				case "WA_mail_validation_failed": $s = "Die Überprüfung der Mailadresse ist schief gelaufen - <a href='index.php?action=new_validation_mail&amp;mail=".$args["mail"]."' class='link'>Überprüfungsmail neu verschicken</a></br><small>Wenn die Überprüfung erneut fehl schlägt, <a href='kontakt.php' class='link'>sag bescheid</a>, wir lösen das Problem.</small>"; break;
				case "WA_incomplete_form": $s = "Bitte alle Daten ausfüllen"; break;
				case "WA_no_profile_img": $s = "Kein Profilbild gefunden"; break;
				case "WA_security_logout": $s = "Es ist ein Fehler aufgetreten<br /><small>Zu deiner Sicherheit wurdest du ausgeloggt. Logge dich wieder ein oder kontaktiere uns unter <a href='kontakt.php' class='link'>Kontakt</a></small>"; break;
				case "WA_profile_not_found": $s = "Person wurde nicht gefunden, bitte versuche es erneut"; break;
                case "WA_no_person_to_mail": $s = "Es wurde keine Person mit dieser Mailadresse gefunden"; break;
				case "WA_out_of_range_pwd": $s = "Passwörter müssen mindestens ".$args["min"]." Zeichen  und maximal ".$args["max"]." Zeichen lang sein"; break;
				case "WA_out_of_range_mail": $s = "Die Mail mussen mindestens ".$args["min"]." Zeichen und maximal ".$args["max"]." Zeichen lang sein"; break;
				case "WA_out_of_range_name": $s = "Der Name mussen mindestens ".$args["min"]." Zeichen und maximal ".$args["max"]." Zeichen lang sein"; break;
				case "WA_old_pwd_need": $s = "Bitte das alte Passwort angeben"; break;
				case "WA_wrong_old_pwd": $s = "Das alte Passwort ist falsch"; break;
				case "WA_wrong_pwd": $s = "Das Passwort ist falsch"; break;
				case "WA_different_pwds": $s = "Die Passwörter stimmen nicht überein"; break;
				case "WA_mail_in_use": $s = "Diese Mailadresse wird bereits verwendet, bitte benutze eine andere"; break;
				case "WA_delete_image_failed": $s = "Diese Mailadresse wird bereits verwendet, bitte benutze eine andere"; break;
				case "WA_wrong_data_format": $s = "Falsches Datumsformat"; break;
				case "WA_to_young": $s = "Das Mindestalter für diese Anwendung ist ".$args["age"]." Jahre"; break;
				case "WA_no_choos_image": $s = "Es wurden keine Bilder ausgewählt"; break;
				case "WA_no_privilege_image": $s = "Keine Berechtigung zum Löschen des Bildes"; break;
				case "WA_no_choos": $s = "Es wurde keine Aktion gewählt"; break;
				case "WA_incorrect_mail": $s = $args["mail"]." ist keine Gültige eMail-Adresse"; break;
				case "WA_failed_upload_image": $s = "Der Upload von ".$args["name"]." ist fehlgeschlagen. Es dürfen nur folgende Dateitypen hochgeladen werden: <b>JPEG, GIF, PNG</b>. Die  Dateigröße all deiner Bilder darf maximal bei 10MB liegen."; break;
				case "WA_failed": $s = "Es ist ein Fehler aufgetreten"; break;
				case "WA_key_failed": $s = "Der angegebene Schlüssel ist leider ungültig."; break;
				case "WA_pers_blocked": $s = "Du hast keinen Zugriff auf dieses Profil"; break;

				case "ER_failed": $s = "Es ist ein Fehler aufgetreten"; break;
				case "ER_save_failed": $s = "Speicherung fehlgeschlagen"; break;
				case "ER_failed_upload_image": $s = "Es ist ein Fehler beim Upload von ".$args["name"]." aufgetreten. Bitte versuche es noch einmal."; break;
				case "ER_failed_try_again": $s = "Speichern fehlgeschlagen. Bitte versuche es erneut."; break;
				case "ER_failed_edit_image": $s = "Beim bearbeiten des Bildes ".$args["name"]." ist ein Fehler aufgetreten. Probiere es mit einem kleineren Bild erneut."; break;
				case "ER_exc_catch": $s = "Es ist ein interner Fehler aufgetreten. Versuche es noch einmal oder kontaktiere <a href='contact.php' class='link'>uns</a>."; break;
                                
				case "FE_db_check": $s = "Fehler bei der Datenbanküberprüfung: ".$args["exception"]; break;

				case "ME_save_success": $s = "Erfolgreich gespeichert!"; break;
				case "ME_remove_success": $s = "Erfolgreich gelöscht!"; break;
				case "ME_mail_success": $s = "Mail erfolgreich gesendet"; break;
				
				case "WA_input_error": $s = "Bitte überprüfe folgende Angaben: " . $args["agb"]; break;
			}
			switch(substr($key,0,2)){
				case "ME": $this->message($s); break;
				case "WA": $this->error($s); break;
				case "ER": 
					$this->error($s); 
					$this->writeLog(2, $key, $s." in ".$args["file"].": ".$args["line"]);
					break;
				case "FE": 
					$this->error($s); 
					$this->writeLog(3, $key, $s." in ".$args["file"].": ".$args["line"]);
					break;
				default:  
					$this->writeLog(1, "Keine Passende Aktion für '".$key."' gefunden", __FILE__.": ".__LINE__); 
					break;
			}

		}
		
	}

?>