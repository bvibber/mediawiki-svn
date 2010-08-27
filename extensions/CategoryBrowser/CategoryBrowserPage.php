<?php
/**
 * ***** BEGIN LICENSE BLOCK *****
 * This file is part of CategoryBrowser.
 *
 * CategoryBrowser is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * CategoryBrowser is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CategoryBrowser; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * ***** END LICENSE BLOCK *****
 *
 * CategoryBrowser is an AJAX-enabled category filter and browser for MediaWiki.
 *
 * To activate this extension :
 * * Create a new directory named CategoryBrowser into the directory "extensions" of MediaWiki.
 * * Place the files from the extension archive there.
 * * Add this line at the end of your LocalSettings.php file :
 * require_once "$IP/extensions/CategoryBrowser/CategoryBrowser.php";
 *
 * @version 0.2.1
 * @link http://www.mediawiki.org/wiki/Extension:CategoryBrowser
 * @author Dmitriy Sintsov <questpc@rambler.ru>
 * @addtogroup Extensions
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file is a part of MediaWiki extension.\n" );
}

class CategoryBrowserPage extends SpecialPage {

	function __construct() {
		parent::__construct( 'CategoryBrowser' );
		wfLoadExtensionMessages( 'CategoryBrowser' );
		CB_Setup::initUser();
	}

	function setHeaders() {
		global $wgOut, $wgContLang;
		parent::setHeaders();
		CategoryBrowser::headScripts( $wgOut, $wgContLang->isRTL() );
	}

	var $source_ranges =
		array(
// start of test entries
// NOTE: do not forget that only '>=', '<=', '=' comparsions are allowed, otherwise the bug check will be "triggered"
//		array( '(', 'cat_pages >= 1000', 'OR', 'cat_subcats >= 10', ')', 'AND', 'cat_files >= 100' ),
//		array( 'cat_pages >= 1000', 'OR', 'cat_subcats >= 10', 'AND', 'cat_files >= 100' ),
//		array( 'cat_pages >= 100', 'OR', 'cat_subcats >= 10' ),
//		array( '(', 'cat_pages <= 100', 'AND', 'cat_pages >= 10', ')', 'OR', '(', 'cat_subcats >= 0', 'AND', 'cat_subcats <= 10', ')' ),
// end of test entries
		array( '' ), // default value "all",
		array( 'cat_pages >= 100', 'OR', 'cat_subcats >= 1', 'OR', 'cat_files >= 10' ),
		array( 'cat_pages >= 1000', 'OR', 'cat_subcats >= 10', 'OR', 'cat_files >= 100' ),
		array( 'cat_pages >= 10000', 'OR', 'cat_subcats >= 100', 'OR', 'cat_files >= 1000' ),
		array( 'cat_subcats >= 1' ),
		array( 'cat_pages >= 1' ),
		array( 'cat_files >= 1' ),
		array( 'cat_subcats = 0' ),
		array( 'cat_pages = 0' ),
		array( 'cat_files = 0' ),
	);
	var $ranges;

	function execute( $param ) {
		global $wgOut;
		$this->setHeaders();
		$this->ranges = CategoryBrowser::generateRanges( $this->source_ranges );
		$cb = new CategoryBrowser();
		# try to create rootPager from rootcond cookie value
		if ( is_string( $encPolishQueue = CB_Setup::getCookie( 'rootcond' ) ) ) {
			$sqlCond = CB_SqlCond::newFromEncodedPolishQueue( $encPolishQueue );
			$rootPager = CB_RootPager::newFromSqlCond( $sqlCond );
			# add selected condition to range, if not duplicate
			CategoryBrowser::addRange( $this->ranges, $rootPager->sqlCond );
		} else {
			# otherwise, try to create rootPager from the list of predefined infix queues (ranges)
			if ( !is_object( $rootPager = CB_RootPager::newFromCategoryRange( $this->ranges ) ) ) {
				return;
			}
		}
		$rootPager->getCurrentRows();
		/* reverse polish queue encode / decode validations */
		$testCond = CB_SqlCond::newFromEncodedPolishQueue( $rootPager->sqlCond->getEncodedQueue( false ) );
		if ( $rootPager->sqlCond->getCond() != $testCond->getCond() ) {
			throw new MWException( 'Infix queue was not re-built correctly from encoded polish queue in ' . __METHOD__ );
		}
		/* infix queue encode / decode validations */
		$testCond = CB_SqlCond::newFromEncodedInfixQueue( $rootPager->sqlCond->getEncodedQueue( true ) );
		if ( $rootPager->sqlCond->getCond() != $testCond->getCond() ) {
			throw new MWException( 'Infix queue was not re-built correctly from encoded infix queue in ' . __METHOD__ );
		}
		/* end of validations */
		# {{{ top template
		$condSelector = '';
		$catlist = array();
		$js_setNameFilter = 'CategoryBrowser.setNameFilter( this )';
		$nameFilterFields = array(
			array( '__tag' => 'input', 'type' => 'text', 'onkeyup' => $js_setNameFilter, 'onchange' => $js_setNameFilter, 'id' => 'cb_cat_name_filter' )
		);
		if ( CB_Setup::$cat_title_CI != '' ) {
			// case insensitive search is possible
			$nameFilterFields[] = wfMsg( 'cb_cat_name_filter_ci' );
			$nameFilterFields[] = array( '__tag' => 'input', 'type' => 'checkbox', 'onchange' => $js_setNameFilter, 'id' => 'cb_cat_name_filter_ci', 'checked' => null );
		}
		$top_tpl =
			array( '__tag' => 'table', 'class' => 'cb_top_container', '__end' => "\n",
				array( '__tag' => 'tr', '__end' => "\n",
					array( '__tag' => 'td', 'class' => 'cb_toolbox_top', '__end' => "\n", 0 => &$condSelector )
				),
				array( '__tag' => 'tr', '__end' => "\n",
					array( '__tag' => 'td', 'class' => 'cb_toolbox_bottom', '__end' => "\n",
						array( wfMsg( 'cb_cat_name_filter' ) ),
						&$nameFilterFields,
					)
				),
				array( '__tag' => 'tr', '__end' => "\n",
					array( '__tag' => 'td', 'class' => 'cb_toolbox', 'style' => 'display:none; ', '__end' => "\n",
						array( '__tag' => 'div', 'id' => 'cb_editor_container', 0 => '' /* holder of condition line */ ),
						array( '__tag' => 'div', 'class' => 'cb_separate_container', 0 => '' /* holder of apply button */ )
					)
				),
				array( '__tag' => 'tr', '__end' => "\n",
					array( '__tag' => 'td', 'class' => 'cb_toolbox', 'style' => 'display:none; ', '__end' => "\n",
						array( '__tag' => 'div', 'class' => 'cb_copy_line_hint', 0 => wfMsg( 'cb_copy_line_hint' ) ),
						array( '__tag' => 'div', 'id' => 'cb_samples_container', 0 => '' /* holder of samples line */ )
					)
				),
				array( '__tag' => 'tr', '__end' => "\n",
					array( '__tag' => 'td', '__end' => "\n",
						array( '__tag' => 'div', 'id' => 'cb_root_container', 0 => &$catlist )
					)
				)
			);
		# }}}
		$condSelector = CategoryBrowser::generateSelector( $this->ranges, $rootPager );
		$pagerView = new CB_CategoriesView( $rootPager );
		$catlist = $pagerView->generateList();
		$wgOut->addHTML( CB_XML::toText( $top_tpl ) );
	}

} /* end of CategoryBrowserPage class */
