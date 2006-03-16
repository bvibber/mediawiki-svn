<?php

require_once ( "filter_named_entities.php" ) ; # PHP4 and early PHP5 bug workaround
require_once ( "wiki2xml.php" ) ;
require_once ( "content_provider.php" ) ;

# A funtion to remove directories and subdirectories
# Modified from php.net
function SureRemoveDir($dir) {
   if(!$dh = @opendir($dir)) return;
   while (($obj = readdir($dh))) {
     if($obj=='.' || $obj=='..') continue;
     if (!@unlink($dir.'/'.$obj)) {
         SureRemoveDir($dir.'/'.$obj);
     }
   }
   @closedir ( $dh ) ;
   @rmdir($dir) ;
}

/**
 * The main converter class
 */
class MediaWikiConverter {

	/**
	 * Converts a single article in MediaWiki format to XML
	 */
	function article2xml ( $title , &$text , $params = array () ) {
		$title = urlencode ( str_replace ( "_" , " " , $title ) ) ;
		$p = new wiki2xml ;
		$p->auto_fill_templates = $params['resolvetemplates'] ;
		$xml = '<article' ;
		if ( $title != "" ) $xml .= " title='{$title}'" ;
		$xml .= '>' ;
		$xml .= $p->parse ( $text ) . "</article>" ;
		return $xml ;
	}
	
	/**
	 * Converts XML to plain text
	 */
	function articles2text ( &$xml , $params = array () ) {
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
	
	/**
	 * Converts XML to DocBook XML
	 */
	function articles2docbook_xml ( &$xml , $params = array () , $use_gpl = false ) {
		require_once ( "./xml2docbook_xml.php" ) ;

		$x2t = new xml2php ;
		$tree = $x2t->scanString ( $xml ) ;

		# Chosing DTD; parameter-given or default
		$dtd = "" ;
		if ( isset ( $params['docbook']['dtd'] ) )
			$dtd = $params['docbook']['dtd'] ;
		if ( $dtd == "" ) $dtd = 'http://www.oasis-open.org/docbook/xml/4.4/docbookx.dtd' ;

		$out = "<?xml version='1.0' encoding='UTF-8' ?>\n" ;
		$out .= '<!DOCTYPE book PUBLIC "-//OASIS//DTD DocBook XML V4.4//EN" "' . $dtd . '"' ;
		if ( $use_gpl ) {
			$out .= "\n[<!ENTITY gpl SYSTEM \"gpl.xml\">]\n" ;
		}
		$out .= ">\n\n<book>\n" ;
		$out .= trim ( $tree->parse ( $tree ) ) ;
		if ( $use_gpl ) {
			$out .= "\n&gpl;\n" ;
		}
		$out .= "\n</book>\n" ;

		return $out ;
	}
	
	/**
	 * Converts XML to PDF via DocBook
	 * Requires special parameters in local.php to be set (see sample_local.php)
	 * Uses articles2docbook_xml
	 */
	function articles2docbook_pdf ( &$xml , $params = array () ) {
		$docbook_xml = $this->articles2docbook_xml ( $xml , $params , true ) ;
		
		# Create temporary directory
		$temp_dir = "MWC" ;
		$temp_dir .= substr ( mt_rand() , 0 , 4 ) ;
		$temp_dir = tempnam ( $params['docbook']['temp_dir'], $temp_dir ) ;
		$project = basename ( $temp_dir ) ;
		unlink ( $temp_dir ) ; # It is currently a file, so...
		mkdir ( $temp_dir ) ;
		
		# Write XML file
		$xml_file = $temp_dir . "/" . $project . ".xml" ;
		$handle = fopen ( $xml_file , 'wb' ) ;
		fwrite ( $handle , utf8_encode ( $docbook_xml ) ) ;
		fclose ( $handle ) ;
		copy ( "./gpl.xml" , $temp_dir . "/gpl.xml" ) ;
		
		# Call converter
		$command = str_replace ( "%1" , $project , $params['docbook']['command_pdf'] ) ;
		exec ( $command ) ;
		
		# Cleanup xml file
		SureRemoveDir ( $temp_dir ) ;
		
		# Return pdf filename
		$ret = $params['docbook']['out_dir'] . '/' . $project . '/pdf/' . $project . '.pdf' ;
		return $ret ;
	}
}


?>