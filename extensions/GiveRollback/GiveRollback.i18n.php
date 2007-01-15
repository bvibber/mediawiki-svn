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

/* French */
'fr' => array(
'giverollback' => 'Donner ou enlever les droits de révocation',
'giverollback-header' => "'''Un bureaucrate local peut utiliser cette page pour donner ou enlever les droits de révocation (« revert ») à un compte utilisateur.'''<br />
On peut l’utiliser pour autoriser des non-administrateurs à révoquer des vandalismes plus rapidement. Les bureaucrates ne devraient le faire qu’en accord avec les règles en vigueur.",
'giverollback-username' => 'Nom d’utilisateur :',
'giverollback-search' => 'Chercher',
'giverollback-hasrb' => '[[User:$1|$1]] a les droits de révocation.',
'giverollback-norb' => '[[User:$1|$1]] n’a pas les droits de révocation.',
'giverollback-toonew' => '[[User:$1|$1]] est trop récent, et ne peut pas recevoir les droits de révocation.',
'giverollback-sysop' => '[[User:$1|$1]] est un administrateur, et peut déjà révoquer les articles.',
'giverollback-change' => 'Changer le statut :',
'giverollback-grant' => 'Donner',
'giverollback-revoke' => 'Enlever',
'giverollback-comment' => 'Commentaire :',
'giverollback-granted' => '[[User:$1|$1]] possède maintenant les droits de révocation.',
'giverollback-revoked' => '[[User:$1|$1]] ne possède plus les droits de révocation.',
'giverollback-logpage' => 'Historique des droits de révocation',
'giverollback-logpagetext' => 'Cette page présente un journal du changement des droits de révocation.',
'giverollback-logentrygrant' => 'a donné les droits de révocation à [[$1]]',
'giverollback-logentryrevoke' => 'a enlevé les droits de révocation de [[$1]]',
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

/* Serbian default (Sasa Stefanovic) */
'sr' => array(
'giverollback' => 'Додај или одузми права враћања',
'giverollback-header' => "'''Локални бирократа може да користи ову страницу да додели или одузме права враћања другим корисницима.'''<br />Ова права се могу користити како бисте доделили обичним корисницима могућност брзог враћања вандализама. Ово мора да се уради са тренутним правилима пројекта.",
'giverollback-username' => 'Корисник:',
'giverollback-search' => 'Иди',
'giverollback-hasrb' => '[[User:$1|$1]] има права враћања.',
'giverollback-norb' => '[[User:$1|$1]] нема права враћања.',
'giverollback-toonew' => '[[User:$1|$1]] је превише нов, и не могу му се доделити права враћања.',
'giverollback-sysop' => '[[User:$1|$1]] је администратор, и већ има права враћања.',
'giverollback-change' => 'Промени статус:',
'giverollback-grant' => 'Додели',
'giverollback-revoke' => 'Одузми',
'giverollback-comment' => 'Коментар:',
'giverollback-granted' => '[[User:$1|$1]] сад има права враћања.',
'giverollback-revoked' => '[[User:$1|$1]] више нема права враћања.',
'giverollback-logpage' => 'Историја права враћања',
'giverollback-logpagetext' => 'Ово је историја промена обичних корисника са [[Помоћ:Права враћања|правом враћања]] ',
'giverollback-logentrygrant' => 'доделио права враћања кориснику [[$1]]',
'giverollback-logentryrevoke' => 'одузео права враћања кориснику [[$1]]',
),

/* Serbian cyrillic (Sasa Stefanovic) */
'sr-ec' => array(
'giverollback' => 'Додај или одузми права враћања',
'giverollback-header' => "'''Локални бирократа може да користи ову страницу да додели или одузме права враћања другим корисницима.'''<br />Ова права се могу користити како бисте доделили обичним корисницима могућност брзог враћања вандализама. Ово мора да се уради са тренутним правилима пројекта.",
'giverollback-username' => 'Корисник:',
'giverollback-search' => 'Иди',
'giverollback-hasrb' => '[[User:$1|$1]] има права враћања.',
'giverollback-norb' => '[[User:$1|$1]] нема права враћања.',
'giverollback-toonew' => '[[User:$1|$1]] је превише нов, и не могу му се доделити права враћања.',
'giverollback-sysop' => '[[User:$1|$1]] је администратор, и већ има права враћања.',
'giverollback-change' => 'Промени статус:',
'giverollback-grant' => 'Додели',
'giverollback-revoke' => 'Одузми',
'giverollback-comment' => 'Коментар:',
'giverollback-granted' => '[[User:$1|$1]] сад има права враћања.',
'giverollback-revoked' => '[[User:$1|$1]] више нема права враћања.',
'giverollback-logpage' => 'Историја права враћања',
'giverollback-logpagetext' => 'Ово је историја промена обичних корисника са [[Помоћ:Права враћања|правом враћања]] ',
'giverollback-logentrygrant' => 'доделио права враћања кориснику [[$1]]',
'giverollback-logentryrevoke' => 'одузео права враћања кориснику [[$1]]',
),

/* Serbian latin (Sasa Stefanovic) */
'sr-el' => array(
'giverollback' => 'Dodaj ili oduzmi prava vraćanja',
'giverollback-header' => "'''Lokalni birokrata može da koristi ovu stranicu da dodeli ili oduzme prava vraćanja drugim korisnicima.'''<br />Ova prava se mogu koristiti kako biste dodelili običnim korisnicima mogućnost brzog vraćanja vandalizama. Ovo mora da se uradi sa trenutnim pravilima projekta.",
'giverollback-username' => 'Korisnik:',
'giverollback-search' => 'Idi',
'giverollback-hasrb' => '[[User:$1|$1]] ima prava vraćanja.',
'giverollback-norb' => '[[User:$1|$1]] nema prava vraćanja.',
'giverollback-toonew' => '[[User:$1|$1]] je previše nov, i ne mogu mu se dodeliti prava vraćanja.',
'giverollback-sysop' => '[[User:$1|$1]] je administrator, i već ima prava vraćanja.',
'giverollback-change' => 'Promeni status:',
'giverollback-grant' => 'Dodeli',
'giverollback-revoke' => 'Oduzmi',
'giverollback-comment' => 'Komentar:',
'giverollback-granted' => '[[User:$1|$1]] sad ima prava vraćanja.',
'giverollback-revoked' => '[[User:$1|$1]] više nema prava vraćanja.',
'giverollback-logpage' => 'Istorija prava vraćanja',
'giverollback-logpagetext' => 'Ovo je istorija promena običnih korisnika sa [[Pomoć:Prava vraćanja|pravom vraćanja]] ',
'giverollback-logentrygrant' => 'dodelio prava vraćanja korisniku [[$1]]',
'giverollback-logentryrevoke' => 'oduzeo prava vraćanja korisniku [[$1]]',
),

	);
}

?>
