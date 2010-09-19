<?php
/**
 * Initializing file for SMW RDFIO extension.
 *
 * @file
 * @ingroup RDFIO
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'RDFIO_VERSION', '0.5.0' );

global $wgExtensionCredits;

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'RDFIO',
	'version' => RDFIO_VERSION,
	'author' => '[http://saml.rilspace.org Samuel Lampa]',
	'url' => 'http://www.mediawiki.org/wiki/Extension:RDFIO',
	'descriptionmsg' => 'rdfio-desc',
);

/****************************
 * i18n
 ****************************/
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['RDFIO'] = $dir . 'RDFIO.i18n.php';
$wgExtensionAliasesFiles['RDFIO'] = $dir . 'RDFIO.alias.php';

/****************************
 * ARC2 RDF library for PHP *
 ****************************/

$smwgARC2Path = $smwgIP . '/libs/arc/';
require_once( $smwgARC2Path . '/ARC2.php' );

/**************************
 *  ARC2 RDF Store config *
 **************************/

$smwgARC2StoreConfig = array(
              /* Customize these details if you   *
               * want to use an external database */
                'db_host' => $wgDBserver,
                'db_name' => $wgDBname,
                'db_user' => $wgDBuser,
                'db_pwd' =>  $wgDBpassword,
                'store_name' => $wgDBprefix . 'arc2store',
);
$smwgDefaultStore = 'SMWARC2Store'; // Determines database table prefix

require_once( "$IP/extensions/RDFIO/stores/SMW_ARC2Store.php" );
require_once( "$IP/extensions/RDFIO/specials/SpecialARC2Admin.php" );

/**************************
 *   SMWWriter settings   *
 **************************/
// @todo FIXME: use auto loader where possible.
include_once( "$IP/extensions/PageObjectModel/POM.php" );
include_once( "$IP/extensions/SMWWriter/SMWWriter.php" );

/**************************
 *    RDFIO Components    *
 **************************/

require_once( "classes/Utils.php" );
require_once( "classes/RDFStore.php" );
require_once( "classes/SMWBatchWriter.php" );
require_once( "classes/PageHandler.php" );
require_once( "specials/SpecialRDFImport.php" );
require_once( "specials/SpecialSPARQLEndpoint.php" );
