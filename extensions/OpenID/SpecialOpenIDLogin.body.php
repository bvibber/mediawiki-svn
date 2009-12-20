<?php
/**
 * SpecialOpenIDLogin.body.php -- Consumer side of OpenID site
 * Copyright 2006,2007 Internet Brands (http://www.internetbrands.com/)
 * Copyright 2007,2008 Evan Prodromou <evan@prodromou.name>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@prodromou.name>
 * @addtogroup Extensions
 */

if ( !defined( 'MEDIAWIKI' ) )
  exit( 1 );

require_once( "Auth/Yadis/XRI.php" );

class SpecialOpenIDLogin extends SpecialOpenID {

	function __construct() {
		parent::__construct( 'OpenIDLogin' );
	}

	/**
	 * Entry point
	 *
	 * @param $par String or null
	 */
	function execute( $par ) {
		global $wgRequest, $wgUser, $wgOut;

		wfLoadExtensionMessages( 'OpenID' );

		$this->setHeaders();

		if ( $wgUser->getID() != 0 ) {
			$this->alreadyLoggedIn();
			return;
		}

		$this->outputHeader();

		switch ( $par ) {
		case 'ChooseName':
			$this->chooseName();
			break;

		 case 'Finish': # Returning from a server
			$this->finish();
			break;

		default: # Main entry point
			if ( $wgRequest->getText( 'returnto' ) ) {
				$this->setReturnTo( $wgRequest->getText( 'returnto' ), $wgRequest->getVal( 'returntoquery' ) );
			}

			$openid_url = $wgRequest->getText( 'openid_url' );

			if ( !is_null( $openid_url ) && strlen( $openid_url ) > 0 ) {
				$this->login( $openid_url, $this->getTitle( 'Finish' ) );
			} else {
				$this->loginForm();
			}
		}
	}

	/**
	 * Displays an error message saying that the user is already logged-in
	 */
	function alreadyLoggedIn() {
		global $wgUser, $wgOut;

		$wgOut->setPageTitle( wfMsg( 'openiderror' ) );
		$wgOut->setRobotPolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->addWikiMsg( 'openidalreadyloggedin', $wgUser->getName() );
		list( $returnto, $returntoquery ) = $this->returnTo();
		$wgOut->returnToMain( false, $returnto, $returntoquery );
	}

	/**
	 * Displays the main login form
	 */
	function loginForm() {
		global $wgOut, $wgScriptPath, $wgOpenIDShowProviderIcons;

		$oidScriptPath = $wgScriptPath . '/extensions/OpenID';

		$wgOut->addLink( array(
			'rel' => 'stylesheet',
			'type' => 'text/css',
			'media' => 'screen',
			'href' => $oidScriptPath . ( $wgOpenIDShowProviderIcons ? '/skin/openid.css' : '/skin/openid-plain.css' )
		) );

		$wgOut->addScript( '<script type="text/javascript" src="' . $oidScriptPath . '/skin/jquery-1.3.2.min.js"></script>' . "\n" );
		$wgOut->addScript( '<script type="text/javascript" src="' . $oidScriptPath . '/skin/openid.js"></script>' . "\n" );

		$formsHTML = '';

		$largeButtonsHTML = '<div id="openid_large_providers">';
		foreach ( OpenIDProvider::getLargeProviders() as $provider ) {
			$largeButtonsHTML .= $provider->getLargeButtonHTML();
			$formsHTML .= $provider->getLoginFormHTML();
		}
		$largeButtonsHTML .= '</div>';

		$smallButtonsHTML = '';
		if ( $wgOpenIDShowProviderIcons ) {
			$smallButtonsHTML .= '<div id="openid_small_providers_icons">';
			foreach ( OpenIDProvider::getSmallProviders() as $provider ) {
				$smallButtonsHTML .= $provider->getSmallButtonHTML();
				$formsHTML .= $provider->getLoginFormHTML();
			}
			$smallButtonsHTML .= '</div>';
		} else {
			$smallButtonsHTML .= '<div id="openid_small_providers_links">';
			$smallButtonsHTML .= '<ul class="openid_small_providers_block">';
			$small = OpenIDProvider::getSmallProviders();

			$i = 0;
			$break = true;
			foreach ( $small as $provider ) {
				if ( $break && $i > count( $small ) / 2 ) {
					$smallButtonsHTML .= '</ul><ul class="openid_small_providers_block">';
					$break = false;
				}

				$smallButtonsHTML .= '<li>' . $provider->getSmallButtonHTML() . '</li>';

				$formsHTML .= $provider->getLoginFormHTML();
				$i++;
			}
			$smallButtonsHTML .= '</ul>';
			$smallButtonsHTML .= '</div>';
		}

		$wgOut->addHTML(
			Xml::openElement( 'form', array( 'id' => 'openid_form', 'action' => $this->getTitle()->getLocalUrl(), 'method' => 'post', 'onsubmit' => 'openid.update()' ) ) .
			Xml::fieldset( wfMsg( 'openidsigninorcreateaccount' ) ) .
			$largeButtonsHTML .
			'<div id="openid_input_area">' .
			$formsHTML .
			'</div>' .
			$smallButtonsHTML .
			Xml::closeElement( 'fieldset' ) . Xml::closeElement( 'form' )
		);
		$wgOut->addWikiMsg( 'openidlogininstructions' );
	}

	/**
	 * Displays a form to let the user choose an account to attach with the
	 * given OpenID
	 *
	 * @param $openid String: OpenID url
	 * @param $sreg Array: options get from OpenID
	 * @param $messagekey String or null: message name to display at the top
	 */
	function chooseNameForm( $openid, $sreg, $messagekey = NULL ) {
		global $wgOut, $wgOpenIDOnly, $wgAllowRealName;

		if ( $messagekey ) {
			$wgOut->addWikiMsg( $messagekey );
		} else if ( array_key_exists( 'nickname', $sreg ) ) {
			$wgOut->addWikiMsg( 'openidnotavailable', $sreg['nickname'] );
		} else {
			$wgOut->addWikiMsg( 'openidnotprovided' );
		}
		$wgOut->addWikiMsg( 'openidchooseinstructions' );

		$wgOut->addHTML(
			Xml::openElement( 'form',
				array( 'action' => $this->getTitle( 'ChooseName' )->getLocalUrl(), 'method' => 'POST' ) ) . "\n"
		);
		$def = false;

		if ( !$wgOpenIDOnly ) {
			# Let them attach it to an existing user

			# Grab the UserName in the cookie if it exists

			global $wgCookiePrefix;
			$name = '';
			if ( isset( $_COOKIE["{$wgCookiePrefix}UserName"] ) ) {
				$name = trim( $_COOKIE["{$wgCookiePrefix}UserName"] );
			}

			# show OpenID Attributes
			$oidAttributesToAccept = array( 'fullname', 'nickname', 'email', 'language' );
			$oidAttributes = array();

			foreach ( $oidAttributesToAccept as $oidAttr ) {
				if ( $oidAttr == 'fullname' && !$wgAllowRealName ) {
					continue;
				}

				if ( array_key_exists( $oidAttr, $sreg ) ) {
					$oidAttributes[] = Xml::tags( 'div', array(), wfMsgHtml( "openid$oidAttr" ) . ': ' . Xml::tags( 'i', array(), $sreg[$oidAttr] ) );
				}
			}

			$oidAttributesUpdate = '';
			if ( count( $oidAttributes ) > 0 ) {
				$oidAttributesUpdate = Xml::openElement( 'div', array( 'style' => 'margin-left: 25px' ) ) . "\n" .
					Xml::check( 'wpUpdateUserInfo', false, array( 'id' => 'wpUpdateUserInfo' ) ) . "\n" .
					Xml::openElement( 'label', array( 'for' => 'wpUpdateUserInfo' ) ) .
					wfMsgHtml( 'openidupdateuserinfo' ) .
					Xml::tags( 'div', array( 'style' => 'margin-left: 25px' ), implode( "\n", $oidAttributes ) ) .
					Xml::closeElement( 'label' ) . Xml::closeElement( 'div' );
			}

			$wgOut->addHTML(
				Xml::openElement( 'div' ) .
				Xml::radioLabel( wfMsg( 'openidchooseexisting' ), 'wpNameChoice', 'existing', 'wpNameChoiceExisting' ) . "\n" .
				Xml::input( 'wpExistingName', 16, $name, array( 'id' => 'wpExistingName' ) ) . "\n" .
				wfMsgHtml( 'openidchoosepassword' ) . "\n" .
				Xml::password( 'wpExistingPassword' ) . "\n" .
				$oidAttributesUpdate . "\n" .
				Xml::closeElement( 'div' )
			);
		}

		# These options won't exist if we can't get them.
		if ( array_key_exists( 'fullname', $sreg ) && $this->userNameOK( $sreg['fullname'] ) ) {
			$wgOut->addHTML(
				Xml::openElement( 'div' ) .
				Xml::radioLabel( wfMsg( 'openidchoosefull', $sreg['fullname'] ), 'wpNameChoice', 'full', 'wpNameChoiceFull', !$def ) .
				Xml::closeElement( 'div' )
			);
			$def = true;
		}

		$idname = $this->toUserName( $openid );
		if ( $idname && $this->userNameOK( $idname ) ) {
			$wgOut->addHTML(
				Xml::openElement( 'div' ) .
				Xml::radioLabel( wfMsg( 'openidchooseurl', $idname ), 'wpNameChoice', 'url', 'wpNameChoiceUrl', !$def ) .
				Xml::closeElement( 'div' )
			);
			$def = true;
		}

		# These are always available
		$wgOut->addHTML(
			Xml::openElement( 'div' ) . "\n" .
			Xml::radioLabel( wfMsg( 'openidchooseauto', $this->automaticName( $sreg ) ), 'wpNameChoice', 'auto', 'wpNameChoiceAuto', !$def ) . "\n" .
			Xml::closeElement( 'div' ) . "\n" .
			Xml::openElement( 'div' ) . "\n" .
			Xml::radioLabel( wfMsg( 'openidchoosemanual' ), 'wpNameChoice', 'manual', 'wpNameChoiceManual' ) . "\n" .
			Xml::input( 'wpNameValue', 16, false, array( 'id' => 'wpNameValue' ) ) . "\n" .
			Xml::closeElement( 'div' ) . "\n" .
			Xml::submitButton( wfMsg( 'login' ), array( 'name' => 'wpOK' ) ) . Xml::submitButton( wfMsg( 'cancel' ), array( 'name' => 'wpCancel' ) ) . "\n" .
			Xml::closeElement( 'form' )
		);
	}

	function loginSetCookie( $openid ) {
		global $wgRequest, $wgOpenIDCookieExpiration;
		$wgRequest->response()->setcookie( 'OpenID', $openid, time() +  $wgOpenIDCookieExpiration );
	}

	/**
	 * Handle "Choose name" form submission
	 */
	function chooseName() {
		global $wgRequest, $wgUser, $wgOut;

		list( $openid, $sreg ) = $this->fetchValues();
		if ( is_null( $openid ) ) {
			wfDebug( "OpenID: aborting in ChooseName because identity_url is missing\n" );
			$this->clearValues();
			# No messing around, here
			$wgOut->showErrorPage( 'openiderror', 'openiderrortext' );
			return;
		}

		if ( $wgRequest->getCheck( 'wpCancel' ) ) {
			$this->clearValues();
			$wgOut->showErrorPage( 'openidcancel', 'openidcanceltext' );
			return;
		}

		$choice = $wgRequest->getText( 'wpNameChoice' );
		$nameValue = $wgRequest->getText( 'wpNameValue' );

		if ( $choice == 'existing' ) {
			$user = $this->attachUser( $openid, $sreg,
				$wgRequest->getText( 'wpExistingName' ),
				$wgRequest->getText( 'wpExistingPassword' )
			);

			if ( !$user ) {
				$this->chooseNameForm( $openid, $sreg, 'wrongpassword' );
				return;
			}

			if ( $wgRequest->getText( 'wpUpdateUserInfo' ) ) {
				$this->updateUser( $user, $sreg );
			}
		} else {
			$name = $this->getUserName( $openid, $sreg, $choice, $nameValue );

			if ( !$name || !$this->userNameOK( $name ) ) {
				wfDebug( "OpenID: Name not OK: '$name'\n" );
				$this->chooseNameForm( $openid, $sreg );
				return;
			}

			$user = $this->createUser( $openid, $sreg, $name );
		}

		if ( is_null( $user ) ) {
			wfDebug( "OpenID: aborting in ChooseName because we could not create user object\n" );
			$this->clearValues();
			$wgOut->showErrorPage( 'openiderror', 'openiderrortext' );
			return;
		}

		$wgUser = $user;

		$this->clearValues();

		$this->displaySuccessLogin( $openid );
	}

	/**
	 * Called when returning from the authentication server
	 * Find the user with the given openid, if any or displays the "Choose name"
	 * form
	 */
	function finish() {
		global $wgOut, $wgUser;

		wfSuppressWarnings();
		$consumer = $this->getConsumer();
		$response = $consumer->complete( $this->scriptUrl( 'Finish' ) );
		wfRestoreWarnings();

		if ( is_null( $response ) ) {
			wfDebug( "OpenID: aborting in auth because no response was recieved\n" );
			$wgOut->showErrorPage( 'openiderror', 'openiderrortext' );
			return;
		}

		switch ( $response->status ) {
		case Auth_OpenID_CANCEL:
			// This means the authentication was cancelled.
			$wgOut->showErrorPage( 'openidcancel', 'openidcanceltext' );
			break;
		case Auth_OpenID_FAILURE:
			wfDebug( "OpenID: error message '" . $response->message . "'\n" );
			$wgOut->showErrorPage( 'openidfailure', 'openidfailuretext',
				array( ( $response->message ) ? $response->message : '' ) );
			break;
		case Auth_OpenID_SUCCESS:
			// This means the authentication succeeded.
			wfSuppressWarnings();
			$openid = $response->getDisplayIdentifier();
			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse( $response );
			$sreg = $sreg_resp->contents();
			wfRestoreWarnings();

			if ( is_null( $openid ) ) {
				wfDebug( "OpenID: aborting in auth success because display identifier is missing\n" );
				$wgOut->showErrorPage( 'openiderror', 'openiderrortext' );
				return;
			}

			$user = self::getUser( $openid );

			if ( $user instanceof User ) {
				$this->updateUser( $user, $sreg ); # update from server
			} else {
				# For easy names
				$name = $this->createName( $openid, $sreg );
				if ( $name ) {
					$user = $this->createUser( $openid, $sreg, $name );
				} else {
					# For hard names
					$this->saveValues( $openid, $sreg );
					$this->chooseNameForm( $openid, $sreg );
					return;
				}
			}

			if ( !$user instanceof User ) {
				wfDebug( "OpenID: aborting in auth success because we could not create user object\n" );
				$wgOut->showErrorPage( 'openiderror', 'openiderrortext' );
			} else {
				$wgUser = $user;
				$this->displaySuccessLogin( $openid );
			}
		}
	}

	/**
	 * Update some user's settings with value get from OpenID
	 *
	 * @param $user User object
	 * @param $sreg Array of options get from OpenID
	 */
	function updateUser( $user, $sreg ) {
		global $wgAllowRealName, $wgEmailAuthentication;

		// Back compat with old option
		$updateAll = $user->getOption( 'openid-update-userinfo-on-login' );

		// Nick name
		if ( $updateAll || $user->getOption( 'openid-update-userinfo-on-login-nickname' ) ) {
			// FIXME: only update if there's been a change
			if ( array_key_exists( 'nickname', $sreg ) )
				$user->setOption( 'nickname', $sreg['nickname'] );
		}

		// E-mail
		if ( $updateAll || $user->getOption( 'openid-update-userinfo-on-login-email' ) ) {
			if ( array_key_exists( 'email', $sreg ) ) {
				$email = $sreg['email'];
				// If email changed, then email a confirmation mail
				if ( $email != $user->getEmail() ) {
					$user->setEmail( $email );
					$user->invalidateEmail();
					if ( $wgEmailAuthentication && $email != '' ) {
						$result = $user->sendConfirmationMail();
						if( WikiError::isError( $result ) ) {
							$wgOut->addWikiMsg( 'mailerror', $result->getMessage() );
						}
					}
				}
			}
		}

		// Full name
		if ( $wgAllowRealName && ( $updateAll || $user->getOption( 'openid-update-userinfo-on-login-fullname' ) ) ) {
			if ( array_key_exists( 'fullname', $sreg ) )
				$user->setRealName( $sreg['fullname'] );
		}

		// Language
		if ( $updateAll || $user->getOption( 'openid-update-userinfo-on-login-language' ) ) {
			if ( array_key_exists( 'language', $sreg ) ) {
				# FIXME: check and make sure the language exists
				$user->setOption( 'language', $sreg['language'] );
			}
		}

		if ( $updateAll || $user->getOption( 'openid-update-userinfo-on-login-timezone' ) ) {
			if ( array_key_exists( 'timezone', $sreg ) ) {
				# FIXME: do something with it.
				# $offset = OpenIDTimezoneToTzoffset($sreg['timezone']);
				# $user->setOption('timecorrection', $offset);
			}
		}

		$user->saveSettings();
	}

	/**
	 * Display the final "Successful login"
	 *
	 * @param $openid String: OpenID url
	 */
	function displaySuccessLogin( $openid ) {
		global $wgUser, $wgOut;

		$this->setupSession();
		$wgUser->SetCookies();

		# Run any hooks; ignore results
		$inject_html = '';
		wfRunHooks( 'UserLoginComplete', array( &$wgUser, &$inject_html ) );

		# Set a cookie for later check-immediate use

		$this->loginSetCookie( $openid );

		$wgOut->setPageTitle( wfMsg( 'openidsuccess' ) );
		$wgOut->setRobotPolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->addWikiMsg( 'openidsuccess', $wgUser->getName(), $openid );
		$wgOut->addHtml( $inject_html );
		list( $returnto, $returntoquery ) = $this->returnTo();
		$wgOut->returnToMain( false, $returnto, $returntoquery );
	}

	function createUser( $openid, $sreg, $name ) {
		global $wgAuth;

		$user = User::newFromName( $name );

		if ( !$user ) {
			wfDebug( "OpenID: Error adding new user.\n" );
			return NULL;
		}

		$user->addToDatabase();
		$user->addNewUserLogEntry();

		if ( !$user->getId() ) {
			wfDebug( "OpenID: Error adding new user.\n" );
		} else {
			$wgAuth->initUser( $user );
			$wgAuth->updateUser( $user );

			# Update site stats
			$ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
			$ssUpdate->doUpdate();

			self::addUserUrl( $user, $openid );
			$this->updateUser( $user, $sreg );
			$user->saveSettings();
			return $user;
		}
	}

	function attachUser( $openid, $sreg, $name, $password ) {
		$user = User::newFromName( $name );

		if ( !$user ) {
			return null;
		}

		if ( !$user->checkPassword( $password ) ) {
			return null;
		}

		self::addUserUrl( $user, $openid );

		return $user;
	}

	# Methods to get the user name
	# ----------------------------

	function createName( $openid, $sreg ) {
		# try nickname
		if ( array_key_exists( 'nickname', $sreg ) &&
			$this->userNameOK( $sreg['nickname'] ) ) {
			return $sreg['nickname'];
		} else {
			return null;
		}
	}


	function getUserName( $openid, $sreg, $choice, $nameValue ) {
		switch ( $choice ) {
		 case 'full':
			return ( ( array_key_exists( 'fullname', $sreg ) ) ? $sreg['fullname'] : null );
			break;
		 case 'url':
			return $this->toUserName( $openid );
			break;
		 case 'auto':
			return $this->automaticName( $sreg );
			break;
		 case 'manual':
			return $nameValue;
		 default:
			return null;
		}
	}

	function toUserName( $openid ) {
        if ( Auth_Yadis_identifierScheme( $openid ) == 'XRI' ) {
			return $this->toUserNameXri( $openid );
		} else {
			return $this->toUserNameUrl( $openid );
		}
	}

	/**
	 * We try to use an OpenID URL as a legal MediaWiki user name in this order
	 * 1. Plain hostname, like http://evanp.myopenid.com/
	 * 2. One element in path, like http://profile.typekey.com/EvanProdromou/
	 *   or http://getopenid.com/evanprodromou
	 */
    function toUserNameUrl( $openid ) {
		static $bad = array( 'query', 'user', 'password', 'port', 'fragment' );

	    $parts = parse_url( $openid );

		# If any of these parts exist, this won't work

		foreach ( $bad as $badpart ) {
			if ( array_key_exists( $badpart, $parts ) ) {
				return NULL;
			}
		}

		# We just have host and/or path

		# If it's just a host...
		if ( array_key_exists( 'host', $parts ) &&
			( !array_key_exists( 'path', $parts ) || strcmp( $parts['path'], '/' ) == 0 ) )
		{
			$hostparts = explode( '.', $parts['host'] );

			# Try to catch common idiom of nickname.service.tld

			if ( ( count( $hostparts ) > 2 ) &&
				( strlen( $hostparts[count( $hostparts ) - 2] ) > 3 ) && # try to skip .co.uk, .com.au
				( strcmp( $hostparts[0], 'www' ) != 0 ) )
			{
				return $hostparts[0];
			} else {
				# Do the whole hostname
				return $parts['host'];
			}
		} else {
			if ( array_key_exists( 'path', $parts ) ) {
				# Strip starting, ending slashes
				$path = preg_replace( '@/$@', '', $parts['path'] );
				$path = preg_replace( '@^/@', '', $path );
				if ( strpos( $path, '/' ) === false ) {
					return $path;
				}
			}
		}

		return null;
	}

	function toUserNameXri( $xri ) {
		$base = $this->xriBase( $xri );

		if ( !$base ) {
			return null;
		} else {
			# =evan.prodromou
			# or @gratis*evan.prodromou
			$parts = explode( '*', substr( $base, 1 ) );
			return array_pop( $parts );
		}
	}

	function automaticName( $sreg ) {
		if ( array_key_exists( 'nickname', $sreg ) && # try auto-generated from nickname
			strlen( $sreg['nickname'] ) > 0 ) {
			return $this->firstAvailable( $sreg['nickname'] );
		} else { # try auto-generated
			return $this->firstAvailable( wfMsg( 'openidusernameprefix' ) );
		}
	}

	/**
	 * Get an auto-incremented name
	 */
	function firstAvailable( $prefix ) {
		for ( $i = 2; ; $i++ ) { # FIXME: this is the DUMB WAY to do this
			$name = "$prefix$i";
			if ( $this->userNameOK( $name ) ) {
				return $name;
			}
		}
	}

	/**
	 * Is this name OK to use as a user name?
	 */
	function userNameOK( $name ) {
		global $wgReservedUsernames;
		return ( 0 == User::idFromName( $name ) &&
				!in_array( $name, $wgReservedUsernames ) );
	}

	# Session stuff
	# -------------

	function saveValues( $response, $sreg ) {
		$this->setupSession();

		$_SESSION['openid_consumer_response'] = $response;
		$_SESSION['openid_consumer_sreg'] = $sreg;

		return true;
	}

	function clearValues() {
		unset( $_SESSION['openid_consumer_response'] );
		unset( $_SESSION['openid_consumer_sreg'] );
		return true;
	}

	function fetchValues() {
		return array( $_SESSION['openid_consumer_response'], $_SESSION['openid_consumer_sreg'] );
	}

	function returnTo() {
		$returnto = isset( $_SESSION['openid_consumer_returnto'] ) ? $_SESSION['openid_consumer_returnto'] : '';
		$returntoquery = isset( $_SESSION['openid_consumer_returntoquery'] ) ? $_SESSION['openid_consumer_returntoquery'] : '';
		return array( $returnto, $returntoquery );
	}

	function setReturnTo( $returnto, $returntoquery ) {
		$_SESSION['openid_consumer_returnto'] = $returnto;
		$_SESSION['openid_consumer_returntoquery'] = $returntoquery;
	}
}
