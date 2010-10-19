<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
<title>Wikimedia Subversion user list</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<h1>Wikimedia Subversion user list</h1>
<table border="1">
<tr>
<th>Username</th>
<th>Real name</th>
</tr>
<?php

$time = microtime( true );
$lines = file( '/etc/passwd' );
exec( "HOME=/tmp /usr/bin/svn up /var/cache/svnusers 2>&1", $output, $retval );
if ( $retval ) {
	$error = implode( "\n", $output );
} else {
	$error = false;
}

foreach ( $lines as $line ) {
	$parts = explode( ':', trim( $line ) );
	if ( !isset( $parts[2] ) || $parts[2] < 501 ) {
		continue;
	}
	$userInfo = getUserInfo( $parts[0] );
	$encUsername = htmlspecialchars( $parts[0] );
	$userInfo = array_map( 'htmlspecialchars', $userInfo );
	$link = $userInfo['url'] ? "<a href=\"{$userInfo['url']}\">$encUsername</a>" : $encUsername;

	$rows[$parts[0]] = <<<EOT
<tr>
<td>$link</td>
<td>{$userInfo['name']}</td>
</tr>

EOT;
}
ksort( $rows );
echo implode( '', $rows ) . "</table>\n";
echo "<!-- Request time: " . ( microtime( true ) - $time ) . " -->\n";
if ( $retval ) {
	echo "<p>Error: " . htmlspecialchars( $error ) . "</p>\n";
}
echo "</html>\n";

function getUserInfo( $userName ) {
	$userInfo = array(
		'name' => '',
		'url' => ''
	);
	$userFileLines = @file( "/var/cache/svnusers/$userName" );
	if ( $userFileLines ) {
		foreach ( $userFileLines as $userLine ) {
			if ( preg_match( '/^([\w-]+):\s*(.*?)\s*$/', $userLine, $m ) ) {
				$field = strtolower( $m[1] );
				$value = $m[2];
				$userInfo[$field] = $value;
			}
		}
	}
	return $userInfo;
}

?>
