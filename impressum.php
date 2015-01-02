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
			<h1>Impressum</h1>
			
			<h3>Ansprechpartner</h3>
			<p>Mail: <?php echo (defined('SITE_MAIL') !== false ? SITE_MAIL : "MAIL"); ?></p>
			
			<p><b>Organisation</b><br />
			...
			
			<h3>Copyright</h3>
			<p>Alle Designs, Bilder und Grafiken sind urheberrechtlich geschützt. Jegliche Verwendung bedarf der Zustimmung (<?php echo (defined('SITE_MAIL') !== false ? SITE_MAIL : "MAIL"); ?>).</p>
			<p>Das Speichern, Kopieren und Ausdrucken dieser Internetseite (auch von einzelnen Elementen) darf nur für den privaten Gebrauch geschehen.	Das Veröffentlichen der Inhalte bzw. der Internetseite selber ist untersagt!</p>
			
			<h3>Inhalte und Links</h3>
			<p>Für die Aktualität und Vollständigkeit dieser Seite wird keine Haftung übernommen.</p>
			<p>Sämtliche Links zu Seiten Dritter wurden bei der Verlinkung überprüft und es konnten keine Rechtsverletzungen festgestellt werden. Da eine dauerhafte Kontrolle dieser Inhalte unzumutbar ist, kann für diese Inhalte keine Haftung übernommen werden. Sollte sich heraus stellen, dass eine der verlinkten Seiten rechtswidrige Inhalte präsentiert, wird der Link sofort entfernt werden.</p>
		</div>
		<?php printFooter(); ?>
	</div>
</body>
</html>