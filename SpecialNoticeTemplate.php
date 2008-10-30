<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "CentralNotice extension\n";
	exit( 1 );
}

class SpecialNoticeTemplate extends SpecialPage { 
	
	/* Functions */
	
	function __construct() {
		// Initialize special page
		parent::__construct( 'NoticeTemplate' );
		
		// Internationalization
		wfLoadExtensionMessages( 'CentralNotice' );
	}
	
	function execute( $sub ) {
		global $wgOut, $wgUser, $wgRequest;
		
		// Begin output
		$this->setHeaders();
		
		// Get current skin
		$sk = $wgUser->getSkin();
		
		// Check permissions
		if ( !$wgUser->isAllowed( 'centralnotice_admin_rights' ) ) {
			$wgOut->permissionRequired( 'centralnotice_admin_rights' );
			return;
		}
		
		// Show summary
		$wgOut->addWikiText( wfMsg( 'centralnotice-summary' ) );
		
		// Show header
		CentralNotice::printHeader();
		
		// Handle forms
		if ( $wgRequest->wasPosted() ) {
			
			/*
			 * For debugging purposes only
			 */
			$body = file_get_contents( 'php://input' );
			$wgOut->addHtml( Xml::element( 'pre', null, $body ) );
			
			// Build list of templates to remove
			$toRemove = $wgRequest->getArray( 'removeTemplates' );
			if ( isset( $toRemove ) ) { 
				// Remove templates in list
				foreach ( $toRemove as $template ) {
					$this->removeTemplate( $template );
				}
				
				// Show a list of templates 
				$this->listTemplates();
				return;
			}
			
			// Build a list of notices to enable
			$enabledNotices = $wgRequest->getArray( 'enabled' );
			if ( isset( $enabledNotices ) ) {
				// Build a list of notices to disable
				$disabledNotices = array_diff( $this->getNoticesName(), $enabledNotices );
				
				// Set enabled/disabled flag accordingly
				foreach ( $enabledNotices as $notice ) {
					$this->updateEnabled( $notice, 'Y' );
				}
				foreach ( $disabledNotices as $notice ) {
					$this->updateEnabled( $notice, 'N' );
				}
			}
		}
		
		// Handle adding
		if ( $wgRequest->getVal( 'wpMethod' ) == 'addTemplate' ) {
			$this->addTemplate(
				$wgRequest->getVal( 'templateName' ),
				$wgRequest->getVal( 'templateBody' )
			);
		}
		
		// If this is a sub-page, show list of templates
		if ( $sub == 'listTemplates' ) { 
			$this->listTemplates();
			return;
		}
		
		$this->listTemplates();
	}

	private function updateEnabled( $update_notice, $enabled ) {
		global $egCentralNoticeTables;
		
		$dbw = wfGetDB( DB_MASTER );
		$res = $dbw->update( 'central_notice_campaign',
			array( cnc_enabled => $enabled ),
			array( cnc_template => $update_notice ),
			__METHOD__
		);
	}

	public static function previewTemplate() {
		//
	}

	function queryTemplates() {
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'central_notice_templates',
			array( 'template_name', 'template_id' ),
			'',
			__METHOD__,
			array( 'ORDER BY' => 'template_id' )
		);
		
		$templates = array();
		while ( $row = $dbr->fetchObject( $res ) ) {
			array_push( $templates, $row->template_name );
		}
		
		return $templates;
	}

	function listTemplates() {
		$templates = $this->queryTemplates();
		return $this->templatesForm( $templates );
	}
	
	function templatesForm( $templates ) {
		global $wgOut, $wgTitle;
		
		// Templates
		$htmlOut = Xml::openElement( 'form', 
			array( 
				'method' => 'post', 
				'action' => ''
			 )
		);
		$htmlOut .= Xml::fieldset( 'Available Templates' );
		$htmlOut .= Xml::openElement( 'table', array ( 'cellpadding' => 9 ) ) ; 
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-template-name' ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-remove' ) );
		$templates = $this->queryTemplates();
		foreach ( $templates as $templateName ) {
			$htmlOut .= Xml::tags( 'tr', null, 
				Xml::element( 'td', null, $templateName ) .
				Xml::tags( 'td', null, 
					Xml::check( 'removeTemplates[]', false, 
						array( 'value' => $templateName )
			 		)
			 	)
			);
		}
		$htmlOut .= Xml::tags( 'tr', null, 
			Xml::tags( 'td', null, 
				Xml::submitButton( wfMsg( 'centralnotice-modify' ) )
			)
		);
		$htmlOut .= Xml::closeElement( 'table' );
		$htmlOut .= XML::closeElement( 'fieldset' );
		
		// Notices
		$htmlOut .= Xml::openElement( 'form', 
			array(
				'method' => 'post', 
				'action' => SpecialPage::getTitleFor( 'NoticeTemplate' )->getFullUrl()
			)
		);
		$htmlOut .= Xml::openElement( 'fieldset' );
		$htmlOut .= Xml::element( 'legend', null, wfMsg( 'centralnotice-add-template' ) );
		$htmlOut .= Xml::hidden( 'wpMethod', 'addTemplate' );
		$htmlOut .= Xml::tags( 'p', null, 
			Xml::inputLabel(
				wfMsg( 'centralnotice-template-name' ), 
				'templateName', 
				'templateName', 
				25
			)
		);
		$htmlOut .= Xml::tags( 'p', null, 
			Xml::textarea( 'templateBody', '', 60, 20 )
		);
		$htmlOut .= Xml::tags( 'p', null, 
			Xml::submitButton( wfMsg( 'centralnotice-modify' ) ) .
			Xml::submitButton( wfMsg( 'centralnotice-preview' ) )
		);
		$htmlOut .= Xml::closeElement( 'fieldset' );
		$htmlOut .= Xml::closeElement( 'form' );
		
		// Output HTML
		$wgOut->addHtml( $htmlOut );
	}
	
	function listTemplateDetail ( $template ) {
		global $wgOut, $wgUser;
		
		/*
		 * What is this supposed to be?
		 * 
			$form .= 'Preview';
			$form .= 'Template';
			$form .= 'Button';
			$form .= 'Link';
		 */
		
		if ( $wgUser->isAllowed( 'centralnotice-template-edit' ) ) {
			$form .= Xml::tags( 'tr', null,
				Xml::tags( 'td', null,
					Xml::submitButton(
						wfMsgHtml( 'centralnotice-modify' ),
						array(
							'id' => 'centralnoticesubmit',
							'name' => 'centralnoticesubmit'
						)
					)
				)
			);
			$form .= Xml::tags( 'tr', null,
				Xml::tags( 'td', null,
					Xml::submitButton(
						wfMsgHtml( 'centralnotice-preview' ), 
						array(
							'id' => 'centralnoticepreview',
							'name' => 'centralnoticepreview'
						)
					)
				)
			);
		}
		$wgOut->addHTML( $form );
	}
	
	function addTemplate ( $name, $body ) {
		global $wgOut, $egCentralNoticeTables;

		if ( $body == '' || $name == '' ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-null-string' ) );
			return;
		}
		
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'central_notice_templates', 'template_name',
			array( 'template_name' => $name ),
			__METHOD__
		);
		
		if ( $dbr->numRows( $res ) > 0 ) { 
			$wgOut->addHTML( wfMsg( 'centralnotice-template-exists' ) );
			return;
		} else {
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->insert( 'central_notice_templates',
				array( 'template_name' => $name ),
				__METHOD__
			);
			
			/*
			 * Perhaps these should move into the db as blob
			 */
			$article = new Article(
				Title::newFromText( "centralnotice-template-{$name}", NS_MEDIAWIKI )
			);
			$article->doEdit( $body, '' );
			return;
		}
	}

	function removeTemplate ( $templateName ) {
		global $wgOut, $egCentralNoticeTables;

		if ( $templateName == '' ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-template-doesnt-exist' ) );
			return;
		}
		
		$templateId = $this->getTemplateId( $templateName );
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'central_notice_template_assignments', 'template_assignment_id',
			array( 'template_id' => $templateId ),
			__METHOD__
		);
		
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-template-still-bound' ) );
			return;
		} else {
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->delete( 'central_notice_templates',
				array( 'template_id' => $templateId ),
				__METHOD__
			);
		}
	}
	
	function getTemplateId ( $templateName ) {
		global $wgOut, $egCentralNoticeTables;
		
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'central_notice_templates', 'template_id',
			array( 'template_name' => $templateName ),
			__METHOD__
		);
		
		$row = $dbr->fetchObject( $res );
		if( $row ) {
			return $row->template_id;
		}
		return null;
	}
}
