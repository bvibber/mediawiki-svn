<?php

$wgExtensionCredits['other'][] = array(
	'name'        => 'Icon',
	'version'     => '1.0',
	'author'      => 'Tim Laqua',
	'description' => 'Allows you to use Images as Icons and Icon Links',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:Icon',
);

$wgExtensionFunctions[] = 'efIcon_Setup';
$wgHooks['LanguageGetMagic'][]       = 'efIcon_LanguageGetMagic';

function efIcon_Setup() {
        global $wgParser;
        # Set a function hook associating the "example" magic word with our function
        $wgParser->setFunctionHook( 'icon', 'efIcon_Render' );
		return true;
}

function efIcon_LanguageGetMagic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['icon'] = array( 0, 'icon' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}

function efIcon_Render(&$parser, $img, $alt=null, $width=null, $page=null) {
	$ititle = Title::newFromText( $img );

	// this really shouldn't happen... not much we can do here.
	if (!is_object($ititle))
		return '';

	// check if we are dealing with an InterWiki link
	if ( $ititle->isLocal() ) {
		$image = Image::newFromName( $img );
		if (!$image->exists())
			return '[[Image:'.$img.']]';

		$iURL = $image->getURL();
	} else {
		$iURL = $ititle->getFullURL();
	}

	// Optional parameters
	if (empty($alt))
		$alt='';
	
	if (!empty($width))	{
		$width  = intval($width);
		if ($width > 0) {
			$thumb = $image->transform( array( 'width' => $width ) );
			if ( $thumb->isError() ) {
				$imageString = 'Bad Image';
			} else {
				$imageString = $thumb->toHtml( array( 'alt' => $alt, 'title' => $alt ) );
			}
		} else {
			$imageString = 'Bad Width';
		}
	} else {
		$imageString = "<img src='${iURL}' alt=\"{$alt}\" title=\"{$alt}\" />";
	}

	if (!empty($page)) {
		$ptitle = Title::newFromText( $page );

		// this might happen in templates...
		if (!is_object( $ptitle )) {
			//May be too assuming... w/e.
			$output = $imageString;
		} else {
			if ( $ptitle->isLocal() )
			{
				$tURL = $ptitle->getLocalUrl();
				$aClass='';
			}
			else
			{
				$tURL = $ptitle->getFullURL();
				$aClass = 'class="extiw"';
			}
			$output = "<a ".$aClass." href='${tURL}'>{$imageString}</a>";
		}
	} else {
		$output = $imageString;
	}

	return array($output, 'noparse' => true, 'isHTML' => true);
}
