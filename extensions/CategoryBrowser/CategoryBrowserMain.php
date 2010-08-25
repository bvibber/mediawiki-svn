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
 * @version 0.2.0
 * @link http://www.mediawiki.org/wiki/Extension:CategoryBrowser
 * @author Dmitriy Sintsov <questpc@rambler.ru>
 * @addtogroup Extensions
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This file is a part of MediaWiki extension.\n" );
}

abstract class CB_AbstractPager {

	var $db;

	/* pager position (actual offset)
	 * 0 means pager has no previous elements
	 * -1 means pager has no elements at all
	 */
	var $offset = -1;
	/* provided "source" offset */
	var $query_offset;
	/* indicates, whether the pager has further elements */
	var $hasMoreEntries = false;
	/* maximal number of entries per page (actual number of entries on page) */
	var $limit = 0;
	/* provided "source" limit */
	var $query_limit;
	/* array of current entries */
	var $entries;

	/*
	 * abstract query (doesn't instantinate)
	 * @param offset - suggested SQL offset
	 * @param limit - suggested SQL limit
	 */
	function __construct( $offset, $limit ) {
		$this->db = & wfGetDB( DB_SLAVE );
		$this->query_limit = intval( $limit );
		$this->query_offset = intval( $offset );
	}

	/*
	 *
	 * initializes hasMoreEntries and array of entries from DB result
	 */
	function setEntries( &$db_result ) {
		$this->hasMoreEntries = false;
		$this->entries = array();
		$count = $this->db->numRows( $db_result );
		if ( $count < 1 ) { return; }
		$this->offset = $this->query_offset;
		$this->limit = $this->query_limit;
		if ( $this->hasMoreEntries = $count > $this->limit ) {
			// do not include "overflow" entry, it belongs to the next page
			$count--;
		}
		// do not include last row (which was loaded only to set hasMoreEntries)
		for ( $i = 0; $i < $count; $i++ ) {
			$row = $this->db->fetchObject( $db_result );
			$this->entries[] = $row;
		}
	}

	// returns previous SQL select offset
	function getPrevOffset() {
		$prev_offset = $this->offset - $this->limit;
		return ( ( $prev_offset >= 0 ) ? $prev_offset : 0 );
	}

	// returns next SQL select offset
	function getNextOffset() {
		return ( ( $this->hasMoreEntries ) ? $this->offset + $this->limit : 0 );
	}

	/*
	 * suggests, what kind of view should be used for instance of the model
	 * @return name of view method
	 * otherwise throws an error
	 * warning: will fail, when called before calling $this->getCurrentRows() !
	 * warning: $this->limit is not set properly before calling $this->getCurrentRows() !
	 */
	function getListType() {
		// it is not enough to check $this->entries[0],
		// because some broken tables might have just some (not all) cat_title = NULL or page_title = NULL
		foreach ( $this->entries as &$entry ) {
			if ( isset( $entry->page_namespace ) && $entry->page_namespace == NS_FILE ) { return 'generateFilesList'; }
			if ( isset( $entry->page_title ) ) { return 'generatePagesList'; }
			if ( isset( $entry->cat_title ) ) { return 'generateCatList'; }
			if ( isset( $entry->cl_sortkey ) ) { return 'generateCatList'; }
		}
		throw new MWException( 'Entries not initialized in ' . __METHOD__ );
	}

} /* end of CB_AbstractPager class */

/*
 * subentries (subcategories, pages, files) pager
 * TODO: gracefully set offset = 0 when too large offset was given
 */
class CB_SubPager extends CB_AbstractPager {

	var $page_table;
	var $category_table;
	var $categorylinks_table;
	// database ID of parent category
	var $parentCatId;
	// database fields to query
	var $select_fields;
	// namespace SQL condition (WHERE part)
	var $ns_cond;
	// javascript function used to navigate between the pages
	var $js_nav_func;

	/*
	 * creates subcategory list pager
	 *
	 * @param $parentCatId id of parent category
	 * @param $offset SQL offset
	 * @param $limit SQL limit
	 *
	 * TODO: query count of parentCatId subcategories/pages/files in category table for progress / percentage display
	 */
	function __construct( $parentCatId, $offset, $limit, $js_nav_func, $select_fields = '*', $ns_cond = '' ) {
		parent::__construct( $offset, $limit );
		$this->page_table = $this->db->tableName( 'page' );
		$this->category_table = $this->db->tableName( 'category' );
		$this->categorylinks_table = $this->db->tableName( 'categorylinks' );
		$this->parentCatId = $parentCatId;
		$this->select_fields = $select_fields;
		$this->ns_cond = $ns_cond;
		$this->js_nav_func = $js_nav_func;
	}

	/*
	 * set offset, limit, hasMoreEntries and entries
	 * @param $offset SQL offset
	 * @param $limit SQL limit
	 */
	function getCurrentRows() {
		/* TODO: change the query to more optimal one (no subselects)
		 * SELECT cl_sortkey,cat_id,cat_title,cat_subcats,cat_pages,cat_files FROM `wiki_page` INNER JOIN `wiki_categorylinks` FORCE INDEX (cl_sortkey) ON (cl_from = page_id) LEFT JOIN `wiki_category` ON (cat_title = page_title AND page_namespace = 14)  WHERE cl_to IN (SELECT cat_title FROM wiki_category WHERE cat_id = 44) AND page_namespace = 14 ORDER BY cl_sortkey LIMIT 0,11
		 */
		$query_string =
			"SELECT {$this->select_fields} " .
			"FROM {$this->page_table} " .
			"INNER JOIN {$this->categorylinks_table} FORCE INDEX (cl_sortkey) ON cl_from = page_id " .
			"LEFT JOIN {$this->category_table} ON cat_title = page_title AND page_namespace = " . NS_CATEGORY . " " .
			"WHERE cl_to IN (" .
				"SELECT cat_title " .
				"FROM {$this->category_table} " .
				"WHERE cat_id = " . $this->db->addQuotes( $this->parentCatId ) .
			") " . ( ( $this->ns_cond == '' ) ? '' : "AND {$this->ns_cond} " ) .
			"ORDER BY cl_sortkey ";
		$res = $this->db->query( $query_string . "LIMIT {$this->query_offset}," . ( $this->query_limit + 1 ), __METHOD__ );
		$this->setEntries( $res );
	}

	// returns JS function call used to navigate to the previous page of this pager
	function getPrevAjaxLink() {
		$result = (object) array(
			"call" => "return CategoryBrowser.{$this->js_nav_func}(this," . $this->parentCatId . "," . $this->getPrevOffset() . ( ( $this->limit == CB_PAGING_ROWS ) ? '' : ',' . $this->limit ) . ')',
			"placeholders" => false
		);
		return $result;
	}

	// returns JS function call used to navigate to the next page of this pager
	function getNextAjaxLink() {
		$result = (object) array(
			"call" => "return CategoryBrowser.{$this->js_nav_func}(this," . $this->parentCatId . ',' . $this->getNextOffset() . ( ( $this->limit == CB_PAGING_ROWS ) ? '' : ',' . $this->limit ) . ')',
			"placeholders" => false
		);
		return $result;
	}

}  /* end of CB_SubPager class */

/*
 * creates a root category pager
 * TODO: gracefully set offset = 0 when too large offset was given
 * TODO: with $conds == '' categories aren't always sorted alphabetically
 */
class CB_RootPager extends CB_AbstractPager {


	/* string paging conds aka filter (WHERE statement) */
	var $conds;
	/* _optional_ instance of CB_SqlCond object used to construct this pager
	 * (in case it's been provided in constructor call)
	 */
	var $sqlCond = null;

	// category name filter (LIKE)
	var $nameFilter = '';
	// category name filter case-insensetive flag (when true, tries to use insensetive LIKE COLLATE)
	var $nameFilterCI = false;

	/*
	 * formal constructor
	 * real instantination should be performed by calling public static methods below
	 */
	function __construct( $offset, $limit ) {
		parent::__construct( $offset, $limit );
	}

	/*
	 * @param $conds - instanceof CB_SqlCond (parentized condition generator)
	 * @param $offset - SQL OFFSET
	 * @param $limit - SQL LIMIT
	 */
	public static function newFromSqlCond( CB_SqlCond $conds, $offset = 0, $limit = CB_PAGING_ROWS ) {
		$rp = new CB_RootPager( $offset, $limit );
		$rp->conds = $conds->getCond();
		$rp->sqlCond = &$conds;
		return $rp;
	}

	/*
	 * @param $tokens - array of infix ops of sql condition
	 * @param $offset - SQL OFFSET
	 * @param $limit - SQL LIMIT
	 */
	public static function newFromInfixTokens( $tokens, $offset = 0, $limit = CB_PAGING_ROWS ) {
		if ( !is_array( $tokens ) ) {
			return null;
		}
		try {
			$sqlCond = CB_SqlCond::newFromInfixTokens( $tokens );
		} catch ( MWException $ex ) {
			return null;
		}
		return self::newFromSqlCond( $sqlCond, $offset, $limit );
	}

	/*
	 * create root pager from the largest non-empty category range
	 * @param $ranges - array of "complete" token queues (range)
	 *   (every range is an stdobject of decoded infix queue and encoded reverse polish queue)
	 */
	public static function newFromCategoryRange( $ranges ) {
		$rp = null;
		foreach ( $ranges as &$range ) {
			$rp = CB_RootPager::newFromInfixTokens( $range->infix_decoded );
			if ( is_object( $rp ) && $rp->offset != -1 ) {
				break;
			}
		}
		return $rp;
	}

	/*
	 * filter catetories by names
	 * @param $cat_name_filter - string category name begins from
	 * @param $cat_name_filter_ci - boolean, true attempts to use case-insensetive search, when available
	 */
	function setNameFilter( $cat_name_filter, $cat_name_filter_ci ) {
		$this->nameFilter = ltrim( $cat_name_filter );
		$this->nameFilterCI = $cat_name_filter_ci;
	}

	/*
	 * performs range query and stores the results
	 */
	function getCurrentRows() {
		$conds = trim( $this->conds );
		// use name filter, when available
		if ( $this->nameFilter != '' ) {
			if ( $conds != '' ) {
				$conds = "( $conds ) AND ";
			}
			$conds .= 'cat_title LIKE ' . $this->db->addQuotes( $this->nameFilter . '%' );
			if ( $this->nameFilterCI && CB_Setup::$cat_title_CI != '' ) {
				// case insensetive search is active
				$conds .= ' COLLATE ' . $this->db->addQuotes( CB_Setup::$cat_title_CI );
			}
		}
		$options = array( 'OFFSET' => $this->query_offset, 'ORDER BY' => 'cat_title', 'LIMIT' => $this->query_limit + 1 );
		$res = $this->db->select( 'category',
			array( 'cat_id', 'cat_title', 'cat_pages', 'cat_subcats', 'cat_files' ),
			$conds,
			__METHOD__,
			$options
		);
		/* set actual offset, limit, hasMoreEntries and entries */
		$this->setEntries( $res );
	}

	// returns JS function call used to navigate to the previous page of this pager
	function getPrevAjaxLink() {
		$result = (object) array(
			"call" => 'return CategoryBrowser.rootCats(\'' . Xml::escapeJsString( $this->sqlCond->getEncodedQueue( false ) ) . '\',' . $this->getPrevOffset() . ( ( $this->limit == CB_PAGING_ROWS ) ? '' : ',' . $this->limit ) . ')',
			"placeholders" => true
		);
		return $result;
	}

	// returns JS function call used to navigate to the next page of this pager
	function getNextAjaxLink() {
		$result = (object) array(
			"call" => 'return CategoryBrowser.rootCats(\'' . Xml::escapeJsString( $this->sqlCond->getEncodedQueue( false ) ) . '\',' . $this->getNextOffset() . ( ( $this->limit == CB_PAGING_ROWS ) ? '' : ',' . $this->limit ) . ')',
			"placeholders" => false
		);
		return $result;
	}

} /* end of CB_RootPager class */

/*
 * browsing class - both for special page and AJAX calls
 */
class CategoryBrowser {

	function __construct() {
		CB_Setup::initUser();
	}

	/*
	 * include stylesheets and scripts; set javascript variables
	 * @param $outputPage - an instance of OutputPage
	 * @param $isRTL - whether the current language is RTL
	 * currently set: cookie prefix;
	 * localAllOp, local1opTemplate, local2opTemplate, localDbFields, localBrackets, localBoolOps, localCmpOps
	 */
	static function headScripts( &$outputPage, $isRTL ) {
		global $wgJsMimeType;
		$outputPage->addLink(
			array( 'rel' => 'stylesheet', 'type' => 'text/css', 'href' => CB_Setup::$ScriptPath . '/category_browser.css?' . CB_Setup::$version )
		);
		if ( $isRTL ) {
			$outputPage->addLink(
				array( 'rel' => 'stylesheet', 'type' => 'text/css', 'href' => CB_Setup::$ScriptPath . '/category_browser_rtl.css?' . CB_Setup::$version )
			);
		}
		$outputPage->addScript(
			'<script type="' . $wgJsMimeType . '" src="' . CB_Setup::$ScriptPath . '/category_browser.js?' . CB_Setup::$version . '"></script>
			<script type="' . $wgJsMimeType . '">
			CB_lib.setCookiePrefix("' . CB_Setup::getJsCookiePrefix() . '");
			CB_ConditionEditor.setLocalNames( ' .
				CategoryBrowser::getJsObject( 'cbLocalMessages', 'apply_button', 'all_op', 'op1_template', 'op2_template', 'ie6_warning' ) . ", \n\t\t\t" .
				CategoryBrowser::getJsObject( 'cbLocalEditHints', 'left', 'right', 'remove', 'copy', 'append', 'clear', 'paste', 'paste_right' ) . ", \n\t\t\t" .
				CategoryBrowser::getJsObject( 'cbLocalDbFields', 's', 'p', 'f' ) . ", \n\t\t\t" .
				CategoryBrowser::getJsObject( 'cbLocalOps', 'lbracket', 'rbracket' ) . ", \n\t\t\t" .
				CategoryBrowser::getJsObject( 'cbLocalOps', 'or', 'and' ) . ", \n\t\t\t" .
				CategoryBrowser::getJsObject( 'cbLocalOps', 'le', 'ge', 'eq' ) .
				' );</script>' . "\n" );
	}

	static function getJsObject( $method_name ) {
		$args = func_get_args();
		array_shift( $args ); // remove $method_name from $args
		$result = '{ ';
		$firstElem = true;
		foreach ( $args as &$arg ) {
			if ( $firstElem ) {
				$firstElem = false;
			} else {
				$result .= ', ';
			}
			$result .= $arg . ': "' . Xml::escapeJsString( call_user_func( array( 'self', $method_name ), $arg ) ) . '"';
		}
		$result .= ' }';
		return $result;
	}

	/*
	 * currently passed to Javascript:
	 * localMessages, localDbFields, localBrackets, localBoolOps, localCmpOps
	 */
	/*
	 * getJsObject callback
	 */
	static private function cbLocalMessages( $arg ) {
		return wfMsg( "cb_${arg}" );
	}

	static private function cbLocalEditHints( $arg ) {
		return wfMsg( "cb_edit_${arg}_hint" );
	}

	/*
	 * getJsObject callback
	 */
	static private function cbLocalOps( $arg ) {
		return wfMsg( "cb_${arg}_op" );
	}

	/*
	 * getJsObject callback
	 */
	static private function cbLocalDbFields( $arg ) {
		return wfMsg( "cb_" . CB_SqlCond::$decoded_fields[ $arg ] );
	}

	/*
	 * generates "complete" ranges
	 * @param $source_ranges source ranges which contain only decoded infix queue
	 * @return "complete" ranges which contain decoded infix queue and encoded polish queue
	 */
	static function generateRanges( array &$source_ranges ) {
		$ranges = array();
		foreach ( $source_ranges as $infix_queue ) {
			$sqlCond = CB_SqlCond::newFromInfixTokens( $infix_queue );
			$ranges[] = (object) array( 'infix_decoded' => $infix_queue, 'polish_encoded' => $sqlCond->getEncodedQueue( false ) );
		}
		return $ranges;
	}

	/*
	 * add new "complete" range to "complete" ranges list
	 * @param $ranges "complete" ranges list (decoded infix, encoded polish)
	 * @param $sqlCond will be added to $ranges only when no such queue already exists
	 * @modifies $ranges
	 */
	static function addRange( array &$ranges, CB_SqlCond $sqlCond ) {
		$encPolishQueue = $sqlCond->getEncodedQueue( false );
		$queueExists = false;
		foreach ( $ranges as &$range ) {
			if ( $range->polish_encoded == $encPolishQueue ) {
				$queueExists = true;
				break;
			}
		}
		if ( !$queueExists ) {
			$sqlCond->getCond(); // build infix queue array
			$ranges[] = (object) array( 'infix_decoded' => $sqlCond->infix_queue, 'polish_encoded' => $encPolishQueue );
		}
	}

	/*
	 * generates SQL condition selector html code
	 * @param $ranges - array of "complete" (decode infix/encoded polish) token queues
	 * @param $rootPager - root pager currently used with this selector
	 * @return selector html code
	 */
	static function generateSelector( array &$ranges, CB_RootPager $rootPager ) {
		# {{{ condition form/select template
		$condOptList = array();
		// do not pass current pager's limit because it's meaningless
		// we need MAX (default) possible limit, not the current limit
		// also current limit is being calculated only during the call $pager->getCurrentRows()
		// TODO: implement the field to select pager's default limit
		$js_func_call = 'return CategoryBrowser.setExpr(this,' . CB_PAGING_ROWS . ')';
		// FF doesn't always fire onchange, IE doesn't always fire onmouseup
		$condFormTpl = array (
			array( '__tag' => 'noscript', 'class' => 'cb_noscript', 0 => wfMsg( 'cb_requires_javascript' ) ),
			array( '__tag' => 'form', '__end' => "\n",
				array( '__tag' => 'select', 'id' => 'cb_expr_select', 'onmouseup' => $js_func_call, 'onchange' => $js_func_call, '__end' => "\n", 0 => &$condOptList )
			)
		);
		# }}}
		$queueFound = false;
		$selectedEncPolishQueue = $rootPager->sqlCond->getEncodedQueue( false );
		foreach ( $ranges as &$range ) {
			$condOptList[] = self::generateOption( $range, $selectedEncPolishQueue );
			if ( $range->polish_encoded == $selectedEncPolishQueue ) {
				$queueFound = true;
			}
		}
		if ( !$queueFound ) {
			throw new MWException( 'Either the selected queue was not added to ranges list via CategoryBrowser::addRange(), or wrong ranges list passed to ' . __METHOD__ );
		}
		return CB_XML::toText( $condFormTpl );
	}

	static function generateOption( $range, $selectedValue, $nodeName = 'option' ) {
		# {{{ condition select's option template
		$condOptVal = '';
		$condOptName = '';
		$condOptInfix = '';
		$condOptTpl =
			array( '__tag' => $nodeName, 'value' => &$condOptVal, 'infixexpr' => &$condOptInfix, 0 => &$condOptName, '__end' => "\n" );
		# }}}
		$le = new CB_LocalExpr( $range->infix_decoded );
		$condOptVal = CB_Setup::specialchars( $range->polish_encoded );
		$sqlCond = CB_SqlCond::newFromEncodedPolishQueue( $range->polish_encoded );
		$condOptInfix = CB_Setup::specialchars( $sqlCond->getEncodedQueue( true ) );
		if ( $range->polish_encoded == $selectedValue ) {
			$condOptTpl['selected'] = null;
		}
		$condOptName = CB_Setup::entities( $le->toString() );
		return CB_XML::toText( $condOptTpl );
	}

	function initNavTpl() {
		# {{{ navigation link (prev,next) template
		$this->nav_link = '';
		if ( !isset( $this->nav_link_tpl ) ) {
			$this->nav_link_tpl =
				array( '__tag' => 'div', 'class' => 'cb_cat_container', '__end' => "\n", 0 => &$this->nav_link );
		}
		# }}}
	}

	function initAjaxLinkTpl() {
		# {{{ ajax link template
		$this->ajax_onclick = '';
		$this->ajax_link_text = '';
		$this->ajax_link_comment = '';
		if ( !isset( $this->ajax_link_tpl ) ) {
			$this->ajax_link_tpl =
				array(
					array( '__tag' => 'a', 'class' => 'cb_sublink', 'href' => '', 'onclick' => &$this->ajax_onclick, 0 => &$this->ajax_link_text ),
					array( '__tag' => 'span', 'class' => 'cb_comment', 0 => &$this->ajax_link_comment )
				);
		}
		# }}}
	}

	function initSortkeyTpl() {
		# {{{ category sortkey hint template
		$this->sortkey_hint = '';
		if ( !isset( $this->sortkey_hint_tpl ) ) {
			$this->sortkey_hint_tpl = array( '__tag' => 'span', 'class' => 'cb_comment', 'style' => 'padding:0em 0.1em 0em 0.1em;', 0 => &$this->sortkey_hint );
		}
		# }}}
	}

	function generateCatList( CB_AbstractPager $pager ) {
		if ( $pager->offset == -1 ) {
			return ''; // list has no entries
		}
		# {{{ one category container template
		$subcat_count_hint = '';
		$cat_expand_sign = '';
		$cat_link = '';
		$cat_tpl =
			array( '__tag' => 'div', 'class' => 'cb_cat_container', '__end' => "\n",
				array( '__tag' => 'div', 'class' => 'cb_cat_controls',
					array( '__tag' => 'span', 'title' => &$subcat_count_hint, 'class' => 'cb_cat_expand', 0 => &$cat_expand_sign ),
					array( '__tag' => 'span', 'class' => 'cb_cat_item', 0 => &$cat_link )
				)
			);
		# }}}
		$this->initNavTpl();
		$this->initAjaxLinkTpl();
		$this->initSortkeyTpl();
		# create list of categories
		$catlist = array(
			array( '__tag' => 'noscript', 'class' => 'cb_noscript', 0 => wfMsg( 'cb_requires_javascript' ) ),
		);
		# previous page AJAX link
		$this->nav_link = '';
		$prev_link = '&#160;'; // &nbsp;
		$link_obj = $pager->getPrevAjaxLink();
		if ( $pager->offset != 0 ) {
			$this->ajax_onclick = $link_obj->call;
			$prev_offset = $pager->getPrevOffset() + 1;
			$this->ajax_link_text = wfMsg( 'cb_previous_items_link' );
			$this->ajax_link_comment = wfMsg( 'cb_previous_items_stats', $prev_offset, $prev_offset + $pager->limit - 1 );
			$this->nav_link = CB_XML::toText( $this->ajax_link_tpl );
			$prev_link = CB_XML::toText( $this->nav_link_tpl );
		}
		if ( $link_obj->placeholders || $this->nav_link != '' ) {
			$catlist[] = $prev_link;
		}
		# generate entries list
		foreach ( $pager->entries as &$cat ) {
			// cat_title might be NULL sometimes - probably due to DB corruption?
			if ( ( $cat_title_str = $cat->cat_title ) == NULL ) {
				// weird, but occasionally may happen;
				if ( empty( $cat->cl_sortkey ) ) {
					continue;
				}
				$cat_title_str = $cat->cl_sortkey;
				$cat_title_obj = Title::newFromText( $cat_title_str, NS_CATEGORY );
			} else {
				$cat_title_obj = Title::makeTitle( NS_CATEGORY, $cat_title_str );
			}
			$this->ajax_link_comment = '';

			# calculate exact number of pages alone
			$cat->pages_only = intval( $cat->cat_pages ) - intval( $cat->cat_subcats ) - intval( $cat->cat_files );
			# generate tree "expand" sign
			if ( $cat->cat_subcats === NULL ) {
				$cat_expand_sign = 'x';
				$subcat_count_hint = '';
			} elseif ( $cat->cat_subcats > 0 ) {
				$this->ajax_onclick = 'return CategoryBrowser.subCatsPlus(this,' . $cat->cat_id . ')';
				$this->ajax_link_text = '+';
				$cat_expand_sign = CB_XML::toText( $this->ajax_link_tpl );
				$subcat_count_hint = wfMsgExt( 'cb_has_subcategories', array( 'parsemag' ), $cat->cat_subcats );
			} else {
				$cat_expand_sign = '&#160;'; // &nbsp;
				$subcat_count_hint = '';
			}

			# create AJAX links for viewing categories, pages, files, belonging to this category
			$ajax_links = '';
			if ( !empty( $cat->cat_id ) ) {
				$this->ajax_onclick = 'return CategoryBrowser.subCatsLink(this,' . $cat->cat_id . ')';
				$this->ajax_link_text = wfMsgExt( 'cb_has_subcategories', array( 'parsemag' ), $cat->cat_subcats );
				$cat_subcats = ( ( $cat->cat_subcats > 0 ) ? ' | ' . CB_XML::toText( $this->ajax_link_tpl ) : '' );

				$this->ajax_onclick = 'return CategoryBrowser.pagesLink(this,' . $cat->cat_id . ')';
				$this->ajax_link_text = wfMsgExt( 'cb_has_pages', array( 'parsemag' ), $cat->pages_only );
				$cat_pages = ( ( $cat->pages_only > 0 ) ? ' | ' . CB_XML::toText( $this->ajax_link_tpl ) : '' );

				$this->ajax_onclick = 'return CategoryBrowser.filesLink(this,' . $cat->cat_id . ')';
				$this->ajax_link_text = wfMsgExt( 'cb_has_files', array( 'parsemag' ), $cat->cat_files );
				$cat_files = ( ( $cat->cat_files > 0 ) ? ' | ' . CB_XML::toText( $this->ajax_link_tpl ) : '' );
				$ajax_links .= $cat_subcats . $cat_pages . $cat_files;
			}
			$cat_link = CB_Setup::$skin->link( $cat_title_obj, $cat_title_obj->getText() );
			# show the sortkey, when it does not match title name
			# note that cl_sortkey is empty for CB_RootCond pager
			$this->sortkey_hint = '';
			if ( !empty( $cat->cl_sortkey ) &&
					$cat_title_obj->getText() != $cat->cl_sortkey ) {
				$this->sortkey_hint = '(' . CategoryViewer::getSubcategorySortChar( $cat_title_obj, $cat->cl_sortkey ) . ')';
				$cat_link .= CB_XML::toText( $this->sortkey_hint_tpl );
			}
			$cat_link .= $ajax_links;
			# finally add generated $cat_tpl/$cat_link to $catlist
			$catlist[] = CB_XML::toText( $cat_tpl );
		}
		# next page AJAX link
		$this->nav_link = '';
		$next_link = '&#160;'; // &nbsp;
		$link_obj = $pager->getNextAjaxLink();
		if ( $pager->hasMoreEntries ) {
			$this->ajax_onclick = $link_obj->call;
			$this->ajax_link_text = wfMsg( 'cb_next_items_link' );
			$this->ajax_link_comment = wfMsg( 'cb_next_items_stats', $pager->getNextOffset() + 1 );
			$this->nav_link = CB_XML::toText( $this->ajax_link_tpl );
			$next_link = CB_XML::toText( $this->nav_link_tpl );
		}
		if ( $link_obj->placeholders || $this->nav_link != '' ) {
			$catlist[] = $next_link;
		}
		return $catlist;
	}

	function generatePagesList( CB_SubPager $pager ) {
		if ( $pager->offset == -1 ) {
			return ''; // list has no entries
		}
		# {{{ one page container template
		$page_link = '';
		$page_tpl =
			array( '__tag' => 'div', 'class' => 'cb_cat_container', '__end' => "\n",
				array( '__tag' => 'div', 'class' => 'cb_cat_item', 0 => &$page_link )
			);
		# }}}
		$this->initNavTpl();
		$this->initAjaxLinkTpl();
		$this->initSortkeyTpl();
		# create list of pages
		$pagelist = array();
		# previous page AJAX link
		$this->nav_link = '';
		$prev_link = '&#160;'; // &nbsp;
		$link_obj = $pager->getPrevAjaxLink();
		if ( $pager->offset != 0 ) {
			$this->ajax_onclick = $link_obj->call;
			$prev_offset = $pager->getPrevOffset() + 1;
			$this->ajax_link_text = wfMsg( 'cb_previous_items_link' );
			$this->ajax_link_comment = wfMsg( 'cb_previous_items_stats', $prev_offset, $prev_offset + $pager->limit - 1 );
			$this->nav_link = CB_XML::toText( $this->ajax_link_tpl );
			$prev_link = CB_XML::toText( $this->nav_link_tpl );
		}
		if ( $link_obj->placeholders || $this->nav_link != '' ) {
			$pagelist[] = $prev_link;
		}
		foreach ( $pager->entries as &$page ) {
			$page_title = Title::makeTitle( $page->page_namespace, $page->page_title );
			$page_link = CB_Setup::$skin->link( $page_title, $page_title->getPrefixedText() );
			# show the sortkey, when it does not match title name
			# note that cl_sortkey is empty for CB_RootCond pager
			$this->sortkey_hint = '';
			if ( !empty( $page->cl_sortkey ) &&
					$page_title->getText() != $page->cl_sortkey ) {
				$this->sortkey_hint = '(' . CategoryViewer::getSubcategorySortChar( $page_title, $page->cl_sortkey ) . ')';
				$page_link .= CB_XML::toText( $this->sortkey_hint_tpl );
			}
			$pagelist[] = CB_XML::toText( $page_tpl );
		}
		# next page AJAX link
		$this->nav_link = '';
		$next_link = '&#160;'; // &nbsp;
		$link_obj = $pager->getNextAjaxLink();
		if ( $pager->hasMoreEntries ) {
			$this->ajax_onclick = $link_obj->call;
			$this->ajax_link_text = wfMsg( 'cb_next_items_link' );
			$this->ajax_link_comment = wfMsg( 'cb_next_items_stats', $pager->getNextOffset() + 1 );
			$this->nav_link = CB_XML::toText( $this->ajax_link_tpl );
			$next_link = CB_XML::toText( $this->nav_link_tpl );
		}
		if ( $link_obj->placeholders || $this->nav_link != '' ) {
			$pagelist[] = $next_link;
		}
		return $pagelist;
	}

	function generateFilesList( CB_SubPager $pager ) {
		global $wgOut, $wgCategoryMagicGallery;
		// unstub $wgOut, otherwise $wgOut->mNoGallery may be unavailable
		// strange, but calling wfDebug() instead does not unstub successfully
		$wgOut->getHeadItems();
		if ( $pager->offset == -1 ) {
			return ''; // list has no entries
		}
		# respect extension & core settings
		if ( CB_Setup::$imageGalleryPerRow < 1 || !$wgCategoryMagicGallery || $wgOut->mNoGallery ) {
			return $this->generatePagesList( $pager );
		}
		$this->initNavTpl();
		$this->initAjaxLinkTpl();
		$this->initSortkeyTpl();
		# {{{ gallery container template
		$gallery_html = '';
		$gallery_tpl = array( '__tag' => 'div', 'class' => 'cb_files_container', 0 => &$gallery_html );
		# }}}
		# create list of files (holder of prev/next AJAX links and generated image gallery)
		$filelist = array();
		# create image gallery
		$gallery = new ImageGallery();
		$gallery->setHideBadImages();
		$gallery->setPerRow( CB_Setup::$imageGalleryPerRow );
		# previous page AJAX link
		$prev_link = '&#160;'; // &nbsp;
		$this->nav_link = '';
		$link_obj = $pager->getPrevAjaxLink();
		if ( $pager->offset != 0 ) {
			$this->ajax_onclick = $link_obj->call;
			$prev_offset = $pager->getPrevOffset() + 1;
			$this->ajax_link_text = wfMsg( 'cb_previous_items_link' );
			$this->ajax_link_comment = wfMsg( 'cb_previous_items_stats', $prev_offset, $prev_offset + $pager->limit - 1 );
			$this->nav_link = CB_XML::toText( $this->ajax_link_tpl );
		}
		if ( $link_obj->placeholders || $this->nav_link != '' ) {
			$prev_link = CB_XML::toText( $this->nav_link_tpl );
		}
		foreach ( $pager->entries as &$file ) {
			$file_title = Title::makeTitle( $file->page_namespace, $file->page_title );
			# show the sortkey, when it does not match title name
			# note that cl_sortkey is empty for CB_RootCond pager
			$this->sortkey_hint = '';
			if ( !empty( $file->cl_sortkey ) &&
					$file_title->getText() != $file->cl_sortkey ) {
				$this->sortkey_hint = '(' . CategoryViewer::getSubcategorySortChar( $file_title, $file->cl_sortkey ) . ')';
			}
			$gallery->add( $file_title, ( $this->sortkey_hint != '' ) ? CB_XML::toText( $this->sortkey_hint_tpl ) : '' );
		}
		# next page AJAX link
		$next_link = '&#160;'; // &nbsp;
		$this->nav_link = '';
		$link_obj = $pager->getNextAjaxLink();
		if ( $pager->hasMoreEntries ) {
			$this->ajax_onclick = $link_obj->call;
			$this->ajax_link_text = wfMsg( 'cb_next_items_link' );
			$this->ajax_link_comment = wfMsg( 'cb_next_items_stats', $pager->getNextOffset() + 1 );
			$this->nav_link = CB_XML::toText( $this->ajax_link_tpl );
		}
		if ( $link_obj->placeholders || $this->nav_link != '' ) {
			$next_link = CB_XML::toText( $this->nav_link_tpl );
		}
		$filelist = $prev_link;
		if ( !$gallery->isEmpty() ) {
			$gallery_html = $gallery->toHTML();
			$filelist .= CB_XML::toText( $gallery_tpl );
		}
		$filelist .= $next_link;
		return $filelist;
	}

	/*
	 * called via AJAX to get root list for specitied offset, limit
	 * where condition will be read from the cookie previousely set
	 * @param $args[0] : encoded reverse polish queue
	 * @param $args[1] : category name filter string
	 * @param $args[2] : category name filter case insensitive flag
	 * @param $args[3] : offset (optional)
	 * @param $args[4] : limit (optional)
	 */
	static function getRootOffsetHtml() {
		wfLoadExtensionMessages( 'CategoryBrowser' );
		$args = func_get_args();
		$limit = ( count( $args ) > 4 ) ? abs( intval( $args[4] ) ) : CB_PAGING_ROWS;
		$offset = ( count( $args ) > 3 ) ? abs( intval( $args[3] ) ) : 0;
		$nameFilterCI = ( count( $args ) > 2 ) ? $args[2] == 'true' : false;
		$nameFilter = ( count( $args ) > 1 ) ? $args[1] : '';
		$encPolishQueue = ( count( $args ) > 0 ) ? $args[0] : 'all';
		$cb = new CategoryBrowser();
		$sqlCond = CB_SqlCond::newFromEncodedPolishQueue( $encPolishQueue );
		$rootPager = CB_RootPager::newFromSqlCond( $sqlCond, $offset, $limit );
		$rootPager->setNameFilter( $nameFilter, $nameFilterCI );
		$rootPager->getCurrentRows();
		$catlist = $cb->generateCatList( $rootPager );
		return CB_XML::toText( $catlist );
	}

	/*
	 * called via AJAX to get list of (subcategories,pages,files) for specitied parent category id, offset, limit
	 * @param $args[0] : type of pager ('subcats','pages','files')
	 * @param $args[1] : parent category id
	 * @param $args[2] : offset (optional)
	 * @param $args[3] : limit (optional)
	 */
	static function getSubOffsetHtml() {
		$pager_types = array(
			'subcats' => array(
				'js_nav_func' => "subCatsNav",
				'select_fields' => "cl_sortkey, cat_id, cat_title, cat_subcats, cat_pages, cat_files",
				'ns_cond' => "page_namespace = " . NS_CATEGORY
			),
			'pages' => array(
				'js_nav_func' => "pagesNav",
				'select_fields' => "page_title, page_namespace, page_len, page_is_redirect",
				'ns_cond' => "NOT page_namespace IN (" . NS_FILE . "," . NS_CATEGORY . ")"
			),
			'files' => array(
				'js_nav_func' => "filesNav",
				'select_fields' => "page_title, page_namespace, page_len, page_is_redirect",
				'ns_cond' => "page_namespace = " . NS_FILE
			)
		);
		wfLoadExtensionMessages( 'CategoryBrowser' );
		$args = func_get_args();
		if ( count( $args ) < 2 ) {
			return 'Too few parameters in ' . __METHOD__;
		}
		if ( !isset( $pager_types[ $args[0] ] ) ) {
			return 'Unknown pager type in ' . __METHOD__;
		}
		$pager_type = & $pager_types[ $args[0] ];
		$limit = ( count( $args ) > 3 ) ? abs( intval( $args[3] ) ) : CB_PAGING_ROWS;
		$offset = ( count( $args ) > 2 ) ? abs( intval( $args[2] ) ) : 0;
		$parentCatId = abs( intval( $args[1] ) );
		$cb = new CategoryBrowser();
		$pager = new CB_SubPager( $parentCatId, $offset, $limit,
			$pager_type[ 'js_nav_func' ],
			$pager_type[ 'select_fields' ],
			$pager_type[ 'ns_cond' ] );
		$pager->getCurrentRows();
		switch ( $pager->getListType() ) {
		case 'generateCatList' :
			$list = $cb->generateCatList( $pager );
			break;
		case 'generatePagesList' :
			$list = $cb->generatePagesList( $pager );
			break;
		case 'generateFilesList' :
			$list = $cb->generateFilesList( $pager );
			break;
		default :
			return 'Unknown list type in ' . __METHOD__;
		}
		return CB_XML::toText( $list );
	}

	/*
	 * called via AJAX to setup custom edited expression cookie then display category root offset
	 * @param $args[0] : encoded infix expression
	 * @param $args[1] : category name filter string
	 * @param $args[2] : category name filter case insensitive flag
	 * @param $args[3] : 1 - cookie has to be set, 0 - cookie should not be set (expression is pre-defined or already was stored)
	 * @param $args[4] : pager limit (optional)
	 */
	static function applyEncodedQueue() {
		CB_Setup::initUser();
		$args = func_get_args();
		$limit = ( ( count( $args ) > 4 ) ? intval( $args[4] ) : CB_PAGING_ROWS );
		$setCookie = ( ( count( $args ) > 3 ) ? $args[3] != 0 : false );
		$nameFilterCI = ( count( $args ) > 2 ) ? $args[2] == 'true' : false;
		$nameFilter = ( count( $args ) > 1 ) ? $args[1] : '';
		$encInfixQueue = ( ( count( $args ) > 0 ) ? $args[0] : 'all' );
		$sqlCond = CB_SqlCond::newFromEncodedInfixQueue( $encInfixQueue );
		$encPolishQueue = $sqlCond->getEncodedQueue( false );
		if ( $setCookie ) {
			CB_Setup::setCookie( 'rootcond', $encPolishQueue, time() + 60 * 60 * 24 * 7 );
		}
		return self::getRootOffsetHtml( $encPolishQueue, $nameFilter, $nameFilterCI, 0, $limit );
	}

	/*
	 * called via AJAX to generate new selected option when the selected rootcond is new (the rootcond cookie was set)
	 * @param $args[0] currently selected expression in encoded infix format
	 */
	static function generateSelectedOption() {
		wfLoadExtensionMessages( 'CategoryBrowser' );
		CB_Setup::initUser();
		$args = func_get_args();
		if ( count( $args ) < 1 ) {
			throw new MWException( 'Argument 0 is missing in ' . __METHOD__ );
		}
		$encInfixQueue = $args[0];
		$sqlCond = CB_SqlCond::newFromEncodedInfixQueue( $encInfixQueue );
		$ranges = array();
		self::addRange( $ranges, $sqlCond );
		# generate div instead of option to avoid innerHTML glitches in IE
		return self::generateOption( $ranges[0], $sqlCond->getEncodedQueue( false ), 'div' );
	}

} /* end of CategoryBrowser class */
