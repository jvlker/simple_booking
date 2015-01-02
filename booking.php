<?php
	require_once "_printFunctions.php";
	
	printHttpHeader();
	
	if(isset($_GET) && isset($_GET["action"])){
		switch($_GET["action"]){
			case "login":{
				if(isset($_POST["user"]) && isset($_POST["password"]) && Guard::instance()->login($_POST["user"], $_POST["password"])){
					GetMessageSystem()->message("Login erfolgreich");
					Guard::instance()->setVar("user_auth", true);
				} else {
					GetMessageSystem()->error("Login fehlgeschlagen");
				}
				header('Location: booking.php');
				exit;
			}
			case "save":{
				// save
				$ident_key = (isset($_GET["ident_key"]) ? $_GET["ident_key"] : "");
				$key = insert($_POST, $ident_key);
				
				if($key === false){
					GetMessageSystem()->output("WA_failed");
					header('Location: booking.php');
					exit;
				} else {
					if(defined('USER_CONFIRMATION_EMAIL') && USER_CONFIRMATION_EMAIL === true)
					{
						// todo send mail
						sendMail($key);
					}
					
					GetMessageSystem()->message("Erfolgreich gespeichert");
					header('Location: booking_success.php');
					exit;
				}
				break;
			}
			case "change_entry":{
				if(defined('USER_UPDATE') && USER_UPDATE)
				{					
					$ident_key = (isset($_GET["ident_key"]) ? $_GET["ident_key"] : null);
					$entry = PersonalData::getTable("ident_key = '" . $ident_key . "'");
					if(!empty($entry)) {
						$entry_values = $entry[0]->getJSON();
					} else {
						GetMessageSystem()->output("WA_profile_not_found");
						header('Location: booking.php');
						exit;
					}
				} else {
					header('Location:booking.php');
					exit;
				}
				break;
			}
			case "accept_agb":{
				if(isset($_POST["accept_agb"]) && $_POST["accept_agb"] == "true")
					Guard::instance()->setVar("accept_agb", true);
				else
					GetMessageSystem()->error("Die AGB müssen bestätigt werden, bevor zu deine Daten eingeben kannst.");
					
				header('Location:booking.php');
				exit;
				break;
			}
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<?php printHtmlHeader(); ?>
	<link href="css/kas-theme/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<script src="js/jquery-1.9.1.js"></script>
	<script src="js/jquery-ui-1.10.3.custom.min.js"></script>
</head>

<body>
	<?php printTop(); ?>
	<div id='main'>
		<?php printNav(); ?>
		<div id='content'>
			<?php GetMessageSystem()->printMessagesAndErrors(); ?>
				
			<noscript><p>Diese Seite ist nur mit aktiviertem Javascript voll funktionsfähig. Bitte aktiviere es bevor du mit der Bearbeitung fortfährst.</p></noscript>
				
			<h1>Anmeldung</h1>
			
			<?php 
				// show login dialog
				if(	defined('USER_AUTH') 
					&& USER_AUTH === true 
					&& (!Guard::instance()->isLogin()/*!Guard::instance()->issetVar("authentication") || Guard::instance()->getVar("user_auth") !== true*/) 
				) {
			?>
				<p>Bitte gib hier die Nutzername- Passwordkombination ein, um fort zu fahren.</p>
				<form action="?action=login" method="post">
				<table class="input_table">
					<tr><td class="firstColumn">Nutzer</td><td><input type="text" name="user" required /></td></tr>
					<tr><td class="firstColumn">Password</td><td><input type="password" name="password" required /></td></tr>
					<tr><td></td><td><input type="submit" value="Einloggen" /></td></tr>
				</table>
				</form>
			<?php 
				} else if( defined('USER_AGB')
					&& USER_AGB === true
					&& (!Guard::instance()->issetVar("accept_agb") || Guard::instance()->getVar("accept_agb") !== true) 
				) {
			?>
				<div class='textM'><?php echo nl2br(USER_AGB_TEXT); ?></div>
				<hr />
				<form action='?action=accept_agb' method='post'>
					<input type='checkbox' name='accept_agb' value='true' required />
					<span class='textM'>
						Ich habe die Punkte 1 bis 7 gelesen und verstanden und stimme den Bedingungen für das Seminar zu.
					</span><br />
					<input type='submit' value='Weiter' />
				</form>
			<?php 
				} else {
			?>
				<form id='booking_form' action='?action=save<?php if(isset($ident_key)) echo "&amp;ident_key=" . $ident_key; ?>' accept-charset='utf-8' method='post'>
				<?php if(Guard::instance()->getVar("datafields") !== false) foreach(Guard::instance()->getVar("datafields") AS $category => $fields){ ?>
					<hr /><h4><?php echo $category; ?></h4>
					<table class='input_table'>
						<?php if(is_array($fields)) foreach($fields AS $key => $field) { ?>
						<tr>
							<td class="firstColumn"><?php echo $field["display"]; ?></td>
							<td><?php 
								$value = (isset($entry_values[$key]) && USER_UPDATE ? $entry_values[$key] : "");
								$required = (isset($field["required"]) && $field["required"] === true ? "required='required' " : "");
								switch($field["type"]){
									case "select":
										printSelect($field["select-values"], $key, $value, $required);
										break;
										
									case "textarea":
										echo "<textarea name='".$key."' style='height:".(isset($field["textarea-height"]) ? $field["textarea-height"] : "100px").";' ".$required.">" . $value . "</textarea>";
										break;
									
									default:
										echo "<input type='".$field["type"]."' name='".$key."' value='". $value . "' " . $required . "/>";
										break;
								}
								?></td>
							<td class="description"><?php if(isset($field["description"])) echo $field["description"]; ?></td>
						</tr>
						<?php } ?>
					</table>
				<?php } ?>	
					<hr />
					<input type='submit' class='formButton' value='Speichern' />
				</form>
			<?php } // endif steps ?>
		</div>
	</div>
</body>
</html>

<?php
	/** save one PersonalData entry with given ident_key */
	function insert($data, $ident_key = ""){
		$json = array();
		foreach(Guard::instance()->getVar("datafields") AS $category => $fields)
			foreach($fields AS $key => $field)
				if(isset($data[$key]))
					$json[$key] = $data[$key];
		
		$json_string = json_encode($json);
		if (empty($ident_key))
		{
			$ident_key = hash("sha256", $json_string);
			if(PersonalData::create( array("time" => time(), "ident_key" => $ident_key, "json" => $json_string, "active" => true)) )
				return $ident_key;
			else 
				return false;
		} else {
			$entry = PersonalData::getTable("ident_key = '" . $ident_key . "'");
			if(!empty($entry)) {
				$entry[0]["json"] = $json_string;
				$entry[0]["time"] = time();
				$entry[0]["active"] = true;
				return $ident_key;
			} else {
				return false;
			}
		}
	}
	
	function sendMail($ident_key)
	{
		$entry = PersonalData::getTable("ident_key = '" . $ident_key . "'");
		if(empty($entry)) {
			GetMessageSystem()->output("WA_profile_not_found");
			return false;
		}
		
		$data = $entry[0]->getJSON();

	
		$EOL = "\r\n";
		$header= "From:kas-bigband.de<noreply@kas-bigband.de>".$EOL;
		$header.= "X-Mailer: PHP/".phpversion().$EOL;  
		$header.= "MIME-Version: 1.0".$EOL;
		$header .= 'Content-Type: text/plain; Charset=utf-8'.$EOL;
		$header .= 'Content-Transfer-Encoding: 8bit'.$EOL;

		$text = "Hallo " . (isset($data["fname"]) ? $data["fname"] : "") . "," . $EOL . $EOL;
		$text .= "Es wurden folgende Daten von dir gespeichert:".$EOL;
		
		foreach(Guard::instance()->getVar("datafields") AS $category => $fields)
			foreach($fields AS $key => $field)
				if(isset($data[$key]))
					$text .= "   * " . $field["display"] . ": " . $data[$key] . $EOL;
		$text .= $EOL;
				
		if(defined('USER_UPDATE') && USER_UPDATE === true)
		{
			$text .= "Sollte sich ein Fehler eingeschlichen haben oder deine Daten ändern sich, kannst du Sie über den folgenden Link ändern: " . $EOL . $EOL
			. "http://kas-bigband.de/booking.php?action=change_entry&ident_key=" . $entry[0]["ident_key"] . $EOL.$EOL;
		}
			
		$text .= "Keep swingin‘ !"
			. $EOL.$EOL."______________________________________".$EOL
			."Diese Mail wurde automatisch von www.kas-bigband.de verschickt.".$EOL
			."Bitte antworte nicht auf diese Mail. Bei Fragen oder Kritik nutze das Formular unter\r\n http://kas-bigband.de/contact.php";
		 
		
		if(isset($data["email"]) && !empty($data["email"]))		
			return mail($data["email"], '=?UTF-8?B?'.base64_encode("[kas-bigband.de] Anmeldung für die KAS Bigband").'?=', $text, $header);
		else
			return false;
		//GetMessageSystem()->message("Mail: " . nl2br($text));
		//return true;
	}
?>