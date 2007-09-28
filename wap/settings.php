<?php
/*
 * hawpedia settings page
 * $Date$
 */

require_once('config.php');
require_once('hawpedia.php');
require_once('hawhaw/hawhaw.inc');

start_hawpedia_session(); // set session params

require('lang/' . $_SESSION['language'] . '/phonenumbers.php');

if (isset($_GET['save'])) {
	// store validated submitted data in session
	if (isset($_GET['lang']) && validate_language($_GET['lang'])) {
	  $_SESSION['language'] = $_GET['lang'];

    // unset language-specific session variables
    unset($_SESSION['tel']);
  }
  
	if (isset($_GET['tel']))
	  $_SESSION['tel'] = $_GET['tel'];
}

$deck = new HAW_deck(HAWIKI_TITLE, HAW_ALIGN_CENTER);
set_deck_properties($deck);

$emptyLine = new HAW_text("");
$wp_image = new HAW_image(HAWPEDIA_ICON . ".wbmp",
		HAWPEDIA_ICON . ".gif", "");
$deck->add_image($wp_image);

$deck->add_text($emptyLine);

$title = new HAW_text(hawtra("Settings"), HAW_TEXTFORMAT_BIG | HAW_TEXTFORMAT_BOLD);
$deck->add_text($title);

$deck->add_text($emptyLine);

if (!isset($_GET['mode'])) {
	// show setting menu
  $languageLink = new HAW_link(hawtra("Language"), $_SERVER['PHP_SELF'] . "?mode=lang");
  $deck->add_link($languageLink);

	// show spoken wikipedia menu
  $spowiLink = new HAW_link(hawtra("Spoken Wikipedia"), $_SERVER['PHP_SELF'] . "?mode=tel");
  $deck->add_link($spowiLink);
}
else if ($_GET['mode'] == 'lang') {
	// select language
	
	global $supportedLanguages;
	
  $lang_code = Array(
    "bar" => "Boarisch",
    "cs" => "Česky",
    "de" => "Deutsch",
    "el" => "Ελληνικά", 
    "en" => "English",
    "es" => "Español",
    "fr" => "Français",
    "hu" => "Magyar",
    "ksh" => "Ripoarisch",
    "nds" => "Plattdüütsch",
    "nl" => "Nederlands",
    "pt" => "Português",
    "sr" => "Српски / Srpski",
  );
  
  $subtitle = new HAW_text(hawtra("Language"), HAW_TEXTFORMAT_BOLD);
  $deck->add_text($subtitle);

  $form = new HAW_form($_SERVER['PHP_SELF']);
  $selection = new HAW_select("lang");
  
  while (list($key, $val) = each($supportedLanguages)) {
    if ($val == 1) {
      // language is marked as supported

      if ($key == $_SESSION['language'])
        $selected = HAW_SELECTED; // show current language first
      else
        $selected = HAW_NOTSELECTED;
        
      $selection->add_option($lang_code[$key], $key, $selected);
    }
  }

  $saveMode = new HAW_hidden("save", "yes");
  $submission = new HAW_submit(hawtra("Save"));

  $form->add_select($selection);
  $form->add_hidden($saveMode);
  $form->add_submit($submission);

  $session = new HAW_hidden(session_name() , session_id());
  $form->add_hidden($session);

  $deck->add_form($form);
}
else if ($_GET['mode'] == 'tel') {
	// select phone number for spoken wikipedia

  $subtitle = new HAW_text(hawtra("Spoken Wikipedia"), HAW_TEXTFORMAT_BOLD);
  $deck->add_text($subtitle);

  if (count($phonenumbers) == 0) {
  	// there are no phone numbers available for the given language
  	$text = new HAW_text(hawtra("Not available"));
  	$deck->add_text($text);
  	
  	unset($_SESSION['tel']);  
  }
  else {
  	// there are phone numbers - let user decide which one to use
  	
    $form = new HAW_form($_SERVER['PHP_SELF']);
    $selection = new HAW_select("tel");

    while (list($key, $val) = each($phonenumbers)) {
    	if ($_SESSION['tel'] == $key)
    	  $isSelected = true;
    	else
    	  $isSelected = false;
    	  
      $selection->add_option($val . " (" . $key . ")", $key, $isSelected);
    }

    $saveMode = new HAW_hidden("save", "yes");
    $submission = new HAW_submit(hawtra("Save"));

    $form->add_select($selection);
    $form->add_hidden($saveMode);
    $form->add_submit($submission);

    $session = new HAW_hidden(session_name() , session_id());
    $form->add_hidden($session);

    $deck->add_form($form);
  }
}

$deck->add_text($emptyLine);

if (isset($_GET['mode']))
  $backlink = new HAW_link(hawtra("Back"), $_SERVER['PHP_SELF']);
else
  $backlink = new HAW_link(hawtra("Back"), "index.php");

$deck->add_link($backlink);

$deck->create_page();

?>
