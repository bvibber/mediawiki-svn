<html>
<head>
	<title>Wikipedia Statistics</title>
</head>
<body>
	<h1>Wikipedia Statistics</h1>
	<?php

	require_once 'LocalSettings.php';

	$dbh = new PDO(
		'mysql:host=' . $localSettings['host'] . ';' .
		'dbname=' . $localSettings['dbname'],
		$localSettings['username'],
		$localSettings['password']
	);
	foreach( $dbh->query( 'SELECT * FROM user_groups' ) as $row ){
		echo implode( ' / ', $row ) . '<br />';
	}
	$dbh = null;

	?>
</body>