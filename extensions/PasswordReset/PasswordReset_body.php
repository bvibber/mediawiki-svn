<?php
/** \file
* \brief Contains code for the PasswordReset Class (extends SpecialPage).
*/

///Special page class for the Password Reset extension
/**
 * Special page that allows sysops to reset local MW user's
 * passwords
 *
 * @addtogroup Extensions
 * @author Tim Laqua <t.laqua@gmail.com>
 */
class PasswordReset extends SpecialPage
{
        function PasswordReset() {
                SpecialPage::SpecialPage("PasswordReset","passwordreset");
                self::loadMessages();
        }
 
        function execute( $par ) {
                global $wgRequest, $wgOut, $wgUser, $wgTitle;
				
                $this->setHeaders();
 
                if ( !$wgUser->isAllowed( 'passwordreset' ) ) {
                        $wgOut->permissionRequired( 'passwordreset' );
                        return;
                }
 
                $username = Title::newFromText( $wgRequest->getText( 'username' ) );
                $username_text = is_object( $username ) ? $username->getText() : '';
				
				$disableuser = $wgRequest->getCheck('disableuser');
				
				if ($disableuser) {
					$disableuserchecked = ' CHECKED';
					$passwordfielddisabled = ' disabled="true"';
				} else {
					$disableuserchecked = '';
					$passwordfielddisabled = '';
				}
				
                if (strlen($wgRequest->getText('username').$wgRequest->getText('newpass').$wgRequest->getText('confirmpass'))>0) {
                  //POST data found
                  if (strlen($username_text)>0) {
                    $objUser = User::newFromName( $username->getText() );
                    $userID = $objUser->idForName();
 
                    if ( !is_object( $objUser ) || $userID == 0 ) {
                      $validUser = false;
                      $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsg('passwordreset-invalidusername') . "</span><br>\n" );
                    } else {
                      $validUser = true;
                    }
                  } else {
                    $validUser = false;
                    $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsg('passwordreset-emptyusername') . "</span><br>\n" );
                  }
 
                  $newpass = $wgRequest->getText( 'newpass' );
                  $confirmpass = $wgRequest->getText( 'confirmpass' );
 
                  if (($newpass==$confirmpass && strlen($newpass)>0) || $disableuser) {
                    //Passwords match
                    $passMatch = true;
                  } else {
                    //Passwords DO NOT match
                    $passMatch = false;
                    $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsg('passwordreset-nopassmatch') . "</span><br>\n" );
                  }
 
                  if (!$wgUser->matchEditToken( $wgRequest->getVal( 'token' ) ) ) {
                    $validUser = false;
                    $passMatch = false;
                    $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsg('passwordreset-badtoken') . "</span><br>\n" );
 
                  }
                } else {
					$validUser = false;
					$confirmpass = '';
					$newpass = '';
				}
 
                $action = $wgTitle->escapeLocalUrl();
                $token = $wgUser->editToken();
 
                $wgOut->addHTML( "
<script language=\"Javascript\">
	function disableUserClicked() {
		if (document.getElementById('disableuser').checked) {
			document.getElementById('newpass').disabled = false;
			document.getElementById('confirmpass').disabled = false;
		} else {
			document.getElementById('newpass').disabled = true;
			document.getElementById('confirmpass').disabled = true;
		}
		return true;
	}
</script>
<form id='passwordresetform' method='post' action=\"$action\">
<table>
        <tr>
                <td align='right'>" . wfMsg('passwordreset-username') . "</td>
                <td align='left'><input tabindex='1' type='text' size='20' name='username' id='username' value=\"$username_text\" onFocus=\"document.getElementById('username').select;\" /></td>
        </tr>
        <tr>
                <td align='right'>" . wfMsg('passwordreset-newpass') . "</td>
                <td align='left'><input tabindex='2' type='password' size='20' name='newpass' id='newpass' value=\"$newpass\" onFocus=\"document.getElementById('newpass').select;\"{$passwordfielddisabled} /></td>
        </tr>
        <tr>
                <td align='right'>" . wfMsg('passwordreset-confirmpass') . "</td>
                <td align='left'><input tabindex='3' type='password' size='20' name='confirmpass' id='confirmpass' value=\"$confirmpass\" onFocus=\"document.getElementById('confirmpass').select;\"{$passwordfielddisabled} /></td>
        </tr>
        <tr>
                <td align='right'>" . wfMsg('passwordreset-disableuser') . "</td>
                <td align='left'><input tabindex='4' type='checkbox' name='disableuser' id='disableuser' onmouseup='return disableUserClicked();'{$disableuserchecked} /> " . wfMsg('passwordreset-disableuserexplain') . "</td>
        </tr>
        <tr>
                <td>&nbsp;</td>
                <td align='right'><input type='submit' name='submit' value=\"" . wfMsg('passwordreset-submit') . "\" /></td>
        </tr>
</table>
<input type='hidden' name='token' value='$token' />
</form>");
 
                if ($validUser && $passMatch) {
                  $wgOut->addWikiText ( "<hr />\n" );
                  $wgOut->addWikiText ( $this->resetPassword( $userID, $newpass, $disableuser ) );
                } else {
                  //Invalid user or passwords don't match - do nothing
                }
        }
 
        private function resetPassword( $userID, $newpass, $disableuser ) {
                $dbw =& wfGetDB( DB_MASTER );
				
				if ($disableuser) {
					$passHash = 'DISABLED';
					$message = wfMsg('passwordreset-disablesuccess', $userID);
				} else {
					$passHash = wfEncryptPassword( $userID, $newpass );
					$message = wfMsg('passwordreset-success', $userID);
				}
				
				$dbw->update( 'user',
					array(
						'user_password' => $passHash
					),
					array(
						'user_id' => $userID
					)
				);
				return $message;
        }
 
        function loadMessages() {
                static $messagesLoaded = false;
                global $wgMessageCache;
                if ( $messagesLoaded ) return true;
                $messagesLoaded = true;
                 wfLoadExtensionMessages('PasswordReset');
		return true;
        }
		
		function GetBlockedStatus(&$user) {
			global $wgTitle;
			
			if ($wgTitle->isSpecial('Userlogin')) {
				global $wgRequest;
				if ($wgRequest->wasPosted()) {
					$name = $wgRequest->getText('wpName');
					if ($name <> '') {
						
						$dbr = wfGetDB( DB_SLAVE );
						$res = $dbr->select( 'user',
							array( 'user_password' ),
							array( 'user_name' => $name ),
							__METHOD__ );
							
						while ( $row = $dbr->fetchObject( $res ) ) {
							if ($row->user_password == 'DISABLED') {
								$user->mBlockedby = 1;
								$user->mBlockreason = wfMsg( 'passwordreset-accountdisabled' );
							}
						}
					} 
				} 
			} elseif ( $user->isLoggedIn() ) {
				if ($user->mPassword == 'DISABLED') {
					global $wgOut;
					//mean, I know.
					$user->logout();
					$wgOut->redirect( Title::newMainPage()->escapeFullURL());
				}
			}
			
			return true;
		}
}
