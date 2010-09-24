<?php

/**
 * SlimboxThumbs extension, see http://www.mediawiki.org/wiki/Extension:SlimboxThumbs
 * 
 * This extension includes a copy of Slimbox. You can however get your own copy at
 * http://www.digitalia.be/software/slimbox2
 * and use it by replacing the included one, or pointing to it with $slimboxThumbsFilesDir
 *
 * @license Creative Commons Attribution-ShareAlike license 3.0: http://creativecommons.org/licenses/by-sa/3.0/
 *
 * @file SlimboxThumbs.php
 *
 * @author David Raison
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

define( 'SlimboxThumbs_VERSION', '0.2' );

// Register the extension credits.
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'SlimboxThumbs',
	'url' => 'http://www.mediawiki.org/wiki/Extension:SlimboxThumbs',
	'author' => array(
		'[http://david.raison.lu Kwisatz]',
		'[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw].' .
		' Inspired by [http://www.mediawiki.org/wiki/User:Alxndr Alexander]',
	),
	'descriptionmsg' => 'slimboxthumbs-desc',
	'version' => SlimboxThumbs_VERSION
);

$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['SlimboxThumbs'] = $dir . 'SlimboxThumbs.i18n.php';

// Include the settings file.
require_once 'SlimboxThumbs_Settings.php';

/**
 * Other potential hooks would be http://www.mediawiki.org/wiki/Manual:Hooks/ImageOpenShowImageInlineBefore
 * or http://www.mediawiki.org/wiki/Manual:Hooks/ImageBeforeProduceHTML
 * or http://www.mediawiki.org/wiki/Manual:Hooks/BeforeGalleryFindFile
 * or http://www.mediawiki.org/wiki/Manual:Hooks/BeforeParserrenderImageGallery
 * but they would be called for each image, making the wiki even slower
 */
if ( $slimboxThumbsFilesDir ) {
    $slimboxThumbsFilesDir = rtrim( trim( $slimboxThumbsFilesDir ), '/' );  // strip whitespace, then any trailing /
    $wgHooks['BeforeParserrenderImageGallery'][] = 'efSBTTestForGallery'; // this seems to fail on some pages :(
    $hasGallery = true; // temporary fix
    $wgHooks['BeforePageDisplay'][] = 'efSBTAddScripts';
    $wgHooks['BeforePageDisplay'][] = 'efSBTAddSlimboxCode';
}

function efSBTTestForGallery( $parser, $gallery ) {
        global $hasGallery;
        $hasGallery = $gallery instanceof ImageGallery;
        return $hasGallery;
}

function efSBTDebugVar( $varName, $var ) {
    return "\n\n<!--\n$varName: " . str_replace( '--', '__', print_r( $var, true ) ) . "\n-->\n\n";
}

// This is a callback function that gets called by efBeforePageDisplay().
function efRewriteThumbImage( $matches ) {
    global $wgOut, $slimboxThumbsDebug;
    
    if ( $slimboxThumbsDebug ) { global $wgContLang; }
    
    $titleObj = Title::newFromText( rawurldecode( $matches[2] ) );
    $image = wfFindFile( $titleObj, false, false, true ); # # wfFindFile($titleObj,false,false,true) to bypass cache
        $output =  $matches[1]
                . ' href="' . $image->getURL() . '" class="image" rel="lightbox" title="'
                . htmlspecialchars( $wgOut->parse( "'''[[:" . $titleObj->getFullText() . "|" . $titleObj->getText() . "]]:''' " ) . $matches[3] )
                . '" ' . $matches[4] . $matches[5]   // url, hashpath,/w/thumb.php?f=FoodHacker_01.jpg&amp;width=800
                . ( $slimboxThumbsDebug ? efDebugVar( '$matches', $matches )
                        . efSBTDebugVar( '$titleObj', $titleObj )
                        . efSBTDebugVar( '$image', $image )
                        . efSBTDebugVar( '$wgContLang->namespaceNames', $wgContLang->namespaceNames ):'' );
                        
        return $output;
}

// Rewrite the gallery code.
function efRewriteGalleryImage( $matches ) {
        global $wgOut, $slimboxThumbsDebug, $slimboxDefaultWidth;
        $titleObj = Title::newFromText( rawurldecode( $matches[2] ) );
        $image = wfFindFile( $titleObj, false, false, true );
        $realwidth = (Integer) $image->getWidth();
        $width = ( $realwidth > $slimboxDefaultWidth ) ? $slimboxDefaultWidth : $realwidth -1;
        $output = $matches[1]
                // .' href="'.$image->getURL().'" class="image" rel="lightbox[gallery]" title="'
                . ' href="/w/thumb.php?f=' . $image->getName() . '&amp;width=' . $width . '" class="image" rel="lightbox[gallery]" title="'
                . htmlspecialchars( $wgOut->parse( "'''[[:File:" . $titleObj->getFullText() . "|" . $titleObj->getText() . "]]:''' " )
                . $matches[4] )
                . '" ' . $matches[3] . $matches[4] . "</div>"
                . ( $slimboxThumbsDebug ? efDebugVar( '$matches', $matches )
                        . efSBTDebugVar( '$titleObj', $titleObj )
                        . efSBTDebugVar( '$image', $image ):'' );
                        
        return $output;
}

/* Add javacsripts and stylesheets */
function efSBTAddScripts( $out ) {
        global $slimboxThumbsFilesDir, $hasGallery;
        
        // We don't want to load jQuery if there's no gallery here.
        //if ( !$hasGallery ) return false;

        $out->addScript( '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>' . "\n" );
        $out->addScript( '<script type="text/javascript" src="' . $slimboxThumbsFilesDir . '/js/slimbox2.js"></script>' . "\n" );
        $out->addExtensionStyle( $slimboxThumbsFilesDir . '/css/slimbox2.css', 'screen' );

        // use thumb.php to resize pictures if browser window is smaller than the picture itself
        $out->addInlineScript( '$(document).ready(function(){
                if($("table.gallery").val() != undefined){
                        var boxWidth = ($(window).width() - 20);
                        var rxp = new RegExp(/([0-9]{2,})$/);
                        $("a[rel=\'lightbox[gallery]\']").each(function(el){
                                if(boxWidth < Number(this.search.match(rxp)[0])){
                                        this.href = this.pathname+this.search.replace(rxp,boxWidth);
                                }
                        });
                }
        })' );
        
        return true;
}

function efSBTAddSlimboxCode( $out, $skin ) {
        global $slimboxThumbsFilesDir, $wgContLang, $hasGallery;

        // We don't want to run regular expressions if there's no gallery here.
        if ( !$hasGallery ) {
        	return false;
        }

        # # ideally we'd do this with XPath, but we'd need valid XML for that, so we'll do it with some ugly regexes
        # # (could use a regex to pull out all div.thumb, maybe they're valid XML? ...probably not)
        # # An other alternative would be to use javascript and the DOM

        // regex for thumbnails
        $pattern = '/(<a[^>]+?)'        // $1: start of opening <a> tag through start of href attribute in <a> tag
                 . '\s*href="[^"]*(?:' . $wgContLang->namespaceNames[6] . '):' // dont care about start of original link href...
                 . '([^"\/]+)'           // $2: ...but end is wiki name for the image
                 . '"\s*class="image"\s*title="'
                 . '([^"]+)'                              // $3: link title becomes image caption
                 . '"\s*.'
                 . '([^>]*>)'                             // $4: remainder of opening <a> tag
                 . '\s*'
                 . '(<img[^>]+?class="thumbimage"[^>]*>)' // $5: the img tag itself
                 . '/x';
    $thumbnailsDone = preg_replace_callback( $pattern, 'efRewriteThumbImage', $out->getHTML() );

        // regex for galleries
        $pattern = '/(<div\s*class="gallerybox".+?div\s*class="thumb".+?) # $1: div.gallerybox opening tag through href attribute, so we can keep it intact
                 \s*href="[^"]+"\s*class="image"\s*                   # this is getting replaced
                 title="([^"]+)"                                      # $2: link title attribute holds wiki name for the image
                 ([^>]*>.+?<div\s*class="gallerytext">)               # $3: end of open <a> through start of caption
                 \s*(?:<p>\s*)?                                       #
                 (.+?)                                                # $4: caption is raw HTML... (may choke if contains an ending div)
                 (?:\s*(<\/p>|<br\s*\/?>))?\s*<\/div>                 #
                /sx';
        
    $allDone = preg_replace_callback( $pattern, 'efRewriteGalleryImage', $thumbnailsDone );

    $out->clearHTML();
    $out->addHTML( $allDone );

    return true;
}