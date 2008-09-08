<?php
/* vim: noet ts=4 sw=4
 * http://www.gnu.org/licenses/gpl-3.0.txt */

if (!defined("MEDIAWIKI"))
	die();

/* ---- CREDITS ---- */

$wgExtensionCredits['other'][] = array(
	'name'        => "Authors",
	'author'      => "Merrick Schaefer, Mark Johnston, Evan Wheeler and Adam Mckaig (at UNICEF)",
	'description' => "Reformats the recent changes in a human-readable fashion."
);

/* ---- INTERNATIONALIZATION ---- */

require_once ("FormatChanges.i18n.php");
$wgExtensionFunctions[] = "UW_FormatChanges_i18n";

function UW_FormatChanges_i18n() {
    // add this extension's messages to the message cache
	global $wgMessageCache, $wgFormatChangesMessages;
	foreach ($wgFormatChangesMessages as $lang => $messages)
		$wgMessageCache->addMessages ($messages, $lang);
}

/* ---- HOOKS ---- */

$wgHooks['FetchChangesList'][] = "UW_FormatChanges";
function UW_FormatChanges($user, $skin, $list) {
    $list = new UniwikiChangesList($skin);
	return false;
}

class UniwikiChangesList extends ChangesList {

	public function recentChangesLine(&$rc, $watched=false) {
		global $wgLang;

		// set local vars (this apparently does that)
		extract($rc->mAttribs);

		$this->insertDateHeader($line, $rc_timestamp);

		/* NOTE: the following logic is reproduced from
		 *       the old version of the recent changes
		 *       page in case we want to produce a
		 *       similar result (though much is not
		 *       implemented yet)...
		 */

		// moved pages
		if ($rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT) {
			// handle these?
		}
		// log entries(old) and special pages
		else if ($rc_namespace == NS_SPECIAL) {
			// handle these?
		}
		// new unpatrolled pages
		else if ($rc->unpatrolled && $rc_type == RC_NEW) {
			// handle these?
		}
		// log entries
		else if ($rc_type == RC_LOG) {
			// handle these?
		}
		// edits and new pages
		else {
			$line .= "<li>";
			$page_link = $this->skin->makeKnownLinkObj($rc->getTitle(), '');
			if ($this->isDeleted($rc, Revision::DELETED_USER)) {
				$user_link = '<span class="history-deleted">' . wfMsgHtml('rev-deleted-user') . '</span>';
			} else {
				$user_link = ($rc_user > 0) ? $this->skin->userLink($rc_user, $rc_user_text) : wfMsg('fc_anonymous');
			}
			$timestamp = $wgLang->time($rc->mAttribs['rc_timestamp'], true, true);
			$action = ($rc_type == RC_NEW) ? wfMsg('fc_createdby') : wfMsg('fc_editedby');
			$line .= $page_link . " - " . $action . " "  . $user_link . " (" . $timestamp . ")";
			$line .= "</li>";
		}

		return $line;
	}
}
