<?php

class CodeRevisionStatusSetter extends CodeRevisionView {

	function __construct( $repoName, $rev ){
		parent::__construct( $repoName, $rev );
		
		global $wgRequest;
		$this->mStatus = $wgRequest->getText( 'wpStatus' );
	}

	function execute() {
		global $wgOut;
		
		if( $this->validPost( 'codereview-set-status' ) ) {
			$this->mRev->setStatus( $this->mStatus );
			
			$repo = $this->mRepo->getName();
			$rev = $this->mRev->getId();
			$special = SpecialPage::getTitleFor( 'Code', "$repo/$rev" );
		}
	}
	
	function validPost( $permission ) {
		return parent::validPost( $permission ) &&
			$this->mRev->isValidStatus( $this->mStatus );
	}
}
