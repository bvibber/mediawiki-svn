<?

function wfSpecialMovepage()
{
	global $wgUser, $wgOut, $action, $target;

	if ( ! $wgUser->isSysop() ) {
		$wgOut->sysopRequired();
		return;
	}
	$target = wfCleanQueryVar( $target );
	if ( "" == $target ) {
		$wgOut->errorpage( "notargetitle", "notargettext" );
		return;
	}
	$fields = array( "wpBlockAddress", "wpBlockReason" );
	wfCleanFormFields( $fields );

	$f = new MovePageForm();

	if ( "success" == $action ) { $f->showSuccess(); }
	else if ( "submit" == $action ) { $f->doSubmit(); }
	else { $f->showForm( "" ); }
}

	$nt = Title::newFromURL( $target );
	$oldid = $nt->getArticleID();

class MovePageForm()
{
	function showForm( $err )
	{
		global $wgOut, $wgUser, $wgServer, $wgScript;
		global $ip, $wpBlockAddress, $wpBlockReason;

		$wgOut->setPagetitle( wfMsg( "blockip" ) );
		$wgOut->addWikiText( wfMsg( "blockiptext" ) );

		if ( ! $wpBlockAddress ) { $wpBlockAddress = $ip; }
		$ipa = wfMsg( "ipaddress" );
		$reason = wfMsg( "ipbreason" );
		$ipbs = wfMsg( "ipbsubmit" );
		$action = "$wgServer$wgScript?title=Special%3ABlockip&amp;" .
		  "action=submit";

		if ( "" != $err ) {
			$wgOut->setSubtitle( wfMsg( "formerror" ) );
			$wgOut->addHTML( "<p><font color='red' size='+1'>{$err}</font>\n" );
		}
		$wgOut->addHTML( "<p>
<form method=post action='{$action}'>
<table border=0><tr>
<td align='right'>{$ipa}:</td>
<td align='left'>
<input tabindex=1 type=text size=20 name='wpBlockAddress' value=\"{$wpBlockAddress}\">
</td></tr><tr>
<td align='right'>{$reason}:</td>
<td align='left'>
<input tabindex=2 type=text size=40 name='wpBlockReason' value=\"{$wpBlockReason}\">
</td></tr><tr>
<td>&nbsp;</td><td align='left'>
<input tabindex=3 type=submit name='wpBlock' value=\"{$ipbs}\">
</td></tr></table>
</form>\n" );

	}

	function doSubmit()
	{
		global $wgOut, $wgUser, $wgServer, $wgScript;
		global $ip, $wpBlockAddress, $wpBlockReason;
		$fname = "IPBlockForm::doSubmit";

		if ( ! preg_match( "/\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}/",
		  $wpBlockAddress ) ) {
			$this->showForm( wfMsg( "badipaddress" ) );
			return;
		}
		if ( "" == $wpBlockReason ) {
			$this->showForm( wfMsg( "noblockreason" ) );
			return;
		}
		$sql = "INSERT INTO ipblocks (ipb_address, ipb_user, ipb_by, " .
		  "ipb_reason, ipb_timestamp ) VALUES ('{$wpBlockAddress}', 0, " .
		  $wgUser->getID() . ", '" . wfStrencode( $wpBlockReason ) . "','" .
		  date( "YmdHis" ) . "')";
		wfQuery( $sql, $fname );

		$success = "$wgServer$wgScript?title=Special%3ABlockip" .
		  "&action=success&ip={$wpBlockAddress}";
		$wgOut->redirect( $success );
	}

	function showSuccess()
	{
		global $wgOut, $wgUser, $wgServer, $wgScript;
		global $ip;

		$wgOut->setPagetitle( wfMsg( "blockip" ) );
		$wgOut->setSubtitle( wfMsg( "blockipsuccesssub" ) );
		$text = str_replace( "$1", $ip, wfMsg( "blockipsuccesstext" ) );
		$wgOut->addWikiText( $text );
	}
}

?>
