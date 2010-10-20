<?php
class ThreadLanguage {
	protected $threadLanguageId;
	protected $threadId;
	protected $language;

	static function create( $threadId, $language ) {
		$threadLanguage = new ThreadLanguage();

		if ($language == 'zh-hans') {
			$language = 'zh-CN';
		} else if ($language == 'pt') {
			$language = 'pt-PT';
		}

		$threadLanguage->setThreadId($threadId);
		$threadLanguage->setLanguage($language);

		$threadLanguage->insert();

		return $threadLanguage;
	}

	function insert() {
		$dbw = wfGetDB(DB_MASTER);

		$row = $this->getRow();
		$row['thread_language_id']
			= $dbw->nextSequenceValue('thread_language_thread_language_id');

		$dbw->insert( 'thread_language', $row, __METHOD__);
		$this->threadLanguageId = $dbw->insertId();
	}

	function getRow() {
		$id = $this->threadLanguageId();

		$dbw = wfGetDB( DB_MASTER );

		if (!$id) {
			$id = $dbw->nextSequenceValue( 'thread_language_thread_language_id');
		}

		return array(
			'thread_language_id' => $id,
			'thread_id' => $this->threadId,
			'language' => $this->language,
		);
	}

	function loadThreadLanguage($threadId) {
		$dbr = wfGetDB( DB_SLAVE );

		$res = $dbr->select( 'thread_language', '*',
					array( 'thread_id' => $threadId),
					__METHOD__);

		$threadLanguage = null;
		while ($row = $dbr->fetchObject($res)) {
			$threadLanguage = $row->language;
		}

		return $threadLanguage;
	}

	function setThreadId($threadId) {
		$this->threadId = $threadId;
	}

	function setLanguage($language) {
		$this->language = $language;
	}

	function threadLanguageId() {
		return $this->threadLanguageId;
	}

	function threadId() {
		return $this->threadId;
	}

	function language() {
		return $this->language;
	}
}
