<?php
	
if ( !defined( 'MEDIAWIKI' ) ) {
        echo "CentralNotice extension\n";
        exit( 1 );
}

class CentralNotice extends SpecialPage
{ 
	function CentralNotice() {
		SpecialPage::SpecialPage("CentralNotice");
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
		$wgOut->addHtml("<p>got method $method");
		$wgOut->addHtml("<p>got sub $sub");

		if ( $method == 'addNotice' ) { 
			$noticeName = $wgRequest->getVal ('noticeName');
			if ( $noticeName == '') {
				$wgOut->addHtml("Can't add a null string");
			}
			else {
				$this->addNotice( $noticeName );
			}
		}
		if ( $method == 'removeNotice' ) {
			$noticeName =  $wgRequest->getVal ('noticeName');
			$this->removeNotice ( $noticeName );
		}
		if ( $method == 'addTemplateTo') {
			$noticeName = $wgRequest->getVal('noticeName');
			$templateName = $wgRequest->getVal('templateName');
			$this->addTemplateTo($noticeName, $templateName, 0);
			$this->listNoticeDetail( $noticeName );
			return;
		}
		if ( $method == 'removeTemplateFor') {
			$noticeName = $wgRequest->getVal ( 'noticeName');
			$templateName = $wgRequest->getVal ( 'templateName ');
			$this->removeTemplateFor( $noticeName , $templateName );
		}
		if ( $method == 'listNoticeDetail') { 
			$notice = $wgRequest->getVal ( 'notice' );
			$this->listNoticeDetail( $notice );
			return;
		}

  	  	$this->listNotices();
	}

	private function updateEnabled( $update_notice, $enabled) {
		 $centralnotice_table = "central_notice_campaign";
		 $dbw = wfGetDB( DB_MASTER );
		 $res = $dbw->update($centralnotice_table, array( notice_enabled => $enabled ), array( notice_name => $update_notice));
	}

	private function printHeader() {
		global $wgOut;
		$wgOut->addWikiText(   '[[' . 'Special:CentralNotice/listNotices ' . '|' . wfMsg( 'centralnotice-notices') . ']]' . " | "
				     . '[[' . 'Special:NoticeTemplate/listTemplates' . '|' . wfMsg ( 'centralnotice-templates' ) . ']]' . " | "
				     . '[[' . 'Special:NoticeTranslate/listTranslations' . '|' . wfMsg( 'centralnotice-translate') . ']]' . " | ");
	
	}

	function getNoticesName() {
		$centralnotice_table = "central_notice_campaign";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table,"notice_name" );
		$notices = array();
		while ( $row = $dbr->fetchObject( $res )) {		
			array_push( $notices, $row->notice_names);
		}
		return $notices;
	}


	####
	# listNotices
	###
	# Print out all campaigns found in db
	###

	function listNotices() {
		global $wgOut,$wgRequest,$wgTitle,$wgScript;

		$centralnotice_table = "central_notice_campaign";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table, 
				     "notice_name, notice_start_date, notice_end_date, notice_enabled",
				     '',
				     '',
				      array('ORDER BY' => 'id'),
				     ''
				     );
  		$table .= "<form name='centranoticeform' id='centralnoticeform' action=\"$action\" method='post'>"; 
		$table .= "<fieldset><legend>" . wfMsgHtml( "centralnotice-manage" ) . "</legend>";
		$table .= "<table cellpadding=\"9\">"; 
		$table .= "<tr><th colspan = \"4\"><br></th></tr>"; 
		$table .= "<th>" . wfMsg ( 'centralnotice-notice-name') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-start-date') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-end-date') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-enabled') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-remove') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-metrics') . "</th>";
		while ( $row = $dbr->fetchObject( $res )) {		
			#$table .= "<tr><td><a href=\"" . SpecialPage::getTitleFor( 'CentralNotice' )->getLocalUrl() . "&method=listCampaignDetail&campaign=$row->notice_name" . "\">$row->notice_name</a></td>";
			$table .= "<tr><td><a href=\"" . $this->getTitle()->getLocalUrl("method=listNoticeDetail&campaign=$row->notice_name") . "\">$row->notice_name</a></td>";
			$table .= "<td>$row->notice_start_date</td>";
			$table .= "<td>$row->notice_end_date</td>";
			$enabled = ( $row->notice_enabled == 'Y' ) ? true : false; 
			$table .= "<td>" . Xml::check( 'enabled[]', $enabled, array ( 'value' => $row->notice_name)) . "</td>";
			$table .= "<td>" . Xml::check( 'removeNotices[]', false, array( 'value' => $row->notice_name)) . "</td>";
		}
		$table .= "<tr><td>" . Xml::submitButton( wfMsgHtml('centralnotice-modify'), 
						array('id' => 'centralnoticesubmit','name' => 'centralnoticesubmit') ) . "</td></tr>";
		// Need to add a preview button
		$table .= "</table></fieldset></form>";
		$wgOut->addHTML( $table);
		
		$action = "addNotice";
		$wgOut->addHtml( 
			Xml::openElement( 'form', array(
                                'method' => 'post',
				//'action' =>  $this->getTitle( $this->mUserName )->getLocalUrl( /* 'method=addNotice' */ ) )) .
				'action' =>  SpecialPage::getTitleFor( 'CentralNotice' )->getLocalUrl())) .
			'<fieldset>' .
		       Xml::element( 'legend', array(), wfMsg( 'centralnotice-add-notice' ) ) .
		       Xml::hidden( 'title', $this->getTitle()->getPrefixedText() ) .
		       Xml::hidden( 'method', $action ) .
		      '<p>' .
		      Xml::inputLabel( wfMsg( 'centralnotice-notice-name' ),
			'noticeName', 'noticeName', 25, $this->mNoticeName) .
		      '</p>' .
		      '<p>' .
		      Xml::submitButton( wfMsg( 'centralnotice-modify' ) ) .
		      '</p>' .
		      '</fieldset>' .
	              '</form>'
		    );
	}

	function listNoticeDetail( $notice ) {
		global $wgOut,$wgRequest;

		$eNotice = htmlspecialchars( $notice );

		if ($wgRequest->wasPosted()) {
			$templateToRemove = $wgRequest->getVal('removeTemplates');
			if (isset($templateToRemove)) {
				foreach ($templateToRemove as $template) {
					$this->removeTemplateFor( $eNotice, $template);
				}
			}
				
		}

		$centralnotice_table = "central_notice_campaign";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( array ( $centralnotice_table,"central_notice_template_assignments"),
				     "name,weight",
		                     array ( 'notice_name' => $eNotice, 'campaign_id = id'), 
				     '',
				     array('ORDER BY' => 'id'), 
				     ''
				   );

		$table .= Xml::openElement( 'form', array(
				'method' => 'post',
				'action' =>  SpecialPage::getTitleFor( 'CentralNotice/listNoticeDetail' )->getLocalUrl()));
		$table .= '<fieldset>';
		$table .= Xml::element( 'legend', array() , $eNotice);
		$table .= "<table cellpadding=\"9\">";
	        $table .= "<tr><th colspan = \"3\"></th></tr>";
		$table .= "<th>" . wfMsg ( "centralnotice-templates" ) . "</th>";
		$table .= "<th>" . wfMsg ( "centralnotice-weight" ) . "</th>";
		$table .= "<th>" . wfMsg ( "centralnotice-remove" ) . "</th></tr>";
		while ( $row = $dbr->fetchObject( $res )) {
			$table .= "<tr><td>" . Xml::label($row->name, 'name') . "</td>";
			$table .= "<td>" . Xml::input( 'weight', '', $row->weight) . "</td>";
			$table .= "<td>" . Xml::check( 'removeNotices[]', false) . "</td>"; 
		}
		$table .= "</tr>";
		$table .= Xml::submitButton( wfMsg( 'centralnotice-update-weights' ) ) .
		$table .= "</table></fieldset>";
		$table .= Xml::closeElement( 'form');
		$wgOut->addHTML( $table );

		$wgOut->addHtml( 
			Xml::openElement( 'form', array(
                                'method' => 'post',
				'action' =>  SpecialPage::getTitleFor( 'CentralNotice' )->getLocalUrl( "method=addTemplateTo&noticeName=$eNotice&template=$templateName"))) .
			'<fieldset>' .
		       Xml::element( 'legend', array(), wfMsg( 'centralnotice-add-template' ) ) .
		       Xml::hidden( 'title', $this->getTitle()->getPrefixedText() ) .
		       Xml::hidden( 'method', $action ) .
		      '<p>' .
		      Xml::inputLabel( wfMsg( 'centralnotice-template-name' ),
			'templateName', 'templateName', 25, $this->mTemplateName) .
		      '</p>' .
		      '<p>' .
		      Xml::submitButton( wfMsg( 'centralnotice-modify' ) ) .
		      '</p>' .
		      '</fieldset>' .
	              '</form>'
		    ); 
	}

	function getTemplatesForNotice ( $noticeName ) {
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_template_assignments';

		$eNoticeName = mysql_real_escape_string( $noticeName ) ;
		$res = $dbr->select( array ( $centralnotice_table,"central_notice_template_assignments"),
					     "name,weight",
					     array ( 'notice_name' => $eNoticeName, 'campaign_id = id'), 
					     '',
					     array('ORDER BY' => 'id'), 
					     ''
					   );

	}

	function addNotice( $noticeName ) { 
		global $wgOut;
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_campaign';
		
		$eNoticeName = mysql_real_escape_string( $noticeName ) ;
		$res = $dbr->select( $centralnotice_table, 'notice_name', "notice_name = '$eNoticeName' " );
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHTML( wfMsg( 'centralnotice-notice-exists' ) );
			return;
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->insert( $centralnotice_table, array( notice_name => "$noticeName"));
			return;
		}
	}

	function removeNotice ( $noticeName ) {
		global $wgOut;
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_campaign';

		$eNoticeName = mysql_real_escape_string( $noticeName ) ;
		$res = $dbr->select( $centralnotice_table, 'notice_name', "notice_name = '$eNoticeName' " );
		if ( $dbr->numRows( $res ) < 1 ) {
			 $wgOut->addHTML( wfMsg( 'centralnotice-notice-doesnt-exist' ) );
			 return; 
		}
		else {
			 $dbw = wfGetDB( DB_MASTER );
			 $res = $dbw->delete( $centralnotice_table, array ( notice_name => "$noticeName"));
			 return;
		}
	}

	function addTemplateTo( $noticeName, $templateName, $weight) {
		global $wgOut;
		$centralnotice_table = "central_notice_template_assignments";

		$dbr = wfGetDB( DB_SLAVE );
		$eNoticeName = mysql_real_escape_string( $noticeName );
		$eTemplateName = mysql_real_escape_string( $templateName );
		$eWeight = mysql_real_escape_string ( $weight );
		$res = $dbr->select( $centralnotice_table, 'name', "name = '$eTemplateName'");
		if ( $dbr->numRows( $res ) > 0) {
			$wgOut->addHTML( wfMsg( 'centralnotice-notice-template-already-exist' ) ); 	
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$noticeId = htmlspecialchars($this->getNoticeId( $eNoticeName ));
			$res = $dbw->insert($centralnotice_table, array( name => $eTemplateName, weight => $eWeight, campaign_id => $noticeId));
		}
	}

	function getNoticeId ( $noticeName ) {
		 $dbr = wfGetDB( DB_SLAVE );
		 $centralnotice_table = 'central_notice_campaign';
		 $eNoticeName = mysql_real_escape_string( $noticeName );
		 $res = $dbr->select( $centralnotice_table, 'id', "notice_name = '$eNoticeName'");
		 $row = $dbr->fetchObject( $res );
		 return $row->id;
	}

	function removeTemplateFor( $noticeName, $templateName) {
		$centralnotice_table = "central_notice_template_assignments";
		$dbw = wfGetDB( DB_MASTER );
		$noticeId = htmlspecialchars($this->getNoticeId( $noticeName ));
		$eTemplateName = mysql_real_escape_string( $templateName );
		$res = $dbw->delete( $centralnotice_table, array ( name => "$eTemplateName", campaign_id => $noticeId));
	}
}
