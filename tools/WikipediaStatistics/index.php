<?php

	if (
		isset( $_POST['setup'] ) &&
		isset( $_POST['host'] ) &&
		isset( $_POST['dbname'] ) &&
		isset( $_POST['username'] ) &&
		isset( $_POST['password'] ) &&
		is_writable( './' )
	) {
		$values = array(
			'host' => stripslashes( $_POST['host'] ),
			'dbname' => stripslashes( $_POST['dbname'] ),
			'username' => stripslashes( $_POST['username'] ),
			'password' => stripslashes( $_POST['password'] ),
		);
		$phpValues = "array(";
		foreach( $values as $key => $value ) {
			$phpValues .= "\n\t'{$key}' => '{$value}',";
		}
		$phpValues .= "\n);";
		file_put_contents(
			'LocalSettings.php',
			"<?php\n\$localSettings = " . $phpValues
		);
		header( 'location: index.php' );
	}

?>
<html>
<head>
	<title>Wikipedia Statistics</title>
	<style type="text/css" media="screen">
		body {
			font-family: sans-serif;
		}
	</style>
</head>
<body>
	<h1>Wikipedia Statistics</h1>
	<fieldset>
	<?php if ( file_exists( 'LocalSettings.php' ) ): ?>
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
	<form action="index.php" method="post">
		<table cellpadding="10" summary="Query Configuration">
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
					<input type="submit" name="update" value="Update" />
				</td>
			</tr>
		</table>
	</form>
	<hr noshade="noshade" size="1" />
	<pre><?php

	if ( isset( $_POST['update'] ) ) {
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
	<?php else: ?>
	<h3>Setup</h3>
	<form action="index.php" method="post">
		<table cellpadding="10" summary="Query Configuration">
			<tr>
				<td>Host</td>
				<td>
					<input type="text" name="host"
						value="<?= $parameters['host'] ?>" />
				</td>
			</tr>
			<tr>
				<td>Database Name</td>
				<td>
					<input type="text" name="dbname"
						value="<?= $parameters['dbname'] ?>" />
				</td>
			</tr>
			<tr>
				<td>Username</td>
				<td>
					<input type="text" name="username"
						value="<?= $parameters['username'] ?>" />
				</td>
			</tr>
			<tr>
				<td>Password</td>
				<td>
					<input type="text" name="password"
						value="<?= $parameters['password'] ?>" />
				</td>
			</tr>
			<tr>
				<td align="right" colspan="2">
					<input type="submit" name="setup" value="Setup" />
				</td>
			</tr>
		</table>
	</form>
	<hr noshade="noshade" size="1" />
	<pre><?php
		if ( !is_writable( './' ) ) {
			?>
			<span style="color:red;">
				You must make this directory writable to configure.
			</span>
			<?php
		}
	?>
	<?php endif; ?>
	</fieldset>
</body>