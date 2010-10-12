<?php
/**
 * Internationalisation file for ArticleComments extension.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'article-comments-desc' => 'Enables comment sections on article pages',
	'article-comments-title-string' => 'title',
	'article-comments-name-string' => 'Name',
	'article-comments-name-field' => 'Name (required):',
	'article-comments-url-field' => 'Website:',
	'article-comments-url-string' => 'URL',
	'article-comments-comment-string' => 'Comment',
	'article-comments-comment-field' => 'Comment:',
	'article-comments-submit-button' => 'Submit',
	'article-comments-leave-comment-link' => 'Leave a comment ...',
	'article-comments-invalid-field' => 'The $1 provided <nowiki>[$2]</nowiki> is invalid.',
	'article-comments-required-field' => '$1 field is required.',
	'article-comments-submission-failed' => 'Comment submission failed',
	'article-comments-failure-reasons' => 'Sorry, your comment submission failed for the following reason(s):',
	'article-comments-no-comments' => 'Sorry, the article "[[$1]]" is not accepting comments at this time.',
	'article-comments-talk-page-starter' => "<noinclude>Comments on [[$1]]\n<comments />\n----- __NOEDITSECTION__</noinclude>\n",
	'article-comments-commenter-said' => '$1 said ...',
	'article-comments-summary' => 'Comment provided by $1 - via ArticleComments extension',
	'article-comments-submission-succeeded' => 'Comment submission succeeded',
	'article-comments-submission-success' => 'You have successfully submitted a comment for [[$1]]',
	'article-comments-submission-view-all' => 'You may view all comments on that article [[$1|here]]',
	'article-comments-prefilled-comment-text' => '',
	'article-comments-user-is-blocked' => 'Your user account is currently blocked from editing [[$1]].',
	'article-comments-new-comment-heading' => "\n== {{int:article-comments-commenter-said|\$1}} ==\n\n",
	'article-comments-comment-bad-mode' => '<div class="error">Invalid mode given for comment. Available ones are plain, normal and wiki.</div>',
	'article-comments-comment-contents' => "<div class='commentBlock'><small>$4</small>$5--\$3</div>\n",
	'article-comments-comment-missing-name-parameter' => 'Missing name',
	'article-comments-comment-missing-date-parameter' => 'Missing comment date',
	'article-comments-no-spam' => 'At least one of the submitted fields was flagged as spam.',
	'processcomment' => 'Process article comment',
);

$messages['qqq'] = array(
	'article-comments-required-field' => 'Shown as a list below article-comments-failure-reasons. With $1 being one of article-comments-*-string messages.',
	'article-comments-submission-failed' => 'Page title when there are errors in the comment submission',
	'article-comments-invalid-field' => 'Shown as a list below article-comments-failure-reasons. With $1 being article-comments-title-string or article-comments-url-string messages, and $2 the wrong value.',
	'article-comments-new-comment-heading' => 'Wiki text which will appear above the &lt;comment&gt; tags.

Available variables:
* $1 - Commenter name.
* $2 - Commenter url (may be empty).
* $3 - Datetime.
* $4 - Comment text.',
	'article-comments-comment-contents' => 'Way in which &lt;comment&gt; tags are parsed.
Note it is importat not to place the $5 between new-lines in order to get new lines correctly converted into new paragraphs in normal mode (wfMsgExt can\'t place it inside a <p/>).
	
* $1 - Commenter name.
* $2 - Comment url.
* $3 - User signature, if an URL is available, name linking to its web.
* $4 - Parsed datetime.
* $5 - Comment text.',
);

