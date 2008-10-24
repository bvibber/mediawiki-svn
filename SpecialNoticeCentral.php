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
			//$body = file_get_contents('php://input');
			//$wgOut->addHtml("Body of post: $body");

			$toRemove = $wgRequest->getArray('removeNotices');
			if ( isset($toRemove) ){  
				foreach ( $toRemove as $template ) {
					$this->removeNotice( $template );
				}
				$this->listNotices();
				return;
			}

			$lockedNotices = $wgRequest->getArray('locked');
			if ( isset( $lockedNotices ) ) {
				$allNotices = $this->getNoticesName();
				$diff_set = array_diff( $allNotices, $lockedNotices);
				
				foreach( $lockedNotices as $notice ) {
				     $this->updateLock( $notice, 'Y' );
				}
				
				foreach( $diff_set as $notice ) {
				     $this->updateLock( $notice, 'N' );
				}
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
			$start_date = $wgRequest->getArray('start_date');
			if ( isset( $start_date ) ) {
				foreach( $start_date as $noticeName => $date_value ) {
					$updatedStartDate = '';
					foreach ( $date_value as $date_portion => $value) {
						$updatedStartDate .= $value;
					}
					$updatedStartDate .= "00";
					$this->updateStartDate( $noticeName, $updatedStartDate);
				}		
			}
			$end_date = $wgRequest->getArray('end_date');
			if ( isset( $end_date ) ) {
				foreach( $end_date as $noticeName => $date_value ) {
					$updatedEndDate = '';
					foreach ( $date_value as $date_portion => $value) {
						$updatedEndDate .= $value;
					}
					$updatedEndDate .= "000000";
					$this->updateEndDate( $noticeName, $updatedEndDate);
				}		
			}
			$noticeName = $wgRequest->getVal('notice');
			$updatedWeights = $wgRequest->getArray('weight');
			if ( isset( $updatedWeights ) ) {
				foreach( $updatedWeights as $templateName => $weight) {
					$this->updateWeight( $noticeName, $templateName, $weight);
				}	
			}
		}

		$method = $wgRequest->getVal('method');
		$this->showAll = $wgRequest->getVal('showAll');

		if ( $method == 'addNotice' ) { 
			$noticeName       = $wgRequest->getVal('noticeName');
			$start_min        = $wgRequest->getVal('start_min');
			$start_day        = $wgRequest->getVal('start_day');
			$start_month      = $wgRequest->getVal('start_month');
			$start_year       = $wgRequest->getVal('start_year');
			$start_hour       = $wgRequest->getVal('start_hour');
			$project_name     = $wgRequest->getVal('project_name');
			$project_language = $wgRequest->getVal('wpUserLanguage');
			if ( $noticeName == '') {
				$wgOut->addHtml("Can't add a null string");
			}
			else {
				$this->addNotice( $noticeName, 'N', $start_year, $start_month, $start_day, $start_hour, $start_min, $project_name, $project_language );
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
		 $res = $dbw->update($centralnotice_table, array( 'notice_enabled' => $enabled ), array( 'notice_name' => $update_notice));
	}

	static public function printHeader() {
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
			array_push( $notices, $row->notice_name);
		}
		return $notices;
	}

	####
	# listNotices
	###
	# Print out all campaigns found in db
	###

	function listNotices() {
		global $wgOut,$wgRequest,$wgTitle,$wgScript,$wgNoticeLang;

		$centralnotice_table = "central_notice_campaign";
		$dbr = wfGetDB( DB_SLAVE );
		$this->showAll = 'Y'; //temp
		if ( isset( $this->showAll )) {
			$res = $dbr->select( $centralnotice_table,
					     "notice_name, notice_start_date, notice_end_date, notice_enabled, notice_project, notice_language, notice_locked", 
					     '',
					     '',
					     array('ORDER BY' => 'notice_id'),
					     ''
					    );
		}
		else { //show only notices for this language
			$res = $dbr->select( $centralnotice_table, 
	     				     "notice_name, notice_start_date, notice_end_date, notice_enabled, notice_project, notice_locked",
				             array ( "notice_language = '$wgNoticeLang'"),
				             '',
					     array('ORDER BY' => 'notice_id'),
					     ''
				 	   );
		}
		
		$years = range( 2007, 2012);
		$months = range( 1, 12 );
		$months = array_map( array( $this, 'addZero'), $months );  
		$days = range( 1 , 31);
		$days = array_map( array( $this, 'addZero'), $days);
		$hours = range( 0 , 23);
		$hours = array_map( array( $this, 'addZero'), $hours);
		$min = range( 0, 59, 15);
		$min = array_map( array( $this, 'addZero'), $min);

		$table  = "<form name='centranoticeform' id='centralnoticeform' method='post'>"; 
		$table .= "<fieldset><legend>" . wfMsgHtml( "centralnotice-manage" ) . "</legend>";
		$table .= "<table cellpadding=\"9\">";
		$table .= "<tr><th colspan = \"9\"></th></tr>"; 
		$table .= "<th>" . wfMsg ( 'centralnotice-notice-name') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-project-name') . "</th>";
		if ( isset ( $this->showAll ) ) 
			$table .=  "<th>" . wfMsg ( 'centralnotice-project-lang') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-start-date') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-end-date') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-enabled') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-locked') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-remove') . "</th>";
		while ( $row = $dbr->fetchObject( $res )) {		
			$table .= "<tr><td><a href=\"" . $this->getTitle()->getLocalUrl("method=listNoticeDetail&notice=$row->notice_name") . "\">$row->notice_name</a></td>";
			$table .= "<td>$row->notice_project</td>";
		        if ( isset ( $this->showAll )) 
				$table .=  "<td>" . $row->notice_language . "</td>";
			
			$start_timestamp = $row->notice_start_date;
			$start_year = substr( $start_timestamp, 0 , 4);
			$start_month = substr( $start_timestamp, 4, 2);
			$start_day = substr( $start_timestamp, 6, 2);
			$start_hour = substr( $start_timestamp, 8, 2);
			$start_min = substr( $start_timestamp, 10, 2);

			$end_timestamp = $row->notice_end_date;
			$end_year = substr( $end_timestamp, 0 , 4);
			$end_month = substr( $end_timestamp, 4, 2);
			$end_day = substr( $end_timestamp, 6, 2);

			$table .= "<td>" . Xml::listDropDown( "start_date[$row->notice_name][year]", $this->dropDownList( wfMsg( 'centralnotice-year'), $years ), '', $start_year, '', 3) 
					 . Xml::listDropDown( "start_date[$row->notice_name][month]", $this->dropDownList( wfMsg( 'centralnotice-month'), $months), '', $start_month, '', 4 ) 
			                 . Xml::listDropDown( "start_date[$row->notice_name][day]",  $this->dropDownList( wfMsg( 'centralnotice-day' ), $days ) ,  '', $start_day, '', 5)
					 . Xml::listDropDown( "start_date[$row->notice_name][hour]", $this->dropDownList( wfMsg( 'centralnotice-hours'), $hours), '', $start_hour, '', 6)
					 . Xml::listDropDown( "start_date[$row->notice_name][min]", $this->dropDownList( wfMsg( 'centralnotice-min'), $min), '', $start_min, '', 7)
					 . "</td>";
			$table .= "<td>" . Xml::listDropDown( "end_date[$row->notice_name][year]", $this->dropDownList( wfMsg( 'centralnotice-year'), $years ), '', $end_year, '', 8) 
					 . Xml::listDropDown( "end_date[$row->notice_name][month]", $this->dropDownList( wfMsg( 'centralnotice-month'), $months ), '', $end_month, '', 9 ) 
					 . Xml::listDropDown( "end_date[$row->notice_name][day]", $this->dropDownList( wfMsg( 'centralnotice-day'), $days ), '', $end_day, '', 10 )
					 . "</td>";
			$enabled = ( $row->notice_enabled == 'Y' ) ? true : false; 
			$table .= "<td>" . Xml::check( 'enabled[]', $enabled, array ( 'value' => $row->notice_name)) . "</td>";
			// lock down to certain users
			$locked = ( $row->notice_locked == 'Y' ) ? true : false; 
			$table .= "<td>" . Xml::check( 'locked[]', $locked, array ( 'value' => $row->notice_name)) . "</td>";
			$table .= "<td>" . Xml::check( 'removeNotices[]', false, array( 'value' => $row->notice_name)) . "</td>";
		}
		$table .= "<tr><td>" . Xml::submitButton( wfMsgHtml('centralnotice-modify'), 
						array('id' => 'centralnoticesubmit','name' => 'centralnoticesubmit') ) . "</td></tr>";
		$table .= "</table></fieldset></form>";
		$wgOut->addHTML( $table);
		
		$current_day   = gmdate( 'd' );
		$current_month = gmdate( 'm');
		$current_year  = gmdate( 'o' );
		$action = "addNotice";

		global $wgNoticeProject,$wpUserLang;

		list( $lsLabel, $lsSelect) = Xml::languageSelector( $wpUserLang );
		$languagebar = $this->tableRow( $lsLabel, $lsSelect) ;

		$wgOut->addHtml( 
			Xml::openElement( 'form', array(
                                'method' => 'post',
				'action' =>  SpecialPage::getTitleFor( 'CentralNotice' )->getLocalUrl())) .
			'<fieldset>' .
		       Xml::element( 'legend', array(), wfMsg( 'centralnotice-add-notice' ) ) .
		       Xml::hidden( 'title', $this->getTitle()->getPrefixedText() ) .
		       Xml::hidden( 'method', $action ) .
		      '<p>' .
		      Xml::inputLabel( wfMsg( 'centralnotice-notice-name' ),
			'noticeName', 'noticeName', 25) .
		      " " . Xml::label( wfMsg('centralnotice-start-date'), 'start-date') . ": " .
		      Xml::listDropDown( 'start_month', $this->dropDownList( wfMsg( 'centralnotice-month'), $months ), '', $current_month, '', 6 ) .
		      Xml::listDropDown( 'start_day',  $this->dropDownList( wfMsg( 'centralnotice-day'), $days ), '', $current_day, '', 7 )  .
		      Xml::listDropDown( 'start_year',  $this->dropDownList( wfMsg( 'centralnotice-year'), $years ), '', $current_year, '', 8) .
		      " " . wfMsg( 'centralnotice-start-hour' ) . "(GMT)" . ": " .
		      Xml::listDropDown( 'start_hour', $this->dropDownList( wfMsg( 'centralnotice-hours'), $hours), '', "00", '', 9) .
		      Xml::listDropDown( 'start_min', $this->dropDownList( wfMsg( 'centralnotice-min'), $min), '', "00", '', 10) . "<p>" .
		      " " . wfMsg( 'centralnotice-project-name' ) . ": " . 
		      Xml::listDropDown( 'project_name', wfMsg( 'centralnotice-project-name-list'), '', $wgNoticeProject, '', 11) .
		      " " . wfMsg( 'centralnotice-project-lang') . ":" . $languagebar .	 
		      '</p>' .
		      '<p>' .
		      Xml::submitButton( wfMsg( 'centralnotice-modify' ) ) .
		      '</p>' .
		      '</fieldset>' .
	              '</form>'
		    );
	}

	function tableRow( $td1, $td2 ) { 
                $td3 = ''; 
                $td1 = Xml::tags( 'td', array( 'class' => 'pref-label' ), $td1 );
                $td2 = Xml::tags( 'td', array( 'class' => 'pref-input' ), $td2 );
                return Xml::tags( 'tr', null, $td1 . $td2 ). $td3 . "\n";
        }

	function listNoticeDetail( $notice ) {
		global $wgOut,$wgRequest;

		$eNotice = htmlspecialchars( $notice );

		if ($wgRequest->wasPosted()) {
			$templateToRemove = $wgRequest->getArray('removeTemplates');
			if (isset( $templateToRemove )) {
				foreach ($templateToRemove as $template) {
					$this->removeTemplateFor( $eNotice, $template);
				}
			}
			$weights = $wgRequest->getArray('weights');
			if (isset( $weights )) {
			}	
			$templatesToAdd = $wgRequest->getArray('addTemplates');
			if (isset( $templatesToAdd )) {
				foreach ($templatesToAdd as $template) {
					$this->addTemplateTo( $notice, $template, 0);
				}
			}
				
		}

		$centralnotice_table = "central_notice_campaign";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( array ( $centralnotice_table,
					     "central_notice_template_assignments",
					     "central_notice_templates"),
				     "template_name,template_weight",
		                     array ( 'notice_name' => $eNotice, 
					     'campaign_id = notice_id',
					     'central_notice_template_assignments.template_id = central_notice_templates.template_id'), 
				     '',
				     array('ORDER BY' => 'notice_id'), 
				     ''
				   );
		if ( $dbr->numRows( $res ) < 1) {
			$wgOut->addHtml( wfMsg ("centralnotice-no-templates"));
			$wgOut->addHtml( $this->addTemplatesForm());
			return;
		}
		$table .= "<form name='centranoticeform' id='centralnoticeform' action=\"$action\" method='post'>";
		$table .= '<fieldset><legend>' . $eNotice . "</legend>";
		$table .= "<table cellpadding=\"9\">";
	        $table .= "<tr><th colspan = \"3\"></th></tr>";
		$table .= "<th>" . wfMsg ( "centralnotice-templates" ) . "</th>";
		$table .= "<th>" . wfMsg ( "centralnotice-weight" ) . "</th>";
		$table .= "<th>" . wfMsg ( "centralnotice-remove" ) . "</th></tr>";
		while ( $row = $dbr->fetchObject( $res )) {
			$table .= "<tr><td>" . Xml::label($row->template_name, 'name') . "</td>";
			$table .= "<td>" . Xml::listDropDown( "weight[$row->template_name]", $this->dropDownList( wfMsg( 'centralnotice-weight' ), range ( 0, 100, 5) ), '', $row->template_weight, '', 1) . "</td>";
			$table .= "<td>" . Xml::check( 'removeTemplates[]', false, array( 'value' => $row->template_name)) . "</td></tr>"; 
		}
		$table .= "<tr><td>" . Xml::submitButton( wfMsg( 'centralnotice-modify') ) . "</td></tr>";
		$table .= "</table></fieldset></form>";
		$wgOut->addHTML( $table );
		$wgOut->addHTML( $this->addTemplatesForm() );
	}

	function addTemplatesForm() {
		$centralnotice_table = 'central_notice_templates';
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table,
				     "template_name",
				     '',
				     '',
				     array('ORDER BY' => 'template_id'));
		$table = "<form name='centranoticeform' id='centralnoticeform' action=\"$action\" method='post'>";
		$table .= '<fieldset><legend>' . wfMsg( "centralnotice-available-templates") . '</legend>';
		$table .= "<table cellpadding=\"9\">"; 
		$table .= "<tr><th colspan = \"2\"></th></tr>";
		$table .= "<th>" . wfMsg ( 'centralnotice-template-name') . "</th>";
		$table .= "<th>" . wfMsg ( 'centralnotice-add' ) .  "</th>";
		while ( $row = $dbr->fetchObject( $res )) { 
			$table .= "<tr><td>" . $row->template_name . "</td>";
			$table .= "<td>" . Xml::check( 'addTemplates[]', '', array ( 'value' => $row->template_name)) . "</td></tr>";
		}
		$table .= "<tr><td>" . Xml::submitButton( wfMsgHtml('centralnotice-modify')) . "</td></tr>";
		$table .= "</table></fieldset></form>";
		return $table;
	}

	public 	function getTemplatesForNotice ( $noticeName ) {
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_template_assignments';

		$eNoticeName = mysql_real_escape_string( $noticeName ) ;
		$res = $dbr->select( array ( 
					$centralnotice_table, "central_notice_campaign","central_notice_templates"),
					"template_name,template_weight",
					array ( 
						'notice_name' => $eNoticeName,
						 'campaign_id = notice_id'), 
					'',
					array(
						'ORDER BY' => 'notice_id'), 
					''
					);
		$templates = array();
		while ( $row = $dbr->fetchObject( $res )) {
			$templates[$row->name] = $row->weight;
		}
		return $templates;

	}

	function addNotice( $noticeName, $enabled, $start_year, $start_month, $start_day, $start_hour, $start_min, $project_name, $project_language ) { 
		global $wgOut;
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_campaign';
		$start_hour = substr( $start_hour, 0 , 2 );
		
		$eNoticeName = mysql_real_escape_string( $noticeName ) ;
		$res = $dbr->select( $centralnotice_table, 'notice_name', "notice_name = '$eNoticeName' " );
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHTML( wfMsg( 'centralnotice-notice-exists' ) );
			return;
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$start_date = wfTimeStamp( TS_MW, $start_year . ":" .
							  $start_month . ":" .
							  $start_day . " " . 
							  $start_hour . ":" .
							  $start_min . ":00");
			if ( $start_month == 12) {
				$end_month = '01'; 
				$end_year = ($start_year + 1);
			}
			elseif ( $start_month == '09' ) {
				$end_month = '10';
				$end_year = $start_year;
			}	
			else {
				$end_month = (substr ( $start_month, 0, 1)) == 0 ? 0 . (intval($start_month) + 1) : ($start_month + 1);  
				$end_year = $start_year;
			}
			$end_date = wfTimeStamp( TS_MW,  $end_year . ":" . 
							 $end_month . ":" .
							 $start_day . " " .
							 $start_hour . ":" .
							 "00" . ":00");

			$res = $dbr->select( $centralnotice_table, 'notice_name',
								 array ( "notice_start_date >= '$start_date'",
									 "notice_end_date <= '$end_date'",
									 "notice_project = '$project_name'",
									 "notice_language = '$project_language'") );
			if ( $dbr->numRows( $res ) > 0 ) {
				$wgOut->addHtml( wfMsg( 'centralnotice-overlap'));
			}
			else {	
				$res = $dbw->insert( $centralnotice_table, 
							array( 'notice_name' => "$noticeName",
							       'notice_enabled' => "$enabled",
							       'notice_start_date' => "$start_date",
							       'notice_end_date' => "$end_date",
							       'notice_project' => $project_name,
     							       'notice_language' => $project_language));
			}
			return;
		}
	}

	function removeNotice ( $noticeName ) {
		global $wgOut;
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_campaign';

		$eNoticeName = mysql_real_escape_string( $noticeName ) ;
		$res = $dbr->select( $centralnotice_table, 'notice_name, notice_locked', "notice_name = '$eNoticeName' " );
		if ( $dbr->numRows( $res ) < 1 ) {
			 $wgOut->addHTML( wfMsg( 'centralnotice-notice-doesnt-exist' ) );
			 return; 
		}
		$row = $dbr->fetchObject( $res );
		if ( $row->notice_locked == 'Y' ) {
			 $wgOut->addHTML( wfMsg( 'centralnotice-notice-is-locked' ) );
			 return; 
		}
		else {
			 $dbw = wfGetDB( DB_MASTER );
			 $noticeId = htmlspecialchars($this->getNoticeId( $noticeName ));
			 $res = $dbw->delete( "central_notice_template_assignments",  array ( 'campaign_id' => $noticeId)); 
			 $res = $dbw->delete( $centralnotice_table, array ( 'notice_name' => "$noticeName"));
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

		$noticeId = $this->getNoticeId( $noticeName );
		$templateId = $this->getTemplateId( $templateName );
		$res = $dbr->select( $centralnotice_table, 'template_assignment_id', array( template_id => $templateId, campaign_id => $noticeId));
		if ( $dbr->numRows( $res ) > 0) {
			$wgOut->addHTML( wfMsg( 'centralnotice-template-already-exists' ) ); 	
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$noticeId = $this->getNoticeId( $eNoticeName );
			$res = $dbw->insert($centralnotice_table, array( 'template_id' => $templateId, 'template_weight' => $eWeight, 'campaign_id' => $noticeId));
		}
	}

	function getNoticeId ( $noticeName ) {
		 $dbr = wfGetDB( DB_SLAVE );
		 $centralnotice_table = 'central_notice_campaign';
		 $eNoticeName = htmlspecialchars( $noticeName );
		 $res = $dbr->select( $centralnotice_table, 'notice_id', "notice_name = '$eNoticeName'");
		 $row = $dbr->fetchObject( $res );
		 return $row->notice_id;
	}

	function getTemplateId ( $templateName ) {
		$dbr = wfGetDB( DB_SLAVE );
		$centralnotice_table = 'central_notice_templates';
		$templateName = htmlspecialchars ( $templateName );
		$res = $dbr->select( $centralnotice_table, 'template_id', "template_name = '$templateName'");
		$row = $dbr->fetchObject( $res );
		return $row->template_id;
	}

	function removeTemplateFor( $noticeName, $templateName) {
		global $wgOut;
		$centralnotice_table = "central_notice_template_assignments";
		$dbw = wfGetDB( DB_MASTER );
		$noticeId = $this->getNoticeId( $noticeName );
		$templateId = $this->getTemplateId( $templateName );
		$res = $dbw->delete( $centralnotice_table, array ( 'template_id' => "$templateId", 'campaign_id' => $noticeId));
	}

	function updateNotice ( $noticeName, $startDate, $endDate , $enabled) {
		$centralnotice_table = "central_notice_template_assignments";
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table, notice_name, "notice_name = '$noticeName'" );
		if ($dbr->numRows( $res ) < 1) {
			$wgOut->addHTML( wfMsg( 'centralnotice-doesnt-exist'));
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->update( $centralnotice_table, 
						array( 'notice_start_date' => $startDate,
						       'notice_end' => $endDate,
						       ' notice_enabled' => $enabled),
						"notice_name = '$noticeName'");
		}
	}
	
	function updateStartDate ( $noticeName, $startDate ) {
		global $wgOut;
		$centralnotice_table = 'central_notice_campaign';
   	        $dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table, 'notice_id', 
							    array( "notice_name =" . $dbr->addQuotes( $noticeName ),
								   "notice_end_date <" . $dbr->addQuotes( $dbr->timestamp($startDate))));
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-invalid-date-range'  ));
			return;
		}
   	        $dbw = wfGetDB( DB_MASTER );
		$res = $dbw->update( $centralnotice_table, array( 'notice_start_date' => $startDate), array( 'notice_name' => $noticeName));
	}
	
	
	function updateEndDate ( $noticeName, $endDate ) {
		global $wgOut;
		$centralnotice_table = 'central_notice_campaign';
   	        $dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table, 'notice_id', 
							    array( "notice_name =" . $dbr->addQuotes( $noticeName ),
								   "notice_start_date >" . $dbr->addQuotes( $dbr->timestamp($endDate))));
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-invalid-date-range'  ));
			return;
		}
   	        $dbw = wfGetDB( DB_MASTER );
		$res = $dbw->update( $centralnotice_table, array( 'notice_end_date' => $endDate), array( 'notice_name' => $noticeName));
	}

	function updateLock ( $noticeName, $isLocked ) {
		global $wgOut;
		$centralnotice_table = 'central_notice_campaign';
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( $centralnotice_table, 'notice_name', "notice_name = '$noticeName'" );
		if ($dbr->numRows( $res ) < 1) {
			$wgOut->addHTML( wfMsg( 'centralnotice-doesnt-exist'));
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->update( $centralnotice_table, array( 'notice_locked' => $isLocked ), array( 'notice_name' => $noticeName));
		}
	}	
	
	function updateWeight ( $noticeName, $templateName, $weight ) {
		 $centralnotice_table = 'central_notice_template_assignments';
		 $dbw = wfGetDB( DB_MASTER );
		 $noticeId = $this->getNoticeId( $noticeName );
  		 $templateId = $this->getTemplateId( $templateName );
		 $res = $dbw->update( $centralnotice_table, array ( 'template_weight' => $weight ), array( 'template_id' => $templateId, 'campaign_id' => $noticeId));
	}
	
	function dropDownList ( $text, $values ) {
		$dropDown = "* $text\n";
		foreach( $values as $element ) {
			$dropDown .= "**$element\n";
		}
		return $dropDown;
	}

	function addZero ( $text ) {
		if ( strlen( $text ) == 1 )  // append a 0 for text needing it
			$text = 0 . $text;
		return $text; 
	}
}
