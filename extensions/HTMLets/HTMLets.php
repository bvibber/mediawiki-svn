<?php
/**
 * HTMLets extension - lets you inline HTML snippets from files in a given directory.
 *
 * Usage: on a wiki page, &lt;htmlet&gt;foobar&lt;/htmlet%gt; will inline the contents (HTML) of the 
 * file <tt>foobar.html</tt> from the htmlets directory. The htmlets directory can be
 * configured using <tt>$wgHTMLetsDirectory</tt>; it defaults to $IP/htmlets, i.e. the
 * directory <tt>htmlets</tt> in the installation root of MediaWiki. 
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Daniel Kinzler, brightbyte.de
 * @copyright © 2007 Daniel Kinzler
 * @licence GNU General Public Licence 2.0 or later
 */

$wgExtensionCredits['other'][] = array( 
	'name' => 'HTMLets', 
	'author' => 'Daniel Kinzler', 
	'url' => 'http://mediawiki.org/wiki/HTMLets_extension',
	'description' => 'lets you inline HTML snippets from files',
);

$wgHTMLetsDirectory = NULL;
$wgExtensionFunctions[] = "wfHTMLetsExtension";

function wfHTMLetsExtension() {
    global $wgParser;
    $wgParser->setHook( "htmlet", "wfRenderHTMLet" );
}

# The callback function for converting the input text to HTML output
function wfRenderHTMLet( $name, $argv, &$parser ) {
    global $wgHTMLetsDirectory, $IP;

    $dir = $wgHTMLetsDirectory;
    if (!$dir) $dir = "$IP/htmlets";

    $name = preg_replace('@[\\/!]|^\.+@', '', $name); #strip path separators and leading dots.
    $name .= '.html'; #append html ending, for added security and conveniance

    $f = "$dir/$name";

    if (!file_exists($f)) {
        $output = '<div class="error">Can\'t find html file '.htmlspecialchars($name).'</div>';
    }
    else {
        $output = file_get_contents($f);
        if ($output === false) $output = '<div class="error">Failed to load html file '.htmlspecialchars($name).'</div>';
    }

    return $output;
}
?>