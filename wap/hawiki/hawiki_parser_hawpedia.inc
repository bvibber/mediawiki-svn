<?php
/*
 * hawiki parser for hawpedia
 * $Date$
 */

function namespace_regex1($namespace_name /* english string */ , $separator='/')
{
	$namespace_name = (preg_quote($namespace_name, $separator));
	$first = (substr($namespace_name, 0, 1));
	return('['.strtoupper($first).strtolower($first).']'.substr($namespace_name, 1));
}

function namespace_regex($namespace_name /* english string */ , $separator='/')
{
	//FIXME: could check translation == original, and make a simpler regex.
	return($separator.'^('.namespace_regex1($namespace_name, $separator)
		.'|'.namespace_regex1(translate_wikipedia_keyword($namespace_name), $separator).')'.$separator);
}

function HAWIKI_parser(&$input, $isFirstColumn)
{
	// lexical analyser for wikipedia pages

	$po = array(); // array for parser output

	$po["text"] = ""; // init text output

	if (strlen($input) == 0)
		return(0);  // end of input reached

	if (preg_match("/^([\r\n]+)/", $input, $matches))
	{
		// new line(s)
		$po["count"] = substr_count($matches[1], "\n");
		$input = substr($input, strlen($matches[1]));
		$po["token"] = HAWIKI_TOKEN_NEWLINE;
		return($po);
	}

	if (preg_match("/^('{3,})/", $input, $matches))
	{
		// format bold
		$input = substr($input, strlen($matches[1]));
		$po["token"] = HAWIKI_TOKEN_BOLD;
		return($po);
	}

	if (preg_match("/^''/", $input, $matches))
	{
		// format italic
		$input = substr($input, 2);
		$po["token"] = HAWIKI_TOKEN_ITALIC;
		return($po);
	}

	if (preg_match("/^(==+)/", $input, $matches))
	{
		// heading
		$po["level"] = strlen($matches[1]) - 1;
		$input = substr($input, strlen($matches[1]));
		$po["token"] = HAWIKI_TOKEN_HEADING;
		return($po);
	}

	if ($isFirstColumn && preg_match("/^(\*+)/", $input, $matches))
	{
		// bullet list
		$po["level"] = strlen($matches[1]);
		$input = substr($input, $po["level"]);
		$po["token"] = HAWIKI_TOKEN_BULLIST;
		return($po);
	}

	if ($isFirstColumn && preg_match("/^(#+)/", $input, $matches))
	{
		// numbered list
		$po["level"] = strlen($matches[1]);
		$input = substr($input, $po["level"]);
		$po["token"] = HAWIKI_TOKEN_NUMLIST;
		return($po);
	}

	if ($isFirstColumn && preg_match("/^(----*\r?\n)/", $input, $matches))
	{
		// horizontal rule
		$input = substr($input, strlen($matches[1]));
		$po["token"] = HAWIKI_TOKEN_RULE;
		return($po);
	}

	#if (preg_match("/^\[\[([^\[\]]+)\]\]/", $input, $matches))
	// this MediaWiki regex breaks the parser and creates "invalid wiki syntax" errors!
	#if (preg_match("/^\[\[(([^\[\]]+)|(\[[^\[\]]*\])*|(\[\[[^\[\]]*\]\])*)+\]\]/", $input, $matches))
	// this old HawPedia regex does not work from php 5.2 onward!
	if (preg_match("/^\[\[/", $input))
	{
		// Forced WikiName ((a_page_with_any_name))

		// images can be nested like this: [[Image:...|[[link]]...]]
		//                        or this: [[Image:...|[link]...]]
		
		// find closing brackets ...
		$bracket_level = 2;
		for ($pos = 2; $bracket_level > 0; $pos++) {
      if (substr($input, $pos, 1) == '[')
        $bracket_level++;			

      if (substr($input, $pos, 1) == ']')
        $bracket_level--;			
    }
		
		$wikipage = substr($input, 2, $pos-4);
		$input = substr($input, $pos);
		
		$po["extwiki"] = ""; // hawpedia supports no external wikis

		$link = explode("|", $wikipage); // test for alternative link label
		if (count($link) > 1)
		{
			$po["page"]  = $link[0];
			$po["label"] = $link[1];
		}
		else
		{
			$po["page"]  = $link[0]; // no extra labelling of link required
			$po["label"] = $link[0];
		}

		if (strpos($po["page"], ':') != false)
		{
			// category link

			if (preg_match("/^[Ww]ikipedia:/", $po["page"])) {
				// follow wikipedia category link
				$po["token"] = HAWIKI_TOKEN_WIKINAME;
				return($po);
			}

			if (preg_match("/^[Ww]iktionary:/", $po["page"])) {
				// display wiktionary links as plain text
				$po["text"] = $po["label"];
				$po["token"] = HAWIKI_TOKEN_TEXT;
				return($po);
			}

			// Image Link - added localization of property strings missing (not needed for lang =  en, es)

			if (!defined('DISABLE_IMAGES') || !DISABLE_IMAGES) {
				$temp = namespace_regex('Image:','/');
				if (preg_match($temp, $po['page'])) {
					// isolate image description.
					$temp = substr($wikipage, strlen($link[0]));
					$ltemp = -1;
					while($ltemp != strlen($temp)) {
						$ltemp = strlen($temp);
						$temp = preg_replace(array('/^\|/', '/\|$/',
							'/^(left|right|center|[0-9]+px|thumb)\|/', 
							'/\|(left|right|center|[0-9]+px|thumb)$/',
							'/^(left|right|center|[0-9]+px|thumb)$/'), '', $temp);
					}
					$po['subscript'] = $temp;
					// convert wikilinks to plain text in alt tag.
					$po['alt'] = preg_replace(
						array('/\[\[([^|\]]+\|)([^\]]*)\]\]/', '/\[\[([^|\]]+)\]\]/'),
						array('$2', '$1'), $temp);
					// raw image source
					$imagename = preg_replace('/^[^\\:]+\\:/', '', $link[0]);
					
					// image width
					// - if width given in wiki code, use it,
					//   - else use HAWIKI_DISP_WIDTH.
					// - if width is less than both IMG_MAX_WIDTH and HAWIKI_DISP_WIDTH, use it,
					//   - else use the minimum of IMG_MAX_WIDTH and HAWIKI_DISP_WIDTH.
					// - if requested width is bigger than the image, server will return
					//   us the smaller one anyway.
					$width = 0;
					$temp = substr($wikipage, strlen($link[0]));
					$temp = preg_replace('/\[\[[^\]]*\]\]/', '_', $temp);	// get rid of links
					$temp = explode("|", $temp); // isolate elements
					foreach($temp as $tmp)
					{
						if(preg_match('/^[0-9]+px$/', trim($tmp)))
							$width = 0+preg_replace('/[^0-9]+/', '', $tmp); //keep last.
					}
					if ($width) {
						$width = min($width, IMG_MAX_WIDTH, HAWIKI_DISP_WIDTH);
					} else {
						$width = HAWIKI_DISP_WIDTH;
					}
					
					// We'll pass on the image info to our image.php,
					// which sends to Wikipedia's API to get the actual URL
					// of the appropriately-sized thumbnail or original image,
					// then we convert formats on our end.
					$imageInfo = array(
						"name" => $imagename,
						"language" => $_SESSION["language"],
						"width" => $width );
					$po["src"] = "image.php?" . http_build_query($imageInfo, "", "&");
							
					$po["token"] = HAWIKI_TOKEN_IMAGE;
					return($po);
				}
			}

			// other category links are not supported
			$po["token"] = HAWIKI_TOKEN_DUMMY;
			if (preg_match("/^([\r\n]+)/", $input, $matches))
			{
				// skip whole line
				$input = substr($input, strlen($matches[1]));
				$po["firstColumn"] = true;
			}
			else
				$po["firstColumn"] = false;

			return($po);
		}

		$po["token"] = HAWIKI_TOKEN_WIKINAME;
		return($po);
	}

	if (preg_match("/^(\[(https?:\/\/.*?)\])/", $input, $matches) ||
		preg_match("/^((https?:\/\/.*?)[ \r\n])/", $input, $matches))
		{
			// link
			$input = substr($input, strlen($matches[1]));

			$seperator_pos = strpos($matches[2], ' ');
			if ($seperator_pos == true) // alternative link label available
			{
				$po["url"]   = substr($matches[2], 0, $seperator_pos);
				$po["label"] = substr($matches[2], $seperator_pos + 1);
			}
			else
			{
				$po["url"]   = $matches[2]; // no extra labelling of link required
				$po["label"] = $matches[2];
			}

			$po["ext"] = true;
			$po["extlink_text"] = hawtra("external link");

			$po["token"] = HAWIKI_TOKEN_LINK;
			return($po);
		}

	// plain text - search next syntax element resp. eof
	//if (preg_match("/^(.*?)(~np~|__|''|===|-=|\^|::|~~|\)\)|\(\(|\[|\|\||\{img |\(:|[A-Z][a-z0-9_]+[A-Z][a-z0-9_]+[\|\w]*|[\r\n]|\{[A-Z]+\([^\)]*\)\}|<[aA]|$)/",
	//    $input, $matches))
	if (preg_match("/^(.*?)(''|==|\[\[|\[?https?:\/\/|[\r\n]|$)/",
		$input, $matches))
		{
			// plain text
			$po["text"] = $matches[1];
			$input = substr($input, strlen($po["text"]));

			if (strlen($po["text"]) > 0)
				$po["token"] = HAWIKI_TOKEN_TEXT;
			else {
				//echo "ERROR! input:<br />" . $input;
				return(0); // can happen in case of syntax errors
			}
			return($po);
		}

	return(0);
}

?>
