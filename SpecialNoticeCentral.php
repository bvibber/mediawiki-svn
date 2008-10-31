<?php
	
if ( !defined( 'MEDIAWIKI' ) ) {
        echo "CentralNotice extension\n";
        exit( 1 );
}

class CentralNotice extends SpecialPage {
	
	/* Functions */
	
	function CentralNotice() {
		// Register special page
		SpecialPage::SpecialPage( 'CentralNotice' );
		
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
		if ( !$wgUser->isAllowed( 'centralnotice_admin_rights' ) )  {
			$wgOut->permissionRequired( 'centralnotice_admin_rights' );
			return;
		}
		
		// Show summary
		$wgOut->addWikiText( wfMsg( 'centralnotice-summary' ) );
		
		// Show header
		$this->printHeader( $sub );
		
		// Handle form sumissions
		if ( $wgRequest->wasPosted() ) {
			
			// Handle removing
			$toRemove = $wgRequest->getArray( 'removeNotices' );
			if ( isset( $toRemove ) ) {
				// Remove notices in list
				foreach ( $toRemove as $template ) {
					$this->removeNotice( $template );
				}
				
				// Show list of notices
				$this->listNotices();
				return;
			}
			
			// Handle locking/unlocking
			$lockedNotices = $wgRequest->getArray( 'locked' );
			if ( isset( $lockedNotices ) ) {
				// Build list of notices to lock
				$unlockedNotices = array_diff( $this->getNoticesName(), $lockedNotices);
				
				// Set locked/unlocked flag accordingly
				foreach( $lockedNotices as $notice ) {
				     $this->updateLock( $notice, '1' );
				}
				foreach( $unlockedNotices as $notice ) {
				     $this->updateLock( $notice, '0' );
				}
			}
			
			// Handle enabling/disabling
			$enabledNotices = $wgRequest->getArray('enabled');
			if ( isset( $enabledNotices ) ) {
				// Build list of notices to disable
				$disabledNotices = array_diff( $this->getNoticesName(), $enabledNotices);
				
				// Set enabled/disabled flag accordingly
				foreach ( $enabledNotices as $notice) {
					$this->updateEnabled( $notice, '1');
				}
				foreach ( $disabledNotices as $notice) {
					$this->updateEnabled( $notice, '0');
				}
			}
			
			$noticeName = $wgRequest->getVal( 'notice' );
			
			// Handle range setting
			$start = $wgRequest->getArray( 'start' );
			$end = $wgRequest->getArray( 'end' );
			if ( isset( $start ) && isset( $end )) {
				foreach( $start as $noticeName => $date ) {
					$updatedStart = '';
					foreach ( $date as $value) {
						$updatedStart .= $value;
					}
					$updatedStart .= '00';
				}
				foreach( $end as $noticeName => $date ) {
					$updatedEnd = '';
					foreach ( $date as $value) {
						$updatedEnd .= $value;
					}
					$updatedEnd .= '000000';
				}
				$this->updateNoticeDate( $noticeName, $updatedStart, $updatedEnd );
			}
	
			// Handle updates if no post content came through	
			if ( !isset( $lockedNotices )  && $wgRequest->getText( 'change' ) !== 'weight' )  {
               			 $allNotices = $this->getNoticesName();
                			foreach ( $allNotices as $notice ) {
                				$this->updateLock( $notice, '0' );
                			}
            		}
			
			if ( !isset( $enabledNotices ) && $wgRequest->getText( 'change' ) !== 'weight' ) {
				$allNotices = $this->getNoticesName();
				foreach ( $allNotices as $notice ) {
					$this->updateEnabled( $notice, '0' );
				}
			}
			
			// Handle weight change
			$updatedWeights = $wgRequest->getArray( 'weight' );
			if ( isset( $updatedWeights ) ) {
				foreach( $updatedWeights as $templateName => $weight ) {
					$this->updateWeight( $noticeName, $templateName, $weight );
				}
			}
		}
		
		// Handle adding
		$method = $wgRequest->getVal('method');
		$this->showAll = $wgRequest->getVal('showAll');
		if ( $method == 'addNotice' ) { 
			$noticeName       = $wgRequest->getVal( 'noticeName' );
			$start_min        = $wgRequest->getVal( 'start_min' );
			$start_day        = $wgRequest->getVal( 'start_day' );
			$start_month      = $wgRequest->getVal( 'start_month' );
			$start_year       = $wgRequest->getVal( 'start_year' );
			$start_hour       = $wgRequest->getVal( 'start_hour' );
			$project_name     = $wgRequest->getVal( 'project_name' );
			$project_language = $wgRequest->getVal( 'wpUserLanguage' );
			if ( $noticeName == '' ) {
				$wgOut->addHtml( wfMsg ( 'centralnotice-null-string' ) );
			}
			else {
				$this->addNotice( $noticeName, '0', $start_year, $start_month, $start_day, $start_hour, $start_min, $project_name, $project_language );
			}
		}
		
		// Handle removing
		if ( $method == 'removeNotice' ) {
			$noticeName =  $wgRequest->getVal ( 'noticeName' );
			$this->removeNotice ( $noticeName );
		}
		
		// Handle adding of template
		if ( $method == 'addTemplateTo' ) {
			$noticeName = $wgRequest->getVal( 'noticeName' );
			$templateName = $wgRequest->getVal( 'templateName' );
			$this->addTemplateTo( $noticeName, $templateName, 100 );
			$this->listNoticeDetail( $noticeName );
			return;
		}
		
		// Handle removing of template
		if ( $method == 'removeTemplateFor' ) {
			$noticeName = $wgRequest->getVal ( 'noticeName' );
			$templateName = $wgRequest->getVal ( 'templateName ');
			$this->removeTemplateFor( $noticeName , $templateName );
		}
		
		// Handle showing detail
		if ( $method == 'listNoticeDetail' ) { 
			$notice = $wgRequest->getVal ( 'notice' );
			$this->listNoticeDetail( $notice );
			return;
		}
		
		// Show lsit of notices
  	  	$this->listNotices();
	}
	
	// Update the enabled/disabled state of notice
	private function updateEnabled( $notice, $state ) {
		 $dbw = wfGetDB( DB_MASTER );
		 $res = $dbw->update( 'cn_notices',
		 	array( 'not_enabled' => $state ), 
		 	array( 'not_name' => $notice )
		 );
	}
	
	static public function printHeader( $sub ) {
		global $wgOut, $wgTitle;
		
		$pages = array(
			'Special:CentralNotice/listNotices' => wfMsg( 'centralnotice-notices' ),
			'Special:NoticeTemplate/listTemplates' => wfMsg ( 'centralnotice-templates' )
		);
		$htmlOut = Xml::openElement( 'table', array( 'cellpadding' => 9 ) );
		$htmlOut .= Xml::openElement( 'tr' );
		foreach ( $pages as $page => $msg ) {
			$title = Title::newFromText( $page );
			
			$style = array( 'style' => 'border-bottom:solid 1px silver;' );
			if ( dirname( $title->getPrefixedText() ) == $wgTitle->getPrefixedText() ) {
				$style = array( 'style' => 'border-bottom:solid 1px black;' );
			}
			
			$htmlOut .= Xml::tags( 'td', $style,
				Xml::tags( 'a', array( 'href' => $title->getFullURL() ), $msg )
			);
		}
		$htmlOut .= Xml::closeElement( 'tr' );
		$htmlOut .= Xml::closeElement( 'table' );
		
		$wgOut->addHTML( $htmlOut );
	}
	
	function getNoticesName() {
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'cn_notices', 'not_name' );
		$notices = array();
		while ( $row = $dbr->fetchObject( $res )) {		
			array_push( $notices, $row->not_name);
		}
		return $notices;
	}
	
	/*
	 * listNotices
	 * 
	 * Print out all campaigns found in db
	 */

	function listNotices() {
		global $wgOut, $wgRequest, $wgTitle, $wgScript, $wgNoticeLang;
		global $wgNoticeProject, $wpUserLang;
		
		// Get connection
		$dbr = wfGetDB( DB_SLAVE );
		
		/*
		 * This is temporarily hard-coded
		 */
		$this->showAll = 'Y';
		
		// If all languages should be shown
		if ( isset( $this->showAll )) {
			// Get only notices for all languages
			$res = $dbr->select( 'cn_notices',
				array(
					'not_name',
					'not_start',
					'not_end',
					'not_enabled',
					'not_project',
					'not_language',
					'not_locked'
				),
				null,
				__METHOD__,
				array('ORDER BY' => 'not_id')
			);
		} else {
			// Get only notices for this language
			$res = $dbr->select( 'cn_notices',
				array(
					'not_name',
					'not_start',
					'not_end',
					'not_enabled',
					'not_project',
					'not_locked'
				),
				array ( 'not_language' => $wgNoticeLang ),
				__METHOD__,
				array('ORDER BY' => 'not_id')
			);
		}
		
		// Format date and time data
        $years = range( 2007, 2012 );
        $months = range( 1, 12 );
        $months = array_map( array( $this, 'addZero' ), $months );  
        $days = range( 1 , 31 );
        $days = array_map( array( $this, 'addZero' ), $days);
        $hours = range( 0 , 23 );
        $hours = array_map( array( $this, 'addZero' ), $hours);
        $min = range( 0, 59, 15 );
        $min = array_map( array( $this, 'addZero' ), $min);
		
		// Build HTML
		$htmlOut = Xml::openElement( 'form', 
			array( 
				'method' => 'post', 
				'action' => SpecialPage::getTitleFor( 'CentralNotice' )->getFullUrl()
			 )
		);
		$htmlOut .= Xml::fieldset( wfMsgHtml( "centralnotice-manage" ) );
		$htmlOut .= Xml::openElement( 'table', array ( 'cellpadding' => 9 ) );
		
		// Headers
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-notice-name' ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-project-name' ) );
		
		// If all languages should be shown
		if ( isset ( $this->showAll ) ) {
			$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-project-lang' ) );
		}
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-start-date' ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-end-date' ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-enabled' ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-locked' ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-remove' ) );
		
		// Rows
		if ( $dbr->numRows( $res ) >= 1 ) {  
			while ( $row = $dbr->fetchObject( $res ) ) {
				$htmlOut .= Xml::openElement( 'tr' );
			
				// Name
				$urlNotice = $this->getTitle()->getLocalUrl(
					'method=listNoticeDetail&notice=' . $row->not_name
				);
				$htmlOut .= Xml::tags( 'td', null,
					Xml::element( 'a', array( 'href' => $urlNotice ), $row->not_name )
				);
				
				// Project
				$htmlOut .= Xml::tags( 'td', null, $this->getProjectName( $row->not_project ) );
			
				// Language
				if ( isset ( $this->showAll ) ) {
					$htmlOut .= Xml::tags( 'td', null, 
						$row->not_language
				 	);
				}
			
				// Date and time calculations
				$start_timestamp = $row->not_start;
				$start_year = substr( $start_timestamp, 0 , 4 );
				$start_month = substr( $start_timestamp, 4, 2 );
				$start_day = substr( $start_timestamp, 6, 2 );
				$start_hour = substr( $start_timestamp, 8, 2 );
				$start_min = substr( $start_timestamp, 10, 2 );
				$end_timestamp = $row->not_end;
				$end_year = substr( $end_timestamp, 0 , 4 );
				$end_month = substr( $end_timestamp, 4, 2);
				$end_day = substr( $end_timestamp, 6, 2 );
			
				// Start
				$htmlOut .= Xml::element( 'td', null,
					"{$start_year}/{$start_month}/{$start_day} {$start_hour}:{$start_min}"
				);
			
				// End
				$htmlOut .= Xml::element( 'td', null,
					"{$end_year}/{$end_month}/{$end_day}"
				);
			
				// Enabled
				$htmlOut .= Xml::tags( 'td', null,
					Xml::check( 'enabled[]', ( $row->not_enabled == '1' ),
					array( 'value' => $row->not_name) )
				);
			
				// Locked 
				$htmlOut .= Xml::tags( 'td', null,
					Xml::check( 'locked[]', ( $row->not_locked == '1' ),
					array( 'value' => $row->not_name) )
				);
			
				// Remove
				$htmlOut .= Xml::tags( 'td', null,
					Xml::check( 'removeNotices[]', false,
					array( 'value' => $row->not_name) )
				);
			
				$htmlOut .= Xml::closeElement( 'tr' );
			}
			$htmlOut .= Xml::tags( 'tr', null,
				Xml::tags( 'td', null,
					Xml::submitButton( wfMsgHtml('centralnotice-modify'), 
						array(
							'id' => 'centralnoticesubmit',
							'name' => 'centralnoticesubmit'
						)
					)
				)
			);
		
			$htmlOut .= Xml::closeElement( 'table' );
			$htmlOut .= XML::closeElement( 'fieldset' );
			$htmlOut .= XML::closeElement( 'form' );
		
			
		// No notices to show
	        } else {
			$htmlOut = wfMsg('centralnotice-no-notices-exist');		
    	        }

		// Notice Adding
		$htmlOut .= Xml::openElement( 'form',
			array(
				'method' => 'post',
				'action' =>  SpecialPage::getTitleFor( 'CentralNotice' )->getLocalUrl()
			)
		);
		$htmlOut .= Xml::openElement( 'fieldset' );
		$htmlOut .= Xml::element( 'legend', null, wfMsg( 'centralnotice-add-notice' ) );
		$htmlOut .= Xml::hidden( 'title', $this->getTitle()->getPrefixedText() );
		$htmlOut .= Xml::hidden( 'method', 'addNotice' );
	
		$htmlOut .= Xml::openElement( 'table', array ( 'cellpadding' => 9 ) );

		$current_day   = gmdate( 'd' );
		$current_month = gmdate( 'm');
		$current_year  = gmdate( 'o' );

		// Name
		$htmlOut .= Xml::tags( 'tr', null, 
			Xml::tags( 'td', null, wfMsg( 'centralnotice-notice-name' ) ) .
			Xml::tags( 'td', null, Xml::inputLabel( '', 'noticeName',  'noticeName', 25 ) )
		);
		
		// Start Date
		$htmlOut .= Xml::tags( 'tr', null, 
			Xml::tags( 'td', null, Xml::label( wfMsg('centralnotice-start-date' ), 'start-date' ) ) .
			Xml::tags( 'td', null,
				Xml::listDropDown( 'start_month', $this->dropDownList( wfMsg( 'centralnotice-month' ), $months ), '', $current_month, '', 6 ) .
				Xml::listDropDown( 'start_day',  $this->dropDownList( wfMsg( 'centralnotice-day' ), $days ), '', $current_day, '', 7 )  .
				Xml::listDropDown( 'start_year',  $this->dropDownList( wfMsg( 'centralnotice-year' ), $years ), '', $current_year, '', 8 )
			)
		);
		
		// Start Time
		$htmlOut .= Xml::tags( 'tr', null, 
			Xml::tags( 'td', null, wfMsg( 'centralnotice-start-hour' ) . "(GMT)" ) .
			Xml::tags( 'td', null,
				Xml::listDropDown( 'start_hour', $this->dropDownList( wfMsg( 'centralnotice-hours' ), $hours ), '', "00", '', 9 ) .
				Xml::listDropDown( 'start_min', $this->dropDownList( wfMsg( 'centralnotice-min' ), $min ), '', "00", '', 10 )
			)
		);
		
		// Project
		$htmlOut .= Xml::openElement( 'tr' );
		$htmlOut .= Xml::element( 'td', null, wfMsg( 'centralnotice-project-name' ) );
		$htmlOut .= Xml::tags( 'td', null,
			$this->projectDropDownList()
		);
		$htmlOut .= Xml::closeElement( 'tr' );
		
		
		// Language
		list( $lsLabel, $lsSelect) = Xml::languageSelector( $wpUserLang );
		
		/*
		 * Dirty hack to add our very own "All" option
		 */
		// Strip selected flag
		$lsSelect = str_replace( ' selected="selected"', '', $lsSelect );
		
		// Find the first select tag
		$insertPoint = stripos( $lsSelect , '<option' );
		
		// Create our own option
		$option = Xml::element( 'option',
			array(
				'selected' => 'selected',
				'value' => ''
			),
			'All Languages'
		);
		
		// Insert our option
		$lsSelect = substr( $lsSelect, 0, $insertPoint ) . $option . substr( $lsSelect, $insertPoint );
		
		$htmlOut .= Xml::tags( 'tr', null,
			Xml::tags( 'td', null, $lsLabel ) .
			Xml::tags( 'td', null, $lsSelect )
		);
		
		// Submit
		$htmlOut .= Xml::tags( 'tr', null,
			Xml::tags( 'td', array( 'colspan' => 2 ),
				Xml::submitButton( wfMsg( 'centralnotice-modify' ) )
			)
		);
		
		$htmlOut .= Xml::hidden( 'change', 'weight');
		$htmlOut .= Xml::closeElement( 'table' );
		$htmlOut .= Xml::closeElement( 'fieldset' );
		$htmlOut .= Xml::closeElement( 'form' );
		// Output HTML
		$wgOut->addHTML( $htmlOut );
	}
	
	function listNoticeDetail( $notice ) {
		global $wgOut, $wgRequest;
		
		if ( $wgRequest->wasPosted() ) {
			// Handle removing of templates
			$templateToRemove = $wgRequest->getArray( 'removeTemplates' );
			if ( isset( $templateToRemove ) ) {
				foreach ( $templateToRemove as $template ) {
					$this->removeTemplateFor( $notice, $template );
				}
			}
			
			// Handle weight change
			$weights = $wgRequest->getArray( 'weights' );
			if ( isset( $weights ) ) {
				// Do something?
			}
			
			// Handle adding of templates
			$templatesToAdd = $wgRequest->getArray( 'addTemplates' );
			if ( isset( $templatesToAdd ) ) {
				foreach ( $templatesToAdd as $template ) {
					$this->addTemplateTo( $notice, $template, 0 );
				}
			}
		}
		
		$dbr = wfGetDB( DB_SLAVE );
		
		/*
		 * Temporarily hard coded
		 */
		$this->showAll = 'Y';
		
        if ( isset( $this->showAll )) {
            $res = $dbr->select( 'cn_notices',
            	array(
            		'not_id',
            		'not_name',
            		'not_start',
            		'not_end',
            		'not_enabled',
            		'not_project',
            		'not_language',
            		'not_locked'
            	),
				array( 'not_name' => $notice ),
				__METHOD__,
				array( 'ORDER BY' => 'not_id' )
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


		$htmlOut = Xml::openElement( 'form',
			array(
				'method' => 'post',
				'action' => SpecialPage::getTitleFor( 'CentralNotice' )->getLocalUrl()
			)
		);
		
		// Build Html
		$htmlOut .= Xml::fieldset( $notice );
		$htmlOut .= Xml::openElement( 'table', array(  'cellpadding' => 9 ) );

                $htmlOut .= Xml::Element( 'th', null, wfMsg ( 'centralnotice-project-name') );
                $htmlOut .= Xml::Element( 'th', null, wfMsg ( 'centralnotice-project-lang') );
		$htmlOut .= Xml::Element( 'th', null, wfMsg ( 'centralnotice-start-date') );
		$htmlOut .= Xml::Element( 'th', null, wfMsg ( 'centralnotice-end-date') );
		$htmlOut .= Xml::Element( 'th', null, wfMsg ( 'centralnotice-enabled') );
		$htmlOut .= Xml::Element( 'th', null, wfMsg ( 'centralnotice-locked') );
		$htmlOut .= Xml::Element( 'th', null, wfMsg ( 'centralnotice-remove') );
	
		// Rows
		while ( $row = $dbr->fetchObject( $res )) { 	
	
			 // Date and time calculations
			$start_timestamp = $row->not_start;
			$start_year = substr( $start_timestamp, 0 , 4);
			$start_month = substr( $start_timestamp, 4, 2);
			$start_day = substr( $start_timestamp, 6, 2);
			$start_hour = substr( $start_timestamp, 8, 2);
			$start_min = substr( $start_timestamp, 10, 2);

			$end_timestamp = $row->not_end;
			$end_year = substr( $end_timestamp, 0 , 4);
			$end_month = substr( $end_timestamp, 4, 2);
			$end_day = substr( $end_timestamp, 6, 2);

			$htmlOut .= Xml::openElement( 'tr' ) .
			
					// Project
					Xml::tags( 'td', null,
						$this->projectDropDownList()
					) .
				    
					// Language
					Xml::tags( 'td', null, $row->not_language) .
				    
				    // Start
				    Xml::tags( 'td', null, 
			    	    	Xml::listDropDown( "start[$row->not_name][year]",
						$this->dropDownList( wfMsg( 'centralnotice-year'), $years ), '', $start_year, '', 3) .
				   	Xml::listDropDown( "start[$row->not_name][month]", 
					  	$this->dropDownList( wfMsg( 'centralnotice-month'), $months), '', $start_month, '', 4 ) .
    					Xml::listDropDown( "start[$row->not_name][day]",	
						$this->dropDownList( wfMsg( 'centralnotice-day' ), $days ) ,  '', $start_day, '', 5) .
				    	Xml::listDropDown( "start[$row->not_name][hour]", 
						$this->dropDownList( wfMsg( 'centralnotice-hours'), $hours), '', $start_hour, '', 6) .
				        Xml::listDropDown( "start[$row->not_name][min]",
						$this->dropDownList( wfMsg( 'centralnotice-min'), $min), '', $start_min, '', 7)
			            ) .

				    // End
				    Xml::tags( 'td', null,
					Xml::listDropDown( "end[$row->not_name][year]", 
							$this->dropDownList( wfMsg( 'centralnotice-year'), $years ), '', $end_year, '', 8) .
					Xml::listDropDown( "end[$row->not_name][month]", 
							$this->dropDownList( wfMsg( 'centralnotice-month'), $months ), '', $end_month, '', 9 ) .
				   	Xml::listDropDown( "end[$row->not_name][day]", 
							$this->dropDownList( wfMsg( 'centralnotice-day'), $days ), '', $end_day, '', 10 )
				    );

			// Enabled
		        $htmlOut .= Xml::tags( 'td', null, 
				Xml::check( 'enabled[]', ( $row->not_enabled == '1' ),
				array( 'value' => $row->not_name))
			);
			
			// Locked
			$htmlOut .= Xml::tags( 'td', null, 
				Xml::check( 'locked[]', ( $row->not_locked == '1' ), 
				array( 'value' => $row->not_name))
			);

			// Remove
			$htmlOut .= Xml::tags( 'td', null,
				Xml::check( 'removeNotices[]', false, 
				array( 'value' => $row->not_name))
			);		
			$htmlOut .= Xml::closeElement( 'tr' );
		}

		// Submit
		$htmlOut .= Xml::tags( 'tr', null, 
			Xml::tags('td', array( 'colspan' => 2),
					Xml::submitButton( wfMsg( 'centralnotice-modify' ) ) 
				)
		);
		$htmlOut .= Xml::closeElement( 'table' );
		$htmlOut .= XMl::closeElement( 'fieldset' );
		$htmlOut .= Xml::closeElement( 'form' );

		// Output HTML
		$wgOut->addHtml( $htmlOut ) ;
		
		$res = $dbr->select(
			array(
				'cn_notices',
				'cn_assignments',
				'cn_templates'
			),
			array(
				'cn_templates.tmp_name',
				'cn_assignments.tmp_weight'
			),
			array(
				'cn_notices.not_name' => $notice,
				'cn_notices.not_id = cn_assignments.not_id',
				'cn_assignments.tmp_id = cn_templates.tmp_id'
			), 
			__METHOD__,
			array('ORDER BY' => 'cn_notices.not_id')
		);
		if ( $dbr->numRows( $res ) < 1) {
			$wgOut->addHtml( wfMsg ("centralnotice-no-templates-assigned") );
			$wgOut->addHtml( $this->addTemplatesForm() );
			return;
		}

		// Build Assigned Template HTML
		$htmlOut = Xml::openElement( 'form',
			array(
				'method' => 'post',
				'action' => '' 
			)
		);
		$htmlOut .= Xml::hidden( 'change', 'weight' );
		$htmlOut .= Xml::openElement( 'fieldset', null, $notice );
		$htmlOut .= Xml::openElement( 'table', array( 'cellpadding' => 9) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( "centralnotice-templates" ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( "centralnotice-weight" ) );
		$htmlOut .= Xml::element( 'th', null, wfMsg ( "centralnotice-remove" ) );

		// Rows
		while ( $row = $dbr->fetchObject( $res )) {
	
			// Name
			$htmlOut .=  Xml::openElement( 'tr' ) .
				     Xml::tags( 'td', null,	
				     	Xml::label( $row->tmp_name, 'name') 
			);
	
			// Weight
			$htmlOut .= Xml::tags( 'td', null,
					 Xml::listDropDown( "weight[$row->tmp_name]",
						 $this->dropDownList( wfMsg( 'centralnotice-weight' ),
						 range ( 0, 100, 5) ),
						 '',
						 $row->tmp_weight,
						 '',
						 1)
			);
	
			// Remove
			$htmlOut .= Xml::tags( 'td', null, 
					Xml::check( 'removeTemplates[]', false, array( 'value' => $row->tmp_name)) 
			);
		}
		$htmlOut .= Xml::tags( 'tr', null, 
				Xml::tags( 'td', array( 'collspan' => 2),
					Xml::submitButton( wfMsg( 'centralnotice-modify') )
				)
		);
		$htmlOut .= Xml::closeElement( 'table');
		$htmlOut .= Xml::closeElement( 'fieldset' );
		$htmlOut .= Xml::closeElement( 'form' );
		$wgOut->addHTML( $htmlOut );
		$wgOut->addHTML( $this->addTemplatesForm() );
	}

	function addTemplatesForm() {
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'cn_templates', 'tmp_name', '', '', array( 'ORDER BY' => 'tmp_id' ) );
	
		if ( $dbr->numRows( $res ) > 0 ) {
			// Build HTML	
			$htmlOut = Xml::openElement( 'form',
				array(
					'method' => 'post',
					'action' => '',
				)
			);		
			$htmlOut .= Xml::fieldset( wfMsg( "centralnotice-available-templates") );
			$htmlOut .= Xml::openElement( 'table', array( 'cellpadding' => 9 ) );
	
			$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-template-name') );
			$htmlOut .= Xml::element( 'th', null, wfMsg ( 'centralnotice-add' ) );

			// Build rows
			while ( $row = $dbr->fetchObject( $res )) { 
				$htmlOut .= Xml::openElement( 'tr' );
	
				// Name
				$htmlOut .= Xml::tags( 'td', null, Xml::label( $row->tmp_name, 'tmp_name' ));
		
				// Add
				$htmlOut .= Xml::tags( 'td', null, 
						Xml::check( 'addTemplates[]', '', array ( 'value' => $row->tmp_name)) 
				   	    );
				$htmlOut .= Xml::closeElement( 'tr' );
			}

			// Submit
			$htmlOut .= Xml::tags( 'tr', null, 
		 		Xml::tags( 'td', array( 'collspan' => 2), 
					Xml::submitButton( wfMsgHtml('centralnotice-modify'))
				)
			);

			$htmlOut .= Xml::closeElement( 'table' );
			$htmlOut .= Xml::closeElement( 'fieldset' );
			$htmlOut .= Xml::closeElement( 'form' );
		} else {
			$htmlOut = Xml::element( 'p' );
			$htmlOut .= Xml::element( 'a', 
						array( 'href' =>  SpecialPage::getTitleFor( 'NoticeTemplate' )->getFullUrl()),
							wfMsg( 'centralnotice-no-templates' )
			   	    );
				    
		}
		return $htmlOut;
	}
	
	
	/** 
	 * Lookup function for active notice under a given language and project
	 * Returns an id for the running notice
	 */
	function selectNoticeTemplates( $project, $language ) {
		$dbr = wfGetDB( DB_SLAVE );
		$encTimestamp = $dbr->addQuotes( $dbr->timestamp() );
		$res = $dbr->select(
			array(
				'cn_notices',
				'cn_assignments',
				'cn_templates',
			),
			array(
				'tmp_name',
				'SUM(tmp_weight) AS total_weight'
			),
			array (
				"not_start <= $encTimestamp",
				"not_end >= $encTimestamp",
				"not_enabled = 1",
				"not_language" => array( '', $language ),
				"not_project" => array( '', $project ),
				'cn_notices.not_id=cn_assignments.not_id',
				'cn_assignments.tmp_id=cn_templates.tmp_id',
			),
			__METHOD__,
			array(
				'GROUP BY' => 'tmp_name',
			)
		);
		$templateWeights = array();
		foreach( $res as $row ) {
			$name = $row->tmp_name;
			$weight = intval( $row->total_weight );
			$templateWeights[$name] = $weight;
		}
		return $templateWeights;
	}

	function addNotice( $noticeName, $enabled, $start_year, $start_month, $start_day, $start_hour, $start_min, $project_name, $project_language ) { 
		global $wgOut;
		
		$dbr = wfGetDB( DB_SLAVE );
		$start_hour = substr( $start_hour, 0 , 2 );
		$res = $dbr->select( 'cn_notices', 'not_name', array( 'not_name' => $noticeName ) );
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHTML( wfMsg( 'centralnotice-notice-exists' ) );
			return;
		}
		else {
			$dbw = wfGetDB( DB_MASTER );
			$start = wfTimeStamp( TS_MW, "{$start_year}:{$start_month}:{$start_day} {$start_hour}:00:00");
			if ( $start_month == 12 ) {
				$end_month = '01'; 
				$end_year = ($start_year + 1);
			} elseif ( $start_month == '09' ) {
				$end_month = '10';
				$end_year = $start_year;
			} else {
				$end_month = ( substr( $start_month, 0, 1) ) == 0 ? 0 . ( intval( $start_month ) + 1 ) : ( $start_month + 1 );  
				$end_year = $start_year;
			}
			$end = wfTimeStamp( TS_MW, "{$end_year}:{$end_month}:{$start_day} {$start_hour}:00:00" );
			
			$res = $dbr->select( 'cn_notices', 'not_name',
				array (
					"not_start >= '{$start}'",
					"not_end <= '{$end}'",
					'not_project' => $project_name,
					'not_language' => $project_language
				)
			);
			if ( $dbr->numRows( $res ) > 0 ) {
				$wgOut->addHtml( wfMsg( 'centralnotice-overlap' ) );
			} else {
				$res = $dbw->insert( 'cn_notices', 
					array( 'not_name' => $noticeName,
						'not_enabled' => $enabled,
						'not_start' => $start,
						'not_end' => $end,
						'not_project' => $project_name,
						'not_language' => $project_language
					)
				);
			}
			return;
		}
	}

	function removeNotice ( $noticeName ) {
		global $wgOut;
		$dbr = wfGetDB( DB_SLAVE );
		
		$res = $dbr->select( 'cn_notices', 'not_name, not_locked',
			array( 'not_name' => $noticeName )
		);
		if ( $dbr->numRows( $res ) < 1 ) {
			 $wgOut->addHTML( wfMsg( 'centralnotice-notice-doesnt-exist' ) );
			 return; 
		}
		$row = $dbr->fetchObject( $res );
		if ( $row->not_locked == '1' ) {
			 $wgOut->addHTML( wfMsg( 'centralnotice-notice-is-locked' ) );
			 return; 
		} else {
			 $dbw = wfGetDB( DB_MASTER );
			 $noticeId = htmlspecialchars($this->getNoticeId( $noticeName ) );
			 $res = $dbw->delete( 'cn_assignments',  array ( 'not_id' => $noticeId ) ); 
			 $res = $dbw->delete( 'cn_notices', array ( 'not_name' => $noticeName ) );
			 return;
		}
	}
	
	function addTemplateTo( $noticeName, $templateName, $weight ) {
		global $wgOut;
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$eNoticeName = htmlspecialchars ( $noticeName );
		$noticeId = $this->getNoticeId( $eNoticeName );
		$templateId = $this->getTemplateId( $templateName );
		$res = $dbr->select( 'cn_assignments', 'asn_id', 
			array(
				'tmp_id' => $templateId,
				'not_id' => $noticeId
			)
		);
		if ( $dbr->numRows( $res ) > 0 ) {
			$wgOut->addHTML( wfMsg( 'centralnotice-template-already-exists' ) ); 	
		} else {
			$dbw = wfGetDB( DB_MASTER );
			$noticeId = $this->getNoticeId( $eNoticeName );
			$res = $dbw->insert('cn_assignments',
				array(
					'tmp_id' => $templateId,
					'tmp_weight' => $weight,
					'not_id' => $noticeId
				)
			);
		}
	}

	function getNoticeId ( $noticeName ) {
		 $dbr = wfGetDB( DB_SLAVE );
		 $eNoticeName = htmlspecialchars( $noticeName );
		 $res = $dbr->select( 'cn_notices', 'not_id', array( 'not_name' => $eNoticeName ) );
		 $row = $dbr->fetchObject( $res );
		 return $row->not_id;
	}

	function getNoticeLanguage ( $noticeName ) {
		 $dbr = wfGetDB( DB_SLAVE );
		 $eNoticeName = htmlspecialchars( $noticeName );
		 $res = $dbr->select( 'cn_notices', 'not_language', array( 'not_name' => $eNoticeName ) );
		 $row = $dbr->fetchObject( $res );
		 return $row->not_language;
	}

	function getNoticeProjectName ( $noticeName ) {
		 $dbr = wfGetDB( DB_SLAVE );
		 $eNoticeName = htmlspecialchars( $noticeName );
		 $res = $dbr->select( 'cn_notices', 'not_project', array( 'not_name' => $eNoticeName ) );
		 $row = $dbr->fetchObject( $res );
		 return $row->not_project;
	}

	function getTemplateId ( $templateName ) {
		$dbr = wfGetDB( DB_SLAVE );
		$templateName = htmlspecialchars ( $templateName );
		$res = $dbr->select( 'cn_templates', 'tmp_id', array( 'tmp_name' => $templateName ) );
		$row = $dbr->fetchObject( $res );
		return $row->tmp_id;
	}
	
	function removeTemplateFor( $noticeName, $templateName) {
		global $wgOut;
		
		$dbw = wfGetDB( DB_MASTER );
		$noticeId = $this->getNoticeId( $noticeName );
		$templateId = $this->getTemplateId( $templateName );
		$res = $dbw->delete( 'cn_assignments', array ( 'tmp_id' => $templateId, 'not_id' => $noticeId ) );
	}

	function updateNoticeDate ( $noticeName, $start, $end ) {
		global $wgOut;
		
		$dbr = wfGetDB( DB_SLAVE );
		$project_name = $this->getNoticeProjectname( $noticeName );
		$project_language = $this->getNoticeLanguage( $noticeName );
		
		// Start / end dont line up
		if ( $start > $end || $end < $start) {
			 $wgOut->addHtml( wfMsg( 'centralnotice-invalid-date-range3' ) );
			 return;
		}
		
		// Invalid notice name
		$res = $dbr->select( 'cn_notices', 'not_name', array( 'not_name' => $noticeName ) );
		if ( $dbr->numRows( $res ) < 1 ) {
			$wgOut->addHTML( wfMsg( 'centralnotice-doesnt-exist' ) );
		}
		
		// Overlap over a date within the same project and language
		$startDate = $dbr->timestamp( $start );
		$endDate = $dbr->timestamp( $end );
		$res = $dbr->select( 'cn_notices', 'not_id', 
			array(
				'not_language' => $project_language,
				'not_project' => $project_name,
				"not_end <= {$endDate}",
				"not_start >= {$startDate}"
			)
		);
		if ( $dbr->numRows( $res ) > 1 ) {
			$wgOut->addHtml( wfMsg( 'centralnotice-overlap-with-existing-notice' ) );
			return;
		} else {
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->update( 'cn_notices', 
				array(
					'not_start' => $start,
					'not_end' => $end
				),
				array( 'not_name' => $noticeName )
			);
		}
	}
	
	function updateLock ( $noticeName, $isLocked ) {
		global $wgOut;
		
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'cn_notices', 'not_name',
			array( 'not_name' => $noticeName )
		);
		if ( $dbr->numRows( $res ) < 1 ) {
			$wgOut->addHTML( wfMsg( 'centralnotice-doesnt-exist') );
		} else {
			$dbw = wfGetDB( DB_MASTER );
			$res = $dbw->update( 'cn_notices',
				array( 'not_locked' => $isLocked ),
				array( 'not_name' => $noticeName )
			);
		}
	}	
	
	function updateWeight ( $noticeName, $templateName, $weight ) {
		 $dbw = wfGetDB( DB_MASTER );
		 $noticeId = $this->getNoticeId( $noticeName );
  		 $templateId = $this->getTemplateId( $templateName );
		 $res = $dbw->update( 'cn_assignments',
		 	array ( 'tmp_weight' => $weight ),
		 	array(
				'tmp_id' => $templateId,
				'not_id' => $noticeId
			)
		);
	}
	
	function projectDropDownList() {
		global $wgNoticeProjects;
		
		$htmlOut = Xml::openElement( 'select', array( 'name' => 'project_name' ) );
		$htmlOut .= Xml::element( 'option', array( 'value' => '' ), 'All projects' );
		foreach( $wgNoticeProjects as $value ) {
			$htmlOut .= Xml::element( 'option', array( 'value' => $value ), $value );
		}
		$htmlOut .= Xml::closeElement( 'select' );
		return $htmlOut;
	}
	
	function getProjectName( $value ) {
		return $value; // @fixme -- use wfMsg()
	}
	
	function dropDownList ( $text, $values ) {
		$dropDown = "* {$text}\n";
		foreach( $values as $value ) {
			$dropDown .= "**{$value}\n";
		}
		return $dropDown;
	}
	
	function addZero ( $text ) {
		// Prepend a 0 for text needing it
		if ( strlen( $text ) == 1 ) {
			$text = "0{$text}";
		}
		return $text; 
	}
}
