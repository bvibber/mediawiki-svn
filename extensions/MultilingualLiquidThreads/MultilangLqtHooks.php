<?php
class MultilangLqtHooks {

	static $translatedSubject;

	static function saveReply( &$info, &$e, &$thread ) {
		$replyTo = $info['replyTo'];
		$subject = $replyTo->subject();
		$langridAccessObject = new LanguageGridAccessObject();
		$translatedSubject = $langridAccessObject->translateSubject($replyTo, $subject);
		$replyTo->setSubject( $translatedSubject );
		$info['replyTo'] = $replyTo;
		return true;
	}

	static function onThreadCommands( $thread, &$commands ) {
		$langridAccessObject = new LanguageGridAccessObject();
		if ( !$langridAccessObject->needsNoTranslation( $thread->id() ) ) {
			$translatedRoot = $langridAccessObject->translatedRootByThread( $thread );
			if ( isset( $translatedRoot ) ) {
				$thread->setRoot( $translatedRoot );
			}
		}

		$translated_history_url = LqtView::permalinkUrlWithQuery( $thread, array( 'action' => 'history' ) );
		$commands['history'] = array( 'label' => wfMsgExt( 'history_short', 'parseinline' ),
						 'href' => $translated_history_url,
						 'enabled' => true );
		return true;
	}

	static function onThreadPermalinkView( $threadParmalinkView, &$thread ) {
		$langridAccessObject = new LanguageGridAccessObject();
		$originalRoot = $langridAccessObject->originalRootbyRoot( $threadParmalinkView->article );
		if ( is_null( $originalRoot ) ) {
			$thread = Threads::withRoot( $threadParmalinkView->article );
		} else {
			$thread = Threads::withRoot( $originalRoot );
		}

		return true;
	}

	static function translateRoot( $thread, &$article, $talkpage) {
		$langridAccessObject = new LanguageGridAccessObject();
        $article = $langridAccessObject->translatedRootByRoot($thread->root());
		return true;
    }

	static function onThreadSignature( $thread, &$signature) {
		$langridAccessObject = new LanguageGridAccessObject();
		$languageCode = $langridAccessObject->getSourceLanguage( $thread->id() );
		$signature .= wfMsg( 'multilang_lqt_post_language',
						LanguageGridAccessObject::convertLanguageCodeIntoLanguageName($languageCode));
		return true;
	}

	static function translateSubject( $thread, &$html ) {
		$sourceSubject = $thread->subjectWithoutIncrement();
		$langridAccessObject = new LanguageGridAccessObject();
		$translatedSubject = $langridAccessObject->translateSubject( $thread, $sourceSubject );
		if ( is_null( $translatedSubject ) ) {
			$html .= ' <font color="#f00">(Translation Failed)</font> ';
		} else {
			$html = LqtView::formatSubject( $translatedSubject );
		}
		return true;
	}

	static function translateSubjectforTOC( $thread ) {
		$sourceSubject = $thread->subjectWithoutIncrement();
		$langridAccessObject = new LanguageGridAccessObject();
		$translatedSubject = $langridAccessObject->translateSubject( $thread, $sourceSubject );
		if ( is_null( $translatedSubject ) ) {
			$thread->setSubject( $sourceSubject );
		} else {
			$thread->setSubject( $translatedSubject );
		}
		return true;
	}

	static function translateBody( $thread, &$post ) {
		$langridAccessObject = new LanguageGridAccessObject();
		$root = $langridAccessObject->translatedRootByThread($thread);
		if ( !is_null( $root ) ) {
			$post = $root;
		}

		return true;
	}

	static function onThreadMajorCommands( $thread, &$commands ) {
		$langridAccessObject = new LanguageGridAccessObject();
		if ( !$langridAccessObject->needsNoTranslation( $thread->id() ) ) {
				$languageCode = $langridAccessObject->getSourceLanguage( $thread->id() );
				$sourceMsg = wfMsg( 'multilang_lqt_show_original',
							LanguageGridAccessObject::convertLanguageCodeIntoLanguageName( $languageCode ) );
				if ( is_null( $sourceMsg ) ) $sourceMsg = 'Original';
				$sourceUrl = LqtView::talkpageUrl( $thread->title() , 'source', $thread,
						true /* include fragment */, true );
				$commands['source'] =
						array(
							'label' => $sourceMsg,
							'class' => 'lqt-command-source',
							'href' => $sourceUrl,
							'enabled' => true
							);
		}

		return true;
	}

	static function onShowPostThreadBody( $thread, $request, &$html ) {
		$sView = new SourceView( $thread, $request );

		// show source
		if ( $sView->methodAppliesToThread( 'source' ) ) {
			// As with above, flush HTML to avoid refactoring EditPage.
			$html .= '<hr>';
			$html .= $sView->showSource( $thread );
		} else {
			$html .= Xml::tags( 'div',
					array( 'class' => 'lqt-source-form lqt-edit-form',
						'style' => 'display: none;'  ),
					'' );
		}

		return true;
	}

	static function onDoInlineEditForm( $thread, $request, &$output ) {
		$sView = new SourceView( $thread, $request );

		if ( $sView->methodAppliesToThread( 'source' ) ) {
			$html = $sView->showSource( $thread );
			$output->addHTML( $html );
		}

		return true;
	}

	static function saveSourceLang( &$thread ) {
		$dbw = wfGetDB( DB_MASTER );
		$threadId = $thread->id();

		$langridAccessObject = new LanguageGridAccessObject();
		$postlang = $langridAccessObject->getTargetLanguage();

		$threadlang = ThreadLanguage::create( $threadId, $postlang );

		return true;
	}

	static function showLanguageSelector( &$e, $t ) {
		$language_label = wfMsg( 'multilang_lqt_language' );

		$lang_select_html .= Xml::openElement('select', array('name' => 'wpPostlang', 'id' => 'wpPostlang', 'style' => $select));
		global $wgLanguageSelectorLanguages,$wgContLang,$wgLang;

		//$postlang = $this->request->getVal( 'wpPostlang' );
		$langridAccessObject = new LanguageGridAccessObject();
		$postlang = $langridAccessObject->getTargetLanguage();
		if($postlang) {
			// for preview page
			$code = $postlang;
		} else {
			$code = $wgLang->getCode();
		}

		foreach ($wgLanguageSelectorLanguages as $ln) {
		    $name = $wgContLang->getLanguageName($ln);
		    if ($showCode) $name = wfBCP47($ln) . ' - ' . $name;
		    $lang_select_html .= Xml::option($name, $ln, $ln == $code);
		}
		$lang_select_html .= Xml::closeElement('select');
		$e->editFormTextBeforeContent .=
		Xml::Label( $language_label, 'lqt_lang_name' ) .
		$lang_select_html .
		Xml::element( 'br' );

		return true;
	}

}
