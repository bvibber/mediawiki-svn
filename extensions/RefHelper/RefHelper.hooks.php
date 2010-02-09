<?php

if (!defined('MEDIAWIKI')) {
        exit( 1 );
}

class RefHelperHooks {
	function addRefHelperJavascript( $pageObj ) {
	    global $wgRefHelperExtensionPath;
	    $pageObj->addScript( "<script src='$wgRefHelperExtensionPath/refhelper.js' type='text/javascript'></script>" );
	    return TRUE;
	}
	
	function addRefHelperLink( $tpl ) {
	    ?><li id="t-reflink"><?php
	        ?><a href="/on/Special:RefHelper">Create Reference</a><?php
	    ?></li><?php
	    ?><li id="t-reflink"><?php
	        ?><a href="/on/Special:RefSearch">Create Reference from Search</a><?php
	    ?></li><?php
	    return TRUE;
	}
}
