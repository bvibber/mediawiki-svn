<?php
/**
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/**
 * Special page lists pages without language links
 *
 * @file
 * @ingroup SpecialPage
 * @author Rob Church <robchur@gmail.com>
 */
class WithoutInterwikiPage extends PageQueryPage {
	private $prefix = '';

	function __construct() {
		SpecialPage::__construct( 'Withoutinterwiki' );
	}
	
	function execute( $par ) {
		global $wgRequest, $wgContLang, $wgCapitalLinks;
		$prefix = $wgRequest->getVal( 'prefix', $par );
		if( $wgCapitalLinks ) {
			$prefix = $wgContLang->ucfirst( $prefix );
		}
		$this->prefix = $prefix;
		parent::execute( $par );
	}

	function getPageHeader() {
		global $wgScript, $wgMiserMode;

		# Do not show useless input form if wiki is running in misermode
		if( $wgMiserMode ) {
			return '';
		}

		$prefix = $this->prefix;
		$t = SpecialPage::getTitleFor( $this->getName() );

		return 	Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) ) .
			Xml::openElement( 'fieldset' ) .
			Xml::element( 'legend', null, wfMsg( 'withoutinterwiki-legend' ) ) .
			Xml::hidden( 'title', $t->getPrefixedText() ) .
			Xml::inputLabel( wfMsg( 'allpagesprefix' ), 'prefix', 'wiprefix', 20, $prefix ) . ' ' .
			Xml::submitButton( wfMsg( 'withoutinterwiki-submit' ) ) .
			Xml::closeElement( 'fieldset' ) .
			Xml::closeElement( 'form' );
	}

	function sortDescending() {
		return false;
	}
	
	function getOrderFields() {
		return array( 'page_namespace', 'page_title' );
	}

	// inexpensive?
	function isExpensive() {
		return true;
	}

	function isSyndicated() {
		return false;
	}

	function getQueryInfo() {
		$query = array (
			'tables' => array ( 'page', 'langlinks' ),
			'fields' => array ( 'page_namespace AS namespace',
					'page_title AS title',
					'page_title AS value' ),
			'conds' => array ( 'll_title IS NULL' ),
			'join_conds' => array ( 'langlinks' => array (
					'LEFT JOIN', 'll_from = page_id' ) )
		);
		if ( $this->prefix ) {
			// TODO: Include namespace so this is indexed
			$dbr = wfGetDb( DB_SLAVE );
			$encPrefix = $dbr->escapeLike( $this->prefix );
			$query['conds'][] = "page_title LIKE '{$encPrefix}%'";
		}
		return $query;
	}

	function setPrefix( $prefix = '' ) {
		$this->prefix = $prefix;
	}

}

function wfSpecialWithoutinterwiki() {
	global $wgRequest, $wgContLang;
	list( $limit, $offset ) = wfCheckLimits();
	// Only searching the mainspace anyway
	$prefix = Title::capitalize( $wgRequest->getVal( 'prefix' ), NS_MAIN );
}
