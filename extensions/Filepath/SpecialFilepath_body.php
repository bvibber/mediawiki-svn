<?php
if (!defined('MEDIAWIKI')) {
	echo "Special:Filepath extension\n";
	die( 1 );
}

class SpecialFilepath extends SpecialPage {
	function SpecialFilepath() {
		SpecialPage::SpecialPage( 'Filepath' );
	}

	function execute( $par ) {
		global $wgRequest, $wgOut;

		wfLoadExtensionMessages( 'Filepath' );

		$file = isset( $par ) ? $par : $wgRequest->getText( 'file' );

		$title = Title::makeTitleSafe( NS_IMAGE, $file );

		if ( is_null( $title ) ) {
			$this->setHeaders();
			$this->outputHeader();
			$cform = new FilepathForm( $title );
			$cform->execute();
		} else {
			$file = Image::newFromTitle( $title );
			if ( $file && $file->exists() ) {
				$wgOut->redirect( $file->getURL() );
			} else {
				$wgOut->setStatusCode( 404 );
				$this->setHeaders();
				$this->outputHeader();
				$cform = new FilepathForm( $title );
				$cform->execute();
			}
		}
	}
}

class FilepathForm {
	var $mTitle;

	function FilepathForm( &$title ) {
		$this->mTitle =& $title;
	}

	function execute() {
		global $wgOut, $wgTitle, $wgScript;

		$wgOut->addHTML(
			wfElement( 'form',
				array(
					'id' => 'specialfilepath',
					'method' => 'get',
					'action' => $wgScript,
				),
				null
			) .
				wfHidden( 'title', $wgTitle->getPrefixedText() ) .
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
