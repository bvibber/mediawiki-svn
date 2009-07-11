<?php

class ReaderFeedbackXML {
	/**
	 * Get tag dropdown select
	 * @param int $selected, selected level
	 */
	public static function getTagMenu( $selected = '' ) {
		wfLoadExtensionMessages( 'FlaggedRevs' );
		$s  = "<label for='wpRatingTag'>" . wfMsgHtml('revreview-tagfilter') . "</label>&nbsp;";
		$s .= Xml::openElement( 'select', array('name' => 'ratingtag', 'id' => 'wpRatingTag') );
		foreach( FlaggedRevs::getFeedbackTags() as $tag => $weight ) {
			$s .= Xml::option( wfMsg( "readerfeedback-$tag" ), $tag, $selected===$tag );
		}
		$s .= Xml::closeElement('select')."\n";
		return $s;
	}

}
