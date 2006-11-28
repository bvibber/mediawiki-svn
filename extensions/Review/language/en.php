<?php
/**
 * English language file for the 'Review' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'review_sidebar_title' => "Review",
		'review_topics' => "
			# The following is the list of topics and ranges for the review extension.
			# Each topic is defined by a line of five values, separated by ':' (all other lines are ignored).
			# The order is: numerical key (>0), name, max range (>1; 2=yes/no), meaning of lowest value (e.g., 'bad'), meaning of highest value (e.g., 'good').
			1:Quality:5:bad:good
			2:Ready for 1.0:2:no:yes",
		'review_topic_page' => "Review topics",
		'review_sidebar_explanation' => "The leftmost checkbox always means 'no opinion'.",
		'review_save' => "Store review",
		'review_your_review' => "Your review of this page/revision:",
		'review_sidebar_you_have_other_reviews_for_this_article' => "You have already reviewed other versions of this article. Your other ratings will be merged into this one where you selected 'no opinion'.",
		'review_page_link' => "review statistics",
		'review_sidebar_final' => "For review data of this page view, see its $1",
		'review_for_page' => "Review for page \"$1\"",
		'review_for_user' => "Review for user \"$1\"",
		'review_error' => "Something's wrong!",
		'review_no_reviews_for_page' => "There are currently no reviews for \"$1\".",
		'review_total_statistics' => "Total",
		'review_statistics_left_corner' => "Revision",
		'review_version_link' => "Revision #$1",
		'review_statistic_cell' => "Average: $1 of $2<br />($4 users, $5 anons)",
		'review_version_statistic_cell' => "$1 of $2",
		'review_version_reviews_link' => "<small>(version reviews)</small>",
		'review_concerns_page' => "This review is about the page \"$1\".",
		'review_concerns_user' => "This is about reviews by user \"$1\".",
		'review_user_reviews' => "<small>(reviews by this user)</small>",
		'review_user_page_list' => "The user reviewed the following pages:",
		'review_user_details_link' => "(details)",
		'review_do_merge' => "Merge my reviews of other revisions of this page into this one",
		'review_has_been_stored' => "<font id='review_has_been_stored'>Your review has been stored!</font>",
		'revision_review_this_page_version_link' => "Review this version of the page.",
		'review_page_review' => "Review of page \"$1\"",
		'review_blocked' => "You're blocked, go away.",
		'review_wrong_namespace' => "Pages in this namespace cannot be reviewed!",
		'review_topic' => "Topic",
		'review_no_opinion' => "No opinion",
		'review_rating' => "Rating",
		'review_comment' => "Comment",
	)
);
?>
