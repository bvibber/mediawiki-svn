<?php
# Copyright (C) 2004 Ryan Lane <rlane32@gmail.com>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
# http://www.gnu.org/copyleft/gpl.html

# SmoothGallery extension. Creates galleries of images that are in your wiki.
#
# SmoothGallery.php
#
# Extension info available at http://www.mediawiki.org/wiki/Extension:SmoothGallery
# SmoothGallery available at http://smoothgallery.jondesign.net/
#
# Version 1.0h / 2007-02-03
#

if( !defined( 'MEDIAWIKI' ) )
        die( -1 );

$wgExtensionFunctions[] = "wfSmoothGallery";
$wgExtensionFunctions[] = "wfSetupSpecialSmoothGallery";

$wgHooks['BeforePageDisplay'][] = 'addSmoothGalleryJavascriptAndCSS';

//sane defaults. always initialize to avoid register_globals vulnerabilities
$wgSmoothGalleryDelimiter = "\n";
$wgSmoothGalleryExtensionPath = $wgScriptPath . '/extensions/SmoothGallery'; 

function wfSmoothGallery() {
        global $wgParser;

        $wgParser->setHook( 'sgallery', 'renderSmoothGallery' );
}

function wfSetupSpecialSmoothGallery() {
        global $IP;
        global $wgMessageCache;

        require_once($IP . '/includes/SpecialPage.php');

        SpecialPage::addPage(new SpecialPage('SmoothGallery', '', false));
        $wgMessageCache->addMessage('smoothgallery', 'Special:SmoothGallery');
}

function wfSpecialSmoothGallery() {
        global $wgRequest;

        $gallery = new SmoothGallery( $wgRequest );
        $gallery->execute();
}

class SmoothGallery {
        /**#@+
         * @access private
         */
        var $mOptionArray, $mInput;

        function SmoothGallery( &$request ) {
                global $wgSmoothGalleryDelimiter;

                //This is a dirty, dirty hack that should be replaced. It works, and
                //it is safe, but there *MUST* be a better way to do this...
                if ( !isset($wgSmoothGalleryDelimiter) ) {
                        $wgSmoothGalleryDelimiter = "\n";
                }
                $this->mInput = $request->getVal( 'input' );
                $this->mInput = str_replace( array( ":::" ), array( "$wgSmoothGalleryDelimiter" ), $this->mInput );

                $this->mOptionArray['height'] = $request->getVal( 'height' );
                $this->mOptionArray['width'] = $request->getVal( 'width' );
                $this->mOptionArray['showcarousel'] = $request->getVal( 'showcarousel' );
                $this->mOptionArray['timed'] = $request->getVal( 'timed' );
                $this->mOptionArray['delay'] = $request->getVal( 'delay' );
                $this->mOptionArray['showarrows'] = $request->getVal( 'showarrows' );
                $this->mOptionArray['showinfopane'] = $request->getVal( 'showinfopane' );

                //The extension expects true/false and not 1/0
                $boollist = array("showcarousel", "timed", "delay", "showarrows", "showinfopane");
                foreach ( $boollist as $bool ) {
                        if ( $this->mOptionArray[$bool] == "1" ) {
                                $this->mOptionArray[$bool] = "true";
                        } else {
                                $this->mOptionArray[$bool] = "false";
                        }
                }
        }

        function execute() {
                global $wgOut;
                global $wgTitle;
                global $wgParser;

                //We need a parser to pass to the render function, this
                //seems kinda dirty, but it works on MediaWiki 1.6-1.9...
                $local_parser = clone $wgParser;
                $local_parser->mOptions = new ParserOptions();
                $local_parser->Title( $wgTitle );
                $local_parser->clearState();

                $wgOut->addHTML( renderSmoothGallery( $this->mInput, $this->mOptionArray, $local_parser ) );
        }
}

function smoothGalleryImagesByCat( $title ) {
	global $wgContLang;

	$name = $title->getDBKey();

	$dbr = wfGetDB( DB_SLAVE );

	list( $page, $categorylinks ) = $dbr->tableNamesN( 'page', 'categorylinks' );
	$sql = "SELECT page_namespace, page_title FROM $page " .
		"JOIN $categorylinks ON cl_from = page_id " .
		"WHERE cl_to = " . $dbr->addQuotes( $name ) . " " .
		"AND page_namespace = " . NS_IMAGE . " " .
		"ORDER BY cl_sortkey";

	$images = array();
	$res = $dbr->query( $sql, 'smoothGalleryImagesByCat' );
	while ( $row = $dbr->fetchObject( $res ) ) {
		$img = Title::makeTitle( $row->page_namespace, $row->page_title );

		$images[] = $img;
	}
	$dbr->freeResult($res);

	return $images;
}

/**
 * @todo Internationalize
 */
function renderSmoothGallery( $input, $argv, &$parser ) {
        global $wgContLang;
        global $wgSmoothGalleryDelimiter;

        if ( !isset($wgSmoothGalleryDelimiter) ) {
                $wgSmoothGalleryDelimiter = "\n";
        }

        //Sanity check
        if ( $input == "" ) {
                $output = "<p><b>SGallery error:</b> no images were added into the gallery. Please add at least one image.</p>";
                return $output;
        }

        //Give this gallery a random name so that we can have more than one gallery
        //on a page.
        $name = "myGallery" . mt_rand();

        //Parse arguments, set defaults, and do sanity checks
        if ( isset( $argv["height"] ) && is_numeric( $argv["height"] ) ) {
                if ( isset( $argv["special"] ) ) {
                        //Creating a link instead, the special page is going to call this
                        //function again, so "px" will be appended.
                        $height = $argv["height"];
                } else {
                        $height = $argv["height"] . "px";
                }
        } else {
                $height = "300px";
        }

        if ( isset( $argv["width"] ) && is_numeric( $argv["width"] ) ) {
                if ( isset( $argv["special"] ) ) {
                        //Creating a link instead, the special page is going to call this
                        //function again, so "px" will be appended.
                        $width = $argv["width"];
                } else {
                        $width = $argv["width"] . "px";
                }
        } else {
                $width = "400px";
        }

        if ( isset( $argv["showcarousel"] ) && $argv["showcarousel"] == "false" ) {
                $carousel = false;
        } else {
                $carousel = true;
        }

        if ( isset( $argv["timed"] ) && $argv["timed"] == "true" ) {
                $timed = true;
        } else {
                $timed = false;
        }

        if ( isset( $argv["delay"] ) && is_numeric($argv["delay"]) ) {
                $delay = $argv["delay"];
        } else {
                $delay = "9000";
        }

        if ( isset( $argv["showarrows"] ) && $argv["showarrows"] == "false" ) {
                $showarrows = false;
        } else {
                $showarrows = true;
        }

        if ( isset( $argv["showinfopane"] ) && $argv["showinfopane"] == "false" ) {
                $showinfopane = false;
        } else {
                $showinfopane = true;
        }

        if ( isset( $argv["special"] ) ) {
                //The user wants a link to a special page instead. Let's provide a link with
                //the relevant info

                //sanity check
                $name = htmlspecialchars( $argv["special"] );

                //This is a dirty, dirty hack that should be replaced. It works, and
                //it is safe, but there *MUST* be a better way to do this...
                $input = str_replace( array( "$wgSmoothGalleryDelimiter" ), array( ":::"), $input );

                //Get a local link from the special page
                $sp = Title::newFromText( "Special:SmoothGallery" );
                $output = $sp->getLocalURL( "height=" . $height . "&width=" . $width . "&showcarousel=" . $carousel . "&timed=" .
                        $timed . "&delay=" . $delay . "&showarrows=" . $showarrows . "&showinfopane=" . $showinfopane . "&input=" . htmlspecialchars( $input ) );

                //Provide the link
                return '<a href="' . $output . '">' . $name . '</a>';
        }

        //Open the outer div of the gallery
        $output = '<div id="' . $name . '" class="myGallery" style="width: ' . $width . ';height: ' . $height . '; display:none;">';

        //Expand templates in the input
        $input = $parser->replaceVariables( $input );

        //The image array is a delimited list of images (strings)
        $img_arr = explode( $wgSmoothGalleryDelimiter, $input );
        $img_count = count( $img_arr );

        $title_arr = array();
        foreach ( $img_arr as $img ) {
		$title = Title::newFromText( $img, NS_IMAGE );

		if ( is_null($title) ) {
			continue;
		}

		$ns = $title->getNamespace();
		
		if ( $ns == NS_IMAGE ) $title_arr[] = $title;
		else if ( $ns == NS_CATEGORY ) {
			//list images in category
			$cat_images = smoothGalleryImagesByCat( $title );
			if ( $cat_images ) {
				$title_arr = array_merge( $title_arr, $cat_images );
			}
		}
        }

        //Initialize a string for images we can't find, so that we
        //can report them later
        $missing_img = "";

        $plain_gallery = new ImageGallery();

        foreach ( $title_arr as $title ) {
		$img = $title->getText();

                //Get the image object from the database
                #$img_obj = Image::newFromName( $wgContLang->ucfirst($img) );
		$img_obj = new Image( $title );

                //Image wasn't found. No point in going any further.
                if ( is_null($img_obj) ) {
                        continue;
                }

                //check media type. Only images are supported
		$mtype = $img_obj->getMediaType();
                if ( $mtype != MEDIATYPE_DRAWING && $mtype != MEDIATYPE_BITMAP ) {
                        continue;
                }

                //Create a thumbnail the same size as our gallery so that
                //full images fit correctly
                $full_thumb_obj = $img_obj->getThumbnail( $width, $height );
                $full_thumb = $full_thumb_obj->getUrl();

                if ( $full_thumb == "" ) {
                        //The thumbnail we requested was larger than the image;
                        //we need to just provide the image
                        $full_thumb = $img_obj->getUrl();
                }

                if ( $carousel ) {
                        //We are going to show a carousel to the user; we need
                        //to make icon thumbnails
                        $icon_thumb = $img_obj->createThumb( "100" );
                        if ( $icon_thumb == "" ) {
                                //The thumbnail we requested was larger than the image;
                                //we need to just provide the image
                                $icon_thumb = $img_obj->getUrl();
                        }
                }

                //Load the image page from the database with the provided title from
                //the image object
                $db = wfGetDB( DB_SLAVE );
                $img_rev = Revision::loadFromTitle( $db, $title );

                if ( $img_rev == null ) {
                        //The user asked for an image that doesn't exist, let's
                        //add this to the list of missing objects and not output
                        //any html
                        $img_count = $img_count - 1;
                        $missing_img .= " " . htmlspecialchars($img);

                        continue;
                }

                //Get the text from the image page's description
                $fulldesc = $img_rev->getText();

                //Add the html for the image
                $output .= '<div class="imageElement">';
                $output .= '<h3>' . $img_obj->getName() . '</h3>';
                $output .= '<p>' . $fulldesc . '</p>';
                $output .=  '<a href="' . $title->getFullURL() . '" title="open image" class="open"></a>';
                $output .=  '<a href="' . $img_obj->getViewURL() . '" title="open image" class="open"></a>';
                $output .=  '<img src="' . $full_thumb . '"  class="full" />';

                if ( $carousel ) {
                        $output .=  '<img src="' . $icon_thumb . '"  class="thumbnail" />';
                }

                $output .= '</div>';

                $plain_gallery->add( $img_obj ); //TODO: use text
        }

        //Make sure we have something to output
        if ( $img_count == 0 ) {
                //The user requested images, but none of the ones requested
                //actually exist, let's inform the user
                $output = "<p><b>SGallery error:</b> ";

                //Sanity check
                if ( $missing_img != "" ) {
                        $output .= "No images were included in this gallery. Make sure all images requested exist. The following images were not found: $missing_img";
                } else {
                        $output .= "There was an unexpected error. Please file a bug report.";
                }

                $output .= "</p>";

                return $output;
        }

        //Close the outer div of the gallery
        $output .= '</div>';

        //Wrapper div for plain old gallery, to be shown per default, if JS is off.
        $output .= '<div id="' . $name . '-plain">';

        $output .= $plain_gallery->toHTML();

        //Close the wrappe div for the plain old gallery
        $output .= '</div>';

        //Output the javascript needed for the gallery with any
        //options the user requested
        $output .= '<script type="text/javascript">';

        $output .= 'document.getElementById("' . $name . '-plain").style.display = "none";'; //hide plain gallery
        $output .= 'document.getElementById("' . $name . '").style.display = "block";'; //show smooth gallery

        $output .= 'function startGallery_' . $name . '() {';
        $output .= "var myGallery = new gallery($('" . $name . "'), {";

        //A boolean to tell whether or not we need a comma before
        //the next element of the list
        $previousoption = false;

        //Add user provided options
        if ( $timed ) {
                $output .= 'timed: true,';
                $output .= 'delay: ' . $delay;
                $previousoption = true;
        }

        if ( !$carousel ) {
                if ( $previousoption ) {
                        $output .= ',showCarousel: false';
                } else {
                        $output .= 'showCarousel: false';
                }
                $previousoption = true;
        }

        if ( !$showarrows ) {
                if ( $previousoption ) {
                        $output .= ',showArrows: false';
                } else {
                        $output .= 'showArrows: false';
                }
                $previousoption = true;
        }

        if ( !$showinfopane ) {
                if ( $previousoption ) {
                        $output .= ',showInfopane: false';
                } else {
                        $output .= 'showInfopane: false';
                }
        }

        $output .= '});';
        $output .= '}';
        $output .= 'addOnloadHook(startGallery_' . $name . ');';
        $output .= '</script>';

        //Finished, let's send it out
        return $output;
}

function addSmoothGalleryJavascriptAndCSS( &$m_pageObj ) {
        global $wgSmoothGalleryExtensionPath;

        $extensionpath = $wgSmoothGalleryExtensionPath;

        //Add mootools (required by SmoothGallery)
        //You can use the compressed js if you want, but I
        //generally don't trust them unless I wrote them myself
        $m_pageObj->addScript( '<script src="' . $extensionpath . '/scripts/mootools.uncompressed.js" type="text/javascript"></script>' );

        //Add SmoothGallery javascript and CSS (I should probably
        //be using addLink for the CSS...)
        $m_pageObj->addScript( '<script src="' . $extensionpath . '/scripts/jd.gallery.js" type="text/javascript"></script>' );
        $m_pageObj->addScript( '<link rel="stylesheet" href="' . $extensionpath . '/css/jd.gallery.css" type="text/css" media="screen" charset="utf-8" />' );

        return true;
}

/**
 * Add extension information to Special:Version
 */
$wgExtensionCredits['other'][] = array(
        'name' => 'SmoothGallery parser extension',
        'version' => '1.0h',
        'author' => 'Ryan Lane',
        'description' => 'Allows users to create galleries with images that have been uploaded. Allows most options of SmoothGallery',
        'url' => 'http://www.mediawiki.org/wiki/Extension:SmoothGallery'
        );
?>
