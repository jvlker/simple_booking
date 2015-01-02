<?php
	require_once "_printFunctions.php";
	printHttpHeader();
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
			<h1>Fertig!</h1>
			<p>Danke für die Eingabe, deine Angaben wurden gespeichert.</p>
			
			<?php if(defined('USER_CONFIRMATION_EMAIL') && USER_CONFIRMATION_EMAIL === true) { ?>
				<p>Du erhältst eine Bestätigungsmail an die angegeben E-Mail-Adresse innerhalb von 30min.</p>

				<p>Falls Du keine E-Mail erhalten hast, versuche es noch einmal oder wende Dich an <a href='mailto:<?php echo (defined('SITE_MAIL') !== false ? SITE_MAIL : "MAIL"); ?>'><?php echo (defined('SITE_MAIL') !== false ? SITE_MAIL : "MAIL"); ?></a>.</p>
			<?php } ?>
			
			<p>Keep swingin‘ !</p>
			
		</div>
		<?php printFooter(); ?>
	</div>
</body>
</html>