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
		parent::__construct( $this->getName(), $this->getPermission() );
	}

	/**
	 * Get the name of the report
	 *
	 * @return string
	 */
	public function getName() {}
	
	/**
	 * Permission required to view the report
	 *
	 * @return string
	 */
	public function getPermission() {
		return '';
	}
	
	/**
	 * Should this report be cached?
	 *
	 * @return bool
	 */
	public function isCacheable() {
		return true;
	}
	
	/**
	 * Are updates for this report disabled?
	 *
	 * @return bool
	 */
	public function isDisabled() {
		global $wgDisabledReports;
		return in_array( $this->getName(), $wgDisabledReports );
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
	 * Should redirects be filtered from results?
	 *
	 * @return bool
	 */
	public function excludeRedirects() {
		return false;
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
	 * Get the default namespace to filter on;
	 * false to indicate no filtering per default
	 *
	 * @return bool
	 */
	public function getDefaultNamespace() {
		return ( $ns = $this->getApplicableNamespaces() ) !== false
			? $ns[0]
			: false;
	}
	
	/**
	 * Is there a more appropriate title to show when
	 * a particular namespace is selected? Return the
	 * message name here
	 *
	 * @param int $namespace
	 * @return string
	 */
	public function getNamespaceTitleVariant( $namespace ) {
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
	 * Return additional WHERE clauses and other conditions
	 * to which the paging clauses will be appened when
	 * the report runs live
	 *
	 * @param Database $dbr Database object being queried
	 * @return array
	 */
	public function getExtraConditions( $dbr ) {
		return array();
	}

	/**
	 * Get ORDER BY clauses to be applied when the
	 * report is run live
	 *
	 * @return array
	 */
	public function getOrderingClauses() {
		return array();
	}

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
		# Namespace and redirect filter values
		$namespace = $this->getNamespace();
		$redirects = $wgRequest->getCheck( 'redirects' );		
		# Per-namespace title variants
		if( ( $msg = $this->getNamespaceTitleVariant( $namespace ) ) !== false )
			$wgOut->setPageTitle( wfMsg( $msg ) );
		# Cache information, etc.
		$pager = $this->getPager();
		if( $this->isDisabled() ) {
			$wgOut->addHtml( '<div class="mw-report-disabled">' . wfMsgExt( 'report-disabled', 'parse' ) . '</div>' );
		} elseif( $pager instanceof CachedReportPager ) {
			$wgOut->addHtml( $this->getCacheInfo() );
		}
		# Filtering UI
		$wgOut->addHtml( $this->buildFilterUI( $namespace, $redirects ) );
		# Report results
		if( ( $count = $pager->getNumRows() ) > 0 ) {
			$wgOut->addHtml( $pager->getNavigationBar() );
			$wgOut->addHtml( $pager->getBody() );
			$wgOut->addHtml( $pager->getNavigationBar() );
		} else {
			$wgOut->addHtml( '<p>' . wfMsgHtml( 'report-no-results' ) . '</p>' );
		}
	}
	
	/**
	 * Get an appropriate pager for this report
	 *
	 * @return ReportPager
	 */
	private function getPager() {
		global $wgMiserMode;
		return $wgMiserMode && $this->isCacheable()
			? new CachedReportPager( $this )
			: new ReportPager( $this );
	}

	/**
	 * Get the namespace to filter results for, or false
	 * to omit filtering
	 *
	 * @return mixed
	 */
	public function getNamespace() {
		global $wgRequest;
		if( $this->allowNamespaceFilter() ) {
			return $wgRequest->getCheck( 'namespace' )
				? $wgRequest->getInt( 'namespace' )
				: $this->getDefaultNamespace();
		} else {
			return false;
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
		$namespaces = $this->getApplicableNamespaces();
		if( $namespaces === false ) {
			$html .= Xml::option( wfMsg( 'report-filter-namespace-all' ), '', $select === false );
			$namespaces = array_keys( $wgContLang->getNamespaces() );
		}
		foreach( $namespaces as $index ) {
			if( $index >= 0 && $index != NS_MEDIAWIKI && $index != NS_MEDIAWIKI_TALK ) {
				$label = $index != 0
					? $wgContLang->getFormattedNsText( $index )
					: wfMsg( 'blanknamespace' );
				$html .= Xml::option( $label, $index, $select === $index );
			}
		}
		$html .= Xml::closeElement( 'select' );
		return $html;
	}
	
	/**
	 * Build a box containing information about when the
	 * cache for this report was last updated
	 *
	 * @return string
	 */
	private function getCacheInfo() {
		global $wgLang;
		$html  = '<div class="mw-report-cached">';
		if( ( $ts = ReportCache::getUpdateTime( $this ) ) !== false ) {
			$html .= wfMsgExt( 'report-cached-timestamp', 'parse', $wgLang->timeAndDate( $ts, true ) );
		} else {
			$html .= wfMsgExt( 'report-cached', 'parse' );
		}
		$html .= '</div>';
		return $html;
	}
	
	/**
	 * Get a list of all reports
	 *
	 * @return array
	 */
	public static function getReports() {
		return array(
			'RedirectReport',
			'ShortPagesReport',
			'UncategorisedPagesReport',
			'WithoutInterwikiReport',
		) + $GLOBALS['wgCustomReports'];
	}
	
}

?>