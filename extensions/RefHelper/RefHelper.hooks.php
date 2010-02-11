<?php

if (!defined('MEDIAWIKI')) {
        exit( 1 );
}

class RefHelperHooks {
	function __construct() {
		wfLoadExtensionMessages('RefHelper');
	}
	function addRefHelperJavascript( $pageObj ) {
	    global $wgRefHelperExtensionPath;
	    $pageObj->addScript( 
			Xml::element('script',array('src'=>"$wgRefHelperExtensionPath/refhelper.js", 'type'=>'text/javascript') ) );
	    return TRUE;
	}
	
	function addRefHelperLink( $tpl ) {
		global $wgScript;
		echo Xml::openElement('li',array('class'=>'t-reflink')) .
			Xml::element('a',array('href'=>"$wgScript?title=Special:RefHelper"), wfMsg( RefHelper::MSG . 'toolbox_link_create' ) ) .
			Xml::closeElement('li');
		echo Xml::openElement('li',array('class'=>'t-reflink')) .
			Xml::element('a',array('href'=>"$wgScript?title=Special:RefSearch"), wfMsg( RefHelper::MSG . 'toolbox_link_search' ) ) .
			Xml::closeElement('li');
	    return TRUE;
	}
}
