<?php

/**
 * Internationalisation file for the Username Blacklist extension
 *
 * @author Rob Church <robchur@gmail.com>
 * @addtogroup Extensions
 */

function efUsernameBlacklistMessages( $single = false ) {
	$messages = array(

/* English (Rob Church) */
'en' => array(
'blacklistedusername' => 'Blacklisted username',
'blacklistedusernametext' => 'The user name you have chosen matches the [[MediaWiki:Usernameblacklist|
list of blacklisted usernames]]. Please choose another name.',
),

/* German (Raymond) */
'de' => array(
'blacklistedusername' => 'Benutzername auf der Sperrliste',
'blacklistedusernametext' => 'Der gewählte Benutzername steht auf der [[MediaWiki:Usernameblacklist|Liste der gesperrten Benutzernamen]]. Bitte einen anderen wählen.',
),

/* French */
'fr' => array(
'blacklistedusername' => 'Noms d’utilisateurs en liste noire',
'blacklistedusernametext' => 'Le nom d’utilisateur que vous avez choisi se trouve sur la 
[[MediaWiki:Usernameblacklist|liste des noms interdits]]. Veuillez choisir un autre nom.',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'blacklistedusername' => 'Daftar hitam nama pengguna',
'blacklistedusernametext' => 'Nama pengguna yang Anda pilih berada dalam [[MediaWiki:Usernameblacklist|
daftar hitam nama pengguna]]. Harap pilih nama lain.',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
'blacklistedusername' => 'Gebruikersnaam op zwarte lijst',
'blacklistedusernametext' => 'De gebruikersnaam die u heeft gekozen staat op de [[MediaWiki:Usernameblacklist|
zwarte lijst van gebruikersnamen]]. Kies alstublieft een andere naam.',
),

	);
	return $single ? $messages['en'] : $messages;
}

?>
