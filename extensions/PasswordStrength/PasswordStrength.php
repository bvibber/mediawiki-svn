<?php

/**
 * PasswordStrength
 *   Perform additional security checks on a password via regular
 *   expressions
 *
 * Copyright (C) 2008 Chad Horohoe <innocentkiller@gmail.com>
 * http://www.mediawiki.org/wiki/Extension:PasswordStrength
 *
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
 */

$wgExtensionMessagesFiles['PasswordStrength'] = dirname(__FILE__) . '/PasswordStrength.i18n.php';
$wgHooks['isValidPassword'][] = 'PrefsPasswordAudit';
$wgExtensionCredits['other'][] = array(
	'name'           	=> 'PasswordStrength',
	'author'         	=> 'Chad Horohoe',
	'url'				=> 'http://www.mediawiki.org/wiki/Extension:PasswordStrength',
	'description'		=> 'Perform additional security checks on passwords with regular expressions.',
	'description-msg'   => 'passwordstr-desc',
	'version'			=> '0.5',
);

// Config 
$wgMustHaveInts = 0;
$wgMustHaveUpperCase = 0;
$wgMustHaveLowerCase = 0;
$wgPasswordStrengthRegexes = array();
$wgPasswordStrengthRegexes[] = '/^\d+$/'; // No passwords made of all digits

/**
 * Hook function for PrefsPasswordAudit. Loop through the regex's and the other
 * strength tests
 */
function psCheckRegex( $user, $newpass, $status ) {
	global $wgPasswordStrengthCheck;
	if ( $status != 'success' ) { // skip earlier checks
		return true;
	}
	wfLoadExtensionMessages( 'PasswordStrength' );
	
	if ( $wgMustHaveInts > 0 ) {
		preg_match_all( "/[0-9]/", $newpass, $matches );
		if ( (count($matches) - 1)  < $wgMustHaveInts ) {
			$result = wfMsgExt( 'passwordstr-needmore-ints', array( 'parsemag'), $wgMustHaveInts );
			throw new PasswordError( $result );
		}
	}
	if ( $wgMustHaveUpperCase > 0 ) {
		preg_match_all( "/[A-Z]/", $newpass, $matches );
		if ( (count($matches) - 1)  < $wgMustHaveUpperCase ) {
			$result = wfMsgExt( 'passwordstr-needmore-upper', array( 'parsemag'), $wgMustHaveUpperCase );
			throw new PasswordError( $result );
		}
	}
	if ( $wgMustHaveLowerCase > 0 ) {
		preg_match_all( "/[a-z]/", $newpass, $matches );
		if ( (count($matches) - 1)  < $wgMustHaveLowerCase ) {
			$result = wfMsgExt( 'passwordstr-needmore-lower', array( 'parsemag'), $wgMustHaveLowerCase );
			throw new PasswordError( $result );	
		}
	}
	
	if ( is_array( $wgPasswordStrengthCheck ) ) {
		foreach ( $wgPasswordStrengthCheck as $regex ) {
			if ( preg_match( $regex, $password ) ) {
				$result = wfMsg( 'passwordstr-regex-hit' );
				throw new PasswordError( $result );
			}
		}
	}
}
