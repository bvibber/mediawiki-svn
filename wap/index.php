<?php
/*
 * hawpedia main page
 * $Date$
 */

require_once('hawpedia.php');

start_hawpedia_session(); // set session params

$deck = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
set_deck_properties($deck);

determine_settings();

$emptyLine = new HAW_text("");
$wp_image = new HAW_image(HAWPEDIA_ICON . ".wbmp",
                HAWPEDIA_ICON . ".gif", "");
$deck->add_image($wp_image);

$deck->add_text($emptyLine);
		
$title = new HAW_text(HAWIKI_TITLE, HAW_TEXTFORMAT_BIG);
$deck->add_text($title);

$form = new HAW_form("transcode.php"); 
$go = new HAW_input("go", "", "");
$go->set_size(16);
$form->add_input($go);
$session = new HAW_hidden(session_name() , session_id());
$form->add_hidden($session);
$submit_button_label = translate_wikipedia_keyword('Go');
$submit = new HAW_submit($submit_button_label);
$form->add_submit($submit);

$deck->add_form($form);

$settingsLink = new HAW_link(hawtra("Settings"), "settings.php");
$deck->add_link($settingsLink);

$aboutLink = new HAW_link(hawtra("About") . " " . HAWIKI_TITLE,
                          "about.php");
$deck->add_link($aboutLink);

$deck->create_page();

?>
