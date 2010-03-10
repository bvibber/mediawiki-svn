<?php

class TranslatedSubject {
	
	protected $translatedSubjectId;
	protected $threadId;
	protected $subject;
	protected $targetLang;
	
	static function create( $threadId, $subject, $targetLang ) {
		$translatedSubject = new TranslatedSubject();
		
		$translatedSubject->setThreadId($threadId);
		$translatedSubject->setSubject($subject);
		$translatedSubject->setTargetLang($targetLang);
		
		$translatedSubject->insert();
	
		return $translatedSubject;
	}
	
	function insert() {
		$dbw = wfGetDB(DB_MASTER);
		
		$row = $this->getRow();
		$row['translated_subject_id']
			= $dbw->nextSequenceValue('translated_subject_translated_subject_id');
		
		$dbw->insert( 'translated_subject', $row, __METHOD__);
		$this->translatedSubjectId = $dbw->insertId();
	}
	
	function getRow() {
		$id = $this->translatedSubjectId();
		
		$dbw = wfGetDB( DB_MASTER );
		
		if (!$id) {
			$id = $dbw->nextSequenceValue( 'translated_subject_translated_subject_id');
		}
		
		return array(
			'translated_subject_id' => $id,
			'thread_id' => $this->threadId,
			'subject' => $this->subject,
			'target_lang' => $this->targetLang,
		);
	}
	
	static function loadTranslatedSubject($threadId, $targetLang) {
		$dbr = wfGetDB( DB_SLAVE );
		
		$res = $dbr->select( 'translated_subject', '*',
					array( 'thread_id' => $threadId,
						'target_lang' => $targetLang),
					__METHOD__);
		
		$translatedSubject = null;
		while ($row = $dbr->fetchObject($res)) {
			$translatedSubject = new TranslatedSubject();
			$translatedSubject->translatedSubjectId = $row->translated_subject_id;
			$translatedSubject->threadId = $row->thread_id;
			$translatedSubject->subject = $row->subject;
			$translatedSubject->targetLang = $row->target_lang;
		}
		
		return $translatedSubject;
	}
	
	function setThreadId ( $threadId ) {
		$this->threadId = $threadId;
	}
	
	function setSubject ($subject) {
		$this->subject = $subject;
	}
	
	function setTargetLang ($targetLang) {
		$this->targetLang = $targetLang;
	}
	
	function translatedSubjectId() {
		return $this->translatedSubjectId;
	}
	
	function threadId() {
		return $this->threadId;
	}
	
	function subject() {
		return $this->subject;
	}
		
	function targetLang() {
		return $this->targetLang;
	}
}
?>