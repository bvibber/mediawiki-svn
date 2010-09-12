<?php
$messages = array();

/** English
 * @author Nimish Gautam
 * @author Sam Reed
 * @author Brandon Harris
 */
$messages['en'] = array(
	'articleassessment' => 'Article assessment',
	'articleassessment-desc' => 'Article assessment pilot version',
	'articleassessment-yourfeedback' => 'Your feedback',
	'articleassessment-pleaserate' => 'Please take a moment to rate this page below.',
	'articleassessment-submit' => 'Submit',
	'articleassessment-rating-wellsourced' => 'Well-Sourced:',
	'articleassessment-rating-neutrality' => 'Neutral:',
	'articleassessment-rating-completeness' => 'Complete:',
	'articleassessment-rating-readability' => 'Readable:',
	'articleassessment-rating-wellsourced-tooltip' => 'Do you feel this article has sufficient citations and that those citations come from trustworthy sources?',
	'articleassessment-rating-neutrality-tooltip' => 'Do you feel that this article shows a fair representation of all perspectives on the issue?',
	'articleassessment-rating-completeness-tooltip' => 'Do you feel that this article covers the essential topic areas that it should?',
	'articleassessment-rating-readability-tooltip' => 'Do you feel that this article is well-organized and well written?',
	'articleassessment-articlerating' => 'Article rating',
	'articleassessment-error' => 'An error has occurred.
Please try again later.',
	'articleassessment-thanks' => 'Thanks! Your ratings have been saved.',

	# This special page doesn't exist yet, but it will soon.
	'articleassessment-featurefeedback' => 'Give us [[Special:Article Assessment Feedback|feedback]] about this feature.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|rating|ratings}})',
	# Some of these messages are unused in the code as it is but may be in the future depending on performance.  They should still be translated.
	'articleassessment-stalemessage-revisioncount' => "This article has been ''revised'' '''$1 times''' since you last reviewed it.
You may wish to re-rate it.",
	'articleassessment-stalemessage-greaterthancount' => "This article has been ''revised'' more than $1 times since you last reviewed it.
You may wish to re-rate it.",
	'articleassessment-stalemessage-norevisioncount' => "This article has been ''revised'' since you last reviewed it.
You may wish to re-rate it.",

	# Links get rewritten in javascript.
	'articleassessment-results-show' => "(Results hidden. [[|Show]] them.)",
	'articleassessment-results-hide' => "([[|Hide results]])",
);

/** Message documentation (Message documentation)
 * @author Sam Reed
 * @author Brandon Harris
 */
$messages['qqq'] = array(
	'articleassessment' => 'The title of the feature. It is about reader feedback.
	
Please visit http://prototype.wikimedia.org/articleassess/Main_Page for a prototype installation.',
	'articleassessment-desc' => '{{desc}}',
	'articleassessment-yourfeedback' => 'This is a box or section header.  It indicates that the contents of the box are personal to the user.',
	'articleassessment-pleaserate' => 'This is a call to action for the user to provide their ratings about the page.',
	'articleassessment-submit' => 'This is for when the user submits their ratings to the server.',
	'articleassessment-rating-wellsourced' => 'This is a rating metric label. The metric is for measuring how researched the article is.',
	'articleassessment-rating-neutrality' => "This is a rating metric label. The metric is for measuring an article's NPOV.",
	'articleassessment-rating-completeness' => 'This is a rating metric label. The metric is for measuring how comprehensive the article is.',
	'articleassessment-rating-readability' => 'This is a rating metric label. The metric is for measuring how well written the article is.',
	'articleassessment-rating-wellsourced-tooltip' => 'This is a tool tip that is designed to explain what the "well-sourced" metric means.',
	'articleassessment-rating-neutrality-tooltip' => 'This is a tool tip that is designed to explain what the "neutrality" metric means.',
	'articleassessment-rating-completeness-tooltip' => 'This is a tool tip that is designed to explain what the "completeness" metric means.',
	'articleassessment-rating-readability-tooltip' => 'This is a tool tip that is designed to explain what the "readability" metric means.',
	'articleassessment-articlerating' => 'This is a box or section header. It indicates that the contents of the box are the average ratings for the article.',
	'articleassessment-error' => 'A generic error message to display on any error.',
	'articleassessment-thanks' => 'The message to display when the user has successfully submitted a rating.',
	'articleassessment-featurefeedback' => 'This is a call to action link for users to provide feedback about the feature.  It takes them to a survey.',
	'articleassessment-noratings' => 'This indicates the number of ratings that the article has received.',
	# Some of these messages are unused in the code as it is but may be in the future depending on performance.  They should still be translated.	
	'articleassessment-stalemessage-revisioncount' => 'This is a message shown to the user when their ratings are "stale" and includes the number of revisions since.',
	'articleassessment-stalemessage-greaterthancount' => 'This is a message shown to the user when their ratings are "stale". It allows for not looking up the total number of revisions.',
	'articleassessment-stalemessage-norevisioncount' => 'This is a message shown to the user when their ratings are "stale" and does NOT include the number of revisions. This is an ambiguous reason, and allows for us to have complicated staleness patterns. This is the preferred message.',
	'articleassessment-results-show' => 'This is an explanatory control that, when clicked, will display hidden aggregate ratings.
The incomplete looking wikilinks get rewritten in javascript.',
	'articleassessment-results-hide' => 'This is a control that, when clicked, will hide the aggregate ratings.
The incomplete looking wikilinks get rewritten in javascript.',
);

