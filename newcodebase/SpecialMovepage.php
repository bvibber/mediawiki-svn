<?

function wfSpecialMovepage()
{
	global $wgUser, $wgOut, $action, $target;

	if ( ! $wgUser->isSysop() ) {
		$wgOut->sysopRequired();
		return;
	}
	$fields = array( "wpNewTitle", "wpOldTitle" );
	wfCleanFormFields( $fields );

	$f = new MovePageForm();

	if ( "success" == $action ) { $f->showSuccess(); }
	else if ( "submit" == $action ) { $f->doSubmit(); }
	else { $f->showForm( "" ); }
}

class MovePageForm {

	function showForm( $err )
	{
		global $wgOut, $wgUser;
		global $wpNewTitle, $wpOldTitle, $target;

		$wgOut->setPagetitle( wfMsg( "movepage" ) );
		$wgOut->addWikiText( wfMsg( "movepagetext" ) );

		if ( ! $wpOldTitle ) {
			$target = wfCleanQueryVar( $target );
			if ( "" == $target ) {
				$wgOut->errorpage( "notargettitle", "notargettext" );
				return;
			}
			$wpOldTitle = $target;
		}
		$ot = Title::newFromURL( $wpOldTitle );
		$ott = $ot->getPrefixedText();

		$ma = wfMsg( "movearticle" );
		$newt = wfMsg( "newtitle" );
		$mpb = wfMsg( "movepagebtn" );
		$action = wfLocalUrlE( "Special:Movepage", "action=submit" );

		if ( "" != $err ) {
			$wgOut->setSubtitle( wfMsg( "formerror" ) );
			$wgOut->addHTML( "<p><font color='red' size='+1'>{$err}</font>\n" );
		}
		$wgOut->addHTML( "<p>
<form method=post action=\"{$action}\">
<table border=0><tr>
<td align=right>{$ma}:</td>
<td align=left><strong>{$ott}</strong></td>
</tr><tr>
<td align=right>{$newt}:</td>
<td align=left>
<input type=text size=40 name='wpNewTitle' value=\"{$wpNewTitle}\">
<input type=hidden name='wpOldTitle' value=\"{$wpOldTitle}\">
</td>
</tr><tr>
<td>&nbsp;</td><td align=left>
<input type=submit name='wpMove' value=\"{$mpb}\">
</td></tr></table>
</form>\n" );

	}

	function doSubmit()
	{
		global $wgOut, $wgUser;
		global $wpNewTitle, $wpOldTitle, $target;
		global $wgDeferredUpdateList;
		$fname = "MovePageForm::doSubmit";

		$ot = Title::newFromText( $wpOldTitle );
		$nt = Title::newFromText( $wpNewTitle );
		$nns = $nt->getNamespace();
		$ndt = wfStrencode( $nt->getDBkey() );
		$nft = wfStrencode( $nt->getPrefixedDBkey() );
		$ons = $ot->getNamespace();
		$odt = wfStrencode( $ot->getDBkey() );
		$oft = wfStrencode( $ot->getPrefixedDBkey() );
		$oldid = $ot->getArticleID();

		if ( 0 != $nt->getArticleID() ) {
			$this->showForm( wfMsg( "articleexists" ) );
			return;
		}
		if ( ( ! Namespace::isMovable( $ons ) ) || ( "" == $odt ) ||
		  ( ! Namespace::isMovable( $nns ) ) || ( "" == $ndt ) ) {
			$this->showForm( wfMsg( "badarticleerror" ) );
			return;
		}
		$sql = "UPDATE cur SET cur_timestamp=cur_timestamp," .
		  "cur_namespace={$nns},cur_title='{$ndt}',cur_ind_title='" .
		  wfStrencode( $nt->getIndexTitle() ) . "' WHERE cur_id={$oldid}";
		wfQuery( $sql, $fname );

		$mt = wfMsg( "movedto" );
		$sql = "INSERT INTO cur (cur_namespace,cur_title,cur_text," .
		  "cur_comment,cur_user,cur_timestamp,cur_minor_edit,cur_counter," .
		  "cur_restrictions,cur_ind_title,cur_user_text,cur_is_redirect," .
		  "cur_is_new) VALUES ({$ons},'{$odt}','#REDIRECT [[{$ndt}]]\n','" .
		  "{$mt} \"{$ndt}\"','" .
		  $wgUser->getID() . "','" . date( "YmdHis" ) . "',0,0,'','" .
		  wfStrencode( $ot->getIndexTitle() ) . "','" .
		  wfStrencode( $wgUser->getName() ) . "',1,1)";
		wfQuery( $sql, $fname );
		$newid = wfInsertId();

		$sql = "UPDATE old SET old_timestamp=old_timestamp," .
		  "old_namespace={$nns},old_title='{$ndt}' WHERE old_title='{$odt}'";
		wfQuery( $sql, $fname );

		$sql = "UPDATE links SET l_from='{$nft}' WHERE l_from='{$oft}'";
		wfQuery( $sql, $fname );

		$sql = "UPDATE links SET l_to={$newid} WHERE l_to={$oldid}";
		wfQuery( $sql, $fname );

		$sql = "INSERT INTO links (l_from,l_to) VALUES ('{$oft}',{$oldid})";
		wfQuery( $sql, $fname );

		$sql = "UPDATE imagelinks SET il_from='{$nft}' WHERE il_from='{$oft}'";
		wfQuery( $sql, $fname );

		$nu = urlencode( $wpNewTitle );
		$ou = urlencode( $wpOldTitle );
		$success = wfLocalUrl( "Special:Movepage",
		  "action=success&oldtitle={$ou}&newtitle={$nu}" );
		$wgOut->redirect( $success );
	}

	function showSuccess()
	{
		global $wgOut, $wgUser;
		global $newtitle, $oldtitle;

		$wgOut->setPagetitle( wfMsg( "movepage" ) );
		$wgOut->setSubtitle( wfMsg( "pagemovedsub" ) );
		$text = str_replace( "$1", $oldtitle, wfMsg( "pagemovedtext" ) );
		$text = str_replace( "$2", $newtitle, $text );
		$wgOut->addWikiText( $text );
	}
}

?>
