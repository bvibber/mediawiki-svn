<?php

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
# http://www.gnu.org/copyleft/gpl.html



class DoubleWiki {

	/*
	 * Tags that must be closed. (list copied from Sanitizer.php)
	 */
	var $tags = "/<\/?(b|del|i|ins|u|font|big|small|sub|sup|h1|h2|h3|h4|h5|h6|cite|code|em|s|strike|strong|tt|tr|td|var|div|center|blockquote|ol|ul|dl|table|caption|pre|ruby|rt|rb|rp|p|span)([\s](.*?)>|>)/i";

	/**
	 * Constructor
	 */
	function DoubleWiki() {
		global $wgParser, $wgHooks;
		$wgParser->setHook( 'iw_align' , array( &$this, 'iw_align' ) );
		$wgHooks['OutputPageBeforeHTML'][] = array( &$this, 'addMatchedText' );
	}

	/*
	 * Wrap the list of matched phrases into a hidden element.
	 */
	function iw_align( $input, $args, $parser ) { 
		if ( isset( $args['lang'] ) ) {
			$lang = $args['lang'];
			return "<div id=\"align-$lang\" style=\"display:none;\">\n" . trim( $input ). "\n</div>";
		}
		return '';
	}

	/*
	 * Read the list of matched phrases and add tags to the html output.
	 */
	function addMatchingTags ( &$text, $lang ) { 
		$pattern = "/<div id=\"align-$lang\" style=\"display:none;\">\n<p>([^<]*?)<\/p>\n<\/div>/is"; 
		if( ! preg_match( $pattern, $text, $m ) ) return ;
		$text = str_replace( $m[1], '', $text );
		$line_pattern = "/\s*([^\|\n]*?)\s*\|\s*([^\|\n]*?)\s*\n/i"; 
		preg_match_all( $line_pattern, $m[1], $items, PREG_SET_ORDER );
		foreach( $items as $n => $i ) {
			$text = str_replace( $i[1], "<span id=\"dw-$n\" title=\"{$i[2]}\"/>".$i[1], $text );
		}
	}

	/*
	 * Hook function called with &match=lang
	 * Transform $text into a bilingual version
	 */
	function addMatchedText ( &$parserOutput , &$text ) { 

		global $wgContLang, $wgRequest, $wgLang, $wgContLanguageCode, $wgTitle;

		$match_request = $wgRequest->getText( 'match' );
		if ( $match_request === '' ) { 
			return true;
		}
		$this->addMatchingTags ( &$text, $match_request );

		foreach( $parserOutput->mLanguageLinks as $l ) {
			$nt = Title::newFromText( $l );
			$iw = $nt->getInterwiki();
			if( $iw === $match_request ){
				$url =  $nt->getFullURL(); 
				$myURL = $wgTitle -> getLocalURL() ;
				$languageName = $wgContLang->getLanguageName( $nt->getInterwiki() );
				$myLanguage = $wgLang->getLanguageName( $wgContLanguageCode );

				$sep = ( in_string( '?', $url ) ) ? '&' : '?'; 
				$translation = Http::get( $url.$sep.'action=render' );
				if ( $translation !== null ) {
					#first find all links that have no 'class' parameter.
					#these links are local so we add '?match=xx' to their url, 
					#unless it already contains a '?' 
					$translation = preg_replace( 
						"/<a href=\"http:\/\/([^\"\?]*)\"(([\s]+)(c(?!lass=)|[^c\>\s])([^\>\s]*))*\>/i",
						"<a href=\"http://\\1?match={$wgContLanguageCode}\"\\2>", $translation );
					#now add class='extiw' to these links
					$translation = preg_replace( 
						"/<a href=\"http:\/\/([^\"]*)\"(([\s]+)(c(?!lass=)|[^c\>\s])([^\>\s]*))*\>/i",
						"<a href=\"http://\\1\" class=\"extiw\"\\3>", $translation );
					#use class='extiw' for images too
					$translation = preg_replace(
						"/<a href=\"http:\/\/([^\"]*)\"([^\>]*)class=\"image\"([^\>]*)\>/i",
						"<a href=\"http://\\1\"\\2class=\"extiw\"\\3>", $translation );

					#add prefixes to internal links, in order to prevent duplicates
					$translation = preg_replace("/<a href=\"#(.*?)\"/i","<a href=\"#l_\\1\"",
								    $translation );
					$translation = preg_replace("/<li id=\"(.*?)\"/i","<li id=\"l_\\1\"",
								    $translation );
					$text = preg_replace("/<a href=\"#(.*?)\"/i","<a href=\"#r_\\1\"", $text );
					$text = preg_replace("/<li id=\"(.*?)\"/i","<li id=\"r_\\1\"", $text );

					#add ?match= to local links of the local wiki
					$text = preg_replace( "/<a href=\"\/([^\"\?]*)\"/i",
							"<a href=\"/\\1?match={$match_request}\"", $text );

					#do the job
					$text = $this->matchColumns ( $text, $myLanguage, $myURL, $wgContLanguageCode, 
							       $translation, $languageName, $url, $match_request );
				}
				return true;
			}
		}
		return true;
	}


	/*
	 * Format the text as a two-column table with aligned paragraphs
	 */
	function matchColumns( $left_text, $left_title, $left_url, $left_lang,
			       $right_text, $right_title, $right_url, $right_lang ) {

		list( $left_slices, $left_tags ) = $this->find_slices( $left_text );

		$body = '';
		$left_chunk = '';
		$right_chunk = ''; 

		for ( $i=0 ; $i < count($left_slices) ; $i++ ) {

			// some slices might be empty
			if( $left_slices[$i] == '' ) {
				continue; 
			}

			$found = false;
			$tag = $left_tags[1][$i];
			$left_chunk .= $left_slices[$i];

			# if we are at the end of the loop, finish quickly
			if ( $i== count( $left_slices ) - 1 ) { 
				$right_chunk .= $right_text;
				$found = true;
			} else {
				#look for requested tag in the text
				$a = strpos ( $right_text, $tag );
				if( $a ) {
					$found = true; 
					$sub = substr( $right_text, 0, $a);
					// detect the end of previous paragraph
					// regexp matches the rightmost delimiter
					if ( preg_match("/(.*)<\/(p|dl)>/is", $sub, $m ) ) {
						$right_chunk .= $m[0];
						$right_text = substr( $right_text, strlen($m[0]) );
					}
				#} else {
				#	print "<br/>tag not found ".$tag;
				}
			}

			if( $found && $right_chunk ) {
				// Detect paragraphs
				$left_bits  = $this->find_paragraphs( $left_chunk );
				$right_bits = $this->find_paragraphs( $right_chunk );

				// $body .= "<tr style=\"background-color:#ffdddd;\"><td>".count($left_bits)."</td><td>".count($right_bits)."</td></tr>\n";
				// Do not align paragraphs if counts are different
				if ( count( $left_bits ) != count( $right_bits ) ) {
					$left_bits  = Array( $left_chunk );
					$right_bits = Array( $right_chunk );
				}

				$left_chunk  = '';
				$right_chunk = '';
				for($l=0; $l < count( $left_bits ) ; $l++ ) {
					$body .= 
					  "<tr><td valign=\"top\" style=\"vertical-align:100%;padding-right: 0.5em\" lang=\"{$left_lang}\">"
					  ."<div style=\"width:35em; margin:0px auto\">\n".$left_bits[$l]."</div>"
					  ."</td>\n<td valign=\"top\" style=\"padding-left: 0.5em\" lang=\"{$right_lang}\">"
					  ."<div style=\"width:35em; margin:0px auto\">\n".$right_bits[$l]."</div>"
					  ."</td></tr>\n";
				}
			}
		}

		// format table head and return results
		$left_url = htmlspecialchars( $left_url );
		$right_url = htmlspecialchars( $right_url );
		$head = 
		  "<table id=\"doubleWikiTable\" width=\"100%\" border=\"0\" bgcolor=\"white\" rules=\"cols\" cellpadding=\"0\">
<colgroup><col width=\"50%\"/><col width=\"50%\"/></colgroup><thead>
<tr><td bgcolor=\"#cfcfff\" align=\"center\" lang=\"{$left_lang}\">
<a href=\"{$left_url}\">{$left_title}</a></td>
<td bgcolor=\"#cfcfff\" align=\"center\" lang=\"{$right_lang}\">
<a href=\"{$right_url}\" class='extiw'>{$right_title}</a>
</td></tr></thead>\n";
		return $head . $body . "</table>" ;
	}



	/*
	 * Split text and return a set of html-balanced paragraphs
	 */
	function find_paragraphs( $text ) {
		$result = Array();
		$bits = preg_split( $this->tags, $text );
		preg_match_all( $this->tags, $text, $m, PREG_SET_ORDER);
		$counter = 0;
		$out = '';
		for($i=0; $i < count($m); $i++){
			$t = $m[$i][0];
			if( substr( $t, 0, 2) != "</" ) {
				$counter++;
			} else {
				$counter--;
			}
			$out .= $bits[$i] . $t;
			if( ($t == "</p>" || $t == "</dl>" ) && $counter==0 ) {
				$result[] = $out;
				$out = '';
			}
		}
		if($out) {
			$result[] = $out;
		}
		return $result; 
	}


	/*
	 * Split text and return a set of html-balanced slices
	 */
	function find_slices( $left_text ) {

		$tag_pattern = "/<span id=\"dw-[^\"]*\" title=\"([^\"]*)\"\/>/i";
		$left_slices = preg_split( $tag_pattern, $left_text );
		preg_match_all( $tag_pattern, $left_text,  $left_tags, PREG_PATTERN_ORDER );
		$n = count( $left_slices);

		/* 
		 * Make slices that are full paragraphs
		 * If two slices correspond to the same paragraph, the second one will be empty
		 */
		for ( $i=0 ; $i < $n - 1 ; $i++ ) {
			$str = $left_slices[$i];
			if ( preg_match("/(.*)<(p|dl)>/is", $str, $m ) ) { 
				$left_slices[$i] = $m[1];
				$left_slices[$i+1] = substr( $str, strlen($m[1]) ) . $left_slices[$i+1];
			}
		}

		/* 
		 * Keep only slices that contain balanced html
		 * If a slice is unbalanced, we merge it with the next one.
		 * The first and last slices are compensated.
		 */
		$stack = array();
		$counter = 0;
		for( $i=0 ; $i < $n ; $i++) {
			$bits = preg_split( $this->tags, $left_slices[$i] );
			preg_match_all( $this->tags, $left_slices[$i], $m, PREG_SET_ORDER);
			$counter = 0;
			for($k=0 ; $k < count($m) ; $k++) {
				$t = $m[$k];
				if( substr( $t[0], 0, 2) != "</" ) {
					$counter++;
					array_push($stack, $t);
				} else {
					$tt = array_pop($stack);
					$counter--;
				}
			}
			if( $i==0 ) {
				$opening = '';
				$closure = '';
				for( $k=0; $k < $counter ; $k++ ) {
					$opening .= "<".$stack[$k][1].">";
					$closure = "</".$stack[$k][1].">" . $closure;
				}
				$left_slices[$i] = $left_slices[$i] . $closure;
			} else if( $i == $n - 1 ) {
				$left_slices[$i] = $opening . $left_slices[$i];
			} else if( $counter != 0 ) {
				$left_slices[$i+1] = $left_slices[$i] . $left_slices[$i+1];
				$left_slices[$i] = '';
			}
		}
		return array($left_slices, $left_tags);
	}

}
