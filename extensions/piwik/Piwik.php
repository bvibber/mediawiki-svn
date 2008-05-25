<?php
/**
 * Parser hook extension to add a <randomimage> tag
 *
 * @addtogroup Extensions
 * @author isb1009 <isb1009 at gmail dot com>
 * @copyright © 2008 isb1009
 * @licence GNU General Public Licence 2.0
 */




if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionCredits['other'][] = array(
	'name'           => 'Piwik Integration',
	'version'        => '0.2 Alpha',
	'author'         => 'isb1009',
	'description'    => 'Inserts Piwik script into MediaWiki pages for tracking. Based on Google Analytics Integration by Tim Laqua.',
	'descriptionurl' => 'piwik-desc',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Piwik_Integration',
);



$wgHooks['SkinAfterBottomScripts'][]  = 'efPiwikHookText';

$wgPiwikIDSite = "";
$wgPiwikURLpiwikjs = "";
$wgPiwikURLpiwikphp = "";
$wgPiwikIgnoreSysops = true;
$wgPiwikIgnoreBots = true;


function efPiwikHookText(&$skin, &$text='') {
	$text .= efAddPiwik();
	return true;
}

function efAddPiwik() {
	global $wgPiwikIDSite, $wgPiwikURLpiwikjs, $wgPiwikURLpiwikphp, $wgPiwikIgnoreSysops, $wgPiwikIgnoreBots, $wgUser;
	if (!$wgUser->isAllowed('bot') || !$wgPiwikIgnoreBots) {
		if (!$wgUser->isAllowed('protect') || !$wgPiwikIgnoreSysops) {
			if ( !empty($wgPiwikIDSite) AND !empty($wgPiwikURLpiwikjs) AND !empty($wgPiwikURLpiwikphp)) {
				$funcOutput = <<<PIWIK
<!-- Piwik -->
<a href="http://piwik.org" title="Web analytics" onclick="window.open(this.href);return(false);">
<script language="javascript" src="{$wgPiwikURLpiwikjs}" type="text/javascript"></script>
<script type="text/javascript">
<!--
piwik_action_name = '';
piwik_idsite = {$wgPiwikIDSite};
piwik_url = '{$wgPiwikURLpiwikphp}';
piwik_log(piwik_action_name, piwik_idsite, piwik_url);
		if( source.className == "image" ) {
		_pk_link_type = 'link';
		_pk_not_site_hostname = 0;
		}
//-->
</script><object>
<noscript><p>Web analytics <img src="{$wgPiwikURLpiwikphp}" style="border:0" alt="piwik"/></p>
</noscript></object></a>
<!-- /Piwik -->
PIWIK;
			} else {
				$funcOutput = "\n<!-- You need to set the settings for Piwik -->";
			}
		} else {
			$funcOutput = "\n<!-- Piwik tracking is disabled for users with 'protect' rights (i.e., sysops) -->";
		}
	} else {
		$funcOutput = "\n<!-- Piwik tracking is disabled for bots -->";
	}

	return $funcOutput;
}

// Alias for efAddPiwik - backwards compatibility.
function addPiwik() {
	return efAddPiwik();
}
?>
