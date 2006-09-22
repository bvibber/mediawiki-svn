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
	'newuserlog-create-text'   => "[[User talk:$1|$2]] | [[Special:Contributions/$1|$3]] | [[Special:Blockip/$1|$4]]", # Don't translate this
);
$wgNewuserlogMessages['cs'] = array(
	'newuserlogpage'           => 'Kniha nových uživatelů',
	'newuserlogpagetext'       => 'Toto je záznam nově zaregistrovaných uživatelů.',
	'newuserlog-create-entry'  => 'Nově založený uživatel',
	'newuserlog-create2-entry' => 'založil účet pro $1',
);
$wgNewuserlogMessages['de'] = array(
	'newuserlogpage'           => 'Neuanmeldungs-Logbuch',
	'newuserlogpagetext'       => 'Dies ist ein Logbuch für neu erstellte Benutzerkonten.',
	'newuserlog-create-entry'  => 'Neues Benutzerkonto',
	'newuserlog-create2-entry' => 'Benutzerkonto für $1 erstellt',
);
$wgNewuserlogMessages['he'] = array(
	'newuserlogpage'           => 'יומן הרשמת משתמשים',
	'newuserlogpagetext'       => 'זהו יומן המכיל הרשמות של משתמשים.',
	'newuserlog-create-entry'  => 'משתמש חדש',
	'newuserlog-create2-entry' => 'יצר חשבון עבור $1',
);
$wgNewuserlogMessages['id'] = array(
	'newuserlogpage'           => 'Log pengguna baru',
	'newuserlogpagetext'       => 'Di bawah ini adalah log pendaftaran pengguna baru',
	'newuserlog-create-entry'  => 'Pengguna baru',
	'newuserlog-create2-entry' => 'membuat akun untuk $1',
);
$wgNewuserlogMessages['ja'] = array(
	'newuserlogpage'           => 'アカウント作成記録',
	'newuserlogpagetext'       => '以下は最近作成されたアカウントの一覧です。',
	'newuserlog-create-entry'  => '新規利用者',
	'newuserlog-create2-entry' => 'が新規に $1 を作成しました',
);
$wgNewuserlogMessages['nl'] = array(
	'newuserlogpage'           => 'Logboek nieuwe gebruikers',
	'newuserlogpagetext'       => 'Hieronder staan de nieuw ingeschreven gebruikers',
	'newuserlog-create-entry'  => 'heeft zich ingeschreven',
	'newuserlog-create2-entry' => 'heeft gebruiker $1 ingeschreven',
);
$wgNewuserlogMessages['pl'] = array(
	'newuserlogpage'           => 'Nowi użytkownicy',
	'newuserlogpagetext'       => 'To jest rejestr ostatnio utworzonych kont użytkowników',
	'newuserlog-create-entry'  => 'Nowy użytkownik',
	'newuserlog-create2-entry' => 'utworzono konto dla $1',
);
$wgNewuserlogMessages['pt'] = array(
	'newuserlogpage'           => 'Registo de criação de utilizadores',
	'newuserlogpagetext'       => 'Este é um registo de novas contas de utilizador',
	'newuserlog-create-entry'  => 'Novo utilizador',
	'newuserlog-create2-entry' => 'criou conta para $1',
);

$wgNewuserlogMessages['ru'] = array(
	'newuserlogpage'           => 'Журнал регистрации участников',
	'newuserlogpagetext'       => 'Список недавно зарегистрировавшихся участников',
	'newuserlog-create-entry'  => 'Новый участник',
	'newuserlog-create2-entry' => 'cоздал новую учётную запись: $1',
);

$wgNewuserlogMessages['wa'] = array(
	'newuserlogpage'           => 'Djournå des noveas uzeus',
	'newuserlogpagetext'       => 'Chal pa dzo c\' est ene djivêye des uzeus novelmint eredjîstrés.',
	'newuserlog-create-entry'  => 'novea uzeu',
	'newuserlog-create2-entry' => 'conte ahivé po $1',
);
$wgNewuserlogMessages['zh-cn'] = array(
	'newuserlogpage'           => '新进用户名册',
	'newuserlogpagetext'       => '本日志是显示新注册用户的日志',
	'newuserlog-create-entry'  => '新用户',
	'newuserlog-create2-entry' => '已创建$1的账户',
);
$wgNewuserlogMessages['zh-tw'] = array(
	'newuserlogpage'           => '新進用戶名冊',
	'newuserlogpagetext'       => '這是一個最近被創建用戶的日誌',
	'newuserlog-create-entry'  => '新用戶',
	'newuserlog-create2-entry' => '已創建$1的帳戶',
);
$wgNewuserlogMessages['zh-yue'] = array(
	'newuserlogpage'           => '使用者開戶記錄',
	'newuserlogpagetext'       => '呢個係一個使用者開戶嘅日誌',
	'newuserlog-create-entry'  => '新用戶',
	'newuserlog-create2-entry' => '已經開咗$1嘅戶口',
);
$wgNewuserlogMessages['zh-hk'] = $wgNewuserlogMessages['zh-tw'];
$wgNewuserlogMessages['zh-sg'] = $wgNewuserlogMessages['zh-cn'];
?>
