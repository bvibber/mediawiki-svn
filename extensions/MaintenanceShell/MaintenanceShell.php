<?php

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        echo wfMsg_MS('maintshell-installfail3');
        exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'MaintenanceShell',
	'author' => '[http://swiftlytilting.com SwiftlyTilting]',
	'url' => 'http://www.mediawiki.org/wiki/Extension:MaintenanceShell',
	'description' => 'Adds a special page to provide access to maintenance scripts.',
	'descriptionmsg' => 'maintenanceshell-desc',
	'version' => '0.2.4',
);

$dir = dirname(__FILE__) . '/';

$wgAutoloadClasses['MaintenanceShell'] = $dir . 'MaintenanceShell_body.php'; # Tell MediaWiki to load the extension body.
$wgExtensionMessagesFiles['MaintenanceShell'] = $dir . 'MaintenanceShell.i18n.php';
$wgExtensionAliasesFiles['MaintenanceShell'] = $dir . 'MaintenanceShell.alias.php';
$wgSpecialPages['MaintenanceShell'] = 'MaintenanceShell'; # Let MediaWiki know about your new special page.

// Special page group for MW 1.13+
$wgSpecialPageGroups['MaintenanceShell'] = 'wiki';

// New user right - required to access Special:MaintenanceShell
$wgAvailableRights[] = 'maintenanceshell';

// check that there is a group assigned to maintenanceshell
$wgMaintShellPermissions = 0;
foreach ($wgGroupPermissions as $v)
{ $wgMaintShellPermissions += array_key_exists('maintenanceshell', $v) ? 1 : 0;
}

// load language file
require_once($IP . '/extensions/MaintenanceShell/MaintenanceShell.i18n.php');

// check for custom settings
global $wgMaintenancePath, $wgLanguageCode;

$wgMaintenanceShellLang = isset($wgLanguageCode) ? $wgLanguageCode : 'en';
$maintenance_path = isset( $wgMaintenancePath) ?  $wgMaintenancePath :  $IP . "/maintenance/"; 
$maintshell_pagename = wfMsg_MS( 'maintshell-pagename');

// catch operations before wiki does anything, so we can act like we're coming from the command line
if (array_key_exists('commandline', $_REQUEST) && array_key_exists('title', $_REQUEST) && ($_REQUEST['title'] = $maintshell_pagename))
{

	// first lets check to see if we're installed correctly
	if ($wgMaintShellPermissions === 0)
	{	echo wfMsg_MS('maintshell-installfail');
		exit;
	}

	// set up system to verify user permissions
	require_once('./includes/Setup.php');

	$head_redirect = 'Location: http://' .  $_SERVER['SERVER_NAME'] .
	               ($_SERVER['SERVER_PORT'] =="80" ? "":$_SERVER['SERVER_PORT']) . $_SERVER['SCRIPT_NAME'] . '?title=' . $maintshell_pagename;
	if ( $wgUser->isBlocked() || wfReadOnly() || !$wgUser->isAllowed( 'maintenanceshell' ) ) {
		header($head_redirect);
		return;
	}

	echo '<a href="' . $_SERVER['SCRIPT_NAME'] . '?title=' . $maintshell_pagename . '">'. wfMsg_MS('maintshell-return') .  '</a>';

	echo '<hr />';
	if (array_key_exists('script', $_REQUEST) and (($script = trim($_REQUEST['script'])) !== ''))
	{
		$script = str_replace(array('.','/'), '', $script);

		
		if (file_exists(trim($maintenance_path.$script.".php")))
		{
			// display shell frame
			echo "<div style='border:5px solid gray;background-color:black;color:green;padding:1em'>";
			echo "<pre style='white-space: -moz-pre-wrap;white-space: -pre-wrap;white-space: -o-pre-wrap;white-space: pre-wrap;word-wrap: break-word;'><b>";
			chdir(isset($wgMaintenanceShellDir) ? $wgMaintenanceShellDir : $maintenance_path);
			echo getcwd() . "$ php ". (isset($wgMaintenanceShellDir) ? $maintenance_path : '') .$script . ".php " . $_REQUEST['commandline'] . "</b>\n\n";

			// make commandLine.inc think we're coming from the command line
			// Unset request method and build $argv
			unset($_SERVER['REQUEST_METHOD']);

			$_REQUEST['commandline'] = str_ireplace('{{root}}', $_SERVER['DOCUMENT_ROOT'], $_REQUEST['commandline']);

			// handle quote marks in command line

			preg_match_all('%\"(.*)\"%Us', $_REQUEST['commandline'], $matches);
			$temp_command = $_REQUEST['commandline'];
			foreach ($matches[1] as $n => $v)
			{
				$temp_command = str_replace($v, str_replace(" ","\n", $v), $temp_command );
			}

			$temp_command = preg_replace('% +%', ' ', $temp_command);
			$argv = explode(' ',basename($_SERVER["PHP_SELF"]) . ' ' . $temp_command);

			$search = array( '"', "\n");
			$replace = array('', ' ');
			foreach($argv as $n => $v)
			{	$argv[$n] = str_replace($search, $replace, $v);
			}

			$argc = count($argv);

			// catch exit calls from within the called script

			function exit_callback($param = false)
			{  
				echo "</pre></div><hr />$param";
				exit;
			}
			
			register_shutdown_function('exit_callback');
			
			// needed for MW 1.15
			ob_start();
			
			// call the script			
			include_once($maintenance_path.$script.".php");
			
			// exit, if the script doesn't explictly exit.  Will call the exit callback function
			exit;
		}
		else
		{	echo wfMsg_MS('maintshell-noexist',  $maintenance_path .$script);
		}
	}
	exit;
}


// language functions which aren't loaded my MW yet, so define them manually
function wfMsg_MS($key)
{	global $wgMaintenanceShellLang, $messages; 
	$args = func_get_args();
   array_shift( $args );

	if ( array_key_exists( $wgMaintenanceShellLang, $messages ) && array_key_exists( $key, $messages[$wgMaintenanceShellLang] ) )
	{	$ret = $messages[$wgMaintenanceShellLang][$key];
	}
	else
	{	$ret = $messages['en'][$key];
	}
	
	return wfMsgReplaceArgs_MS($ret, $args);
}

function wfMsgReplaceArgs_MS( $message, $args ) 
{
	# Fix windows line-endings
	# Some messages are split with explode("\n", $msg)
	$message = str_replace( "\r", '', $message );
	
	// Replace arguments
	if ( count( $args ) ) {
	        if ( is_array( $args[0] ) ) {
	                $args = array_values( $args[0] );
	        }
	        $replacementKeys = array();
	        foreach( $args as $n => $param ) {
	                $replacementKeys['$' . ($n + 1)] = $param;
	        }
	        $message = strtr( $message, $replacementKeys );
	}
	
	return $message;
}

