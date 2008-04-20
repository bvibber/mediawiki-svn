<?php

# Make an HTML table showing all the wikis on the site

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo "This file is part of MediaWiki, it is not a valid entry point.\n";
	exit(1);
}

$wgExtensionCredits['specialpage'][] = array(
	'name'           => 'SiteMatrix',
	'version' => preg_replace('/^.* (\d\d\d\d-\d\d-\d\d) .*$/', '\1', '$LastChangedDate$'), #just the date of the last change
	'url'            => 'http://www.mediawiki.org/wiki/Extension:SiteMatrix',
	'description'    => 'Displays a list of Wikimedia wikis',
	'descriptionmsg' => 'sitematrix-desc',
);

$dirname = dirname( __FILE__ );

$wgExtensionMessagesFiles['SiteMatrix'] = $dirname . '/SiteMatrix.i18n.php';

$wgSiteMatrixFile = '/home/wikipedia/common/langlist';

$wgAutoloadClasses['SiteMatrix'] = $dirname . '/SiteMatrix_body.php';
$wgAutoloadClasses['ApiQuerySiteMatrix'] = $dirname . '/SiteMatrix_body.php';
$wgAutoloadClasses['SiteMatrixPage'] = $dirname . '/SiteMatrix_body.php';
$wgAutoloadClasses['SiteMatrixParserFunctions'] = $dirname . '/SiteMatrix.funcs.php';
require_once( $dirname . '/SiteMatrix.funcs.i18n.php' );

$wgAPIModules['sitematrix'] = 'ApiQuerySiteMatrix';
$wgSpecialPages['SiteMatrix'] = 'SiteMatrixPage';
$wgExtensionFunctions[] = 'efSetupSiteMatrixFunctions';
$wgHooks['LanguageGetMagic'][] = 'SiteMatrixMagicI18n::getMagic';

function efSetupSiteMatrixFunctions() {
	globaL $wgParser, $wgHooks;

	$functions = SiteMatrixParserFunctions::singleton();
	if ( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) ) {
		$wgHooks['ParserFirstCallInit'][] = array( &$functions, 'registerParser' );
	} else {
		if ( class_exists( 'StubObject' ) && !StubObject::isRealObject( $wgParser ) ) {
			$wgParser->_unstub();
		}
		$functions->registerParser( $wgParser );
	}
}
