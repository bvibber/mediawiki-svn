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
			1:Quality:5:bad:good
			2:Ready for 1.0:2:no:yes",
		'review_topic_page' => "Review topics",
		'review_sidebar_explanation' => "The leftmost checkbox always means 'no opinion'.",
		'review_save' => "Store review",
		'review_your_review' => "Your review of this page/revision:",
		'review_sidebar_you_have_other_reviews_for_this_article' => "You have already reviewed other versions of this article. Your other ratings will be merged into this one where you selected 'no opinion'.",
		'review_page_link' => "review statistics",
		'review_sidebar_final' => "For review data of this page view, see its $1",
	)
);
?>
