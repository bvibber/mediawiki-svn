<?

function wfSpecialAsksql()
{
	global $wgUser, $wgOut, $action;

	if ( ! $wgUser->isSysop() ) {
		$wgOut->sysopRequired();
		return;
	}
	$fields = array( "wpSqlQuery" );
	wfCleanFormFields( $fields );
	$f = new SqlQueryForm();

	if ( "submit" == $action ) { $f->doSubmit(); }
	else { $f->showForm( "" ); }
}

class SqlQueryForm {

	function showForm( $err )
	{
		global $wgOut, $wgUser, $wgLang;
		global $wpSqlQuery;

		$wgOut->setPagetitle( wfMsg( "asksql" ) );
		$wgOut->addWikiText( wfMsg( "asksqltext" ) );

		if ( "" != $err ) {
			$wgOut->addHTML( "<p><font color='red' size='+1'>" . htmlspecialchars($err) . "</font>\n" );
		}
		if ( ! $wpSqlQuery ) { $wpSqlQuery = "SELECT ... FROM ... WHERE ..."; }
		$q = wfMsg( "sqlquery" );
		$qb = wfMsg( "querybtn" );
		$action = wfLocalUrlE( $wgLang->specialPage( "Asksql" ),
		  "action=submit" );

		$wgOut->addHTML( "<p>
<form method=post action=\"{$action}\">
<table border=0><tr>
<td align=right>{$q}:</td>
<td align=left>
<input type=text size=80 name='wpSqlQuery' value=\"" . htmlspecialchars($wpSqlQuery) ."\">
</td>
</tr><tr>
<td>&nbsp;</td><td align='left'>
<input type=submit name='wpQueryBtn' value=\"{$qb}\">
</td></tr></table>
</form>\n" );

	}

	function doSubmit()
	{
		global $wgOut, $wgUser, $wgServer, $wgScript;
		global $wpSqlQuery;

		if ( ! $wgUser->isDeveloper() ) {
			if ( 0 != strcmp( "select", strtolower(
			  substr( $wpSqlQuery, 0, 6 ) ) ) ) {
				$this->showForm( wfMsg( "selectonly" ) );
				return;
			}
		}
		$res = wfQuery( $wpSqlQuery, "SpecialAsksql::doSubmit" );

		$n = 0;
		$n = mysql_num_fields( $res );
		if ( $n ) {
			$k = array();
			for ( $x = 0; $x < $n; ++$x ) {
				array_push( $k, mysql_field_name( $res, $x ) );
			}
			$a = array();
			while ( $s = mysql_fetch_object( $res ) ) {
				array_push( $a, $s );
			}
			mysql_free_result( $res );

			$r = "<table border=1 bordercolor=black cellspacing=0 " .
			  "cellpadding=2><tr>\n";
			foreach ( $k as $x ) $r .= "<th>{$x}</th>";
			$r .= "</tr>\n";

			foreach ( $a as $y ) {
				$r .= "<tr>";
				foreach ( $k as $x ) {
					$r .= "<td>" . $y->$x . "</td>\n";
				}
				$r .= "</tr>\n";
			}
			$r .= "</table>\n";
		}
		$this->showForm( wfMsg( "querysuccessful" ) );
		$wgOut->addHTML( "<hr>{$r}\n" );
	}

}

?>
