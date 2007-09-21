<?php

/**
 * Some tags to access i18n function in language files
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Niklas Laxström
 */

if( defined( 'MEDIAWIKI' ) ) {

	$wgExtensionFunctions[] = 'efSetupPIT';

	$wgExtensionCredits['parserhook'][] = array(
		'name' => 'Parser i18n tags',
		'description' => 'Access the i18n functions for number formatting, ' .
			'grammar and plural in any available language',
		'version' => '2.1',
		'author' => 'Niklas Laxström',
	);

	function efSetupPIT() {
		global $wgParser;
		$wgParser->setHook( 'formatnum', 'efFormatnum'  );
		$wgParser->setHook( 'grammar',   'efGrammar' );
		$wgParser->setHook( 'plural',    'efPlural'  );
		$wgParser->setHook( 'linktrail', 'efLinkTrail'  );
	}

	function efFormatnum( $data, $params, $parser ) {
		$lang = efGetLangObject( $params );
		return $lang->formatNum($data);
	}

	function efGrammar( $data, $params, $parser ) {
		if ( isset($params['case']) ) {
			$case = $params['case'];
		} else {
			$case = "";
		}
		$lang = efGetLangObject( $params );
		return $lang->convertGrammar($data, $case);
	}

	function efPlural( $data, $params, $parser ) {
		if ( isset($params['n']) ) {
			$n = intval($params['n']);
		} else {
			$n = intval(rand()/rand()*1020);
		}
		$args = explode('|', $data);
		while ( count($args) < 5 ) { $args[] = $args[count($args)-1]; }
		$lang = efGetLangObject( $params );
		$t = $lang->convertPlural( $n, $args[0], $args[1], $args[2], $args[3], $args[4]);
		return wfMsgReplaceArgs($t, array($n, 'NOT DEFINED'));
	}

	function efLinkTrail( $data, $params, $parser ) {
		$lang = efGetLangObject( $params );
		$regex = $lang->linkTrail();

		$inside = '';
		if ( '' != $data ) {
			$predata = array();
			preg_match( '/^\[\[([^\]|]+)(\|[^\]]+)?\]\](.*)$/sDu', $data, $predata );
			$m = array();
			if ( preg_match( $regex, $predata[3], $m ) ) {
				$inside = $m[1];
				$data = $m[2];
			}
		}
		$predata = isset( $predata[2] ) ? $predata[2] : isset( $predata[1] ) ? $predata[1] : $predata[0];
		return "<b>$predata$inside</b>$data";
	}

	function efGetLangObject( $params ) {
		global $wgContLang;
		return isset( $params['lang'] ) ? Language::factory( $params['lang'] ) : $wgContLang;
	}

} else {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	exit( 1 );
}

?>