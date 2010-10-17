<?php
class SourceView {
	public $thread;
	public $langridAccessObject;
	public $request;

	function __construct( $thread, $request ) {
		$this->thread = $thread;
		$this->langridAccessObject = new LanguageGridAccessObject();
		$this->request = $request;
	}
	
	function methodAppliesToThread( $method ) {
		return $this->request->getVal( 'lqt_method' ) == $method &&
			$this->request->getVal( 'lqt_operand' ) == $this->thread->id();
	}
	
	function getMethod() {
		return $this->request->getVal( 'lqt_method' );
	}
	
	function getLang() {
		return $this->request->getVal( 'setlang' );
	}
		
	function showSource( $thread ) {
		
		$sourceBody = MultilangThreadController::getSourceSentence( $thread );

		$cancelUrl = LqtView::talkpageUrl( $thread->title(), null, $thread,
							true , $this->request );
		$languageCode = $this->getLang();
		if( !$languageCode ) {
			$languageCode = $this->langridAccessObject->getSourceLanguage( $thread->id() );
		}
		$hideMsg = wfMsg( 'multilang_lqt_hide_original',
						LanguageGridAccessObject::convertLanguageCodeIntoLanguageName( $languageCode ) );
		if( is_null( $hideMsg ) ) {
			$hideMsg = 'Hide';
		}
		
		$sourceBody .= Xml::tags( 'a',
							array( 'href' => $cancelUrl, 'id' => 'mw-source-cancel' ),
							$hideMsg );

		$tag = Xml::tags( 'div', array( 'class' => 'source-content' ),
				$sourceBody );
		
		return $tag;
	}

}
