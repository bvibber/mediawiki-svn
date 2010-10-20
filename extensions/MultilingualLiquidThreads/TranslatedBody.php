<?php

class TranslatedBody {
	
	protected $translatedBodyId;
	protected $threadId;
	protected $body;
	protected $targetLang;
	
	static function create( $threadId, $body, $targetLang ) {
		$translatedBody = new TranslatedBody();
		
		$translatedBody->setThreadId($threadId);
		$translatedBody->setBody($body);
		$translatedBody->setTargetLang($targetLang);
		
		$translatedBody->insert();
	
		return $translatedBody;
	}
	
	function insert() {
		$dbw = wfGetDB(DB_MASTER);
		
		$row = $this->getRow();
		$row['translated_body_id']
			= $dbw->nextSequenceValue('translated_body_translated_body_id');
		
		$dbw->insert( 'translated_body', $row, __METHOD__);
		$this->translatedBodyId = $dbw->insertId();
	}
	
	function getRow() {
		$id = $this->translatedBodyId();
		
		$dbw = wfGetDB( DB_MASTER );
		
		if (!$id) {
			$id = $dbw->nextSequenceValue( 'translated_body_translated_body_id');
		}
		
		return array(
			'translated_body_id' => $id,
			'thread_id' => $this->threadId,
			'body' => $this->body,
			'target_lang' => $this->targetLang,
		);
	}
	
	static function loadTranslatedBody($threadId, $targetLang) {
		$dbr = wfGetDB( DB_SLAVE );
		
		$res = $dbr->select( 'translated_body', '*',
					array( 'thread_id' => $threadId,
						'target_lang' => $targetLang),
					__METHOD__);
		
		$translatedBody = null;
		while ($row = $dbr->fetchObject($res)) {
			$translatedBody = new TranslatedBody();
			$translatedBody->translatedBodyId = $row->translated_body_id;
			$translatedBody->threadId = $row->thread_id;
			$translatedBody->body = $row->body;
			$translatedBody->targetLang = $row->target_lang;
		}
		
		return $translatedBody;
	}
	
	function setThreadId ( $threadId ) {
		$this->threadId = $threadId;
	}
	
	function setBody ($body) {
		$this->body = $body;
	}
	
	function setTargetLang ($targetLang) {
		$this->targetLang = $targetLang;
	}
	
	function translatedBodyId() {
		return $this->translatedBodyId;
	}
	
	function threadId() {
		return $this->threadId;
	}
	
	function body() {
		return $this->body;
	}
	
	function targetLang() {
		return $this->targetLang;
	}
}
