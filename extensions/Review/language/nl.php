<?php
/**
 * nld / Dutch language file for the 'Review' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'review_topics' => "
			# The following is the list of topics and ranges for the review extension.
			# Each topic is defined by a line of five values, separated by ':' (all other lines are ignored).
			# The order is: numerical key (>0), name, max range (>1; 2=yes/no), meaning of lowest value (e.g., 'bad'), meaning of highest value (e.g., 'good').
			1:Kwaliteit:5:slecht:goed
			2:Klaar voor 1.0:2:nee:ja",
		'review_topic_page' => "Review onderwerpen",
		'review_sidebar_explanation' => "Het meest linkse vakje betekent altijd 'geen mening'.",
		'review_save' => "Review opslaan",
		'review_your_review' => "Uw review van deze pagina/versie:",
		'review_sidebar_you_have_other_reviews_for_this_article' => "U heeft al een review gedaan voor andere versies van deze pagina. Uw andere waarderingen worden samengevoegd met deze waar u 'geen mening' heeft opgegeven.",
		'review_page_link' => "reviewstatistieken",
		'review_sidebar_final' => "Zie $1 voor reviewgegevens van deze pagina",
		'review_for_page' => "Review voor pagina \"$1\"",
		'review_for_user' => "Review voor gebruiker \"$1\"",
		'review_error' => "Er is iets niet in orde!",
		'review_no_reviews_for_page' => "Er zijn op dit moment geen reviews voor \"$1\".",
		'review_total_statistics' => "Totaal",
		'review_statistics_left_corner' => "Versie",
		'review_version_link' => "Versie #$1",
		'review_statistic_cell' => "Gemiddelde: $1 van $2<br />($4 gebruiker, $5 anomienen)",
		'review_version_statistic_cell' => "$1 van $2",
		'review_version_reviews_link' => "<small>(versiereviews)</small>",
		'review_concerns_page' => "Deze review gaat over de pagina \"$1\".",
		'review_concerns_user' => "Dit gaat over reviews van gebruiker \"$1\".",
		'review_user_reviews' => "<small>(reviews van deze gebruiker)</small>",
		'review_user_page_list' => "De gebruiker heeft een review gedaan op de volgende pagina\'s:",
		'review_do_merge' => "Voeg mijn reviews van andere versies van deze pagina samen met deze",
		'review_has_been_stored' => "<font id='review_has_been_stored'>Uw review is opgeslagen!</font>",
		'revision_review_this_page_version_link' => "Review deze versie van de pagina.",
		'review_page_review' => "Review van pagina \"$1\"",
		'review_blocked' => "U bent geblokkeerd. Ga weg.",
		'review_wrong_namespace' => "Voor pagina\'s in deze naamruimte is review niet mogelijk!",
		'review_topic' => "Onderwerp",
		'review_no_opinion' => "Geen mening",
		'review_rating' => "Waardering",
		'review_comment' => "Opmerking",
	)
);