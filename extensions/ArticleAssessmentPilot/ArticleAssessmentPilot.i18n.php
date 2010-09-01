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
	'articleassessment-pleaserate' => "Please take a moment to rate this page below.",
	'articleassessment-submit' => 'Submit',
	'articleassessment-rating-wellsourced' => 'Well-Sourced:',
	'articleassessment-rating-neutrality' => 'Neutrality:',
	'articleassessment-rating-completeness' => 'Completeness:',
	'articleassessment-rating-readability' => 'Readability:',
	'articleassessment-rating-wellsourced-tooltip' => 'Do you feel this article has sufficient citations and that those citations come from trustworthy sources?',
	'articleassessment-rating-neutrality-tooltip' => 'Do you feel that this article shows a fair representation of all perspectives on the issue?',
	'articleassessment-rating-completeness-tooltip' => 'Do you feel that this article covers the essential topic areas that it should?',
	'articleassessment-rating-readability-tooltip' => 'Do you feel that this article is well-organized and well written?',
	'articleassessment-articlerating' => 'Article rating',
	'articleassessment-error' => "We're sorry! An error has occurred. Please try again later.",
	'articleassessment-thanks' => 'Thanks! Your ratings have been saved.',

	# FIXME: Special page seems not exist.
	'articleassessment-featurefeedback' => 'Give us [[Special:Article Assessment Feedback|feedback]] about this feature.',
	'articleassessment-noratings' => '$1 ($2 {{PLURAL:$2|rating|ratings}})',
	'articleassessment-stalemessage-revisioncount' => "This article has been ''revised'' '''$1 times''' since you last reviewed it. You may wish to re-rate it.",

	# FIXME: Message seems unused. Please check.
	'articleassessment-stalemessage-norevisioncount' => "This article has been ''revised'' since you last reviewed it. You may wish to re-rate it.",

	# FIXME: Invalid links
	'articleassessment-results-show' => "(Results hidden. [[|Show]] them.)",
	'articleassessment-results-hide' => "([[|Hide Results]])",
);

/** Message documentation (Message documentation)
 * @author Sam Reed
 */
$messages['qqq'] = array(
	'articleassessment' => 'The title of the feature. It is about reader feedback.',
	'articleassessment-desc' => 'A description of the purpose of this feature',
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
	'articleassessment-stalemessage-revisioncount' => 'This is a message shown to the user when their ratings are "stale" and includes the number of revisions since.',
	'articleassessment-stalemessage-norevisioncount' => 'This is a message shown to the user when their ratings are "stale" and does NOT include the number of revisions. This is included for completeness, in case revision counts need to be turned off.',
	'articleassessment-results-show' => 'This is an explanatory control that, when clicked, will display hidden aggregate ratings.',
	'articleassessment-results-hide' => 'This is a control that, when clicked, will hide the aggregate ratings.',
);
