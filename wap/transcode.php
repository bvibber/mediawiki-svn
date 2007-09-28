<?php
/*
 * transcode wikipedia content for hawpedia
 * $Date$
 */

require_once('hawpedia.php');
require_once('hawiki/hawiki_parser_hawpedia.inc');
require_once('hawiki/hawiki.inc');

start_hawpedia_session(); // set session params

require('lang/' . $_SESSION['language'] . '/phonenumbers.php');

// pediaphon support is optional for a given language
@include('lang/' . $_SESSION['language'] . '/pediaphon_data.php');

// get wikipedia article via export interface
$export_result = export_wikipedia($_GET['go']);

if (!$export_result) {
	
	// no article was matching exactly => search related articles

  $articles = search_articles($_GET['go']);
  
  if (!$articles)
    hawpedia_error(hawtra("No wikipedia article found for:") . " " . $_GET['go']);

  // show deck with article links    
  show_related_articles($articles, $_GET['go']);
    
  exit;
}
  
$title = $export_result['title'];
$wikitext = $export_result['wikitext'];

if (preg_match("/^#REDIRECT:?\s*\[\[(.*)\]\]/i", $wikitext, $matches))
{
	// perform redirection (only once - no endless loops!!!)
	$_GET['go'] = $matches[1];
  $export_result = export_wikipedia($_GET['go']);
  $title = $export_result['title'];
  $wikitext = $export_result['wikitext'];
}

// remove all templates
$wikitext = remove_section($wikitext, "{{", "}}");

// remove all tables
$wikitext = remove_section($wikitext, "{|", "|}");

// remove all references
$wikitext = remove_section($wikitext, "&lt;ref&gt;", "&lt;/ref&gt;");

// remove image galleries
$wikitext = remove_section($wikitext, "&lt;gallery&gt;", "&lt;/gallery&gt;");

// remove all comments
$wikitext = remove_section($wikitext, "<!--", "-->");
$wikitext = remove_section($wikitext, "&lt;!--", "--&gt;");

// remove wikipedia controls
$wikitext = remove_controls($wikitext);

// replace html entities
$wikitext = str_replace("&amp;", "&", $wikitext);
// throws warning PHP4: http://bugs.php.net/bug.php?id=25670
$wikitext = @html_entity_decode($wikitext, ENT_QUOTES, 'UTF-8');

// replace some control codes
$wikitext = str_replace("\vec", "", $wikitext);

// remove leading newlines and whitespace
$wikitext = preg_replace('/^\s*/','', $wikitext);

if (isset($_GET['mode']) && ($_GET['mode'] == "content"))
{
  // show page content only
  preg_match_all("/\n==([^=]+)/", $wikitext, $matches);
  
  $contentDeck = new HAW_deck(hawtra("Contents"));
  set_deck_properties($contentDeck);

  $titlebar = new HAW_text($title, HAW_TEXTFORMAT_BIG | HAW_TEXTFORMAT_BOXED);
  $contentDeck->add_text($titlebar);

	if ($contentDeck->ml == HAW_VXML) {
		$instruction = new HAW_text(hawtra("please press"));
    $contentDeck->add_text($instruction);
  }

  for ($i=0; $i < count($matches[1]); $i++) {

    $chapter = trim($matches[1][$i]);
    
  	$link = new HAW_link(trim($matches[1][$i]),
  	                     $_SERVER['SCRIPT_NAME'] . '?go=' .
  	                     $_GET['go'] . "&chapter=" . urlencode($chapter));
  	
  	$link->set_voice_dtmf($i+1); // enable dtmf control
  	$link->set_voice_input("");  // disable voice input (new in hawhaw.inc V5.13)
  	$link->set_voice_text(($i+1) . " " . hawtra("for") . " " . $chapter);
  	
  	$contentDeck->add_link($link);  
  }

  $contentDeck->create_page();
}
else
{
	// not content mode ...
	
	// create dummy deck for markup distiction
	$dummyDeck = new HAW_deck();
	
	// extract chapter
	if (isset($_GET['chapter']))
	  $wikitext = extract_chapter($wikitext, $_GET['chapter']);
	  
	// VoiceXML treatment
	if ($dummyDeck->ml == HAW_VXML) {
	  $wikitext = links2text($wikitext); // remove all links for VoiceXML
    $wikitext = hawpptra("preamble") . "\n"
                . $wikitext
                . hawpptra("credits") . "\n";
	}
	
	// determine maximum segment length from browser type
	if ($dummyDeck->ml == HAW_WML)
	  $segLength = SEGLENGTH_WML;
	else if ($dummyDeck->ml == HAW_HDML)
	  $segLength = SEGLENGTH_HDML;
	else if ($dummyDeck->ml == HAW_VXML)
	  $segLength = SEGLENGTH_VXML;
	else $segLength = SEGLENGTH_HTML;
	
	// split wikitext into appropriate segments
	$segments = split_wikitext($wikitext, $segLength);
	
	if (isset($_GET['seg'])) {
	  $segNumber = $_GET['seg'];
	  if ($segNumber > count($segments))
	    $segNumber = 1; // can happen in case of voice access after wap access
	}
	else
	  $segNumber = 1;
	
	$textSegment = $segments[$segNumber - 1];
	
	if (count($segments) > 1)
	  // add segment info to wikitext
	  $textSegment .= "\n----\n" . $segNumber . "/" . count($segments);

	$wikibase = $_SERVER['SCRIPT_NAME'] . '?go=';
	$wikipage = new HAWIKI_page($textSegment, $wikibase, $title, $_SESSION['language']);
	
	if ($dummyDeck->ml != HAW_VXML) {
		
		// no navigation links for VoiceXML
		
		if (isset($_GET['chapter']))
		  $chapter = "&chapter=" . $_GET['chapter'];
		else
		  $chapter = "";
		
		if ($segNumber < count($segments)) {
		  // set forward navigation link
		  $wikipage->set_navlink(hawtra("Continue") . " ...",
		               $wikibase . urlencode($_GET['go']) . "&seg=" . ($segNumber + 1) . $chapter,
		               HAWIKI_NAVLINK_BOTTOM);
		}
		
		if ($segNumber > 1) {
		  // set backward navigation link
		  $wikipage->set_navlink(hawtra("Back"),
		               $wikibase . urlencode($_GET['go']) . "&seg=" . ($segNumber - 1) . $chapter,
		               HAWIKI_NAVLINK_BOTTOM);
		}
			
		// set content link
	  $wikipage->set_navlink(hawtra("Contents"), $wikibase . urlencode($_GET['go']) . "&mode=content",
	                         HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
		
		// set copyright link
	  $wikipage->set_navlink(hawtra("Copyright"), "copyright.php?article=" . urlencode($_GET['go']),
	                         HAWIKI_NAVLINK_BOTTOM);
		
		// set link to home
	  $wikipage->set_navlink(hawtra("Home"), "index.php", HAWIKI_NAVLINK_BOTTOM);
		
		// set phone link
		//if (defined('PHONENUMBER_BROWSER') && ($dummyDeck->ml == HAW_HTML) && $dummyDeck->pureHTML)
		//  $phone_number = PHONENUMBER_BROWSER; // try Skype or something on web browsers
		//else
		//  $phone_number = PHONENUMBER_PSTN; // PSTN
			  
	  //$wikipage->set_phonelink(hawtra("Call"), $phone_number,
	  //                         HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);

    if (isset($_SESSION['tel']))
	  $wikipage->set_phonelink(hawtra("Call"), $phonenumbers[$_SESSION['tel']],
	                           HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
	                           
		// set pediaphon MP3 link (if supported for given language)
    if (isset($pediaphon)) {
   	  $wikipage->set_navlink($pediaphon['label'], "pediaphon.php?article=" . urlencode($_GET['go']),
   	                         HAWIKI_NAVLINK_BOTTOM);
    }
	}
	
	$wikipage->display();
	
	// save url for possible voice browser access
	save_url();
}
	  
?>
