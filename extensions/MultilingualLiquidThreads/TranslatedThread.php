<?php

class TranslatedThread {

	protected $ttId;
	protected $ttRoot;
	protected $ttOriginal;
	protected $ttSubject;
	protected $ttLang;


	static function create( $ttRoot, $ttOriginal,  $ttSubject, $ttLang ) {
		$translatedThread = new TranslatedThread();

		$translatedThread->setTtRoot($ttRoot);
		$translatedThread->setTtOriginal($ttOriginal);
		$translatedThread->setTtSubject($ttSubject);
		$translatedThread->setTtLang($ttLang);


		$translatedThread->insert();

		return $translatedThread;
	}

	function insert() {
		$dbw = wfGetDB(DB_MASTER);

		$row = $this->getRow();
		$row['tt_id']
			= $dbw->nextSequenceValue('translated_thread_tt_id');

		$dbw->insert( 'translated_thread', $row, __METHOD__);
		$this->ttId = $dbw->insertId();
	}

	function getRow() {
		$id = $this->ttId();

		$dbw = wfGetDB( DB_MASTER );

		if (!$id) {
			$id = $dbw->nextSequenceValue( 'translated_thread_tt_id');
		}

		return array(
			'tt_id' => $id,
			'tt_root' => $this->ttRoot,
			'tt_original' => $this->ttOriginal,
            'tt_subject' => $this->ttSubject,
			'tt_lang' => $this->ttLang,

		);
	}

	static function loadTranslatedThread($ttOriginal, $ttLang) {
		$dbr = wfGetDB( DB_SLAVE );

		$res = $dbr->select( 'translated_thread', '*',
					array( 'tt_original' => $ttOriginal,
						'tt_lang' => $ttLang),
					__METHOD__);

		$translatedThread = null;
		while ($row = $dbr->fetchObject($res)) {
			$translatedThread = new TranslatedThread();
			$translatedThread->ttId = $row->tt_id;
			$translatedThread->ttRoot = $row->tt_root;
			$translatedThread->ttOriginal = $row->tt_original;
            $translatedThread->ttSubject = $row->tt_subject;
			$translatedThread->ttLang = $row->tt_lang;

		}

		return $translatedThread;
	}


    function rootPageIdByThreadIdAndLang($threadId, $targetLang) {
      $dbr = wfGetDB( DB_SLAVE );
      return $dbr->selectField(
                               'translated_thread',
                               'tt_root',
                               array(
                                     'tt_original' => $threadId,
                                     'tt_lang' => $targetLang,
                                     ),
                               __METHOD__ ); // what's  __METHOD__ ?
    }

    function getOriginalThread(){
    	return Threads::withId( $this->ttOriginal );
    }

	function save(){
		$dbw = wfGetDB( DB_MASTER );
		$res = $dbw->update( 'translated_thread',
		     /* SET */ $this->getRow(),
		     /* WHERE */ array( 'tt_id' => $this->ttId, ),
		     null );
		return $res;
	}

    function setTtRoot ($ttRoot) {
		$this->ttRoot = $ttRoot;
	}

	function setTtOriginal ($ttOriginal) {
		$this->ttOriginal = $ttOriginal;
	}

    function setTtSubject( $ttSubject ) {
		$this->ttSubject = $ttSubject;
    }

	function setTtLang ($ttLang) {
		$this->ttLang = $ttLang;
	}

	function ttId() {
		return $this->ttId;
	}

    function ttRoot() {
		return $this->ttRoot;
	}

	function ttOriginal() {
		return $this->ttOriginal;
	}

    function ttSubject() {
		return $this->ttSubject;
	}

	function ttLang() {
		return $this->ttLang;
	}
}
