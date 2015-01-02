<?php
	require_once "_printFunctions.php";
	printHttpHeader();
	
	if(isset($_GET) && isset($_GET["action"])){
		if( $_GET["action"]==="mail"){
			if($_POST["name"]!="" && $_POST["mail"]!="" && $_POST["text"]!=""){
				$text="<p style='background-color: #eef; padding: 10px; width:95%; margin: 10px;'>Anfrage von: ".strip_tags($_POST["name"])
					."<br />Mail: <a href='mailto:".strip_tags($_POST["mail"])."'>".strip_tags($_POST["mail"])."</a></p>"
					.nl2br(strip_tags($_POST["text"]));
				$from = (defined('SITE_MAIL') !== false ? SITE_MAIL : "MAIL");
				$header= 'From: '.$from."\n"
					.'Content-Type: text/html; Charset=utf-8'."\n"
					.'Content-Transfer-Encoding: 8bit'."\n";
				mail("info@kas-gruppe-dresden.de", "Anfrage über Kontaktformular von: ".strip_tags($_POST["name"]), $text , $header);
				GetMessageSystem()->output("ME_mail_success");
			}
			else
				GetMessageSystem()->output("ME_mail_failed");
			
			header('Location: contact.php');
			exit;
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<?php printHtmlHeader(); ?>
</head>
<body>
	<?php printTop(); ?>
	<div id='main'>
		<?php printNav(); ?>
		<div id='content'>
			<?php GetMessageSystem()->printMessagesAndErrors(); ?>
			<h1>Kontakt</h1>
			<p class="textS">Kontaktformular für Fragen, Anmerkungen, etc.</p>
			<form action="contact.php?action=mail" accept-charset="utf-8" method="post" onsubmit="return checkForm(this);">
				<table>
				<tr><td class='firstColumn'>Name</td>
					<td><input name="name" style="width: 300px; padding: 3px;" required/></td>
				</tr>
				<tr><td class='firstColumn'>Mailadresse</td>
					<td><input type="email" name="mail" style="width: 300px; padding: 3px;" required/></td>
				</tr>
				<tr><td class='firstColumn' style='vertical-align:top;'>Nachricht</td>
					<td><textarea name="text" style="width: 300px; height: 200px; padding: 3px;" required></textarea></td>
				</tr>
				<tr><td></td><td><input type="submit" value="Abschicken" class="formButton"/></td></tr>
				</table>
				
			</form>
			
		</div>
		<?php printFooter(); ?>
	</div>
	<script type='text/javascript'>
		function checkForm(elem){
			var success=true;
			var inputElems=elem.getElementsByTagName("input");
			for(i=0; i<inputElems.length; i++){
				if(inputElems[i].value==""){
					inputElems[i].className="colorRed";
					success=false;
				}
			}
			var textareaElems= elem.getElementsByTagName("textarea");
			for(i=0; i<textareaElems.length; i++){
				if(textareaElems[i].value==""){
					textareaElems[i].className="colorRed";
					success=false;
				}
			}
			if(!success) alert("Bitte füllen Sie alle rot-markierten Felder aus.");
			return success;
		}
	</script>
</body>
</html>