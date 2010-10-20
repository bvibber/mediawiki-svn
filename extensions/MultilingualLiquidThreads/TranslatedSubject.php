<?php

class TranslatedSubject {

	protected $translatedSubjectId;
	protected $threadId;
	protected $translatedSubjectRoot;
	protected $targetLang;

	static function create( $threadId, $translatedSubjectRoot, $targetLang ) {
		$translatedSubject = new TranslatedSubject();

		$translatedSubject->setThreadId($threadId);
		$translatedSubject->setTranslatedSubjectRoot($translatedSubjectRoot);
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
			'translated_subject_root' => $this->translatedSubjectRoot,
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
			$translatedSubject->translatedSubjectRoot = $row->translated_subject_root;
			$translatedSubject->targetLang = $row->target_lang;
		}

		return $translatedSubject;
	}

	function setThreadId ( $threadId ) {
		$this->threadId = $threadId;
	}

	function setTranslatedSubjectRoot ($translatedSubjectRoot) {
		$this->translatedSubjectRoot = $translatedSubjectRoot;
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

	function translatedSubjectRoot() {
		return $this->translatedSubjectRoot;
	}

	function targetLang() {
		return $this->targetLang;
	}
}
