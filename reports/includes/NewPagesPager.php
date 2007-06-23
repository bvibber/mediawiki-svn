<?php

/**
 * Pager for Special:Newpages
 *
 * @addtogroup SpecialPage
 * @author Rob Church <robchur@gmail.com>
 */
class NewPagesPager extends IndexPager {

	private $namespace = 0;
	private $username = '';
	
	/**
	 * Constructor
	 *
	 * @param mixed $namespace
	 * @param mixed $username
	 */
	public function __construct( $namespace, $username ) {
		parent::__construct();
		$this->namespace = $namespace;
		$this->username = $username;
	}
	
	/**
	 * Column to use for paging
	 *
	 * @return string
	 */
	public function getIndexField() {
		return 'rc_id';
	}
	
	/**
	 * Information about the SELECT operation
	 *
	 * @return array
	 */
	public function getQueryInfo() {
		$base = array(
			'tables' => array(
				'recentchanges',
				'page',
			),
			'fields' => array(
				'rc_id',
				'rc_namespace',
				'rc_title',
				'rc_user',
				'rc_user_text',
				'rc_comment',
				'rc_timestamp',
				'rc_patrolled',
				'page_len',
				'page_latest',
			),
			'conds' => array(
				'rc_cur_id = page_id',
				'rc_new = 1',
				'page_is_redirect = 0',
			),
		);
		$base['conds'] += $this->getNamespaceConditions();
		$base['conds'] += $this->getUserConditions();
		return $base;
	}
	
	/**
	 * Get conditions for filtering per namespace
	 *
	 * @return array
	 */
	private function getNamespaceConditions() {
		return $this->namespace != 'all'
			? array( 'rc_namespace' => $this->namespace )
			: array();
	}
	
	/**
	 * Get conditions for filtering per user
	 *
	 * @return array
	 */
	private function getUserConditions() {
		$username = trim( $this->username );
		return strlen( $username ) > 0
			? array( 'rc_user_text' => $username )
			: array();		
	}
	
	/**
	 * Format a result row
	 *
	 * @param object $row
	 * @return string
	 */
	public function formatRow( $row ) {
		# FIXME: Full repertoire required
		$title = Title::makeTitleSafe( $row->rc_namespace, $row->rc_title );
		return "<li>" . $this->getSkin()->makeKnownLinkObj( $title ) . "</li>\n";
	}
	
	/**
	 * Get paging and limit links
	 *
	 * @return
	 */
	public function getNavigationBar() {
		foreach( array( 'first', 'last', 'prev', 'next' ) as $link )
			$labels[$link] = wfMsgHtml( 'paging-' . $link );
		return '( ' . implode( ' | ', $this->getPagingLinks( $labels ) ) . ' ) ( '
			. implode( ' | ', $this->getLimitLinks() ) . ' )';
	}
	
	/**
	 * Inject start-of-list-tag
	 *
	 * @return string
	 */
	public function getStartBody() {
		return '<ul>';
	}

	/**
	 * Inject end-of-list-tag
	 *
	 * @return string
	 */
	public function getEndBody() {
		return '</ul>';
	}

}

?>