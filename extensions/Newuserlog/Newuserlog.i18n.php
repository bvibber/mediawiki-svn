<?php
/**
 * Internationalisation file for Newuserlog extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgNewuserlogMessages = array();

$wgNewuserlogMessages['en'] = array(
	'newuserlogpage'           => 'User creation log',
	'newuserlogpagetext'       => 'This is a log of user creations',
	'newuserlogentry'          => '', # For compatibility, don't translate this
	'newuserlog-create-entry'  => 'New user',
	'newuserlog-create2-entry' => 'created account for $1',
	'newuserlog-create-text'   => "[[User talk:$1|$2]] | [[Special:Contributions/$1|$3]] | [[Special:Blockip/$1|$4]]",
);
$wgNewuserlogMessages['de'] = array(
	'newuserlogpage'           => 'Neuanmeldungs-Logbuch',
	'newuserlogpagetext'       => 'Dies ist ein Logbuch für neu erstellte Benutzerkonten.',
	'newuserlog-create-entry'  => 'Neues Benutzerkonto',
	'newuserlog-create2-entry' => 'Benutzerkonto für $1 erstellt',
	'newuserlog-create-text'   => "[[{{ns:user_talk}}:$1|$2]] | [[{{ns:special}}:Contributions/$1|$3]] | [[{{ns:special}}:Blockip/$1|$4]]",
);
$wgNewuserlogMessages['he'] = array(
	'newuserlogpage'           => 'יומן הרשמת משתמשים',
	'newuserlogpagetext'       => 'זהו יומן המכיל הרשמות של משתמשים.',
	'newuserlog-create-entry'  => 'משתמש חדש',
	'newuserlog-create2-entry' => 'יצר חשבון עבור $1',
	'newuserlog-create-text'   => "[[{{ns:user_talk}}:$1|$2]] | [[{{ns:special}}:Contributions/$1|$3]] | [[{{ns:special}}:Blockip/$1|$4]]",
);
$wgNewuserlogMessages['id'] = array(
	'newuserlogpage'           => 'Log pengguna baru',
	'newuserlogpagetext'       => 'Di bawah ini adalah log pendaftaran pengguna baru',
	'newuserlog-create-entry'  => 'Pengguna baru',
	'newuserlog-create2-entry' => 'membuat akun untuk $1',
	'newuserlog-create-text'   => "[[User talk:$1|$2]] | [[Special:Contributions/$1|$3]] | [[Special:Blockip/$1|$4]]",
);
$wgNewuserlogMessages['ja'] = array(
	'newuserlogpage'           => 'アカウント作成記録',
	'newuserlogpagetext'       => '以下は最近作成されたアカウントの一覧です。',
	'newuserlog-create-entry'  => '新規利用者',
	'newuserlog-create2-entry' => 'が新規に $1 を作成しました',
	'newuserlog-create-text'   => "[[User talk:$1|$2]] | [[Special:Contributions/$1|$3]] | [[Special:Blockip/$1|$4]]",
);
$wgNewuserlogMessages['nl'] = array(
	'newuserlogpage'           => 'Logboek nieuwe gebruikers',
	'newuserlogpagetext'       => 'Hieronder staan de nieuw ingeschreven gebruikers',
	'newuserlog-create-entry'  => 'heeft zich ingeschreven',
	'newuserlog-create2-entry' => 'heeft gebruiker $1 ingeschreven',
	'newuserlog-create-text'   => "[[{{ns:user_talk}}:$1|$2]] | [[{{ns:special}}:Contributions/$1|$3]] | [[{{ns:special}}:Blockip/$1|$4]]",
);
$wgNewuserlogMessages['pt'] = array(
	'newuserlogpage'           => 'Registo de criação de utilizadores',
	'newuserlogpagetext'       => 'Este é um registo de novas contas de utilizador',
	'newuserlog-create-entry'  => 'Novo utilizador',
	'newuserlog-create2-entry' => 'criou conta para $1',
	'newuserlog-create-text'   => "[[{{ns:user_talk}}:$1|$2]] | [[{{ns:special}}:Contributions/$1|$3]] | [[{{ns:special}}:Blockip/$1|$4]]",
);
?>
