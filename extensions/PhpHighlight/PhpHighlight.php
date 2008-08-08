<?php

if( !defined( 'MEDIAWIKI' ) ) die();

/**
 * Extension that use the PHP syntax highlighter (php -s)
 *
 * @author Alexandre Emsenhuber
 * @license GPL v.2 or higher
 */

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'PHP highlight',
	'url' => 'http://www.mediawiki.org/wiki/Extension:PhpHighlight',
	'svn-date' => '$LastChangedDate$',
	'svn-revision' => '$LastChangedRevision$',
	'author' => 'Alexandre Emsenhuber',
	'description' => 'Adds a <code>&lt;php&gt;</code> tag to use the PHP syntax highlighter',
);

/**
 * Path to the php executable
 */
$wgPhpPath = 'php';

if( defined( 'MW_SUPPORTS_PARSERFIRSTCALLINIT' ) )
	$wgHooks['ParserFirstCallInit'][] = 'efSetPhp';
else
	$wgExtensionFunctions[] = 'efSetPhpOld';

function efSetPhpOld(){
	global $wgParser;
	efSetPhp( $wgParser );
}

function efSetPhp( &$parser ){
	$parser->setHook( 'php', 'efRenderPhp' );
	return true;
}

/**
 * Call back
 */
function efRenderPhp( $text ){
	global $wgPhpPath;
	$file = tempnam( wfTempDir(), 'highlight-' );
	file_put_contents( $file, $text );
	$html = wfShellExec( "{$wgPhpPath} -s {$file}" );
	unlink( $file );
	return str_replace( '<br />', "<br />\n", $html );
}
