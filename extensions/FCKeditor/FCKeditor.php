<?php

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
    echo <<<HEREDOC
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/FCKeditor/FCKeditor.php" );
HEREDOC;
    exit( 1 );
}

/*
This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
*/

require_once $IP . "/includes/GlobalFunctions.php";
require_once $IP . "/includes/ParserOptions.php";
require_once $IP . "/includes/Parser.php";

$dir = dirname(__FILE__) . '/';
require_once $dir . "FCKeditorSajax.body.php";
require_once $dir . "FCKeditorParser.body.php";
require_once $dir . "FCKeditorParserOptions.body.php";
require_once $dir . "FCKeditorSkin.body.php";
require_once $dir . "FCKeditor.body.php";
require_once $dir . "fckeditor" . DIRECTORY_SEPARATOR . "fckeditor.php";

$wgExtensionMessagesFiles['FCKeditor'] = $dir . 'FCKeditor.i18n.php';

$wgFCKEditorExtDir       = "extensions/FCKeditor";
$wgFCKEditorDir          = "extensions/FCKeditor/fckeditor";
$wgFCKEditorToolbarSet   = "Wiki";
$wgFCKEditorHeight       = "0";		// "0" for automatic ("300" minimum).

/**
 * Enable use of AJAX features.
 */
$wgUseAjax = true;
$wgAjaxExportList[] = 'wfSajaxSearchImageFCKeditor';
$wgAjaxExportList[] = 'wfSajaxSearchArticleFCKeditor';
$wgAjaxExportList[] = 'wfSajaxWikiToHTML';
$wgAjaxExportList[] = 'wfSajaxGetImageUrl';
$wgAjaxExportList[] = 'wfSajaxGetMathUrl';

$wgExtensionCredits['other'][] = array(
	"name" => "FCKeditor extension",
	"author" => "FCKeditor.net (inspired by the code written by Mafs [Meta])",
	"version" => 'fckeditor/mw-extension $Rev$ 2008',
	"url" => "http://www.mediawiki.org/wiki/Extension:FCKeditor_(by_FCKeditor_and_Wikia)",
	"description" => "Use the FCKeditor for editing wiki pages",
	"descriptionmsg" => "textrichditor-desc",
);

$fckeditor = new FCKeditor("fake");
$wgFCKEditorIsCompatible = $fckeditor->IsCompatible();

$oFCKeditorExtension = new FCKeditor_MediaWiki();
$oFCKeditorExtension->registerHooks();
