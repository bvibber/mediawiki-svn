<?php

# The BreadCrumbs extension, an extension for providing an breadcrumbs
# navigation to users.

# @addtogroup Extensions
# @author Manuel Schneider <manuel.schneider@wikimedia.ch>
# @copyright © 2007 by Manuel Schneider
# @licence GNU General Public Licence 2.0 or later


if( !defined( 'MEDIAWIKI' ) ) {
  echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
  die();
}
                
## Options:
# $wgBreadCrumbsDelimiter - set the delimiter
$wgBreadCrumbsDelimiter = ' &gt; ';
# $wgBreadCrumbsCount - number of breadcrumbs to use
$wgBreadCrumbsCount = 5;

## Register extension setup hook and credits:
$wgExtensionFunctions[] = 'fnBreadCrumbs';
$wgExtensionCredits['parserhook'][] = array(
  'name'          => 'BreadCrumbs',
  'author'        => 'Manuel Schneider',
  'url'           => 'http://www.mediawiki.org/wiki/Extension:BreadCrumbs',
  'description'   => 'Shows a breadcrumb navigation.'
);
                                
## Set Hook:
function fnBreadCrumbs() {
  global $wgHooks;

  ##
  $wgHooks['ArticleViewHeader'][] = 'fnBreadCrumbsShowHook';
}

function fnBreadCrumbsShowHook( &$m_pageObj ) {
  global $wgTitle;
  global $wgOut;
  global $wgUser;
  global $wgBreadCrumbsDelimiter;
  global $wgBreadCrumbsCount;
  
  # deserialize data from session into array:
  $m_BreadCrumbs = array();
  $m_BreadCrumbs = $_SESSION['BreadCrumbs'];
  
  # check for doubles:
  if( $m_BreadCrumbs[$wgBreadCrumbsCount-1] != $wgTitle->getPrefixedText() ) {
    # reduce the array set, remove older elements:
    $m_BreadCrumbs = array_slice( $m_BreadCrumbs, -$wgBreadCrumbsCount );
    # add new page:
    array_push( $m_BreadCrumbs, $wgTitle->getPrefixedText() );
    # serialize data from array to session:
    $_SESSION['BreadCrumbs'] = $m_BreadCrumbs;
  }
  
  # acquire a skin object:
  $m_skin =& $wgUser->getSkin();
  # build the breadcrumbs trail:
  $m_trail = '<div id="BreadCrumbsTrail">';
  for( $i = 0; $i < $wgBreadCrumbsCount; $i++ ) {
    $m_trail .= $m_skin->makeLink( $m_BreadCrumbs[$i] );
    if( $i < $wgBreadCrumbsCount-1 ) $m_trail .= $wgBreadCrumbsDelimiter;
  }
  $m_trail .= '</div>';
  $wgOut->addHTML( $m_trail );
  
  # invalidate internal MediaWiki cache:
  $wgTitle->invalidateCache();
  
  # Return true to let the rest work:
  return true;
}
?>