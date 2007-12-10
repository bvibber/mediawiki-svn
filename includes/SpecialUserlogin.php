<?php
/**
 *
 * @addtogroup SpecialPage
 */

/**
 * constructor
 */
function wfSpecialUserlogin() {
	global $wgRequest;
	if( session_id() == '' ) {
		wfSetupSession();
	}

	$form = new LoginForm( $wgRequest );
	$form->execute();
}

/**
 * implements Special:Login
 * @addtogroup SpecialPage
 */
class LoginForm {

	const SUCCESS = 0;
	const NO_NAME = 1;
	const ILLEGAL = 2;
	const WRONG_PLUGIN_PASS = 3;
	const NOT_EXISTS = 4;
	const WRONG_PASS = 5;
	const EMPTY_PASS = 6;
	const RESET_PASS = 7;
	const ABORTED = 8;
	const COOKIE = 9;
	const NOCOOKIE = 10;
	const READ_ONLY = 11;
	const NOT_ALLOWED = 12;
	const SORBS = 13;
	const USER_EXISTS = 14;
	const BAD_RETYPE = 15;
	const TOO_SHORT = 16;
	const ABORT_ERROR = 17;
	const DB_ERROR = 18;
	const NO_EMAIL = 19;
	const MAIL_ERROR = 20;
	const ACCMAILTEXT = 21;
	const PASSWORD_SENT = 22;
	const PASSWORD_REMINDER_THROTTLED = 23;
	const NO_SUCH_USER = 24;
	const RATE_LIMITED = 25;
	const MAILPASSWORD_BLOCKED = 26;
	const RESETPASS_FORBIDDEN = 27;
	const NO_COOKIES_NEW = 28;
	const NO_COOKIES_LOGIN = 29;   
	const ERROR = 30;
	const SUCCESFUL_LOGIN = 31;
	const USER_BLOCKED = 32;

	var $mName, $mPassword, $mRetype, $mReturnTo, $mCookieCheck, $mPosted;
	var $mAction, $mCreateaccount, $mCreateaccountMail, $mMailmypassword;
	var $mLoginattempt, $mRemember, $mEmail, $mDomain, $mLanguage;

	/**
	 * Constructor
	 * @param WebRequest $request A WebRequest object passed by reference
	 */
	function LoginForm( &$request ) {
		global $wgLang, $wgAllowRealName, $wgEnableEmail;
		global $wgAuth;

		$this->mType = $request->getText( 'type' );
		$this->mName = $request->getText( 'wpName' );
		$this->mPassword = $request->getText( 'wpPassword' );
		$this->mRetype = $request->getText( 'wpRetype' );
		$this->mDomain = $request->getText( 'wpDomain' );
		$this->mReturnTo = $request->getVal( 'returnto' );
		$this->mCookieCheck = $request->getVal( 'wpCookieCheck' );
		$this->mPosted = $request->wasPosted();
		$this->mCreateaccount = $request->getCheck( 'wpCreateaccount' );
		$this->mCreateaccountMail = $request->getCheck( 'wpCreateaccountMail' )
		                            && $wgEnableEmail;
		$this->mMailmypassword = $request->getCheck( 'wpMailmypassword' )
		                         && $wgEnableEmail;
		$this->mLoginattempt = $request->getCheck( 'wpLoginattempt' );
		$this->mAction = $request->getVal( 'action' );
		$this->mRemember = $request->getCheck( 'wpRemember' );
		$this->mLanguage = $request->getText( 'uselang' );

		if( $wgEnableEmail ) {
			$this->mEmail = $request->getText( 'wpEmail' );
		} else {
			$this->mEmail = '';
		}
		if( $wgAllowRealName ) {
		    $this->mRealName = $request->getText( 'wpRealName' );
		} else {
		    $this->mRealName = '';
		}

		if( !$wgAuth->validDomain( $this->mDomain ) ) {
			$this->mDomain = 'invaliddomain';
		}
		$wgAuth->setDomain( $this->mDomain );

		# When switching accounts, it sucks to get automatically logged out
		if( $this->mReturnTo == $wgLang->specialPage( 'Userlogout' ) ) {
			$this->mReturnTo = '';
		}
	}

	function execute() {
		$resultDetails = null;
		$value = null;
		if ( !is_null( $this->mCookieCheck ) ) {
			$value = $this->onCookieRedirectCheck( $this->mCookieCheck, $resultDetails );
			return $this->processRest($value, $resultDetails);
		} else if( $this->mPosted ) {
			if( $this->mCreateaccount ) {
				$value = $this->addNewAccount($resultDetails);
				return $this->processRest($value,$resultDetails);
			} else if ( $this->mCreateaccountMail ) {
				$value = $this->addNewAccountMailPassword($resultDetails);
				return $this->processRest($value,$resultDetails);
			} else if ( $this->mMailmypassword ) {
				$value = $this->mailPassword($resultDetails);
				return $this->processRest($value,$resultDetails);
			} else if ( ( 'submitlogin' == $this->mAction ) || $this->mLoginattempt ) {
				return $this->processLogin();
			}
		}
		$this->mainLoginForm( '' );
	}

	/**
	 * @private
	 */
	function addNewAccountMailPassword(&$results) {
		global $wgOut;

		if ('' == $this->mEmail) {
			return self::NO_EMAIL; 
		}

		$u = $this->addNewaccountInternal($results);

		if( !is_object($u) ) {
			return $u;
		}

		// Wipe the initial password and mail a temporary one
		$u->setPassword( null );
		$u->saveSettings();
		$results['error'] = $this->mailPasswordInternal( $u, false );

		wfRunHooks( 'AddNewAccount', array( $u ) );

		$results['user'] = $u;
		if( WikiError::isError( $results['error']  ) ) {
			return self::MAIL_ERROR;
		} else {            
			return self::ACCMAILTEXT;
		}
	}

	/**
	 * @private
	 */
	function addNewAccount(&$results) {
		global $wgUser, $wgEmailAuthentication;

		# Create the account and abort if there's a problem doing so
		$u = $this->addNewAccountInternal($results);
		if( !is_object($u) )
			return $u;

		# If we showed up language selection links, and one was in use, be
		# smart (and sensible) and save that language as the user's preference
		global $wgLoginLanguageSelector;
		if( $wgLoginLanguageSelector && $this->mLanguage )
			$u->setOption( 'language', $this->mLanguage );

		# Save user settings and send out an email authentication message if needed
		$u->saveSettings();
		if( $wgEmailAuthentication && User::isValidEmailAddr( $u->getEmail() ) ) {
			global $wgOut;
			$results['error'] = $u->sendConfirmationMail();
			$results['mailMsg'] = 0;
			if( WikiError::isError( $results['error'] ) ) {
				$results['mailMsg'] = 2;
			} else {
				$results['mailMsg'] = 1;
			}
		}

		# If not logged in, assume the new account as the current one and set session cookies
		# then show a "welcome" message or a "need cookies" message as needed
		if( $wgUser->isAnon() ) {
			$wgUser = $u;
			$wgUser->setCookies();
			wfRunHooks( 'AddNewAccount', array( $wgUser ) );
			if( $this->hasSessionCookie() ) {
				return self::COOKIE;
			} else {
				return self::NOCOOKIE;
			}
		} else {
			# Confirm that the account was created
			$results['user'] = $u;

			wfRunHooks( 'AddNewAccount', array( $u ) );
			return self::SUCCESS;
		}
	}

	/**
	 * @private
	 */
	function addNewAccountInternal(&$results) {
		global $wgUser, $wgOut;
		global $wgEnableSorbs, $wgProxyWhitelist;
		global $wgMemc, $wgAccountCreationThrottle;
		global $wgAuth, $wgMinimalPasswordLength;
		global $wgEmailConfirmToEdit;

		// If the user passes an invalid domain, something is fishy
		if( !$wgAuth->validDomain( $this->mDomain ) ) {
			return self::WRONG_PASS;
		}

		// If we are not allowing users to login locally, we should
		// be checking to see if the user is actually able to
		// authenticate to the authentication server before they
		// create an account (otherwise, they can create a local account
		// and login as any domain user). We only need to check this for
		// domains that aren't local.
		if( 'local' != $this->mDomain && '' != $this->mDomain ) {
			if( !$wgAuth->canCreateAccounts() && ( !$wgAuth->userExists( $this->mName ) || !$wgAuth->authenticate( $this->mName, $this->mPassword ) ) ) {
				return self::WRONG_PASS;
			}
		}

		if ( wfReadOnly() ) {
			return self::READ_ONLY;
		}

		# Check permissions
		if ( !$wgUser->isAllowed( 'createaccount' ) ) {
			return self::NOT_ALLOWED;
		} elseif ( $wgUser->isBlockedFromCreateAccount() ) {
			return self::USER_BLOCKED;
		}

		$ip = wfGetIP();
		if ( $wgEnableSorbs && !in_array( $ip, $wgProxyWhitelist ) &&
		  $wgUser->inSorbsBlacklist( $ip ) )
		{
			$results['ip'] = $ip;
			return self::SORBS;
		}

		# Now create a dummy user ($u) and check if it is valid
		$name = trim( $this->mName );
		$u = User::newFromName( $name, 'creatable' );
		if ( is_null( $u ) ) {
			return self::NO_NAME;
		}

		if ( 0 != $u->idForName() ) {
			return self::USER_EXISTS;
		}

		if ( 0 != strcmp( $this->mPassword, $this->mRetype ) ) {
			return self::BAD_RETYPE;
		}

		# check for minimal password length
		if ( !$u->isValidPassword( $this->mPassword ) ) {
			if ( !$this->mCreateaccountMail ) {
				$this->mainLoginForm( wfMsg( 'passwordtooshort', $wgMinimalPasswordLength ) );
				return self::TOO_SHORT;
			} else {
				# do not force a password for account creation by email
				# set pseudo password, it will be replaced later by a random generated password
				$this->mPassword = '-';
			}
		}

		# if you need a confirmed email address to edit, then obviously you need an email address.
		if ( $wgEmailConfirmToEdit && empty( $this->mEmail ) ) {
			$this->mainLoginForm( wfMsg( 'noemailtitle' ) );
			return false;
		}

		if( !empty( $this->mEmail ) && !User::isValidEmailAddr( $this->mEmail ) ) {
			$this->mainLoginForm( wfMsg( 'invalidemailaddress' ) );
			return false;
		}

		# Set some additional data so the AbortNewAccount hook can be
		# used for more than just username validation
		$u->setEmail( $this->mEmail );
		$u->setRealName( $this->mRealName );

		$abortError = '';
		if( !wfRunHooks( 'AbortNewAccount', array( $u, &$abortError  ) ) ) {
			// Hook point to add extra creation throttles and blocks
			wfDebug( "LoginForm::addNewAccountInternal: a hook blocked creation\n" );
			$results['error'] = $abortError;
			return self::ABORT_ERROR;
		}

		if ( $wgAccountCreationThrottle && $wgUser->isPingLimitable() ) {
			$key = wfMemcKey( 'acctcreate', 'ip', $ip );
			$value = $wgMemc->incr( $key );
			if ( !$value ) {
				$wgMemc->set( $key, 1, 86400 );
			}
			if ( $value > $wgAccountCreationThrottle ) {
				return self::ILLEGAL;
			}
		}

		if( !$wgAuth->addUser( $u, $this->mPassword, $this->mEmail, $this->mRealName ) ) {
			return self::DB_ERROR;
		}

		return $this->initUser( $u, false );
	}

	/**
	 * Actually add a user to the database.
	 * Give it a User object that has been initialised with a name.
	 *
	 * @param $u User object.
	 * @param $autocreate boolean -- true if this is an autocreation via auth plugin
	 * @return User object.
	 * @private
	 */
	function initUser( $u, $autocreate ) {
		global $wgAuth;

		$u->addToDatabase();

		if ( $wgAuth->allowPasswordChange() ) {
			$u->setPassword( $this->mPassword );
		}

		$u->setEmail( $this->mEmail );
		$u->setRealName( $this->mRealName );
		$u->setToken();

		$wgAuth->initUser( $u, $autocreate );

		$u->setOption( 'rememberpassword', $this->mRemember ? 1 : 0 );
		$u->saveSettings();

		# Update user count
		$ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
		$ssUpdate->doUpdate();

		return $u;
	}

	/**
	 * Internally authenticate the login request.
	 *
	 * This may create a local account as a side effect if the
	 * authentication plugin allows transparent local account
	 * creation.
	 *
	 * @public
	 */
	function authenticateUserData() {
		global $wgUser, $wgAuth;
		if ( '' == $this->mName ) {
			return self::NO_NAME;
		}
		$u = User::newFromName( $this->mName );
		if( is_null( $u ) || !User::isUsableName( $u->getName() ) ) {
			return self::ILLEGAL;
		}
		if ( 0 == $u->getID() ) {
			global $wgAuth;
			/**
			 * If the external authentication plugin allows it,
			 * automatically create a new account for users that
			 * are externally defined but have not yet logged in.
			 */
			if ( $wgAuth->autoCreate() && $wgAuth->userExists( $u->getName() ) ) {
				if ( $wgAuth->authenticate( $u->getName(), $this->mPassword ) ) {
					$u = $this->initUser( $u, true );
				} else {
					return self::WRONG_PLUGIN_PASS;
				}
			} else {
				return self::NOT_EXISTS;
			}
		} else {
			$u->load();
		}

		// Give general extensions, such as a captcha, a chance to abort logins
		$abort = self::ABORTED;
		if( !wfRunHooks( 'AbortLogin', array( $u, $this->mPassword, &$abort ) ) ) {
			return $abort;
		}
		
		if (!$u->checkPassword( $this->mPassword )) {
			if( $u->checkTemporaryPassword( $this->mPassword ) ) {
				// The e-mailed temporary password should not be used
				// for actual logins; that's a very sloppy habit,
				// and insecure if an attacker has a few seconds to
				// click "search" on someone's open mail reader.
				//
				// Allow it to be used only to reset the password
				// a single time to a new value, which won't be in
				// the user's e-mail archives.
				//
				// For backwards compatibility, we'll still recognize
				// it at the login form to minimize surprises for
				// people who have been logging in with a temporary
				// password for some time.
				//
				// As a side-effect, we can authenticate the user's
				// e-mail address if it's not already done, since
				// the temporary password was sent via e-mail.
				//
				if( !$u->isEmailConfirmed() ) {
					$u->confirmEmail();
				}

				// At this point we just return an appropriate code
				// indicating that the UI should show a password
				// reset form; bot interfaces etc will probably just
				// fail cleanly here.
				//
				$retval = self::RESET_PASS;
			} else {
				$retval = '' == $this->mPassword ? self::EMPTY_PASS : self::WRONG_PASS;
			}
		} else {
			$wgAuth->updateUser( $u );
			$wgUser = $u;

			$retval = self::SUCCESS;
		}
		wfRunHooks( 'LoginAuthenticateAudit', array( $u, $this->mPassword, $retval ) );
		return $retval;
	}

	function processRest($value,$results = null) {
		global $wgUser, $wgAuth, $wgOut;
		if ($results['mailMsg'] == 1) {
			$wgOut->addWikiText( wfMsg( 'confirmemail_oncreate' ) );
		} else if ($results['mailMsg'] == 2) {
			$wgOut->addWikiText( wfMsg( 'confirmemail_sendfailed', $results['error']->getMessage() ) );
		}
		switch ($value)
		{
			case self::SUCCESS:
				$self = SpecialPage::getTitleFor( 'Userlogin' );
				$wgOut->setPageTitle( wfMsgHtml( 'accountcreated' ) );
				$wgOut->setArticleRelated( false );
				$wgOut->setRobotPolicy( 'noindex,nofollow' );
				$wgOut->addHtml( wfMsgWikiHtml( 'accountcreatedtext', $this->mName ) );
				$wgOut->returnToMain( false, $self );
				break;
			case self::COOKIE:
				$this->successfulLogin( wfMsg( 'welcomecreation', $wgUser->getName() ), false );
				break;
			case self::NOCOOKIE:
				$this->cookieRedirectCheck( 'new' );
				break;
			case self::WRONG_PASS :
				$this->mainLoginForm( wfMsg( 'wrongpassword' ) );
				break; 
			case self::READ_ONLY:
				$wgOut->readOnlyPage();
				break; 
			case self::NOT_ALLOWED:
				$this->userNotPrivilegedMessage();
				break; 
			case self::USER_BLOCKED:
				$this->userBlockedMessage();
				break;
			case self::SORBS:
				$this->mainLoginForm( wfMsg( 'sorbs_create_account_reason' ) . ' (' . htmlspecialchars( $results['ip'] ) . ')' );
				break;
			case self::NO_NAME:
				$this->mainLoginForm( wfMsg( 'noname' ) );
				break; 
			case self::USER_EXISTS:
				$this->mainLoginForm( wfMsg( 'userexists' ) );
				break; 
			case self::BAD_RETYPE:
				$this->mainLoginForm( wfMsg( 'badretype' ) );
				break; 
			case self::TOO_SHORT:
				$this->mainLoginForm( wfMsg( 'passwordtooshort', $wgMinimalPasswordLength ) );
				break; 
			case self::ABORT_ERROR:
				$this->mainLoginForm( $results['error'] );
				break; 
			case self::DB_ERROR:
				$this->mainLoginForm( wfMsg( 'externaldberror' ) );
				break; 
			case self::NO_EMAIL:
				$this->mainLoginForm( wfMsg( 'noemail', htmlspecialchars( $this->mName ) ) );
				break;
			case self::MAIL_ERROR:
				$wgOut->setPageTitle( wfMsg( 'accmailtitle' ) );
				$wgOut->setRobotpolicy( 'noindex,nofollow' );
				$wgOut->setArticleRelated( false );
				$this->mainLoginForm( wfMsg( 'mailerror', $results['error']->getMessage() ) );
				break;
			case self::ACCMAILTEXT:
				$wgOut->setPageTitle( wfMsg( 'accmailtitle' ) );
				$wgOut->setRobotpolicy( 'noindex,nofollow' );
				$wgOut->setArticleRelated( false );
				$wgOut->addWikiText( wfMsg( 'accmailtext', $results['user']->getName(), $results['user']->getEmail() ) );
				$wgOut->returnToMain( false );
				break;
			case self::ILLEGAL:
				$this->throttleHit( $wgAccountCreationThrottle );
				break;
			case self::PASSWORD_SENT:
				$this->mainLoginForm( wfMsg( 'passwordsent', $results['user']->getName() ), 'success' );
				break;
			case self::PASSWORD_REMINDER_THROTTLED:
				global $wgPasswordReminderResendTime;
				# Round the time in hours to 3 d.p., in case someone is specifying minutes or seconds.
				$this->mainLoginForm( wfMsg( 'throttled-mailpassword', round( $wgPasswordReminderResendTime, 3 ) ) );
				break;
			case self::NO_SUCH_USER:
				$this->mainLoginForm( wfMsg( 'nosuchuser', $results['user']->getName() ) );
				break;
			case self::RATE_LIMITED:
				$wgOut->rateLimited();
				break;
			case self::MAILPASSWORD_BLOCKED:
				$this->mainLoginForm( wfMsg( 'blocked-mailpassword' ) );
				break;
			case self::RESETPASS_FORBIDDEN:
				$this->mainLoginForm( wfMsg( 'resetpass_forbidden' ) );
				break;
			case self::NO_COOKIES_NEW:
				$this->mainLoginForm( wfMsg( 'nocookiesnew' ) );
				break;
			case self::NO_COOKIES_LOGIN:
				$this->mainLoginForm( wfMsg( 'nocookieslogin' ) );
				break;
			case self::ERROR:
				$this->mainLoginForm( wfMsg( 'error' ) );
				break;
			case self::LOGIN_SUCCESS:
				$this->successfulLogin( wfMsg( 'loginsuccess', $wgUser->getName() ) );
				break;
			default:
				wfDebugDieBacktrace( "Unhandled case value" );
		}
	}

	function processLogin() {
		global $wgUser, $wgAuth;

		switch ($this->authenticateUserData())
		{
			case self::SUCCESS:
				# We've verified now, update the real record
				if( (bool)$this->mRemember != (bool)$wgUser->getOption( 'rememberpassword' ) ) {
					$wgUser->setOption( 'rememberpassword', $this->mRemember ? 1 : 0 );
					$wgUser->saveSettings();
				} else {
					$wgUser->invalidateCache();
				}
				$wgUser->setCookies();

				if( $this->hasSessionCookie() ) {
					return $this->successfulLogin( wfMsg( 'loginsuccess', $wgUser->getName() ) );
				} else {
					return $this->cookieRedirectCheck( 'login' );
				}
				break;

			case self::NO_NAME:
			case self::ILLEGAL:
				$this->mainLoginForm( wfMsg( 'noname' ) );
				break;
			case self::WRONG_PLUGIN_PASS:
				$this->mainLoginForm( wfMsg( 'wrongpassword' ) );
				break;
			case self::NOT_EXISTS:
				$this->mainLoginForm( wfMsg( 'nosuchuser', htmlspecialchars( $this->mName ) ) );
				break;
			case self::WRONG_PASS:
				$this->mainLoginForm( wfMsg( 'wrongpassword' ) );
				break;
			case self::EMPTY_PASS:
				$this->mainLoginForm( wfMsg( 'wrongpasswordempty' ) );
				break;
			case self::RESET_PASS:
				$this->resetLoginForm( wfMsg( 'resetpass_announce' ) );
				break;
			default:
				wfDebugDieBacktrace( "Unhandled case value" );
		}
	}

	function resetLoginForm( $error ) {
		global $wgOut;
		$wgOut->addWikiText( "<div class=\"errorbox\">$error</div>" );
		$reset = new PasswordResetForm( $this->mName, $this->mPassword );
		$reset->execute();
	}

	/**
	 * @private
	 */
	function mailPassword(&$results){
		global $wgUser, $wgOut, $wgAuth;

		if( !$wgAuth->allowPasswordChange() ) {
			return self::RESETPASS_FORBIDDEN;
		}

		# Check against blocked IPs
		# fixme -- should we not?
		if( $wgUser->isBlocked() ) {
			return self::MAILPASSWORD_BLOCKED;
		}

		# Check against the rate limiter
		if( $wgUser->pingLimiter( 'mailpassword' ) ) {
			return self::RATE_LIMITED; 
		}

		if ( '' == $this->mName ) {
			return self::NO_NAME;
		}
		$u = User::newFromName( $this->mName );
		if( is_null( $u ) ) {
			return self::NO_NAME;
		}
		if ( 0 == $u->getID() ) {
			$results['user']=$u;
			return self::NO_SUCH_USER;
		}

		# Check against password throttle
		if ( $u->isPasswordReminderThrottled() ) {
			return self::PASSWORD_REMINDER_THROTTLED;
		}

		$results['error'] = $this->mailPasswordInternal( $u, true );
		$results['user'] = $u;
		if( WikiError::isError( $results['error'] ) ) {
			return self::MAIL_ERROR;
		} else {
			return self::PASSWORD_SENT;
		}
	}


	/**
	 * @param object user
	 * @param bool throttle
	 * @param string message name of email title
	 * @param string message name of email text
	 * @return mixed true on success, WikiError on failure
	 * @private
	 */
	function mailPasswordInternal( $u, $throttle = true, $emailTitle = 'passwordremindertitle', $emailText = 'passwordremindertext' ) {
		global $wgCookiePath, $wgCookieDomain, $wgCookiePrefix, $wgCookieSecure;
		global $wgServer, $wgScript;

		if ( '' == $u->getEmail() ) {
			return new WikiError( wfMsg( 'noemail', $u->getName() ) );
		}

		$np = $u->randomPassword();
		$u->setNewpassword( $np, $throttle );

		setcookie( "{$wgCookiePrefix}Token", '', time() - 3600, $wgCookiePath, $wgCookieDomain, $wgCookieSecure );

		$u->saveSettings();

		$ip = wfGetIP();
		if ( '' == $ip ) { $ip = '(Unknown)'; }

		$m = wfMsg( $emailText, $ip, $u->getName(), $np, $wgServer . $wgScript );
		$result = $u->sendMail( wfMsg( $emailTitle ), $m );

		return $result;
	}


	/**
	 * @param string $msg Message that will be shown on success
	 * @param bool $auto Toggle auto-redirect to main page; default true
	 * @private
	 */
	function successfulLogin( $msg, $auto = true ) {
		global $wgUser;
		global $wgOut;

		# Run any hooks; ignore results

		wfRunHooks('UserLoginComplete', array(&$wgUser));

		$wgOut->setPageTitle( wfMsg( 'loginsuccesstitle' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->addWikiText( $msg );
		if ( !empty( $this->mReturnTo ) ) {
			$wgOut->returnToMain( $auto, $this->mReturnTo );
		} else {
			$wgOut->returnToMain( $auto );
		}
	}

	/** */
	function userNotPrivilegedMessage() {
		global $wgOut;

		$wgOut->setPageTitle( wfMsg( 'whitelistacctitle' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );

		$wgOut->addWikiText( wfMsg( 'whitelistacctext' ) );

		$wgOut->returnToMain( false );
	}

	/** */
	function userBlockedMessage() {
		global $wgOut, $wgUser;

		# Let's be nice about this, it's likely that this feature will be used
		# for blocking large numbers of innocent people, e.g. range blocks on 
		# schools. Don't blame it on the user. There's a small chance that it 
		# really is the user's fault, i.e. the username is blocked and they 
		# haven't bothered to log out before trying to create an account to 
		# evade it, but we'll leave that to their guilty conscience to figure
		# out.

		$wgOut->setPageTitle( wfMsg( 'cantcreateaccounttitle' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );

		$ip = wfGetIP();
		$blocker = User::whoIs( $wgUser->mBlock->mBy );
		$block_reason = $wgUser->mBlock->mReason;

		$wgOut->addWikiText( wfMsg( 'cantcreateaccount-text', $ip, $block_reason, $blocker ) );
		$wgOut->returnToMain( false );
	}

	/**
	 * @private
	 */
	function mainLoginForm( $msg, $msgtype = 'error' ) {
		global $wgUser, $wgOut, $wgAllowRealName, $wgEnableEmail;
		global $wgCookiePrefix, $wgAuth, $wgLoginLanguageSelector;
		global $wgAuth, $wgEmailConfirmToEdit;

		if ( $this->mType == 'signup' ) {
			if ( !$wgUser->isAllowed( 'createaccount' ) ) {
				$this->userNotPrivilegedMessage();
				return;
			} elseif ( $wgUser->isBlockedFromCreateAccount() ) {
				$this->userBlockedMessage();
				return;
			}
		}

		if ( '' == $this->mName ) {
			if ( $wgUser->isLoggedIn() ) {
				$this->mName = $wgUser->getName();
			} else {
				$this->mName = isset( $_COOKIE[$wgCookiePrefix.'UserName'] ) ? $_COOKIE[$wgCookiePrefix.'UserName'] : null;
			}
		}

		$titleObj = SpecialPage::getTitleFor( 'Userlogin' );

		if ( $this->mType == 'signup' ) {
			$template = new UsercreateTemplate();
			$q = 'action=submitlogin&type=signup';
			$linkq = 'type=login';
			$linkmsg = 'gotaccount';
		} else {
			$template = new UserloginTemplate();
			$q = 'action=submitlogin&type=login';
			$linkq = 'type=signup';
			$linkmsg = 'nologin';
		}

		if ( !empty( $this->mReturnTo ) ) {
			$returnto = '&returnto=' . wfUrlencode( $this->mReturnTo );
			$q .= $returnto;
			$linkq .= $returnto;
		}

		# Pass any language selection on to the mode switch link
		if( $wgLoginLanguageSelector && $this->mLanguage )
			$linkq .= '&uselang=' . $this->mLanguage;

		$link = '<a href="' . htmlspecialchars ( $titleObj->getLocalUrl( $linkq ) ) . '">';
		$link .= wfMsgHtml( $linkmsg . 'link' ); # Calling either 'gotaccountlink' or 'nologinlink'
		$link .= '</a>';

		# Don't show a "create account" link if the user can't
		if( $this->showCreateOrLoginLink( $wgUser ) )
			$template->set( 'link', wfMsgHtml( $linkmsg, $link ) );
		else
			$template->set( 'link', '' );

		$template->set( 'header', '' );
		$template->set( 'name', $this->mName );
		$template->set( 'password', $this->mPassword );
		$template->set( 'retype', $this->mRetype );
		$template->set( 'email', $this->mEmail );
		$template->set( 'realname', $this->mRealName );
		$template->set( 'domain', $this->mDomain );

		$template->set( 'action', $titleObj->getLocalUrl( $q ) );
		$template->set( 'message', $msg );
		$template->set( 'messagetype', $msgtype );
		$template->set( 'createemail', $wgEnableEmail && $wgUser->isLoggedIn() );
		$template->set( 'userealname', $wgAllowRealName );
		$template->set( 'useemail', $wgEnableEmail );
		$template->set( 'emailrequired', $wgEmailConfirmToEdit );
		$template->set( 'canreset', $wgAuth->allowPasswordChange() );
		$template->set( 'remember', $wgUser->getOption( 'rememberpassword' ) or $this->mRemember  );

		# Prepare language selection links as needed
		if( $wgLoginLanguageSelector ) {
			$template->set( 'languages', $this->makeLanguageSelector() );
			if( $this->mLanguage )
				$template->set( 'uselang', $this->mLanguage );
		}

		// Give authentication and captcha plugins a chance to modify the form
		$wgAuth->modifyUITemplate( $template );
		if ( $this->mType == 'signup' ) {
			wfRunHooks( 'UserCreateForm', array( &$template ) );
		} else {
			wfRunHooks( 'UserLoginForm', array( &$template ) );
		}

		$wgOut->setPageTitle( wfMsg( 'userlogin' ) );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );
		$wgOut->disallowUserJs();  // just in case...
		$wgOut->addTemplate( $template );
	}

	/**
	 * @private
	 */
	function showCreateOrLoginLink( &$user ) {
		if( $this->mType == 'signup' ) {
			return( true );
		} elseif( $user->isAllowed( 'createaccount' ) ) {
			return( true );
		} else {
			return( false );
		}
	}

	/**
	 * Check if a session cookie is present.
	 *
	 * This will not pick up a cookie set during _this_ request, but is
	 * meant to ensure that the client is returning the cookie which was
	 * set on a previous pass through the system.
	 *
	 * @private
	 */
	function hasSessionCookie() {
		global $wgDisableCookieCheck, $wgRequest;
		return $wgDisableCookieCheck ? true : $wgRequest->checkSessionCookie();
	}

	/**
	 * @private
	 */
	function cookieRedirectCheck( $type ) {
		global $wgOut;

		$titleObj = SpecialPage::getTitleFor( 'Userlogin' );
		$check = $titleObj->getFullURL( 'wpCookieCheck='.$type );

		return $wgOut->redirect( $check );
	}

	/**
	 * @private
	 */
	function onCookieRedirectCheck( $type, &$results ) {
		global $wgUser;

		if ( !$this->hasSessionCookie() ) {
			if ( $type == 'new' ) {
				return self::NO_COOKIES_NEW;
			} else if ( $type == 'login' ) {
				return self::NO_COOKIES_LOGIN;
			} else {
				# shouldn't happen
				return self::ERROR;
			}
		} else {
			return self::LOGIN_SUCCESS;
		}
	}

	/**
	 * @private
	 */
	function throttleHit( $limit ) {
		global $wgOut;

		$wgOut->addWikiText( wfMsg( 'acct_creation_throttle_hit', $limit ) );
	}

	/**
	 * Produce a bar of links which allow the user to select another language
	 * during login/registration but retain "returnto"
	 *
	 * @return string
	 */
	function makeLanguageSelector() {
		$msg = wfMsgForContent( 'loginlanguagelinks' );
		if( $msg != '' && !wfEmptyMsg( 'loginlanguagelinks', $msg ) ) {
			$langs = explode( "\n", $msg );
			$links = array();
			foreach( $langs as $lang ) {
				$lang = trim( $lang, '* ' );
				$parts = explode( '|', $lang );
				if (count($parts) >= 2) {
					$links[] = $this->makeLanguageSelectorLink( $parts[0], $parts[1] );
				}
			}
			return count( $links ) > 0 ? wfMsgHtml( 'loginlanguagelabel', implode( ' | ', $links ) ) : '';
		} else {
			return '';
		}
	}

	/**
	 * Create a language selector link for a particular language
	 * Links back to this page preserving type and returnto
	 *
	 * @param $text Link text
	 * @param $lang Language code
	 */
	function makeLanguageSelectorLink( $text, $lang ) {
		global $wgUser;
		$self = SpecialPage::getTitleFor( 'Userlogin' );
		$attr[] = 'uselang=' . $lang;
		if( $this->mType == 'signup' )
			$attr[] = 'type=signup';
		if( $this->mReturnTo )
			$attr[] = 'returnto=' . $this->mReturnTo;
		$skin = $wgUser->getSkin();
		return $skin->makeKnownLinkObj( $self, htmlspecialchars( $text ), implode( '&', $attr ) );
	}
}

