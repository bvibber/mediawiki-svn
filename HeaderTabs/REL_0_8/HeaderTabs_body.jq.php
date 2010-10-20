<?php
/**
 * Version of the HeaderTabs class that uses jQuery and the ResourceLoader.
 *
 * @file
 * @ingroup Extensions
 *
 * @author Sergey Chernyshev
 * @author Yaron Koren
 */

class HeaderTabs {
	function tag( $input, $args, $parser ) {
		// this tag besides just enabling tabs, also designates end of tabs
		// TOC doesn't make sense where tabs are used
		return '<div id="nomoretabs"></div>';
	}

	function replaceFirstLevelHeaders( &$parser, &$text ) {
		global $htUseHistory, $wgVersion;

		$aboveandbelow = explode( '<div id="nomoretabs"></div>', $text, 2 );

		if ( count( $aboveandbelow ) <= 1 ) {
			return true; // <headertabs/> tag is not found
		}
		$below = $aboveandbelow[1];

		$aboveandtext = preg_split( '/(<a name=".*?"><\/a>)?<h1.*?class="mw-headline".*?<\/h1>/', $aboveandbelow[0], 2 );
		if ( count( $aboveandtext ) > 1 ) {
			$above = $aboveandtext[0];

			$tabs = array();

			$v = explode( '.', $wgVersion );
			if ( $v[0] > 1 || ( $v[0] == 1 && $v[1] >= 16 ) ) {
				$parts = preg_split( '/(<h1.*?class="mw-headline".*?<\/h1>)/', $aboveandbelow[0], - 1, PREG_SPLIT_DELIM_CAPTURE );
				array_shift( $parts ); // don't need above part anyway

				for ( $i = 0; $i < ( count( $parts ) / 2 ); $i++ )
				{
					preg_match( '/id="(.*?)"/', $parts[$i * 2], $matches );
					$tabid = $matches[1];

					preg_match( '/<span.*?class="mw-headline".*?>\s*(.*?)\s*<\/h1>/', $parts[$i * 2], $matches );
					$tabtitle = $matches[1];

					array_push( $tabs, array(
						'tabid' => $tabid,
						'title' => $tabtitle,
						'tabcontent' => $parts[$i * 2 + 1]
					) );
				}
			} else {
				$parts = preg_split( '/<a name="(.*?)"><\/a><h1>.*?<span class="mw-headline">\s*(.*?)\s*<\/span><\/h1>/', $aboveandbelow[0], - 1, PREG_SPLIT_DELIM_CAPTURE );
				array_shift( $parts ); // don't need above part anyway

				for ( $i = 0; $i < ( count( $parts ) / 3 ); $i++ ) {
					array_push( $tabs, array(
						'tabid' => $parts[$i * 3],
						'title' => $parts[$i * 3 + 1],
						'tabcontent' => $parts[$i * 3 + 2]
					) );
				}
			}

			$tabhtml  = '<div id="headertabs">';

			$tabhtml .= '<ul>';
			foreach ( $tabs as $i => $tab ) {
				$tabhtml .= '<li';
				if ( $i == 0 ) {
					$tabhtml .= ' class="selected"';
				}
				$tabhtml .= '><a href="#' . $tab['tabid'] . '">' . $tab['title'] . "</a></li>\n";
			}
			$tabhtml .= '</ul>';

			foreach ( $tabs as $tab ) {
				$tabhtml .= '<div id="' . $tab['tabid'] . '"><p>' . $tab['tabcontent'] . '</p></div>';
			}
			$tabhtml .= '</div>';

			$text = $above . $tabhtml . $below;
		}

		return true;
	}

	function addHTMLHeader( &$wgOut ) {
		global $htUseHistory; // unused, for now

		$wgOut->addModules( 'jquery.ui.tabs' );
		$js_text =<<<END
<script type="text/javascript">
$(function() {
	$("#headertabs").tabs();
	var curHash = window.location.hash;
	if ( curHash.indexOf( "#tab=" ) == 0 ) {
		var tabName = curHash.replace( "#tab=", "" );
		$("#headertabs").tabs('select', tabName);
	}
});
$(".tabLink").click( function() {
	var href = $(this).attr('href');
	var tabName = href.replace( "#tab=", "" );
	$("#headertabs").tabs('select', tabName);
	return false; //$htUseHistory;
} );
</script>

END;
			$wgOut->addScript($js_text);

		return true;
	}

	function renderSwitchTabLink( &$parser, $tabName, $linkText, $anotherTarget = '' ) {
		$tabTitle = Title::newFromText( $tabName );
		$tabKey = $tabTitle->getDBkey();

		if ( $anotherTarget != '' ) {
			$targetTitle = Title::newFromText( $anotherTarget );
			$targetURL = $targetTitle->getFullURL();

			$output = '<a href="' . $targetURL . '#tab=' . $tabKey . '">' . $linkText . '</a>';
		} else {
			$output =<<<END
<a href="#tab=$tabKey" class="tabLink">$linkText</a>
END;
	}

		return $parser->insertStripItem( $output, $parser->mStripState );
	}

}
