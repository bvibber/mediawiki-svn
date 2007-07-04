<?php

/**
 * Internationalisation file for Resign extension
 *
 * @addtogroup Extensions
*/

function efResignMessages() {
	$messages = array(
	
'en' => array(
'resign'           => 'Resign',
'resign-text'      => "You can '''remove your permissions''' (e.g. sysop) in this page. Please check the permissions you would like to remove.

Remember: '''You cannot get your permissions back this way!''' You will have to convince a bureaucrat or steward to grant you back the permissions. Please use this page very carefully.",
'resign-reason'    => 'Reason:',
'resign-confirm'   => 'I confirm that I would like to remove all the permissions I checked.',
'resign-noconfirm' => 'Please check the confirm checkbox if you would like to remove your permissions.',
'resign-nogroups'  => 'Please check the permissions you would like to remove.',
'resign-success'   => "'''The permissions were successfully removed.'''",
'resign-logentry'  => 'removed permissions $2 from himself',
),

'de' => array(
'resign'           => 'Rücktritt',
'resign-text'      => "Sie können auf dieser Seite '''Ihre Berechtigungen zurückgeben''' (z.B. die Administrator-Berechtigung). Bitte prüfen Sie die Berechtigungen, die Sie zurückgeben möchten.

Wichtig: '''Dieser Schritt ist unwiderruflich.!''' Sie müssen einen Bürokraten oder Steward kontaktieren, um Ihre Rechte wiederzuerhalten. Bitte benutzen Sie diese Seite daher nur nach sorgfäliger Überlegung.",
'resign-reason'    => 'Grund:',
'resign-confirm'   => 'Ich bestätige, dass ich alle markierten Berechtigungen zurückgeben möchte.',
'resign-noconfirm' => 'Bitte markieren Sie das Bestätigungsfeld um Ihre Berechtigungen zurückzugeben.',
'resign-nogroups'  => 'Bitte markieren Sie die Berechtigungen, die Sie zurückgeben möchten.',
'resign-success'   => "'''Die Rückgabe der Berechtigungen war erfolgreich.'''",
'resign-logentry'  => 'gab die Berechtigung(en) $2 zurück',
),

'fi' => array(
'resign'           => 'Eroa',
'resign-text'      => "Voit '''poistaa omat oikeutesi''' (esim. ylläpitäjän oikeudet) tällä sivulla. Ole hyvä ja rastita oikeudet, jotka haluaisit poistaa.

Muista: '''Et voi saada oikeuksiasi takaisin tämän sivun avulla!''' Sinun täytyy vakuuttaa byrokraatti tai ylivalvoja antamaan oikeutesi takaisin. Ole hyvä ja käytä tätä sivua erittäin varovasti.",
'resign-reason'    => 'Syy:',
'resign-confirm'   => 'Vakuutan, että haluan poistaa kaikki rastitsemani oikeudet.',
'resign-noconfirm' => 'Ole hyvä ja rastita varmistusruutu, jos haluat poistaa oikeutesi.',
'resign-nogroups'  => 'Ole hyvä ja rastita oikeudet, jotka haluat poistaa.',
'resign-success'   => "'''Oikeudet poistettiin onnistuneesti.'''",
'resign-logentry'  => 'poisti oikeudet $2 itseltään',
),

'he' => array(
'resign'           => 'התפטרות',
'resign-text'      => "בדף זה תוכלו '''להסיר את ההרשאות שלכם''' (למשל, הרשאות מפעיל מערכת). אנא סמנו את ההרשאות שברצונכם להסיר.

להזכירכם: '''אינכם יכולים לקבל בחזרה את ההרשאות בדרך זו!''' יהיה עליכם לשכנע ביורוקרט או דייל להעניק לכם את הרשאותיכם בחזרה. אנא השתמשו בדף זה בזהירות רבה.",
'resign-reason'    => 'סיבה:',
'resign-confirm'   => 'אני מאשר שברצוני להסיר את כל ההרשאות שלי שסימנתי, וידוע לי שלא אוכל לקבל אותן בחזרה בדרך זו.',
'resign-noconfirm' => 'אנא סמנו את תיבת הסימון כדי לאשר את ההסרה.',
'resign-nogroups'  => 'אנא בחרו את ההרשאות שברצונכם להסיר.',
'resign-success'   => "'''ההרשאות הוסרו בהצלחה.'''",
'resign-logentry'  => 'הסיר את ההרשאות $2 שלו',
),
	
'id' => array(
'resign'           => 'Pengunduran diri',
'resign-text'      => "Anda dapat melepaskan hak Anda (mis. pengurus) melalui halaman ini. Silakan tandai hak yang ingin Anda lepas.

Ingat: '''Anda tidak dapat memperoleh kembali hak Anda melalui ini!''' Anda harus meyakinkan seorang birokrat atau steward untuk memberikan kembali hak tersebut. Harap gunakan halaman ini dengan sangat hati-hati.",
'resign-reason'    => 'Alasan:',
'resign-confirm'   => 'Saya menyetujui untuk melepas hak-hak yang saya tandai.',
'resign-noconfirm' => 'Harap pilih kotak cek konfirmasi untuk melepaskan hak Anda.',
'resign-nogroups'  => 'Harap pilih hak yang ingin Anda lepas.',
'resign-success'   => "'''Proses pelepasan hak berhasil.'''",
'resign-logentry'  => 'melepaskan hak $2 dari dirinya sendiri',
),

	);
	return $messages;
}


