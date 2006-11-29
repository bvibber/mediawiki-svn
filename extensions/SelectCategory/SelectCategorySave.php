<?php

# Setup and Hooks for the SelectCategory extension, an extension of the
# edit box of MediaWiki to provide an easy way to add category links
# to a specific page.

# @package MediaWiki
# @subpackage Extensions
# @author Leon Weber <leon.weber@leonweber.de> & Manuel Schneider <manuel.schneider@wikimedia.ch>
# @copyright © 2006 by Leon Weber & Manuel Schneider
# @licence GNU General Public Licence 2.0 or later

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die();
}

## Entry point for the hook and main worker function:
function fnSelectCategorySaveHook( &$article, &$user, &$m_text, &$summary, $minor, $watch, $sectionanchor, &$flags ) {
	global $wgContLang;
	
	# Get localised namespace string:
	$m_catString = $wgContLang->getNsText( NS_CATEGORY );
	# Get some distance from the rest of the content:
	$m_text .= "\n";
	# Iterate through all selected category entries:
	foreach( $_POST['SelectCategoryList'] as $m_cat ) {
		$m_text .= "\n[[$m_catString:$m_cat]]";
	}

	# Return to the let MediaWiki do the rest of the work:
	return true;
}
?>