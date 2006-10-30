<?php
/**
 * Main include file for the DynamicPageList2 extension of MediaWiki.
 * This code is released under the GNU General Public License.
 *
 *  Purpose:
 * 	outputs a union of articles residing in a selection of categories and namespaces using configurable output- and ordermethods
 * 
 * Note: DynamicPageList2 is based on DynamicPageList.
 *
 * Usage:
 * 	require_once("extensions/DynamicPageList2.php"); in LocalSettings.php
 * 
 * @package MediaWiki
 * @subpackage Extensions
 * @link http://meta.wikimedia.org/wiki/DynamicPageList2 Documentation
 * @author n:en:User:IlyaHaykinson 
 * @author n:en:User:Amgine 
 * @author w:de:Benutzer:Unendlich 
 * @author m:User:Dangerman <cyril.dangerville@gmail.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 0.7.7
 */

/*
 * Current version
 */
define('DPL2_VERSION', '0.7.7');

/**
 * Register the extension with MediaWiki
 */
$wgExtensionFunctions[] = "wfDynamicPageList2";
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'DynamicPageList2',
	'author' => '[http://en.wikinews.org/wiki/User:IlyaHaykinson IlyaHaykinson], [http://en.wikinews.org/wiki/User:Amgine Amgine], [http://de.wikipedia.org/wiki/Benutzer:Unendlich Unendlich], [http://meta.wikimedia.org/wiki/User:Dangerman Cyril Dangerville]',
	'url' => 'http://meta.wikimedia.org/wiki/DynamicPageList2',
	'description' => 'hack of the original [http://meta.wikimedia.org/wiki/DynamicPageList DynamicPageList] extension featuring many Improvements',
  	'version' => DPL2_VERSION
  );

/**
 * Extension options 
 */
$wgDPL2MaxCategoryCount = 4; // Maximum number of categories allowed in the Query
$wgDPL2MinCategoryCount = 0; // Minimum number of categories needed in the Query
$wgDPL2MaxResultCount = 50; // Maximum number of results to allow
$wgDPL2CategoryStyleListCutoff = 6; //Max length to format a list of articles chunked by letter as bullet list, if list bigger, columnar format user (same as cutoff arg for CategoryPage::formatList())
$wgDPL2AllowUnlimitedCategories = true; // Allow unlimited categories in the Query
$wgDPL2AllowUnlimitedResults = true; // Allow unlimited results to be shown
$wgDPL2AllowedNamespaces = NULL; // to be initialized at first use of DPL2, array of all namespaces except Media and Special, because we cannot use the DB for these to generate dynamic page lists. Cannot be customized. Use $wgDPL2Options['namespace'] or $wgDPL2Options['notnamespace'] for customization.

/**
 * Map parameters to possible values.
 * A 'default' key indicates the default value for the parameter.
 * A 'pattern' key indicates a pattern for regular expressions (that the value must match).
 * For some options (e.g. 'namespace'), possible values are not yet defined but will be if necessary (for debugging) 
 */	
$wgDPL2Options = array(
	'addcategories' => array('default' => 'false', 'false', 'true'),
	'addeditdate' => array('default' => 'false', 'false', 'true'),
	'addfirstcategorydate' => array('default' => 'false', 'false', 'true'),
	'addpagetoucheddate' => array('default' => 'false', 'false', 'true'),
	'adduser' => array('default' => 'false', 'false', 'true'),
	/**
	 * category= Cat11 | Cat12 | ...
	 * category= Cat21 | Cat22 | ...
	 * ...
	 * [Special value] catX='' (empty string without quotes) means pseudo-categoy of Uncategorized pages
	 * Means pages have to be in category (Cat11 OR (inclusive) Cat2 OR...) AND (Cat21 OR Cat22 OR...) AND...
	 * If '+' prefixes the list of categories (e.g. category=+ Cat1 | Cat 2 ...), only these categories can be used as headings in the DPL. See  'headingmode' param.
	 * Magic words allowed.
	 * @todo define 'category' options (retrieve list of categories from 'categorylinks' table?)
	 */
	'category' => NULL,
	/**
	 * Max of results to display.
	 * Empty count value (default) indicates no count limit.
	 */
	'count' => array('default' => '', 'pattern' => '/^\d*$/'),
	/**
	 * debug=...
	 * - 0: displays no debug message;
	 * - 1: displays fatal errors only; 
	 * - 2: fatal errors + warnings only;
	 * - 3: every debug message.
	 */
	'debug' => array( 'default' => '2', '0', '1', '2', '3'),
	/**
	 * Mode at the heading level with ordermethod on multiple components, e.g. category heading with ordermethod=category,...: 
	 * html headings (H2, H3, H4), definition list, no heading (none), ordered, unordered.
	 */
	'headingmode' => array( 'default' => 'none', 'H2', 'H3', 'H4', 'definition', 'none', 'ordered', 'unordered'),
	/**
	 * Attributes for HTML list items (headings) at the heading level, depending on 'headingmode' (e.g. 'li' for ordered/unordered)
	 * Not yet applicable to 'headingmode=none | definition | H2 | H3 | H4'.
	 * @todo Make 'hitemattr' param applicable to  'none', 'definition', 'H2', 'H3', 'H4' headingmodes.
	 * Example: hitemattr= class="topmenuli" style="color: red;"
	 */
	'hitemattr' => array('default' => ''),
	/**
	 * Attributes for the HTML list element at the heading/top level, depending on 'headingmode' (e.g. 'ol' for ordered, 'ul' for unordered, 'dl' for definition)
	 * Not yet applicable to 'headingmode=none'.
	 * @todo Make 'hlistattr' param applicable to  headingmode=none.
	 * Example: hlistattr= class="topmenul" id="dmenu"
	 */
	'hlistattr' => array('default' => ''),
	/** 
	 * Inline text is some wiki text used to separate list items with 'mode=inline'.
	 */
	'inlinetext' => array('default' => '&nbsp;-&nbsp;'),
	/**
	 * Attributes for HTML list items, depending on 'mode' ('li' for ordered/unordered, 'span' for others).
	 * Not applicable to 'mode=category'.
	 * @todo Make 'itemattr' param applicable to 'mode=category'.
	 * Example: itemattr= class="submenuli" style="color: red;"
	 */
	'itemattr' => array('default' => ''),
	/**
	 * Attributes for HTML list elements, depending on 'mode' ('ol' for ordered, 'ul' for unordered, 'div' for others)
	 * Can be used with pseudo 'mode=inline' where 'inlinetext' contains one or more <BR/>.
	 * Not applicable to 'mode=category' or 'mode=inline' (with no <BR/> in inlinetext).
	 * @todo Make 'listattr' param applicable to 'mode=category'.
	 * Example: listattr= class="submenul" style="color: red;"
	 */
	'listattr' => array('default' => ''),
	/**
	 * this parameter restricts the output to articles which contain a reference to the specified page.
	 * Magic words allowed.
	 * Examples:   linksto=my article   ,  linksto=Template:my template   ,  linksto = {{FULLPAGENAME}}
	 */
    'linksto' => array('default' => ''),
	/**
	 * Mode for list of pages (possibly within a heading, see 'headingmode' param).
	 * 'none' mode is implemented as a specific submode of 'inline' with <BR/> as inline text
	 */
	'mode' => array('default' => 'unordered', 'category', 'inline', 'none', 'ordered', 'unordered'),
	/**
	 * namespace= Ns1 | Ns2 | ...
	 * [Special value] NsX='' (empty string without quotes) means Main namespace
	 * Means pages have to be in namespace Ns1 OR Ns2 OR...
	 * Magic words allowed.
	 */
	'namespace' => NULL,
	/**
	 * notcategory= Cat1
	 * notcategory = Cat2
	 * ...
	 * Means pages can be NEITHER in category Cat1 NOR in Cat2 NOR...
	 * Magic words allowed.
	 * @todo define 'notcategory' options (retrieve list of categories from 'categorylinks' table?)
	 */
	'notcategory' => NULL,
	/**
	 * notnamespace= Ns1
 	 * notnamespace= Ns2
 	 * ...
	 * [Special value] NsX='' (empty string without quotes) means Main namespace
	 * Means pages have to be NEITHER in namespace Ns1 NOR Ns2 NOR...
	 * Magic words allowed.
	*/
	'notnamespace' => NULL,
	'order' => array('default' => 'ascending', 'ascending', 'descending'),
	/**
	 * 'ordermethod=param1,param2' means ordered by param1 first, then by param2.
	 * @todo: add 'ordermethod=category,categoryadd' (for each category CAT, pages ordered by date when page was added to CAT).
	 */
	'ordermethod' => array('default' => 'title', 'category,firstedit',  'category,lastedit', 'category,pagetouched', 'category,sortkey', 'categoryadd', 'firstedit', 'lastedit', 'pagetouched', 'title', 'user,firstedit', 'user,lastedit'),
	/**
	 * minoredits =... (compatible with ordermethod=...,firstedit | lastedit only)
	 * - exclude: ignore minor edits when sorting the list (rev_minor_edit = 0 only)
	 * - include: include minor edits
	 */
	'minoredits' => array('default' => 'include', 'exclude', 'include'),
	/**
	 * redirects =...
	 * - exclude: excludes redirect pages from lists (page_is_redirect = 0 only)
	 * - include: allows redirect pages to appear in lists
	 * - only: lists only redirect pages in lists (page_is_redirect = 1 only)
	 */
	'redirects' => array('default' => 'exclude', 'exclude', 'include', 'only'),
	'shownamespace' => array('default' => 'true', 'false', 'true'),
	/**
	 * Max # characters of page title to display.
	 * Empty value (default) means no limit.
	 * Not applicable to mode=category.
	 */
	 'titlemaxlength' => array('default' => '', 'pattern' => '/^\d*$/'),
);

/**
 *  Define codes and map debug message to min debug level above which message can be displayed
 */
$wgDPL2DebugCodes = array(
	// (FATAL) ERRORS
	'DPL2_ERR_WRONGNS' => 1,
	'DPL2_ERR_WRONGLINKSTO' => 1,
 	'DPL2_ERR_TOOMANYCATS' => 1,
	'DPL2_ERR_TOOFEWCATS' => 1,
	'DPL2_ERR_CATDATEBUTNOINCLUDEDCATS' => 1,
	'DPL2_ERR_CATDATEBUTMORETHAN1CAT' => 1,
	'DPL2_ERR_MORETHAN1TYPEOFDATE' => 1,
	'DPL2_ERR_WRONGORDERMETHOD' => 1,
	'DPL2_ERR_NOCLVIEW' => 1,
	// WARNINGS
	'DPL2_WARN_UNKNOWNPARAM' => 2,
	'DPL2_WARN_WRONGPARAM' => 2,
	'DPL2_WARN_WRONGPARAM_INT' => 2,
	'DPL2_WARN_NORESULTS' => 2,
	'DPL2_WARN_CATOUTPUTBUTWRONGPARAMS' => 2,
	'DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD' => 2,
	'DPL2_WARN_DEBUGPARAMNOTFIRST' => 2,
	// OTHERS
	'DPL2_QUERY' => 3
);
$wgDPL2DebugMinLevels = array();
$i = 0;
foreach ($wgDPL2DebugCodes as $name => $minlevel ) {
	define( $name, $i );
	$wgDPL2DebugMinLevels[$i] = $minlevel;
	$i++;
}

// Internationalization file
require_once( 'DynamicPageList2.i18n.php' );

function wfDynamicPageList2() {
	global $wgParser, $wgMessageCache, $wgDPL2Messages;
	foreach( $wgDPL2Messages as $sLang => $aMsgs ) {
		$wgMessageCache->addMessages( $aMsgs, $sLang );
	}
	$wgParser->setHook( "DPL", "DynamicPageList2" );
}


// The callback function for converting the input text to HTML output
function DynamicPageList2( $input, $params, &$parser ) {

	error_reporting(E_ALL);
	
	global  $wgUser, $wgContLang, $wgDPL2AllowedNamespaces, $wgDPL2Options, $wgDPL2MaxCategoryCount, $wgDPL2MinCategoryCount, $wgDPL2MaxResultCount, $wgDPL2AllowUnlimitedCategories, $wgDPL2AllowUnlimitedResults;
	
	// INVALIDATE CACHE
	$parser->disableCache();

	$aParams = array();
	
	/**
	 * Initialization
	 */
	 // Local parser created. See http://meta.wikimedia.org/wiki/MediaWiki_extensions_FAQ#How_do_I_render_wikitext_in_my_extension.3F
	$localParser = new Parser();
	$pOptions = $parser->mOptions;
	$pTitle = $parser->mTitle;
	
	// Extension variables
	// Allowed namespaces for DPL2: all namespaces except the first 2: Media (-2) and Special (-1), because we cannot use the DB for these to generate dynamic page lists.
	if( !is_array($wgDPL2AllowedNamespaces) ) { // Initialization
		$aNs = $wgContLang->getNamespaces();
		$wgDPL2AllowedNamespaces = array_slice($aNs, 2, count($aNs), true);
		if( !is_array($wgDPL2Options['namespace']) )
			$wgDPL2Options['namespace'] = $wgDPL2AllowedNamespaces;
		else // Make sure user namespace options are allowed.
			$wgDPL2Options['namespace'] = array_intersect($wgDPL2Options['namespace'], $wgDPL2AllowedNamespaces);
		if( !isset($wgDPL2Options['namespace']['default']) )
			$wgDPL2Options['namespace']['default'] = NULL;
		if( !is_array($wgDPL2Options['notnamespace']) )
			$wgDPL2Options['notnamespace'] = $wgDPL2AllowedNamespaces;
		else
			$wgDPL2Options['notnamespace'] = array_intersect($wgDPL2Options['notnamespace'], $wgDPL2AllowedNamespaces);
		if( !isset($wgDPL2Options['notnamespace']['default']) )
			$wgDPL2Options['notnamespace']['default'] = NULL;
	}	
	
	 // Options
	$aOrderMethods = explode(',', $wgDPL2Options['ordermethod']['default']);
	$sOrder = $wgDPL2Options['order']['default'];
	$sPageOutputMode = $wgDPL2Options['mode']['default'];
	$sHeadingOutputMode = $wgDPL2Options['headingmode']['default'];
	$sMinorEdits = NULL;
	$sRedirects = $wgDPL2Options['redirects']['default'];
	$sInlTxt = $wgDPL2Options['inlinetext']['default'];
	$bShowNamespace = $wgDPL2Options['shownamespace']['default'] == 'true';
	$bAddFirstCategoryDate = $wgDPL2Options['addfirstcategorydate']['default'] == 'true';
	$bAddPageTouchedDate = $wgDPL2Options['addpagetoucheddate']['default'] == 'true';
	$bAddEditDate = $wgDPL2Options['addeditdate']['default'] == 'true';
	$bAddUser = $wgDPL2Options['adduser']['default'] == 'true';
	$bAddCategories = $wgDPL2Options['addcategories']['default'] == 'true';
	$_sCount = $wgDPL2Options['count']['default'];
	$iCount = ($_sCount == '') ? NULL: intval($_sCount);
	$sListHtmlAttr = $wgDPL2Options['listattr']['default'];
	$sItemHtmlAttr = $wgDPL2Options['itemattr']['default'];
	$sHListHtmlAttr = $wgDPL2Options['hlistattr']['default'];
	$sHItemHtmlAttr = $wgDPL2Options['hitemattr']['default'];
	$_sTitleMaxLen = $wgDPL2Options['titlemaxlength']['default'];
	$iTitleMaxLen = ($_sTitleMaxLen == '') ? NULL: intval($_sTitleMaxLen);
	$tLinksTo = Title::newFromText($localParser->transformMsg($wgDPL2Options['linksto']['default'], $pOptions));
	
	$aIncludeCategories = array(); // $aIncludeCategories is a 2-dimensional array: Memberarrays are linked using 'AND'
	$aExcludeCategories = array();
	$aCatHeadings = array();
	$aNamespaces = array();
	$aExcludeNamespaces  = array();
	
	// Output
	$output = '';
	
	//logger (display of debug messages)
	$logger = new DPL2Logger();

// ###### PARSE PARAMETERS ######
	$aParams = explode("\n", $input);
	$bIncludeUncat = false; // to check if pseudo-category of Uncategorized pages is included
	
	foreach($aParams as $iParam => $sParam) {
		
		$aParam = explode('=', $sParam, 2);
		if( count( $aParam ) < 2 )
			continue;
		$sType = trim($aParam[0]);
		$sArg = trim($aParam[1]);
		
		switch ($sType) {
			/**
			 * FILTER PARAMETERS
			 */
			case 'category':
				// Init array of categories to include
				$aCategories = array();
				$bHeading = false;
				if($sArg != '' && $sArg[0] == '+') {// categories are headings
					$bHeading = true;
					$sArg[0] = '';
				}
				$aParams = explode('|', $sArg);
				foreach($aParams as $sParam) {
					$sParam=trim($sParam);
					if($sParam == '') { // include uncategorized pages (special value: empty string)
						$bIncludeUncat = true;
						$aCategories[] = '';
					} else {
						$title = Title::newFromText($localParser->transformMsg($sParam, $pOptions));
						if( !is_null($title) )
							$aCategories[] = $title->getDbKey();
					}
				}
				if( !empty($aCategories) ) {
					$aIncludeCategories[] = $aCategories;
					if($bHeading)
						$aCatHeadings = array_unique($aCatHeadings + $aCategories);
				}	
				break;
				
			case 'notcategory':
				$title = Title::newFromText($localParser->transformMsg($sArg, $pOptions));
				if( !is_null($title) )
					$aExcludeCategories[] = $title->getDbKey();
				break;
				
			case 'namespace':
				$aParams = explode('|', $sArg);
				foreach($aParams as $sParam) {
					$sParam=trim($sParam);
					$sNs = $localParser->transformMsg($sParam, $pOptions);
					if( !in_array($sNs, $wgDPL2Options['namespace']) )
						return $logger->msgWrongParam('namespace', $sParam);
					$aNamespaces[] = $wgContLang->getNsIndex($sNs);
				}
				break;
			
			case 'notnamespace':
				$sArg=trim($sArg);
				$sNs = $localParser->transformMsg($sArg, $pOptions);
				if( !in_array($sNs, $wgDPL2Options['notnamespace']) )
					return $logger->msgWrongParam('notnamespace', $sArg);
				$aExcludeNamespaces[] = $wgContLang->getNsIndex($sNs);
				break;
			
			case 'linksto':
				$tLinksTo = Title::newFromText($localParser->transformMsg($sArg, $pOptions));
				if( is_null($tLinksTo) ) // wrong param
					return $logger->msgWrongParam('linksto', $sArg);
				break;
			
			case 'minoredits':
				if( in_array($sArg, $wgDPL2Options['minoredits']) )
					$sMinorEdits = $sArg;
				else { //wrong param val, using default
					$sMinorEdits = $wgDPL2Options['minoredits']['default'];
					$output .= $logger->msgWrongParam('minoredits', $sArg);
				}
				break;
				
			case 'redirects':
				if( in_array($sArg, $wgDPL2Options['redirects']) )
					$sRedirects = $sArg;
				else
					$output .= $logger->msgWrongParam('redirects', $sArg);
				break;
				
			case 'count':
				//ensure that $iCount is a number or no count limit;
				if( preg_match($wgDPL2Options['count']['pattern'], $sArg) )
					$iCount = ($sArg == '') ? NULL: intval($sArg);
				else // wrong value
					$output .= $logger->msgWrongParam('count', $sArg);
				break;
			
			/**
			 * CONTENT PARAMETERS
			 */
			case 'addcategories':
				if( in_array($sArg, $wgDPL2Options['addcategories']))
					$bAddCategories = $sArg == 'true';
				else
					$output .= $logger->msgWrongParam('addcategories', $sArg);
				break;
			
			case 'addeditdate':
				if( in_array($sArg, $wgDPL2Options['addeditdate']))
					$bAddEditDate = $sArg == 'true';
				else
					$output .= $logger->msgWrongParam('addeditdate', $sArg);
				break;
			
			case 'addfirstcategorydate':
				if( in_array($sArg, $wgDPL2Options['addfirstcategorydate']))
					$bAddFirstCategoryDate = $sArg == 'true';
				else
					$output .= $logger->msgWrongParam('addfirstcategorydate', $sArg);
				break;
				
			case 'addpagetoucheddate':
				if( in_array($sArg, $wgDPL2Options['addpagetoucheddate']))
					$bAddPageTouchedDate = $sArg == 'true';
				else
					$output .= $logger->msgWrongParam('addpagetoucheddate', $sArg);
				break;

			case 'adduser':
				if( in_array($sArg, $wgDPL2Options['adduser']))
					$bAddUser = $sArg == 'true';
				else
					$output .= $logger->msgWrongParam('adduser', $sArg);
				break;
				
			/**
			 * ORDER PARAMETERS
			 */	
			case 'ordermethod':
				if( in_array($sArg, $wgDPL2Options['ordermethod']) )
					$aOrderMethods = explode(',', $sArg);
				else
					$output .= $logger->msgWrongParam('ordermethod', $sArg);
				break;
			
			case 'order':
				if( in_array($sArg, $wgDPL2Options['order']) )
					$sOrder = $sArg;
				else
					$output .= $logger->msgWrongParam('order', $sArg);
				break;
				
			/**
			 * FORMAT/HTML PARAMETERS
			 * @todo allow addpagetoucheddate, addeditdate, adduser, addcategories to have effect with 'mode=category'
			 */
			case 'headingmode':
				if( in_array($sArg, $wgDPL2Options['headingmode']) )
					$sHeadingOutputMode = $sArg;
				else
					$output .= $logger->msgWrongParam('headingmode', $sArg);
				break;
				
			case 'mode':
				if( in_array($sArg, $wgDPL2Options['mode']) )
					//'none' mode is implemented as a specific submode of 'inline' with <BR/> as inline text
					if($sArg == 'none') {
						$sPageOutputMode = 'inline';
						$sInlTxt = '<BR/>';
					} else
					$sPageOutputMode = $sArg;
				else
					$output .= $logger->msgWrongParam('mode', $sArg);
				break;
				
			case 'inlinetext':
				//parse wiki text and get HTML output
				$pOutput = $localParser->parse($sArg, $pTitle, $pOptions, false);
				$sInlTxt = $pOutput->getText();
				break;
			
			case 'shownamespace':
				if( in_array($sArg, $wgDPL2Options['shownamespace']))
					$bShowNamespace = $sArg == 'true';
				else
					$output .= $logger->msgWrongParam('shownamespace', $sArg);
				break;
			
			case 'titlemaxlength':
				//processed like 'count' param
				if( preg_match($wgDPL2Options['titlemaxlength']['pattern'], $sArg) )
					$iTitleMaxLen = ($sArg == '') ? NULL: intval($sArg);
				else // wrong value
					$output .= $logger->msgWrongParam('titlemaxlength', $sArg);
				break;
				
			case 'listattr':
				$sListHtmlAttr = $sArg;
				break;
			case 'itemattr':
				$sItemHtmlAttr = $sArg;
				break;
			case 'hlistattr':
				$sHListHtmlAttr = $sArg;
				break;
			case 'hitemattr':
				$sHItemHtmlAttr = $sArg;
				break;
				
			/**
			 * DEBUG PARAMETER
			 */
			case 'debug':
				if( in_array($sArg, $wgDPL2Options['debug']) ) {
					if($iParam > 1)
						$output .= $logger->msg(DPL2_WARN_DEBUGPARAMNOTFIRST, "$sArg");
					$logger->iDebugLevel = intval($sArg);
				}
				else
					$output .= $logger->msgWrongParam('debug', $sArg);
				break;
				
			/**
			 * UNKNOWN PARAMETER
			 */
			default:
				$output .= $logger->msg(DPL2_WARN_UNKNOWNPARAM, $sType, implode(', ', array_keys($wgDPL2Options)));
		}
	}
	
	$iIncludeCatCount = count($aIncludeCategories);
	$iTotalIncludeCatCount = count($aIncludeCategories, COUNT_RECURSIVE) - $iIncludeCatCount;
	$iExcludeCatCount = count($aExcludeCategories);
	$iTotalCatCount = $iTotalIncludeCatCount + $iExcludeCatCount;

// ###### CHECKS ON PARAMETERS ######
	// too many categories!!
	if ( ($iTotalCatCount > $wgDPL2MaxCategoryCount) && (!$wgDPL2AllowUnlimitedCategories) )
		return $logger->msg(DPL2_ERR_TOOMANYCATS, "$wgDPL2MaxCategoryCount");

	// too few categories!!
	if ($iTotalCatCount < $wgDPL2MinCategoryCount)
		return $logger->msg(DPL2_ERR_TOOFEWCATS, "$wgDPL2MinCategoryCount");
		
	// no included categories but ordermethod=categoryadd or addfirstcategorydate=true!!
	if ($iTotalIncludeCatCount == 0 && ($aOrderMethods[0] == 'categoryadd' || $bAddFirstCategoryDate == true) )
		return $logger->msg(DPL2_ERR_CATDATEBUTNOINCLUDEDCATS);

	// more than one included category but ordermethod=categoryadd or addfirstcategorydate=true!!
	if ($iTotalIncludeCatCount > 1 && ($aOrderMethods[0] == 'categoryadd' || $bAddFirstCategoryDate == true) )
		return $logger->msg(DPL2_ERR_CATDATEBUTMORETHAN1CAT);
		
	// no more than one type of date at a time!!
	if($bAddPageTouchedDate + $bAddFirstCategoryDate + $bAddEditDate > 1)
		return $logger->msg(DPL2_ERR_MORETHAN1TYPEOFDATE);

	// category-style output requested with not compatible order method
	if ($sPageOutputMode == 'category' && !array_intersect($aOrderMethods, array('sortkey', 'title')) )
		return $logger->msg(DPL2_ERR_WRONGORDERMETHOD,  'mode=category', 'sortkey | title' );
	
	// addpagetoucheddate=true with unappropriate order methods
	if( $bAddPageTouchedDate && !array_intersect($aOrderMethods, array('pagetouched', 'title')) )
		return $logger->msg(DPL2_ERR_WRONGORDERMETHOD,  'addpagetoucheddate=true', 'pagetouched | title' );
	
	// addeditdate=true but not (ordermethod=...,firstedit or ordermethod=...,lastedit)
	//firstedit (resp. lastedit) -> add date of first (resp. last) revision
	if( $bAddEditDate && !array_intersect($aOrderMethods, array('firstedit', 'lastedit')) )
		return $logger->msg(DPL2_ERR_WRONGORDERMETHOD, 'addeditdate=true', 'firstedit | lastedit' );
	
	// adduser=true but not (ordermethod=...,firstedit or ordermethod=...,lastedit)
	/**
	 * @todo allow to add user for other order methods.
	 * The fact is a page may be edited by multiple users. Which user(s) should we show? all? the first or the last one?
	 * Ideally, we could use values such as 'all', 'first' or 'last' for the adduser parameter.
	*/
	if( $bAddUser && !array_intersect($aOrderMethods, array('firstedit', 'lastedit')) )
		return $logger->msg(DPL2_ERR_WRONGORDERMETHOD, 'adduser=true', 'firstedit | lastedit' );
	
	if( isset($sMinorEdits) && !array_intersect($aOrderMethods, array('firstedit', 'lastedit')) )
		return $logger->msg(DPL2_ERR_WRONGORDERMETHOD, 'minoredits', 'firstedit | lastedit' );
	
	/**
	 * If we include the Uncategorized, we need the 'dpl_clview': VIEW of the categorylinks table where we have cl_to='' (empty string) for all uncategorized pages. This VIEW must have been created by the administrator of the mediawiki DB at installation. See the documentation.
	 */
	$dbr =& wfGetDB( DB_SLAVE );
	$sPageTable = $dbr->tableName( 'page' );
	$sCategorylinksTable = $dbr->tableName( 'categorylinks' );
	$sDplClView = '';
	if($bIncludeUncat) {
		$sDplClView = $dbr->tableName( 'dpl_clview' );
		// If the view is not there, we can't perform logical operations on the Uncategorized.
		$res = $dbr->query( "SHOW TABLE STATUS LIKE '" . trim($sDplClView, '`') . "'" );
		if ($dbr->numRows( $res ) != 0) {
			$dbr->freeResult($res);
		} else {
			$sSqlCreate_dpl_clview = 'CREATE VIEW ' . $sDplClView . " AS SELECT IFNULL(cl_from, page_id) AS cl_from, IFNULL(cl_to, '') AS cl_to, cl_sortkey FROM " . $sPageTable . ' LEFT OUTER JOIN ' . $sCategorylinksTable . ' ON page_id=cl_from';
			$output .= $logger->msg(DPL2_ERR_NOCLVIEW, $sDplClView, $sSqlCreate_dpl_clview);
			return $output;
		}
	}
	
	//add*** parameters have no effect with 'mode=category' (only namespace/title can be viewed in this mode)
	if( $sPageOutputMode == 'category' && ($bAddCategories || $bAddEditDate || $bAddFirstCategoryDate || $bAddPageTouchedDate || $bAddUser) )
		$output .= $logger->msg(DPL2_WARN_CATOUTPUTBUTWRONGPARAMS);
		
	//headingmode has effects with ordermethod on multiple components only
	if( $sHeadingOutputMode != 'none' && count($aOrderMethods) < 2 ) {
		$output .= $logger->msg(DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD, $sHeadingOutputMode, 'none');
		$sHeadingOutputMode = 'none';
	}

	// justify limits
	if ( isset($iCount) ) {
		if($iCount > $wgDPL2MaxResultCount)
			$iCount = $wgDPL2MaxResultCount;
	} elseif(!$wgDPL2AllowUnlimitedResults)
		$iCount = $wgDPL2MaxResultCount;


// ###### BUILD SQL QUERY ######
	$sSqlPage_touched = '';
	$sSqlSortkey = '';
	$sSqlCl_to = '';
	$sSqlCats = '';
	$sSqlCl_timestamp = '';
	$sSqlClHeadTable = '';
	$sSqlCond_page_cl_head = '';
	$sSqlClTableForGC = '';
	$sSqlCond_page_cl_gc = '';
	$sRevisionTable = $dbr->tableName( 'revision' );
	$sSqlRevisionTable = '';
	$sSqlRev_timestamp = '';
	$sSqlRev_user = '';
	$sSqlCond_page_rev = '';
	$sPageLinksTable = $dbr->tableName( 'pagelinks' );
	$sSqlPageLinksTable = '';
	$sSqlCond_page_pl = '';
	$sSqlWhere = ' WHERE 1=1 ';
	$sSqlGroupBy = '';
	
	foreach($aOrderMethods as $sOrderMethod) {
		switch ($sOrderMethod) {
			case 'category':
				$sSqlCl_to = "cl_head.cl_to, "; // Gives category headings in the result
				$sSqlClHeadTable = ( in_array('', $aCatHeadings) ? $sDplClView : $sCategorylinksTable ) . ' AS cl_head';
				$sSqlCond_page_cl_head = 'page_id=cl_head.cl_from';
				if(!empty($aCatHeadings))
					$sSqlWhere .= " AND cl_head.cl_to IN ('" . implode("', '", $aCatHeadings) . "')";
				break;
			case 'firstedit':
				$sSqlRevisionTable = $sRevisionTable . ', ';
				$sSqlRev_timestamp = ', min(rev_timestamp) AS rev_timestamp';
				$sSqlCond_page_rev = ' AND page_id=rev_page';
				$sSqlGroupBy = ' GROUP BY ' . $sSqlCl_to . 'page_id';
				break;
			case 'lastedit':
				$sSqlRevisionTable = $sRevisionTable . ', ';
				$sSqlRev_timestamp = ', max(rev_timestamp) AS rev_timestamp';
				$sSqlCond_page_rev = ' AND page_id=rev_page';
				$sSqlGroupBy = ' GROUP BY ' . $sSqlCl_to . 'page_id';
				break;
			case 'sortkey':
				// We need the namespaces with strictly positive indices (DPL2 allowed namespaces, except the first one: Main).
				$aStrictNs = array_slice($wgDPL2AllowedNamespaces, 1, count($wgDPL2AllowedNamespaces), true);
				// map ns index to name
				$sSqlNsIdToText = 'CASE page_namespace';
				foreach($aStrictNs as $iNs => $sNs)
					$sSqlNsIdToText .= ' WHEN ' . $iNs . " THEN '"  . $sNs . "'";
				$sSqlNsIdToText .= ' END';
				// If cl_sortkey is null (uncategorized page), generate a sortkey in the usual way (full page name, underscores replaced with spaces).
				$sSqlSortkey = ", IFNULL(cl_head.cl_sortkey, REPLACE(CONCAT( IF(page_namespace=0, '', CONCAT(" . $sSqlNsIdToText . ", ':')), page_title), '_', ' ')) as sortkey";
				break;
			case 'title':
				$aStrictNs = array_slice($wgDPL2AllowedNamespaces, 1, count($wgDPL2AllowedNamespaces), true);
				// map ns index to name
				$sSqlNsIdToText = 'CASE page_namespace';
				foreach($aStrictNs as $iNs => $sNs)
					$sSqlNsIdToText .= ' WHEN ' . $iNs . " THEN '"  . $sNs . "'";
				$sSqlNsIdToText .= ' END';
				// Generate sortkey like for category links.
				$sSqlSortkey = ", REPLACE(CONCAT( IF(page_namespace=0, '', CONCAT(" . $sSqlNsIdToText . ", ':')), page_title), '_', ' ') as sortkey";
				break;
			case 'user':
				$sSqlRevisionTable = $sRevisionTable . ', ';
				break;
		}
	}
	
	if ( !is_null($tLinksTo) ) {
		$sSqlPageLinksTable = $sPageLinksTable . ' as pl, ';
 		$sSqlCond_page_pl = ' AND page_id=pl.pl_from  AND pl.pl_namespace=' . $tLinksTo->getNamespace() . " AND pl.pl_title='" . $tLinksTo->getDbKey() . "'";
 	}
	
	if ($bAddFirstCategoryDate)
		//format cl_timestamp field (type timestamp) to string in same format as rev_timestamp field
		//to make it compatible with $wgLang->date() function used in function DPL2OutputListStyle() to show "firstcategorydate"
		$sSqlCl_timestamp = ", DATE_FORMAT(c1.cl_timestamp, '%Y%m%d%H%i%s') AS cl_timestamp";
	if ($bAddPageTouchedDate)
		$sSqlPage_touched = ', page_touched';
	if ($bAddUser)
		$sSqlRev_user = ', rev_user, rev_user_text';
	if ($bAddCategories) {
		$sSqlCats = ", GROUP_CONCAT(DISTINCT cl_gc.cl_to ORDER BY cl_gc.cl_to ASC SEPARATOR ' | ') AS cats"; // Gives list of all categories linked from each article, if any.
		$sSqlClTableForGC = $sCategorylinksTable . ' AS cl_gc'; // Categorylinks table used by the Group Concat (GC) function above
		$sSqlCond_page_cl_gc = 'page_id=cl_gc.cl_from';
		$sSqlGroupBy = ' GROUP BY ' . $sSqlCl_to . 'page_id';
	}
	
	// SELECT ... FROM
	$sSqlSelectFrom = 'SELECT DISTINCT ' . $sSqlCl_to . 'page_namespace, page_title' . $sSqlSortkey . $sSqlPage_touched . $sSqlRev_timestamp . $sSqlRev_user . $sSqlCats . $sSqlCl_timestamp . ' FROM ' . $sSqlRevisionTable . $sSqlPageLinksTable . $sPageTable;
	
	// JOIN ...
	if($sSqlClHeadTable != '' || $sSqlClTableForGC != '') {
		$b2tables = ($sSqlClHeadTable != '') && ($sSqlClTableForGC != '');
		$sSqlSelectFrom .= ' LEFT OUTER JOIN (' . $sSqlClHeadTable . ($b2tables ? ', ' : '') . $sSqlClTableForGC . ') ON (' . $sSqlCond_page_cl_head . ($b2tables ? ' AND ' : '') . $sSqlCond_page_cl_gc . ')';
	}
	
	// Include categories...
	$iClTable = 0;
	for ($i = 0; $i < $iIncludeCatCount; $i++) {
		// If we want the Uncategorized
		$sSqlSelectFrom .= ' INNER JOIN ' . ( in_array('', $aIncludeCategories[$i]) ? $sDplClView : $sCategorylinksTable ) . ' AS cl' . $iClTable . ' ON page_id=cl' . $iClTable . '.cl_from AND (cl' . $iClTable . '.cl_to=' . $dbr->addQuotes($aIncludeCategories[$i][0]);
		for ($j = 1; $j < count($aIncludeCategories[$i]); $j++)
			$sSqlSelectFrom .= ' OR cl' . $iClTable . '.cl_to=' . $dbr->addQuotes($aIncludeCategories[$i][$j]);
		$sSqlSelectFrom .= ') ';
		$iClTable++;
	}
	
	// Exclude categories...
	for ($i = 0; $i < $iExcludeCatCount; $i++) {
		$sSqlSelectFrom .=
			' LEFT OUTER JOIN ' . $sCategorylinksTable . ' AS cl' . $iClTable .
			' ON page_id=cl' . $iClTable . '.cl_from' .
			' AND cl' . $iClTable . '.cl_to=' . $dbr->addQuotes($aExcludeCategories[$i]);
		$sSqlWhere .= ' AND cl' . $iClTable . '.cl_to IS NULL';
		$iClTable++;
	}

	// WHERE... (actually finish the WHERE clause we may have started if we excluded categories - see above)
	// Namespace IS ...
	if ( !empty($aNamespaces)) {
		$sSqlWhere .= ' AND page_namespace IN (' . implode (', ', $aNamespaces) . ')';
	}
	// Namespace IS NOT ...
    if ( !empty($aExcludeNamespaces)) {
        $sSqlWhere .= ' AND page_namespace NOT IN (' . implode (', ', $aExcludeNamespaces) . ')';
    }
    // rev_minor_edit IS
    if( isset($sMinorEdits) && $sMinorEdits == 'exclude' )
		$sSqlWhere .= ' AND rev_minor_edit=0';
	// page_is_redirect IS ...	
	switch ($sRedirects) {
		case 'only':
			$sSqlWhere .= ' AND page_is_redirect=1';
			break;
		case 'exclude':
			$sSqlWhere .= ' AND page_is_redirect=0';
			break;
	}
	
	// page_id=rev_page (if revision table required)
	$sSqlWhere .= $sSqlCond_page_rev;
	// page_id=pl.pl_from (if pagelinks table required)
	$sSqlWhere .= $sSqlCond_page_pl;
	
	// GROUP BY ...
	$sSqlWhere .= $sSqlGroupBy;
	
	// ORDER BY ...
	$sSqlWhere .= ' ORDER BY ';
	foreach($aOrderMethods as $i => $sOrderMethod) {
		if($i > 0)
			$sSqlWhere .= ', ';
		switch ($sOrderMethod) {
			case 'category':
				$sSqlWhere .= 'cl_head.cl_to';
				break;
			case 'categoryadd':
				$sSqlWhere .= 'cl0.cl_timestamp';
				break;
			case 'firstedit':
			case 'lastedit':
				$sSqlWhere .= 'rev_timestamp';
				break;
			case 'pagetouched':
				$sSqlWhere .= 'page_touched';
				break;
			case 'sortkey':
			case 'title':
				$sSqlWhere .= 'sortkey';
				break;
			case 'user':
				// rev_user_text can discriminate anonymous users (e.g. based on IP), rev_user cannot (=' 0' for all)
				$sSqlWhere .= 'rev_user_text';
				break;
		}
	}
	if ($sOrder == 'descending')
		$sSqlWhere .= ' DESC';
	else
		$sSqlWhere .= ' ASC';

	// LIMIT ....
	if ( isset($iCount) )
		$sSqlWhere .= ' LIMIT ' . $iCount;



// ###### PROCESS SQL QUERY ######
	//DEBUG: output SQL query 
	$output .= $logger->msg(DPL2_QUERY, $sSqlSelectFrom . $sSqlWhere);
	// echo 'QUERY: [' . $sSqlSelectFrom . $sSqlWhere . "]<br />";

	$res = $dbr->query($sSqlSelectFrom . $sSqlWhere);
	if ($dbr->numRows( $res ) == 0) {
		$output .= $logger->msg(DPL2_WARN_NORESULTS);
		return $output;
	}
	
	$sk =& $wgUser->getSkin();
	// generate link to Special:Uncategorizedpages (used if ordermethod=category,...)
	$tSpecUncat = Title::makeTitle( NS_SPECIAL, 'Uncategorizedpages' );
	$sSpecUncatLnk = $sk->makeKnownLinkObj( $tSpecUncat, wfMsg('uncategorizedpages') );
	// generate title for Special:Contributions (used if adduser=true)
	$tSpecContribs = Title::makeTitle( NS_SPECIAL, 'Contributions' );
	
	// linkBatch used to check the existence of titles
	$linkBatch = new LinkBatch();
	$aHeadings = array();
	//heading titles to be checked by $linkBatch for existence (id in $aHeadings => title)
	$aUncheckedHeadingTitles = array();
	$aArticles = array();
	$aArticles_start_char =array();
	$aAddDates = array();
	$aAddUsers = array();
	//user titles to be checked by $linkBatch for existence (id in $aAddUsers => title)
	$aUncheckedUserTitles = array();
	$aAddCategories = array();
	//category titles to be checked by $linkBatch for existence (id in $aAddCategories => (id' in $aAddCategories[id] => title))
	$aUncheckedCatTitles = array();
	$iArticle = 0;
	while( $row = $dbr->fetchObject ( $res ) ) {
		//HEADINGS (category,... or user,... ordermethods)
		if($sHeadingOutputMode != 'none')
			switch($aOrderMethods[0]) {
				case 'category':
					if($row->cl_to == '') //uncategorized page
						$aHeadings[] = $sSpecUncatLnk;
					else {
						$tCat = Title::makeTitle(NS_CATEGORY, $row->cl_to);
						//The category title may not exist. Add title to LinkBatch to check that out and to make link accordingly.
						$linkBatch->addObj($tCat);
						$aUncheckedHeadingTitles[$iArticle] = $tCat;
						$aHeadings[] = NULL;
					}
					break;
				case 'user':
					if($row->rev_user == 0) //anonymous user
						$aHeadings[] = $sk->makeKnownLinkObj($tSpecContribs,  $wgContLang->convertHtml($row->rev_user_text), 'target=' . $row->rev_user_text);
					else {
						$tUser = Title::makeTitle( NS_USER, $row->rev_user_text );
						//The user title may not exist. Add title to LinkBatch to check that out and to make link accordingly.
						$linkBatch->addObj($tUser);
						$aUncheckedHeadingTitles[$iArticle] = $tUser;
						$aHeadings[] = NULL;
					}
					break;
			}
			
		//PAGE LINK
		$title = Title::makeTitle($row->page_namespace, $row->page_title);
		$sTitleText = $title->getText();
		//chop off title if "too long"
		if( isset($iTitleMaxLen) && (strlen($sTitleText) > $iTitleMaxLen) )
			$sTitleText = substr($sTitleText, 0, $iTitleMaxLen) . '...';
		if ($bShowNamespace)
			//Adapted from Title::getPrefixedText()
			$sTitleText = str_replace( '_', ' ', $title->prefix($sTitleText) );
		$aArticles[] = $sk->makeKnownLinkObj($title,  $wgContLang->convertHtml($sTitleText));
		
		//get first char used for category-style output
		if( isset($row->sortkey) )
			$aArticles_start_char[] = $wgContLang->convert($wgContLang->firstChar($row->sortkey));
			
		//SHOW "PAGE_TOUCHED" DATE, "FIRSTCATEGORYDATE" OR (FIRST/LAST) EDIT DATE
		if($bAddPageTouchedDate)
			$aAddDates[] = $row->page_touched;
		elseif ($bAddFirstCategoryDate)
			$aAddDates[] = $row->cl_timestamp;
		elseif ($bAddEditDate)	
			$aAddDates[] = $row->rev_timestamp;
		else
			$aAddDates[] = '';	
		
		//USER/AUTHOR(S)
		if($bAddUser)
			if($row->rev_user == 0) //not registered users
				$aAddUsers[] = $sk->makeKnownLinkObj($tSpecContribs, $row->rev_user_text, 'target=' . $row->rev_user_text);
			else {
				$tUser = Title::makeTitle( NS_USER, $row->rev_user_text );
				//The user title may not exist. Add title to LinkBatch to check that out and to make link accordingly.
				$linkBatch->addObj($tUser);
				$aUncheckedUserTitles[$iArticle] = $tUser;
				$aAddUsers[] = NULL;
			}
		else
			$aAddUsers[] = '';
		
		//CATEGORY LINKS FROM CURRENT PAGE 
		if(!$bAddCategories || ($row->cats == ''))
			$aAddCategories[] = '';
		else {
			$artCatNames = explode(' | ', $row->cats);
			$artCatLinks = array();
			foreach($artCatNames as $iArtCat => $artCatName) {
				$tArtCat = Title::makeTitle(NS_CATEGORY, $artCatName);
				//The category title may not exist. Add title to LinkBatch to check that out and to make link accordingly.
				$linkBatch->addObj($tArtCat);
				$aUncheckedCatTitles[$iArticle][$iArtCat] = $tArtCat;
				$artCatLinks[] = NULL;
			}
			$aAddCategories[] = $artCatLinks;
		}
		
		$iArticle++;
	}
	$dbr->freeResult( $res );
	
	//ckeck titles in $linkBatch and update links accordingly
	$linkCache = new LinkCache();
	$linkBatch->executeInto($linkCache);
	DPL2UpdateLinks($aUncheckedHeadingTitles, $linkCache, $aHeadings);
	DPL2UpdateLinks($aUncheckedUserTitles, $linkCache, $aAddUsers);
	DPL2UpdateLinks($aUncheckedCatTitles, $linkCache, $aAddCategories);

// ###### SHOW OUTPUT ######
	$outputMode = new DPL2OutputMode($sPageOutputMode, $sInlTxt, $sListHtmlAttr, $sItemHtmlAttr);
	
	if(!empty($aHeadings)) {
		$headingMode = new DPL2OutputMode($sHeadingOutputMode, '', $sHListHtmlAttr, $sHItemHtmlAttr);
		$aHeadingCounts = array_count_values($aHeadings); //count articles under each heading
		$output .= $headingMode->sStartList;
		$headingStart = 0;
		foreach($aHeadingCounts as $heading => $headingCount) {
			$output .= $headingMode->sStartItem;
			$output .= $headingMode->sStartHeading . $heading . $headingMode->sEndHeading;
			$output .= DPL2FormatCount($headingCount, $aOrderMethods[0], $localParser, $pOptions, $pTitle);
			if ($sPageOutputMode == 'category')
				$output .= DPL2OutputCategoryStyle($aArticles, $aArticles_start_char, $headingStart, $headingCount);
			else
				$output .= DPL2OutputListStyle($aArticles, $aAddDates, $aAddUsers, $aAddCategories, $outputMode, $headingStart, $headingCount);
			$output .= $headingMode->sEndItem;
			$headingStart += $headingCount;
		}
		$output .= $headingMode->sEndList;
	}
	elseif($sPageOutputMode == 'category')
		$output .= DPL2OutputCategoryStyle($aArticles, $aArticles_start_char, 0, count($aArticles));
	else
		$output .= DPL2OutputListStyle($aArticles, $aAddDates, $aAddUsers, $aAddCategories, $outputMode, 0, count($aArticles));
	return $output;
}


function DPL2UpdateLinks($aTitles, $linkCache, &$aLinks) {
	global $wgUser, $wgContLang;
	$sk =& $wgUser->getSkin();
	foreach($aTitles as $key => $titleval)
		if( is_array($titleval) )
			DPL2UpdateLinks($titleval, $linkCache, $aLinks[$key]);
		else //$titleval is a single Title object in this case
			$aLinks[$key] = $linkCache->isBadLink($titleval->getPrefixedDbKey()) ?
				$sk->makeBrokenLinkObj($titleval, $wgContLang->convertHtml($titleval->getText())) :
				$sk->makeKnownLinkObj($titleval, $wgContLang->convertHtml($titleval->getText()));
}

function DPL2OutputListStyle ($aArticles, $aAddDates, $aAddUsers, $aAddCategories, $mode, $iStart, $iCount) {
	global $wgUser, $wgLang;
	
	$sk = & $wgUser->getSkin();
	// generate link to Special:Categories (used if addcategories=true)
	$tSpecCats = Title::makeTitle( NS_SPECIAL, 'Categories' );
	$sSpecCatsLnk = $sk->makeKnownLinkObj( $tSpecCats, wfMsg('categories'));
	
	//process results of query, outputing equivalent of <li>[[Article]]</li> for each result,
	//or something similar if the list uses other startlist/endlist;
	$r = $mode->sStartList;
	for ($i = $iStart; $i < $iStart+$iCount; $i++) {
		if($i > $iStart)
			$r .= $mode->sInline; //If mode is not 'inline', sInline attribute is empty, so does nothing
		$r .= $mode->sStartItem;
		if($aAddDates[$i] != '')
			$r .=  $wgLang->timeanddate($aAddDates[$i]) . ': ';
		$r .= $aArticles[$i];
		if($aAddUsers[$i] != '')
			$r .= ' . . ' . $aAddUsers[$i];
		if($aAddCategories[$i] != '')
			$r .= ' . . <SMALL>' . $sSpecCatsLnk . ': ' . implode(' | ', $aAddCategories[$i]) . '</SMALL>';
		$r .= $mode->sEndItem;
	}
	$r .= $mode->sEndList;
	return $r;
}

//slightly different from CategoryPage::formatList() (no need to instantiate a CategoryPage object)
function DPL2OutputCategoryStyle($aArticles, $aArticles_start_char, $iStart, $iCount) {
	global $wgDPL2CategoryStyleListCutoff;
	
	$subaArticles = array_slice($aArticles, $iStart, $iCount);
	$subaArticles_start_char = array_slice($aArticles_start_char, $iStart, $iCount);
	require_once ('CategoryPage.php');
	if ( count ( $subaArticles ) > $wgDPL2CategoryStyleListCutoff ) {
		return CategoryPage::columnList( $subaArticles, $subaArticles_start_char );
	} elseif ( count($subaArticles) > 0) {
		// for short lists of articles in categories.
		return CategoryPage::shortList( $subaArticles, $subaArticles_start_char );
	}
	return '';
}

function DPL2FormatCount( $numart, $headingtype = '', $parser, $poptions, $ptitle ) {
	global $wgLang;
	if($headingtype == 'category')
		$message = 'categoryarticlecount';
	else 
		$message = 'dpl2_articlecount';
	return DPL2MsgExt( $message, $parser, $poptions, $ptitle, $numart);
}

/**
 * Returns message in the requested format after parsing wikitext to html
 * This is meant to be equivalent to wfMsgExt($key, array('parse')) but using a local parser instead of the global one (bugfix).
 * @param string $key Key of the message
 */
function DPL2MsgExt( $key, $parser, &$poptions, $ptitle ) {
	$args = func_get_args();
	array_shift( $args );
	array_shift( $args );
	array_shift( $args );
	array_shift( $args );
	$string = wfMsgGetKey( $key, true, false, false );
	$string = wfMsgReplaceArgs( $string, $args );
	$poptions->setInterfaceMessage(true);
	$parserOutput = $parser->parse( $string, $ptitle, $poptions );
	$poptions->setInterfaceMessage(false);
	return $parserOutput->getText();
}

class DPL2Logger {
	var $iDebugLevel;
	
	function DPL2Logger() {
		global $wgDPL2Options;
		$this->iDebugLevel = $wgDPL2Options['debug']['default'];
	}
	
	function msg($msgid) {
		global $wgDPL2DebugMinLevels;
		if($this->iDebugLevel >= $wgDPL2DebugMinLevels[$msgid]) {
			$args = func_get_args();
			array_shift( $args );
			/**
			 * @todo add a DPL id to identify the DPL tag that generates the message, in case of multiple DPLs in the page
			 */
			return '%DPL2-' . DPL2_VERSION . '-' .  wfMsg('dpl2_debug_' . $msgid, $args) . '<BR/>';
		}
		return '';
	}
	
	function msgWrongParam($paramvar, $val) {
		global $wgContLang, $wgDPL2Options;
		$msgid = DPL2_WARN_WRONGPARAM;
		switch($paramvar) {
			case 'namespace':
			case 'notnamespace':
				$msgid = DPL2_ERR_WRONGNS;
				break;
			case 'linksto':
				$msgid = DPL2_ERR_WRONGLINKSTO;
				break;
			case 'count':
			case 'titlemaxlength':
				$msgid = DPL2_WARN_WRONGPARAM_INT;
				break;
		}
		$paramoptions = array_unique($wgDPL2Options[$paramvar]);
		sort($paramoptions);
		return $this->msg( $msgid, $paramvar, $val, $wgDPL2Options[$paramvar]['default'], implode(' | ', $paramoptions ));
	}
	
}

	
class DPL2OutputMode {
	var $name;
	var $sStartList = '';
	var $sEndList = '';
	var $sStartHeading = '';
	var $sEndHeading = '';
	var $sStartItem = '';
	var $sEndItem = '';
	var $sInline = '';
	
	function DPL2OutputMode($outputmode, $inlinetext = '&nbsp;-&nbsp', $listattr = '', $itemattr = '') {
		$this->name = $outputmode;
		$_listattr = ($listattr == '') ? '' : ' ' . $listattr;
		$_itemattr = ($itemattr == '') ? '' : ' ' . $itemattr;

		switch ($outputmode) {
			case 'inline':
				if( stristr($inlinetext, '<BR />') ) { //one item per line (pseudo-inline)
					$this->sStartList = '<DIV'. $_listattr . '>';
					$this->sEndList = '</DIV>';
				}
				$this->sStartItem = '<SPAN' . $_itemattr . '>';
				$this->sEndItem = '</SPAN>';
				$this->sInline = $inlinetext;
				break;
			case 'ordered':
				$this->sStartList = '<OL' . $_listattr . '>';
				$this->sEndList = '</OL>';
				$this->sStartItem = '<LI'. $_itemattr . '>';
				$this->sEndItem = '</LI>';
				break;
			case 'unordered':
				$this->sStartList = '<UL' . $_listattr . '>';
				$this->sEndList = '</UL>';
				$this->sStartItem = '<LI' . $_itemattr . '>';
				$this->sEndItem = '</LI>';
				break;
			case 'definition':
				$this->sStartList = '<DL' . $_listattr . '>';
				$this->sEndList = '</DL>';
				// item html attributes on dt element or dd element ?
				$this->sStartHeading = '<DT>';
				$this->sEndHeading = '</DT><DD>';
				$this->sEndItem = '</DD>';
				break;
			case 'H2':
			case 'H3':
			case 'H4':
				$this->sStartList = '<DIV' . $_listattr . '>';
				$this->sEndList = '</DIV>';
				$this->sStartHeading = '<' . $outputmode .'>';
				$this->sEndHeading = '</' . $outputmode . '>';
				break;
		}
	}
}
?>
