<?php

$wgAutoloadClasses['PngHandler'] = dirname(__FILE__) . '/PngHandler_body.php';

$wgExtensionCredits['PngHandler'][] = array(
	'name' => 'PngHandler',
	'author' => 'Bryan Tong Minh', 
	'url' => 'http://www.mediawiki.org/wiki/Extension:PngHandler', 
	'description' => 'Resize PNGs using pngds'
);

/*
 * Path to the pngds executable. Download the source from 
 * <http://svn.wikimedia.org/svnroot/mediawiki/trunk/pngds> or binaries from
 * <http://toolserver.org/~bryan/pngds/>
 */
$egPngdsPath = '';
/*
 * If true tries to resize using the default media handler.
 * Handy as pngds not support upscaling or palette images
 */
$egPngdsFallback = true;
/*
 * Minimum size in pixels for an image to be handled using PNGHandler. 
 * Smaller files will be handled using the default media handler.
 */
$egPngdsMinSize = 2000000;

$wgMediaHandlers['image/png'] = 'PngHandler';
