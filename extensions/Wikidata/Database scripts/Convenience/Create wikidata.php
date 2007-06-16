<?php

# this module create a wikidata extension for mediawiki
# it generates the tables in a database (passed as parameter) with a defined prefix (passed as parameter)

$wgUseMasterForMaintenance = true;

$sep = PATH_SEPARATOR;
$IP = realpath( dirname( __FILE__ ) . "../../../../../" );
$currentdir = dirname( __FILE__ );
chdir( $IP );

ini_set( 'include_path', ".$sepIP$sep$IP/includes$sep$IP/languages$sep$IP/maintenance" );

require_once( "StartProfiler.php" );
require_once( "includes/Exception.php");
require_once( "includes/GlobalFunctions.php");
require_once( "includes/Database.php");


function ReadSQLFile( $database, $pattern, $prefix, $filename ){
	$fp = fopen( $filename, 'r' );
	if ( false === $fp ) {
		return "Could not open \"{$filename}\".\n";
	}

	$cmd = "";
	$done = false;

	while ( ! feof( $fp ) ) {
		$line = trim( fgets( $fp, 1024 ) );
		$sl = strlen( $line ) - 1;

		if ( $sl < 0 ) { continue; }
		if ( '-' == $line{0} && '-' == $line{1} ) { continue; }

		if ( ';' == $line{$sl} && ($sl < 2 || ';' != $line{$sl - 1})) {
			$done = true;
			$line = substr( $line, 0, $sl );
		}

		if ( '' != $cmd ) { $cmd .= ' '; }
		$cmd .= "$line\n";

		if ( $done ) {
			$cmd = str_replace(';;', ";", $cmd);
			$cmd = trim( str_replace( $pattern, $prefix, $cmd ) );
			$res = $database->query( $cmd );

			if ( false === $res ) {
				return "Query \"{$cmd}\" failed with error code \".\n";
			}

			$cmd = '';
			$done = false;
		}
	}
	fclose( $fp );
	return true;
}

$dbclass  = 'Database' . ucfirst( $wgDBtype ) ;
$comment  = '';
$database = $wgDBname;
$user     = $wgDBadminuser;
$password = $wgDBadminpassword;
$server   = $wgDBserver;

# Parse arguments
for( $arg = reset( $argv ); $arg !== false; $arg = next( $argv ) ) {
	if ( substr( $arg, 0, 7 ) == '-prefix' ) {
		$prefix = next( $argv );
		$wgWDprefix = $prefix . "_";
	}
	else if ( substr( $arg, 0, 9 ) == '-template' ) {
		$wgWDtemplate = next( $argv );
	}
	else if ( substr( $arg, 0, 8 ) == '-comment' ) {
		$comment = next( $argv );
	}
	else if ( substr( $arg, 0, 7 ) == '-server' ) {
		$server = next( $argv );
	}
	else if ( substr( $arg, 0, 9 ) == '-database' ) {
		$database = next( $argv );
	}
	else if ( substr( $arg, 0, 5 ) == '-user' ) {
		$user = next( $argv );
	}
	else if ( substr( $arg, 0, 9 ) == '-password' ) {
		$password = next( $argv );
	} else {
		$args[] = $arg;
	}
}

if ( !isset( $wgWDtemplate ) ){
	echo( "SQL template should be provided!");
	echo( "usage: create wikidata.php -prefix <prefix> -template <sql template> [-comment '<comment line>' -server <server> -database <database> -user <username> -password <password>]");
	exit();
}

if ( !isset( $wgWDprefix ) ){
	echo( "database prefix should be provided!");
	echo( "usage: create wikidata.php -prefix <prefix> -template <sql template> [-comment '<comment line>' -database <database> -user <username> -password <password>]");
	exit();
}

# Do a pre-emptive check to ensure we've got credentials supplied
# We can't, at this stage, check them, but we can detect their absence,
# which seems to cause most of the problems people whinge about
if( !isset( $user ) || !isset( $password ) ) {
	echo( "No superuser credentials could be found. Please provide the details\n" );
	echo( "of a user with appropriate permissions to update the database. See\n" );
	echo( "AdminSettings.sample for more details.\n\n" );
	exit();
}
# Attempt to connect to the database as a privileged user
# This will vomit up an error if there are permissions problems
$wdDatabase = new $dbclass( $server, $user, $password, $database, 1 );

if( !$wdDatabase->isOpen() ) {
	# Appears to have failed
	echo( "A connection to the database could not be established. Check the\n" );
	echo( "values of \$wgDBadminuser and \$wgDBadminpassword.\n" );
	exit();
}

ReadSQLFile( $wdDatabase, "/*\$wgWDprefix*/", $wgWDprefix, $currentdir . DIRECTORY_SEPARATOR . $wgWDtemplate );
$wdDatabase->query( "DELETE FROM wikidata_sets WHERE set_prefix = '$prefix'" );
$wdDatabase->query( "INSERT INTO wikidata_sets (set_prefix,set_fallback_name,set_dmid) VALUES ('$prefix','$comment',0)" );

$queryResult = $wdDatabase->query( "SELECT user_name FROM user WHERE user_real_name = '$prefix'" );
if ( $row = $wdDatabase->fetchObject( $queryResult ) ){
	echo "user already existed";
}
else{
	$wdDatabase->query( "INSERT INTO user (user_name,user_real_name,user_password,user_newpassword,user_email,user_options) VALUES ('$comment','$prefix','','','','')" );
}
$wdDatabase->close();

?>