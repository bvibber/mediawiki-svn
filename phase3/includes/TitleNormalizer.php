<?php

/**
 * External class for the normalization/backconversion of titles.
 * 
 * This is a replacement for the old secureAndSplit function used
 * inside of Title.php.
 * 
 * To customize title handling one may use the helper methods to
 * alter the order and actions of the pass sequences in the pass
 * groups, or even replace or remove them.
 * If that is not enough for customization, then this class may
 * be subclassed and $wgTitleNormalizerClass may be set to the new class
 * to replace the normal one here. 
 *  
 * @author Daniel Friesen (dantman)
 */

class TitleNormalizer {
	
	private $mIllegalCharacters;
	private $mPassGroups, $mPassSequences;
	
	function __construct() {
		// Build regex for illegal chaacters.
		$this->mIllegalCharacters = '/' .
			# Any character not allowed is forbidden...
			'[^' . Title::legalChars() . ']' .
			# URL percent encoding sequences interfere with the ability
			# to round-trip titles -- you can't link to them consistently.
			'|%[0-9A-Fa-f]{2}' .
			# XML/HTML character references produce similar issues.
			'|&[A-Za-z0-9\x80-\xff]+;' .
			'|&#[0-9]+;' .
			'|&#x[0-9A-Fa-f]+;' .
			'/S';
		
		// Construct the Standard Pass Groups and Sequences
		$this->mPassGroups = array(
			// Starting sequences for pre-pass enforcement of data reqirements.
			'init',
			// First check for illegal characters and clean them out
			'initial-sanitize',
			// Split up the various parts of the title
			'split',
			// Sanitize the actual title portion itself
			'sanitize',
			// Do final output changes to the data
			'finalize',
		);
		
		$this->mPassSequences = array(
			'init' => array(
				'enforceRequiredData' => array( 'enforceRequiredData' ),
			),
			
			'finalize' => array(
				'finalizeOutputData' => array( 'finalizeOutputData' )
			),
		);
	}
	
	/** 
	 * Take a set of title data and normalize it out into a usable
	 * title data object.
	 * 
	 * Standard data:
	 * articleid - (Output only) [Dev note, don't know why I
	 * put this here, I'll look over things and see if it has use] 
	 * interwiki - (Output only) Interwiki prefix for the title. 
	 * namespace - Numeric id of the title's namespace.
	 *             (Input is default namespace to use)
	 * dbkey - Key format of the title
	 * usercasedbkey - (Output only) DBKey form with case
	 *                 specified by input for interwiki use.
	 * textform - Display format of the title
	 * urlform - (Output only) DBKey form ready for URL output
	 * fragment - (Output only) Fragment after the #
	 * 
	 * @access public
	 * @param mixed $titleData Array or Object containing keys
	 *  of the above standard data. At minimum dbkey or textform
	 *  MUST be passed, but cannot be passed together.
	 *  If textform is not passed then the title will be normalized
	 *  along key standards only and textform will remain a empty
	 *  string to be filled latter by Title::getText();
	 * @return object Altered form of the $titleData input containing the final data.
	 */
	function normalize( $titleData ) {
		// Only accept valid types
		switch( typeof($titleData) ) {
			case 'array': break;
			case 'object': break;
			default:
				throw new MWException(__CLASS__."::".__METHOD__.": Passed titleData of invalid type.");
				break;
		}
		// Consider $titleData an array for manipulation.
		$titleData = (array) $titleData;
		
		
		
		// Output $titleData an object for object
		return (object) $titleData;
	}
	
#----------------------------------------------------------------------------
#	Core manipulation functions
#----------------------------------------------------------------------------
	
	/**
	 * 
	 * @access private
	 */
	function enforceRequiredData( $titleData ) {
		// Blank data which should be output only.
		//TODO: See if I can use some sort of array mapping or merge do do this cleanly.
		$titleData['urlform'] =
		$titleData['fragment'] =
		$titleData['interwiki'] =
		$titleData['usercasedbkey'] =
			NULL;
		
		// Continue processing
		return true;
	}
	
	/**
	 * Splits Namespace and Interwiki prefixes out of title.
	 * 
	 * @access private
	 */
	function splitPrefixes( $titleData ) {
		
		// Continue processing
		return true;
	}
	
	/**
	 * Restrict the title length.
	 * 
	 * @access private
	 */
	function restrictLength( $titleData ) {
		/**
		 * Limit the size of titles to 255 bytes.
		 * This is typically the size of the underlying database field.
		 * We make an exception for special pages, which don't need to be stored
		 * in the database, and may edge over 255 bytes due to subpage syntax 
		 * for long titles, e.g. [[Special:Block/Long name]]
		 */
		if( ( $titleData['namespace'] != NS_SPECIAL && strlen( $titleData['dbkey'] ) > 255 )
		 || strlen( $titleData['dbkey'] ) > 512 ) {
			// Stop processing and consider bad
			return false;
		}
		// Continue processing
		return true;
	}
	
	/**
	 * 
	 * 
	 * @access private
	 */
	function finalizeOutputData( $titleData ) {
		// Add url form for the data
		$titleData['urlform'] = wfUrlencode( $titleData['dbkey'] );
		
		// Continue processing
		return true;
	}
	
	/**
	 * Backconvert a dbkey into a real title for use when a real title does not exist.
	 * 
	 * @access public
	 * @param string $dbkey DB Key to backconvert.
	 * @return string Backconverted DB Key.
	 */
	function backconvert( $dbkey ) {
		// Allow extensions to override backconversion.
		if( !wfRunHooks( 'TitleNormalizerBackconvert', array( &$dbkey ) ) ) {
			return $dbkey;
		}
		// Since we weren't overriden by an extension, do it the old way.
		return str_replace( '_', ' ', $dbkey );
	}
}