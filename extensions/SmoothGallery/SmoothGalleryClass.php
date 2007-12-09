<?php

class SmoothGallery {

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

