<?php

/**
 * Internationalisation file for the GiveRollback extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */

function efGiveRollbackMessages() {
	return array(
	
/* English (Rob Church) */
'en' => array(
'giverollback' => 'Grant or revoke rollback rights',
'giverollback-header' => "'''A local bureaucrat can use this page to grant or revoke [[Help:Rollback|rollback rights]] to another user account.'''<br />This can be used to allow non-sysops to revert vandalism quickly. This should be done in accordance with applicable policies.",
'giverollback-username' => 'Username:',
'giverollback-search' => 'Go',
'giverollback-hasrb' => '[[User:$1|$1]] has rollback rights.',
'giverollback-norb' => '[[User:$1|$1]] does not have rollback rights.',
'giverollback-toonew' => '[[User:$1|$1]] is too new, and cannot be given rollback rights.',
'giverollback-sysop' => '[[User:$1|$1]] is a sysop, and already has rollback permissions.',
'giverollback-change' => 'Change status:',
'giverollback-grant' => 'Grant',
'giverollback-revoke' => 'Revoke',
'giverollback-comment' => 'Comment:',
'giverollback-granted' => '[[User:$1|$1]] now has rollback rights.',
'giverollback-revoked' => '[[User:$1|$1]] no longer has rollback rights.',
'giverollback-logpage' => 'Rollback rights log',
'giverollback-logpagetext' => 'This is a log of changes to non-sysops\' [[Help:Rollback|rollback]] rights.',
'giverollback-logentrygrant' => 'granted rollback rights to [[$1]]',
'giverollback-logentryrevoke' => 'removed rollback rights from [[$1]]',
),

/* German (Raymond) */
'de' => array(
'giverollback' => 'Zurücksetzen-Recht erteilen oder entziehen',
'giverollback-header' => "'''Ein lokaler Bürokrat kann auf dieser Seite anderen Benutzern das Recht zum Zurücksetzen ''(Rollback)'' erteilen oder entziehen.<br />Dadurch können auch Benutzer ohne Administratoren-Status Vandalismus schnell rückgängig machen. Dies sollte in Übereinstimmung mit den anwendbaren Richtlinien geschehen.",
'giverollback-username' => 'Benutzername:',
'giverollback-search' => 'Ok',
'giverollback-hasrb' => '[[User:$1|$1]] hat das Zurücksetzen-Recht.',
'giverollback-norb' => '[[User:$1|$1]] hat das Zurücksetzen-Recht nicht.',
'giverollback-toonew' => '[[User:$1|$1]] ist zu neu, ihm kann das Zurücksetzen-Recht nicht gegeben werden.',
'giverollback-sysop' => '[[User:$1|$1]] ist ein Administrator und hat bereits das Zurücksetzen-Recht.',
'giverollback-change' => 'Ändere den Status:',
'giverollback-grant' => 'Erteile',
'giverollback-revoke' => 'Entziehe',
'giverollback-comment' => 'Kommentar:',
'giverollback-granted' => '[[User:$1|$1]] wurde das Zurücksetzen-Recht erteilt.',
'giverollback-revoked' => '[[User:$1|$1]] wurde das Zurücksetzen-Recht entzogen.',
'giverollback-logpage' => 'Zurücksetzen-Rechte Logbuch',
'giverollback-logpagetext' => 'Dies ist das Logbuch der Zurücksetzen-Rechtevergabe für Nicht-Administratoren.',
'giverollback-logentrygrant' => 'erteilte das Zurücksetzen-Recht an [[$1]]',
'giverollback-logentryrevoke' => 'entzog das Zurücksetzen-Recht von [[$1]]',
),
	
/* Indonesian (Ivan Lanin) */
'id' => array(
'giverollback' => 'Pemberian atau penarikan hak pengembalian',
'giverollback-header' => "'''Seorang birokrat lokal dapat menggunakan halaman ini untuk memberikan atau menarik  [[{{NS:HELP}}:Pengembalian|hak pengembalian]] ke akun pengguna lain.'''<br />Hal ini dapat dilakukan untuk mengizinkan non-pengurus untuk mengembalikan vandalisme dengan cepat. Hal ini harus dilakukan sesuai dengan kebijakan yang ada.",
'giverollback-username' => 'Nama pengguna:',
'giverollback-search' => 'Cari',
'giverollback-hasrb' => '[[User:$1|$1]] memiliki hak pengembalian.',
'giverollback-norb' => '[[User:$1|$1]] tidak memiliki hak pengembalian.',
'giverollback-toonew' => '[[User:$1|$1]] terlalu baru, sehingga tak dapat diberikan hak pengembalian.',
'giverollback-sysop' => '[[User:$1|$1]] adalah pengurs, dan telah memiliki hak pengembalian.',
'giverollback-change' => 'Ganti status:',
'giverollback-grant' => 'Berikan',
'giverollback-revoke' => 'Tarik',
'giverollback-comment' => 'Komentar:',
'giverollback-granted' => '[[User:$1|$1]] sekarang memiliki hak pengembalian.',
'giverollback-revoked' => '[[User:$1|$1]] sekarang tidak lagi memiliki hak pengembalian.',
'giverollback-logpage' => 'Log perubahan hak pengembalian',
'giverollback-logpagetext' => 'Di bawah ini adalah log perubahan [[{{NS:HELP}}:Pengembalian|hak pengembalian]] untuk non-pengurus.',
'giverollback-logentrygrant' => 'memberikan hak pengembalian ke untuk [[$1]]',
'giverollback-logentryrevoke' => 'menarik hak pengembalian ke untuk [[$1]]',
),

	);
}

?>
