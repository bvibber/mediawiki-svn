<?php

class LocalisationException extends Exception {};
class NoSuchMessageFileException extends LocalisationException {};

/**loosely inspired on class of same name from mediawiki.
* (mediawiki version is overkill for our purposes though)
* also, unlike mediawiki, we always use ISO 639-3 for language codes.
*/
class Language {

	private $code; #language code, mostly for debugging purposes
	private $messages; 
	private $fallback=false;

	public function __construct($code=null) {
		$this->code=$code;
		if ($code) 
			$this->loadMessages($code);

	}

	public function loadMessages($code="Default") {
		$messages="Messages_$code.php";
		$path=dirname(__FILE__)."/messages/$messages";
		if (file_exists($path)) {
			include($path);	
			if (isset($messages)) {
				$this->messages=$messages; #messages is from the included file
			} else {
				throw new LocalisationException("messages file format problem, there's no $messages");
			}
			
			if (isset($fallback)) {
				if ($fallback===false) {
					$this->fallback=false;
				} else {
					$this->fallback=new Language($fallback);
				}
			}

		} elseif ($code!=="Default") { #last dutch fallbacsk
			$code="Default";
			$this->loadMessages("Default");
		} else {
			throw new NoSuchMessageFileException("Could not find message file for language code '$code'; file '$path' does not exist");
		}
		$this->code=$code;
	}

	/**
	 * Get language names available for i18n, indexed by code.
	 * If $customisedOnly is true, only returns codes with a messages file
	 * modified from mediawiki (GPL applies)
	 */
	public static function getI18NLanguageNames() {

		$languageNames=Language::getAllLanguageNames();

		$messageFiles = glob( dirname(__FILE__)."/messages/Messages_*.php" );
		var_dump($messageFiles);
		$names = array();
		foreach ( $messageFiles as $file ) {
			$m = array();
			if( preg_match( '/Messages_([a-z_]+)\.php$/', $file, $m ) ) {
				$code = str_replace( '_', '-', strtolower( $m[1] ) );
				var_dump($code);
				if ( isset( $languageNames[$code] ) ) {
					$names[$code] = $languageNames[$code];
				}
			}
		}
		return $names;
	}

	public function translation_exists($phrase) {
		if ($this->messages) {
			return array_key_exists($phrase, $this->messages);
		} else {
			throw new Exception("not initialized, code ".$this->code);
		}
	}

	/** translate the phrase, but doesn't do any substitutions. use printf,sprintf, or vsprintf for that */
	public function translate($phrase) {
		if ($this->translation_exists($phrase)) {
			return $this->messages[$phrase];
		} elseif ($this->fallback && $this->fallback->translation_exists($phrase)) {
			return $this->fallback->translate($phrase);
		} else {
			return "{untranslated: '$phrase'}";
		}
	}
	

	# == Diverse sprintf-ish functions 

	/** i18nsprint is a simpler way to go about things, will do i18n replacement
	on antyhing enclosed in <|  |>, any %signs in these substrings
	    will be substituted with items from the array*/
	public function i18nsprint($string, $replacements=array()) {
		$callback=new I18Ncallback();
		$callback->replacements=$replacements;
		$callback->language=$this;
		return preg_replace_callback("#(?U)(<\|.*\|>)#", array($callback,"replace"), $string);
	}

	/* like i18nsprint, but prints directly to output*/
	public function i18nprint($string, $replacements=array()) {
		print $this->i18nsprint($string, $replacements);
	}


	public function printf($phrase) {
		$args=func_get_args();
		$str=$this->vsprintf($phrase, $args);
		print $str;
		return strlen($str);
	}

	# internationalized sprintf
	public function sprintf($phrase) {
		$args=func_get_args();
		return $this->vsprintf($phrase, $args);
	}

	# internationalized vprintf
	public function vsprintf($phrase, $array) {
		return vsprintf($this->translate($phrase),$array);
	}

	/** assoc variant on vsprintf,
	 * (modified from sprintf2 by "matt", 10-Mar-2008 06:13,
	 *  http://nl2.php.net/manual/en/function.sprintf.php )
	 * original Apparently  Copyright © 2001-2008 The PHP Group, copied here
	 * on condition that copyright notice is retained. )
	 */
	function vsprintf2($phrase='', $vars=array(), $char='%') {
		$str=$this->translate($phrase);
		if (!$str) return '';
		if (count($vars) > 0) {
			foreach ($vars as $k => $v) {
				$str = str_replace($char . $k, $v, $str);
			}
		}

		return $str;
	}

	# == Getters/setters

	/**@return iso693_3 3-letter language code, or "Default".*/
	public function getCode() {
		return $this->code;
	}
	
	/** @return all possible languages indexed by code */
	public static function getAllLanguageNames() {
		include("Names.php");
		return $languageNames;
	}

}


/** for use by Language::i18nprint */
class I18Ncallback {
	public $language;
	public $replacements;
	public function replace($matches) {
		$match=substr($matches[0],2,-2);

		return $this->language->vsprintf2($match,$this->replacements);
	}
}

?>
