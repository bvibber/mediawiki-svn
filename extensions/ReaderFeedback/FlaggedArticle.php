<?php

class FlaggedArticle extends Article {
	 /**
	 * Is this a view page action?
	 * @param $action string
	 * @returns bool
	 */
	protected static function isViewAction( $action ) {
		return ( $action == 'view' || $action == 'purge' || $action == 'render' );
	}
	
	 /**
	 * Is this article rateable?
	 */
	public function isRateable() {
		return FlaggedRevs::isPageRateable( $this->parent->getTitle() );
	}

	 /**
	 * Add feedback form to pages when necessary
	 */
	public function addFeedbackForm( &$data ) {
		global $wgRequest, $wgUser, $wgOut;
		if( !$this->parent->exists() || !$this->isRateable() || !$wgOut->getRevisionId() ) {
			return true;
		}
		# Check action and if page is protected
		$action = $wgRequest->getVal( 'action', 'view' );
		if( !self::isViewAction($action) ) {
			return true;
		}
		if( $wgUser->isAllowed( 'feedback' ) ) {
			# Only allow votes on the latest revision!
			$id = $wgOut->getRevisionId();
			if( $id != $this->parent->getLatest() ) {
				return true;
			}
			# If the user already voted, then don't show the form.
			# Always show for IPs however, due to squid caching...
			if( !$wgUser->getId() || !ReaderFeedback::userAlreadyVoted( $this->parent->getTitle(), $id ) ) {
				$this->addQuickFeedback( $data );
			}
		}
		return true;
	}

	 /**
	 * Adds a brief feedback form to a page.
	 * @param OutputPage $out
	 * @param Title $title
	 * @param bool $top, should this form always go on top?
	 */
	public function addQuickFeedback( &$data, $top = false ) {
		global $wgOut, $wgUser, $wgRequest, $wgFlaggedRevsFeedbackTags;
		# Are there any reader input tags?
		if( empty($wgFlaggedRevsFeedbackTags) ) {
			return false;
		}
		# Revision being displayed
		$id = $wgOut->getRevisionId();
		# Load required messages
		wfLoadExtensionMessages( 'FlaggedRevs' );
		$reviewTitle = SpecialPage::getTitleFor( 'ReaderFeedback' );
		$action = $reviewTitle->getLocalUrl( 'action=submit' );
		$form = Xml::openElement( 'form', array( 'method' => 'post', 'action' => $action,
			'id' => 'mw-feedbackform' ) );
		$form .= Xml::openElement( 'fieldset', array('class' => 'flaggedrevs_reviewform noprint') );
		$form .= "<legend><strong>" . wfMsgHtml( 'readerfeedback' ) . "</strong></legend>\n";
		# Avoid clutter
		if( !$wgUser->isAllowed('review') ) {
			$form .= wfMsgExt( 'readerfeedback-text', array('parse') );
		}
		$form .= Xml::openElement( 'span', array('id' => 'mw-feedbackselects') );
		# Loop through all different flag types
		foreach( FlaggedRevs::getFeedbackTags() as $quality => $levels ) {
			$label = array();
			$selected = ( isset($flags[$quality]) && $flags[$quality] > 0 ) ? $flags[$quality] : -1;
			$form .= "<b>" . Xml::label( wfMsgHtml("readerfeedback-$quality"), "wp$quality" ) . ":</b>";
			$attribs = array( 'name' => "wp$quality", 'id' => "wp$quality",
				'onchange' => "updateFeedbackForm()" );
			$form .= '&nbsp;' . Xml::openElement( 'select', $attribs );
			$levels = array_reverse($levels,true);
			foreach( $levels as $i => $name ) {
				$optionClass = array( 'class' => "fr-rating-option-$i" );
				$form .= Xml::option( wfMsg("readerfeedback-level-$i"), $i, ($i == $selected), $optionClass ) ."\n";
			}
			$form .= Xml::option( wfMsg("readerfeedback-level-none"), -1, (-1 == $selected) ) ."\n";
			$form .= Xml::closeElement( 'select' )."\n";
		}
		$form .= Xml::closeElement( 'span' );
		$form .= Xml::submitButton( wfMsg('readerfeedback-submit'),
			array('id' => 'submitfeedback','accesskey' => wfMsg('revreview-ak-review'),
			'title' => wfMsg('revreview-tt-review').' ['.wfMsg('revreview-ak-review').']' )
		);
		# Hidden params
		$form .= Xml::hidden( 'title', $reviewTitle->getPrefixedText() ) . "\n";
		$form .= Xml::hidden( 'target', $this->parent->getTitle()->getPrefixedDBKey() ) . "\n";
		$form .= Xml::hidden( 'oldid', $id ) . "\n";
		$form .= Xml::hidden( 'validatedParams', ReaderFeedback::validationKey( $id, $wgUser->getId() ) );
		$form .= Xml::hidden( 'action', 'submit') . "\n";
		$form .= Xml::hidden( 'wpEditToken', $wgUser->editToken() ) . "\n";
		# Honeypot input
		$form .= Xml::input( 'commentary', 12, '', array('style' => 'display:none;') ) . "\n";
		$form .= Xml::closeElement( 'fieldset' );
		$form .= Xml::closeElement( 'form' );
		if( $top ) {
			$wgOut->prependHTML( $form );
		} else {
			$data .= $form;
		}
		return true;
	}
}
