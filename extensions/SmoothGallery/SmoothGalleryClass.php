<?php

class SmoothGallery {

	var $parser;
	var $special, $set;
	var $argumentArray, $galleriesArray;
	var $errors;

	function hasErrors() {
		if ( $this->errors == '' ) {
			return false;
		} else {
			return true;
		}
	}

	function getErrors() {
		return $this->errors;
	}

	function checkForErrors() {
		foreach ( $this->galleriesArray["galleries"] as $galleryArray ) {
			//We are being harsh for gallery sets.
			//If even one gallery is missing all images, we
			//are going to return an error to the user.
			if ( !isset( $galleryArray["images"] ) ) {
				wfLoadExtensionMessages( 'SmoothGallery' );
				$error = wfMsg( "smoothgallery-error" );
		
				if ( isset( $galleryArray["missing_images"] )  && isset( $galleryArray["invalid_images"] ) ) {
					$error .= wfMsg( "smoothgallery-no-images", implode( ", " , $galleryArray["missing_images"] ) );
					$error .= wfMsg( "smoothgallery-invalid-images", implode( ", " , $galleryArray["invalid_images"] ) );
				} else if ( isset( $galleryArray["invalid_images"] ) ) {
					$error .= wfMsg( "smoothgallery-invalid-images", implode( ", " , $galleryArray["invalid_images"] ) );
				} else if ( isset( $galleryArray["missing_images"] ) ) {
					$error .= wfMsg( "smoothgallery-no-images", implode( ", " , $galleryArray["missing_images"] ) );
				} else {
					$error .= wfMsg( "smoothgallery-not-found" );
				}
		
				if ( $this->errors == '' ) {
					$this->errors = $error;
				} else {
					$this->errors .= "<br />" . $error;
				}
			}
		}
	
	}

	function setArguments( $argumentArray ) {
		$this->argumentArray = $argumentArray;
	}

	function setGalleries( $galleriesArray ) {
		$this->galleriesArray = $galleriesArray;
	}

	function setParser( &$parser ) {
		$this->parser = $parser;
	}

	function setSpecial( $calledFromSpecial ) {
		$this->special = $calledFromSpecial;
	}

	function setSet( $calledAsSet ) {
		$this->set = $calledAsSet;
	}

	function toHTML () {
		global $wgSmoothGalleryUseDatabase;
	
		$output = '';
		$fallbackOutput = '';
	
		if ( $this->set ) {
			//Open the div, and initialize any needed variables
			$output = '<div id="' . $this->galleriesArray["gallery_set_name"] . '" style="width: ' . $this->argumentArray["width"] . ';height: ' . $this->argumentArray["height"] . '; display: none;" >';
	
			//iterate through galleries, call renderGallery on each, and
			//collect the fallback output
			$i = 1;
			foreach ( $this->galleriesArray["galleries"] as $galleryArray ) {
				$output .= $this->renderGallery( $galleryArray );
				$fallbackOutput .= $this->renderFallback( $galleryArray );
				$i++;
			}
	
			$output .= '</div>';
			$output .= '<div id="' . $this->galleriesArray["gallery_set_name"] . '-fallback">' . $fallbackOutput . '</div>';
			$output .= $this->renderJavascript( $this->galleriesArray["gallery_set_name"] );
		} else {
			$output = $this->renderGallery( $this->galleriesArray["galleries"][0] );
			$output .= $this->renderFallback( $this->galleriesArray["galleries"][0] );
			$output .= $this->renderJavascript( $this->galleriesArray["galleries"][0]["gallery_name"] );
		}
	
		//Save input and (cache) output to database if
		//the plugin is configured to use the database
		if ( $wgSmoothGalleryUseDatabase ) {
			$gId = $this->addSGTextToDB( $input, $output );
	
			//Get a local link from the special page
			$sp = Title::newFromText( "Special:SmoothGallery" );
			$locallink = $sp->getLocalURL( "gallery=" . $gId );
			$linktext = htmlspecialchars( $this->argumentArray["special"] );
			if ( $linktext == '' ) {
				$linktext = 'link';
			}
			$link = '<a href="' . $locallink . '">' . $linktext . '</a>';
	
			if ( $this->argumentArray["special"] != '' ) {
				// We are only sending a special page link
				$output = $link;
			} else if ( !$this->argumentArray["nolink"] ) {
				// We want to send a link with the gallery
				$output = '<div class="MediaWikiSGalleryLink">' . $link . $output . '</div>';
			}
		}

		# flags for use by smoothGalleryParserOutput
		$this->parser->mOutput->mSmoothGalleryTag = true;
		if ( $this->set ) {
			$this->parser->mOutput->mSmoothGallerySetTag = true;
		}
	
		//Finished, let's send it out
		return $output;
	}
	
	function renderGallery ( $galleryArray ) {
		global $wgSmoothGalleryDelimiter;
		global $wgSmoothGalleryUseDatabase;
	
		if ( $this->argumentArray["special"] ) {
			if ( !$wgSmoothGalleryUseDatabase ) {
				//The user wants a link to a special page instead. Let's provide a link with
				//the relevant info
	
				//sanity check
				$name = htmlspecialchars( $this->argumentArray["special"] );
	
				//This is a dirty, dirty hack that should be replaced. It works, and
				//it is safe, but there *MUST* be a better way to do this...
				$img_list = '';
				foreach ( $galleryArray["images"] as $imageArray ) {
					if ( $img_list == '' ) {
						$img_list = $imageArray["title"] . "\n";
					} else {
						$img_list .= $img_list . '|' . $imageArray["title"] . "\n";
					}
				}
	
				//Get a local link from the special page
				$sp = Title::newFromText( "Special:SmoothGallery" );
				$output = $sp->getLocalURL( "height=" . $this->argumentArray["height"]
					. "&width=" . $this->argumentArray["width"]
					. "&showcarousel=" . $this->argumentArray["carousel"]
					. "&timed=" . $this->argumentArray["timed"]
					. "&delay=" . $this->argumentArray["delay"]
					. "&showarrows=" . $this->argumentArray["showarrows"]
					. "&showinfopane=" . $this->argumentArray["showinfopane"]
					. "&fallback=" . $this->argumentArray["fallback"]
					. "&input=" . htmlspecialchars( $img_list ) );
	
				//Provide the link
				return '<a href="' . $output . '">' . $name . '</a>';
			}
		}
	
		//Open the outer div of the gallery
		if ( $this->set ) {
			$output = '<div id="' . $galleryArray["gallery_name"] . '" class="galleryElement">';
			$output .= '<h2>' . $galleryArray["gallery_name"] . '<h2>';
		} else {
			$output = '<div id="' . $galleryArray["gallery_name"] . '" style="width: ' . $this->argumentArray["width"] . ';height: ' . $this->argumentArray["height"] . '; display:none;">';
		}
	
		//TODO iterate over the images and output each
		foreach ( $galleryArray["images"] as $imageArray ) {
			//Add the html for the image
			$output .= '<div class="imageElement">';
			$output .= '<h3>' . $imageArray["heading"] . '</h3>';
			$output .= '<p>' . $imageArray["description"] . '</p>';
			$output .=  '<a href="' . $imageArray["full_url"] . '" title="open image" class="open"></a>';
			$output .=  '<a href="' . $imageArray["view_url"] . '" title="open image" class="open"></a>';
			$output .=  '<img src="' . $imageArray["full_thumb_url"] . '"  class="full" alt="' . $imageArray["description"] . '" />';
	
			if ( $this->argumentArray["carousel"] ) {
				$output .=  '<img src="' . $imageArray["icon_thumb_url"] . '"  class="thumbnail" alt="' . $imageArray["description"] . '" />';
			}
	
			$output .= '</div>';
		}
	
	
		//Close the outer div of the gallery
		$output .= '</div>';
	
		return $output;
	}
	
	function renderFallback ( $galleryArray ) {
		global $wgSmoothGalleryUseDatabase;
	
		$output = '';
	
		if ( !isset( $galleryArray["images"] ) ) {
			return $output;
		}
	
		if ( $this->argumentArray["special"] != "" && !$wgSmoothGalleryUseDatabase ) {
			return $output;
		}
	
		if ( $this->argumentArray["fallback"] == "image" ) {
			if ( !isset( $galleryArray["images"][0] ) ) {
				return '';
			}

			$output .= '<div id="' . $galleryArray['gallery_name'] . '-fallback" class="MediaWikiSGallerySingleImage" style="width: ' . $this->argumentArray["width"] . ';height: ' . $this->argumentArray["height"] . ';" alt="' . $galleryArray["images"][0]["description"] . '">';
			$output .= $galleryArray["images"][0]["full_thumb_url"];
			$output .= '</div>';
		} elseif ( $this->argumentArray["fallback"] == "image-warn" ) {
			if ( !isset( $galleryArray["images"][0] ) ) {
				return '';
			}

			$output .= '<div id="' . $galleryArray['gallery_name'] . '-fallback" class="MediaWikiSGalleryWarning" style="width: ' . $this->argumentArray["width"] . ';height: ' . $this->argumentArray["height"] . ';" alt="' . $galleryArray["images"][0]["description"] . '">';

			wfLoadExtensionMessages( 'SmoothGallery' );
			$output .= wfMsg("smoothgallery-javascript-disabled");

			$output .= '<div class="MediaWikiSGallerySingleImage">';
			$output .= $galleryArray["images"][0]["full_thumb_url"];
			$output .= '</div></div>';
		} else {
			$output .= $this->renderPlainGallery ( $galleryArray );
		}
	
		return $output;
	}
	
	function renderPlainGallery ( $galleryArray ) {
		global $wgVersion;
	
		if ( !isset( $galleryArray["images"] ) ) {
			return '';
		}
	
		//Wrapper div for plain old gallery, to be shown per default, if JS is off.
		$output = '<div id="' . $galleryArray["gallery_name"] . '-fallback">';
	
		$plain_gallery = new ImageGallery();
	
		foreach ( $galleryArray["images"] as $image ) {
			if ( version_compare( $wgVersion, "1.11", '<' ) ) {
				$plain_gallery->add( $image["image_object"], $image["description"] ); //TODO: use text
			} else {
				$plain_gallery->add( $image["image_object"]->getTitle(), $image["description"] ); //TODO: use text
			}
		}
	
		$output .= $plain_gallery->toHTML();
	
		//Close the wrapper div for the plain old gallery
		$output .= '</div>';
	
		return $output;
	}
	
	function renderJavascript ( $name ) {
		//Output the javascript needed for the gallery with any
		//options the user requested
		$output = '<script type="text/javascript">';
	
		$output .= 'document.getElementById("' . $name . '-fallback").style.display = "none";'; //hide plain gallery
		$output .= 'document.getElementById("' . $name . '").style.display = "block";'; //show smooth gallery
	
		$output .= 'function startGallery_' . $name . '() {';
		if ( $this->set ) {
			$output .= "var MediaWikiSGallerySet = new gallerySet($('" . $name . "'), {";
		} else {
			$output .= "var MediaWikiSGallery = new gallery($('" . $name . "'), {";
		}
	
		$output .= 'thumbWidth: 100, thumbHeight: 75'; //would be nice if we could change this to 120x120 to re-use thumbnails...
	
		//Add user provided options
		if ( $this->argumentArray["timed"] ) {
			$output .= ', timed: true';
			$output .= ', delay: ' . $this->argumentArray["delay"];
		}
	
		if ( !$this->argumentArray["carousel"] ) {
			$output .= ', showCarousel: false';
		}
	
		if ( !$this->argumentArray["showarrows"] ) {
			$output .= ', showArrows: false';
		}
	
		if ( !$this->argumentArray["showinfopane"] ) {
			$output .= ', showInfopane: false';
		}
	
		$output .= ', useHistoryManager: true';
		#$output .= ', preloader: true';
		#$output .= ', preloaderImage: true';
		#$output .= ', preloaderErrorImage: true';
		#$output .= ', carouselPreloader: true';
		#$output .= ", textPreloadingCarousel: '" . wfMsg("smoothgallery-loading") . "'";
	
		$output .= '});';
		$output .= 'HistoryManager.start();';
		$output .= '}';
		$output .= "window.addEvent('domready', startGallery_$name);";
		#$output .= 'addOnloadHook(startGallery_' . $name . ');';
		$output .= '</script>';
	
		return $output;
	}
	
	function addSGTextToDB($input, $cache) {
		global $wgSharedDB, $wgDBname;
		$dbw =& wfGetDB( DB_MASTER );
	
		if (isset($wgSharedDB)) {
			# It would be nicer to get the existing dbname
			# and save it, but it's not possible
			$dbw->selectDB($wgSharedDB);
		}
	
		$dbw->insert('text_sg', array('sg_text' => $dbw->addQuotes( $input ),
						  'sg_cache' => $cache));
		return $dbw->insertId();
	}

        static function setGalleryHeaders(  &$outputPage ) {
                global $wgSmoothGalleryExtensionPath;

                $extensionpath = $wgSmoothGalleryExtensionPath;

                //Add mootools (required by SmoothGallery)
                //You can use the compressed js if you want, but I
                //generally don't trust them unless I wrote them myself
                $outputPage->addScript( '<script src="' . $extensionpath . '/scripts/mootools.uncompressed.js" type="text/javascript"></script>' );

                //Add SmoothGallery javascript
                $outputPage->addScript( '<script src="' . $extensionpath . '/scripts/jd.gallery.js" type="text/javascript"></script>' );
                $outputPage->addScript( '<script src="' . $extensionpath . '/scripts/HistoryManager.js" type="text/javascript"></script>' );

                //Add SmoothGallery css
                $outputPage->addLink(
                        array(
                                'rel' => 'stylesheet',
                                'type' => 'text/css',
                                'href' => $extensionpath . '/css/jd.gallery.css'
                        )
                );

                #$outputPage->addScript( '<link rel="stylesheet" href="' . $extensionpath . '/css/jd.gallery.css" type="text/css" media="screen" charset="utf-8" />' );

                $outputPage->addScript( '<style type="text/css">.jdGallery .slideInfoZone { overflow:auto ! important; }</style>' );

                return true;
        }

        static function setGallerySetHeaders(  &$outputPage ) {
                global $wgSmoothGalleryExtensionPath;

                $extensionpath = $wgSmoothGalleryExtensionPath;
                $outputPage->addScript( '<script src="' . $extensionpath . '/scripts/jd.gallery.set.js" type="text/javascript"></script>' );

		return true;
	}
}
