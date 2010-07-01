<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @author Roan Kattouw
 *
 */

/**
 * This file is the entry point for the resource loader.
 */
 
// TODO: Caching + easy 304s before WebStart

require ( dirname( __FILE__ ) . '/includes/WebStart.php' );
wfProfileIn( 'loader.php' );

// URL safety checks
//
// See RawPage.php for details; summary is that MSIE can override the
// Content-Type if it sees a recognized extension on the URL, such as
// might be appended via PATH_INFO after 'load.php'.
//
// Some resources can contain HTML-like strings (e.g. in messages)
// which will end up triggering HTML detection and execution.
//
if ( $wgRequest->isPathInfoBad() ) {
	wfHttpError( 403, 'Forbidden',
		'Invalid file extension found in PATH_INFO. ' .
		'The resource loader must be accessed through the primary script entry point.' );
	return;
	// FIXME: Doesn't this execute the rest of the request anyway?
	// Was taken from api.php so I guess it's maybe OK but it doesn't look good.
}

$loader = new ResourceLoader( $wgRequest->getVal( 'lang', 'en' ) );
$loader->setUseJSMin( $wgRequest->getBool( 'jsmin', true ) );
$loader->setUseCSSMin( $wgRequest->getBool( 'cssmin', true ) );
$loader->setUseDebugMode( $wgRequest->getBool( 'debug', false ) );
$loader->setUseCSSJanus( $wgRequest->getVal( 'dir', 'ltr' ) == 'rtl' );
$moduleParam = $wgRequest->getVal( 'modules' );
$modules = $moduleParam ? explode( '|', $moduleParam ) : array();
foreach ( $modules as $module ) {
	$loader->addModule( $module );
}

// TODO: Cache-Control header
// TODO: gziphandler
header( 'Content-Type', 'text/javascript' );
echo $loader->getOutput();

wfProfileOut( 'loader.php' );
wfLogProfilingData();

// Shut down the database
wfGetLBFactory()->shutdown();