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
			<h1>Buchung geschlossen</h1>
			
			<p>Es können keine Anmeldungen mehr erfolgen.</p>
		</div>
		<?php printFooter(); ?>
	</div>
</body>
</html>