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
# Version 1.0i / 2007-02-03
#

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

$wgExtensionFunctions[] = "efSmoothGallery";

$wgHooks['OutputPageParserOutput'][] = 'smoothGalleryParserOutput';

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['SmoothGallery'] = $dir . 'SmoothGallery.i18n.php';
$wgAutoloadClasses['SmoothGallery'] = $dir . 'SmoothGalleryClass.php';
$wgAutoloadClasses['SpecialSmoothGallery'] = $dir . 'SpecialSmoothGallery.php';
$wgSpecialPages['SmoothGallery'] = 'SpecialSmoothGallery';

//sane defaults. always initialize to avoid register_globals vulnerabilities
$wgSmoothGalleryDelimiter = "\n";
$wgSmoothGalleryExtensionPath = $wgScriptPath . '/extensions/SmoothGallery';
$wgSmoothGalleryUseDatabase = false;

function efSmoothGallery() {
	global $wgParser;

	$wgParser->setHook( 'sgallery', 'initSmoothGallery' );
	$wgParser->setHook( 'sgalleryset', 'initSmoothGallerySet' );
}

function initSmoothGallery( $input, $argv, &$parser, $calledFromSpecial=false, $calledAsSet=false ) {
	require_once( 'SmoothGalleryParser.php' );

	$sgParser = new SmoothGalleryParser( $input, $argv, $parser, $calledFromSpecial, $calledAsSet );
	$sgGallery = new SmoothGallery();

	$sgGallery->setParser( $parser );
	$sgGallery->setSpecial( $calledFromSpecial );
	$sgGallery->setSet( $calledAsSet );
	$sgGallery->setArguments( $sgParser->getArguments() );
	$sgGallery->setGalleries( $sgParser->getGalleries() ); 

	$sgGallery->checkForErrors();
	if ( $sgGallery->hasErrors() ) {
		return $sgGallery->getErrors();
	} else {
		return $sgGallery->toHTML();
	}
}

function initSmoothGallerySet( $input, $args, &$parser, $calledFromSpecial=false ) {
	$output = initSmoothGallery( $input, $args, $parser, $calledFromSpecial, true );

	return $output;
}

/**
 * Hook callback that injects messages and things into the <head> tag
 * Does nothing if $parserOutput->mSmoothGalleryTag is not set
 */
function smoothGalleryParserOutput( &$outputPage, &$parserOutput )  {
	if ( !empty( $parserOutput->mSmoothGalleryTag ) ) {
		SmoothGallery::setGalleryHeaders( $outputPage );
	}
	if ( !empty( $parserOutput->mSmoothGallerySetTag ) ) {
		SmoothGallery::setGallerySetHeaders( $outputPage );
	}
	return true;
}

/**
 * Add extension information to Special:Version
 */
$wgExtensionCredits['other'][] = array(
	'name'        => 'SmoothGallery parser extension',
	'version'     => '1.1b (alpha)',
	'author'      => 'Ryan Lane',
	'description' => 'Allows users to create galleries with images that have been uploaded. Allows most options of SmoothGallery',
	'descriptionmsg' => 'smoothgallery-desc',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:SmoothGallery',
);
