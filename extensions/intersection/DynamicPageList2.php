<?php
/*

 Version:
	Hack v0.5 (DynamicPageList2 is based on DynamicPageList)
	
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
	Add following to LocalSettings.php:
		include("extensions/intersection/DynamicPageList2.php");
*/


$wgExtensionFunctions[] = "wfDynamicPageList2";
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'DynamicPageList2',
	'author'=>'[http://en.wikinews.org/wiki/User:IlyaHaykinson IlyaHaykinson], [http://en.wikinews.org/wiki/User:Amgine Amgine], [http://de.wikipedia.org/wiki/Benutzer:Unendlich Unendlich], [http://meta.wikimedia.org/wiki/User:Dangerman Cyril Dangerville]',
	'url'=>'http://meta.wikimedia.org/wiki/DynamicPageList2',
	'description'=>'hack of the original [http://meta.wikimedia.org/wiki/DynamicPageList DynamicPageList] extension from DynamicPageList featuring many Improvements',
  	'version'=>'0.5.1'
  );

$wgDPL2MaxCategoryCount = 4;				// Maximum number of categories allowed in the Query
$wgDPL2MinCategoryCount = 0;				// Minimum number of categories needed in the Query
$wgDPL2MaxResultCount = 50;				// Maximum number of results to allow
$wgDPL2CategoryStyleListCutoff = 6; //Max length to format a list of articles chunked by letter as bullet list, if list bigger, columnar format user (same as cutoff arg for CategoryPage::formatList())
$wgDPL2AllowUnlimitedCategories = true;			// Allow unlimited categories in the Query
$wgDPL2AllowUnlimitedResults = true;				// Allow unlimited results to be shown


function wfDynamicPageList2() {
	global $wgParser, $wgMessageCache;

	$wgMessageCache->addMessages( array(
					'dpl2_toomanycats' 					=> 'DynamicPageList2: Too many categories!',
					'dpl2_toofewcats' 					=> 'DynamicPageList2: Too few categories!',
					'dpl2_noresults' 					=> 'DynamicPageList2: No results!',
					'dpl2_noincludedcatsorns' 			=> 'DynamicPageList2: You need to include/exclude at least one category or include/exclude at least one namespace!',
					'dpl2_noincludedcatsbutcatdate' 		=> "DynamicPageList2: You need to include at least one category if you want to use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
					'dpl2_morethanonecatbutcatdate'		=> "DynamicPageList2: If you include more than one category you cannot use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
					'dpl2_addmorethanonetypeofdate'		=> 'DynamicPageList2: You cannot add more than one type of date at a time.',		
					'dpl2_catoutputwithwrongordermethod'	=> "DynamicPageList2: You have to use 'ordermethod=title' or 'ordermethod=category' when using category-style output!",
					'dpl2_addpagetoucheddatewithwrongordermethod' => "DynamicPageList2: You cannot use 'addpagetoucheddate=true' with 'ordermethod=firstedit' or 'ordermethod=lastedit' or 'ordermethod=categoryadd'.",
					'dpl2_addeditdatewithwrongordermethod'	=> "DynamicPageList2: You have to use 'ordermethod=firstedit' or 'ordermethod=lastedit' when using 'addeditdate=true'.",
					'dpl2_adduserwithwrongordermethod'	=> "DynamicPageList2: You have to use 'ordermethod=firstedit' or 'ordermethod=lastedit' when using 'adduser=true'."
					)
				);
	/*
	TODO: allow 'adduser=true' for other order methods. 
	In fact, a page may be edited by multiple users. Which user(s) should we show? all? the first or the last one?
	Suggested solution: use values such as 'all', 'first' or 'last' for the adduser parameter.
	*/
	$wgParser->setHook( "DPL", "DynamicPageList2" );
}


// The callback function for converting the input text to HTML output
function DynamicPageList2( $input, $params, &$parser ) {

	error_reporting(E_ALL);
	
	global $wgUser, $wgContLang, $wgDPL2MaxCategoryCount, $wgDPL2MinCategoryCount, $wgDPL2MaxResultCount, $wgDPL2AllowUnlimitedCategories, $wgDPL2AllowUnlimitedResults;
	
	// INVALIDATE CACHE
	$parser->disableCache();

	$aParams = array();
	
	// Default Values
	$sOrderMethod = 'title';
	$sOrder = 'descending';	
	$sPageOutputMode = 'unordered';
	$sCatOutputMode = 'unordered';	
	$sRedirects = 'exclude';
	$sInlSymbol = '-';
	$bShowNamespace = true;
	$bSuppressErrors = false;
	$bAddFirstCategoryDate = false;
	$bAddPageTouchedDate = false;
	$bAddEditDate = false;
	$bAddUser = false;
	$bAddCategories = false;
	$bCountSet = false;
	$aaIncludeCategories = array();		// $aaIncludeCategories is a two 2-dimensional array: Memberarrays are linked using 'AND'
	$aExcludeCategories = array();
	$aNamespaces = array();
	$aExcludeNamespaces  = array();
	
	//Local parser created. See http://meta.wikimedia.org/wiki/MediaWiki_extensions_FAQ#How_do_I_render_wikitext_in_my_extension.3F
	$localParser = new Parser();
	$poptions = $parser->mOptions;	

// ###### PARSE PARAMETERS ######
	$aParams = explode("\n", $input);
	
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
					$title = Title::newFromText($localParser->transformMsg($sParam, $poptions));
					if( $title != NULL )
						$aCategories[] = $title;
				}
				if (!empty($aCategories))
					$aaIncludeCategories[] = $aCategories;	
				break;
				
			case 'notcategory':
				$title = Title::newFromText($localParser->transformMsg($sArg, $poptions));
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
					elseif (intval($sParam) >= 0)
						$aNamespaces[] = intval($sParam);
				}
				break;
			
			case 'notnamespace':
                $sArg=trim($sArg);
                $sNS = $wgContLang->getNsIndex($sArg);
                if ( $sNS != NULL )
                    $aExcludeNamespaces[] = $sNS;
                elseif (intval($sArg) >= 0)
                    $aExcludeNamespaces[] = intval($sArg);
                break;
				
			case 'count':
				//ensure that $iCount is a number;
				$iCount = IntVal( $sArg );
				$bCountSet = true;
				break;
				
			//mode for list of pages (possibly within a category if 'ordermethod=category', see 'categorymode' parameter)
			//TODO: solve the issue: addpagetoucheddate, addeditdate, adduser, addcategories have no effect with 'mode=category'
			case 'mode':
				if ( in_array($sArg, array('none','ordered','unordered','category','inline')) )
					$sPageOutputMode = $sArg;
				break;
				
			//mode at the category level, used with ordermethod=category only: 
			//ordered, unordered, definition list, or section, subsection, sub-subsection, etc.
			case 'categorymode':
				if ( in_array($sArg, array('ordered','unordered','definition')) || preg_match('/^((sub-)*sub)?section$/', $sArg))
					$sCatOutputMode = $sArg;
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
	if ($iTotalCatCount == 0 && empty($aNamespaces) && empty($aExcludeNamespaces))
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
	if ($sPageOutputMode == 'category' && $sOrderMethod != 'title' && $sOrderMethod != 'category')
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
	$sSqlCl_to = ''; 
	$sSqlCats = ''; 
	$sSqlCl_timestamp = '';
	$sSqlCl1Table = ''; 
	$sSqlCond_page_cl1 = ''; 
	$sSqlCl2Table = ''; 
	$sSqlCond_page_cl2 = '';	
	$sRevisionTable = $dbr->tableName( 'revision' );
	$sSqlRevision = '';
	$sSqlRev_timestamp = ''; 
	$sSqlRev_user = '';
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
		//to make it compatible with $wgLang->date() function used in function DPL2OutputListStyle() to show "firstcategorydate"
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
	$sSqlSelectFrom = "SELECT DISTINCT " . $sSqlCl_to . "page_namespace, page_title" . $sSqlPage_touched . $sSqlRev_timestamp . $sSqlRev_user . $sSqlCats . $sSqlCl_timestamp . " FROM " . $sSqlRevision . $sPageTable;
	
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
		$sSqlWhere .= ' AND (page_namespace IN (' . implode (',', $aNamespaces) . '))';
	}
	// Namespace IS NOT ...
    if ( !empty($aExcludeNamespaces)) {
        $sSqlWhere .= ' AND (page_namespace NOT IN (' . implode (',', $aExcludeNamespaces) . '))';
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
	//$output .= 'QUERY: [' . $sSqlSelectFrom . $sSqlWhere . "]<br/>";
	//echo 'QUERY: [' . $sSqlSelectFrom . $sSqlWhere . "]<br />";

	$res = $dbr->query($sSqlSelectFrom . $sSqlWhere);
	if ($dbr->numRows( $res ) == 0) {
		if (!$bSuppressErrors)
			return htmlspecialchars( wfMsg( 'dpl2_noresults' ) );
		else
			return '';
	}
	
	$sk =& $wgUser->getSkin();
	// generate link to Special:Uncategorizedpages (used if ordermethod=category)
	$tSpecUncat = & Title::makeTitle( NS_SPECIAL, 'Uncategorizedpages' );
	$sSpecUncatLnk = $sk->makeKnownLinkObj( $tSpecUncat, wfMsg('uncategorizedpages') );
	// generate title for Special:Contributions (used if adduser=true)
	$tSpecContribs =& Title::makeTitle( NS_SPECIAL, 'Contributions' );
	
	$aCategories = array();
	$aArticles = array();
	$aArticles_start_char =array();
	$aAddDates = array();
	$aAddUsers = array();
	$aAddCategories = array();
	while( $row = $dbr->fetchObject ( $res ) ) {	
		//CATEGORY LINKED TO (per page) IF ORDER BY CATEGORY
		if($sOrderMethod == 'category') { 
			if(empty($row->cl_to)) { //uncategorized page
				$aCategories[] = $sSpecUncatLnk; 
			} else {
				$tCat = & Title::makeTitle(NS_CATEGORY, $row->cl_to);
				$aCategories[] = $sk->makeKnownLinkObj($tCat, $wgContLang->convertHtml($tCat->getText()));
			}
		} else
			$aCategories[] = '';
			
		//PAGE LINK
		$title = & Title::makeTitle($row->page_namespace, $row->page_title);
		if ($bShowNamespace)
			$aArticles[] = $sk->makeKnownLinkObj($title);
		else
			$aArticles[] = $sk->makeKnownLinkObj($title, $wgContLang->convertHtml($title->getText()));
		//get first char used for category-style output
		$aArticles_start_char[] = $wgContLang->convert($wgContLang->firstChar($row->page_title));
			
		//SHOW "PAGE_TOUCHED" DATE, "FIRSTCATEGORYDATE" OR (FIRST/LAST) EDIT DATE
		if($bAddPageTouchedDate)
			$aAddDates[] = $row->page_touched;
		elseif ($bAddFirstCategoryDate)
			$aAddDates[] = $row->cl_timestamp;
		elseif ($bAddEditDate)	
			$aAddDates[] = $row->rev_timestamp;
		else
			$aAddDates[] = '';	
		
		//USER/AUTHOR
		if($bAddUser) {
			if($row->rev_user == 0) { //anonymous user
				$aAddUsers[] = $sk->makeKnownLinkObj($tSpecContribs, $row->rev_user_text, 'target=' . $row->rev_user_text);
			} else {
				$tUser =& Title::makeTitle( NS_USER, $row->rev_user_text );
				/*
				The user's page may not exist (->"bad" red link), makeLinkObj() executes a DB query to check that out
				TODO: optimize with a LinkBatch on the user titles to get good (user page exists) and bad links,
				then use makeKnownLinkObj() for good links (user page exists), makeBrokenLinkObj() on bad ones
				*/
				$aAddUsers[] = $sk->makeLinkObj($tUser, $wgContLang->convertHtml($tUser->getText()));
			}
		} else
			$aAddUsers[] = '';
		
		//CATEGORY LINKS PER PAGE 
		if(!$bAddCategories || empty($row->cats)) 
			$aAddCategories[] = '';
		else {
			$artCatNames = explode(' | ', $row->cats);
			$artCatLinks = array();
			foreach($artCatNames as $artCatName) {
				$tArtCat = & Title::makeTitle(NS_CATEGORY, $artCatName);
				/*
				TODO: see TODO comment for user pages above
				*/
				$artCatLinks[] = $sk->makeLinkObj($tArtCat, $wgContLang->convertHtml($tArtCat->getText()));
			}
			$aAddCategories[] = $artCatLinks;
		}
	}
	$dbr->freeResult( $res );
	

// ###### SHOW OUTPUT ######	
	if($sOrderMethod == 'category') {
		$catMode = new DPL2OutputMode($sCatOutputMode);
		$aCatCounts = array_count_values($aCategories); //count articles per category
		$output .= $catMode->sStartList; 
		$catStart = 0;
		foreach($aCatCounts as $cat => $catCount) {
			$output .= $catMode->sStartItem;
			$output .= $catMode->sStartHeading . $cat . $catMode->sEndHeading;
			$output .= '<p>' . DPL2FormatCount($catCount, 'categoryarticlecount') . '</p>';
			if ($sPageOutputMode == 'category')
				$output .= DPL2OutputCategoryStyle($aArticles, $aArticles_start_char, $catStart, $catCount);
			else
				$output .= DPL2OutputListStyle($aArticles, $aAddDates, $aAddUsers, $aAddCategories, $sPageOutputMode, $sInlSymbol, $catStart, $catCount);
			$output .= $catMode->sEndItem;
			$catStart += $catCount;
		}
		$output .= $catMode->sEndList;
	} elseif($sPageOutputMode == 'category')
		$output .= DPL2OutputCategoryStyle($aArticles, $aArticles_start_char, 0, count($aArticles));
	else
		$output .= DPL2OutputListStyle($aArticles, $aAddDates, $aAddUsers, $aAddCategories, $sPageOutputMode, $sInlSymbol, 0, count($aArticles));

	return $output;
}


function DPL2OutputListStyle ($aArticles, $aAddDates, $aAddUsers, $aAddCategories, $sOutputMode, $sInlSymbol, $iStart, $iCount) {	
	global $wgUser,  $wgLang;
	
	$sk = & $wgUser->getSkin();
	// generate link to Special:Categories (used if addcategories=true)
	$tSpecCats = & Title::makeTitle( NS_SPECIAL, 'Categories' );
	$sSpecCatsLnk = $sk->makeKnownLinkObj( $tSpecCats, wfMsg('categories'));
	
	$mode = new DPL2OutputMode($sOutputMode, $sInlSymbol);
	//process results of query, outputing equivalent of <li>[[Article]]</li> for each result,
	//or something similar if the list uses other startlist/endlist;
	$r = $mode->sStartList;
	for ($i = $iStart; $i < $iStart+$iCount; $i++) {
		$r .= $mode->sStartItem;
		if(!empty($aAddDates[$i]))
			$r .=  $wgLang->date($aAddDates[$i]) . ': ';
		$r .= $aArticles[$i];
		if(!empty($aAddUsers[$i]))
			$r .= ' . . ' . $aAddUsers[$i];
		if(!empty($aAddCategories[$i]))
			$r .= ' . . <small>' . $sSpecCatsLnk . ': ' . implode(' | ', $aAddCategories[$i]) . '</small>';
		if( (($mode->name != 'inline') && ($mode->name != 'none')) || ($i < $iCount-1)) //no inline symbol (inline mode) at end of list
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
	
	
//slightly different from CategoryPage::formatCount() (first argument is the number not an array)
function DPL2FormatCount( $numart, $message ) {
	global $wgContLang;
	if( $numart == 1 ) {
		# Slightly different message to avoid silly plural
		$message .= '1';
	}
	return wfMsg( $message, $wgContLang->formatNum( $numart ) );
}
	
	
class DPL2OutputMode {
	var $name;
	var $sStartList = '';
	var $sEndList = '';
	var $sStartHeading = '';
	var $sEndHeading = '';
	var $sStartItem = '';
	var $sEndItem = '';
	
	function DPL2OutputMode($outputmode, $inlinesymbol = '-') {
		$this->name = $outputmode;
		switch ($outputmode) {
			case 'none':
				$this->sEndItem = '<br/>';
				break;
			case 'inline':
				$this->sEndItem = ' ' . $inlinesymbol . ' ';
				break;
			case 'ordered':
				$this->sStartList = '<ol>';
				$this->sEndList = '</ol>';
				$this->sStartItem = '<li>';
				$this->sEndItem = '</li>';
				break;
			case 'unordered':
				$this->sStartList = '<ul>';
				$this->sEndList = '</ul>';
				$this->sStartItem = '<li>';
				$this->sEndItem = '</li>';
				break;
			case 'definition':
				$this->sStartList = '<dl>';
				$this->sEndList = '</dl>';
				$this->sStartHeading = '<dt>';
				$this->sEndHeading = '</dt><dd>';
				$this->sEndItem = '</dd>';
				break;
			default:
				if(preg_match('/^((sub-)*sub)?section$/', $outputmode)) {
					$level = 2 + preg_match_all('/sub/', $outputmode, $matches);
					$this->sStartHeading = '<h' . $level . '>';
					$this->sEndHeading = '</h' . $level . '>';
				}
		}
	}
}
?>
