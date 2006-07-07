<?php

/*

 Version:
	Hack v0.4 (DynamicPageList2 is based on DynamicPageList)
	
 Purpose:outputs a union of articles residing in a selection 
				of categories and namespaces using configurable output- and
				ordermethods

 Contributors: 
	n:en:User:IlyaHaykinson n:en:User:Amgine w:de:Benutzer:Unendlich m:User:Dangerman
	http://en.wikinews.org/wiki/User:Amgine
	http://en.wikinews.org/wiki/User:IlyaHaykinson
	http://de.wikipedia.org/wiki/Benutzer:Unendlich
	http://meta.wikimedia.org/wiki/User:Dangerman

 Licence:
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or 
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License along
	with this program; if not, write to the Free Software Foundation, Inc.,
	59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
	http://www.gnu.org/copyleft/gpl.html

 Installation:
	To install, add following to LocalSettings.php
   include("extensions/intersection/DynamicPageList2.php");
*/


$wgDPL2MaxCategoryCount = 4;				// Maximum number of categories allowed in the Query
$wgDPL2MinCategoryCount = 0;				// Minimum number of categories needed in the Query
$wgDPL2MaxResultCount = 50;				// Maximum number of results to allow
$wgDPL2MaxCategoryShortListResultCount = 10; // Maximum number of results to allow for a short list with mode=category, if max exceed, results output in a column list
$wgDPL2AllowUnlimitedCategories = true;			// Allow unlimited categories in the Query
$wgDPL2AllowUnlimitedResults = true;				// Allow unlimited results to be shown

$wgExtensionFunctions[] = "wfDynamicPageList2";
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'DynamicPageList2',
	'author'=>'[http://en.wikinews.org/wiki/User:IlyaHaykinson IlyaHaykinson], [http://en.wikinews.org/wiki/User:Amgine Amgine], [http://de.wikipedia.org/wiki/Benutzer:Unendlich Unendlich], [http://meta.wikimedia.org/wiki/User:Dangerman Cyril Dangerville]',
	'url'=>'http://meta.wikimedia.org/wiki/DynamicPageList2',
	'description'=>'hack of the original [http://meta.wikimedia.org/wiki/DynamicPageList DynamicPageList] extension from DynamicPageList featuring many Improvements',
  	'version'=>'0.4'
  );

function wfDynamicPageList2() {
	global $wgParser, $wgMessageCache;

	$wgMessageCache->addMessages( array(
					'dpl2_toomanycats' 					=> 'DynamicPageList2: Too many categories!',
					'dpl2_toofewcats' 					=> 'DynamicPageList2: Too few categories!',
					'dpl2_noresults' 					=> 'DynamicPageList2: No results!',
					'dpl2_noincludedcatsorns' 			=> 'DynamicPageList2: You need to include at least one category or specify a namespace!',
					'dpl2_noincludedcatsbutcatdate' 		=> "DynamicPageList2: You need to include at least one category if you want to use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
					'dpl2_morethanonecatbutcatdate'		=> "DynamicPageList2: If you include more than one category you cannot use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
					'dpl2_addmorethanonetypeofdate'		=> 'DynamicPageList2: You cannot add more than one type of date at a time.',		
					'dpl2_catoutputwithwrongordermethod'	=> "DynamicPageList2: You have to use 'ordermethod=title' or 'ordermethod=category' when using category-style output!",
					'dpl2_addpagetoucheddatewithwrongordermethod' => "DynamicPageList2: You cannot use 'addpagetoucheddate=true' with 'ordermethod=firstedit' or 'ordermethod=lastedit' or 'ordermethod=categoryadd'.",
					'dpl2_addeditdatewithwrongordermethod'	=> "DynamicPageList2: You have to use 'ordermethod=firstedit' or 'ordermethod=lastedit' when using 'addeditdate=true'.",
					'dpl2_adduserwithwrongordermethod'	=> "DynamicPageList2: You have to use 'ordermethod=firstedit' or 'ordermethod=lastedit' when using 'adduser=true'.\n
	TODO: allow 'adduser=true' for other order methods. 
	In fact, a page may be edited by multiple users. Which user(s) should we show? all? the first or the last one?
	Suggested solution: use values such as 'all', 'first' or 'last' for the adduser parameter."
					)
				);
	$wgParser->setHook( "DPL", "DynamicPageList2" );
}


// The callback function for converting the input text to HTML output
// TODO: use the parser and wiki text in the code for the output (instead of HTML)
function DynamicPageList2( $input, $params, &$parser ) {

	error_reporting(E_ALL);

	// INVALIDATE CACHE
	$parser->disableCache();
	
	global $wgTitle;
	global $wgOut;
	global $wgUser;
	global $wgLang;
	global $wgContLang;
	global $wgDPL2MaxCategoryCount, $wgDPL2MinCategoryCount, $wgDPL2MaxResultCount;
	global $wgDPL2AllowUnlimitedCategories, $wgDPL2AllowUnlimitedResults;

	$aParams = array();
	$bCountSet = false;
	
	// Default Values
	$sOrderMethod = 'title';
	$sOrder = 'descending';	
	$sOutputMode = 'unordered';	
	$sRedirects = 'exclude';
	$sInlSymbol = '-';
	$bShowNamespace = true;
	$bSuppressErrors = false;
	$bAddFirstCategoryDate = false;
	$bAddPageTouchedDate = false;
	$bAddEditDate = false;
	$bAddUser = false;
	$bAddCategories = false;
	
	$aaIncludeCategories = array();		// $aaIncludeCategories is a two 2-dimensional array: Memberarrays are linked using 'AND'
	$aExcludeCategories = array();
	$aNamespaces = array();
	
// ###### PARSE PARAMETERS ######

	$aParams = explode("\n", $input);
	
	$parser = new Parser;
	$poptions = new ParserOptions;
	
	foreach($aParams as $sParam) {
		
		$aParam = explode("=", $sParam);
		if( count( $aParam ) < 2 )
			continue;
		$sType = trim($aParam[0]);
		$sArg = trim($aParam[1]);
		
		switch ($sType) {
			case 'category':
				$aCategories = array();			// Categories in one line separated by '|' are linked using 'OR'
				$aParams = explode("|", $sArg);
				foreach($aParams as $sParam) {
					$sParam=trim($sParam);
					$title = Title::newFromText( $parser->transformMsg($sParam, $poptions) );
					if( $title != NULL )
						$aCategories[] = $title;
				}
				if (!empty($aCategories))
					$aaIncludeCategories[] = $aCategories;	
				break;
				
			case 'notcategory':
				$title = Title::newFromText( $parser->transformMsg($sParam, $poptions) );
				if( $title != NULL )
					$aExcludeCategories[] = $title; 
				break;
				
			case 'namespace':
				$aParams = explode("|", $sArg);
				foreach($aParams as $sParam) {
					$sParam=trim($sParam);
					$sNS = $wgContLang->getNsIndex($sParam);
					if ( $sNS != NULL )
						$aNamespaces[] = $sNS;
					elseif (intval($sParam)>=0)
						$aNamespaces[] = intval($sParam);
				}
				break;
				
			case 'count':
				//ensure that $iCount is a number;
				$iCount = IntVal( $sArg );
				$bCountSet = true;
				break;
				
			//TODO: solve the issue: addpagetoucheddate, addeditdate, adduser, addcategories have no effect with 'mode=category'
			case 'mode':
				if ( in_array($sArg, array('none','ordered','unordered','category','inline')) )
					$sOutputMode = $sArg;
				break;
				
			case 'inlinesymbol':
				$sInlSymbol = strip_tags($sArg);
				break;
				
			case 'order':
				if ( in_array($sArg, array('ascending','descending')) )
					$sOrder = $sArg;
				break;	
											
			// TODO: add order methods: (category,title), (category,pagetouched), etc.
			case 'ordermethod':
				if ( in_array($sArg, array('pagetouched','firstedit','lastedit','categoryadd','title','category')) )
					$sOrderMethod = $sArg;
				break;
				
			case 'redirects':
				if ( in_array($sArg, array('include','only','exclude')) )
					$sRedirects = $sArg;
				break;
			
			case 'suppresserrors':
				if ($sArg == 'true') $bSuppressErrors = true;
				if ($sArg == 'false') $bSuppressErrors = false;
				break;
				
			case 'addfirstcategorydate':
				if ($sArg == 'true') $bAddFirstCategoryDate = true;
				if ($sArg == 'false') $bAddFirstCategoryDate = false;
				break;
				
			case 'addpagetoucheddate':
				if ($sArg == 'true') $bAddPageTouchedDate = true;
				if ($sArg == 'false') $bAddPageTouchedDate = false;
				break;
				
			case 'addeditdate': //default
				if ($sArg == 'true') $bAddEditDate = true;
				if ($sArg == 'false') $bAddEditDate = false;
				break;
			
			case 'adduser':
				if ($sArg == 'true') $bAddUser = true;
				if ($sArg == 'false') $bAddUser = false;
				break;
			
			case 'addcategories':
				if ($sArg == 'true') $bAddCategories = true;
				if ($sArg == 'false') $bAddCategories = false;
				break;
			
			case 'shownamespace':
				if ($sArg == 'true') $bShowNamespace = true;
				if ($sArg == 'false') $bShowNamespace = false;
				break;
		}
	}
	
	$iIncludeCatCount = count($aaIncludeCategories);
	$iTotalIncludeCatCount = count($aaIncludeCategories,COUNT_RECURSIVE) - $iIncludeCatCount;
	$iExcludeCatCount = count($aExcludeCategories);
	$iTotalCatCount = $iIncludeCatCount + $iExcludeCatCount;

// ###### CHECKS ON PARAMETERS ######
	
	// no included/excluded categories or namespaces!!
	if ($iTotalCatCount == 0 && empty($aNamespaces) )
		return htmlspecialchars( wfMsg( 'dpl2_noincludedcatsorns' ) );	

	// too many categories!!
	if ( ($iTotalCatCount > $wgDPL2MaxCategoryCount) && (!$wgDPL2AllowUnlimitedCategories) )
		return htmlspecialchars( wfMsg( 'dpl2_toomanycats' ) );			

	// too few categories!!
	if ($iTotalCatCount < $wgDPL2MinCategoryCount)
		return htmlspecialchars( wfMsg( 'dpl2_toofewcats' ) );			

	// no included categories but ordermethod=categoryadd or addfirstcategorydate=true!!
	if ($iTotalIncludeCatCount == 0 && ($sOrderMethod == 'categoryadd' || $bAddFirstCategoryDate == true) ) 
		return htmlspecialchars( wfMsg( 'dpl2_noincludedcatsbutcatdate' ) );

	// more than one included category but ordermethod=categoryadd or addfirstcategorydate=true!!
	if ($iTotalIncludeCatCount > 1 && ($sOrderMethod == 'categoryadd' || $bAddFirstCategoryDate == true) ) 
		return htmlspecialchars( wfMsg( 'dpl2_morethanonecatbutcatdate' ) );
		
	// add one type of date at a time!!
	// TODO: this may be more elegant using a XOR operator
	if( ($bAddPageTouchedDate && ($bAddFirstCategoryDate || $bAddEditDate))  || ($bAddFirstCategoryDate && $bAddEditDate) )
		return htmlspecialchars( wfMsg( 'dpl2_addmorethanonetypeofdate' ) );

	// category-style output requested but not ordermethod=title!!
	if ($sOutputMode == 'category' && $sOrderMethod != 'title' && $sOrderMethod != 'category')
		return htmlspecialchars( wfMsg( 'dpl2_catoutputwithwrongordermethod' ) );
	
	// addpagetoucheddate=true with unappropriate order methods
	if( $bAddPageTouchedDate && ($sOrderMethod == 'firstedit' || $sOrderMethod == 'lastedit' || $sOrderMethod == 'categoryadd') )
		return htmlspecialchars( wfMsg( 'dpl2_addpagetoucheddatewithwrongordermethod' ) );
	
	// addeditdate=true but not (ordermethod=firstedit or ordermethod=lastedit)
	// One of these 2 ordermethods determines the edit date to add.
	if( $bAddEditDate && $sOrderMethod != 'firstedit' && $sOrderMethod != 'lastedit')
		return htmlspecialchars( wfMsg( 'dpl2_addeditdatewithwrongordermethod' ) );
	
	// adduser=true but not (ordermethod=firstedit or ordermethod=lastedit)
	/* 
	TODO: allow to add user for other order methods. 
	The fact is a page may be edited by multiple users. Which user(s) should we show? all? the first or the last one?
	Ideally, we could use values such as 'all', 'first' or 'last' for the adduser parameter.
	*/
	if( $bAddUser && $sOrderMethod != 'firstedit' && $sOrderMethod != 'lastedit')
		return htmlspecialchars( wfMsg( 'dpl2_adduserwithwrongordermethod' ) );

	// justify limits
	if ($bCountSet) {
		if ($iCount > $wgDPL2MaxResultCount)
			$iCount = $wgDPL2MaxResultCount;
	} else
		if (!$wgDPL2AllowUnlimitedResults) {
			$iCount = $wgDPL2MaxResultCount;
			$bCountSet = true;
		}


// ###### BUILD SQL QUERY ######

	$dbr =& wfGetDB( DB_SLAVE );
	$sPageTable = $dbr->tableName( 'page' );
	$sSqlPage_touched = '';
	$sCategorylinksTable = $dbr->tableName( 'categorylinks' );
	$sSqlCl_to = ''; $sSqlCats = ''; $sSqlCl_timestamp = '';
	$sSqlCl1Table = ''; $sSqlCond_page_cl1 = ''; 
	$sSqlCl2Table = ''; $sSqlCond_page_cl2 = '';	
	$sRevisionTable = $dbr->tableName( 'revision' );
	$sSqlRevision = '';
	$sSqlRev_timestamp = ''; $sSqlRev_user = '';
	$sSqlCond_page_rev = '';
	
	switch ($sOrderMethod) {
		case 'firstedit':
			$sSqlRevision = "$sRevisionTable, ";
			$sSqlRev_timestamp = ', min(rev_timestamp) AS rev_timestamp';
			$sSqlCond_page_rev = ' AND page_id=rev_page';
			break;
		case 'lastedit':
			$sSqlRevision = "$sRevisionTable, ";
			$sSqlRev_timestamp = ', max(rev_timestamp) AS rev_timestamp';
			$sSqlCond_page_rev = ' AND page_id=rev_page';
			break;
		case 'category':
			$sSqlCl_to = 'cl1.cl_to, ';
			$sSqlCl1Table = "$sCategorylinksTable AS cl1";
			$sSqlCond_page_cl1 = 'page_id=cl1.cl_from';
			break;
	}
	
	if ($bAddFirstCategoryDate)
		//format cl_timestamp field (type timestamp) to string in same format as rev_timestamp field
		//to make it compatible with $wgLang->date() function used later to show "firstcategorydate"
		$sSqlCl_timestamp = ", DATE_FORMAT( c1.cl_timestamp, '%Y%m%d%H%i%s' ) AS cl_timestamp";	
	if ($bAddPageTouchedDate)
		$sSqlPage_touched = ', page_touched';	
	if ($bAddUser)
		$sSqlRev_user = ', rev_user, rev_user_text';
	if ($bAddCategories) {
		$sSqlCats = ", GROUP_CONCAT(DISTINCT cl2.cl_to ORDER BY cl2.cl_to ASC SEPARATOR ' | ') AS cats";
		$sSqlCl2Table = "$sCategorylinksTable AS cl2";
		$sSqlCond_page_cl2 = 'page_id=cl2.cl_from';
	}
	
	// SELECT ... FROM
	if ($iTotalIncludeCatCount == 1) 
		$sSqlSelectFrom = "SELECT DISTINCT " . $sSqlCl_to . "page_namespace, page_title" . $sSqlPage_touched . $sSqlRev_timestamp . $sSqlRev_user . $sSqlCats . $sSqlCl_timestamp . " FROM " . $sSqlRevision . $sPageTable;
	else
		$sSqlSelectFrom = "SELECT DISTINCT " . $sSqlCl_to . "page_namespace, page_title" . $sSqlPage_touched . $sSqlRev_timestamp . $sSqlRev_user . $sSqlCats . " FROM " . $sSqlRevision . $sPageTable;
	
	// JOIN ...	
	if($bAddCategories || $sOrderMethod=='category') {
		$b2tables = !empty($sSqlCl1Table) && !empty($sSqlCl2Table);
		$sSqlSelectFrom .= ' LEFT JOIN (' .$sSqlCl1Table . ($b2tables ? ', ' : '') . $sSqlCl2Table.') ON ('. $sSqlCond_page_cl1 . ($b2tables ? ' AND ' : '') . $sSqlCond_page_cl2 .')';	
	}
	$iCurrentTableNumber = 0;
	for ($i = 0; $i < $iIncludeCatCount; $i++) {
		$sSqlSelectFrom .= " INNER JOIN $sCategorylinksTable AS c" . ($iCurrentTableNumber+1);
		$sSqlSelectFrom .= ' ON page_id = c' . ($iCurrentTableNumber+1) . '.cl_from';
		$sSqlSelectFrom .= ' AND (c' . ($iCurrentTableNumber+1) . '.cl_to=' . $dbr->addQuotes( $aaIncludeCategories[$i][0]->getDbKey() );
		for ($j = 1; $j < count($aaIncludeCategories[$i]); $j++)
			$sSqlSelectFrom .= ' OR c' . ($iCurrentTableNumber+1) . '.cl_to=' . $dbr->addQuotes( $aaIncludeCategories[$i][$j]->getDbKey() );
		$sSqlSelectFrom .= ') ';
		$iCurrentTableNumber++;
	}
	$sSqlWhere = ' WHERE 1=1 ';
	for ($i = 0; $i < $iExcludeCatCount; $i++) {
		$sSqlSelectFrom .= " LEFT OUTER JOIN $sCategorylinksTable AS c" . ($iCurrentTableNumber+1);
		$sSqlSelectFrom .= ' ON page_id = c' . ($iCurrentTableNumber+1) . '.cl_from';
		$sSqlSelectFrom .= ' AND c' . ($iCurrentTableNumber+1) . '.cl_to='.
		$dbr->addQuotes( $aExcludeCategories[$i]->getDbKey() );
		$sSqlWhere .= ' AND c' . ($iCurrentTableNumber+1) . '.cl_to IS NULL';
		$iCurrentTableNumber++;
	}

	// WHERE ...
	// Namespace IS ...
	if ( !empty($aNamespaces)) {
		$sSqlWhere .= ' AND (page_namespace='.$aNamespaces[0];
		for ($i = 1; $i < count($aNamespaces); $i++)
			$sSqlWhere .= ' OR page_namespace='.$aNamespaces[$i];
		$sSqlWhere .= ') ';
	}
	// is_Redirect IS ...	
	switch ($sRedirects) {
		case 'only':
			$sSqlWhere .= ' AND page_is_redirect = 1';
			break;
		case 'exclude':
			$sSqlWhere .= ' AND page_is_redirect = 0';
			break;
	}
	
	$sSqlWhere .= $sSqlCond_page_rev;
	
	// GROUP BY ...
	$sSqlWhere .= ' GROUP BY '. $sSqlCl_to .'page_id';// for min or max(rev_timestamp) (page_id=rev_page)
	
	// ORDER BY ...
	switch ($sOrderMethod) {
		case 'pagetouched':
			$sSqlWhere .= ' ORDER BY page_touched';
			break;
		case 'firstedit':
		case 'lastedit':
			$sSqlWhere .= ' ORDER BY rev_timestamp';
			break;
		case 'categoryadd':
			$sSqlWhere .= ' ORDER BY c1.cl_timestamp';
			break;
		case 'title':
			$sSqlWhere .= ' ORDER BY page_title';
			break;
		case 'category':
			$sSqlWhere .= ' ORDER BY cl1.cl_to, page_title';
			break;
	}
	if ($sOrder == 'descending')
		$sSqlWhere .= ' DESC';
	else
		$sSqlWhere .= ' ASC';

	// LIMIT ....
	if ($bCountSet)
		$sSqlWhere .= ' LIMIT ' . $iCount;



// ###### PROCESS SQL QUERY ######
	$output = '';
	//DEBUG: output SQL query 
	//$output = 'QUERY: [' . $sSqlSelectFrom . $sSqlWhere . "]<br />";    
	//echo 'QUERY: [' . $sSqlSelectFrom . $sSqlWhere . "]<br />";    	

	$res = $dbr->query($sSqlSelectFrom . $sSqlWhere);
	$sk =& $wgUser->getSkin();
	if ($dbr->numRows( $res ) == 0) {
		if (!$bSuppressErrors)
			return htmlspecialchars( wfMsg( 'dpl2_noresults' ) );
		else
			return '';
	}
	
	// generate link to Special:Contributions (used if adduser=true)
	$contribsPage =& Title::makeTitle( NS_SPECIAL, 'Contributions' );
	// generate link to Special:Categories (used if addcategories=true)
	$catsPage = & Title::makeTitle( NS_SPECIAL, 'Categories' );
	$catsLink = $sk->makeLinkObj( $catsPage, $catsPage->getText() );
	// generate link to Special:Uncategorizedpages (used if ordermethod=category)
	$uncatPage = & Title::makeTitle( NS_SPECIAL, 'Uncategorizedpages' );
	$uncatLink = $sk->makeLinkObj( $uncatPage, wfMsg('uncategorizedpages') );
	$aCategories = array(); //maps index of category start (in page list) to category link/heading (string)
	$row_idx = 0;
	
	while( $row = $dbr->fetchObject ( $res ) ) {	
		//CATEGORY LINKS IN HEADINGS IF ORDER BY CATEGORY
		if ( ($sOrderMethod=='category') && ( !isset($curCat) || (!empty($row) && ($row->cl_to!=$curCat)) ) ) { //first line (no current category) or new category (or uncategorized) begins -> get category link
			if(empty($row->cl_to)) { //uncategorized pages 
				$curCat= '';
				$catLink = $uncatLink;
			} else {
				$curCat = $row->cl_to;
				$catPage = & Title::makeTitle(NS_CATEGORY, $curCat);
				$catLink = $sk->makeLinkObj( $catPage, $catPage->gettext() );
			}
			$aCategories[$row_idx] = "<h2>$catLink</h2>"; // add row index as key where category starts
		}
			
		//NAMESPACE IN ARTICLE LINK
		$title = Title::makeTitle( $row->page_namespace, $row->page_title);
		if ($bShowNamespace)
			$sLink = $sk->makeKnownLinkObj($title);
		else
			$sLink = $sk->makeKnownLinkObj($title, $wgContLang->convertHtml($title->getText()));
		
		//ARTICLE LINK
		$aArticles[] = $sLink;
		//get first char used for category-style output
		$aArticles_start_char[] = $wgContLang->convert( $wgContLang->firstChar($row->page_title) );
			
		//SHOW "PAGE_TOUCHED" DATE, "FIRSTCATEGORYDATE" OR (FIRST/LAST) EDIT DATE
		if($bAddPageTouchedDate)
			$aAddDates[] = $wgLang->date($row->page_touched) . ': ';
		elseif ($bAddFirstCategoryDate)
			$aAddDates[] = $wgLang->date($row->cl_timestamp) . ': ';
		elseif ($bAddEditDate)	
			$aAddDates[] = $wgLang->date($row->rev_timestamp) . ': ';
		else
			$aAddDates[] = '';	
		
		//USER/AUTHOR
		if($bAddUser) {
			if ( 0 == $row->rev_user ) {
				$aAddUsers[] = ' . . ' . $sk->makeLinkObj( $contribsPage, $row->rev_user_text, 'target=' . $row->rev_user_text );
			} else {
				$userPage =& Title::makeTitle( NS_USER, $row->rev_user_text );
				$aAddUsers[] = ' . . ' . $sk->makeLinkObj( $userPage, htmlspecialchars( $row->rev_user_text ) );
			}
		} else
			$aAddUsers[] = '';
		
		//CATEGORY LINKS PER ARTICLE 
		if(!$bAddCategories || empty($row->cats)) 
			$aAddCategories[] = '';
		else {
			$artCatNames = explode(' | ', $row->cats);
			$artCatLinks = array();
			foreach($artCatNames as $catName) {
				$catPage = & Title::makeTitle(NS_CATEGORY, $catName);
				$artCatLinks[] = $sk->makeLinkObj( $catPage, $catPage->getText() );
			}
			$aAddCategories[] = $catsLink.': '.implode(' | ', $artCatLinks);
		}
		$row_idx++;			
	}
	$dbr->freeResult( $res );
	

// ###### SHOW OUTPUT ######
//(String concatenation for output in case the lines for debugging SQL query are used.
//See above for these lines.)
	if ($sOutputMode == 'category')
		$output .= DPL2OutputCategoryStyle( $aCategories, $aArticles, $aArticles_start_char );
	else
		$output .= DPL2OutputListStyle( $aCategories, $aArticles, $aAddDates, $aAddUsers, $aAddCategories, $sOutputMode, $sInlSymbol ); 
		
	return $output;
}

function DPL2OutputListStyle ($aCategories, $aArticles, $aAddDates, $aAddUsers, $aAddCategories, $sOutputMode, $sInlSymbol ) {
	
	switch ($sOutputMode) {
		case 'none':
			$sStartList = '';
			$sEndList = '';
			$sStartItem = '';
			$sEndItem = '<br />';
			$bAddLastEndItem = false;
			break;
		case 'inline':
			$sStartList = '';
			$sEndList = '';
			$sStartItem = '';
			$sEndItem = ' ' . $sInlSymbol . ' ';
			$bAddLastEndItem=false;
			break;
		case 'ordered':
			$sStartList = '<ol>';
			$sEndList = '</ol>';
			$sStartItem = '<li>';
			$sEndItem = '</li>';
			$bAddLastEndItem=true;
			break;
		case 'unordered':
		default:
			$sStartList = '<ul>';
			$sEndList = '</ul>';
			$sStartItem = '<li>';
			$sEndItem = '</li>';
			$bAddLastEndItem=true;
			break;
	}		
	
	//process results of query, outputing equivalent of <li>[[Article]]</li> for each result,
	//or something similar if the list uses other startlist/endlist;
	$r = $sStartList . "\n";
	for ($i=0; $i<count($aArticles); $i++) {
		if(isset($aCategories[$i])) //if this is index of category start 
			$r .= $sEndList."\n".$aCategories[$i]."\n".$sStartList . "\n";
		$r .= $sStartItem . $aAddDates[$i] . $aArticles[$i] . $aAddUsers[$i];
		if(!empty($aAddCategories[$i])) {
			if($sOutputMode=='inline' || $sOutputMode=='none')
				$r .= '<font size=-2> ('.$aAddCategories[$i].')</font>';
			else $r .= '<font size=-2><br />'.$aAddCategories[$i].'</font>';
		}
		if ($i<count($aArticles)-1 || $bAddLastEndItem==true)
			$r .= $sEndItem;
		$r .= "\n";
	}
	$r .= $sEndList . "\n";

	return $r;
}

function DPL2OutputCategoryStyle($aCategories, $aArticles, $aArticles_start_char) { 

	global $wgDPL2MaxCategoryShortListResultCount;
	require_once ('CategoryPage.php');
	
	if(empty($aCategories)) {
		$ret = count ($aArticles).' article(s) listed.';
		//You can decide to uncomment the line below and use it instead of the previous one, more generic.
		//However, this message makes sense only if category parameter is used, and with one category.
		// $ret = CategoryPage::formatCount( $aArticles, 'categoryarticlecount' );
		if ( count ($aArticles) > $wgDPL2MaxCategoryShortListResultCount )
			$ret .= CategoryPage::columnList( $aArticles, $aArticles_start_char );
		elseif ( count($aArticles) > 0)
			$ret .= CategoryPage::shortList( $aArticles, $aArticles_start_char );
	} else {
		$aCatStarts = array_keys($aCategories);
		$ret = '';		
		foreach($aCatStarts as $i => $catStart) {
			$ret .= $aCategories[$catStart]."\n";
			if($i==count($aCatStarts)-1) {
				$aCatArticles = array_slice($aArticles, $catStart);
				$aCatArticles_start_char = array_slice($aArticles_start_char, $catStart);
			} else {
				$aCatArticles = array_slice($aArticles, $catStart, $aCatStarts[$i+1] - $catStart);
				$aCatArticles_start_char = array_slice($aArticles_start_char, $catStart, $aCatStarts[$i+1] - $catStart);
			}
			$ret .= CategoryPage::formatCount( $aCatArticles, 'categoryarticlecount' );
			if ( count ($aCatArticles) > $wgDPL2MaxCategoryShortListResultCount )
				$ret .= CategoryPage::columnList( $aCatArticles, $aCatArticles_start_char );
			elseif ( count($aCatArticles) > 0)
				$ret .= CategoryPage::shortList( $aCatArticles, $aCatArticles_start_char );
		}
	}

	return $ret;
}
	
?>
