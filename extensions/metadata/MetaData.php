<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "Not a valid entry point\n" );
}

$wgExtensionFunctions[] = 'wfMetaDataSetup';

function wfMetaDataActionHook( $action, &$article ) {
	global $wgParser, $wgOut;

	if ( $action !== 'meta' ) {
		// Continue hook chain
		return true;
	}
	
	if ( !is_object( $article ) ) {
		return wfMsg( 'metadata_noarticle' );
	}
	
	// Get article text
	$text = $article->fetchContent();
	if ( !is_string( $text ) ) {
		return wfMsg( 'metadata_noarticle' );
	}

	// Set up $wgOut
	$wgOut->setArticleFlag( false );
	$wgOut->setArticleRelated( true );
	$wgOut->setPageTitle( wfMsg( 'metadata_title', $article->mTitle->getPrefixedText() ) );
	
	// Parse it and extract the metadata key/value pairs
	$pout = $wgParser->parse( $text, $article->mTitle, new ParserOptions );

	if ( !is_array( $pout->mMetaData ) || count( $pout->mMetaData ) == 0 ) {
		$wgOut->addWikiText( wfMsg( 'metadata_nometa' ) );
		// Done with hook chain
		return false;
	}
	$s = '';
	foreach ( $pout->mMetaData as $pair ) {
		list( $key, $value ) = explode( '=', $pair, 2 );
		$s .= "$key = $value<br />";
	}
	$wgOut->addWikiText( $s );

	// Done with hook chain
	return false;
}

function wfMetaDataParserHook( $text, $params, &$parser ) {
	// Expand templates
	$text = $parser->replaceVariables( $text, $parser->mAssocArgs );
	$text = $parser->unstrip( $text, $parser->mStripState );
	$text = $parser->unstripNoWiki( $text, $parser->mStripState );

	// Initialise mMetaData
	if ( !isset( $parser->mOutput->mMetaData ) ) {
		$parser->mMetaData = array();
	}
	
	// Split lines
	$lines = array_map( 'trim', explode( "\n", $text ) );
	foreach ( $lines as $line ) {
		if ( preg_match( '/^(.*?)\s*=\s*(.*)$/', $line, $m ) ) {
			// Pair found
			// Put them into a standard form string and put them into an array
			// indexed by that string, thereby implicitly removing duplicates
			$standardForm = "{$m[1]}={$m[2]}";
			
			$parser->mOutput->mMetaData[$standardForm] = $standardForm;
		}
	}
	
	// Display text if requested
	if ( isset( $params['display'] ) ) {
		if ( ( $params['display'] == 'immediate' && !count( $parser->mArgStack ) ) 
       			|| $params['display'] == 'always' || $params['display'] === 'display' /*default*/ )
		{
			return $parser->internalParse( nl2br( $text ) );
		}
	}
	return '';
}

function wfMetaDataSetup() {
	global $wgParser, $wgHooks, $wgMessageCache;
	$wgParser->setHook( 'meta', 'wfMetaDataParserHook' );
	SpecialPage::addPage( new SpecialPage( 'AdvancedSearch', '', true, 
		'wfSpecialAdvancedSearch', 'extensions/MetaData/AdvancedSearch.php' ) );
	$wgHooks['UnknownAction'][] = 'wfMetaDataActionHook';
	$wgMessageCache->addMessages( array(
		'metadata_noarticle' => 'The specified article does not exist',
		'metadata_nometa' => 'No key/value pairs were found',
		'metadata_title' => 'Metadata for $1',
	));
}

?>
