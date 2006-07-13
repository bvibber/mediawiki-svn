<?php
if (!defined('MEDIAWIKI')) {
	echo "Special:Filepath extension\n";
	die( 1 );
}

# Add messages
global $wgMessageCache, $wgFilepathMessages;
foreach( $wgFilepathMessages as $key => $value ) {
	$wgMessageCache->addMessages( $wgFilepathMessages[$key], $key );
}

class SpecialFilepath extends SpecialPage {
	function SpecialFilepath() {
		SpecialPage::SpecialPage( 'Filepath' );
	}
	
	function execute( $par ) {
		global $wgRequest, $wgOut;

		$file = isset( $par ) ? $par : $wgRequest->getText( 'file' );
		
		$title = Title::makeTitleSafe( NS_IMAGE, $file );
		
		if ( ! is_null( $title ) && ! $title->exists() ) {
			$wgOut->setStatusCode( 404 );
			$this->setHeaders();
			$this->outputHeader();
			$cform = new FilepathForm( $title );
			$cform->execute();
		} else if ( is_null( $title ) ) {
			$this->setHeaders();
			$this->outputHeader();
			$cform = new FilepathForm( $title );
			$cform->execute();
		} else {
			$file = new Image( $title ); 
			$wgOut->redirect( $file->getURL() );
		}
	}
}

class FilepathForm {
	var $mTitle;
	
	function FilepathForm( &$title ) {
		$this->mTitle =& $title;
	}
	
	function execute() {
		global $wgOut, $wgTitle;

		$wgOut->addHTML(
			wfElement( 'form',
				array(
					'id' => 'specialfilepath',
					'method' => 'get',
					'action' => $wgTitle->escapeLocalUrl()
				),
				null
			) .
				wfOpenElement( 'label' ) .
					wfMsgHtml( 'filepath_page' ) .
					' ' .
					wfElement( 'input',
						array(
							'type' => 'text',
							'size' => 25,
							'name' => 'file',
							'value' => is_object( $this->mTitle ) ? $this->mTitle->getText() : ''
						),
						''
					) .
					' ' .
					wfElement( 'input',
						array(
							'type' => 'submit',
							'value' => wfMsgHtml( 'filepath_submit' )
						),
						''
					) .
				wfCloseElement( 'label' ) .
			wfCloseElement( 'form' )
		);
	}

}

?>
