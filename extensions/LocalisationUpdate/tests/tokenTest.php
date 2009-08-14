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
	$ce = new ConfEditor("<?php $php");
	$vars = $ce->getVars();
	$retval = @$vars[$varname];
	return $retval;
}

$sources = glob("$IP/languages/messages/Messages*.php");

foreach( $sources as $sourceFile ) {
	$sourceData = file_get_contents( $sourceFile );
	$sourceData = preg_replace( "/<\\?php/", "", $sourceData );
	$sourceData = preg_replace( "/\?" . ">/", "", $sourceData );

	$start = microtime(true);
	$eval = evalExtractArray( $sourceData, 'messages' );
	$deltaEval = microtime(true) - $start;
	
	$start = microtime(true);
	$token = confExtractArray( $sourceData, 'messages' );
	$deltaToken = microtime(true) - $start;
	
	$hashEval = md5(serialize($eval));
	$hashToken = md5(serialize($token));
	
	$rel = wfRelativePath( $sourceFile, $IP );
	printf( "%s %s %0.1f - eval\n", $rel, $hashEval, $deltaEval * 1000 );
	printf( "%s %s %0.1f - token\n", $rel, $hashToken, $deltaToken * 1000 );
	
	if( $hashEval !== $hashToken ) {
		file_put_contents( 'eval.txt', var_export( $eval, true ) );
		file_put_contents( 'token.txt', var_export( $token, true ) );
		die("check eval.txt and token.txt\n");
	}
}

echo "ok\n";
