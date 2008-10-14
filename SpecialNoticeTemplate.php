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
		$this->printHeader();


		if ( $wgRequest->wasPosted() ) {
			$body = file_get_contents('php://input');
			$wgOut->addHtml("Body of post: $body");

			$toRemove = $wgRequest->getArray('removeTemplate');
			if ( isset($toRemove) ){  
				foreach ( $toRemove as $template ) {
					$this->removeNotice( $template );
				}
				$this->listNotices();
				return;
			}

			$enabledNotices = $wgRequest->getArray('enabled');
			if ( isset( $enabledNotices ) ) {
				$allNotices = $this->getNoticesName();

				$diff_set = array_diff( $allNotices, $enabledNotices);

				$wgOut->addHtml("<p>diff set is $diff_set[1]");

				foreach ( $enabledNotices as $notice) {
					$this->updateEnabled( $notice, 'Y');
				}
				foreach ( $diff_set as $notice) {
					$this->updateEnabled( $notice, 'N');
				}
			}
		}

		$method = $wgRequest->getVal('method');
		$wgOut->addHtml("got method $method");

		if ( $method == 'addTemplate') { 
			$this->addTemplate();
			return;
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

	private function previewTemplate() {
	}

# Display methods

	private function printHeader() {
		global $wgOut;
		$wgOut->addWikiText(   '[[' . 'Special:CentralNotice/listNotices ' . '|' . wfMsg( 'centralnotice-notices') . ']]' . " | "
				     . '[[' . 'Special:NoticeTemplate/listTemplates' . '|' . wfMsg ( 'centralnotice-templates' ) . ']]' . " | "
				     . '[[' . 'Special:NoticeTranslate/listTranslations' . '|' . wfMsg( 'centralnotice-translate') . ']]' . " | ");
	
	}

	function queryTemplates() {
		$centralnotice_template_table = "central_notice_templates";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_template_table, "name");

		$templates = array();
		while ( $row = $dbr->fetchObject( $res )) {
			array_push($templates, $row->name);
		}
		return $templates;
	}
		
	function listTemplates() {
		$templates = $this->queryTemplates();
		return $this->templatesForm( $templates );
	}

	function templatesForm( $templates ) {
		global $wgOut, $wgTitle;
		
		$table .= Xml::openElement('table', array ( 'id' => 'templates')) . 
		$templates = $this->queryTemplates();
		foreach ( $templates as $templateName ) {
			$table .= "<tr><td>" . $templateName . "</td></tr>";
		}
		$table .= Xml::closeElement( 'table' );

		$wgOut->addHtml( $table );

		$wgOut->addHtml( 
			Xml::openElement( 'form', array(
                                'method' => 'get',
				'action' =>  $this->getTitle( $this->mUserName )->getLocalUrl( 'method=addTemplate' ) )) .
			'<fieldset>' .
		       Xml::element( 'legend', array(), wfMsg( 'centralnotice-add-template' ) ) .
		       Xml::hidden( 'title', $this->getTitle()->getPrefixedText() ) .
		       Xml::hidden( 'wpMethod', $action ) .
		      '<p>' .
		      Xml::inputLabel( wfMsg( 'centralnotice-template-name' ),
			'templateName', 'templateName', 25, $this->mTemplateName) .
		      '</p>' .
		      '<p>' . 
		      Xml::inputLabel( wfMsg( 'centralnotice-template-body'),
		      	'templateBody', 'templateBody', 60, $this->templateBody) .
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

	function addTemplate ( $template ) {
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_templates';

		$eTemplateName = mysql_real_escape_string( $templateName );
		 
		$res = $dbr->select( $centralnotice_table, 'name', "notice_name = '$eTemplateName' " );
		if ( $dbr->numRows( $res ) > 0 ) { 
		 	$wgOut->addHTML( wfMsg( 'centralnotice-template-exists' ) );
			return;
		}
		else {
			 $dbw = wfGetDB( DB_MASTER );
			 $res = $dbw->insert( $centralnotice_table, array( notice_name => "$noticeName"));
			 return;
		}
	}
}
