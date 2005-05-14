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
define( 'GR_STEWARD', 5); 

function wfSetupMakesysop() {
	require_once( 'SpecialPage.php' );
	global $wgAvailableRights, $wgStaticGroups;

	// Set groups to the appropriate sysop/bureaucrat structure
	// This record was retrieved from Special:Groups?showrecord=1
	
	$wgStaticGroups = 
		'a:5:{i:1;O:5:"group":5:{s:4:"name";s:16:":group-anon-name";s:2:"id";s:1:"1";' .
		's:11:"description";s:16:":group-anon-desc";s:10:"dataLoaded";b:1;s:6:"rights' .
		'";s:30:"read,edit,delete,createaccount";}i:2;O:5:"group":5:{s:4:"name";s:20:' .
		'":group-loggedin-name";s:2:"id";s:1:"2";s:11:"description";s:20:":group-logg' .
		'edin-desc";s:10:"dataLoaded";b:1;s:6:"rights";s:44:"read,edit,move,createacc' .
		'ount,upload,validate";}i:3;O:5:"group":5:{s:4:"name";s:17:":group-admin-name' .
		'";s:2:"id";s:1:"3";s:11:"description";s:17:":group-admin-desc";s:10:"dataLoa' .
		'ded";b:1;s:6:"rights";s:118:"read,edit,move,delete,undelete,protect,block,cr' .
		'eateaccount,upload,asksql,rollback,patrol,editinterface,validate,import";}i:' .
		'4;O:5:"group":5:{s:4:"name";s:22:":group-bureaucrat-name";s:2:"id";s:1:"4";s' .
		':11:"description";s:22:":group-bureaucrat-desc";s:10:"dataLoaded";b:1;s:6:"r' .
		'ights";s:128:"read,edit,move,delete,undelete,protect,block,createaccount,upl' .
		'oad,asksql,rollback,patrol,editinterface,validate,import,makesysop";}i:5;O:5' .
		':"group":5:{s:4:"name";s:19:":group-steward-name";s:2:"id";s:1:"5";s:11:"des' .
		'cription";s:19:":group-steward-desc";s:10:"dataLoaded";b:1;s:6:"rights";s:16' .
		'1:"read,edit,move,delete,undelete,protect,block,userrights,grouprights,creat' .
		'eaccount,upload,asksql,rollback,patrol,editinterface,siteadmin,validate,impo' .
		'rt,makesysop";}}';

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
	var $mTarget, $mAction, $mRights, $mUser, $mSubmit, $mSetBureaucrat, $mSetSteward;

	function MakesysopForm( &$request ) {
		global $wgUser;

		$this->mAction = $request->getText( 'action' );
		$this->mRights = $request->getVal( 'wpRights' );
		$this->mUser = $request->getText( 'wpMakesysopUser' );
		$this->mSubmit = $request->getBool( 'wpMakesysopSubmit' ) &&
			$request->wasPosted() &&
			$wgUser->matchEditToken( $request->getVal( 'wpEditToken' ) );		
		$this->mSetBureaucrat = $request->getBool( 'wpSetBureaucrat' );
		$this->mSetSteward = $request->getBool( 'wpSetSteward' );

		$this->mIsSteward = in_array( GR_STEWARD, $wgUser->getGroups() );
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

		if ( $this->mIsSteward ) {
			$setstewardflag = wfMsg( "setstewardflag" );
			$wgOut->addHTML(
				"<tr>
					<td>&nbsp;</td><td align=left>
						<input type=checkbox name=\"wpSetSteward\" value=1>$setstewardflag
					</td>
				</tr>"
			);
		}


		$mss = wfMsg( "set_user_rights" );

		$token = htmlspecialchars( $wgUser->editToken() );
		$wgOut->addHTML(
			"<tr>
				<td>&nbsp;</td><td align='left'>
					<input type='submit' name=\"wpMakesysopSubmit\" value=\"{$mss}\" />
				</td></tr></table>
				<input type='hidden' name='wpEditToken' value=\"{$token}\" />
			</form>\n" 
		);
	}

	function doSubmit() {
		global $wgOut, $wgUser, $wgLang;
		global $wgDBname, $wgMemc, $wgLocalDatabases, $wgSharedDB;

		$fname = 'MakesysopForm::doSubmit';
		
		$dbw =& wfGetDB( DB_MASTER );
		$user_groups = $dbw->tableName( 'user_groups' );
		$usertable   = $dbw->tableName( 'user' );
		$parts = explode( '@', $this->mUser );

		if( count( $parts ) == 2 && $this->mIsSteward && strpos( '.', $user_groups ) === false ){
			$username = $parts[0];
			if ( array_key_exists( $parts[1], $wgLocalDatabases ) ) {
				$dbName = $wgLocalDatabases[$parts[1]];
				$user_groups = "`$dbName`.$user_groups";
				if ( !$wgSharedDB ) {
					$usertable   = "`$dbName`.$usertable";
				}
			} else {
				$this->showFail();
				return;
			}
		} else {
			$username = $this->mUser;
			$dbName = $wgDBname;
		}

		// Clean up username
		$t = Title::newFromText( $username );
		if ( !$t ) {
			$this->showFail();
			return;
		}
		$username = $t->getText();
		
		if ( $username{0} == "#" ) {
			$id = intval( substr( $username, 1 ) );
		} else {
			$id = $dbw->selectField( $usertable, 'user_id', array( 'user_name' => $username ), $fname );
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
		$wasSteward = !empty( $groups[GR_STEWARD] );

		if ( $this->mSetSteward ) {
			if ( $wasSteward ) {
				$this->showFail( 'already_steward' );
				return;
			} else {
				$dbw->insert( $user_groups, array( 'ug_user' => $id, 'ug_group' => GR_STEWARD ), $fname );
			}
		}
		if ( $this->mSetBureaucrat ) {
			if ( $wasBureaucrat ) {
				if ( !$this->mSetSteward ) {
					$this->showFail( 'already_bureaucrat' );
					return;
				}
			} else {
				$dbw->insert( $user_groups, array( 'ug_user' => $id, 'ug_group' => GR_BUREAUCRAT ), $fname );
				$rightsNotation[] = "+bureaucrat";
			}
		} elseif ( $wasSysop ) {
			$this->showFail( 'already_sysop' );
			return;
		}
		if ( !$wasSysop ) {
			$dbw->insert( $user_groups, array( 'ug_user' => $id, 'ug_group' => GR_SYSOP ), $fname );
			$rightsNotation[] = "+sysop";
		}
		
		$wgMemc->delete( "$dbName:user:id:$id" );
			
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
