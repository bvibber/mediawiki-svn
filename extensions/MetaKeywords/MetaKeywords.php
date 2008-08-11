<?php

/*
	Extension:MetaKeywords Copyright (C) 2008 Conrad.Irwin

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

//Ideas from http://mediawiki.org/wiki/Extension:Gadgets thanks to Duesentrieb
//           [[User:Mike Dillon]]

$wgExtensionCredits['other'][] = array(
	'name'           => 'MetaKeywords',
	'author'         => '[http://en.wiktionary.org/wiki/User:Conrad.Irwin Conrad Irwin]',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:MetaKeywords',
	'description'    => 'Lets wikis add meta keywords depending on namespace',
	'descriptionmsg' => 'metakeywords-desc',
);

$wgExtensionMessagesFiles['MetaKeywords'] = dirname( __FILE__ ) . '/MetaKeywords.i18n.php';

$wgHooks['BeforePageDisplay'][] = 'wfMetaKeywordOutput';
$wgHooks['ArticleSaveComplete'][] = 'wfMetaKeywordClearCache';

//Adds customised keywords after the pagename of the <meta keywords> tag
function wfMetaKeywordOutput( &$out ){
	global $wgTitle, $wgMemc;
	$ns = $wgTitle->getNamespace();

	//Keywords
	$opts = $wgMemc->get( "Metakeywords-opts" );
	if($opts === null ){ //Reload if not in cache
		$opts = wfMetaKeywordInput( 'keywords' );
	}
	$pagename = array_shift( $out->mKeywords );

	if( $opts[$ns] ){ //Namespace specific keywords
		array_unshift( $out->mKeywords, $opts[$ns]);
	}elseif( $opts['*'] ){ //Global keywords
		array_unshift( $out->mKeywords, $opts['*']);
	}
	if( $pagename ){ //No pagename for special pages
		array_unshift( $out->mKeywords, $pagename );
	}
	//Descriptions
	$opts = $wgMemc->get( "Metadescription-opts" );

	if($opts === null ){ //Reload if not in cache
		$opts = wfMetaKeywordInput( 'description', $pagename );
	}
	if( $opts[$ns] ){ //Namespace specific descrption
		$out->addMeta('description',$opts[$ns]);
	}elseif( $opts['*'] ){ //Otherwise global description
		$out->addMeta('description',$opts['*']);
	}
	return true;
}

//Reads [[MediaWiki:Meta$page]]
function wfMetaKeywordInput( $type, $arg = false ){
	global $wgContLang, $wgMemc, $wgDBname;

	if ($arg) {
		$params = wfMsgForContentNoTrans("meta$type", $arg);
	} else {
		$params = wfMsgForContentNoTrans("meta$type");
	}
	$opts = array(0);

	if (! wfEmptyMsg( "meta$type", $params ) ) {
	   $opts = wfMetaKeywordParse($params);
	}
	return $opts;
}

//Parses the syntax, ignores things it does not understand
function wfMetaKeywordParse( $params ){
	global $wgContLang;
	$lines = preg_split( '/(\r\n|\r|\n)/', $params );

	foreach( $lines as $l ){
		if( preg_match( '/^([^\|]+)\|(.+)$/',$l,$m ) ){
			$ns=false;

			if($m[1] == '(main)'){
				$ns=0;
			}elseif($m[1] == '(all)'){
				$ns='*';
			}elseif(is_numeric($m[1])){ //a namespace number
				$ns=$m[1];
			}else{ //normal namespace name
				$ns = $wgContLang->getNsIndex($m[1]);
			}
			if($ns !== false ){
				$opts[$ns] = $m[2];
			}
		}
	}
	return $opts;
}

//Updates the cache if [[MediaWiki:Metakeywords]] or [[MediaWiki:Metadescription]] has been edited
function wfMetaKeywordClearCache( &$article, &$wgUser, &$text ) {
	global $wgMemc;
		$title = $article->mTitle;

		if( $title->getNamespace() == NS_MEDIAWIKI){
			$tt = $title->getText();
			if( $tt == 'Metakeywords' || $tt == 'Metadescription' ) {
				$opts = wfMetaKeywordParse( $text );
				$wgMemc->set($tt.'-opts',$opts,900);
			}
		}
		return true;
}
