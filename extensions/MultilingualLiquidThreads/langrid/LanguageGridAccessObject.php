<?php
/**
 * This is Object Class to access translation functions provided by Language Grid.
 * @author kadowaki
 */

define('LQT_BINDING_SET_NAME', 'LIQUID_THREADS');

class LanguageGridAccessObject {
	
	private $translationClient;
	
	function __construct() {
		$this->translationClient = new LangridAccessClient();
	}
	
	/**
	 * Translate a distinct subject in Multilanguage by Language Grid.
	 * Translated subjects will be stored in DB and be reused.
	 */
	public function translateSubject($sourceSubject, $thread) {
		$threadId = $thread->id();
		$threadArticleTitle = $thread->articleTitle();
		
		$sourceLang = $this->getSourceLanguage($threadId);
		$targetLang = $this->getTargetLanguage();
		
		if ($sourceLang == $targetLang) {
			return $sourceSubject;
		}
		
		$obj = TranslatedSubject::loadTranslatedSubject($threadId, $targetLang);
		if (!is_null($obj)) { 
			// Translated subject already exists in DB. 
			$translatedSubject = $obj->subject();
		} else { 
			$sourceSubject = htmlspecialchars_decode($sourceSubject);
			$response = $this->translationClient->translate($sourceLang, $targetLang, $sourceSubject, $threadArticleTitle);
			
			if ($response['status'] == 'OK') {
				$translatedSubject = htmlspecialchars($response['contents']->result);
				
				// Insert translated subject into DB.
				TranslatedSubject::create($threadId, $translatedSubject, $targetLang);
			} else { 
				// If error in translation
				$translatedSubject = htmlspecialchars($sourceSubject).' <font color="#f00">(Translation Failed)</font> ';
			}
		}
		
		return $translatedSubject;
	}
	
	/**
	 * Translate posted body in Multilanguage by Language Grid.
	 * Translated posted body will be stored in DB and be reused.
	 */
	public function translateBody($sourceBody, $thread) {
		$threadId = $thread->id();
		$threadArticleTitle = $thread->articleTitle();
		
		$sourceLang = $this->getSourceLanguage($threadId);
		$targetLang = $this->getTargetLanguage();
		
		if ($sourceLang == $targetLang) {
			return $sourceBody;
		}
		
		$obj = TranslatedBody::loadTranslatedBody($threadId, $targetLang);
		if (!is_null($obj)) { 
			// Translated body already exists in DB
			$translatedBody = $obj->body();
		} else {
			/**
			 *  (1) Analyze HTML structure using a regular expression and remove HTML tags.
			 *  (2) Translate only posted body (without HTML tags).
			 *  (3) Restructure HTML
			 */
			
			$translatedBody = "";
			// Analyze HTML structure
			preg_match_all("/(<[^>]+>)+([^<]*)(<\/[^>]+>)+/", $sourceBody, $matches, PREG_OFFSET_CAPTURE); 
			if (! empty($matches) && ! empty($matches[1])) {
        		for ($i = 0; $i < count($matches[1]); $i++) {
        			// Translate posted body
        			$sourceIncludingTags = $matches[0][$i][0];
        			$source = $matches[2][$i][0];
        			
					$source = htmlspecialchars_decode($source);
                    $source = trim($source);

					if ( empty($source)) {
						$translatedBody .= $matches[0][$i][0];
						continue ;
                    }

					$response = $this->translationClient->translate($sourceLang, $targetLang, $source, $threadArticleTitle);
        			if ($response['status'] == 'OK') {
                        $result = htmlspecialchars($response['contents']->result);
                        $translatedBody .= str_replace($source, $result, $sourceIncludingTags);
                    } else { //if error occurred
                        return $sourceBody . ' <font color="#f00">(Translation Failed)</font> ';
					}
				}
			}
            
	        // If no error in translation, insert translated body into DB.
			TranslatedBody::create($threadId, $translatedBody, $targetLang);
			// Message for first viewer
			$translatedBody .=  ' <div style="color:#f00; font-size:80%">'.wfMsg( 'multilang_lqt_thanks' ).'</div>';
			$translatedBody .=  ' <div style="color:#f00; font-size:80%">'.wfMsg( 'multilang_lqt_thanks_detail' , $this->convertLanguageCodeIntoLanguageName($targetLang) ).'</div>';
		}
		
		return $translatedBody;
	}
	
	public function getSourceLanguage($threadId) {
		return ThreadLanguage::loadThreadLanguage($threadId);
	}
	
	public function getTargetLanguage() {
		global $wgLanguageSelectorRequestedLanguage, $wgLanguageCode, $wgLanguageSelectorDetectLanguage;
		
		if ($wgLanguageSelectorRequestedLanguage) {
			$targetLang =  $wgLanguageSelectorRequestedLanguage;
		} else {
			$targetLang = wfLanguageSelectorDetectLanguage( $wgLanguageSelectorDetectLanguage );
		}
		
		if ($targetLang == 'zh-hans') {
			$targetLang = 'zh-CN';
		} else if ($targetLang == 'pt') {
			$targetLang = 'pt-PT';
		}
		
		return $targetLang;
	}
	
	static function convertLanguageCodeIntoLanguageName( $languageCode ) {
		switch ( $languageCode ) {
			case 'ja':
				return wfMsg('multilang_lqt_language_name_ja');
			case 'en':
				return wfMsg('multilang_lqt_language_name_en');
			case 'ko':
				return wfMsg('multilang_lqt_language_name_ko');
			case 'zh':
				return wfMsg('multilang_lqt_language_name_zh');
			case 'zh-CN':
				return wfMsg('multilang_lqt_language_name_zh');
			case 'ar':
				return wfMsg('multilang_lqt_language_name_ar');
			case 'de':
				return wfMsg('multilang_lqt_language_name_de');
			case 'es':
				return wfMsg('multilang_lqt_language_name_es');
			case 'fr':
				return wfMsg('multilang_lqt_language_name_fr');
			case 'id':
				return wfMsg('multilang_lqt_language_name_id');
			case 'it':
				return wfMsg('multilang_lqt_language_name_it');
			case 'ms':
				return wfMsg('multilang_lqt_language_name_ms');
			case 'pt':
				return wfMsg('multilang_lqt_language_name_pt');
			case 'pt-PT':
				return wfMsg('multilang_lqt_language_name_pt');
			case 'ru':
				return wfMsg('multilang_lqt_language_name_ru');
			case 'th':
				return wfMsg('multilang_lqt_language_name_th');
			case 'vi':
				return wfMsg('multilang_lqt_language_name_vi');
		}
	}
}
?>