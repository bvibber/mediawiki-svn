<?php
/**
 * Internationalisation file for extension DeleteQueue.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Andrew Garrett
 */
$messages['en'] = array(
	// General
	'deletequeue-desc' => 'Creates a [[Special:DeleteQueue|queue-based system for managing deletion]]',

	// Landing page
	'deletequeue-action' => 'Suggest deletion',
	'deletequeue-action-title' => "Suggest deletion of \"$1\"",
	'deletequeue-action-text' => "{{SITENAME}} has a number of processes for deleting pages:
*If you believe that this page warrants ''speedy deletion'', you may suggest that [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=speedy}} here].
*If this page does not warrant speedy deletion, but ''deletion will likely be uncontroversial'', you should [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=prod}} propose uncontested deletion].
*If this page's deletion is ''likely to be contested'', you should [{{fullurl:{{FULLPAGENAME}}|action=delnom&queue=deletediscuss}} open a discussion].",

	// Permissions errors
	'deletequeue-permissions-noedit' => "You must be able to edit a page to be able to affect its deletion status.",

	// Nomination forms
	'deletequeue-generic-reasons' => "* Generic reasons\n ** Vandalism\n ** Spam\n ** Maintenance\n ** Out of project scope",

	// Speedy deletion
	'deletequeue-speedy-title' => 'Mark "$1" for speedy deletion',
	'deletequeue-speedy-text' => "You can use this form to mark the page \"'''$1'''\" for speedy deletion.

An administrator will review this request, and, if it is well-founded, delete the page.
You must select a reason for deletion from the drop-down list below, and add any other relevant information.",
	'deletequeue-speedy-reasons' => "-",

	// Proposed deletion
	'deletequeue-prod-title' => "Propose deletion of \"$1\"",
	'deletequeue-prod-text' => "You can use this form to propose that \"'''$1'''\" is deleted.\n
	If, after five days, nobody has contested this page's deletion, it will be deleted after final review by an administrator.",
	'deletequeue-prod-reasons' => '-',

	'deletequeue-delnom-reason' => 'Reason for nomination:',
	'deletequeue-delnom-otherreason' => 'Other reason',
	'deletequeue-delnom-extra' => 'Extra information:',
	'deletequeue-delnom-submit' => 'Submit nomination',

	// Log entries
	'deletequeue-log-nominate' => "nominated [[$1]] for deletion in the '$2' queue.",
	'deletequeue-log-rmspeedy' => "declined to speedily delete [[$1]].",
	'deletequeue-log-requeue' => "transferred [[$1]] to a different deletion queue: from '$2' to '$3'.",
	'deletequeue-log-dequeue' => "removed [[$1]] from the deletion queue '$2'.",

	// Rights
	'right-speedy-nominate' => 'Nominate pages for speedy deletion',
	'right-speedy-review' => 'Review nominations for speedy deletion',
	'right-prod-nominate' => 'Propose page deletion',
	'right-prod-review' => 'Review proposed deletion',
	'right-prod-admin' => 'Close uncontested deletion proposals',
	'right-deletediscuss-nominate' => 'Start deletion discussions',
	'right-deletediscuss-review' => 'Close deletion discussions',

	// Queue names
	'deletequeue-queue-speedy' => 'Speedy deletion',
	'deletequeue-queue-prod' => 'Proposed deletion',
	'deletequeue-queue-deletediscuss' => 'Deletion discussion',

	// Display of status in page body
	'deletequeue-page-speedy' => "This page has been nominated for speedy deletion.
The reason given for this deletion is ''$1''.",
	'deletequeue-page-prod' => "It has been proposed that this page is deleted.
The reason given was ''$1''.If this proposal is uncontested at ''$2'', this page will be deleted.",
	'deletequeue-page-deletediscuss' => "This page has been proposed for deletion, and that proposal has been contested.
The reason given was ''$1''.
A discussion is ongoing at [[$3]], which will conclude at ''$2''",

	// Review
	//Generic
	'deletequeue-notqueued' => 'The page you have selected is currently not queued for deletion',
	'deletequeue-review-action' => "Action to take:",
	'deletequeue-review-delete' => "Delete the page.",
	'deletequeue-review-change' => "Delete this page, but with a different rationale.",
	'deletequeue-review-requeue' => "Transfer this page to the following queue:",
	'deletequeue-review-dequeue' => "Take no action, and remove the page from the deletion queue.",
	'deletequeue-review-reason' => 'Comments:',
	'deletequeue-review-newreason' => 'New reason:',
	'deletequeue-review-newextra' => 'Extra information:',
	'deletequeue-review-submit' => 'Save Review',
	'deletequeue-review-original' => "Reason for nomination",
	'deletequeue-actiondisabled-involved' => 'The following action is disabled because you have taken part in this deletion case in the roles $1:',
	'deletequeue-actiondisabled-notexpired' => 'The following action is disabled because the deletion nomination has not yet expired:',
	'deletequeue-review-badaction' => 'You specified an invalid action',
	'deletequeue-review-actiondenied' => 'You specified an action which is disabled for this page',
	"deletequeue-review-objections" => "'''Warning''': The deletion of this page has [{{FULLURL:{{FULLPAGENAME}}|action=delvoteview|votetype=object}} objections].
Please ensure that you have considered these objections before deleting this page.",
	//Speedy deletion
	'deletequeue-reviewspeedy-tab' => 'Review speedy deletion',
	'deletequeue-reviewspeedy-title' => 'Review speedy deletion nomination of "$1"',
	'deletequeue-reviewspeedy-text' => "You can use this form to review the nomination of \"'''$1'''\" for speedy deletion.
Please ensure that this page can be speedily deleted in accordance with policy.",
	//Proposed deletion
	'deletequeue-reviewprod-tab' => 'Review proposed deletion',
	'deletequeue-reviewprod-title' => 'Review proposed deletion of "$1"',
	'deletequeue-reviewprod-text' => "You can use this form to review the uncontested nomination of \"'''$1'''\" for deletion.

You may contest the deletion of this page by transferring it to the deletion discussion queue.",
	// Discussions
	'deletequeue-reviewdeletediscuss-tab' => 'Review deletion',
	'deletequeue-reviewdeletediscuss-title' => "Review deletion discussion for \"$1\"",
	'deletequeue-reviewdeletediscuss-text' => "You can use this form to review the deletion discussion of \"'''$1'''\".

A [{{FULLURL:{{FULLPAGENAME}}|action=delviewvotes}} list] of endorsements and objections of this deletion is available, and the discussion itself can be found at [[$2]].
Please ensure that you make a decision in accordance with the consensus on the discussion.",

	// Deletion discussions
	'deletequeue-deletediscuss-discussionpage' => "This is the discussion page for the deletion of [[$1]].
There are currently $2 {{PLURAL:$2|user|users}} endorsing deletion, and $3 {{PLURAL:$3|user|users}} objecting to deletion.
You may [{{FULLURL:$1|action=delvote}} endorse or object] to deletion, or [{{FULLURL:$1|action=delviewvotes}} view all endorsements and objections].",
	'deletequeue-discusscreate-summary' => 'Creating discussion for deletion of [[$1]].',
	'deletequeue-discusscreate-text' => 'Deletion proposed for the following reason: $2 ~~~~',

	// Roles
	'deletequeue-role-nominator' => 'original nominator for deletion',
	'deletequeue-role-vote-endorse' => 'endorser of deletion',
	'deletequeue-role-vote-object' => 'objector to deletion',

	// Endorsement and objection
	'deletequeue-vote-tab' => 'Endorse/Object to deletion',
	'deletequeue-vote-title' => 'Endorse or object to deletion of "$1"',
	'deletequeue-vote-text' => "You may use this form to endorse or object to the deletion of \"'''$1'''\".
This action will override any previous endorsements/objections you have given to deletion of this page.
You can [{{FULLURL:{{FULLPAGENAME}}|action=delviewvotes}} view] the existing endorsements and objections.
The reason given in the nomination for deletion was ''$2''.",
	'deletequeue-vote-legend' => 'Endorse/Object to deletion',
	'deletequeue-vote-action' => 'Recommendation:',
	'deletequeue-vote-endorse' => 'Endorse deletion.',
	'deletequeue-vote-object' => 'Object to deletion.',
	'deletequeue-vote-reason' => 'Comments:',
	'deletequeue-vote-submit' => 'Submit',
	'deletequeue-vote-success-endorse' => 'You have successfully endorsed the deletion of this page.',
	'deletequeue-vote-success-object' => 'You have successfully objected to the deletion of this page.',
	'deletequeue-vote-requeued' => 'You have successfully objected to the deletion of this page.
Due to your objection, the page has been moved to the $1 queue.',

	// View all votes
	'deletequeue-showvotes' => "Endorsements and objections to deletion of \"$1\"",
	'deletequeue-showvotes-text' => "Below are the endorsements and objections made to the deletion of the page \"'''$1'''\".
You can register your own endorsement of, or objection to this deletion [{{FULLURL:{{FULLPAGENAME}}|action=delvote}} here].",
	'deletequeue-showvotes-restrict-endorse' => "Show endorsements only",
	'deletequeue-showvotes-restrict-object' => "Show objections only",
	'deletequeue-showvotes-restrict-none' => "Show all endorsements and objections",
	'deletequeue-showvotes-vote-endorse' => "'''Endorsed''' deletion at $1 $2",
	'deletequeue-showvotes-vote-object' => "'''Objected''' to deletion at $1 $2",
	'deletequeue-showvotes-showingonly-endorse' => "Showing only endorsements",
	'deletequeue-showvotes-showingonly-object' => "Showing only objections",
	'deletequeue-showvotes-none' => "There are no endorsements or objections to the deletion of this page.",
	'deletequeue-showvotes-none-endorse' => "There are no endorsements of the deletion of this page.",
	'deletequeue-showvotes-none-object' => "There are no objections to the deletion of this page.",

	// List of queued pages
	'deletequeue' => 'Deletion queue',
	'deletequeue-list-text' => "This page displays all pages which are in the deletion system.",
	'deletequeue-list-search-legend' => 'Search for pages',
	'deletequeue-list-queue' => 'Queue:',
	'deletequeue-list-status' => 'Status:',
	'deletequeue-list-expired' => 'Show only nominations requiring closing.',
	'deletequeue-list-search' => 'Search',
	'deletequeue-list-anyqueue' => '(any)',
	'deletequeue-list-votes' => 'List of votes',
	'deletequeue-list-votecount' => '$1 {{PLURAL:$1|endorsement|endorsements}}, $2 {{PLURAL:$2|objection|objections}}',
	'deletequeue-list-header-page' => 'Page',
	'deletequeue-list-header-queue' => 'Queue',
	'deletequeue-list-header-votes' => 'Endorsements and objections',
	'deletequeue-list-header-expiry' => 'Expiry',
	'deletequeue-list-header-discusspage' => 'Discussion page',
);
