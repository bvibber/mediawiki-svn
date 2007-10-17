<?php

/// If true, notice only displays if 'sitenotice=yes' is in the query string
$wgNoticeTestMode = false;

/// Client-side cache timeout for the loader JS stub.
/// If 0, clients will (probably) rechceck it on every hit,
/// which is good for testing.
$wgNoticeTimeout = 0;

// http://meta.wikimedia.org/wiki/Special:NoticeLoader
$wgNoticeLoader = 'http://smorgasbord.local/trunk/index.php/Special:NoticeLoader';
$wgNoticeText = 'http://smorgasbord.local/trunk/index.php/Special:NoticeText';
$wgNoticeStyle = 'http://smorgasbord.local/trunk/index.php?title=MediaWiki:Centralnotice-style&action=raw&ctype=text/css';

//$wgNoticeEpoch = '20071003183510';
$wgNoticeEpoch = gmdate( 'YmdHis', @filemtime( dirname( __FILE__ ) . '/SpecialNoticeText.php' ) );

$wgNoticeLang = 'en';
$wgNoticeProject = 'wikipedia';

$wgHooks['BeforePageDisplay'][] = 'wfCentralNoticeStyleHook';

function wfCentralNoticeStyleHook( $output ) {
	global $wgNoticeStyle;
	$output->addLink(
		array(
			'rel' => 'stylesheet',
			'href' => $wgNoticeStyle ) );
	return true;
}

function wfCentralNotice( &$notice ) {
	global $wgNoticeLoader, $wgNoticeLang, $wgNoticeProject;
	
	// Throw away the classic notice, use the central loader...
	
	$encNoticeLoader = htmlspecialchars( $wgNoticeLoader );
	$encScript = Xml::encodeJsVar( <<<EOT
<script type="text/javascript">
console.log("Loading notice...");
</script>
<script type="text/javascript" src="$encNoticeLoader"></script>
<script type="text/javascript">
console.log("Notice is... " + wgNotice);
if (wgNotice != '') {
	document.getElementById('siteNotice').innerHTML = wgNotice;
}
</script>
EOT
		 );
	
	$encProject = Xml::encodeJsVar( $wgNoticeProject );
	$encLang = Xml::encodeJsVar( $wgNoticeLang );
	$notice = <<<EOT
<script type="text/javascript">
var wgNotice="";
var wgNoticeLang=$encLang;
var wgNoticeProject=$encProject;
console.log("adding hook...");
addOnloadHook(function(){console.log("running hook...");document.writeln($encScript);});
</script>
EOT;
	
	return true;
}

$wgHooks['SiteNoticeAfter'][] = 'wfCentralNotice';

$wgAutoloadClasses['NoticePage'] =
	dirname( __FILE__ ) . '/NoticePage.php';

$wgSpecialPages['NoticeLoader'] = 'SpecialNoticeLoader';
$wgAutoloadClasses['SpecialNoticeLoader'] =
	dirname( __FILE__ ) . '/SpecialNoticeLoader.php';

$wgSpecialPages['NoticeText'] = 'SpecialNoticeText';
$wgAutoloadClasses['SpecialNoticeText'] =
	dirname( __FILE__ ) . '/SpecialNoticeText.php';
