<?php
/**
 * @package MediaWiki
 * @subpackage SpecialPage
 */

/**
 *
 */

if ( defined( 'MEDIAWIKI' ) ) {

$wgExtensionFunctions[] = 'wfSetupMakesysop';

define( 'GR_SYSOP', 3 );
define( 'GR_BUREAUCRAT', 4 );

function wfSetupMakesysop() {
	require_once( 'SpecialPage.php' );
	global $wgAvailableRights;

	SpecialPage::addPage( new SpecialPage( 'Makesysop', 'makesysop', /*listed*/ true, /*function*/ false, /*file*/ false ) );
	$wgAvailableRights[] = 'makesysop';
}

/**
 * Constructor
 */
function wfSpecialMakesysop() {
	require_once( "LinksUpdate.php" );
	
	global $wgUser, $wgOut, $wgRequest;

	if ( $wgUser->isAnon() or $wgUser->isBlocked() ) {
		$wgOut->errorpage( "movenologin", "movenologintext" );
		return;
	}
	if (! $wgUser->isAllowed('userrights') ) {
		$wgOut->errorpage( "bureaucrattitle", "bureaucrattext" );
		return;
	}
	
	if ( wfReadOnly() ) {
		$wgOut->readOnlyPage();
		return;
	}

	$f = new MakesysopForm( $wgRequest );

	if ( $f->mSubmit ) { 
		$f->doSubmit(); 
	} else { 
		$f->showForm( '' ); 
	}
}

/**
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */
class MakesysopForm {
	var $mTarget, $mAction, $mRights, $mUser, $mSubmit;

	function MakesysopForm( &$request ) {
		$this->mAction = $request->getText( 'action' );
		$this->mRights = $request->getVal( 'wpRights' );
		$this->mUser = $request->getText( 'wpMakesysopUser' );
		$this->mSubmit = $request->getBool( 'wpMakesysopSubmit' ) && $request->wasPosted();
		$this->mBuro = $request->getBool( 'wpSetBureaucrat' );
	}

	function showForm( $err = '') {
		global $wgOut, $wgUser, $wgLang;

		$wgOut->setPagetitle( wfMsg( "makesysoptitle" ) );
		$wgOut->addWikiText( wfMsg( "makesysoptext" ) );

		$titleObj = Title::makeTitle( NS_SPECIAL, "Makesysop" );
		$action = $titleObj->escapeLocalURL( "action=submit" );

		if ( "" != $err ) {
			$wgOut->setSubtitle( wfMsg( "formerror" ) );
			$wgOut->addHTML( "<p class='error'>{$err}</p>\n" );
		}
		$namedesc = wfMsg( "makesysopname" );
		if ( !is_null( $this->mUser ) ) {
			$encUser = htmlspecialchars( $this->mUser );
		} else {
			$encUser = "";
		}

		$wgOut->addHTML( "
			<form id=\"makesysop\" method=\"post\" action=\"{$action}\">
			<table border='0'>
			<tr>
				<td align='right'>$namedesc</td>
				<td align='left'>
					<input type='text' size='40' name=\"wpMakesysopUser\" value=\"$encUser\" />
				</td>
			</tr>" 
		);
		
		$makeburo = wfMsg( "setbureaucratflag" );
		$wgOut->addHTML(
			"<tr>
				<td>&nbsp;</td><td align=left>
					<input type=checkbox name=\"wpSetBureaucrat\" value=1>$makeburo
				</td>
			</tr>"
		);


		$mss = wfMsg( "set_user_rights" );
		$wgOut->addHTML(
			"<tr>
				<td>&nbsp;</td><td align='left'>
					<input type='submit' name=\"wpMakesysopSubmit\" value=\"{$mss}\" />
				</td></tr></table>
			</form>\n" 
		);

	}

	function doSubmit() {
		global $wgOut, $wgUser, $wgLang;
		global $wgMemc, $wgDBname;

		$fname = 'MakesysopForm::doSubmit';
		
		$dbw =& wfGetDB( DB_MASTER );
		$user_groups = $dbw->tableName( 'user_groups' );
		$usertable   = $dbw->tableName( 'user' );

		$username = wfStrencode( $this->mUser );
		if ( $username{0} == "#" ) {
			$id = intval( substr( $username, 1 ) );
		} else {
			$u = User::newFromName( $username );
			$id = $u->idForName();
		}
		if ( !$id ) {
			$this->showFail();
			return;
		}

		$sql = "SELECT ug_user,ug_group FROM $user_groups WHERE ug_user=$id FOR UPDATE";
		$res = $dbw->query( $sql, $fname );

		$row = false;
		$groups = array();
		while ( $row = $dbw->fetchObject( $res ) ) {
			$groups[$row->ug_group] = true;
		}
		$dbw->freeResult( $res );

		$rightsNotation = array();
		$wasSysop = !empty( $groups[GR_SYSOP] );
		$wasBureaucrat = !empty( $groups[GR_BUREAUCRAT] );

		if ( $this->mBuro ) {
			if ( $wasBureaucrat ) {
				$this->showFail( 'already_bureaucrat' );
				return;
			} else {
				$dbw->insert( 'user_groups', array( 'ug_user' => $id, 'ug_group' => GR_BUREAUCRAT ), $fname );
				$rightsNotation[] = "+bureaucrat";
			}
		} elseif ( $wasSysop ) {
			$this->showFail( 'already_sysop' );
			return;
		}
		if ( !$wasSysop ) {
			$dbw->insert( 'user_groups', array( 'ug_user' => $id, 'ug_group' => GR_SYSOP ), $fname );
			$rightsNotation[] = "+sysop";
		}
		
		$wgMemc->delete( "$wgDBname:user:id:$id" );
			
		$log = new LogPage( 'rights' );
		$log->addEntry( 'rights', Title::makeTitle( NS_USER, $this->mUser ),
			implode( " ", $rightsNotation ) );
			
		$this->showSuccess();
	}

	function showSuccess() {
		global $wgOut, $wgUser;

		$wgOut->setPagetitle( wfMsg( "makesysoptitle" ) );
		$text = wfMsg( "makesysopok", $this->mUser );
		$text .= "\n\n";
		$wgOut->addWikiText( $text );
		$this->showForm();

	}

	function showFail( $msg = 'set_rights_fail' ) {
		global $wgOut, $wgUser;

		$wgOut->setPagetitle( wfMsg( "makesysoptitle" ) );
		$this->showForm( wfMsg( $msg, $this->mUser ) );
	}
}

} // End of invocation guard
?>
