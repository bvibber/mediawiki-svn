<?php
/* vim: noet ts=4 sw=4
 * http://www.mediawiki.org/wiki/Extension:Uniwiki_Generic_Edit_Page
 * http://www.gnu.org/licenses/gpl-3.0.txt */

if (!defined('MEDIAWIKI'))
	die();

/* ---- CREDITS ---- */

/* This code was adapted from CreatePage.php from:
 *     Travis Derouin <travis@wikihow.com>
 *
 * originally licensed as:
 *     GNU GPL v2.0 or later */

$wgExtensionCredits['other'][] = array(
	'name'        => "CreatePage",
	'author'      => "Merrick Schaefer, Mark Johnston, Evan Wheeler and Adam Mckaig (at UNICEF)",
	'description' => "Adds a Special Page for creating new pages"
);

$wgHooks['BeforePageDisplay'][] = 'UW_CreatePage_CSS';
function UW_CreatePage_CSS($out) {
	global $wgScriptPath;
	$out->addScript ("<link rel='stylesheet' href='$wgScriptPath/extensions/uniwiki/CreatePage/style.css' />");
	return true;
}


/* ---- INTERNATIONALIZATION ---- */

require_once( 'CreatePage.i18n.php' );
$wgExtensionFunctions[] = 'UW_CreatePage_i18n';

function UW_CreatePage_i18n() {
	// add this extension's messages to the message cache
	global $wgMessageCache, $wgCreatePageMessages;
	foreach ($wgCreatePageMessages as $lang => $messages)
		$wgMessageCache->addMessages ($messages, $lang);
}


/* ---- SPECIAL PAGE ---- */

require_once("SpecialPage.php");

$wgExtensionFunctions[] = "wfCreatePage";
function wfCreatePage() {
	SpecialPage::AddPage (new SpecialPage ("CreatePage")); }


function wfSpecialCreatePage ($parser) {
	global $wgOut, $wgRequest, $wgUser;
	$skin = $wgUser->getSkin();
	$thisPage = Title::newFromText ("CreatePage", NS_SPECIAL);
	$target = $wgRequest->getVal ("target", null);

	// check to see if we are trying to create a page
	if ($target != null) {
		$title = Title::newFromText ($target);

		if ($title->getArticleID() > 0) {

			// if the title exists then let the user know and give other options
			$wgOut->addWikiText (wfMsg ("createpage_titleexists", $title->getFullText())."<br/>");
			$wgOut->addHTML ("<a href='".$title->getEditURL()."'>".wfMsg ("createpage_editexisting")."</a><br/>"
				.$skin->makeLinkObj ($thisPage, wfMsg ("createpage_tryagain"))
			);
		} else {
			/* TODO - may want to search for closely named pages and give
			 * other options here... */

			// otherwise, redirect them to the edit page for their title
			$wgOut->redirect ($title->getEditURL());
		}

		return;
	}

	// if this is just a normal GET, then output the form

	// prefill the input with the title, if it was passed along
	$newTitle = $wgRequest->getVal("newtitle", null);
	if ($newTitle != null) $newTitle = str_replace("_", " ", $newTitle);

	// add some instructions
	$wgOut->addHTML(wfMsg('createpage_instructions'));

	// js for checking the form
	$wgOut->addHTML("
		<script type='text/javascript' >
			function checkForm(){
					// check the title
					if (document.createpageform.target && document.createpageform.target.value == \"\") {
						alert('".wfMsg('createpage_entertitle')."');
						document.createpageform.target.focus();
						return false;
					}
				// everything is OK, return true
				return true;
			}
		</script>
	");

	// output the form
	$wgOut->addHTML("
		<form method=POST onsubmit='return checkForm()' name='createpageform'>
			<input type=text name=target size=50 value='$newTitle'><br/><br/>
	");

	$wgOut->addHTML("
			<input type=submit value='".wfMsg('createpage_submitbutton')."'>
		</form>
	");
}
