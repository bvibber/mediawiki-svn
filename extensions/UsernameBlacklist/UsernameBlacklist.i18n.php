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
'blacklistedusername'     => 'Blacklisted username',
'blacklistedusernametext' => 'The user name you have chosen matches the [[MediaWiki:Usernameblacklist|list of blacklisted usernames]]. Please choose another name.',
'usernameblacklist' => '<pre>
# Entries in this list will be used as part of a regular expression when
# blacklisting usernames from registration. Each item should be part of
# a bulleted list, e.g.
#
# * Foo
# * [Bb]ar
</pre>',

),

/* Arabic (Meno25) */
'ar' => array(
'blacklistedusername' => 'اسم مستخدم في القائمة السوداء',
'blacklistedusernametext' => 'اسم المستخدم الذي اخترته يطابق [[MediaWiki:Usernameblacklist|
قائمة أسماء المستخدمين السوداء]]. من فضلك اختر اسما آخر.',
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

/* Italian (BrokenArrow) */
'it' => array(
'blacklistedusername' => 'Nome utente non consentito',
'blacklistedusernametext' => 'Il nome utente scelto è inserito nella [[MediaWiki:Usernameblacklist|lista dei nomi non consentiti]]. Si prega di scegliere un altro nome.',
),

/* Kazakh Cyrillic (kk:AlefZet) */
'kk-kz' => array(
'blacklistedusername' => 'Қара тізімдегі қатысушы аты',
'blacklistedusernametext' => 'Тандаған қатысушы атыңыз [[{{ns:mediawiki}}:Usernameblacklist| қатысушы аты қара тізіміне]] кіреді.
Басқа атау талғаңыз.',
),
/* Kazakh Latin (kk:AlefZet) */
'kk-tr' => array(
'blacklistedusername' => 'Qara tizimdegi qatıswşı atı',
'blacklistedusernametext' => 'Tandağan qatıswşı atıñız [[{{ns:mediawiki}}:Usernameblacklist| qatıswşı atı qara tizimine]] kiredi.
Basqa ataw talğañız.',
),
/* Kazakh Arabic (kk:AlefZet) */
'kk-cn' => array(
'blacklistedusername' => 'قارا تٸزٸمدەگٸ قاتىسۋشى اتى',
'blacklistedusernametext' => 'تانداعان قاتىسۋشى اتىڭىز [[{{ns:mediawiki}}:Usernameblacklist| قاتىسۋشى اتى قارا تٸزٸمٸنە]] كٸرەدٸ.
باسقا اتاۋ تالعاڭىز.',
),

/* Kurdi */
'ku' => array(
'blacklistedusernametext' => 'Wê navî yê te hilbijart li ser [[MediaWiki:Usernameblacklist|lîstêya navên nebaş]] e. Xêra xwe navekî din hilbijêre.',
),

/* Lao */
'lo' => array(
'blacklistedusername' => 'ຊື່ຜູ້ໃຊ້ ໃນ ບັນຊີດຳ',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
'blacklistedusername' => 'Gebruikersnaam op zwarte lijst',
'blacklistedusernametext' => 'De gebruikersnaam die u heeft gekozen staat op de [[MediaWiki:Usernameblacklist|
zwarte lijst van gebruikersnamen]]. Kies alstublieft een andere naam.',
),

/* Norwegian (Jon Harald Søby) */
'no' => array(
'blacklistedusername' => 'Svartelistet brukernavn',
'blacklistedusernametext' => 'Brukernavnet du har valgt tilsvarer et navn på [[MediaWiki:Usernameblacklist|listen over svartelistede brukernavn]]. Velg et annet navn.',
),

/* Occitan (Cedric31) */
'oc' => array(
'blacklistedusername' => 'Noms d’utilizaires en lista negra',
'blacklistedusernametext' => 'Lo nom d’utilizaire qu\'avètz causit se tròba sus la [[MediaWiki:Usernameblacklist|lista dels noms interdiches]]. Causissètz un autre nom.',
),

/* Piedmontese (Bèrto 'd Sèra) */
'pms' => array(
'blacklistedusername' => 'Stranòm vietà',
'blacklistedusernametext' => 'Lë stranòm ch\'a l\'ha sërnusse a l\'é ant la [[MediaWiki:Usernameblacklist|lista djë stranòm vietà]]. Për piasì, ch\'as në sërna n\'àotr.',
),

/* Portuguese (Lugusto) */
'pt' => array(
'blacklistedusername' => 'Nome de utilizador na lista negra',
'blacklistedusernametext' => 'O nome de utilizador selecionado é similar a um presente na [[MediaWiki:Usernameblacklist|
lista negra de nomes de utilizadores]]. Por gentileza, escolha outro.',
),

/* Russian */
'ru' => array(
'blacklistedusername' => 'Запрещённое имя пользователя',
'blacklistedusernametext' => 'Имя пользователя, которое вы выбрали, находится в [[MediaWiki:Usernameblacklist|
списке запрещённых имён]]. Пожалуйста, выберите другое имя.',
),

/* Slovak (helix84) */
'sk' => array(
'blacklistedusername' => 'Používateľské meno na čiernej listine',
'blacklistedusernametext' => 'Používateľské meno, ktoré ste si zvolili sa nachádza na [[MediaWiki:Usernameblacklist|
čiernej listine používateľských mien]]. Prosím, zvoľte si iné.',
'usernameblacklist' => '<pre>
# Položky z tohto zoznamu sa použijú ako časť regulárneho výrazu pre
# zamedzenie vytvorenia účtu s daným používateľským menom. Každá položka 
# musí byť ako odrážka v zozname, napr.:
#
# * Foo
# * [Bb]ar
</pre>',
),

/* Sundanese (Irwangatot via BetaWiki) */
'su' => array(
'blacklistedusername' => 'Ngaran pamaké nu dicorét:',
'blacklistedusernametext' => 'Ngaran pamaké nu dipilih cocog jeung [[MediaWiki:Usernameblacklist|ngaran pamaké nu dicorét]]. Mangga pilih ngaran séjén.',
),

/* Swedish */
'sv' => array(
'blacklistedusername' => 'Svartlistat användarnamn',
'blacklistedusernametext' => 'Det användarnamn du vill använda är [[MediaWiki:Usernameblacklist|svartlistat]]. Välj ett annat namn.',
),

/* Cantonese (Shinjiman) */
'yue' => array(
'blacklistedusername' => '列入黑名單嘅用戶名',
'blacklistedusernametext' => '你所揀嘅用戶名係同[[MediaWiki:Usernameblacklist|用戶名黑名單一覽]]符合。請揀過另一個名喇。',
),

/* Chinese (Simplified) (Shinjiman) */
'zh-hans' => array(
'blacklistedusername' => '列入黑名单的用户名',
'blacklistedusernametext' => '您所选择的用户名是与[[MediaWiki:Usernameblacklist|用户名黑名单列表]]匹配。请选择另一个名称。',
),

/* Chinese (Traditional) (Shinjiman) */
'zh-hant' => array(
'blacklistedusername' => '列入黑名單的用戶名',
'blacklistedusernametext' => '您所選擇的用戶名是與[[MediaWiki:Usernameblacklist|用戶名黑名單列表]]符合。請選擇另一個名稱。',
),

	);

	/* Kazakh default, fallback to kk-kz */
	$messages['kk'] = $messages['kk-kz'];

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];

	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $single ? $messages['en'] : $messages;
}




