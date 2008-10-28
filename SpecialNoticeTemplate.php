<?php
	
if ( !defined( 'MEDIAWIKI' ) ) {
        echo "CentralNotice extension\n";
        exit( 1 );
}

class SpecialNoticeTemplate extends SpecialPage { 

        function __construct() {
	                parent::__construct( "NoticeTemplate" );
			wfLoadExtensionMessages('CentralNotice');
	}

	function execute( $sub ) {
		global $wgOut, $wgUser, $wgRequest;
		
		$this->setHeaders();
		$sk = $wgUser->getSkin();

		if ( !$wgUser->isAllowed( 'centralnotice_admin_rights' )) {
			$wgOut->permissionRequired( 'centralnotice_admin_rights' );
			return;
		}
		
		$wgOut->addWikiText( wfMsg( 'centralnotice-summary' ));
		CentralNotice::printHeader();


		if ( $wgRequest->wasPosted() ) {
		//	$body = file_get_contents('php://input');
		//	$wgOut->addHtml("Body of post: $body");

			$toRemove = $wgRequest->getArray('removeTemplates');
			if ( isset($toRemove) ){  
				foreach ( $toRemove as $template ) {
					$this->removeTemplate( $template );
				}
				$this->listTemplates();
				return;
			}

			$enabledNotices = $wgRequest->getArray('enabled');
			if ( isset( $enabledNotices ) ) {
				$allNotices = $this->getNoticesName();

				$diff_set = array_diff( $allNotices, $enabledNotices);

				foreach ( $enabledNotices as $notice) {
					$this->updateEnabled( $notice, 'Y');
				}
				foreach ( $diff_set as $notice) {
					$this->updateEnabled( $notice, 'N');
				}
			}
		}

		$method = $wgRequest->getVal('wpMethod');

		if ( $method == 'addTemplate') { 
			$templateName = $wgRequest->getVal('templateName');
			$templateBody = $wgRequest->getVal('templateBody');
			$this->addTemplate( $templateName, $templateBody);
		}
		if ( $sub == 'listTemplates' ) { 
			$this->listTemplates();
			return;
		}

  	  	$this->listTemplates();
	}

	private function updateEnabled( $update_notice, $enabled) {
		 $centralnotice_table = "central_notice_campaign";
		 $dbw = wfGetDB( DB_MASTER );
		 $res = $dbw->update($centralnotice_table, array( cnc_enabled => $enabled ), array( cnc_template => $update_notice));
	}

	public static function previewTemplate() {
	}


	function queryTemplates() {
		$centralnotice_template_table = "central_notice_templates";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_template_table, "template_name, template_id", '', '', array('ORDER BY' => 'template_id'));

		$templates = array();
		while ( $row = $dbr->fetchObject( $res )) {
			array_push($templates, $row->template_name);
		}
		return $templates;
	}
		
	function listTemplates() {
		$templates = $this->queryTemplates();
		return $this->templatesForm( $templates );
	}

	function templatesForm( $templates ) {
		global $wgOut, $wgTitle;
		
		$table = Xml::openElement( 'form', array(
						'method' => 'post',
						'action' => ''));
		$table .= Xml::fieldset( 'Available Templates' );
		$table .= Xml::openElement( 'table', array ( 'cellpadding' => 9)) ; 
		$table .= "<th>" . wfMsg ( 'centralnotice-template-name' ) . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-remove') . "</th>";

		$templates = $this->queryTemplates();
		foreach ( $templates as $templateName ) {
			$table .= "<tr><td>" . 
					$templateName .
				  "</td>";
			$table .=  "<td>" .
					Xml::check( 'removeTemplates[]', false, array( 'value' => $templateName)) .
				   "</td></tr>";
		}
		$table .= "<tr><td>" . Xml::submitButton( wfMsg( 'centralnotice-modify')) . "</td></tr>"; 
		$table .= Xml::closeElement( 'table' );
		$table .= XML::closeElement( 'fieldset' );

		$wgOut->addHtml( $table );

		$wgOut->addHtml( 
			Xml::openElement( 'form', array(
                                'method' => 'post',
				'action' => SpecialPage::getTitleFor( 'NoticeTemplate' )->getFullUrl() ) ) .
			'<fieldset>' .
		       Xml::element( 'legend', array(), wfMsg( 'centralnotice-add-template' ) ) .
		       Xml::hidden( 'wpMethod', 'addTemplate' ) .
		      '<p>' .
		      Xml::inputLabel( wfMsg( 'centralnotice-template-name' ),
			'templateName', 'templateName', 25) .
		      '</p>' .
		      '<p>' . 
		      Xml::textarea( 'templateBody', '', 60, 20) .
		      '<p>' .
		      Xml::submitButton( wfMsg( 'centralnotice-modify' ) ) .
		      Xml::submitButton( wfMsg( 'centralnotice-preview' ) ) .
		      '</p>' .
		      '</fieldset>' .
	              '</form>' .
		      Xml::closeElement( 'form' )
		    );
	}

	function listTemplateDetail ( $template ) {
		global $wgOut,$wgUser;

		$form  .= "Preview";
		$form  .= "Template";
		$form  .= "Button";
		$form  .= "Link";

		if ( $wgUser->isAllowed( 'centralnotice-template-edit' ) ) {
			$form .= "<tr><td><center>" . Xml::submitButton( wfMsgHtml('centralnotice-modify'),
					array('id' => 'centralnoticesubmit','name' => 'centralnoticesubmit') ) . "</td>";
			$form .= "<tr><td>" . Xml::submitButton( wfMsgHtml('centralnotice-preview'),
					array('id' => 'centralnoticepreview','name' => 'centralnoticepreview') ) . "</center></td>";

		}
	        
		$wgOut->addHTML( $form );
	}

	function addTemplate ( $templateName, $templateBody ) {
		global $wgOut;

		if ( $templateBody == '' || $templateName == '' ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-null-string' ) );
			return;
		}

		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_templates';

		$eTemplateName = htmlspecialchars ( $templateName );
		 
		$res = $dbr->select( $centralnotice_table, 'template_name', "template_name = '$eTemplateName' " );
		if ( $dbr->numRows( $res ) > 0 ) { 
		 	$wgOut->addHTML( wfMsg( 'centralnotice-template-exists' ) );
			return;
		}
		else {
			 $dbw = wfGetDB( DB_MASTER );
			 $res = $dbw->insert( $centralnotice_table, array( 'template_name' => "$templateName"));
			 
			 //perhaps these should move into the db as blob
			 $templatePage = "Centralnotice-" . "template-" . "$templateName";
			 $title = Title::newFromText( $templatePage, NS_MEDIAWIKI );
			 $article = new Article( $title );
			 $article->doEdit( $templateBody, '' );
			 return;
			
		}
	}

	function removeTemplate ( $templateName ) {
		global $wgOut;

		if ( $templateName == '' ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-template-doesnt-exist' ) );
			$return;
		}
		
		$templateId = $this->getTemplateId( $templateName );
		$centralnotice_table = 'central_notice_template_assignments';
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table, 'template_assignment_id', 
							   "template_id =" . $dbr->addQuotes( $templateId ) );
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-template-still-bound' ) );
			$return;
		}
		else {
			$centralnotice_table = 'central_notice_templates';
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->delete( $centralnotice_table, array( "template_id =" . $dbr->addQuotes($templateId)));
		}
	}

	function getTemplateId ( $templateName ) {
		global $wgOut;
		
		$centralnotice_table = 'central_notice_templates';
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table, 'template_id', array( "template_name =" . $dbr->addQuotes( $templateName )));
		$row = $dbr->fetchObject( $res );
		return $row->template_id;
	}
}
