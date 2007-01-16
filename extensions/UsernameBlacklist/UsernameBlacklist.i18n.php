<?php

/**
 * Internationalisation file for the Username Blacklist extension
 *
 * @author Rob Church <robchur@gmail.com>
 * @package MediaWiki
 * @subpackage Extensions
 */

function efUsernameBlacklistMessages( $single = false ) {
	$messages = array(

/* English (Rob Church) */
'en' => array(
'blacklistedusername' => 'Blacklisted username',
'blacklistedusernametext' => 'The user name you have chosen matches the [[MediaWiki:Usernameblacklist|
list of blacklisted usernames]]. Please choose another name.',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'blacklistedusername' => 'Daftar hitam nama pengguna',
'blacklistedusernametext' => 'Nama pengguna yang Anda pilih berada dalam [[MediaWiki:Usernameblacklist|
daftar hitam nama pengguna]]. Harap pilih nama lain.',
),

	);
	return $single ? $messages['en'] : $messages;
}

?>
