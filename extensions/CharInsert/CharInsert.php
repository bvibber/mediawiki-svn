<?php
# Copyright (C) 2004 Brion Vibber <brion@pobox.com>
# http://www.mediawiki.org/
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

/**
 * Extension to create new character inserts which can be used on
 * the edit page to make it easy to get at special characters and
 * such forth.
 *
 * @author Brion Vibber <brion at pobox.com>
 * @package MediaWiki
 * @subpackage Extensions
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die();
}

$wgExtensionFunctions[] = 'setupSpecialChars';

function setupSpecialChars() {
    global $wgParser;
    $wgParser->setHook( 'charinsert', 'charInsert' );
}

function charInsert( $data ) {
	return implode( "<br />\n",
		array_map( 'charInsertLine',
			explode( "\n", trim( $data ) ) ) );
}

function charInsertLine( $data ) {
	return implode( "\n",
		array_map( 'charInsertItem',
			preg_split( '/\\s+/', $data ) ) );
}

function charInsertItem( $data ) {
	$chars = array_map( 'charInsertCleanChar', explode( '+', $data ) );
	if( count( $chars ) > 1 ) {
		return charInsertChar( $chars[0], $chars[1], 'CLick the character while selecting a text' );
	} elseif( count( $chars ) == 1 ) {
		return charInsertChar( $chars[0] );
	} else {
		return charInsertChar( '+' );
	}
}

function charInsertCleanChar( $data ) {
	if( preg_match( '/^&#\d+;$/', $data ) ) {
		return $data;
	} elseif( preg_match( '/^&#x[0-9a-f]+;$/i', $data ) ) {
		return $data;
	} elseif( preg_match( '/^&[0-9a-z]+;$/i', $data ) ) {
		return $data;
	} else {
		return htmlspecialchars( $data, ENT_QUOTES );
	}
}

function charInsertChar( $start, $end = '', $title = null ) {
	$estart = htmlspecialchars( $start );
	$eend   = htmlspecialchars( $end   );
	if( $eend == '' ) {
		$inline = $start;
	} else {
		$inline = $start . $end;
	}
	if( $title ) {
		$extra = ' title="' . htmlspecialchars( $title ) . '"';
	} else {
		$extra = '';
	}
	return "<a href=\"javascript:insertTags('$estart','$eend','')\">$inline</a>";
}


?>