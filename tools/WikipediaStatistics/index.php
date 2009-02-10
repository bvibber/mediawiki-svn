<html>
<head>
	<title>Wikipedia Statistics</title>
</head>
<body>
	<h1>Wikipedia Statistics</h1>
	<?php

	require_once 'LocalSettings.php';
	require_once 'Database.php';

	// Set default parameters
	$parameters = array(
		'from' => '2009-01-01',
		'to' => '2009-02-01',
	);
	// Detect custom parameters
	if ( isset( $_POST['from'] ) ) {
		$parameters['from'] = stripslashes( $_POST['from'] );
	}
	if ( isset( $_POST['to'] ) ) {
		$parameters['to'] = stripslashes( $_POST['to'] );
	}

	?>
	<h2>Number of Edits</h2>
	<fieldset>
		<form action="index.php" method="post">
			<table>
				<tr>
					<td>From</td>
					<td>
						<input type="text" name="from"
							value="<?= $parameters['from'] ?>" />
					</td>
				</tr>
				<tr>
					<td>To</td>
					<td>
						<input type="text" name="to"
							value="<?= $parameters['to'] ?>" />
					</td>
				</tr>
				<tr>
					<td align="right" colspan="2">
						<input type="submit" name="submit" value="Update" />
					</td>
				</tr>
			</table>
		</form>
		<pre><?php

	$dbr = new Database();
	$result = $dbr->select(
		'revision',
		'COUNT(*)',
		array(
			sprintf(
				'rev_timestamp > %s',
				$dbr->addQuotes( date( 'Ymd', strtotime( $parameters['from'] ) ) )
			),
			sprintf(
				'rev_timestamp < %s',
				$dbr->addQuotes( date( 'Ymd', strtotime( $parameters['to'] ) ) )
			),
		)
	);
	while( $row = $result->fetchRow() ) {
		var_dump( $row );
	}
		?></pre>
	</fieldset>
</body>