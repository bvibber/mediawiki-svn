<?php

#####################
# PURPOSE
#   PHP script for the extension to use Gnuplot in a mediawiki instance.
#   Insert the Gnuplot code between the tags <gnuplot>...</gnuplot>.
# AUTHOR
#   Christina Pöpper
#   The work has been funded by a fellowship at ESA.
# DATE
#   Jan 19, 2006: creation
#   Jun 30, 2006: security upgrade (filter out system commands) and 
#                 filenames instead of paths v1.1
# NOTE
#   Code adapted from the timeline extension.
#   Include this file in your LocalSettings.php:
#     add 'include("extensions/Gnuplot.php")' to the end of the file.
#   Set member gnuplotCommand of $wgGnuplotSettings to your gnuplot.
####################

class GnuplotSettings {
  var $gnuplotCommand, $defaultTerminal, $defaultSize;

    function GnuplotSettings () {
	$this->gnuplotCommand = '/usr/bin/gnuplot';
	// Set some defaults here; they may be overwritten by the user.
	$this->defaultTerminal = 'set terminal png';
	$this->defaultSize = 'set size 0.5,0.5';
    }
};

$wgGnuplotSettings = new GnuplotSettings;
$wgExtensionFunctions[] = "wfGnuplotExtension";

function wfGnuplotExtension() {
    global $wgParser;
    $wgParser->setHook( "gnuplot", "renderGnuplot" );
}

function renderGnuplot( $gnuplotsrc ) {
    global $wgGnuplotSettings, $wgUploadDirectory, $wgUploadPath;

    // filter out harmfull system commands to close a security hole
    $replaces = array("`" => "", "system" => "", "shell" => "");
    $gnuplotsrc = strtr($gnuplotsrc, $replaces);

    // create directory for storing the plot
    $gnuplotDir = "/gnuplot/";
    $dest = $wgUploadDirectory . $gnuplotDir;
    if (!is_dir($dest)) {
	mkdir($dest, 0777);
	chmod($dest, 0777);
    }

    // get the name of the graph to be produced
    $name = getOutputName ($gnuplotsrc);
    $graphname = $dest . $name;
    $fname = $graphname . ".tmp";

    // write the default settings and the input code from wiki into a 
    // temporary file to be executed by gnuplot, then execute the command
    if ( ! (file_exists($fname) || file_exists($fname . '.err'))) { 
	$handle = fopen($fname, 'w');
	
	// if terminal and size are not set in the gnuplot source we do it here
	if (strpos($gnuplotsrc, 'set terminal ') === false) {
	    fwrite($handle, $wgGnuplotSettings->defaultTerminal . "\n");
	}
	if (strpos($gnuplotsrc, 'set size ') === false) {
	    fwrite($handle, $wgGnuplotSettings->defaultSize . "\n");
	}

	// Need to find each occurance of src:<FILE NAME> and replace it 
	// with the complete file path
	while (strpos($gnuplotsrc, 'src:') != false) {
	    $srcStartPosition = strpos ($gnuplotsrc, 'src:') + strlen("src:");
            $srcEndPosition = strpos ($gnuplotsrc, ' ', $srcStartPosition);
            $tmpString = substr($gnuplotsrc, $srcStartPosition, $srcEndPosition-$srcStartPosition-1);
	    $srcFileNamePath = getSourceDataPath($tmpString);
	    $gnuplotsrc = str_replace("src:$tmpString",$srcFileNamePath,$gnuplotsrc);
        }

	fwrite($handle, "\nset output '" . $graphname . "'\n");
	// Remove the 'set output' command from the source as we will set it.
	$gnuplotsrc = deleteOutputLine($gnuplotsrc);

	fwrite($handle, $gnuplotsrc . "\n");
	fclose($handle);

	$cmdlinePlot = wfEscapeShellArg($wgGnuplotSettings->gnuplotCommand)
	    . ' ' . $fname;
	shell_exec($cmdlinePlot);
	
	// some cleanup
       	unlink($fname);
    }

    return "<p><b><img src=\"" . $wgUploadPath . $gnuplotDir . $name .
	"\" alt=\"Gnuplot Plot\"></b></p>" ;
}

/*** 
 * Function: getOutputName
 * Purpose : Determines the name of the output file. If it is specified by the 
 *           user ("set output 'name'") this name is returned, 
 *	     otherwise a new name is computed (by a hash function).
 * Input   : $gnuplotsrc - the gnuplot input code from wiki
 * Output  : the file name to be used 
 */
function getOutputName ( $gnuplotsrc ) {
    // determine the file format of the plot - default is png
    $format = "png";
    $termpos = strpos($gnuplotsrc, "set terminal ");
    if ($termpos === true) {
      list( , ,$format, ) = split(" ", substr($gnuplotsrc, $termpos), 4);
    }

    $output = "";
    $strlength = strlen("set output ");
    $pos = strpos ($gnuplotsrc, "set output");
    if (! $pos ) {	// If there is no output file specified
	$output = md5($gnuplotsrc) . "." . $format;
    } else {		// If the output file is directly specified
	$posEnd = strpos($gnuplotsrc, "\n", $pos);
	$output = substr($gnuplotsrc, 
			 $pos + $strlength + 1, 
			 $posEnd - $pos - $strlength - 2);
    }
    return $output;
}

/***
 * Function: getSourceDataPath
 * Purpose : Look up the real filesystem path of the specified file
 * Input   : $name - the file name
 * Output  : the filesystem path for the file
 */
function getSourceDataPath ( $name ) {
    $h = Image::newFromName($name);

    if (!$h->exists())
        return null;
    else
        return $h->getImagePath(); 
}

/*** 
 * Function: deleteOutputLine
 * Purpose : Deletes the line starting with 'set output' from the gnuplot 
 *           source. If it does not exist this function has no effect.
 * Input   : $gnuplotsrc - the gnuplot input code from wiki
 * Output  : the file name to be used 
 */
function deleteOutputLine ( $gnuplotsrc ) {
    $strlength = strlen("set output ");
    $pos = strpos ($gnuplotsrc, "set output");

    if ($pos) {
	$posEnd = strpos ($gnuplotsrc, "\n", $pos);

	$gnuplotsrc = substr($gnuplotsrc, 0, $pos) 
	    . substr($gnuplotsrc, $posEnd + 1, strlen($gnuplotsrc) - 1);
     }
   return $gnuplotsrc;
}

