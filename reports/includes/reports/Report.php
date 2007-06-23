<?php

/**
 * Skeleton report, from which all subsequent reports should be
 * derived
 *
 * @addtogroup Reports
 * @author Rob Church <robchur@gmail.com>
 */
abstract class Report extends SpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( $this->getName() );
	}

	/**
	 * Get the name of the report
	 *
	 * @return string
	 */
	public function getName() {}
	
	/**
	 * Should this report be cached?
	 *
	 * @return bool
	 */
	public function isCacheable() {
		return true;
	}
	
	/**
	 * Is it appropriate to allow filtering redirects?
	 *
	 * @return bool
	 */
	public function allowRedirectFilter() {
		return true;
	}
	
	/**
	 * Is it appropriate to allow filtering namespaces?
	 *
	 * @return bool
	 */
	public function allowNamespaceFilter() {
		return true;
	}
	
	/**
	 * Get a list of namespaces this report can be run
	 * against - false indicates *all* namespaces
	 *
	 * @return mixed
	 */
	public function getApplicableNamespaces() {
		return false;
	}
	
	/**
	 * Return base SQL for the report
	 *
	 * @param Database $dbr Database object being queried
	 * @return string
	 */
	public abstract function getBaseSql( $dbr );

	/**
	 * Get the column used for paging when the report is run live
	 *
	 * @return string
	 */
	public function getPagingColumn() {
		return 'page_id';
	}

	/**
	 * Get a partial WHERE clause to filter on namespace when
	 * the report is run live
	 *
	 * @param int $namespace Namespace to limit to
	 * @return string
	 */
	public function getNamespaceClause( $namespace ) {
		return "page_namespace = {$namespace}";
	}
	
	/**
	 * Get a partial WHERE clause to exclude redirects when
	 * the report is run live
	 *
	 * @return string
	 */
	public function getRedirectClause() {
		return 'page_is_redirect = 0';
	}

	/**
	 * Given a result object, extract additional parameters
	 * as a dictionary for later use
	 *
	 * @param object $row Result row
	 * @return array
	 */
	public function extractParameters( $row ) {
		return array();
	}

	/**
	 * Format an individual result row
	 *
	 * @param Title $title Result title
	 * @param object $row Result row
	 * @param array $params Result parameters
	 * @param Skin $skin User skin
	 * @return string
	 */
	public abstract function formatRow( $title, $row, $params, $skin );
	
	/**
	 * Run the report
	 *
	 * @param mixed $par Parameters passed to the page
	 */
	public function execute( $par = false ) {
		global $wgOut, $wgRequest, $wgLang;
		$this->setHeaders();
		# Filtering UI
		$wgOut->addHtml(
			$this->buildFilterUI(
				$wgRequest->getVal( 'namespace', '' ),
				$wgRequest->getCheck( 'redirects' )
			)
		);
		# Report results
		$pager = new ReportPager( $this );
		if( ( $count = $pager->getNumRows() ) > 0 ) {
			#$wgOut->addHtml( '<p>' . wfMsgHtml( 'report-num-results', $wgLang->formatNum( $count ) ) . '</p>' );
			$wgOut->addHtml( $pager->getNavigationBar() );
			$wgOut->addHtml( $pager->getBody() );
			$wgOut->addHtml( $pager->getNavigationBar() );
		} else {
			$wgOut->addHtml( '<p>' . wfMsgHtml( 'report-no-results' ) . '</p>' );
		}
	}
	
	/**
	 * Build the filtering form for the top of the page
	 *
	 * @param mixed $namespace Pre-select namespace
	 * @param bool $redirects Pre-check redirects toggle
	 * @return string
	 */
	protected function buildFilterUI( $namespace, $redirects ) {
		if( $this->allowNamespaceFilter() || $this->allowRedirectFilter() ) {
			global $wgScript;
			$self = SpecialPage::getTitleFor( $this->getName() );
			$form  = '<fieldset>';
			$form .= '<legend>' . wfMsgHtml( 'report-filter-legend' ) . '</legend>';
			$form .= Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) );
			$form .= Xml::hidden( 'title', $self->getPrefixedUrl() );
			$form .= '<table>';
			# Namespace selector
			if( $this->allowNamespaceFilter() ) {
				$form .= '<tr><td>' . Xml::label( wfMsg( 'report-filter-namespace' ), 'namespace' ) . '</td>';
				#$form .= '<td>' . Xml::namespaceSelector( $namespace, '' ) . '</td></tr>';
				$form .= '<td>' . $this->buildNamespaceSelector( $namespace ) . '</td></tr>';
			}
			# Redirect toggle
			if( $this->allowRedirectFilter() ) {
				$form .= '<tr><td></td>';
				$form .= '<td>' . Xml::checkLabel( wfMsg( 'report-filter-redirects' ), 'redirects',
					'redirects', $redirects ) . '</td></tr>';
			}
			$form .= '<tr><td></td><td>' . Xml::submitButton( wfMsg( 'report-filter-submit' ) ) . '</td></tr>';
			$form .= '</table>';
			$form .= '</fieldset>';
			return $form;
		} else {
			return '';
		}
	}
	
	/**
	 * Build a namespace selector providing appropriate
	 * namespace selections for this report
	 *
	 * @param mixed $select Pre-select namespace
	 * @return string
	 */
	public function buildNamespaceSelector( $select ) {
		global $wgContLang;
		$html  = Xml::openElement( 'select', array( 'id' => 'namespace', 'name' => 'namespace' ) );
		$html .= Xml::option( wfMsg( 'report-filter-namespace-all' ), '' );
		$namespaces = $this->getApplicableNamespaces();
		if( $namespaces === false )
			$namespaces = array_keys( $wgContLang->getNamespaces() );
		foreach( $namespaces as $index ) {
			if( $index >= 0 ) {
				$label = $index != 0
					? $wgContLang->getFormattedNsText( $index )
					: wfMsg( 'blanknamespace' );
				$html .= Xml::option( $label, $index, $select !== '' && $select == $index );
			}
		}
		$html .= Xml::closeElement( 'select' );
		return $html;
	}
	
	/**
	 * Encode a set of parameters for insertion
	 * into the cache
	 *
	 * @param array $params
	 * @return string
	 */
	public static function encodeParams( $params ) {
		return serialize( $params );
	}

	/**
	 * Decode a set of parameters from the cache
	 *
	 * @param string $params
	 * @return array
	 */
	public static function decodeParams( $params ) {
		return unserialize( $params );
	}

}

?>