<?php

# Setup and Hooks for the SelectCategory extension, an extension of the
# edit box of MediaWiki to provide an easy way to add category links
# to a specific page.

# @package MediaWiki
# @subpackage Extensions
# @author Leon Weber <leon.weber@leonweber.de> & Manuel Schneider <manuel.schneider@wikimedia.ch>
# @copyright © 2006 by Leon Weber & Manuel Schneider
# @licence GNU General Public Licence 2.0 or later

if( !defined( 'MEDIAWIKI' ) ) {
	echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
	die();
}

## Entry point for the hook and main worker function:
function fnSelectCategoryEditHook( &$m_pageObj ) {
	global $wgSelectCategoryNamespaces;
	global $wgTitle;

	# Run only if we are in an enabled namespace:
	if ( $wgSelectCategoryNamespaces[ $wgTitle->getNamespace() ] ) {
		# Extract all categorylinks from page:
		$m_pageCats = fnSelectCategoryGetPageCategories( $m_pageObj );
		# Get all categories from wiki:
		$m_allCats = fnSelectCategoryGetAllCategories();
		# Print the select box:
		$m_pageObj->editFormTextAfterWarn .= "<!-- SelectCategory begin -->\n";
		$m_pageObj->editFormTextAfterWarn .= "<select size=\"5\" name=\"SelectCategoryList[]\" multiple=\"multiple\">\n";
		# Populate box with categories:
		foreach( $m_allCats as $m_cat => $m_prefix ) {
			# Check if the category is in the list of category links on the page then select the entry:
			if ( $m_pageCats[ $m_cat ] ) $m_selected = 'selected="selected"';
			else $m_selected = '';
			# Print the entry:
			$m_pageObj->editFormTextAfterWarn .= "\t<option $m_selected value=\"". htmlspecialchars( $m_cat ) . "\">";
			for ( $m_i = 0; $m_i < $m_prefix; $m_i++ ) $m_pageObj->editFormTextAfterWarn .= '&nbsp;';
			$m_pageObj->editFormTextAfterWarn .= htmlspecialchars( $m_cat );
			$m_pageObj->editFormTextAfterWarn .= "</option>\n";
		}
		# Close select box:
		$m_pageObj->editFormTextAfterWarn .= "</select>\n";
		$m_pageObj->editFormTextAfterWarn .= "<!-- SelectCategory end -->\n";
	}	
	# Return true to let the rest work:
	return true;
}

## Get all categories from the wiki - starting with a given root or otherwise detect root automagically (expensive):
function fnSelectCategoryGetAllCategories() {
	global $wgSelectCategoryRoot;

	if( $wgSelectCategoryRoot ) {
		# Include root and step into the recursion:
		$m_allCats = array_merge( array( $wgSelectCategoryRoot => 0 ), fnSelectCategoryGetChildren( $wgSelectCategoryRoot ) );
	} else {
		# Get a database object:
		$m_dbObj =& wfGetDB( DB_SLAVE );
		# Get table names to access them in SQL query:
		$m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
		$m_tblPage = $m_dbObj->tableName( 'page' );
	
		# Automagically detect root categories:
		$m_sql = "	SELECT tmpSelectCat1.cl_to AS title
				FROM $m_tblCatLink AS tmpSelectCat1 
				LEFT JOIN $m_tblPage AS tmpSelectCatPage ON tmpSelectCat1.cl_to = tmpSelectCatPage.page_title 
				LEFT JOIN $m_tblCatLink AS tmpSelectCat2 ON tmpSelectCatPage.page_id = tmpSelectCat2.cl_from 
				WHERE tmpSelectCat2.cl_from IS NULL GROUP BY tmpSelectCat1.cl_to";
		# Run the query:
		$m_res = $m_dbObj->query( $m_sql, __METHOD__ );
		# Process the resulting rows:
		while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {
			# Attach the entry to our array:
			$m_allCats = array_merge( array( $m_row['title'] => 0 ), fnSelectCategoryGetChildren( $m_row['title'] ) );
		}	
		# Free result:
		$m_dbObj->freeResult( $m_res );
	}
	
	# Afterwards return the array to the caller:
	return $m_allCats;
}

function fnSelectCategoryGetChildren( $m_root, $m_prefix = 1 ) {
	# Initialize return value:
	$m_allCats = array();
	
	# Get a database object:
	$m_dbObj =& wfGetDB( DB_SLAVE );
	# Get table names to access them in SQL query:
	$m_tblCatLink = $m_dbObj->tableName( 'categorylinks' );
	$m_tblPage = $m_dbObj->tableName( 'page' );
	
	# The normal query to get all children of a given root category:
	$m_sql = "	SELECT tmpSelectCatPage.page_title AS title
			FROM $m_tblCatLink AS tmpSelectCat 
			LEFT JOIN $m_tblPage AS tmpSelectCatPage ON tmpSelectCat.cl_from = tmpSelectCatPage.page_id 
			WHERE tmpSelectCat.cl_to LIKE '$m_root' AND tmpSelectCatPage.page_namespace = 14";
	# Run the query:
	$m_res = $m_dbObj->query( $m_sql, __METHOD__ );
	# Process the resulting rows:
	while ( $m_row = $m_dbObj->fetchRow( $m_res ) ) {
		# Add current entry to array:
		$m_allCats = array_merge( array( $m_row['title'] => $m_prefix ), fnSelectCategoryGetChildren( $m_row['title'], ++$m_prefix ) );
	}	
	# Free result:
	$m_dbObj->freeResult( $m_res );
	
	# Afterwards return the array to the upper recursion level:
	return $m_allCats;
}

## Returns an array with the categories the articles is in.
## Also removes them from the text the user views in the editbox.
function fnSelectCategoryGetPageCategories( $m_pageObj ) {
	global $wgContLang;
	
	# Get page contents:
	$m_pageText = $m_pageObj->textbox1;
	# Get localised namespace string:
	$m_catString = strtolower( $wgContLang->getNsText( NS_CATEGORY ) );
	# The regular expression to find the category links:
	$m_pattern = '\[\[({$m_catString}|category):(.*)\]\]';
	$m_replace = '$2';
	# The container to store all found category links:
	$m_catLinks = array ();
	# The container to store the processed text:
	$m_cleanText = '';

	# Check linewise for category links:
	foreach( explode( "\n", $m_pageText ) as $m_textLine ) {
		# Filter line through pattern and store the result:
                $m_cleanText .= trim( preg_replace( "/{$m_pattern}/i", "", $m_textLine ) ) . "\n";
		# Check if we have found a category, else proceed with next line:
                if( !preg_match( "/{$m_pattern}/i", $m_textLine) ) continue;
		# Get the category link from the original text and store it in our list:
		$m_catLinks[ preg_replace( "/.*{$m_pattern}/i", $m_replace, $m_textLine ) ] = true;
	}
	# Place the cleaned text into the text box:
	$m_pageObj->textbox1 = $m_cleanText;
	
	# Return the list of categories as an array without dupes:
	return $m_catLinks;
}
?>