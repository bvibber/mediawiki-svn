<?php
/**
 * This is Object Class to access translation functions provided by Language Grid.
 * @author kadowaki
 */

define('LQT_BINDING_SET_NAME', 'LIQUID_THREADS');
require('MetaTranslator.php');

class LanguageGridAccessObject {

	private $translationClient;

	function __construct() {
		$this->translationClient = new LangridAccessClient();
	}

	/**
	 * Translate a distinct subject in Multilanguage by Language Grid.
	 * Translated subjects will be stored in DB and be reused.
	 */

	public function needsNoTranslation($threadId) {
		$sourceLang = $this->getSourceLanguage($threadId);
		$targetLang = $this->getTargetLanguage();
		return ($sourceLang == $targetLang);
	}

	function translatedRootByThreadId($threadId) {
		if ($this->needsNoTranslation($threadId)) {
			return Threads::$cache_by_id[$threadId]->root();
		}
		else{
			 $title = Title::newFromID(TranslatedThread::rootPageIdByThreadIdAndLang($threadId, $this->getTargetLanguage()) );
			 return $title ? new Article_LQT_Compat( $title ) : null;
		}
	}


	/**
	 * Translate posted body in Multilanguage by Language Grid.
	 * Translated posted body will be stored in DB and be reused.
	 */
	function translatedRootByThread($thread) {
		$dbr = wfGetDB( DB_SLAVE );
		$threadId = $thread->id();
		$targetLang = $this->getTargetLanguage();
		$root = $this->translatedRootByThreadId($threadId);
		if (is_null($root)) {
			$this->translateBody($thread);
			$root = $this->translatedRootByThreadId($threadId);
		}
		return $root;
	}

	function translatedRootByRootId( $rootId ) {
		$dbr = wfGetDB( DB_SLAVE );

		$res = $dbr->select( 'thread', '*',
					array( 'thread_root' => $rootId ),
					__METHOD__);
		while ( $row = $dbr->fetchObject( $res ) ) {
			$thread = Thread::newFromRow( $row );
		}
		if ( is_null( $thread ) )
			return null;
		else
			return $this->translatedRootByThread( $thread );
	}

	function translatedRootByRoot( $root ) {
		return $this->translatedRootByRootId( $root->getID() );
	}

	function originalRootbyRoot( $root ){
		$dbr = wfGetDB( DB_SLAVE );

		$res = $dbr->select( 'translated_thread', '*',
					array( 'tt_root' => $root->getID() ),
					__METHOD__);
		while ( $row = $dbr->fetchObject( $res ) ) {
			$originalThreadId = $row->tt_original;
		}
		if( is_null( $originalThreadId ) )
			return null;
		else {
			$thread = Threads::withId( $originalThreadId );
			return $thread->root();
		}
	}

	function getLatestBody($page) {
		return LqtView::showPostBody($page);
	}

	public function parseFromRootId($root_id) {
		$title = Title::newFromID($root_id);
		$post = new Article_LQT_Compat($title);
		global $wgRequest;
		$oldid = $wgRequest->getVal('oldid', null);
		$parserOutput = $post->getParserOutput($oldid);
		return $parserOutput->getText();
	}

	public function translateBody($thread, $sourceBody = null) {
		if(!$sourceBody) $sourceBody = $this->getLatestBody($thread->root());

		$threadId = $thread->id();
		if ($this->needsNoTranslation($threadId)) return $sourceBody;
		$targetLang = $this->getTargetLanguage();
		$obj = TranslatedThread::loadTranslatedThread($threadId, $targetLang);
		if (!is_null($obj)) {
			// Translated body already exists in DB
			$translatedBody = $this->parseFromRootId($obj->ttRoot());
		} else {
			$translatedBody = "";

			if (is_null(MultilangLqtHooks::$translatedSubject)) {
				return $sourceBody . ' <font color="#f00">(Translation Failed)</font> ';
			}
			else {
				/**
				 * MetaTranslate
				 */
				$source = $sourceBody;
				$source = trim($source);


				$sourceLang = $this->getSourceLanguage($threadId);
				$meta = new MetaTranslator($sourceLang);

				/* parameters */
				$translationFunc = array($this->translationClient, 'translate');
				$translationParam = array($sourceLang, $targetLang, $source, $thread->article()->getTitle());
				$parameterOrder = array(0,1,2);
				$resultFunc = array($this, 'getResult');
				$statusFunc = array($this, 'getStatus');

				$translatedBody = $meta->metaTranslate($translationFunc, $translationParam, $parameterOrder, true, true, $resultFunc, $statusFunc);

				if($translatedBody==null) {
    			  return $sourceBody . ' <font color="#f00">(Translation Failed)</font> ';
				}

				$translatedBody='<p>'.$translatedBody.'</p>';

				// If no error in translation, insert translated body into DB.
				$text = $translatedBody;
				$summary = 'translation';
				$t = $thread->title()->getPrefixedText().'/'.$targetLang;
				$namespace = $thread->root()->getTitle()->getNamespace();

				$title = Title::newFromText($t, $namespace);
				$a = new Article($title);
				$a->doEdit($text,$summary);

				$articleId = $a->getID();
				$ttSubject = $this->getSubject();
				TranslatedThread::create( $articleId, $threadId,  MultilangLqtHooks::$translatedSubject, $targetLang );
			}
		}

		return $translatedBody;
	}

	public function translateSubject($thread, $sourceSubject) {
		$threadId = $thread->id();
		if ($this->needsNoTranslation($threadId)) {
			MultilangLqtHooks::$translatedSubject = $sourceSubject;
			return $sourceSubject;
		}
		$targetLang = $this->getTargetLanguage();
		$obj = TranslatedThread::loadTranslatedThread($threadId, $targetLang);
		if (!is_null($obj)) {
			// Translated subject already exists in DB.
			$translatedSubject = $obj->ttSubject();
			MultilangLqtHooks::$translatedSubject = $translatedSubject;
		} else {
			$sourceSubject = htmlspecialchars_decode($sourceSubject);
			$response = $this->translationClient->translate($this->getSourceLanguage($threadId), $targetLang, $sourceSubject, $thread->article()->getTitle());

			if ($response['status'] == 'OK') {
				$translatedSubject = htmlspecialchars($response['contents']->result);
				MultilangLqtHooks::$translatedSubject = $translatedSubject;
			} else {
				// If error in translation
				$translatedSubject = null;
				//htmlspecialchars($sourceSubject);//.' <font color="#f00">(Translation Failed)</font> ';
				MultilangLqtHooks::$translatedSubject = null;
			}
		}

		return $translatedSubject;
	}

	public function getResult($response) {
		return $response['contents']->result;
	}

	public function getStatus($response) {
		return $response['status'];
	}

	public function getSubject() {
		return "SomeSubject"; // temporary
	}
	public function getRoot() {
		return 10; // temporary
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
