<?php
/*
 * common functions for hawpedia
 * $Date$
 */

require_once('hawhaw/hawhaw.inc');
require_once('config.php');
require_once('hawiki/hawiki_cfg.inc');

function start_hawpedia_session()
{
	$sessionDeck = new HAW_deck();
	$sessionDeck->enable_session();
	ini_set('session.use_cookies', 0);    // do not use cookies
	ini_set('session.use_trans_sid', 1);  // use transient sid support

	if ($sessionDeck->ml == HAW_HTML) {
		// remove form entry - see http://bugs.php.net/bug.php?id=13472
		ini_set('url_rewriter.tags', 'a=href');
	}

	session_start();

	determine_settings();
}

function set_deck_properties(&$deck)
{
	$deck->set_charset("UTF-8");
	$deck->set_width(HAWIKI_DISP_WIDTH);
	$deck->set_height(HAWIKI_DISP_HEIGHT);
	$deck->set_disp_bgcolor(HAWIKI_DISP_BGCOLOR);
	$deck->use_simulator(HAWIKI_SKIN);
}

function hawpedia_error($error_msg)
{
	$error_deck = new HAW_deck(HAWIKI_TITLE);
	set_deck_properties($error_deck);
	$error_text = new HAW_text($error_msg);
	$error_deck->add_text($error_text);

	$rule = new HAW_rule();
	$error_deck->add_rule($rule);

	$homelink = new HAW_link(hawtra("Home"), "index.php");
	$error_deck->add_link($homelink);

	$error_deck->create_page();
	exit();
}

function validate_language($lang) {
	global $supportedLanguages;
	return is_string($lang) &&
		preg_match('/^[a-z][a-z_]*[a-z]$/', $lang) &&
		isset($supportedLanguages[$lang]) &&
		$supportedLanguages[$lang] == 1;
}

function determine_settings()
{
    // Validate previously set session data
    if (isset($_SESSION['lang']) && !validate_language($_SESSION['lang'])) {
		unset($_SESSION['lang']);
	}

	if (isset($_GET['lang']) &&
		validate_language($_GET['lang'])) {
			// language explicitely requested in url parameter
			$_SESSION['language'] = $_GET['lang']; // overwrite session info
		}
	else if (!isset($_SESSION['language'])) 
	{
		// no language info stored in session
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && 
			validate_language($_SERVER['HTTP_ACCEPT_LANGUAGE']) &&
			(!defined('FORCE_DEFAULT_LANGUAGE') || !FORCE_DEFAULT_LANGUAGE))
			{
				// store browser's preference in session
        if (preg_match('/^\s*([a-zA-Z]+)/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches))
  				$_SESSION['language'] = strtolower($matches[1]);
        else
    			$_SESSION['language'] = DEFAULT_LANGUAGE;
			}
		elseif(isset($_SERVER['HTTP_HOST']) &&
			($dot = strpos($_SERVER['HTTP_HOST'], '.')) &&
			($domlang = substr($_SERVER['HTTP_HOST'], 0, $dot)) &&
			validate_language($domlang) &&
			(defined('FORCE_DEFAULT_LANGUAGE') && ('subdomain'==FORCE_DEFAULT_LANGUAGE)))
			{
				// store language subdomain in session
				$_SESSION['language'] = $domlang;
			}
		else
		{
			// store default language in session
			$_SESSION['language'] = DEFAULT_LANGUAGE;
		}
	}

	if (!validate_language($_SESSION['language'])) {
		$_SESSION['language'] = DEFAULT_LANGUAGE;
	}

	require('lang/' . $_SESSION['language'] . '/phonenumbers.php');

	if (isset($_GET['tel']) &&
		isset($phonenumbers[$_GET['tel']])) {
			// phonenumber explicitely requested in url parameter
			$_SESSION['tel'] = $_GET['tel']; // overwrite session info
		}
	else if (!isset($_SESSION['tel'])) {
		// no telephone number info stored in session
		if (count($phonenumbers) > 0) {
			// store key of 1st entry in session
			$_SESSION['tel'] = array_shift(array_keys($phonenumbers));
		}
		else {
			// deactivate feature
			unset($_SESSION['tel']);
		}
	}
}

function hawtra($text)
{
	// translate given text	

	$translationFile = "lang/" . $_SESSION['language'] . "/translations.php";

	if (!file_exists($translationFile))
		return($text); // no translation possible

	require($translationFile);

	if (isset($translation[$text]))
		return $translation[$text];
	else
		return $text; // no translation available
}

function hawpptra($input)
{
  // translate given phone phrase	

  $phraseFile = "lang/" . $_SESSION['language'] . "/phone_phrases.php";

  if (!file_exists($phraseFile))
    return($input); // no translation possible
     
  require($phraseFile);
  
  if (isset($phrase[$input]))
    return $phrase[$input];
  else
    return $input; // no translation available
}

function translate_wikipedia_keyword($keyword) {

	// translate language-specific wikipedia keyword

	if ($_SESSION['language'] == 'en')
		return $keyword; // no translation needed

	$languageFile = "lang/" . $_SESSION['language'] . "/keywords.php";
	if (!file_exists($languageFile))
		die("file not found: " . $filename);
	require($languageFile);

	if (!isset($keywords[$keyword]))
		die("unknown keyword: " . $keyword);

	return $keywords[$keyword];
}

function export_wikipedia($searchTerm)
{
	$result = array();

	$export_keyword = translate_wikipedia_keyword('Special:Export');

	$searchTerm = str_replace(" ", "_", $searchTerm); // blanks must become underscores

	// get wikipedia xml file
	$ch = curl_init();
	$url = "http://" . $_SESSION['language'] . ".wikipedia.org/wiki/" . $export_keyword . "/" . $searchTerm;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curlResultString = curl_exec($ch);
	if (!is_string($curlResultString))
		hawpedia_error(hawtra("Wikipedia currently not available")); // exits internally

	curl_close($ch);

	// determine page title
	if (!preg_match("%<title>(.*)</title>%", $curlResultString, $matches))
		return false; // search term not found

	$result['title'] = $matches[1];

	// determine wiki text
	if (!preg_match("/(<text [^>]*>)/", $curlResultString, $matches))
		hawpedia_error(hawtra("wikipedia export error")); // exits internally
	$textStart = strpos($curlResultString, $matches[1]) + strlen($matches[1]);
	$textEnd = strpos($curlResultString, "</text>");
	$result['wikitext'] = substr($curlResultString, $textStart, $textEnd - $textStart);

	return $result;
}


function uri_exists($uri)
{
	$result = ('');
	// get http headers for the requested uri
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_NOBODY, TRUE);
	curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	$status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close($ch);
	return $status == 200;
}

function search_articles($article)
{
	// search related articles (after export has failed)

	$result = array();

	$search_keyword = translate_wikipedia_keyword('Special:Search');

	$article = str_replace(" ", "_", $article); // blanks must become underscores

	// get wikipedia search result (in html format)
	$ch = curl_init();
	$url = "http://" . $_SESSION['language'] . ".wikipedia.org/wiki/" . $search_keyword . "?search=" . $article;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$curlResultString = curl_exec($ch);
	if (!is_string($curlResultString))
		hawpedia_error(hawtra("Wikipedia currently not available")); // exits internally

	curl_close($ch);

	// extract article links from html
	preg_match_all("%1em;\"><a href=\"/wiki/([^?\"]*)%", $curlResultString, $matches);

	for ($i=0; $i < count($matches[1]); $i++) {
		// iterate over found articles (no category links!)
		if (!strstr($matches[1][$i], ":"))
			$result[] = $matches[1][$i];

		if (count($result) >= 10)
			break; // consider not more than 10 links 
	}

	if (count($result) == 0)
		return 0; // nothing found
	else
		return $result;
}

function show_related_articles($articles, $searchterm)
{
	$search_deck = new HAW_deck(HAWIKI_TITLE);
	set_deck_properties($search_deck);

	// tell what this deck is about
	$intro = new HAW_text(hawtra("Found articles for:") . " " . $searchterm);
	$search_deck->add_text($intro);

	// separate intro from link section 
	$rule = new HAW_rule();
	$search_deck->add_rule($rule);

	// create one link for each article 
	foreach ($articles as $article) {
		$article_link = new HAW_link(urldecode($article), "transcode.php?go=" . $article);
		$search_deck->add_link($article_link);
	}

	// add home link
	$search_deck->add_rule($rule);
	$homelink = new HAW_link(hawtra("Home"), "index.php");
	$search_deck->add_link($homelink);

	$search_deck->create_page();
	exit();
}

function extract_chapter($wikitext, $chapter)
{
	if (!preg_match("/\n(==+)(\s?" . $chapter . "\s?==+)/", $wikitext, $matches))
		return("invalid chapter"); // should never happen

	$chapterStart = strpos($wikitext, $matches[1] . $matches[2]);	

	// search end of chapter
	$chapterEnd = $chapterStart + strlen($chapter);
	do {
		// number of '=' characters must match exactly
		$chapterEnd = strpos($wikitext, "\n" . $matches[1], $chapterEnd + 1);
	}
	while (($chapterEnd !== false) && (substr($wikitext, $chapterEnd + 1 + strlen($matches[1]), 1) == "="));

	if ($chapterEnd !== false)
		$wikitext = substr($wikitext, $chapterStart, $chapterEnd - $chapterStart);
	else
		$wikitext = substr($wikitext, $chapterStart);

	return($wikitext);
}

function remove_section($wikitext, $startStr, $endStr)
{
	// remove all text within startStr and endStr (incl. limiters)
	// preg_replace can cause problems as described here: http://bugs.php.net/bug.php?id=24460

	while (true) {
		// do for all sections in $wikitext ...

	  $secStart = strpos($wikitext, $startStr);	
	  if ($secStart === false)
	    break;

  	// consider nested limiters
  	$nest_count = 1;
	  $search_pos = $secStart + strlen($startStr);
	  $secEnd = false;
		while (true) {
  	  $start_pos = strpos($wikitext, $startStr, $search_pos);
	    $end_pos   = strpos($wikitext, $endStr,   $search_pos + strlen($startStr));
	    
	    if (($start_pos !== false) && ($start_pos < $end_pos)) {
	    	// nested section found
	    	$search_pos = $start_pos + strlen($startStr);
	    	$nest_count++;
	    	continue;	
    	}

    	if ($end_pos !== false) {
    		$nest_count--;
    		if ($nest_count == 0) {
    			// end of section found
    		  $secEnd = $end_pos;
    		  break;
      	}
      	else {
      	  // continue after end of nested section
      	  $search_pos = $end_pos + strlen($endStr);
      	}
    	}
    	else
    	  break; // invalid section syntax!
  	};

    //remove section
    if (($secStart !== false) && ($secEnd !== false)) {
      $wikitext = substr($wikitext, 0, $secStart) . substr($wikitext, $secEnd + strlen($endStr));
  	}
    else
      break;
	};
	
	return($wikitext);
}

function remove_controls($wikitext)
{
	// remove some mediawiki control elements
	$wikitext = str_replace("__NOTOC__", "", $wikitext);
	$wikitext = str_replace("__FORCETOC__", "", $wikitext);
	$wikitext = str_replace("__TOC__", "", $wikitext);
	$wikitext = str_replace("__NOEDITSECTION__", "", $wikitext);
	$wikitext = str_replace("__NEWSECTIONLINK__", "", $wikitext);
	$wikitext = str_replace("__NOCONTENTCONVERT__", "", $wikitext);
	$wikitext = str_replace("__NOCC__", "", $wikitext);
	$wikitext = str_replace("__NOGALLERY__", "", $wikitext);
	$wikitext = str_replace("__NOTITLECONVERT__", "", $wikitext);
	$wikitext = str_replace("__NOTC__", "", $wikitext);
	$wikitext = str_replace("__END__", "", $wikitext);
	$wikitext = str_replace("__START__", "", $wikitext);

	return($wikitext);	
}

function links2text($wikitext)
{
	// make [[wikilinks]] to wikilinks
	$wikitext = preg_replace('/\[\[([^:\]]*\|)?([^:\]]*)\]\]/','${2}', $wikitext);

	// disable detection of http links
	$wikitext = preg_replace('/http/','h t t p ', $wikitext);
	//$wikitext = preg_replace('@\[?http://\S*(.*?)\]?@','${1}', $wikitext);
	//$wikitext = preg_replace('@\[?http://(\S*)\]?@','${1}', $wikitext);

	//echo $wikitext;
	return($wikitext);	
}

function split_wikitext($wikitext, $segLength)
{
	$result = array(); // init empty array

	while(true) {

		$seg = substr($wikitext, 0, $segLength); // determine maximum segment

		if (strlen($seg) < $segLength) {
			// end of text
			$result[] = $seg; // add last array element
			break;  	
		}

		$crPos = strrpos($seg, "\n"); // find previous new line

		if ($crPos === false) {
			// no newline found in segment, find next new line
			$crPos = strpos($wikitext, "\n", $segLength);
		}

		if (($crPos === false) || ($crPos == 0)) {
			// no newline in whole text
			$crPos = strlen($wikitext); // consider whole text
		}

		$seg = substr($wikitext, 0, $crPos + 1); // determine segment
		$result[] = $seg; // add array element
		$wikitext = substr($wikitext, $crPos + 1); // continue with new line

		if ($crPos == 0)
			exit;
	}

	return $result;
}

function save_url()
{
	if (defined('HAWPEDIA_VXML_TMP_FILE')) {
    // write location parameter to temporary file
	  $fp = fopen(HAWPEDIA_VXML_TMP_FILE, "w");
	  if (!$fp)
	    return; // unsuccessful ...

    fputs($fp, $_SERVER["REQUEST_URI"]);
	  fclose($fp);
  }
}

?>
