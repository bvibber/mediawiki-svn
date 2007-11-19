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
 
                if (strlen($wgRequest->getText('username').$wgRequest->getText('newpass').$wgRequest->getText('confirmpass'))>0) {
                  //POST data found
                  if (strlen($username_text)>0) {
                    $objUser = User::newFromName( $username->getText() );
                    $userID = $objUser->idForName();
 
                    if ( !is_object( $objUser ) || $userID == 0 ) {
                      $validUser = false;
                      $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsgForContent('passwordreset-invalidusername') . "</span><br>\n" );
                    } else {
                      $validUser = true;
                    }
                  } else {
                    $validUser = false;
                    $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsgForContent('passwordreset-emptyusername') . "</span><br>\n" );
                  }
 
                  $newpass = $wgRequest->getText( 'newpass' );
                  $confirmpass = $wgRequest->getText( 'confirmpass' );
 
                  if ($newpass==$confirmpass && strlen($newpass.$oldpass)>0) {
                    //Passwords match
                    $passMatch = true;
                  } else {
                    //Passwords DO NOT match
                    $passMatch = false;
                    $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsgForContent('passwordreset-nopassmatch') . "</span><br>\n" );
                  }
 
                  if (!$wgUser->matchEditToken( $wgRequest->getVal( 'token' ) ) ) {
                    $validUser = false;
                    $passMatch = false;
                    $wgOut->addHTML( "<span style=\"color: red;\">" . wfMsgForContent('passwordreset-badtoken') . "</span><br>\n" );
 
                  }
                }
 
                $action = $wgTitle->escapeLocalUrl();
                $token = $wgUser->editToken();
 
                $wgOut->addHTML( "
<form id='passwordresetform' method='post' action=\"$action\">
<table>
        <tr>
                <td align='right'>" . wfMsgForContent('passwordreset-username') . "</td>
                <td align='left'><input tabindex='1' type='text' size='20' name='username' id='username' value=\"$username_text\" onFocus=\"document.getElementById('username').select;\" /></td>
        </tr>
        <tr>
                <td align='right'>" . wfMsgForContent('passwordreset-newpass') . "</td>
                <td align='left'><input tabindex='2' type='password' size='20' name='newpass' id='newpass' value=\"$newpass\" onFocus=\"document.getElementById('newpass').select;\" /></td>
        </tr>
        <tr>
                <td align='right'>" . wfMsgForContent('passwordreset-confirmpass') . "</td>
                <td align='left'><input tabindex='3' type='password' size='20' name='confirmpass' id='confirmpass' value=\"$confirmpass\" onFocus=\"document.getElementById('confirmpass').select;\" /></td>
        </tr>
        <tr>
                <td>&nbsp;</td>
                <td align='right'><input type='submit' name='submit' value=\"" . wfMsgForContent('passwordreset-submit') . "\" /></td>
        </tr>
</table>
<input type='hidden' name='token' value='$token' />
</form>");
 
                if ($validUser && $passMatch) {
                  $wgOut->addWikiText ( "<hr />\n" );
                  $wgOut->addWikiText ( $this->resetPassword( $userID, $newpass ) );
                } else {
                  //Invalid user or passwords don't match - do nothing
                }
        }
 
        private function resetPassword( $userID, $newpass ) {
                $dbw =& wfGetDB( DB_MASTER );
				
				$dbw->update( 'user',
					array(
						'user_password' => wfEncryptPassword( $userID, $newpass )
					),
					array(
						'user_id' => $userID
					)
				);
				return wfMsgForContent('passwordreset-success', $userID);
        }
 
        function loadMessages() {
                static $messagesLoaded = false;
                global $wgMessageCache;
                if ( $messagesLoaded ) return true;
                $messagesLoaded = true;
 
                require( dirname( __FILE__ ) . '/PasswordReset.i18n.php' );
                foreach ( $allMessages as $lang => $langMessages ) {
                        $wgMessageCache->addMessages( $langMessages, $lang );
                }
 
				return true;
        }
}
