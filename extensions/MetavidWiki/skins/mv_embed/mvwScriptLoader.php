<?php
/*
 * 	mvwScriptLoader.php
* Script Loading Library for MediaWiki
*
* @author Michael Dale mdale@wikimedia.org
* @date  feb, 2009
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc.,
* 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
* http://www.gnu.org/copyleft/gpl.html
*/

//set the initial javascript classes: (for loading javascript via class name) 
// also works via direct file calls 
//can be extended via 
global $wgJsAutoloadLocalClasses;
$wgJsAutoloadLocalClasses = array(

);

//include WebStart.php (this can be simplified once we move mvwScriptLoader to the root dir: 
$root_path = dirname(__FILE__) . '/../../../../';
chdir($root_path);
require_once('includes/WebStart.php');

wfProfileIn('mvwScriptLoader.php');

if( isset( $_SERVER['SCRIPT_URL'] ) ) {
	$url = $_SERVER['SCRIPT_URL'];
} else {
	$url = $_SERVER['PHP_SELF'];
}

//if( strcmp( "$wgScriptPath/mvwScriptLoader$wgScriptExtension", $url ) ) {
if( strpos($url, "mvwScriptLoader$wgScriptExtension") === false){
	wfHttpError( 403, 'Forbidden',
		'mvwScriptLoader must be accessed through the primary script entry point.' );
	return;
}
//Verify the script loader is on: 
if (!$wgEnableScriptLoader) {
	echo 'MediaWiki API is not enabled for this site. Add the following line to your LocalSettings.php';
	echo '<pre><b>$wgEnableAPI=true;</b></pre>';
	die(1);
}
//run the main action: 
do_return_js();
function do_return_js(){
	global $wgJsAutoloadLocalClasses, $wgEnableScriptLoaderJsFile, $wgRequest, $IP;
	$jsFileList = array();
	//check for the requested classes 
	if( $wgRequest->getVal('class') ){
		$reqClassList = explode(',', $wgRequest->getVal('class'));
		//clean the class list and populate jsFileList 	
		foreach($reqClassList as $reqClass){
			$reqClass = ereg_replace("[^A-Za-z0-9_\-]", "", $reqClass );
			if( isset( $wgJsAutoloadLocalClasses[$reqClass] ) ){
				$jsFileList[ $reqClass ] = $wgJsAutoloadLocalClasses[ $reqClass ];
			}
		}
	}
	
	//check for requested files if enabled: 
	if( $wgEnableScriptLoaderJsFile ){
		if( $wgRequest->getVal('files')){
			$reqFileList = explode(',', $wgRequest->getVal('files'));
			//clean the file list and populate jsFileList
			foreach($reqFileList as $reqFile){						
				//no jumping dirs: 
				$reqFile = str_replace('../','',$reqFile);
				//only allow alphanumeric underscores periods and ending with .js
				$reqFile = ereg_replace("[^A-Za-z0-9_\-\/\.]", "", $reqFile );				
				if( substr($reqFile, -3)=='.js'){
					if( is_file( $IP . $reqFile) && !in_array($reqFile, $jsFileList ) ){
	 					$jsFileList[] = $IP . $reqFile;
	 				}						
				}				 				
			}
		}
	}	
	//swap in the appropriate language per file:
	$js_file_string = '';
	foreach($jsFileList as $file_name){
		$js_file_string+= do_proccess_js_file($file_name);
	}
	
	print "<pre>"; 
	print_r( $jsFileList );
}

function do_proccess_js_file($file){
	$file_string = file_get_contents($file);
	//print $file_string;
	//do language swap
	preg_replace_callback("/loadGM\(\s*\{([^}]*)/", 'language_msg_replace', $file_string);		
}
function language_msg_replace($match){
	print "CB: ";
	print_r($match);
}
//do the minification and output
require_once ( dirname(__FILE__) . '/jsmin.php');
?>