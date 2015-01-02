<?php
	require_once "_printFunctions.php";
	printHttpHeader();
?>

<!DOCTYPE html>
<html>
<head>
	<?php printHtmlHeader(); ?>
	<link rel="profile" href="http://microformats.org/profile/hcalendar" />
</head>
<body>
	<?php printTop(); ?>
	<div id='main'>
		<?php printNav(); ?>
		<div id='content'>
			<?php GetMessageSystem()->printMessagesAndErrors(); ?>
			<h1><?php echo (defined('TITLE') !== false ? TITLE : "TITLE"); ?></h1>
			
			<p>TEXT</p>
			
		</div>
		<?php printFooter(); ?>
	</div>
</body>
</html>