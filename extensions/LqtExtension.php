<?php

  /**
   * @package MediaWiki
   * @subpackage Extensions
   * @author David McCabe <davemccabe@gmail.com>
   * @licence GPL2
   */

  // This would be replaced by an actual dispatching system.

if( !defined( 'MEDIAWIKI' ) ) {
     echo( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
     die( -1 );
}
else {

//     require_once( 'LqtController' );
     require_once( 'LqtStandardViews.php' );

     $wgExtensionFunctions[] = 'lqtInitialize';

     function lqtSpecialCaseHook( &$title, &$output, $request ) {
	  if( $title->getNamespace() === LQT_NS_THREAD ) {
//	       LqtController::execute();
	       $v = new PermalinkView($title);
	       $v->show();
	       return false;
	  }
	  else if( $title->getNamespace() === LQT_NS_CHANNEL ) {
//	       LqtController::execute();
	       $v= new ChannelView($title);
	       $v->show();
	       return false;
	  }
	  else {
	       return true;
	  }
     }
     
     function lqtInitialize() {
	  global $wgMessageCache, $wgHooks;
	  $wgMessageCache->addMessage( 'lq', 'LiquidThreads' );
	  $wgHooks['SpecialCase'][] = 'lqtSpecialCaseHook';
    }
}

