<?php
/**
 * Extension based on SpecialContributions for arhived revisions
 * Modifications made to SpecialContributions.php by Aaron Schulz
 * Key code snipets from HideRevision.php also modified for use here
 */

# Internationalisation
$wgExtensionFunctions[] = 'efLoadDeletedContribsMessages';

function efLoadDeletedContribsMessages() {
	global $wgMessageCache, $wgDeletedContribsMessages, $wgOut, $wgJsMimeType;
	# Internationalization
	require( dirname( __FILE__ ) . '/DeletedContributions.i18n.php' );
	require( dirname( __FILE__ ) . '/DeletedContributions_body.php' );
	foreach ( $wgDeletedContribsMessages as $lang => $langMessages ) {
		$wgMessageCache->addMessages( $langMessages, $lang );
	}
}

$wgSpecialPages['DeletedContributions'] = array( 'SpecialPage', 'DeletedContributions', 'delete',
		/*listed*/ true, /*function*/ false, /*file*/ false );

?>
