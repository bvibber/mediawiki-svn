<?php

/**
 * ScriptLoader verison of Usability Initiative extension
 * (just a demo) 
 */

/* Configuration */

// Set this to false to include all plugins individually
$wgUsabilityInitiativeResourceMode = 'minified';

/* Setup */

// Adds Autoload Classes
$wgAutoloadClasses['UsabilityInitiativeHooks'] =
	dirname( __FILE__ ) . "/UsabilityInitiative.hooks.php";
	
	

