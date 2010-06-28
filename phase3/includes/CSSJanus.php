<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 */

/**
 * This is a PHP port of CSSJanus, a utility that transforms CSS style sheets
 * written for LTR to RTL.
 * 
 * The original Python version of CSSJanus is Copyright 2008 by Google Inc. and
 * is distributed under the Apache license. 
 * 
 * Original code: http://code.google.com/p/cssjanus/source/browse/trunk/cssjanus.py
 * License of original code: http://code.google.com/p/cssjanus/source/browse/trunk/LICENSE
 * @author Roan Kattouw
 *
 */
class CSSJanus {
	// Patterns defined as null are built dynamically by buildPatterns()
	// We wrap tokens in ` , not ~ like the original implementation does.
	// This was done because ` is not a legal character in CSS and can only
	// occur in URLs, where we escape it to %60 before inserting our tokens.
	private static $patterns = array(
		'newline' => '/\r?\n/',
		'newlineToken' => '`NL`',
		'tmpToken' => '`TMP`',
		'nonAscii' => '[\200-\377]',
		'unicode' => '((\\[0-9a-f]{1,6})(\r\n|\s)?)',
		'body_selector' => 'body\s*{\s*',
		'direction' => 'direction\s*:\s*',
		'escape' => null,
		'nmchar' => null,
		'lookahead_not_open_brace' => null,
		'chars_within_selector' => '[^\}]*?',
		'noflip_annotation' => '\/\*\s*@noflip\s*\*\/',
		'noflip_single' => null,
		'noflip_class' => null,
		'comment' => '/\/\*[^*]*\*+([^\/*][^*]*\*+)*\//',
		'body_direction_ltr' => null,
		'body_direction_rtl' => null,
	);
	
	private static function buildPatterns() {
		$patterns =& self::$patterns;
		$patterns['escape'] = "({$patterns['unicode']}|\\[^\r\n\f0-9a-f])";
		$patterns['nmchar'] = "([_a-z0-9-]|{$patterns['nonAscii']}|{$patterns['escape']})";
		$patterns['lookahead_not_open_brace'] = "(?!({$patterns['nmchar']}|{$patterns['newlineToken']}|\s|#|\:|\.|\,|\+|>)*?{)";
		$pattners['chars_within_selector'] = '[^\}]*?';
		
		$patterns['noflip_single'] = "/({$patterns['noflip_annotation']}{$patterns['lookahead_not_open_brace']}[^;}]+;?)/i";
		$patterns['noflip_class'] = "/({$patterns['noflip_annotation']}{$patterns['chars_within_selector']}})/i";
		$patterns['body_direction_ltr'] = "/({$patterns['body_selector']}{$patterns['chars_within_selector']}{$patterns['direction']})ltr/i";
		$patterns['body_direction_rtl'] = "/({$patterns['body_selector']}{$patterns['chars_within_selector']}{$patterns['direction']})rtl/i";
	}
	
	/**
	 * Transform an LTR stylesheet to RTL
	 * @param string $css Stylesheet to transform
	 * @param bool $swapLtrRtlInURL If true, swap 'ltr' and 'rtl' in URLs
	 * @param bool $swapLeftRightInURL If true, swap 'left' and 'right' in URLs
	 * @return Transformed stylesheet
	 */
	public function transform( $css, $swapLtrRtlInURL = false, $swapLeftRightInURL = false ) {
		// TODO: Escape ` to %60
		self::buildPatterns(); // TODO: Conditionally
		// Tokenize newlines so all CSS is on one line
		$newlines = new CSSJanus_Tokenizer( self::$patterns['newline'], self::$patterns['newlineToken'] );
		$css = $newlines->tokenize( $css );
		
		// Tokenize single line rules with /* @noflip */
		$noFlipSingle = new CSSJanus_Tokenizer( self::$patterns['noflip_single'], '`NOFLIP_SINGLE`' );
		$css = $noFlipSingle->tokenize( $css );
		
		// Tokenize class rules with /* @noflip */
		$noFlipClass = new CSSJanus_Tokenizer( self::$patterns['noflip_class'], '`NOFLIP_CLASS`' );
		$css = $noFlipClass->tokenize( $css );
		
		// Tokenize comments
		$comments = new CSSJanus_Tokenizer( self::$patterns['comment'], '`C`' );
		$css = $comments->tokenize( $css );
		
		// LTR->RTL fixes start here
		$css = self::fixBodyDirection( $css );
		if ( $swapLtrRtlInURL ) {
			$css = self::fixLtrRtlInURL( $css );
		}
		if ( $swapLeftRightInURL ) {
			$css = self::fixLeftRightInURL( $css );
		}
		$css = self::fixLeftAndRight( $css );
		$css = self::fixCursorProperties( $css );
		$css = self::fixFourPartNotation( $css );
		$css = self::fixBackgroundPosition( $css );
		
		// Detokenize stuff we tokenized before
		$css = $comments->detokenize( $css );
		$css = $noFlipClass->detokenize( $css );
		$css = $noFlipSingle->detokenize( $css );
		$css = $newlines->detokenize( $css );
		return $css;
	}
	
	private static function fixBodyDirection( $css ) {
		$css = preg_replace( self::$patterns['body_direction_ltr'],
			'$1$2$3' . self::$patterns['tmpToken'], $css );
		$css = preg_replace( self::$patterns['body_direction_rtl'], '$1$2$3ltr', $css );
		$css = str_replace( self::$patterns['tmpToken'], 'rtl', $css );
		return $css;
	}
	
	private static function fixLtrRtlInURL( $css ) {
		// TODO
		return $css;
	}
	
	private static function fixLeftRightInURL( $css ) {
		// TODO
		return $css;
	}
	
	private static function fixLeftAndRight( $css ) {
		// TODO
		return $css;
	}
	
	private static function fixCursorProperties( $css ) {
		// TODO
		return $css;
	}
	
	private static function fixFourPartNotation( $css ) {
		// TODO
		return $css;
	}
	
	private static function fixBackgroundPosition( $css ) {
		// TODO
		return $css;
	}
}

/**
 * Utility class used by CSSJanus that tokenizes and untokenizes things we want
 * to protect from being janused.
 * @author Roan Kattouw
 */
class CSSJanus_Tokenizer {
	private $regex, $token;
	private $originals;
	
	/**
	 * Constructor
	 * @param $regex string Regular expression whose matches to replace by a token.
	 * @param $token string Token
	 */
	public function __construct( $regex, $token ) {
		$this->regex = $regex;
		$this->token = $token;
		$this->originals = array();
	}
	
	/**
	 * Replace all occurrences of $regex in $str with a token and remember
	 * the original strings. 
	 * @param $str string String to tokenize
	 * @return string Tokenized string
	 */
	public function tokenize( $str ) {
		return preg_replace_callback( $this->regex, array( $this, 'tokenizeCallback' ), $str );
	}
	
	private function tokenizeCallback( $matches ) {
		$this->originals[] = $matches[0];
		return $this->token;
	}
	
	/**
	 * Replace tokens with their originals. If multiple strings were tokenized, it's important they be
	 * detokenized in exactly the SAME ORDER.
	 * @param string $str String previously run through tokenize()
	 * @return string Original string
	 */
	public function detokenize( $str ) {
		// PHP has no function to replace only the first occurrence or to
		// replace occurrences of the same string with different values,
		// so we use preg_replace_callback() even though we don't really need a regex
		return preg_replace_callback( '/' . preg_quote( $this->token, '/' ) . '/',
			array( $this, 'detokenizeCallback' ), $str );
	}
	
	private function detokenizeCallback( $matches ) {
		$retval = current( $this->originals );
		next( $this->originals );
		return $retval;
	}
}