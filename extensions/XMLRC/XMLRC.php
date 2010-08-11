<?php

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo "XMLRC extension";
	exit(1);
}

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'author' => array( 'Daniel Kinzler' ),
	'name' => 'XMLRC',
	'url' => 'http://www.mediawiki.org/wiki/Extension:XMLRC',
	'descriptionmsg'=> 'xmlrc-desc',	
);

# Internationalisation file
$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['XMLRC'] = $dir . 'XMLRC.i18n.php';

$wgXMLRCTransport = null;

#$wgXMLRCTransport = array(
#  'class' => 'XMLRC_File',
#  'file' => '/tmp/rc.xml',
#);

#$wgXMLRCTransport = array(
#  'class' => 'XMLRC_UDP',
#  'port' => 4455,
#  'address' => '127.0.0.1',
#);

$wgXMLRCProperties = 'user|comment|flags|timestamp|title|ids|sizes|redirect|loginfo'; # sensible default
# $wgXMLRCProperties = 'title|timestamp|ids'; # default as per the API
# $wgXMLRCProperties = 'user|comment|parsedcomment|flags|timestamp|title|ids|sizes|redirect|loginfo|tags'; # everything except "patrolled", which is verboten

/*
$wgXMLRCTransport = array(
  'class' => 'XMLRC_XMPP',
  'channel' => 'recentchanges',
  'nickname' => $wgSitename,
  'host' => 'localhost',
  'port' => 5222,
  'user' => 'mediawiki',
  'server' => 'localhost',
  'resource' => 'recentchanges',
  'password' => 'yourpassword',
  'include_path' => './xmpphp',
);
*/

/*
$wgXMLRCTransport = array(
  'class' => 'XMLRC_UDP',
  'address' => 'localhost',
  'port' => 12345,
);
*/

$wgAutoloadClasses[ 'XMLRC' ] = "$dir/XMLRC.class.php";
$wgAutoloadClasses[ 'XMLRC_Transport' ] = "$dir/XMLRC.class.php";
$wgAutoloadClasses[ 'XMLRC_XMPP' ] = "$dir/XMLRC_XMPP.class.php";
$wgAutoloadClasses[ 'XMLRC_UDP' ] = "$dir/XMLRC_UDP.class.php";
$wgAutoloadClasses[ 'XMLRC_File' ] = "$dir/XMLRC_File.class.php";

$wgHooks['RecentChange_save'][] = 'XMLRC::RecentChange_save';
