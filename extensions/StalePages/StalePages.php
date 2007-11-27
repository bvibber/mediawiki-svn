<?php
/** \file
* \brief Contains setup code for the Stale Pages Extension.
*/

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
        echo "Stale Pages extension";
        exit(1);
}
 
$wgExtensionFunctions[] = 'efStalepages';
$wgExtensionCredits['specialpage'][] = array(
    'name'=>'Stale Pages',
    'url'=>'http://www.mediawiki.org/wiki/Extension:Stale_Pages',
    'author'=>'Tim Laqua',
    'description'=>'Generates a list of pages that have not been edited recently',
    'version'=>'0.6'
);


$wgAutoloadClasses['Stalepages'] = dirname(__FILE__) . '/StalePages_body.php';

$wgSpecialPages['Stalepages'] = 'Stalepages';


function efAddStalePages( &$wgQueryPages) {
	$wgQueryPages['Stalepages'] = 'Stalepages';
}

function efStalePages() {
	global $wgMessageCache;
	
	#Add Messages
	require( dirname( __FILE__ ) . '/StalePages.i18n.php' );
	foreach( $stalePagesMessages as $key => $value ) {
		$wgMessageCache->addMessages( $stalePagesMessages[$key], $key );
	}
}