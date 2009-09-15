<?php

class ReaderFeedbackXML {
	/**
	 * Get tag dropdown select
	 * @param int $selected, selected level
	 */
	public static function getTagMenu( $selected = '' ) {
		wfLoadExtensionMessages( 'ReaderFeedback' );
		$s  = "<label for='wpRatingTag'>" . wfMsgHtml('readerfeedback-tagfilter') . "</label>&nbsp;";
		$s .= Xml::openElement( 'select', array('name' => 'ratingtag', 'id' => 'wpRatingTag') );
		foreach( ReaderFeedback::getFeedbackTags() as $tag => $weight ) {
			$s .= Xml::option( wfMsg( "readerfeedback-$tag" ), $tag, $selected===$tag );
		}
		$s .= Xml::closeElement('select')."\n";
		return $s;
	}

	/**
	 * Get rating tier dropdown select
	 * @param int $selected, selected tier
	 */	
	 public static function getRatingTierMenu( $selected = '' ) {
		wfLoadExtensionMessages( 'ReaderFeedback' );
		$s  = "<label for='wpRatingTier'>" . wfMsgHtml('readerfeedback-tierfilter') . "</label>&nbsp;";
		$s .= Xml::openElement( 'select', array('name' => 'ratingtier', 'id' => 'wpRatingTier') );
		$s .= Xml::option( wfMsg( "readerfeedback-tier-high" ), 3, $selected===3);
		$s .= Xml::option( wfMsg( "readerfeedback-tier-medium" ), 2, $selected===2 );
		$s .= Xml::option( wfMsg( "readerfeedback-tier-poor" ), 1, $selected===1 );
		$s .= Xml::closeElement('select')."\n";
		return $s;
	}
}
