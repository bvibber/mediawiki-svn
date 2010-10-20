<?php
class MultilangThreadController {
	public $thread;
	public $langridAccessObject;

	//もしかして、これって$threadを上書きできない？
	//getThreadメソッドが必要？ by嶋田
	function __construct ( $thread ) {
		$this->thread = $thread;
		$this->langridAccessObject = new LanguageGridAccessObject();
	}

    function getRootId( $thread ) {

    }

	function rootById( $rootId ) {
      $article_cache = Thread::$articleCacheById[$rootId];
      if ( isset( $article_cache ) ) {
      	return $article_cache;
      }
      $title_cache = Thread::$titleCacheById[$rootId];
      $title = isset( $title_cache ) ? $title_cache : Title::newFromID( $rootId );
      return $title ? new Article_LQT_Compat( $title ) : null;
	}

	function editMonolingualSubject( $subject ) {
		$threadId = $this->thread->id();
		if ( $this->langridAccessObject->needsNoTranslation( $threadId ) ) {
			$this->thread->setThisSubject( $subject );
		} else {
			$targetLang = $this->langridAccessObject->getTargetLanguage();
			$translatedThread = TranslatedThread::loadTranslatedThread( $threadId, $targetLang );
			$translatedThread->setTtSubject( $subject );
			$translatedThread->save();
		}

		foreach( $this->thread->replies() as $reply ) {
			$multilangThreadController = new MultilangThreadController( $reply );
			$multilangThreadController->editMonolingualSubject( $subject );
		}
	}
	
	function getSourceSentence( $thread ) {
		global $wgOut;
		
		$post = $thread->root();
		$oldid = $thread->isHistorical() ? $thread->rootRevision() : null;

		// Load compatibility layer for older versions
		if ( !( $post instanceof Article_LQT_Compat ) ) {
			wfWarn( "No article compatibility layer loaded, inefficiently duplicating information." );
			$post = new Article_LQT_Compat( $post->getTitle() );
		}

		$parserOutput = $post->getParserOutput( $oldid );
		$wgOut->addParserOutputNoText( $parserOutput );

		return $parserOutput->getText();
	}
}
