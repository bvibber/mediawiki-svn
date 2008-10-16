<?php
if (!defined('MEDIAWIKI')) die();
/**
 * Class file for the GoogleAdSense extension
 *
 * @addtogroup Extensions
 * @author Siebrand Mazeland
 * @license MIT
 */
class GoogleAdSense {

	static function GoogleAdSenseInSidebar( $skin, &$bar ) {
		global $wgGoogleAdSenseWidth, $wgGoogleAdSenseID,
			$wgGoogleAdSenseHeight, $wgGoogleAdSenseClient,
			$wgGoogleAdSenseSlot, $wgGoogleAdSenseSrc;

		// Return $bar unchanged if not all values have been set.
		// FIXME: signal incorrect configuration nicely?
		if( $wgGoogleAdSenseClient == 'none' || $wgGoogleAdSenseSlot == 'none' || $wgGoogleAdSenseID == 'none' )
			return $bar;

		wfLoadExtensionMessages( 'GoogleAdSense' );

		$bar['googleadsense'] = "<script type=\"text/javascript\">
/* <![CDATA[ */
google_ad_client = \"$wgGoogleAdSenseClient\";
/* $wgGoogleAdSenseID */
google_ad_slot = \"$wgGoogleAdSenseSlot\";
google_ad_width = $wgGoogleAdSenseWidth;
google_ad_height = $wgGoogleAdSenseHeight;
/* ]]> */
</script>
<script type=\"text/javascript\"
src=\"$wgGoogleAdSenseSrc\">
</script>";

		return true;
	}
}
