<?php
/**
 * @file
 * @ingroup SpecialPage
 */

/**
 * Let users recover their password.
 * @ingroup SpecialPage
 */
class SpecialResetpass extends SpecialPage {
	public function __construct() {
		parent::__construct( 'Resetpass' );
	}
	
	public $mFormFields = array(
		'Name' => array(
			'type'          => 'info',
			'label-message' => 'yourname',
			'default'       => '',
		),
		'Password' => array(
			'type'          => 'password',
			'label-message' => 'oldpassword',
			'size'          => '20',
			'id'            => 'wpPassword',
			'required'      => '',
		),
		'NewPassword' => array(
			'type'          => 'password',
			'label-message' => 'newpassword',
			'size'          => '20',
			'id'            => 'wpNewPassword',
			'required'      => '',
		),
		'Retype' => array(
			'type'          => 'password',
			'label-message' => 'retypenew',
			'size'          => '20',
			'id'            => 'wpRetype',
			'required'      => '',
		),
		'Remember' => array(
			'type'          => 'check',
			'label-message' => 'remembermypassword',
			'id'            => 'wpRemember',
		),
	);
	public $mSubmitMsg = 'resetpass-submit-loggedin';
	public $mHeaderMsg = '';
	public $mHeaderMsgType = 'error';
	
	public $mUsername;
	public $mOldpass;
	public $mNewpass;
	public $mRetype;

	/**
	 * Main execution point
	 */
	function execute( $par ) {
		global $wgUser, $wgAuth, $wgOut, $wgRequest;

		$this->mUsername = $wgRequest->getVal( 'wpName', $wgUser->getName() );
		$this->mOldpass = $wgRequest->getVal( 'wpPassword' );
		$this->mNewpass = $wgRequest->getVal( 'wpNewPassword' );
		$this->mRetype = $wgRequest->getVal( 'wpRetype' );
		$this->mRemember = $wgRequest->getVal( 'wpRemember' );
		$this->mReturnTo = $wgRequest->getVal( 'returnto' );
		$this->mReturnToQuery = $wgRequest->getVal( 'returntoquery' );
		
		$this->setHeaders();
		$this->outputHeader();

		if( wfReadOnly() ){
			$wgOut->readOnlyPage();
			return false;
		}
		if( !$wgAuth->allowPasswordChange() ) {
			$wgOut->showErrorPage( 'errorpagetitle', 'resetpass_forbidden' );
			return false;
		}

		if( !$wgRequest->wasPosted() && !$wgUser->isLoggedIn() ) {
			$wgOut->showErrorPage( 'errorpagetitle', 'resetpass-no-info' );
			return false;
		}
		
		$data = array(
			'wpName'     => $this->mUsername,
			'wpPassword' => $this->mOldpass,
		);
		$this->mLogin =  new Login( new FauxRequest( $data, true ) );		

		if( $wgRequest->wasPosted() 
		    && $wgUser->matchEditToken( $wgRequest->getVal('wpEditToken') )
			&& $this->attemptReset() )
		{
			# Log the user in if they're not already (ie we're 
			# coming from the e-mail-password-reset route
			if( !$wgUser->isLoggedIn() ) {
				$this->mLogin->attemptLogin( $this->mNewpass );
				# Redirect out to the appropriate target.
				SpecialUserlogin::successfulLogin( 
					'resetpass_success', 
					$this->mReturnTo, 
					$this->mReturnToQuery,
					$this->mLogin->mLoginResult
				);
			} else {
				# Redirect out to the appropriate target.
				SpecialUserlogin::successfulLogin( 
					'resetpass_success', 
					$this->mReturnTo, 
					$this->mReturnToQuery
				);
			}
		} else {
			$this->showForm();
		}
	}

	function showForm() {
		global $wgOut, $wgUser;

		$wgOut->disallowUserJs();
		
		if( $wgUser->isLoggedIn() ){
			unset( $this->mFormFields['Remember'] );
		} else {
			# Request is coming from Special:UserLogin after it
			# authenticated someone with a temporary password.
			$this->mFormFields['Password']['label-message'] = 'resetpass-temp-password';
			$this->mSubmitMsg = 'resetpass_submit';
		}
		$this->mFormFields['Name']['default'] = $this->mUsername;
		
		$header = $this->mHeaderMsg
			? Html::element( 'div', array( 'class' => "{$this->mHeaderMsgType}box" ), wfMsgExt( $this->mHeaderMsg, 'parse' ) )
			: '';
				
		$form = new HTMLForm( $this->mFormFields, '' );
		$form->suppressReset();
		$form->setSubmitText( wfMsg( $this->mSubmitMsg ) );
		$form->setTitle( $this->getTitle() );
		$form->addHiddenField( 'wpName', $this->mUsername );
		$form->addHiddenField( 'returnto', $this->mReturnTo );
		$form->setWrapperLegend( wfMsg( 'resetpass_header' ) );
		$form->loadData();
		
		$form->displayForm( $this->mHeaderMsg );
	}

	/**
	 * Try to reset the user's password 
	 */
	protected function attemptReset() {
		
		if( !$this->mUsername
			|| !$this->mNewpass
			|| !$this->mRetype )
		{
			return false;
		}
		
		$user = $this->mLogin->getUser();
		if( !( $user instanceof User ) ){
			$this->mHeaderMsg = wfMsgExt( 'nosuchuser', 'parse' );
			return false;
		}
		
		if( $this->mNewpass !== $this->mRetype ) {
			wfRunHooks( 'PrefsPasswordAudit', array( $user, $this->mNewpass, 'badretype' ) );
			$this->mHeaderMsg = wfMsgExt( 'badretype', 'parse' );
			return false;
		}

		if( !$user->checkPassword( $this->mOldpass ) && !$user->checkTemporaryPassword( $this->mOldpass ) ) 
		{
			wfRunHooks( 'PrefsPasswordAudit', array( $user, $this->mNewpass, 'wrongpassword' ) );
			$this->mHeaderMsg = wfMsgExt( 'resetpass-wrong-oldpass', 'parse' );
			return false;
		}
		
		try {
			$user->setPassword( $this->mNewpass );
			wfRunHooks( 'PrefsPasswordAudit', array( $user, $this->mNewpass, 'success' ) );
			$this->mNewpass = $this->mOldpass = $this->mRetypePass = '';
		} catch( PasswordError $e ) {
			wfRunHooks( 'PrefsPasswordAudit', array( $user, $this->mNewpass, 'error' ) );
			$this->mHeaderMsg = $e->getMessage();
			return false;
		}
		
		$user->setCookies();
		$user->saveSettings();
		return true;
	}
}
