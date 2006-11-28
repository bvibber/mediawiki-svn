<?php

# This is a simple debugging tool to inspect the contents of the shared cache
# It is unrestricted and insecure, do not enable it on a public site.


# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo "InspectCache extension";
	exit(1);
}

require_once( dirname(__FILE__) . '/../ExtensionFunctions.php' );
extAddSpecialPage( dirname(__FILE__) . '/InspectCache_body.php', 'InspectCache', 'InspectCache' );

?>
