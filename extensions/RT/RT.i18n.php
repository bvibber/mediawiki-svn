<?php
/**
 * Internationalisation file for the RT extension.
 *
 * @ingroup Extensions
 */

/**
 * Get all extension messages
 *
 * @return array
 */

$messages = array();

/** English
 *  Greg Sabino Mullane <greg@endpoint.com>
 */
$messages['en'] = array(
	'rt-desc'         => 'Fancy interface to RT (Request Tracker)',
	'rt-inactive'     => 'The RT extension is not active',
	'rt-badquery'     => 'The RT extension encountered an error when talking to the RT database',
	'rt-badlimit'     => "Invalid LIMIT (l) arg: must be a number.
You tried: '''\$1'''",
	'rt-badorderby'   => "Invalid ORDER BY (ob) arg: must be a standard field (see documentation).
You tried: '''\$1'''",
	'rt-badstatus'    => "Invalid status (s) arg: must be a standard field (see documentation).
You tried: '''\$1'''",
	'rt-badqueue'     => "Invalid queue (q) arg: must be a simple word.
You tried: '''\$1'''",
	'rt-badowner'     => "Invalid owner (o) arg: must be a valid username.
You tried: '''\$1'''",
	'rt-nomatches'    => 'No matching RT tickets were found',
);

/** Message documentation (Message documentation)
 * @author McDutchie
 * @author Purodha
 */
$messages['qqq'] = array(
	'rt-desc' => 'Short description of this extension, shown on [[Special:Version]]. Do not translate or change link targets or tag names.',
	'rt-badlimit' => '* \'\'\'Do not translate "LIMIT (l)".\'\'\' The "l" is a lowercase L.
* Translate "arg" as argument, parameter.',
	'rt-badorderby' => "* '''Do not translate \"ORDER BY (ob)\".'''
* Translate \"arg\" as argument, parameter.",
	'rt-badstatus' => "* '''Do not translate \"status (s)\".'''
* Translate \"arg\" as argument, parameter.",
	'rt-badqueue' => "* '''Do not translate \"queue (q)\".'''
* Translate \"arg\" as argument, parameter.",
	'rt-badowner' => "* '''Do not translate \"owner (o)\".'''
* Translate \"arg\" as argument, parameter.",
);

/** Arabic (العربية)
 * @author OsamaK
 */
$messages['ar'] = array(
	'rt-desc' => 'واجهة فاخرة لمتتبع الطلبات',
	'rt-inactive' => 'امتداد متتبع الطلبات غير مُفعّل',
	'rt-badquery' => 'واجه امتداد متتبع الطلبات خطأً أثناء التخاطب مع قاعدة بيانات متتبع الطلبات',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author McDutchie
 */
$messages['be-tarask'] = array(
	'rt-inactive' => 'Пашырэньне RT не ўключана',
	'rt-badquery' => 'У пашырэньні RT узьнікла памылка пад час абмену зьвесткамі з базай зьвестак RT',
	'rt-badlimit' => "Няслушны аргумэнт LIMIT (l): ён павінен быць лікай.
Вы спрабавалі ўвесьці: '''$1'''",
	'rt-badorderby' => "Няслушны аргумэнт ORDER BY (ob): ён павінен мець стандартны выгляд (глядзіце дакумэнтацыю).
Вы спрабавалі ўвесьці: '''$1'''",
	'rt-badstatus' => "Няслушны аргумэнт status (s): ён павінен мець стандартны выгляд (глядзіце дакумэнтацыю).
Вы спрабавалі ўвесьці: '''$1'''",
	'rt-badqueue' => "Няслушны аргумэнт queue (q): ён павінен быць простам словам.
Вы спрабавалі ўвесьці: '''$1'''",
	'rt-badowner' => "Няслушны аргумэнт owner (o): павінна быць існуючае імя ўдзельніка.
Вы спрабавалі ўвесьці: '''$1'''",
	'rt-nomatches' => 'Ня знойдзена супадзеньняў з RT',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'rt-desc' => 'Moderni interfejs za RT (Praćenje zahtjeva)',
	'rt-inactive' => 'RT proširenje nije aktivno',
	'rt-badquery' => 'RT proširenje je javilo grešku pri kontaktiranju RT baze podataka',
	'rt-badlimit' => "Nevaljan LIMIT (l) argument: mora biti broj.
Pokušali ste: '''$1'''",
	'rt-badorderby' => "Nevaljan ORDER BY (ob) argument: mora biti standardno polje (vidi dokumentaciju).
Pokušali ste: '''$1'''",
	'rt-badstatus' => "Nevaljan status (s) argument: mora biti standardno polje (vidi dokumentaciju).
Pokušali ste: '''$1'''",
	'rt-badqueue' => "Nevaljan queue (q) argument: mora biti jednostavna riječ.
Pokušali ste: '''$1'''",
	'rt-badowner' => "Nevaljan owner (o) argument: mora biti validno korisničko ime.
Vi ste pokušali: '''$1'''",
	'rt-nomatches' => 'Nisu pronađeni odgovarajući RT kuponi',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 */
$messages['fr'] = array(
	'rt-desc' => 'Interface pour RT (Request Tracker)',
	'rt-inactive' => 'L’extension RT n’est pas active',
	'rt-badquery' => "L'extension RT a rencontré une erreur lors d'une requête sur la base de données de RT",
	'rt-badlimit' => "Argument LIMIT (l) invalide : il doit être un nombre.
Vous avez essayé : '''$1'''",
	'rt-badorderby' => "Argument ORDER BY (ob) invalide : il doit être un champ standard (voir la documentation).
Vous avez essayé : '''$1'''",
	'rt-badstatus' => "Argument status (s) invalide : il doit être un champ standard (voir la documentation).
Vous avez essayé : '''$1'''",
	'rt-badqueue' => "Argument queue (q) invalide : il doit être un mot simple.
Vous avez essayé : '''$1'''",
	'rt-badowner' => "Argument owner (o) invalide : il doit être un nom d'utilisateur valide.
Vous avez essayé : '''$1'''",
	'rt-nomatches' => "Aucun ticket RT n'a été trouvé",
);

/** Galician (Galego)
 * @author McDutchie
 * @author Toliño
 */
$messages['gl'] = array(
	'rt-desc' => "Interface da extensión RT (''Request Tracker'')",
	'rt-inactive' => 'A extensión RT non está activa',
	'rt-badquery' => 'A extensión RT atopou un erro ao conectar coa súa base de datos',
	'rt-badlimit' => "O parámetro LIMIT (l) é inválido: debe ser un número.
Vostede tentou: '''$1'''",
	'rt-badorderby' => "O parámetro ORDER BY (ob) é inválido: debe ser un campo estándar (véxase a documentación).
Vostede tentou: '''$1'''",
	'rt-badstatus' => "O parámetro status (s) é inválido: debe ser un campo estándar (véxase a documentación).
Vostede tentou: '''$1'''",
	'rt-badqueue' => "O parámetro queue (q) é inválido: debe ser unha palabra sinxela.
Vostede tentou: '''$1'''",
	'rt-badowner' => "O parámetro owner (o) é inválido: debe ser un nome de usuario válido.
Vostede tentou: '''$1'''",
	'rt-nomatches' => 'Non se atoparon boletos RT que coincidisen',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'rt-desc' => 'ממשק מהודר למעקב הבקשות (RT)',
	'rt-inactive' => 'הרחבת ה־RT אינה פעילה',
	'rt-badquery' => 'הרחבת ה־RT נתקלה בשגיאה בעת ההתקשרות עם בסיס הנתונים של RT',
	'rt-nomatches' => 'לא נמצאו כרטיסיות RT תואמות',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'rt-desc' => 'Interfacie elegante a RT (Request Tracker)',
	'rt-inactive' => 'Le extension RT non es active',
	'rt-badquery' => 'Le extension RT incontrava un error durante le communication con le base de datos RT',
	'rt-badlimit' => "Le parametro LIMIT (l) es invalide: debe esser un numero.
Tu entrava: '''$1'''",
	'rt-badorderby' => "Le parametro ORDER BY (ob) es invalide: debe esser un campo standard (vide le documentation).
Tu entrava: '''$1'''",
	'rt-badstatus' => "Le parametro status (s) es invalide: debe esser un campo standard (vide le documentation).
Tu entrava: '''$1'''",
	'rt-badqueue' => "Le parametro queue (q) es invalide: debe esser un parola simple.
Tu entrava: '''$1'''",
	'rt-badowner' => "Le parametro owner (o) es invalide: debe esser un nomine de usator valide.
Tu entrava: '''$1'''",
	'rt-nomatches' => 'Nulle billet RT trovate que corresponde a iste criterios',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'rt-desc' => '要求追跡 (RT) システムの装飾的なインタフェース',
	'rt-inactive' => 'RT 拡張機能は無効になっています',
	'rt-badquery' => 'RT 拡張機能は、RT データベースとの通信時にエラーに遭遇しました',
	'rt-badlimit' => "LIMIT (l) の引数が不正: 数値でなくてはなりません。
与えられた値: '''$1'''",
	'rt-badorderby' => "ORDER BY (ob) の引数が不正: 標準のフィールドでなくてはなりません（ドキュメントを参照）。
与えられた値: '''$1'''",
	'rt-badstatus' => "status (s) の引数が不正: 標準のフィールドでなくてはなりません（ドキュメントを参照）。
与えられた値: '''$1'''",
	'rt-badqueue' => "queue (q) の引数が不正: 単純な語でなくてはなりません。
与えられた値: '''$1'''",
	'rt-badowner' => "owner (o) の引数が不正: 有効な利用者名でなくてはなりません。
与えられた値: '''$1'''",
	'rt-nomatches' => '一致する RT チケットは見つかりませんでした',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'rt-desc' => 'Ene schecke Zohjreff op dä RT (<i lang="en">Request Tracker</i>) för de Aanforderungs-Verfoljung.',
	'rt-inactive' => 'Dä RT (<i lang="en">Request Tracker</i>) för de Aanforderungs-Verfoljung es nit aanjeschalldt.',
	'rt-badquery' => 'Däm RT (<i lang="en">Request Tracker</i>) för de Aanforderungs-Verfoljung sing Datebangkzohjreff meldt ene Fähler.',
	'rt-badlimit' => 'Dinge Versooch met „$1“ för dä Parrameeter „<code lang="en">LIMIT (l)</code>“ wor nix. De häts en Zahl aanjävve möße.',
	'rt-badorderby' => 'Dinge Versooch met „$1“ för dä Parrameeter „<code lang="en">ORDER BY (ob)</code>“ wohr nix. De häts en shtandatt Feld aanjävve möße. Loor Der de Dokkemäntazjuhn aan, wat dat es.',
	'rt-badstatus' => 'Dinge Versooch met „$1“ för dä Parrameeter „<code lang="en">status (s)</code>“ wohr nix. De häts e shtandatt Feld aanjävve möße. Loor Der de Dokkemäntazjuhn aan, wat dat es.',
	'rt-badqueue' => 'Dinge Versooch met „$1“ för dä Parrameeter „<code lang="en">queue (q)</code>“ wohr nix. De häts e eijnfach Woot aanjävve möße.',
	'rt-badowner' => 'Dinge Versooch met „$1“ för dä Parrameeter „<code lang="en">owner (o)</code>“ wohr nix. De häts ene jöltijje Name för ene Metmaacher aanjävve möße.',
	'rt-nomatches' => 'Mer hann kein zopaß Aanforderunge (ov <i lang="en">tickets</i>) em RT (<i lang="en">Request Tracker</i>) för de Aanforderungs-Verfoljung jevonge.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'rt-inactive' => "D'RT-Erweiderung ass net aktiv",
	'rt-badquery' => "D'RT-Erweiderung hat bäi der Kommunikatioun mat der RT-Datebank e Problem",
);

/** Dutch (Nederlands)
 * @author McDutchie
 * @author Siebrand
 */
$messages['nl'] = array(
	'rt-desc' => 'Interface naar RT (Request Tracker)',
	'rt-inactive' => 'De uitbreiding RT is niet actief',
	'rt-badquery' => 'In de uitbreiding RT is een fout opgetreden in de communicatie met de RT-database',
	'rt-badlimit' => "Ongeldige parameter LIMIT (l): moet een getal zijn.
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badorderby' => "Ongeldige parameter ORDER BY (ob): moet een standaard veld zijn (zie documentatie).
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badstatus' => "Ongeldige parameterstatus (s): moet een standaard veld zijn (zie documentatie).
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badqueue' => "Ongeldige parameter queue (q): moet een eenvoudig woord zijn.
U hebt het volgende geprobeerd: '''$1'''",
	'rt-badowner' => "Ongeldige parameter owner (o): moet een geldige gebruikersnaam zijn.
U hebt het volgende geprobeerd: '''$1'''",
	'rt-nomatches' => 'Er zijn geen RT-meldingen gevonden die aan de critera voldoen',
);

/** Portuguese (Português)
 * @author Lijealso
 * @author Waldir
 */
$messages['pt'] = array(
	'rt-inactive' => 'A extensão RT não está ativa',
	'rt-badquery' => 'A extensão RT encontrou um erro ao comunicar-se com a base de dados RT',
	'rt-nomatches' => 'Não foram encontrados bilhetes RT correspondentes',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 */
$messages['pt-br'] = array(
	'rt-desc' => 'Interface para o RT (Request Tracker)',
	'rt-inactive' => 'A extensão RT não está ativa',
	'rt-badquery' => 'A extensão RT encontrou um erro enquanto se comunicava com a base de dados do RT',
	'rt-badlimit' => "Parâmetro LIMIT (l) inválido: é necessário que seja um número.
Você tentou: '''$1'''",
	'rt-badorderby' => "Parâmetro ORDER BY (ob) inválido: é necessário que seja um campo padrão (veja a documentação).
Você tentou: '''$1'''",
	'rt-badstatus' => "Parâmetro status (s) inválido: é necessário que seja um campo padrão (veja a documentação).
Você tentou: '''$1'''",
	'rt-badqueue' => "Parâmetro queue (q) inválido: é preciso que seja uma palavra simples.
Você tentou: '''$1'''",
	'rt-badowner' => "Parâmetro owner (o) inválido: é necessário que seja um nome de utilizador válido.
Você tentou: '''$1'''",
	'rt-nomatches' => 'Nenhum ticket RT correspondente foi encontrado',
);

/** Russian (Русский)
 * @author Ferrer
 */
$messages['ru'] = array(
	'rt-inactive' => 'Расширение RT неактивно',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'rt-desc' => 'Pekné rozhranie k sledovaniu požiadaviek (RT - Request Tracker)',
	'rt-inactive' => 'Rozšírenie RT nie je aktívne',
	'rt-badquery' => 'V rozšírení RT nastala chyba pri komunikácii s databázou RT',
	'rt-badlimit' => "Neplatný argument LIMIT (l): musí byť číslo.
Skúšali ste: '''$1'''",
	'rt-badorderby' => "Neplatný argument ORDER BY (ob): musí byť štandardné pole (pozri dokumentáciu).
Skúšali ste: '''$1'''",
	'rt-badstatus' => "Neplatný argument status (s): musí byť štandardné pole (pozri dokumentáciu).
Skúšali ste: '''$1'''",
	'rt-badqueue' => "Neplatný argument queue (q): musí byť jednoduché slovo.
Skúšali ste: '''$1'''",
	'rt-badowner' => "Neplatný argument owner (o): musí byť platné používateľské meno.
Skúšali ste: '''$1'''",
	'rt-nomatches' => 'Neboli nájdené zodpovedajúce požiadavky v RT',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'rt-desc' => 'Giao diện đẹp cho RT (Request Tracker - Yêu cầu Bộ dò)',
	'rt-inactive' => 'Phần mở rộng RT không được kích hoạt',
	'rt-badquery' => 'Phần mở rộng RT gặp lỗi khi trao đổi với cơ sở dữ liệu RT',
	'rt-badlimit' => "Thông số LIMIT (l) sai: phải sử dụng số.
Bạn đã gọi: '''$1'''",
	'rt-badorderby' => "Thông số ORDER BY (ob) sai: phải là một trường chuẩn (xem tài liệu đi kèm).
Bạn đã gọi: '''$1'''",
	'rt-badstatus' => "Thông số status (s) sai: phải là một trường chuẩn (xem tài liệu đi kèm).
Bạn đã gọi: '''$1'''",
	'rt-badqueue' => "Thông số queue (q) sai: phải là từ đơn giản.
Bạn đã gọi: '''$1'''",
	'rt-badowner' => "Thông số owner (o) sai: phải là một tên người dùng đã có.
Bạn đã gọi: '''$1'''",
	'rt-nomatches' => 'Không tìm thấy thẻ RT nào trùng với tìm kiếm',
);

