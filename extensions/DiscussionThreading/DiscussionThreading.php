<?php

/**
 * Extension to provide discussion threading similar to a listserv archive
 *
 * @author Jack D. Pond <jack.pond@psitex.com>
 * @addtogroup Extensions
 * @copyright © 2007 Jack D. pond
 * @licence GNU General Public Licence 2.0 or later
 */

if( defined( 'MEDIAWIKI' ) ) {


# Internationalisation file
	require_once( "$IP/extensions/DiscussionThreading/DiscussionThreading.i18n.php" );

	$wgExtensionFunctions[] = 'efDiscussionThreadSetup';
	$wgExtensionCredits['other'][] = array( 
						'name' => 'DiscussionThreading', 
						'author' => 'Jack D. Pond',
						'url' => 'http://www.mediawiki.org/wiki/Extension:DiscussionThreading',
						'description' => 'Add Threading to discussion (talk) pages' );

	/**
	 * Set up hooks for discussion threading
	 *
	 * @param $wgSectionThreadingOn global logical variable to activate threading
	 */


	global $wgSectionThreadingOn;
	$wgSectionThreadingOn = True;

	$wgHooks['EditPage::showEditForm:initial'][] =  'efDiscussionThread';
	$wgHooks['EditPage::attemptSave'][] = 'efStampReply';
	$wgHooks['EditPage::showEditForm:initial'][] =  'efDiscussionThreadEdit';
	$wgHooks['EditSectionLinkForOther'][] =  'efDiscussionLink4other';
	$wgHooks['EditSectionLink'][] =  'efDiscussionLink';
	$wgHooks['AlternateEdit'][] =  'efDiscussionThreadEdit';
	



	/**
	 * Initial setup, add .i18n. messages from $IP/extensions/DiscussionThreading.i18n.php
	*/		
	function efDiscussionThreadSetup() {
		global $wgMessageCache, $wgDiscussionThreadMessages;

		foreach( $wgDiscussionThreadMessages as $lang => $messages )
			$wgMessageCache->addMessages( $messages, $lang );
	}
	/**
	 * This function creates a linkobject for the editSectionLinkForOther function in linker
	 *
	 * @param $callobj Article object.
	 * @param $title Title object.
	 * @param $section Integer: section number.
	 * @param $hint Link String: title, or default if omitted or empty
	 * @param $url Link String: for edit url
	 * @param $result String: Returns the section [new][edit][reply] html if in a talk page - otherwise whatever came in with
	 * @return  true
	 */

	function efDiscussionLink4other ($callobj, $title, $section , $url , &$result)
	{
		global $wgSectionThreadingOn;
		if($wgSectionThreadingOn && $title->isTalkPage() ) {
			$commenturl = '&section='.$section.'&replyto=yes';
			$curl = $callobj->makeKnownLinkObj( $title, wfMsg('replysection'), 'action=edit'.$commenturl );
			$newthreadurl = '&section=new';
			$nurl = $callobj->makeKnownLinkObj( $nt, wfMsg('threadnewsection'), 'action=edit'.$newthreadurl );
			$result =  $nurl."][".$url."][".$curl;
		}
		return (true);
	}

	/**
	 * This function creates a linkobject for the editSectionLink function in linker
	 *
	 * @param $callobj Article object.
	 * @param $nt Title object.
	 * @param $section Integer: section number.
	 * @param $hint Link String: title, or default if omitted or empty
	 * @param $url Link String: for edit url
	 * @param $result String: Returns the section [new][edit][reply] html  if in a talk page - otherwise whatever came in with
	 * @return  true
	 */

	function efDiscussionLink ($callobj, $nt, $section, $hint='', $url , &$result)
	{
		global $wgSectionThreadingOn;
		if($wgSectionThreadingOn && $nt->isTalkPage() ) {
			$commenturl = '&section='.$section.'&replyto=yes';
			$hint = ( $hint=='' ) ? '' : ' title="' . wfMsgHtml( 'replysectionhint', htmlspecialchars( $hint ) ) . '"';
			$curl = $callobj->makeKnownLinkObj( $nt, wfMsg('replysection'), 'action=edit'.$commenturl, '', '', '',  $hint );
			$newthreadurl = '&section=new';
			$hint = ( $hint=='' ) ? '' : ' title="' . wfMsgHtml( 'threadnewsectionhint', htmlspecialchars( $hint ) ) . '"';
			$nurl = $callobj->makeKnownLinkObj( $nt, wfMsg('threadnewsection'), 'action=edit'.$newthreadurl, '', '', '',  $hint );
			$result = $nurl."][".$url."][".$curl;
		}
		return (true);
	}

	/**
	 * This function is a hook used to test to see if empty, if so, start a comment
	 *
	 * @param $efform form object.
	 * @return  true
	 */


	function efDiscussionThreadEdit ($efform) {
		global $wgRequest,$wgSectionThreadingOn;
		$efform->replytosection = '';
		$efform->replyadded = false;
		$efform->replytosection = $wgRequest->getVal( 'replyto' );
		if( !$efform->mTitle->exists() ) {
			if($wgSectionThreadingOn && $efform->mTitle->isTalkPage() ) {
				$efform->section = 'new';
			}
		}
		return (true);
	}

	/**
	 * Create a new header, one level below the 'replyto' header, add re: to front and tag it with user information
	 *
	 * @param $efform Form Object before display
	 * @return  true
	 */

	function efDiscussionThread($efform){
		global $wgSectionThreadingOn;
		$wgSectionThreadingOn = isset($wgSectionThreadingOn) ? $wgSectionThreadingOn : false;
		if ( $efform->replytosection != '' && $wgSectionThreadingOn  && !$efform->replyadded) {
			if ($efform->replytosection != '') {
				$text = $efform->textbox1;
				$matches = array();
				preg_match( "/^(=+)(.+)\\1/mi",
					$efform->textbox1,
					$matches );
				if( !empty( $matches[2] ) ) {
					preg_match( "/.*(-+)\\1/mi",$matches[2],$matchsign);
					if (!empty($matchsign[0]) ){
						$text = $text."\n\n".$matches[1]."=Re: ".trim($matchsign[0])." ~~~~".$matches[1]."=";
					} else {
						$text = $text."\n\n".$matches[1]."=Re: ".trim($matches[2])." -- ~~~~".$matches[1]."=";
					}
				} else {
					$text = $text." -- ~~~~<br>\n\n";
				}
				$efform->replyadded = true;
				$efform->textbox1 = $text;
			}
			return (true);
		}
		return (true);
	}
	/**
	 * When the new header is created from summary in new (+) add comment, just stamp the header as created
	 *
	 * @param $efform Form Object before display
	 * @return  true
	 */

	function efStampReply($efform){
		global $wgSectionThreadingOn;
		$wgSectionThreadingOn = isset($wgSectionThreadingOn) ? $wgSectionThreadingOn : false;
		if ( $efform->section == "new" && $wgSectionThreadingOn  && !$efform->replyadded) {
			$efform->summary = $efform->summary." -- ~~~~";
		}
		return(true);
	}
}
?>