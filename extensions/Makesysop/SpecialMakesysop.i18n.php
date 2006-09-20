<?php
/**
 * Internationalisation file for Makesysop extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgMakesysopMessages = array();

$wgMakesysopMessages['en'] = array(
	'makesysoptitle'       => 'Make a user into a sysop',
	'makesysoptext'        => 'This form is used by bureaucrats to turn ordinary users into administrators. Type the name of the user in the box and press the button to make the user an administrator.',
	'makesysopname'        => 'Name of the user:',
	'makesysopsubmit'      => 'Make this user into a sysop',
	'makesysopok'          => "<b>User \"$1\" is now a sysop</b>",
	'makesysopfail'        => "<b>User \"$1\" could not be made into a sysop. (Did you enter the name correctly?)</b>",
	'setbureaucratflag'    => 'Set bureaucrat flag',
	'rights'               => 'Rights:',
	'set_user_rights'      => 'Set user rights',
	'user_rights_set'      => "<b>User rights for \"$1\" updated</b>",
	'set_rights_fail'      => "<b>User rights for \"$1\" could not be set. (Did you enter the name correctly?)</b>",
	'makesysop'            => 'Make a user into a sysop',
	'already_sysop'        => 'This user is already an administrator',
	'already_bureaucrat'   => 'This user is already a bureaucrat',
	'makesysop-nodatabase' => 'Bad interwiki username: $1',
	'group-steward'        => 'Stewards',
	'group-steward-member' => 'Steward',
	'grouppage-steward'    => '{{ns:project}}:Stewards',
);
$wgMakesysopMessages['cs'] = array(
	'makesysoptitle'       => 'Určit nového správce',
	'makesysoptext'        => 'Pomocí tohoto formuláře mohou byrokraté uživateli přidělit práva správce. Vepište jméno uživatele do políčka a stiskněte tlačítko; zaškrtnutím políčka přidáte uživateli také oprávnění „byrokrat“.',
  'makesysopname'        => 'Jméno uživatele:',
	'makesysopsubmit'      => 'Udělat z tohoto uživatele správce',
	'makesysopok'          => "<b>Uživatel \"$1\" je teď správce</b>",
	'makesysopfail'        => "<b>Uživatel \"$1\" nemůže být správce. (Vložili jste jeho jméno správně?)</b>",
	'setbureaucratflag'    => 'Připojit i oprávnění „byrokrat“',
	'rights'               => 'Oprávnění:',
	'set_user_rights'      => 'Nastavit uživatelova oprávnění',
	'user_rights_set'      => "<b>Oprávnění uživatele \"$1\" změněna</b>",
	'set_rights_fail'      => "<b>Oprávnění uživatele \"$1\" nemohla být nastavena. (Vložili jste jméno správně?)</b>",
	'makesysop'            => 'Určit nového správce',
	'already_sysop'        => 'Tento uživatel již je správce',
	'already_bureaucrat'   => 'Tento uživatel již je byrokrat',
	'makesysop-nodatabase' => '$1 není platná identifikace uživatele',
	'group-steward'        => 'Stevardi',
	'group-steward-member' => 'Stevard',
	'grouppage-steward'    => '{{ns:project}}:Stevardi',

);
$wgMakesysopMessages['de'] = array(
	'makesysoptitle'       => 'Mache einen Benutzer zum Administrator',
	'makesysoptext'        => 'Diese Maske wird von Bürokraten benutzt, um normale Benutzer zu Administratoren zu machen.',
	'makesysopname'        => 'Benutzername:',
	'makesysopsubmit'      => 'Mache diesen Benutzer zu einem Administrator',
	'makesysopok'          => '<b>Benutzer „$1“ ist nun ein Administrator.</b>',
	'makesysopfail'        => '<b>Benutzer „$1“ konnte nicht zu einem Administrator gemacht werden. (Haben Sie den Namen richtig geschrieben?)</b>',
	'setbureaucratflag'    => 'Mache Benutzer zum Bürokraten',
	'rights'               => 'Rechte:',
	'set_user_rights'      => 'Benutzerrechte setzen',
	'user_rights_set'      => '<b>Benutzerrechte für „$1“ aktualisiert</b>',
	'set_rights_fail'      => '<b>Benutzerrechte für „$1“ konnten nicht gesetzt werden. (Haben Sie den Namen korrekt eingegeben?)</b>',
	'makesysop'            => 'Mache einen Benutzer zum Administrator',
	'already_sysop'        => 'Dieser Benutzer ist bereits ein Administrator.',
	'already_bureaucrat'   => 'Dieser Benutzer ist bereits ein Bürokrat.',
	'makesysop-nodatabase' => 'Falscher InterWiki Benutzername: $1',
	'group-steward'        => 'Stewards',
	'group-steward-member' => 'Steward',
	'grouppage-steward'    => '{{ns:project}}:Stewards',
);
$wgMakesysopMessages['eo'] = array(
	'group-steward'        => 'Stevardoj',
	'group-steward-member' => 'Stevardo',
);
$wgMakesysopMessages['es'] = array(
	'group-steward'        => 'Stewards',
	'group-steward-member' => 'Steward',
);
$wgMakesysopMessages['fi'] = array(
	'group-steward'        => 'ylivalvojat',
	'group-steward-member' => 'ylivalvoja',
);
$wgMakesysopMessages['he'] = array(
	'makesysoptitle'       => 'הפוך משתמש למפעיל מערכת',
	'makesysoptext'        => 'דף זה משמש ביורוקרטים להפיכת משתמש רגיל למפעיל מערכת. אנא הקישו את שם המשתמש בתיבת הטקסט ולחצו על הכפתור על מנת להפוך את המשתמש למפעיל מערכת.',
	'makesysopname'        => 'שם המשתמש:',
	'makesysopsubmit'      => 'הפוך משתמש זה למפעיל מערכת',
	'makesysopok'          => '\'\'\'המשתמש "$1" הוא עכשיו מפעיל מערכת.\'\'\'',
	'makesysopfail'        => '\'\'\'לא ניתן היה למנות את המשתמש "$1" למפעיל מערכת.\'\'\' (האם הקלדתם נכונה את שם המשתמש?)',
	'setbureaucratflag'    => 'הפוך משתמש זה לביורוקרט',
	'rights'               => 'הרשאות:',
	'set_user_rights'      => 'קבע הרשאות משתמש',
	'user_rights_set'      => '\'\'\'הרשאות המשתמש "$1" עודכנו.\'\'\'',
	'set_rights_fail'      => '\'\'\'שינוי הרשאות המשתמש "$1" נכשל.\'\'\' (האם הקלדתם נכונה את שם המשתמש?)',
	'makesysop'            => 'הפוך משתמש למפעיל מערכת',
	'already_sysop'        => 'משתמש זה הוא כבר מפעיל מערכת',
	'already_bureaucrat'   => 'משתמש זה הוא כבר ביורוקרט',
	'makesysop-nodatabase' => 'שם משתמש מרוחק שגוי: $1',
	'group-steward'        => 'דיילים',
	'group-steward-member' => 'דייל',
	'grouppage-steward'    => '{{ns:project}}:דיילים',
);
$wgMakesysopMessages['id'] = array(
	'makesysoptitle'       => 'Buat seorang pengguna menjadi pengurus',
	'makesysoptext'        => 'Formulir ini digunakan oleh para birokrat untuk menjadikan pengguna biasa menjadi seorang pengurus. Ketikkan nama pengguna yang dimaksud dalam kotak dan tekan tombol untuk menjadikan pengguna tersebut seorang pengurus.',
	'makesysopname'        => 'Nama pengguna:',
	'makesysopsubmit'      => 'Jadikan pengurus',
	'makesysopok'          => "<strong>Pengguna \"$1\" sekarang adalah seorang pengurus</strong>",
	'makesysopfail'        => "<strong>Pengguna \"$1\" tidak dapat dijadikan pengurus. (Apakah Anda mengetikkan namanya dengan benar?)</strong>",
	'setbureaucratflag'    => 'Beri tanda birokrat',
	'rights'               => 'Hak-hak:',
	'set_user_rights'      => 'Atur hak-hak pengguna',
	'user_rights_set'      => "<strong>Hak-hak untuk \"$1\" telah diperbarui</b>",
	'set_rights_fail'      => "<strong>Hak-hak untuk \"$1\" tidak dapat diperbarui. (Apakah Anda mengetikkan namanya dengan benar?)</strong>",
	'makesysop'            => 'Buat seorang pengguna menjadi pengurus',
	'already_sysop'        => 'Pengguna ini telah berstatus pengurus',
	'already_bureaucrat'   => 'Pengguna ini telah berstatus birokrat',
	'makesysop-nodatabase' => 'Nama pengguna interwiki salah:: $1',
	'group-steward'        => 'Steward',
	'group-steward-member' => 'Steward',
	'grouppage-steward'    => 'Project:Steward',
);
$wgMakesysopMessages['it'] = array(
	'group-steward'        => 'Steward',
	'group-steward-member' => 'Steward',
);
$wgMakesysopMessages['ja'] = array(
	'makesysoptitle'       => '利用者を{{int:group-sysop}}にする',
	'makesysoptext'        => 'このフォームは通常の利用者を{{int:group-sysop}}にするために使用します。{{int:group-sysop}}にする利用者名を入力し、この利用者を管理者にするボタンを押して下さい。',
	'makesysopname'        => '利用者名:',
	'makesysopsubmit'      => 'この利用者を{{int:group-sysop}}にする',
	'makesysopok'          => "<b>利用者 \"$1\" を{{int:group-sysop}}にしました。</b>",
	'makesysopfail'        => "<b>利用者 \"$1\" を{{int:group-sysop}}にできませんでした。利用者名を正しく入力していたかどうか確認してください。</b>",
	'setbureaucratflag'    => '“{{int:group-bureaucrat}}”フラグをセット',
	'rights'               => '権限:',
	'set_user_rights'      => '利用者権限の設定',
	'user_rights_set'      => "<b>利用者 \"$1\" の権限を更新しました</b>",
	'set_rights_fail'      => "<b>利用者 \"$1\" の権限を設定できませんでした。利用者名を正しく入力していたかどうか確認してください。</b>",
	'makesysop'            => '利用者を管理者にする',
	'already_sysop'        => '利用者は既に{{int:group-sysop}}です。',
	'already_bureaucrat'   => '利用者は既に{{int:group-bureaucrat}}です。',
	'makesysop-nodatabase' => 'interwiki利用者名が正しくありません: $1',
	'group-steward'        => 'スチュワード',
	'group-steward-member' => 'スチュワード',
	'grouppage-steward'    => '{{ns:project}}:{{int:group-steward}}',
);
$wgMakesysopMessages['ko'] = array(
	'group-steward'        => '스튜워드',
	'group-steward-member' => '스튜워드',
);
$wgMakesysopMessages['mk'] = array(
	'group-steward'        => 'Стјуарди',
	'group-steward-member' => 'Стјуард',
);
$wgMakesysopMessages['nl'] = array(
	'makesysoptitle'       => 'Maak een gebruiker beheerder',
	'makesysoptext'        => 'Dit formulier wordt door bureaucraten gebruikt om een gebruiker beheerder te maken. Geef de naam van een gebruiker in het veld in en klik op de knop om de gebruiker beheerder te maken.',
	'makesysopname'        => 'Gebruikersnaam:',
	'makesysopsubmit'      => 'Wijzig de gebruikersrechten',
	'makesysopok'          => "<b>Gebruiker \"$1\" is nu beheerder</b>",
	'makesysopfail'        => "<b>Gebruiker \"$1\" kon geen beheerder gemaakt worden. Heeft u de juiste naam opgegeven?</b>",
	'setbureaucratflag'    => 'Maak deze gebruiker ook bureaucraat',
	'rights'               => 'Rechten:',
	'set_user_rights'      => 'Gebruikersrechten aanpassen',
	'user_rights_set'      => "<b>Gebruikersrechten van \"$1\" bijgewerkt</b>",
	'set_rights_fail'      => "<b>Gebruikersrechten van \"$1\" konden niet worden aangepast. Heeft u de juiste naam opgegeven?</b>",
	'makesysop'            => 'Maak een gebruiker beheerder',
	'already_sysop'        => 'Deze gebruiker is al beheerder',
	'already_bureaucrat'   => 'Deze gebruiker is al bureaucraat',
	'makesysop-nodatabase' => 'Onjuiste interwikigebruikersnaam: $1',
);
$wgMakesysopMessages['no'] = array(
	'group-steward'        => 'Stewards',
	'group-steward-member' => 'Steward',
);
$wgMakesysopMessages['pl'] = array(
	'makesysoptitle'       => 'Nadaj użytkownikowi uprawnienia administratora',
	'makesysoptext'        => 'Ten formularz jest wykorzystywany przez użytkowników o statusie biurokraty do przyznawania innym użytkownikom praw administratora. Aby to uczynić, wpisz nazwę użytkownika i kliknij na przycisk.',
	'makesysopname'        => 'Nazwa użytkownika:',
	'makesysopsubmit'      => 'Przyznaj temu użytkownikowi uprawnienia administratora',
	'makesysopok'          => "<b>Użytkownik \"$1\" otrzymał uprawnienia administratora</b>",
	'makesysopfail'        => "<b>Użytkownik \"$1\" nie otrzymał uprawnienień administratora. (Czy wprowadziłeś poprawną nazwę użytkownika?)</b>",
	'setbureaucratflag'    => 'Ustaw status biurokraty',
	'rights'               => 'Uprawnienia:',
	'set_user_rights'      => 'Zmień uprawnienia użytkownika',
	'user_rights_set'      => "<b>Uprawnienia użytkownika \"$1\" zostały zmienione</b>",
	'set_rights_fail'      => "<b>Uprawnienia użytkownika \"$1\" nie zostały zmienione. (Czy wprowadziłeś poprawną nazwę użytkownika?)</b>",
	'makesysop'            => 'Przyznaj użytkownikowi uprawnienia administratora',
	'already_sysop'        => 'Ten użytkownik jest już administratorem',
	'already_bureaucrat'   => 'Ten użytkownik jest już biurokratą',
	'makesysop-nodatabase' => 'Niewłaściwa nazwa użytkownika w interwiki: $1',
	'group-steward'        => 'Zarządcy',
	'group-steward-member' => 'Zarządca',
	'grouppage-steward'    => '{{ns:project}}:Stewards',
);
$wgMakesysopMessages['pms'] = array(
	'group-steward'        => 'Controlor',
	'group-steward-member' => 'Controlor',
);
$wgMakesysopMessages['pt'] = array(
	'makesysoptitle'       => 'Tornar um utilizador num administrador',
	'makesysoptext'        => 'Este formulário é utilizado por burocratas para tornar utilizadores comuns em administradores. Introduza o nome do utilizador na caixa e clique no botão para tornar o utilizador num administrador.',
	'makesysopname'        => 'Nome do utilizador:',
	'makesysopsubmit'      => 'Tornar este utilizar num administrador',
	'makesysopok'          => "<b>Utilizador \"$1\" é agora um administrador</b>",
	'makesysopfail'        => "<b>Não foi possível tornar o utilizador \"$1\" num administrador. (Introduziu o nome correctamente?)</b>",
	'setbureaucratflag'    => 'Atribuir flag de burocrata',
	'rights'               => 'Direitos:',
	'set_user_rights'      => 'Definir direitos de utilizador',
	'user_rights_set'      => "<b>Direitos de utilizador para \"$1\" actualizados</b>",
	'set_rights_fail'      => "<b>Direitos de utilizador para \"$1\" não poderam ser definidos. (Introduziu o nome correctamente?)</b>",
	'makesysop'            => 'Tornar um utilizador num administrador',
	'already_sysop'        => 'Este utilizador já é um administrador',
	'already_bureaucrat'   => 'Este utilizador já é um burocrata',
	'makesysop-nodatabase' => 'Interwiki do utilizador inválido: $1',
	'group-steward'        => 'Stewards',
	'group-steward-member' => 'Steward',
	'grouppage-steward'    => '{{ns:project}}:Stewards',
);
$wgMakesysopMessages['ro'] = array(
	'group-steward'        => 'Stewarzi',
	'group-steward-member' => 'Steward',
);
$wgMakesysopMessages['ru'] = array(

	'makesysoptitle'       => 'Сделать пользователя администратором',
	'makesysoptext'        => 'Эта форма предназначена для бюрократов, и позволяет присваивать участникам статус администратора. Наберите имя участника и нажмите кнопку, чтобы сделать участника администратором.',
	'makesysopname'        => 'Имя участника:',
	'makesysopsubmit'      => 'Сделать этого участника администратором',
	'makesysopok'          => "<b>Участнику $1 присвоен статус администратора</b>",
	'makesysopfail'        => "<b>Участника $1 невозможно сделать администратором. (Вы уверены, что правильно ввели его имя?)</b>",
	'setbureaucratflag'    => 'Установить флаг «Бюрократ»',
	'rights'               => 'Права:',
	'set_user_rights'      => 'Установить права пользователя',
	'user_rights_set'      => "<b>Права пользователя $1 обновлены</b>",
	'set_rights_fail'      => "<b>Невозможно установить права для пользователя $1. (Проверьте, правильно ли введено его имя)</b>",
	'makesysop'            => 'Присвоить участнику статус администратора',
	'already_sysop'        => 'Этот участник уже является администратором',
	'already_bureaucrat'   => 'Этот участник уже является бюрократом',
	'makesysop-nodatabase' => 'Неверное интервики-имя участника: $1',
	'group-steward'        => 'Стюарды',
	'group-steward-member' => 'стюард',
	'grouppage-steward'    => '{{ns:project}}:Стюарды',
);
$wgMakesysopMessages['sk'] = array(
	'group-steward'        => 'Stewardi',
	'group-steward-member' => 'Steward',
);
$wgMakesysopMessages['sl'] = array(
	'group-steward'        => 'Upravniki',
	'group-steward-member' => 'Upravnik',
);
$wgMakesysopMessages['sq'] = array(
	'group-steward'        => 'Përgjegjës',
	'group-steward-member' => 'Përgjegjës',
);
$wgMakesysopMessages['sr-ec'] = array(
	'group-steward'        => 'стјуарди',
	'group-steward-member' => 'стјуард',
);
$wgMakesysopMessages['sr-el'] = array(
	'group-steward'        => 'stjuardi',
	'group-steward-member' => 'stjuard',
);
$wgMakesysopMessages['sv'] = array(
	'group-steward'        => 'Stewarder',
	'group-steward-member' => 'Steward',
);
$wgMakesysopMessages['wa'] = array(
	'group-steward'        => 'Mwaisse-manaedjeus tot avå',
	'group-steward-member' => 'Mwaisse-manaedjeu tot avå',
);
$wgMakesysopMessages['zh-cn'] = array(
	'makesysoptitle'       => '将用户设置为操作员',
	'makesysoptext'        => '这个由行政管理员使用的表单可以将普通用户变为管理员。在输入框中输入用户名并点击按钮就可以将用户设置为管理员。',
	'makesysopname'        => '用户名：',
	'makesysopsubmit'      => '将这位用户设置为操作员',
	'makesysopok'          => "<b>用户\"$1\"现在是操作员了</b>",
	'makesysopfail'        => "<b>不能把用户\"$1\"设置为操作员。（你输入的用户名正确吗？）</b>",
	'setbureaucratflag'    => '设置行政员标记',
	'rights'               => '权限：',
	'set_user_rights'      => '设定用户权限',
	'user_rights_set'      => "<b>用户\"$1\"的权限已经更新</b>",
	'set_rights_fail'      => "<b>无法设定用户\"$1\"的权限。（您是否输入了正确的用户名？）</b>",
	'makesysop'            => '将一名用户设为操作员',
	'already_sysop'        => '这位用户已经是一名管理员',
	'already_bureaucrat'   => '这位用户已经是一名行政员',
	'makesysop-nodatabase' => '错误的跨维基用户名：$1',
	'group-steward'        => '监管员',
	'group-steward-member' => '监管员',
	'grouppage-steward'    => '{{ns:project}}:监管员',
);
$wgMakesysopMessages['zh-hk'] = array(
	'makesysoptitle'       => '將用戶設置為操作員',
	'makesysoptext'        => '這個由行政管理員使用的表單可以將普通用戶變為管理員。在輸入框中輸入用戶名並點擊按鈕就可以將用戶設置為管理員。',
	'makesysopname'        => '用戶名：',
	'makesysopsubmit'      => '將這位用戶設置為操作員',
	'makesysopok'          => "<b>用戶\"$1\"現在是操作員了</b>",
	'makesysopfail'        => "<b>不能把用戶\"$1\"設置為操作員。（你輸入的用戶名正確嗎？）</b>",
	'setbureaucratflag'    => '設置行政員標記',
	'rights'               => '權限：',
	'set_user_rights'      => '設定用戶權限',
	'user_rights_set'      => "<b>用戶\"$1\"的權限已經更新</b>",
	'set_rights_fail'      => "<b>無法設定用戶\"$1\"的權限。（您是否輸入了正確的用戶名？）</b>",
	'makesysop'            => '將一名用戶設為操作員',
	'already_sysop'        => '這位用戶已經是一名管理員',
	'already_bureaucrat'   => '這位用戶已經是一名行政員',
	'makesysop-nodatabase' => '錯誤的跨維基用戶名：$1',
	'group-steward'        => '監管員',
	'group-steward-member' => '監管員',
	'grouppage-steward'    => '{{ns:project}}:監管員',
);
$wgMakesysopMessages['zh-tw'] = array(
	'makesysoptitle'       => '將用戶設置為操作員',
	'makesysoptext'        => '這個由行政管理員使用的表單可以將普通用戶變為管理員。在輸入框中輸入用戶名並點擊按鈕就可以將用戶設置為管理員。',
	'makesysopname'        => '用戶名：',
	'makesysopsubmit'      => '將這位用戶設置為操作員',
	'makesysopok'          => "<b>用戶\"$1\"現在是操作員了</b>",
	'makesysopfail'        => "<b>不能把用戶\"$1\"設置為操作員。（你輸入的用戶名正確嗎？）</b>",
	'setbureaucratflag'    => '設置行政員標記',
	'rights'               => '許可權：',
	'set_user_rights'      => '設定用戶許可權',
	'user_rights_set'      => "<b>用戶\"$1\"的許可權已經更新</b>",
	'set_rights_fail'      => "<b>無法設定用戶\"$1\"的許可權。（您是否輸入了正確的用戶名？）</b>",
	'makesysop'            => '將一名用戶設為操作員',
	'already_sysop'        => '這位用戶已經是一名管理員',
	'already_bureaucrat'   => '這位用戶已經是一名行政員',
	'makesysop-nodatabase' => '錯誤的跨維基用戶名：$1',
	'group-steward'        => '監管員',
	'group-steward-member' => '監管員',
	'grouppage-steward'    => '{{ns:project}}:監管員',
);
$wgMakesysopMessages['zh-yue'] = array(
	'makesysoptitle'       => '整一位用戶做操作員',
	'makesysoptext'        => '呢一個表格係畀事務員用嘅，去轉普通嘅用戶到管理員。
喺個框入面打用戶嘅名，然後撳個掣嚟設佢為管理員',
	'makesysopname'        => '用戶嘅名：',
	'makesysopsubmit'      => '整呢位用戶做一位操作員',
	'makesysopok'          => "<b>用戶 \"$1\" 而家係一位操作員</b>",
	'makesysopfail'        => "<b>用戶 \"$1\" 唔可以整做一位操作員。（你有冇正確噉樣輸入正確嘅名？）</b>",
	'setbureaucratflag'    => '設定事務員旗',
	'rights'               => '權力:',
	'set_user_rights'      => '設定用戶權限',
	'user_rights_set'      => "<b>用戶 \"$1\" 嘅權限已經更新</b>",
	'set_rights_fail'      => "<b>無法設定用戶 \"$1\" 嘅權限。（你有冇打啱個名？）</b>",
	'makesysop'            => '整一位用戶做操作員',
	'already_sysop'        => '呢位用戶已經係一位管理員',
	'already_bureaucrat'   => '呢位用戶已經係一位事務員',
	'makesysop-nodatabase' => '錯誤嘅跨維基用戶名：$1',
	'group-steward'        => '執行員',
	'group-steward-member' => '執行員',
	'grouppage-steward'    => '{{ns:project}}:執行員',);

$wgMakesysopMessages['zh-sg'] = $wgMakesysopMessages['zh-cn'];
?>
