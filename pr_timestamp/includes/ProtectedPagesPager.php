<?php

/**
 * Pager for Special:Protectedpages
 *
 * @addtogroup Pager
 */
class ProtectedPagesPager extends AlphabeticPager {

	/**
	 * Filtering options
	 */
	private $type = '';
	private $level = '';
	private $namespace = null;
	private $sizetype = '';
	private $size = 0;

	/**
	 * Constructor
	 *
	 * @param string $type
	 * @param string $level
	 * @param int $namespace
	 * @param string $sizetype
	 * @param int $size
	 */
	public function __construct( $type, $level, $namespace, $sizetype = '', $size = 0 ) {
		parent::__construct();
		$this->type = $type;
		$this->level = $level;
		$this->namespace = $namespace;
		$this->sizetype = $sizetype;
		$this->size = $size;
	}

	/**
	 * Pre-process results; do a batch existence check on all pages
	 * and their associated talk pages
	 *
	 * @param ResultWrapper $result Result wrapper
	 */
	protected function preprocessResults( $result ) {
		$batch = new LinkBatch();
		while( $row = $result->fetchObject() ) {
			$title = Title::makeTitleSafe( $row->page_namespace, $row->page_title );
			if( $title instanceof Title ) {
				$batch->addObj( $title->getSubjectPage() );
				$batch->addObj( $title->getTalkPage() );
			}
		}
		$batch->execute();
		$result->rewind();
	}

	/**
	 * Format a single result row
	 *
	 * @param object $row Result row
	 * @return string
	 */
	public function formatRow( $row ) {
		global $wgUser, $wgLang;
		wfProfileIn( __METHOD__ );
		
		$skin = $wgUser->getSkin();

		# Date and time
		$timestamp = $row->pr_timestamp
			? '(' . $wgLang->timeAndDate( $row->pr_timestamp ) . ')'
			: '';

		# Page and talk page links
		$title = Title::makeTitleSafe( $row->page_namespace, $row->page_title );
		$page = $skin->makeLinkObj( $title );
		if( !$title->isTalkPage() )
			$page .= ' (' . $skin->makeLinkObj( $title->getTalkPage(), wfMsgHtml( 'talkpagelinktext' ) ) . ')';
		
		# Size indicator
		$size = $row->page_len
			? '<small>' . wfMsgHtml( 'historysize', $wgLang->formatNum( $row->page_len ) ) . '</small> '
			: '';
		
		# Protection level
		$level = wfMsgHtml( 'restriction-level-' . $row->pr_level );
		
		# Expiration
		$expire = '';
		if( $row->pr_expiry && $row->pr_expiry != 'infinity' ) {
			$exptime = Block::decodeExpiry( $row->pr_expiry );
			$expire = ', ' . wfMsgHtml( 'protect-expiring', $wgLang->timeAndDate( $exptime ) );
		}
	
		wfProfileOut( __METHOD__ );
		return "<li>{$timestamp} {$page} {$size}({$level}{$expire})</li>";
	}

	/**
	 * Get query information
	 *
	 * @return array
	 */
	public function getQueryInfo() {
		// Core conditions
		$conds = array(
			'pr_expiry > ' . $this->mDb->addQuotes( $this->mDb->timestamp() ),
			'page_id = pr_page',
			'pr_type = ' . $this->mDb->addQuotes( $this->type ),
		);

		// Protection level
		if( $this->level )
			$conds['pr_level'] = $this->level;

		// Namespace
		if( !is_null( $this->namespace ) )
			$conds['page_namespace'] = $this->namespace;

		// Size limit
		if( $this->sizetype == 'min' ) {
			$conds[] = 'page_len >= ' . intval( $this->size );
		} elseif( $this->sizetype == 'max' ) {
			$conds[] = 'page_len <= ' . intval( $this->size );
		}
		
		return array(
			'tables' => array( 'page_restrictions', 'page' ),
			'fields' => 'pr_id,page_namespace,page_title,page_len,pr_type,pr_level,pr_timestamp,pr_expiry',
			'conds' => $conds
		);
	}

	/**
	 * Get the name of the paging column
	 *
	 * @return string
	 */
	public function getIndexField() {
		return 'pr_id';
	}
	
}