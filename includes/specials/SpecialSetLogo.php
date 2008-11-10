<?php
if (!defined('MEDIAWIKI'))
	die();
	
class SpecialSetLogo extends SpecialPage {
	function __construct( ) {
		parent::__construct( 'SetLogo', 'setlogo' );
	}
	
	function execute() {
		global $wgUser, $wgOut;
		
		$sk = $wgUser->getSkin();
		
		if (!$this->userCanExecute($wgUser)) {
			$this->displayRestrictionError();
			return;
		}
		
		$wgOut->setPageTitle( wfMsg( 'setlogo' ) );
		
		if ( $this->trySubmit() ) {
			return;
		}
		
		if (!$this->mLogo) {
			$this->mLogo = ConfigurationCache::get( 'logo' );
		}
		
		$wgOut->addWikiMsg( 'setlogo-intro' );
		
		## Build form
		$fields = array( 'setlogo-file' => wfInput( 'wpNewLogo', 45, $this->mLogo ) );
		$form = Xml::buildForm( $fields, 'setlogo-preview-button' );
		$form .= Xml::hidden( 'title', $this->getTitle()->getPrefixedText() );
		$form .= Xml::hidden( 'action', 'preview' );
		$form = Xml::tags( 'form', array( 'action' => $this->getTitle()->getLocalURL(), 'method' => 'post' ), $form );
		$form = Xml::fieldset( wfMsg( 'setlogo-fieldset' ), $form );
		
		$wgOut->addHTML( $form );
	}
	
	function trySubmit() {
		global $wgRequest, $wgUser, $wgOut;
		
		$sk = $wgUser->getSkin();
		
		$action = $wgRequest->getVal( 'action' );
		$this->mLogo = $selection = $wgRequest->getText( 'wpNewLogo' );
		$tokenOK = $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ), 'setlogo', $selection );
		
		if ($action == 'success') {
			## We've successfully set the logo
			$wgOut->addWikiMsg( 'setlogo-success' );
			return true;
		}
		
		## Sanity check on $selection
		if (!$selection) {
			return false;
		}
		
		$file = wfFindFile( $selection );
		
		if ( !$file || !$file->exists() ) {
			$error = wfMsgExt( 'setlogo-notfound', array( 'parseinline' ) );
			$error = Xml::tags( 'div', array( 'class' => 'error' ), $error );
			
			$wgOut->addHTML( $error );
			return false;
		}
		
		if ($action == 'set' && $tokenOK ) {
			## The file exists, we've confirmed it properly (evidenced by the edit token), etc. Everything seems sweet.
			ConfigurationCache::set( 'logo', $selection );
			ConfigurationCache::save();
			
			## Redirect them -- we don't want to repeatedly purge the cache.
			$wgOut->redirect( $this->getTitle()->getFullURL( 'action=success' ) );
			return true;
		} elseif ( $action == 'preview' || $action == 'set' ) {
			$wgOut->addWikiMsg( 'setlogo-confirm' );
			$title = Title::makeTitle( NS_IMAGE, $selection );
			
			$display = $sk->makeImageLink2( $title, $file,
				array('thumbnail' => 1, 'caption' => wfMsg('setlogo-confirm-caption'), 'align' => 'center' ),
				array( 'width' => 130, 'height' => 130 )
				);
				
			$wgOut->addHTML( $display );
			
			$form = Xml::hidden( 'wpNewLogo', $selection );
			$form .= Xml::hidden( 'wpEditToken', $wgUser->editToken( 'setlogo', $selection ) );
			$form .= Xml::hidden( 'title', $this->getTitle()->getPrefixedText() );
			$form .= Xml::hidden( 'action', 'set' );
			$form .= Xml::submitButton( wfMsg( 'setlogo-confirm-button' ) );
			$form = Xml::tags( 'form', array( 'method' => 'post', 'action' => $this->getTitle()->getFullURL(), 'class' => 'mw-setlogo-confirm' ), $form );
			
			$wgOut->addHTML( $form );
			
			return true;
		}
	}
}