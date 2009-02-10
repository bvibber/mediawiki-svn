<html>
<head>
	<title>Wikipedia Statistics</title>
	<style>
		body {
			font-family: sans-serif;
		}
	</style>
</head>
<body>
	<h1>Wikipedia Statistics</h1>
	<?php

	require_once 'LocalSettings.php';
	require_once 'Database.php';

	// Query types
	$types = array(
		'edits' => 'Number of Edits',
		'editors' => 'Number of Editors',
	);

	// Set default parameters
	$parameters = array(
		'from' => '2009-01-01',
		'to' => '2009-02-01',
		'type' => current( array_keys( $types ) ),
	);
	// Detect custom parameters
	if ( isset( $_POST['from'] ) ) {
		$parameters['from'] = stripslashes( $_POST['from'] );
	}
	if ( isset( $_POST['to'] ) ) {
		$parameters['to'] = stripslashes( $_POST['to'] );
	}
	if ( isset( $_POST['type'] ) ) {
		$parameters['type'] = stripslashes( $_POST['type'] );
	}

	?>
	<fieldset>
		<form action="index.php" method="post">
			<table cellpadding="10">
				<tr>
					<th align="left" colspan="2">Date Range</th>
				</tr>
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
					<td>Type</td>
					<td>
						<?php foreach( $types as $type => $name ): ?>
						<input type="radio" name="type" value="<?= $type ?>"
							id="type_<?= $type ?>"
							<?= $type == $parameters['type'] ? 'checked' : '' ?>
						/>
						<label for="type_<?= $type ?>"><?= $name ?></label>
						<br />
						<?php endforeach; ?>
					</td>
				</tr>
				<tr>
					<td align="right" colspan="2">
						<input type="submit" name="submit" value="Update" />
					</td>
				</tr>
			</table>
		</form>
		<hr noshade="noshade" size="1" />
		<pre><?php

	if ( isset( $_POST['submit'] ) ) {
		$dbr = new Database();
		$dateRange = array(
			sprintf(
				'rev_timestamp > %s',
				$dbr->addQuotes( date( 'Ymd', strtotime( $parameters['from'] ) )
				)
			),
			sprintf(
				'rev_timestamp < %s',
				$dbr->addQuotes( date( 'Ymd', strtotime( $parameters['to'] ) )
				)
			),
		);
		switch ( $parameters['type'] ) {
			case 'edits':
				$result = $dbr->select(
					'revision',
					'COUNT( rev_id )',
					$dateRange,
					array( 'LIMIT' => 1 )
				);
				break;
			case 'editors':
				$result = $dbr->select(
					'revision',
					'COUNT( DISTINCT rev_user_text )',
					$dateRange,
					array( 'LIMIT' => 1 )
				);
				break;
		}
		if ( isset( $result ) ) {
			while( $row = $result->fetchRow() ) {
				echo $row[0];
			}
		}
	}

		?></pre>
	</fieldset>
</body>