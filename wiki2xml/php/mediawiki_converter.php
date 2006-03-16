<?php

require_once ( "filter_named_entities.php" ) ; # PHP4 and early PHP5 bug workaround
require_once ( "wiki2xml.php" ) ;
require_once ( "content_provider.php" ) ;

class MediaWikiConverter {
	function article2xml ( $title , &$text , $params ) {
		$title = urlencode ( str_replace ( "_" , " " , $title ) ) ;
		$p = new wiki2xml ;
		$p->auto_fill_templates = $params['resolvetemplates'] ;
		$xml = '<article' ;
		if ( $title != "" ) $xml .= " title='{$title}'" ;
		$xml .= '>' ;
		$xml .= $p->parse ( $text ) . "</article>" ;
		return $xml ;
	}
	
	function articles2text ( &$xml , $params ) {
		require_once ( "./xml2txt.php" ) ;

		$x2t = new xml2php ;
		$tree = $x2t->scanString ( $xml ) ;
		if ( $params['plaintext_markup'] ) {
			$tree->bold = '*' ;
			$tree->italics = '/' ;
			$tree->underline = '_' ;
		}
		if ( $params['plaintext_prelink'] ) {
			$tree->pre_link = "&rarr;" ;
		}
		return trim ( $tree->parse ( $tree ) ) ;
	}
	
	function articles2docbook_xml ( &$xml , $xmlg ) {
		require_once ( "./xml2docbook_xml.php" ) ;

		$x2t = new xml2php ;
		$tree = $x2t->scanString ( $xml ) ;

		$dtd = "" ;
		if ( isset ( $params['docbook']['dtd'] ) )
			$dtd = $params['docbook']['dtd'] ;
		if ( $dtd == "" ) $dtd = 'http://www.oasis-open.org/docbook/xml/4.4/docbookx.dtd' ;

		$out = "<?xml version='1.0' encoding='UTF-8' ?>\n" ;
		$out .= '<!DOCTYPE book PUBLIC "-//OASIS//DTD DocBook XML V4.4//EN" "' . $dtd . '">' ;
		$out .= "\n\n" ;
		$out .= trim ( $tree->parse ( $tree ) ) ;

		return $out ;
	}
}


?>