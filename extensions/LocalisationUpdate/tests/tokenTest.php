<?php

$IP = strval( getenv( 'MW_INSTALL_PATH' ) ) !== ''
	? getenv( 'MW_INSTALL_PATH' )
	: realpath( dirname( __FILE__ ) . "/../../../" );

require_once( "$IP/maintenance/commandLine.inc" );

function evalExtractArray( $php, $varname ) {
	eval( $php );
	return @$$varname;
}

function confExtractArray( $php, $varname ) {
	try {
		$ce = new ConfEditor("<?php $php");
		$vars = $ce->getVars();
		$retval = @$vars[$varname];
	} catch( Exception $e ) {
		print $e . "\n";
		$retval = null;
	}
	return $retval;
}

if( count( $args ) ) {
	$sources = $args;
} else {
	$sources = 
		array_merge(
			glob("$IP/extensions/*/*.i18n.php"),
			glob("$IP/languages/messages/Messages*.php") );
}

foreach( $sources as $sourceFile ) {
	$rel = wfRelativePath( $sourceFile, $IP );
	$out = str_replace( '/', '-', $rel );
	
	$sourceData = file_get_contents( $sourceFile );
	
	if( preg_match( '!extensions/!', $sourceFile ) ) {
		$sourceData = LocalisationUpdate::cleanupExtensionFile( $sourceData );
		$items = 'langs';
	} else {
		$sourceData = LocalisationUpdate::cleanupFile( $sourceData );
		$items = 'messages';
	}
	
	file_put_contents( "$out.txt", $sourceData );

	$start = microtime(true);
	$eval = evalExtractArray( $sourceData, 'messages' );
	$deltaEval = microtime(true) - $start;
	
	$start = microtime(true);
	$token = confExtractArray( $sourceData, 'messages' );
	$deltaToken = microtime(true) - $start;
	
	$hashEval = md5(serialize($eval));
	$hashToken = md5(serialize($token));
	$countEval = count( (array)$eval);
	$countToken = count( (array)$token );
	
	printf( "%s %s %d $items - %0.1fms - eval\n", $rel, $hashEval, $countEval, $deltaEval * 1000 );
	printf( "%s %s %d $items - %0.1fms - token\n", $rel, $hashToken, $countToken, $deltaToken * 1000 );
	
	if( $hashEval !== $hashToken ) {
		echo "FAILED on $rel\n";
		file_put_contents( "$out-eval.txt", var_export( $eval, true ) );
		file_put_contents( "$out-token.txt", var_export( $token, true ) );
		#die("check eval.txt and token.txt\n");
	}
	echo "\n";
}

echo "ok\n";
