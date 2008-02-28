<?php
$wgExtensionFunctions[] = "wfYUI";

function wfYUI() {
	global $wgOut;
	$wgOut->addScript("<script type=\"text/javascript\" src=\"/extensions/YUI/yui.js\"></script>\n");
}
