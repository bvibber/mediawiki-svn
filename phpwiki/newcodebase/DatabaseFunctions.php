<?
function wfGetDB()
{
	global $wgDBserver, $wgDBuser, $wgDBpassword;
	global $wgDBname, $wgDBconnection;

	$noconn = str_replace( "$1", $wgDBserver, wfMsg( "noconnect" ) );
	$nodb = str_replace( "$1", $wgDBname, wfMsg( "nodb" ) );

	if ( ! $wgDBconnection ) {
		$wgDBconnection = mysql_pconnect( $wgDBserver, $wgDBuser,
		  $wgDBpassword ) or die( $noconn );
		mysql_select_db( $wgDBname, $wgDBconnection ) or die( $nodb );
	}
	# mysql_ping( $wgDBconnection );
	return $wgDBconnection;
}

function wfSetSQL( $table, $var, $value, $cond )
{
	$conn = wfGetDB();
	$sql = "UPDATE $table SET $var = '" .
	  wfStrencode( $value ) . "' WHERE ($cond)";

	wfDebug( "DB: 1: $sql\n" );
	$result = mysql_query( $sql, $conn );
}

function wfGetSQL( $table, $var, $cond )
{
	$conn = wfGetDB();
	$sql = "SELECT $var FROM $table WHERE ($cond)";

	wfDebug( "DB: 2: $sql\n" );
	$result = mysql_query( $sql, $conn );
	$ret = "";
	if ( $result && ( mysql_num_rows( $result ) > 0 ) ) {
		if ( $s = mysql_fetch_object( $result ) ) {
			$ret = $s->$var;
		}
		mysql_free_result( $result );
	}
	return $ret;
}

function wfStrencode( $s )
{
	$s = str_replace( "\\", "\\\\", $s );
	$s = str_replace( "\r", "\\r", $s );
	$s = str_replace( "\n", "\\n", $s );
	$s = str_replace( "\"", "\\\"", $s );
	$s = str_replace( "'", "\\'", $s );
	$s = str_replace( "\0", "\\0", $s );
	return $s;
}
?>
