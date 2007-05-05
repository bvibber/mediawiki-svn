<?php

/**
 * Not quite ready for production use yet; need to fix up the restricted mode,
 * and provide for preservation across delete/undelete of images.
 */

function wfSpecialRevisiondelete( $par = null ) {
	global $wgOut, $wgRequest;
	
	$target = $wgRequest->getText( 'target' );
	// Handle our many different possible input types
	$oldid = $wgRequest->getArray( 'oldid' );
	$arid = $wgRequest->getArray( 'arid' );
	$logid = $wgRequest->getArray( 'logid' );
	$image = $wgRequest->getArray( 'oldimage' );
	$fileid = $wgRequest->getArray( 'fileid' );
	
	$file = $wgRequest->getVal( 'file' ); // For reviewing deleted files
		
	// We need a target page (possible a dummy like User:#1)
	$page = Title::newFromUrl( $target, false );
	if( is_null( $page ) ) {
		$wgOut->showErrorPage( 'notargettitle', 'notargettext' );
		return;
	}
	
	// Only one target set at a time please!
	$inputs = !is_null($file) + !is_null($oldid) + !is_null($logid) + !is_null($arid) + !is_null($fileid) + !is_null($image);
	if( $inputs > 1 || $inputs==0 ) {
		$wgOut->showErrorPage( 'revdelete-nooldid-title', 'revdelete-nooldid-text' );
		return;
	}
	
	$form = new RevisionDeleteForm( $page, $oldid, $logid, $arid, $fileid, $image, $file );
	if( $wgRequest->wasPosted() ) {
		$form->submit( $wgRequest );
	} else if( $oldid || $arid ) {
		$form->showRevs( $wgRequest );
	} else if( $logid ) {
		$form->showEvents( $wgRequest );
	} else if( $fileid || $image ) {
		$form->showImages( $wgRequest );
	}
}

/**
 * Implements the GUI for Revision Deletion.
 * @addtogroup SpecialPage
 */
class RevisionDeleteForm {
	/**
	 * @param Title $page
	 * @param array $oldids
	 * @param array $logids
	 * @param array $arids
	 * @param array $fileids
	 * @param array $oldimages
     * @param string $file
	 */
	function __construct( $page, $oldids=null, $logids=null, $arids=null, $fileids=null, $oldimages=null, $file=null ) {
		global $wgUser;

		$this->page = $page;
		$this->skin = $wgUser->getSkin();
		
		// For reviewing deleted files
		if ( $file ) {
			$oimage = new OldImage( $this->page, $file );
			// Check if user is allowed to see this file
			if ( !$oimage->userCan(Image::DELETED_FILE) ) {
				$wgOut->permissionRequired( 'hiderevision' ); 
				return false;
			} else {
				// Format for hidden images is <timestamp>!<key>
				list($ts,$key) = explode('!',$file);
				return $this->showFile( $key );
			}
		}
		// At this point, we should only have one of these
		if( $oldids ) {
			$this->revisions = $oldids;
			$hide_content_name = array( 'revdelete-hide-text', 'wpHideText', Revision::DELETED_TEXT );
			$this->deletetype='oldid';
		} else if( $arids ) {
			$this->archrevs = $arids;
			$hide_content_name = array( 'revdelete-hide-text', 'wpHideText', Revision::DELETED_TEXT );
			$this->deletetype='arid';
		} else if( $oldimages ) {
			$this->ofiles = $oldimages;
			$hide_content_name = array( 'revdelete-hide-image', 'wpHideImage', Image::DELETED_FILE );
			$this->deletetype='oldimage';
		} else if( $fileids ) {
			$this->afiles = $fileids;
			$hide_content_name = array( 'revdelete-hide-image', 'wpHideImage', Image::DELETED_FILE );
			$this->deletetype='fileid';
		} else if( $logids ) {
			$this->events = $logids;
			$hide_content_name = array( 'revdelete-hide-name', 'wpHideName', LogViewer::DELETED_ACTION );
			$this->deletetype='logid';
		}
		// Our checkbox messages depends one what we are doing
		$this->checks = array(
			$hide_content_name,
			array( 'revdelete-hide-comment', 'wpHideComment', Revision::DELETED_COMMENT ),
			array( 'revdelete-hide-user', 'wpHideUser', Revision::DELETED_USER ),
			array( 'revdelete-hide-restricted', 'wpHideRestricted', Revision::DELETED_RESTRICTED ) );
	}
	
	/**
	 * Show a deleted file version requested by the visitor.
	 */
	function showFile( $key ) {
		global $wgOut, $wgRequest;
		$wgOut->disable();
		
		# We mustn't allow the output to be Squid cached, otherwise
		# if an admin previews a deleted image, and it's cached, then
		# a user without appropriate permissions can toddle off and
		# nab the image, and Squid will serve it
		$wgRequest->response()->header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', 0 ) . ' GMT' );
		$wgRequest->response()->header( 'Cache-Control: no-cache, no-store, max-age=0, must-revalidate' );
		$wgRequest->response()->header( 'Pragma: no-cache' );
		
		$store = FileStore::get( 'hidden' );
		$store->stream( $key );
	}
	
	/**
	 * This sets any fields that are true to a bitfield to true on a given bitfield
	 * @param $bitfield, running bitfield
	 * @param $nbitfield, new bitfiled
	 */	
	function setBitfield( $bitfield, $nbitfield ) {
		return $bitfield | $nbitfield;
	}
	
	/**
	 * This lets a user set restrictions for live and archived revisions
	 * @param WebRequest $request
	 */
	function showRevs( $request ) {
		global $wgOut, $wgUser, $action;

		$UserAllowed = true;
		
		$count = ($this->deletetype=='oldid') ? count($this->revisions) : count($this->archrevs);
		$wgOut->addWikiText( wfMsgExt( 'revdelete-selected', array('parsemag'), $this->page->getPrefixedText(), $count ) );
		
		$bitfields = 0;
		$wgOut->addHtml( "<ul>" );
		// Live revisions...
		if( $this->deletetype=='oldid') {
			foreach( $this->revisions as $revid ) {
				$rev = Revision::newFromTitle( $this->page, $revid );
				// Hiding top revisison is bad
				if( !is_object($rev) || $rev->isCurrent() ) {
					$wgOut->showErrorPage( 'revdelete-nooldid-title', 'revdelete-nooldid-text' );
					return;
				} else if( !$rev->userCan(Revision::DELETED_RESTRICTED) ) {
				// If a rev is hidden from sysops
					if( $action != 'submit') {
						$wgOut->permissionRequired( 'hiderevision' ); 
						return;
					}
					$UserAllowed=false;
				}
				$wgOut->addHtml( $this->historyLine( $rev ) );
				$bitfields = $this->setBitfield( $bitfields, $rev->mDeleted );
			}
		// The archives...
		} else {
			$archive = new PageArchive( $this->page );
			foreach( $this->archrevs as $revid ) {
    			$rev = $archive->getRevision('', $revid );
				if( !is_object($rev) ) {
					$wgOut->showErrorPage( 'revdelete-nooldid-title', 'revdelete-nooldid-text' );
					return;
				} else if( !$rev->userCan(Revision::DELETED_RESTRICTED) ) {
				//if a rev is hidden from sysops
					if( $action != 'submit') {
						$wgOut->permissionRequired( 'hiderevision' ); 
						return;
					}
					$UserAllowed=false;
				}
				$wgOut->addHtml( $this->historyLine( $rev ) );
				$bitfields = $this->setBitfield( $bitfields, $rev->mDeleted );
			}
		} 
		$wgOut->addHtml( "</ul>" );
		
		$wgOut->addWikiText( wfMsgHtml( 'revdelete-text' ) );
		//Normal sysops can always see what they did, but can't always change it
		if( !$UserAllowed ) return;
		
		$items = array(
			wfInputLabel( wfMsgHtml( 'revdelete-log' ), 'wpReason', 'wpReason', 60 ),
			wfSubmitButton( wfMsgHtml( 'revdelete-submit' ) ) );
		$hidden = array(
			wfHidden( 'wpEditToken', $wgUser->editToken() ),
			wfHidden( 'target', $this->page->getPrefixedText() ),
			wfHidden( 'type', $this->deletetype ) );
		if( $this->deletetype=='oldid' ) {
			foreach( $this->revisions as $revid )
				$hidden[] = wfHidden( 'oldid[]', $revid );
		} else {	
			foreach( $this->archrevs as $revid )
				$hidden[] = wfHidden( 'arid[]', $revid );
		}
		$special = SpecialPage::getTitleFor( 'Revisiondelete' );
		$wgOut->addHtml( wfElement( 'form', array(
			'method' => 'post',
			'action' => $special->getLocalUrl( 'action=submit' ) ),
			null ) );
		
		$wgOut->addHtml( '<fieldset><legend>' . wfMsgHtml( 'revdelete-legend' ) . '</legend>' );
		// FIXME: all items checked for just one rev are checked, even if not set for the others
		foreach( $this->checks as $item ) {
			list( $message, $name, $field ) = $item;
			$wgOut->addHtml( "<div>" .
				wfCheckLabel( wfMsgHtml( $message), $name, $name, $bitfields & $field ) .
				"</div>\n" );
		}
		$wgOut->addHtml( '</fieldset>' );
		foreach( $items as $item ) {
			$wgOut->addHtml( '<p>' . $item . '</p>' );
		}
		foreach( $hidden as $item ) {
			$wgOut->addHtml( $item );
		}
		
		$wgOut->addHtml( '</form>' );
	}

	/**
	 * This lets a user set restrictions for archived images
	 * @param WebRequest $request
	 */
	function showImages( $request ) {
		global $wgOut, $wgUser, $action;

		$UserAllowed = true;
		
		$count = ($this->deletetype=='oldimage') ? count($this->ofiles) : count($this->afiles);
		$wgOut->addWikiText( wfMsgExt( 'revdelete-selected', array('parsemag'), $this->page->getPrefixedText(), $count ) );
		
		$bitfields = 0;
		$wgOut->addHtml( "<ul>" );
		// Live revisions...
		if( $this->deletetype=='oldimage' ) {
			foreach( $this->ofiles as $name ) {
				// Our image may be hidden, if so it's name is formated as <time>!<key>
				// Otherwise, it will be <time>!<image> and the URL only needs to pass the time
				$archivename = ( strpos($name,'!')==false ) ? $name.'!'.$this->page->getDbKey() : $name;
				$oimage = new OldImage( $this->page, $archivename );
				if( !isset( $oimage->mName ) ) {
					$wgOut->showErrorPage( 'revdelete-nooldid-title', 'revdelete-nooldid-text' );
					return;
				} else if( !$oimage->userCan(Revision::DELETED_RESTRICTED) ) {
					// If a rev is hidden from sysops
					if( $action != 'submit') {
						$wgOut->permissionRequired( 'hiderevision' ); 
						return;
					}
					$UserAllowed=false;
				}
				$wgOut->addHtml( $this->uploadLine( $oimage ) );
				$bitfields = $this->setBitfield( $bitfields, $oimage->mDeleted );
			}	
		// Archived files...		
		} else {
			foreach( $this->afiles as $fileid ) {
				$file = new ArchivedFile( $this->page, $fileid );
				if( !isset( $file->mId ) ) {
					$wgOut->showErrorPage( 'revdelete-nooldid-title', 'revdelete-nooldid-text' );
					return;
				} else if( !$file->userCan(Revision::DELETED_RESTRICTED) ) {
				// If a rev is hidden from sysops
					if( $action != 'submit') {
						$wgOut->permissionRequired( 'hiderevision' );
						return;
					}
					$UserAllowed=false;
				}
				$wgOut->addHtml( $this->uploadLine( $file ) );
				$bitfields = $this->setBitfield( $bitfields, $file->mDeleted );
			}
		}
		$wgOut->addHtml( "</ul>" );
		
		$wgOut->addWikiText( wfMsgHtml( 'revdelete-text' ) );
		//Normal sysops can always see what they did, but can't always change it
		if( !$UserAllowed ) return;
		
		$items = array(
			wfInputLabel( wfMsgHtml( 'revdelete-log' ), 'wpReason', 'wpReason', 60 ),
			wfSubmitButton( wfMsgHtml( 'revdelete-submit' ) ) );
		$hidden = array(
			wfHidden( 'wpEditToken', $wgUser->editToken() ),
			wfHidden( 'target', $this->page->getPrefixedText() ),
			wfHidden( 'type', $this->deletetype ) );
		if( $this->deletetype=='oldimage' ) {
			foreach( $this->ofiles as $filename )
				$hidden[] = wfHidden( 'oldimage[]', $filename );
		} else {
			foreach( $this->afiles as $fileid )
				$hidden[] = wfHidden( 'fileid[]', $fileid );
		}
		$special = SpecialPage::getTitleFor( 'Revisiondelete' );
		$wgOut->addHtml( wfElement( 'form', array(
			'method' => 'post',
			'action' => $special->getLocalUrl( 'action=submit' ) ),
			null ) );
		
		$wgOut->addHtml( '<fieldset><legend>' . wfMsgHtml( 'revdelete-legend' ) . '</legend>' );
		// FIXME: all items checked for just one file are checked, even if not set for the others
		foreach( $this->checks as $item ) {
			list( $message, $name, $field ) = $item;
			$wgOut->addHtml( '<div>' .
				wfCheckLabel( wfMsgHtml( $message), $name, $name, $bitfields & $field ) .
				'</div>' );
		}
		$wgOut->addHtml( '</fieldset>' );
		foreach( $items as $item ) {
			$wgOut->addHtml( '<p>' . $item . '</p>' );
		}
		foreach( $hidden as $item ) {
			$wgOut->addHtml( $item );
		}
		
		$wgOut->addHtml( '</form>' );
	}
		
	/**
	 * This lets a user set restrictions for log items
	 * @param WebRequest $request
	 */
	function showEvents( $request ) {
		global $wgOut, $wgUser, $action;

		$UserAllowed = true;
		$wgOut->addWikiText( wfMsgExt( 'logdelete-selected', array('parsemag'), $this->page->getPrefixedText(), count($this->events) ) );
		
		$bitfields = 0;
		$wgOut->addHtml( "<ul>" );
		foreach( $this->events as $logid ) {
			$log = new LogViewer( $wgRequest );
			$event = LogReader::newFromTitle( $this->page, $logid );
			// Don't hide from oversight log!!!
			if( !isset( $event ) || $event->log_type=='oversight' ) {
				$wgOut->showErrorPage( 'revdelete-nooldid-title', 'revdelete-nooldid-text' );
				return;
			} else if( !$log->userCan($event,Revision::DELETED_RESTRICTED) ) {
			// If an event is hidden from sysops
				if( $action != 'submit') {
					$wgOut->permissionRequired( 'hiderevision' );
					return;
				}
				$UserAllowed=false;
			}
			$wgOut->addHtml( $this->logLine( $log, $event ) );
			$bitfields = $this->setBitfield( $bitfields, $event->log_deleted );
		}
		$wgOut->addHtml( "</ul>" );

		$wgOut->addWikiText( wfMsgHtml( 'revdelete-text' ) );
		//Normal sysops can always see what they did, but can't always change it
		if( !$UserAllowed ) return;
		
		$items = array(
			wfInputLabel( wfMsgHtml( 'revdelete-log' ), 'wpReason', 'wpReason', 60 ),
			wfSubmitButton( wfMsgHtml( 'revdelete-submit' ) ) );
		$hidden = array(
			wfHidden( 'wpEditToken', $wgUser->editToken() ),
			wfHidden( 'target', $this->page->getPrefixedText() ),
			wfHidden( 'type', $this->deletetype ) );
		foreach( $this->events as $logid ) {
			$hidden[] = wfHidden( 'logid[]', $logid );
		}
		
		$special = SpecialPage::getTitleFor( 'Revisiondelete' );
		$wgOut->addHtml( wfElement( 'form', array(
			'method' => 'post',
			'action' => $special->getLocalUrl( 'action=submit' ) ),
			null ) );
		
		$wgOut->addHtml( '<fieldset><legend>' . wfMsgHtml( 'revdelete-legend' ) . '</legend>' );
		// FIXME: all items checked for just on event are checked, even if not set for the others
		foreach( $this->checks as $item ) {
			list( $message, $name, $field ) = $item;
			$wgOut->addHtml( '<div>' .
				wfCheckLabel( wfMsgHtml( $message), $name, $name, $bitfields & $field ) .
				'</div>' );
		}
		$wgOut->addHtml( '</fieldset>' );
		foreach( $items as $item ) {
			$wgOut->addHtml( '<p>' . $item . '</p>' );
		}
		foreach( $hidden as $item ) {
			$wgOut->addHtml( $item );
		}
		
		$wgOut->addHtml( '</form>' );
	}
	
	/**
	 * @param Revision $rev
	 * @returns string
	 */
	function historyLine( $rev ) {
		global $wgContLang;
		$date = $wgContLang->timeanddate( $rev->getTimestamp() );
		
		$difflink=''; $del = '';
		// Live revisions
		if( $this->deletetype=='oldid' ) {
			$difflink = '(' . $this->skin->makeKnownLinkObj( $this->page, wfMsgHtml('diff'), 
				'&diff=' . $rev->getId() . '&oldid=prev' ) . ')';
			$revlink = $this->skin->makeLinkObj( $this->page, $date, 'oldid=' . $rev->getId() );
		} else {
		// Archived revisions
			$undelete = SpecialPage::getTitleFor( 'Undelete' );
			$target = $this->page->getPrefixedText();
			$revlink = $this->skin->makeLinkObj( $undelete, $date, "target=$target&timestamp=" . $rev->getTimestamp() );
		}
	
		if( $rev->isDeleted(Revision::DELETED_TEXT) ) {
			$revlink = '<span class="history-deleted">'.$revlink.'</span>';
			$del = ' <tt>' . wfMsgHtml( 'deletedrev' ) . '</tt>';
			if( !$rev->userCan(Revision::DELETED_TEXT) ) {
				$revlink = '<span class="history-deleted">'.$date.'</span>';
			}
		}
		
		return
			"<li> $difflink $revlink " . $this->skin->revUserLink( $rev ) . " " . $this->skin->revComment( $rev ) . "$del</li>";
	}
	
	/**
	 * @param OldImage or ArchivedFile $file
	 * This can work for old or archived revisions
	 * @returns string
	 */	
	function uploadLine( $file ) {
		global $wgContLang, $wgTitle;
		
		$target = $this->page->getPrefixedText();
		$date = $wgContLang->timeanddate( $file->mTimestamp, true  );
	
		$del = '';
		// Special:Undelete for viewing archived images
		if( $this->deletetype=='fileid' ) {
			$undelete = SpecialPage::getTitleFor( 'Undelete' );
			$pageLink = $this->skin->makeKnownLinkObj( $undelete, $date, "target=$target&file=$file->mKey" );
		// Revisiondelete for viewing images
		} else {
			# Hidden files...
			if( $file->isDeleted(Image::DELETED_FILE) ) {
				$del = ' <tt>' . wfMsgHtml( 'deletedrev' ) . '</tt>';
				if( !$file->userCan(Image::DELETED_FILE) ) {
					$pageLink = $date;
				} else {
					$pageLink = $this->skin->makeKnownLinkObj( $wgTitle, $date, "target=$target&file=$file->mArchiveName" );
				}
				$pageLink = '<span class="history-deleted">' . $pageLink . '</span>';
			# Regular files...
			} else {
				$url = htmlspecialchars( wfImageArchiveUrl( $file->mArchiveName ) );
				$pageLink = "<a href=\"{$url}\">{$date}</a>";
			}
		}
		
		$data = wfMsgHtml( 'widthheight',
						$wgContLang->formatNum( $file->mWidth ),
						$wgContLang->formatNum( $file->mHeight ) ) .
				' (' . wfMsgHtml( 'nbytes', $wgContLang->formatNum( $file->mSize ) ) . ')';	
	
		return
			"<li> $pageLink " . $this->skin->fileUserLink( $file ) . " $data " . $this->skin->fileComment( $file ) . "$del</li>";
	}
	
	/**
	 * @param Revision $rev
	 * @returns string
	 */
	function logLine( $log, $event ) {
		global $wgContLang;

		$date = $wgContLang->timeanddate( $event->log_timestamp );
		$paramArray = LogPage::extractParams( $event->log_params );

		if( !LogViewer::userCan($event,LogViewer::DELETED_ACTION) ) {
			$action = '<span class="history-deleted">' . wfMsgHtml('rev-deleted-event') . '</span>';	
		} else {	
			$action = LogPage::actionText( $event->log_type, $event->log_action, $this->page, $this->skin, $paramArray, true, true );
			if( $event->log_deleted & LogViewer::DELETED_ACTION )
				$action = '<span class="history-deleted">' . $action . '</span>';
		}
		return
			"<li>$date" . " " . $this->skin->logUserLink( $event ) . " $action " . $this->skin->logComment( $event ) . "</li>";
	}
	
	/**
	 * @param WebRequest $request
	 */
	function submit( $request ) {
		$bitfield = $this->extractBitfield( $request );
		$comment = $request->getText( 'wpReason' );
		
		$target = $request->getText( 'target' );
		$title = Title::newFromURL( $target, false );
		
		if( $this->save( $bitfield, $comment, $title ) ) {
			$this->success( $request );
		} else if( $request->getCheck( 'oldid' ) || $request->getCheck( 'arid' ) ) {
			return $this->showRevs( $request );
		} else if( $request->getCheck( 'logid' ) ) {
			return $this->showLogs( $request );
		} else if( $request->getCheck( 'oldimage' ) || $request->getCheck( 'fileid' ) ) {
			return $this->showImages( $request );
		} 
	}
	
	function success( $request ) {
		global $wgOut;
		
		$wgOut->setPagetitle( wfMsgHtml( 'actioncomplete' ) );
		
		$target = $request->getText( 'target' );
		$type = $request->getText( 'type' );

		$title = Title::newFromURL( $target, false );
		$name = $title->makeName( $title->getNamespace(), $title->getText() );
		
		$logtitle = SpecialPage::getTitleFor( 'Log' );
        $loglink = $this->skin->makeKnownLinkObj( $logtitle, wfMsgHtml( 'viewpagelogs' ),
		wfArrayToCGI( array('page' => $name ) ) );
		$histlink = $this->skin->makeKnownLinkObj( $title, wfMsgHtml( 'revhistory' ),
		wfArrayToCGI( array('action' => 'history' ) ) );
		
		if( $title->getNamespace() > -1)
			$wgOut->setSubtitle( '<p>'.$histlink.' / '.$loglink.'</p>' );
		
		if( $type=='logid' ) {
			$wgOut->addWikiText( wfMsgHtml('logdelete-success', $target), false );
			$this->showEvents( $request );
		} else if( $type=='oldid' ) {
		  	$wgOut->addWikiText( wfMsgHtml('revdelete-success', $target), false );
		  	$this->showRevs( $request );
		} else if( $type=='arid' || $type=='fileid' ) {
			$undelete = SpecialPage::getTitleFor( 'Undelete' );
			# Redirect out, we already have the deleted history right there
		  	$wgOut->redirect( $undelete->escapeLocalUrl() . '/' . htmlspecialchars($target) );
		} else if( $type=='oldimage' ) {
			# Redirect out, we already have the history right there
		  	$wgOut->redirect( $title->escapeLocalUrl() );
		}
	}
	
	/**
	 * Put together a rev_deleted bitfield from the submitted checkboxes
	 * @param WebRequest $request
	 * @return int
	 */
	function extractBitfield( $request ) {
		$bitfield = 0;
		foreach( $this->checks as $item ) {
			list( /* message */ , $name, $field ) = $item;
			if( $request->getCheck( $name ) ) {
				$bitfield |= $field;
			}
		}
		return $bitfield;
	}
	
	function save( $bitfield, $reason, $title ) {
		$dbw = wfGetDB( DB_MASTER );
		$deleter = new RevisionDeleter( $dbw );
		// By this point, only one of the below should be set
		if( isset($this->revisions) ) {
			return $deleter->setRevVisibility( $title, $this->revisions, $bitfield, $reason );
		} else if( isset($this->archrevs) ) {
			return $deleter->setArchiveVisibility( $title, $this->archrevs, $bitfield, $reason );
		} else if( isset($this->events) ) {
			return $deleter->setEventVisibility( $title, $this->events, $bitfield, $reason );
		} else if( isset($this->ofiles) ) {
			return $deleter->setOldImgVisibility( $title, $this->ofiles, $bitfield, $reason );
		} else if( isset($this->afiles) ) {
			return $deleter->setArchFileVisibility( $title, $this->afiles, $bitfield, $reason );
		}
	}
}

/**
 * Implements the actions for Revision Deletion.
 * @addtogroup SpecialPage
 */
class RevisionDeleter {
	function __construct( $db ) {
		$this->dbw = $db;
	}
	
	/**
	 * @param $title, the page these events apply to
	 * @param array $items list of revision ID numbers
	 * @param int $bitfield new rev_deleted value
	 * @param string $comment Comment for log records
	 */
	function setRevVisibility( $title, $items, $bitfield, $comment ) {
		global $wgOut;
		
		$UserAllowedAll = true;
		$pages_count = array(); 
		$pages_revIds = array();
		// To work!
		foreach( $items as $revid ) {
			$rev = Revision::newFromTitle( $title, $revid );
			if( !is_object($rev) || $rev->isCurrent() ) {
				return false;
			} else if( !$rev->userCan(Revision::DELETED_RESTRICTED) ) {
    			$UserAllowedAll=false; 
				continue;
			}
			$pageid = $rev->getPage();
			// For logging, maintain a count of revisions per page
			if( !isset($pages_count[$pageid]) ) {
				$pages_count[$pageid]=0;
				$pages_revIds[$pageid]=array();
			}
			// Which pages did we change anything about?
			if( $rev->mDeleted != $bitfield ) {
				$pages_count[$pageid]++;
				$pages_revIds[$pageid][]=$revid;
				
			   	$this->updateRevision( $rev, $bitfield );
				$this->updateRecentChangesEdits( $rev, $bitfield, false );
			}
		}
		
		// Clear caches...
		foreach( $pages_count as $pageid => $count ) {
			//Don't log or touch if nothing changed
			if( $count > 0 ) {
			   $title = Title::newFromId( $pageid );
			   $this->updatePage( $title );
			   $this->updateLog( $title, $count, $bitfield, $comment, $title, 'oldid', $pages_revIds[$pageid] );
			}
		}
		// Where all revs allowed to be set?
		if( !$UserAllowedAll ) {
			//FIXME: still might be confusing???
			$wgOut->permissionRequired( 'hiderevision' );
			return false;
		}
		
		return true;
	}
	
	 /**
	 * @param $title, the page these events apply to
	 * @param array $items list of revision ID numbers
	 * @param int $bitfield new rev_deleted value
	 * @param string $comment Comment for log records
	 */
	function setArchiveVisibility( $title, $items, $bitfield, $comment ) {
		global $wgOut;
		
		$UserAllowedAll = true;
		$count = 0; 
		$Id_set = array();
		// To work!
		$archive = new PageArchive( $title );
		foreach( $items as $revid ) {
			$rev = $archive->getRevision( '', $revid );
			if( !is_object($rev) ) {
				return false;
			} else if( !$rev->userCan(Revision::DELETED_RESTRICTED) ) {
    			$UserAllowedAll=false;
				continue;
			}
			// Which revisions did we change anything about?
			if( $rev->mDeleted != $bitfield ) {
			   $Id_set[]=$revid;
			   $count++;
			   
			   $this->updateArchive( $rev, $bitfield );
			}
		}
		
		// For logging, maintain a count of revisions
		if( $count > 0 ) {
			$this->updateLog( $title, $count, $bitfield, $comment, $title, 'arid', $Id_set );
		}
		// Where all revs allowed to be set?
		if( !$UserAllowedAll ) {
			$wgOut->permissionRequired( 'hiderevision' ); 
			return false;
		}
		
		return true;
	}
	
	 /**
	 * @param $title, the page these events apply to
	 * @param array $items list of revision ID numbers
	 * @param int $bitfield new rev_deleted value
	 * @param string $comment Comment for log records
	 */
	function setOldImgVisibility( $title, $items, $bitfield, $comment ) {
		global $wgOut;
		
		$UserAllowedAll = true;
		$count = 0; 
		$set = array();
		// To work!
		foreach( $items as $name ) {
			// Our image may be hidden, if so it's name is formated as <time>!<key>
			// Otherwise, it will be <time>!<image> and the URL only needs to pass the time
			$archivename = ( strpos($name,'!')==false ) ? $name.'!'.$title->getDbKey() : $name;
			$oimage = new OldImage( $title, $archivename );
			if( !isset($oimage->mName) ) {
				return false;
			} else if( !$oimage->userCan(Revision::DELETED_RESTRICTED) ) {
    			$UserAllowedAll=false;
				continue;
			}
			
			$transaction = true;
			// Which revisions did we change anything about?
			if( $oimage->mDeleted != $bitfield ) {
				$count++;
				
				$this->dbw->begin();
				$this->updateOldFiles( $oimage, $bitfield );
				// If this image is currently hidden...
				if( $oimage->mDeleted & Image::DELETED_FILE ) {
					if( $bitfield & Image::DELETED_FILE ) {
						# Leave it alone if we are not changing this...
						$set[]=$name;
						$transaction = true;
					} else {
						# We are moving this out
						$transaction = $this->makeOldImagePublic( $oimage );
						$set[]=$transaction;
					}
				// Is it just now becoming hidden?
				} else if( $bitfield & Image::DELETED_FILE ) {
					$transaction = $this->makeOldImagePrivate( $oimage );
					$set[]=$transaction;
				} else {
					$set[]=$name;
				}
				// If our file operations fail, then revert back the db
				if ( $transaction==false ) {
					$this->dbw->rollback();
					return false;
				}
				$this->dbw->commit();
				
				$this->updatePage( $title ); // Clear the image page cache
			}
		}
		
		// Log if something was changed
		if( $count > 0 ) {
			$this->updateLog( $title, $count, $bitfield, $comment, $title, 'oldimage', $set );
		}
		// Where all revs allowed to be set?
		if( !$UserAllowedAll ) {
			$wgOut->permissionRequired( 'hiderevision' ); 
			return false;
		}
		
		return true;
	}
	
	 /**
	 * @param $title, the page these events apply to
	 * @param array $items list of revision ID numbers
	 * @param int $bitfield new rev_deleted value
	 * @param string $comment Comment for log records
	 */
	function setArchFileVisibility( $title, $items, $bitfield, $comment ) {
		global $wgOut;
		
		$UserAllowedAll = true;
		$count = 0; 
		$Id_set = array();
		// To work!
		foreach( $items as $fileid ) {
			$file = new ArchivedFile( $title, $fileid );
			if( !isset($file->mId) ) {
				return false;
			} else if( !$file->userCan(Revision::DELETED_RESTRICTED) ) {
    			$UserAllowedAll=false;
				continue;
			}
			// Which revisions did we change anything about?
			if( $file->mDeleted != $bitfield ) {
			   $Id_set[]=$fileid;
			   $count++;
			   
			   $this->updateArchFiles( $file, $bitfield );
			}
		}
		
		// Log if something was changed
		if( $count > 0 ) {
			$this->updateLog( $title, $count, $bitfield, $comment, $title, 'fileid', $Id_set );
		}
		// Where all revs allowed to be set?
		if( !$UserAllowedAll ) {
			$wgOut->permissionRequired( 'hiderevision' );
			return false;
		}
		
		return true;
	}

	/**
	 * @param $title, the page these events apply to
	 * @param array $items list of log ID numbers
	 * @param int $bitfield new log_deleted value
	 * @param string $comment Comment for log records
	 */
	function setEventVisibility( $title, $items, $bitfield, $comment ) {
		global $wgOut;
		
		$UserAllowedAll = true;
		$logs_count = array(); 
		$logs_Ids = array();
		// To work!
		foreach( $items as $logid ) {
			$event = LogReader::newFromTitle( $title, $logid );
			if( is_null($event) ) {
				return false;
			} else if( !LogViewer::userCan($event, Revision::DELETED_RESTRICTED) || $event->log_type=='oversight' ) {
			// Don't hide from oversight log!!!
    			$UserAllowedAll=false;
    			continue;
			}
			$logtype = $event->log_type;
			// For logging, maintain a count of events per log type
			if( !isset( $logs_count[$logtype] ) ) {
				$logs_count[$logtype]=0;
				$logs_Ids[$logtype]=array();
			}
			// Which logs did we change anything about?
			if( $event->log_deleted != $bitfield ) {
				$logs_Ids[$logtype][]=$logid;
				$logs_count[$logtype]++;
			   
			   	$this->updateLogs( $event, $bitfield );
				$this->updateRecentChangesLog( $event, $bitfield, true );
			}
		}
		foreach( $logs_count as $logtype => $count ) {
			//Don't log or touch if nothing changed
			if( $count > 0 ) {
			   $target = SpecialPage::getTitleFor( 'Log', $logtype );
			   $this->updateLog( $target, $count, $bitfield, $comment, $title, 'logid', $logs_Ids[$logtype] );
			}
		}
		// Where all revs allowed to be set?
		if( !$UserAllowedAll ) {
			$wgOut->permissionRequired( 'hiderevision' ); 
			return false;
		}
		
		return true;
	}

	/**
	 * Moves an image to a safe private location
	 * @param $dbw, database
	 * @param OldImage $oimage
	 * @returns string, new archivename on success, false on failure
	 */	
	function makeOldImagePrivate( $oimage ) {
		global $wgFileStore, $wgUseSquid;
	
		$transaction = new FSTransaction();
		if( !FileStore::lock() ) {
			wfDebug( __METHOD__.": failed to acquire file store lock, aborting\n" );
			return false;
		}
		
		list($timestamp,$name) = explode('!',$oimage->mArchiveName);
		
		$oldpath = wfImageArchiveDir( $oimage->mName ) . DIRECTORY_SEPARATOR . $oimage->mArchiveName;
		// Dupe the file into the file store
		if( file_exists( $oldpath ) ) {
			$group = 'hidden';
			// Is our directory configured?
			if( $wgFileStore[$group]['directory'] ) {
				$store = FileStore::get( $group );
				$key = FileStore::calculateKey( $oldpath, $this->extension );
				$transaction->add( $store->insert( $key, $oldpath, FileStore::DELETE_ORIGINAL ) );
			} else {
				$group = null;
				$key = null;
				$transaction = false; // Return an error and do nothing
			}
		} else {
			wfDebug( __METHOD__." deleting already-missing '$path'; moving on to database\n" );
			$group = null;
			$key = '';
			$transaction = new FSTransaction(); // empty
		}

		if( $transaction === false ) {
			// Fail to restore?
			wfDebug( __METHOD__.": import to file store failed, aborting\n" );
			throw new MWException( "Could not archive and delete file $path" );
			return false;
		}
		
		wfDebug( __METHOD__.": set db items, applying file transactions\n" );
		$transaction->commit();
		FileStore::unlock();
		// Update our database to add a reference to the key
		// For shortness, make it as <timestamp>!<key>
		if ( $key ) {
			$this->dbw->update( 'oldimage',
				array( 'oi_archive_name' => "{$timestamp}!{$key}" ),
				array( 'oi_name' => $oimage->mName, 'oi_archive_name' => $oimage->mArchiveName ),
				__METHOD__ );
		}
		
		// Use of $timeframe for Image objects can create thumbnails of oldimages
		$imgtitle = Title::makeTitle( NS_IMAGE, $oimage->mName );
		$image = new Image( $imgtitle );
		$image->purgeCache(); // Clear any thumbnails/purge squid cache
		
		return "{$timestamp}!{$key}";
	}

	/**
	 * Moves an image from a safe private location
	 * @param $dbw, database
	 * @param OldImage $oimage
	 * @returns string, timestamp on success, false on failure
	 */		
	function makeOldImagePublic( $oimage ) {
	
		$transaction = new FSTransaction();
		if( !FileStore::lock() ) {
			wfDebug( __METHOD__." could not acquire filestore lock\n" );
			return false;
		}
		$group = 'hidden';
		
		$store = FileStore::get( $group );
		if( !$store ) {
			wfDebug( __METHOD__.": skipping row with no file.\n" );
			return false;
		}
		
		$destDir = wfImageArchiveDir( $oimage->mName );
		if ( !is_dir( $destDir ) ) {
			wfMkdirParents( $destDir );
		}
		// Deleted versions have an archive_name like <timestamp>!<key>
		list($timestamp,$key) = explode('!',$oimage->mArchiveName);
		$archivename = "{$timestamp}!{$oimage->mName}";
		
		$destPath = $destDir . DIRECTORY_SEPARATOR . $archivename;
		// Check if any other stored revisions use this file;
		// if so, we shouldn't remove the file from the hidden
		// archives so they will still work.
		$useCount = $this->dbw->selectField( 'oldimage',
			'COUNT(*)',
			array( 'oi_archive_name' => $oimage->mArchiveName, 'oi_name' => $oimage->mName ),
			__METHOD__ );
			
		if( $useCount == 0 ) {
			wfDebug( __METHOD__.": nothing else using {$oimage->mArchiveName}, will deleting after\n" );
			$flags = FileStore::DELETE_ORIGINAL;
		} else {
			$flags = 0;
		}
		$transaction->add( $store->export( $key, $destPath, $flags ) );
		
		wfDebug( __METHOD__.": set db items, applying file transactions\n" );
		$transaction->commit();
		FileStore::unlock();
		// Re-insert the original archive_name, like <timestamp>!<name>
		$this->dbw->update( 'oldimage',
			array( 'oi_archive_name' => "{$timestamp}!{$oimage->mName}" ),
			array( 'oi_name' => $oimage->mName, 'oi_archive_name' => $oimage->mArchiveName ),
			__METHOD__ );
		
		return $timestamp;
	}
	
	/**
	 * Update the revision's rev_deleted field
	 * @param Revision $rev
	 * @param int $bitfield new rev_deleted bitfield value
	 */
	function updateRevision( $rev, $bitfield ) {
		$this->dbw->update( 'revision',
			array( 'rev_deleted' => $bitfield ),
			array( 'rev_id' => $rev->getId() ),
			'RevisionDeleter::updateRevision' );
	}
	
	/**
	 * Update the revision's rev_deleted field
	 * @param Revision $rev
	 * @param int $bitfield new rev_deleted bitfield value
	 */
	function updateArchive( $rev, $bitfield ) {
		$this->dbw->update( 'archive',
			array( 'ar_deleted' => $bitfield ),
			array( 'ar_rev_id' => $rev->getId() ),
			'RevisionDeleter::updateArchive' );
	}

	/**
	 * Update the images's oi_deleted field
	 * @param Revision $file
	 * @param int $bitfield new rev_deleted bitfield value
	 */
	function updateOldFiles( $oimage, $bitfield ) {
		$this->dbw->update( 'oldimage',
			array( 'oi_deleted' => $bitfield ),
			array( 'oi_archive_name' => $oimage->mArchiveName ),
			'RevisionDeleter::updateOldFiles' );
	}
	
	/**
	 * Update the images's fa_deleted field
	 * @param Revision $file
	 * @param int $bitfield new rev_deleted bitfield value
	 */
	function updateArchFiles( $file, $bitfield ) {
		$this->dbw->update( 'filearchive',
			array( 'fa_deleted' => $bitfield ),
			array( 'fa_id' => $file->mId ),
			'RevisionDeleter::updateArchFiles' );
	}	
	
	/**
	 * Update the logging log_deleted field
	 * @param Revision $rev
	 * @param int $bitfield new rev_deleted bitfield value
	 */
	function updateLogs( $event, $bitfield ) {
		$this->dbw->update( 'logging',
			array( 'log_deleted' => $bitfield ),
			array( 'log_id' => $event->log_id ),
			'RevisionDeleter::updateLogs' );
	}
	
	/**
	 * Update the revision's recentchanges record if fields have been hidden
	 * @param Revision $event
	 * @param int $bitfield new rev_deleted bitfield value
	 */
	function updateRecentChangesLog( $event, $bitfield ) {
		$this->dbw->update( 'recentchanges',
			array( 'rc_deleted' => $bitfield,
				   'rc_patrolled' => 1),
			array( 'rc_logid' => $event->log_id ),
			'RevisionDeleter::updateRecentChangesLog' );
	}
	
	/**
	 * Update the revision's recentchanges record if fields have been hidden
	 * @param Revision $rev
	 * @param int $bitfield new rev_deleted bitfield value
	 */
	function updateRecentChangesEdits( $rev, $bitfield ) {
		$this->dbw->update( 'recentchanges',
			array( 'rc_deleted' => $bitfield,
				   'rc_patrolled' => 1),
			array( 'rc_this_oldid' => $rev->getId() ),
			'RevisionDeleter::updateRecentChangesEdits' );
	}
	
	/**
	 * Touch the page's cache invalidation timestamp; this forces cached
	 * history views to refresh, so any newly hidden or shown fields will
	 * update properly.
	 * @param Title $title
	 */
	function updatePage( $title ) {
		$title->invalidateCache();
		$title->purgeSquid();
	}
	
	/**
	 * Record a log entry on the action
	 * @param Title $title, page where item was removed from
	 * @param int $count the number of revisions altered for this page
	 * @param int $bitfield the new rev_deleted value
	 * @param string $comment
	 * @param Title $target, the relevant page
	 * @param string $param, URL param
	 * @param Array $items
	 */
	function updateLog( $title, $count, $bitfield, $comment, $target, $param, $items = array() ) {
		// Put things hidden from sysops in the oversight log
		$logtype = ( $bitfield & Revision::DELETED_RESTRICTED ) ? 'oversight' : 'delete';
		// Add params for effected page and ids
		$params = array( $target->getPrefixedText(), $param, implode( ',', $items) );
		$log = new LogPage( $logtype );	
		if( $param=='logid' ) {
    		$reason = wfMsgExt('logdelete-logaction', array('parsemag'), $count, $bitfield, $target->getPrefixedText() );
			if($comment) $reason .= ": $comment";
			$log->addEntry( 'event', $title, $reason, $params );
		} else {
    		$reason = wfMsgExt('revdelete-logaction', array('parsemag'), $count, $bitfield );
			if($comment) $reason .= ": $comment";
			$log->addEntry( 'revision', $title, $reason, $params );
		}
	}
}

?>
