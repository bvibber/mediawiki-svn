<?php
/*
 * hawpedia about page
 * $Date$
 */

require_once('config.php');
require_once('hawpedia.php');
require_once('hawhaw/hawhaw.inc');

start_hawpedia_session(); // set session params

require('lang/' . $_SESSION['language'] . '/about_text.php');

$deck = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
set_deck_properties($deck);


$emptyLine = new HAW_text("");
$wp_image = new HAW_image(HAWPEDIA_ICON . ".wbmp",
                HAWPEDIA_ICON . ".gif", "");
$deck->add_image($wp_image);

$deck->add_text($emptyLine);
$emptyLine = new HAW_text("");
  
$title = new HAW_text($about['title'], HAW_TEXTFORMAT_BOLD);
$deck->add_text($title);
$deck->add_text($emptyLine);

$text = new HAW_text($about['text']);
$deck->add_text($text);
$deck->add_text($emptyLine);

$imp1 = new HAW_text($about['imp1']);
$deck->add_text($imp1);
$deck->add_text($emptyLine);

$imp2 = new HAW_text($about['imp2'], HAW_TEXTFORMAT_SMALL);
$deck->add_text($imp2);
$imp3 = new HAW_text($about['imp3'], HAW_TEXTFORMAT_SMALL);
$deck->add_text($imp3);
$imp4 = new HAW_text($about['imp4'], HAW_TEXTFORMAT_SMALL);
$deck->add_text($imp4);
$imp5 = new HAW_text($about['imp5'], HAW_TEXTFORMAT_SMALL);
$deck->add_text($imp5);
$deck->add_text($emptyLine);

$wpLink = new HAW_link($about['wpLinkLabel'], $about['wpLinkUrl']);
$deck->add_link($wpLink);

$backLink = new HAW_link(hawtra("Back"), "index.php");
$deck->add_link($backLink);

$deck->create_page();

?>
