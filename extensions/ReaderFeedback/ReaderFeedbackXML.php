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

}
