<?php
/*

 Purpose:       outputs a bulleted list of most recent
                items residing in a category, or a union
                of several categories.


 Contributors: n:en:User:IlyaHaykinson n:en:User:Amgine
 http://en.wikinews.org/wiki/User:Amgine
 http://en.wikinews.org/wiki/User:IlyaHaykinson

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

 Current feature request list
     1. Unset cached of calling page
     2. Alternative formatting (not just unordered list)
     3. Configurable sort order, ascending/descending
     4. RSS feed output?

 To install, add following to LocalSettings.php
   include("extensions/intersection/DynamicPageList.php");

*/

$wgDLPminCategories = 1;                // Minimum number of categories to look for
$wgDLPmaxCategories = 3;                // Maximum number of categories to look for
$wgDLPMinResultCount = 1;               // Minimum number of results to allow
$wgDLPMaxResultCount = 50;              // Maximum number of results to allow
$wgDLPAllowUnlimitedResults = true;     // Allow unlimited results
$wgDLPAllowUnlimitedCategories = false; // Allow unlimited categories


$wgExtensionFunctions[] = "wfDynamicPageList";

 
function wfDynamicPageList() {
    global $wgParser, $wgMessageCache;
   
    $wgMessageCache->addMessages( array(
					'dynamicpagelist_toomanycats' => 'DynamicPageList: Too many categories!',
					'dynamicpagelist_toofewcats' => 'DynamicPageList: Too few categories!',
					'dynamicpagelist_noresults' => 'DynamicPageList: No results!',
					)
				  );

    $wgParser->setHook( "DynamicPageList", "DynamicPageList" );
}
 
// The callback function for converting the input text to HTML output
function DynamicPageList( $input ) {
    global $wgUser;
    global $wgDLPminCategories, $wgDLPmaxCategories,$wgDLPMinResultCount, $wgDLPMaxResultCount;
    global $wgDLPAllowUnlimitedResults, $wgDLPAllowUnlimitedCategories;
     
    $aParams = array();
    $bCountSet = false;

    $aParams = explode("\n", $input);

    foreach($aParams as $sParam)
    {
      $aParam = explode("=", $sParam);
      if( count( $aParam ) < 2 )
         continue;
      $sType = trim($aParam[0]);
      $sArg = trim($aParam[1]);
      if ($sType == 'category')
      {
        $title = Title::newFromText( $sArg );
        if( is_null( $title ) )
          continue;
        $aCategories[] = $title; 
      }
      else if ('count' == $sType)
      {
        //ensure that $iCount is a number;
        $iCount = IntVal( $sArg );
        $bCountSet = true;
      }
    }

    $iCatCount = count($aCategories);

    if ($iCatCount < $wgDLPminCategories)
      return htmlspecialchars( wfMsg( 'dynamicpagelist_toofewcats' ) ); // "!!too few categories!!";

    if ( $iCatCount > $wgDLPmaxCategories && !$wgDLPAllowUnlimitedCategories )
      return htmlspecialchars( wfMsg( 'dynamicpagelist_toomanycats' ) ); // "!!too many categories!!";

    if ($bCountSet)
    {
      if ($iCount < $wgDLPMinResultCount)
        $iCount = $wgDLPMinResultCount;
      if ($iCount > $wgDLPMaxResultCount)
        $iCount = $wgDLPMaxResultCount;
    }
    else
    {
      if (!$wgDLPAllowUnlimitedResults)
      {
        $iCount = $wgDLPMaxResultCount;
        $bCountSet = true;
      }
    }


    //build the SQL query
    $dbr =& wfGetDB( DB_SLAVE );
    $cur = $dbr->tableName( 'cur' );
    $categorylinks = $dbr->tableName( 'categorylinks' );
    $sSqlSelectFrom = "SELECT cur_namespace, cur_title FROM $cur";
    $sSqlWhere = ' WHERE cur_id = c1.cl_from ';

    for ($i = 0; $i < $iCatCount; $i++) {
      $sSqlSelectFrom .= ", $categorylinks AS c" . ($i+1);

      if ($i > 0)
        $sSqlWhere .= ' AND c1.cl_from = c'.($i+1).'.cl_from';
      $sSqlWhere .= ' AND c'.($i+1).'.cl_to = ' .
        $dbr->addQuotes( $aCategories[$i]->getDbKey() );
    }

    $sSqlWhere .= ' ORDER BY cur_timestamp DESC';
    
    if ($bCountSet)
    {
      $sSqlWhere .= ' LIMIT ' . $iCount;
    }

    //DEBUG: output SQL query 
    //$output = $sSqlSelectFrom . $sSqlWhere . "<br />";    

    // process the query
    $res = $dbr->query($sSqlSelectFrom . $sSqlWhere);
	
    $sk =& $wgUser->getSkin();

    if ($dbr->numRows( $res ) == 0) {
      return htmlspecialchars( wfMsg( 'dynamicpagelist_noresults' ) );
    }
    
    //start unordered list
    $output = "<ul>\n";
	
    //process results of query, outputing equivalent of <li>[[Article]]</li> for each result
    while ($row = $dbr->fetchObject( $res ) ) {
      $title = Title::makeTitle( $row->cur_namespace, $row->cur_title);
      $output .= '<li>' . $sk->makeKnownLinkObj($title) . '</li>' . "\n";
    }

    //end unordered list
    $output .= "</ul>\n";

    return $output;
}
?>
