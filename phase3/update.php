<?

# Update already-installed software
#

if( !function_exists( "version_compare" ) ) {
	# version_compare was introduced in 4.1.0
	die( "Your PHP version is much too old! 4.3.2 or higher is recommended. ABORTING.\n" );
}
if( version_compare( phpversion(), "4.3.2" ) < 0 ) {
	echo "WARNING: PHP 4.3.2 or higher is recommended. Older versions may work but are not actively supported.\n\n";
}

$wgCommandLineMode = true;

if ( ! ( is_readable( "./LocalSettings.php" )
  && is_readable( "./AdminSettings.php" ) ) ) {
	print "A copy of your installation's LocalSettings.php\n" .
	  "and AdminSettings.php must exist in this source directory.\n";
	exit();
}

$IP = "./includes";
include_once( "./LocalSettings.php" );
include_once( "./AdminSettings.php" );

if ( $wgUseTeX && ( ! is_executable( "./math/texvc" ) ) ) {
	print "To use math functions, you must first compile texvc by\n" .
	  "running \"make\" in the math directory.\n";
	exit();
}

umask( 000 );
set_time_limit( 0 );

include_once( "Version.php" );
include_once( "{$IP}/Setup.php" );
$wgTitle = Title::newFromText( "Update script" );

$wgAlterSpecs = array();
do_revision_updates();
alter_ipblocks();

#
# Run ALTER TABLE queries.
#
	$rconn = mysql_connect( $wgDBserver, $wgDBadminuser, $wgDBadminpassword );
	mysql_select_db( $wgDBname );

	print "\n";
	foreach ( $wgAlterSpecs as $table => $specs ) {
		$sql = "ALTER TABLE $table $specs";
		print "$sql;\n";
		$res = mysql_query( $sql, $rconn );
		if ( $res === false ) {
			print "MySQL error: " . mysql_error( $rconn ) . "\n";
		}
	}
	
	do_interwiki_update();
	do_index_update();

	mysql_close( $rconn );

#
# Copy files into installation directories
#
print "Copying files...\n";

copyfile( ".", "wiki.phtml", $IP );
copyfile( ".", "redirect.phtml", $IP );
copyfile( ".", "texvc.phtml", $IP );

copydirectory( "./includes", $IP );
copydirectory( "./stylesheets", $wgStyleSheetDirectory );

copyfile( "./images", "wiki.png", $wgUploadDirectory );
copyfile( "./languages", "Language.php", $IP );
copyfile( "./languages", "Language" . ucfirst( $wgLanguageCode ) . ".php", $IP );

$fp = fopen( $wgDebugLogFile, "w" );
if ( false === $fp ) {
	print "Could not create log file \"{$wgDebugLogFile}\".\n";
	exit();
}
$d = date( "Y-m-d H:i:s" );
fwrite( $fp, "Wiki debug log file created {$d}\n\n" );
fclose( $fp );

if ( $wgUseTeX ) {
	copyfile( "./math", "texvc", "{$IP}/math", 0775 );
	copyfile( "./math", "texvc_test", "{$IP}/math", 0775 );
	copyfile( "./math", "texvc_tex", "{$IP}/math", 0775 );
}

copyfile( ".", "Version.php", $IP );

print "Done.\n";
exit();

#
#
#

function copyfile( $sdir, $name, $ddir, $perms = 0664 ) {
	global $wgInstallOwner, $wgInstallGroup;

	$d = "{$ddir}/{$name}";
	if ( copy( "{$sdir}/{$name}", $d ) ) {
		if ( isset( $wgInstallOwner ) ) { chown( $d, $wgInstallOwner ); }
		if ( isset( $wgInstallGroup ) ) { chgrp( $d, $wgInstallGroup ); }
		chmod( $d, $perms );
		# print "Copied \"{$name}\" to \"{$ddir}\".\n";
	} else {
		print "Failed to copy file \"{$name}\" to \"{$ddir}\".\n";
		exit();
	}
}

function copydirectory( $source, $dest ) {
	$handle = opendir( $source );
	while ( false !== ( $f = readdir( $handle ) ) ) {
		if ( "." == $f{0} ) continue;
		if ( "CVS" == strtoupper( $f ) ) continue;
		copyfile( $source, $f, $dest );
	}
}

function readconsole() {
	$fp = fopen( "php://stdin", "r" );
	$resp = trim( fgets( $fp, 1024 ) );
	fclose( $fp );
	return $resp;
}

function do_revision_updates() {
	global $wgSoftwareRevision;

	if ( $wgSoftwareRevision < 1001 ) { update_passwords(); }
}

function update_passwords() {
	$fname = "Update scripte: update_passwords()";
	print "\nIt appears that you need to update the user passwords in your\n" .
	  "database. If you have already done this (if you've run this update\n" .
	  "script once before, for example), doing so again will make all your\n" .
	  "user accounts inaccessible, so be sure you only do this once.\n" .
	  "Update user passwords? (yes/no)";

	$resp = readconsole();
    if ( ! ( "Y" == $resp{0} || "y" == $resp{0} ) ) { return; }

	$sql = "SELECT user_id,user_password FROM user";
	$source = wfQuery( $sql, fname );

	while ( $row = mysql_fetch_object( $source ) ) {
		$id = $row->user_id;
		$oldpass = $row->user_password;
		$newpass = md5( "{$id}-{$oldpass}" );

		$sql = "UPDATE user SET user_password='{$newpass}' " .
		  "WHERE user_id={$id}";
		wfQuery( $sql, $fname );
	}
}

function alter_ipblocks() {
	global $wgAlterSpecs;
	
	if ( field_exists( "ipblocks", "ipb_id" ) ) {
		return;
	}
	
	if ( array_key_exists( "ipblocks", $wgAlterSpecs ) ) {
		$wgAlterSpecs["ipblocks"] .= ",";
	}

	$wgAlterSpecs["ipblocks"] .=
		"ADD ipb_auto tinyint(1) NOT NULL default '0', ".
		"ADD ipb_id int(8) NOT NULL auto_increment,".
		"ADD PRIMARY KEY (ipb_id)";
}

function do_interwiki_update() {
	# Check that interwiki table exists; if it doesn't source it
	if( database_exists( "interwiki" ) ) {
		echo "...already have interwiki table\n";
		return true;
	}
	echo "Creating interwiki table: ";
	source_sql( "maintenance/archives/patch-interwiki.sql" );
	echo "ok\n";
	echo "Adding default interwiki definitions: ";
	source_sql( "maintenance/interwiki.sql" );
	echo "ok\n";
}

function do_index_update() {
	# Check that proper indexes are in place
	$meta = field_info( "recentchanges", "rc_timestamp" );
	if( $meta->multiple_key == 0 ) {
		echo "Updating indexes to 20031107: ";
		source_sql( "maintenance/archives/patch-indexes.sql" );
		echo "ok\n";
		return true;
	}
	echo "...indexes seem up to 20031107 standards\n";
	return false;
}


function field_exists( $table, $field ) {
	$fname = "Update script: field_exists";
	$res = wfQuery( "DESCRIBE $table", $fname );
	$found = false;
	
	while ( $row = wfFetchObject( $res ) ) {
		if ( $row->Field == $field ) {
			$found = true;
			break;
		}
	}
	return $found;
}


function database_exists( $db ) {
	global $wgDBname;
	$res = mysql_list_tables( $wgDBname );
	if( !$res ) {
		echo "** " . mysql_error() . "\n";
		return false;
	}
	for( $i = mysql_num_rows( $res ) - 1; $i--; $i > 0 ) {
		if( mysql_tablename( $res, $i ) == $db ) return true;
	}
	return false;
}

function field_info( $table, $field ) {
	$res = mysql_query( "SELECT * FROM $table LIMIT 1" );
	$n = mysql_num_fields( $res );
	for( $i = 0; $i < $n; $i++ ) {
		$meta = mysql_fetch_field( $res, $i );
		if( $field == $meta->name ) {
			return $meta;
		}
	}
	return false;
}

function source_sql( $filename ) {
	$strings = file( $filename );
	if($strings === false ) {
		echo "\n** Could not open $filename\n";
		return false;
	}
	$n = 0;
	$last = 1;
	$incoming = "";
	foreach( $strings as $line ) {
		$n++;
		if( preg_match( '/^--/', $line ) ) {
			continue;
		} elseif( preg_match( '/^(.*);$/', $line ) ) { #FIXME
			$incoming .= $line;
			$res = mysql_query( $incoming );
			if( $res === false ) {
				echo "\n** error around line $last-$n in $filename: " . mysql_error() . "\n";
				return false;
			}
			$incoming = "";
			$last = $n+1;
		} else {
			$incoming .= $line;
		}
	}
	return true;
}

?>
