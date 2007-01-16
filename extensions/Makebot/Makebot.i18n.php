<?php

/**
 * Internationalisation file for the MakeBot extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */

function efMakeBotMessages() {
	$messages = array(
	
/* English (Rob Church) */
'en' => array(
'makebot' => 'Grant or revoke bot status',
'makebot-header' => "'''A local bureaucrat can use this page to grant or revoke [[Help:Bot|bot status]] to another user account.'''<br />Bot status hides a user's edits from [[Special:Recentchanges|recent changes]] and similar lists, and is useful for flagging users who make automated edits. This should be done in accordance with applicable policies.",
'makebot-username' => 'Username:',
'makebot-search' => 'Go',
'makebot-isbot' => '[[User:$1|$1]] has bot status.',
'makebot-notbot' => '[[User:$1|$1]] does not have bot status.',
'makebot-privileged' => '[[User:$1|$1]] has [[Special:Listadmins|administrator or bureaucrat privileges]], and cannot be granted bot status.',
'makebot-change' => 'Change status:',
'makebot-grant' => 'Grant',
'makebot-revoke' => 'Revoke',
'makebot-comment' => 'Comment:',
'makebot-granted' => '[[User:$1|$1]] now has bot status.',
'makebot-revoked' => '[[User:$1|$1]] no longer has bot status.',
'makebot-logpage' => 'Bot status log',
'makebot-logpagetext' => 'This is a log of changes to users\' [[Help:Bot|bot]] status.',
'makebot-logentrygrant' => 'granted bot status to [[$1]]',
'makebot-logentryrevoke' => 'removed bot status from [[$1]]',
),

/* Old Church Slavonic (language file) */
'cu' => array(
'makebot-search' => 'Прѣиди',
),

/* Czech (bug 8455) */
'cs' => array(
'makebot' => 'Přidat nebo odebrat příznak bot',
'makebot-header' => "'''Místní byrokraté používají tuto stránku pro přidělení nebo odebrání příznaku [[{{ns:help}}:Bot|bot]] uživatelskému účtu.\'\'\'<br />Příznak bot zajisti, že editace uživatele jsou skryty ze stránky [[Special:Recentchanges|posledních změn]] a podobných seznamů. Jsou užitečné pro roboty provádějící automatické editace.",
'makebot-username' => 'Uživatelské jméno:',
'makebot-search' => 'Provést',
'makebot-isbot' => '[[User:$1|$1]] má příznak bot.',
'makebot-notbot' => '[[User:$1|$1]] nemá příznak bot.',
'makebot-privileged' => '[[User:$1|$1]] má [[Special:Listadmins|práva správce nebo byrokrata]], proto mu nemůže být přidělen příznak bot.',
'makebot-change' => 'Změnit stav:',
'makebot-grant' => 'Přidělit',
'makebot-revoke' => 'Odebrat',
'makebot-comment' => 'Komentář:',
'makebot-granted' => '[[User:$1|$1]] nyní má příznak bot.',
'makebot-revoked' => '[[User:$1|$1]] již nemá příznak bot.',
'makebot-logpage' => 'Kniha příznaků bot',
'makebot-logpagetext' => 'Tato kniha zobrazuje změny v udělovaných příznacích [[{{ns:help}}:Bot|bot]].',
'makebot-logentrygrant' => 'přiděluje účtu [[$1]] příznak bot',
'makebot-logentryrevoke' => 'odebírá účtu [[$1]] příznak bot',
),

/* Finnish (Niklas Laxström) */
'fi' => array(
'makebot' => 'Anna tai poista botti-merkintä',
'makebot-header' => "'''Paikallinen byrokraatti voi antaa tai poista [[Ohje:Botti|botti-merkinnän]] toiselle käyttäjätunnukselle.'''<br />Botti-merkintä piilottaa botti-tunnuksella tehdyt muokkaukset [[Special:Recentchanges|tuoreista muutoksista]] ja vastaavista listoista. Merkintä on hyödyllinen, jos tunnuksella tehdään automaattisia muutoksia. Merkinnän antaminen tai poistaminen tulee tapahtua voimassa olevien käytäntöjen mukaan.",
'makebot-username' => 'Tunnus:',
'makebot-search' => 'Hae',
'makebot-isbot' => '[[User:$1|$1]] on botti.',
'makebot-notbot' => '[[User:$1|$1]] ei ole botti.',
'makebot-privileged' => '[[User:$1|$1]] on [[Special:Listadmins|ylläpitäjä tai byrokraatti]], eikä hänelle voida myöntää botti-merkintää.',
'makebot-change' => 'Muuta merkintää:',
'makebot-grant' => 'Anna',
'makebot-revoke' => 'Poista',
'makebot-comment' => 'Kommentti:',
'makebot-granted' => '[[User:$1|$1]] on nyt botti.',
'makebot-revoked' => '[[User:$1|$1]] ei ole enää botti.',
'makebot-logpage' => 'Botti-loki',
'makebot-logpagetext' => 'Tämä on loki muutoksista käyttäjätunnusten [[Ohje:Botti|botti-merkintään]].',
'makebot-logentrygrant' => 'antoi botti-merkinnän tunnukselle [[$1]]',
'makebot-logentryrevoke' => 'poisti botti-merkinnän tunnukselta [[$1]]',
),

/* French (Bertrand Grondin) */
'fr' => array(
'makebot' => 'Donner ou retirer les droits de bot',
'makebot-header' => "'''Un bureaucrate local peut utiliser cette page pour accorder ou révoquer le [[Aide:Bot|Statut de Bot]] à un autre compte d'utilisateur.'''<br />Le statut de bot a pour particularité de cacher les éditions des utilisateurs dans la page des [[Special:Recentchanges|modification récentes]] et de toutes autres listes similaires. Ceci est très utile pour « flagger » les utilisateurs qui veulent faire des éditions automatiques. Ceci ne doit être fait que conformément aux règles édictées au sein de chaque projet.",
'makebot-username' => 'Nom utilisateur :',
'makebot-search' => 'Valider',
'makebot-isbot' => '[[User:$1|$1]] a le statut de bot.',
'makebot-notbot' => '[[User:$1|$1]] n’a pas le statut de bot.',
'makebot-privileged' => '[[User:$1|$1]] dispose [[Special:Listadmins|des privilèges d’administrateur ou de bureaucrate]], et il n’est pas possible de lui donner le statut de bot.',
'makebot-change' => 'Changer les droits :',
'makebot-grant' => 'Donner',
'makebot-revoke' => 'Retirer',
'makebot-comment' => 'Commentaire :',
'makebot-granted' => '[[User:$1|$1]] a désormais le statut de bot.',
'makebot-revoked' => '[[User:$1|$1]] ne dispose plus du statut de bot.',
'makebot-logpage' => 'Journal du statut de bot',
'makebot-logpagetext' => 'Ceci est le journal des changements du statut de bot pour les utilisateurs concernés',
'makebot-logentrygrant' => 'a donné le statut de bot à [[$1]]',
'makebot-logentryrevoke' => 'a retiré le statut de bot à [[$1]]',
),

/* Italian (BrokenArrow) */
'it' => array(
'makebot' => 'Assegna o revoca lo status di bot',
'makebot-header' => "'''Questa pagina consente ai burocrati di assegnare o revocare lo [[{{ns:help}}:Bot|status di bot]] a un'altra utenza.'''<br /> Tale status nasconde le modifiche effettuate dall'utenza nell'elenco delle [[{{ns:special}}:Recentchanges|ultime modifiche]] e nelle liste simili; è utile per contrassegnare le utenze che effettuano modifiche in automatico. Tale operazione dev'essere effettuata in conformità con le policy del sito.",
'makebot-username' => 'Nome utente:',
'makebot-search' => 'Vai',
'makebot-isbot' => 'L\'utente [[{{ns:user}}:$1|$1]] ha lo status di bot.',
'makebot-notbot' => 'L\'utente [[{{ns:user}}:$1|$1]] non ha lo status di bot.',
'makebot-privileged' => 'L\'utente [[{{ns:user}}:$1|$1]] possiede i privilegi di [[Special:Listadmins|amministratore o burocrate privileges]], che sono incompatibili con lo status di bot.',
'makebot-change' => 'Modifica lo status:',
'makebot-grant' => 'Concedi',
'makebot-revoke' => 'Revoca',
'makebot-comment' => 'Commento:',
'makebot-granted' => 'L\'utente [[{{ns:user}}:$1|$1]] ha ora lo status di bot.',
'makebot-revoked' => 'L\'utente [[{{ns:user}}:$1|$1]] non ha più lo status di bot.',
'makebot-logpage' => 'Registro dei bot',
'makebot-logpagetext' => 'Qui di seguito viene riportata la lista dei cambiamenti di status dei [[{{ns:help}}:bot]].',
'makebot-logentrygrant' => 'ha concesso lo status di bot a [[$1]]',
'makebot-logentryrevoke' => 'ha revocato lo status di bot a [[$1]]',
),

/* Hebrew (Rotem Liss) */
'he' => array(
'makebot'          => 'הענק או בטל הרשאת בוט',
'makebot-header'   => "'''ביורוקרט מקומי יכול להשתמש בדף זה כדי להעניק או לבטל [[{{ns:help}}:בוט|הרשאת בוט]] למשתמש אחר.'''<br />הרשאת בוט מסתירה את עריכותיו של המשתמש מ[[{{ns:special}}:Recentchanges|השינויים האחרונים]] ורשימות דומות, ושימושי למשתמשים המבצעים עריכות אוטומטיות. יש להעניק הרשאת בוט אך ורק לפי הנהלים המתאימים.",
'makebot-username' => 'שם משתמש:',
'makebot-search'   => 'עבור',
'makebot-isbot'      => 'למשתמש [[{{ns:user}}:$1|$1]] יש הרשאת בוט.',
'makebot-notbot'     => 'למשתמש [[{{ns:user}}:$1|$1]] אין הרשאת בוט.',
'makebot-privileged' => 'למשתמש [[{{ns:user}}:$1|$1]] יש כבר [[{{ns:special}}:Listadmins|הרשאות מפעיל מערכת או ביורוקרט]], ולפיכך אי אפשר להעניק לו דגל בוט.',
'makebot-change'     => 'מה לבצע:',
'makebot-grant'      => 'הענקת הרשאה',
'makebot-revoke'     => 'ביטול הרשאה',
'makebot-comment'    => 'סיבה:',
'makebot-granted'    => 'המשתמש [[{{ns:user}}:$1|$1]] קיבל הרשאת בוט.',
'makebot-revoked'    => 'הרשאת הבוט של המשתמש [[{{ns:user}}:$1|$1]] הוסרה בהצלחה.',
'makebot-logpage'        => 'יומן הרשאות בוט',
'makebot-logpagetext'    => 'זהו יומן השינויים בהרשאות ה[[{{ns:help}}:בוט|בוט]] של המשתמשים.',
'makebot-logentrygrant'  => 'העניק הרשאת בוט למשתמש [[$1]]',
'makebot-logentryrevoke' => 'ביטל את הרשאת הבוט למשתמש [[$1]]',
),

/* German (Raymond) */
'de' => array(
'makebot' => 'Botstatus erteilen oder entziehen',
'makebot-header' => "'''Ein Bürokrat dieses Projektes kann anderen Benutzern – in Übereinstimmung mit den lokalen Richtlinien – [[Help:Bot|Botstatus]] erteilen oder entziehen.'''<br /> Mit Botstatus werden die Bearbeitungen eines Bot-Benutzerkontos in den [[Special:Recentchanges|Letzten Änderungen]] und ähnlichen Listen versteckt. Die Botmarkierung ist darüberhinaus zur Feststellung automatischer Bearbeitungen nützlich.",
'makebot-username' => 'Benutzername:',
'makebot-search' => 'Ausführen',
'makebot-isbot' => '[[User:$1|$1]] hat Botstatus.',
'makebot-notbot' => '[[User:$1|$1]] hat keinen Botstatus.',
'makebot-privileged' => '[[User:$1|$1]] hat [[Special:Listusers/sysop|Administrator- oder Bürokratenrechte]], Botstatus kann nicht erteilt werden.',
'makebot-change' => 'Status ändern:',
'makebot-grant' => 'Erteilen',
'makebot-revoke' => 'Zurücknehmen',
'makebot-comment' => 'Kommentar:',
'makebot-granted' => '[[User:$1|$1]] hat nun Botstatus.',
'makebot-revoked' => '[[User:$1|$1]] hat keinen Botstatus mehr.',
'makebot-logpage' => 'Botstatus-Logbuch',
'makebot-logpagetext' => 'Dieses Logbuch protokolliert alle [[Help:Bot|Botstatus]]-Änderungen.',
'makebot-logentrygrant' => 'erteilte Botstatus für [[$1]]',
'makebot-logentryrevoke' => 'entfernte den Botstatus von [[$1]]',
),

/* Portuguese (Lugusto) */
'pt' => array(
'makebot' => 'Conceder ou remover estatuto de bot',
'makebot-header' => "'''Um burocrata local poderá a partir desta página conceder ou remover [[Help:Bot|estatutos de bot]] em outras contas de utilizador.'''<br />Um estatuto de bot faz com que as edições do utilizador sejam ocultadas da página de [[Special:Recentchanges|mudanças recentes]] e listagens similares, sendo bastante útil para marcar contas de utilizadores que façam edições automatizadas. Isso deverá ser feito de acordo com as políticas aplicáveis.",
'makebot-username' => 'Utilizador:',
'makebot-search' => 'Ir',
'makebot-isbot' => '[[User:$1|$1]] possui estatuto de bot.',
'makebot-notbot' => '[[User:$1|$1]] não possui estatuto de bot.',
'makebot-privileged' => '[[User:$1|$1]] possui [[Special:Listadmins|privilégios de administrador ou burocrata]], não podendo que o estatuto de bot seja a ele concedido.',
'makebot-change' => 'Alterar estado:',
'makebot-grant' => 'Conceder',
'makebot-revoke' => 'Remover',
'makebot-comment' => 'Comentário:',
'makebot-granted' => '[[User:$1|$1]] agora possui estatuto de bot.',
'makebot-revoked' => '[[User:$1|$1]] deixou de ter estatuto de bot.',
'makebot-logpage' => 'Registo de estatutos de bot',
'makebot-logpagetext' => 'Este é um registo de alterações quanto ao\' estatuto de [[Help:Bot|bot]].',
'makebot-logentrygrant' => 'concedido estatuto de bot para [[$1]]',
'makebot-logentryrevoke' => 'removido estatuto de bot para [[$1]]',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'makebot' => 'Pemberian atau penarikan status bot',
'makebot-header' => "'''Birokrat lokal dapat menggunakan halaman ini untuk memberikan atau menarik [[Help:Bot|status bot]] untuk akun pengguna lain.'''<br />Status bot akan menyembunyikan suntingan pengguna dari [[Special:Recentchanges|perubahan terbaru]] dan daftar serupa lainnya, dan berguna untuk menandai pengguna yang melakukan penyuntingan otomatis. Hal ini harus dilakukan sesuai dengan kebijakan yang telah digariskan.",
'makebot-username' => 'Nama pengguna:',
'makebot-search' => 'Cari',
'makebot-isbot' => '[[User:$1|$1]] mempunyai status bot.',
'makebot-notbot' => '[[User:$1|$1]] tak mempunyai status bot.',
'makebot-privileged' => '[[User:$1|$1]] mempunyai [[Special:Listadmins|berstatus pengurus atau birokrat]], karenanya tak bisa mendapat status bot.',
'makebot-change' => 'Ganti status:',
'makebot-grant' => 'Berikan',
'makebot-revoke' => 'Tarik ',
'makebot-comment' => 'Komentar:',
'makebot-granted' => '[[User:$1|$1]] sekarang mempunyai status bot.',
'makebot-revoked' => '[[User:$1|$1]] sekarang tidak lagi mempunyai status bot.',
'makebot-logpage' => 'Log perubahan status bot',
'makebot-logpagetext' => 'Di bawah adalah log perubahan status [[Help:Bot|bot]] pengguna.',
'makebot-logentrygrant' => 'memberikan status bot untuk [[$1]]',
'makebot-logentryrevoke' => 'menarik status bot dari [[$1]]',
),

/* Serbian default (Sasa Stefanovic) */
'sr' => array(
'makebot' => 'Давање или одузимање статуса бота',
'makebot-header' => "'''Локални бирократа може користити ову страну да даје или одузима [[Помоћ:Бот|статус бота]] неком другом корисничком налогу.'''<br />Статус бота скрива измене корисника са [[Посебно:Recentchanges|скорашњих измена]] и сличних листа и користан је за обележавање корисника који врше аутоматске измене. Ово треба да се ради у складу са одговарајућим политикама.",
'makebot-username' => 'Корисничко име:',
'makebot-search' => 'Иди',
'makebot-isbot' => '[[User:$1|$1]] има статус бота.',
'makebot-notbot' => '[[User:$1|$1]] нема статус бота.',
'makebot-privileged' => '[[Корисник:$1|$1]] има [[Посебно:Listadmins|администраторске или бирократске привилегије]], и не може му се доделити статус бота.',
'makebot-change' => 'Промени статус:',
'makebot-grant' => 'Дај',
'makebot-revoke' => 'Одузми',
'makebot-comment' => 'Коментар:',
'makebot-granted' => '[[Корисник:$1|$1]] сада има статус бота.',
'makebot-revoked' => '[[Корисник:$1|$1]] више нема статус бота.',
'makebot-logpage' => 'историја статуса бота',
'makebot-logpagetext' => 'Ово је историја измена статуса [[Помоћ:Бот|бота]] корисника.',
'makebot-logentrygrant' => 'дао статус бота: [[$1]]',
'makebot-logentryrevoke' => 'уклонио статус бота: [[$1]]',
),

/* Serbian cyrillic (Sasa Stefanovic) */
'sr-ec' => array(
'makebot' => 'Давање или одузимање статуса бота',
'makebot-header' => "'''Локални бирократа може користити ову страну да даје или одузима [[Помоћ:Бот|статус бота]] неком другом корисничком налогу.'''<br />Статус бота скрива измене корисника са [[Посебно:Recentchanges|скорашњих измена]] и сличних листа и користан је за обележавање корисника који врше аутоматске измене. Ово треба да се ради у складу са одговарајућим политикама.",
'makebot-username' => 'Корисничко име:',
'makebot-search' => 'Иди',
'makebot-isbot' => '[[User:$1|$1]] има статус бота.',
'makebot-notbot' => '[[User:$1|$1]] нема статус бота.',
'makebot-privileged' => '[[Корисник:$1|$1]] има [[Посебно:Listadmins|администраторске или бирократске привилегије]], и не може му се доделити статус бота.',
'makebot-change' => 'Промени статус:',
'makebot-grant' => 'Дај',
'makebot-revoke' => 'Одузми',
'makebot-comment' => 'Коментар:',
'makebot-granted' => '[[Корисник:$1|$1]] сада има статус бота.',
'makebot-revoked' => '[[Корисник:$1|$1]] више нема статус бота.',
'makebot-logpage' => 'историја статуса бота',
'makebot-logpagetext' => 'Ово је историја измена статуса [[Помоћ:Бот|бота]] корисника.',
'makebot-logentrygrant' => 'дао статус бота: [[$1]]',
'makebot-logentryrevoke' => 'уклонио статус бота: [[$1]]',
),

/* Serbian latin (Sasa Stefanovic) */
'sr-el' => array(
'makebot' => 'Davanje ili oduzimanje statusa bota',
'makebot-header' => "'''Lokalni birokrata može koristiti ovu stranu da daje ili oduzima [[Pomoć:Bot|status bota]] nekom drugom korisničkom nalogu.'''<br />Status bota skriva izmene korisnika sa [[Posebno:Recentchanges|skorašnjih izmena]] i sličnih lista i koristan je za obeležavanje korisnika koji vrše automatske izmene. Ovo treba da se radi u skladu sa odgovarajućim politikama.",
'makebot-username' => 'Korisničko ime:',
'makebot-search' => 'Idi',
'makebot-isbot' => '[[User:$1|$1]] ima status bota.',
'makebot-notbot' => '[[User:$1|$1]] nema status bota.',
'makebot-privileged' => '[[Korisnik:$1|$1]] ima [[Posebno:Listadmins|administratorske ili birokratske privilegije]], i ne može mu se dodeliti status bota.',
'makebot-change' => 'Promeni status:',
'makebot-grant' => 'Daj',
'makebot-revoke' => 'Oduzmi',
'makebot-comment' => 'Komentar:',
'makebot-granted' => '[[Korisnik:$1|$1]] sada ima status bota.',
'makebot-revoked' => '[[Korisnik:$1|$1]] više nema status bota.',
'makebot-logpage' => 'istorija statusa bota',
'makebot-logpagetext' => 'Ovo je istorija izmena statusa [[Pomoć:Bot|bota]] korisnika.',
'makebot-logentrygrant' => 'dao status bota: [[$1]]',
'makebot-logentryrevoke' => 'uklonio status bota: [[$1]]',
),

/* Walloon (language file) */
'wa' => array(
'makebot' => 'Diner ou rsaetchî l\' livea d\' robot',
'makebot-header' => '\'\'\'On mwaisse-manaedjeu sol wiki pout eployî cisse pådje ci po dner ou rsaetchî l\' [[{{ns:help}}:Robots|livea d\' robot]] a èn ôte conte d\' uzeu.\'\'\'<br />El livea d\' robot fwait ki les candjmints da cist uzeu la si polèt catchî dins l\' pådje des [[{{special}}:Recentchanges|dierins candjmints]] et des sfwaitès djivêyes, çou k\' est ahessåve po mårker les uzeus ki fjhèt des candjmints otomatikes. Çoula doet esse fwait tot shuvant les rîles ki s\' aplikèt.',
'makebot-username' => 'No d\' uzeu:',
'makebot-search' => 'I va',
'makebot-change' => 'Candjî l\' livea:',
'makebot-grant' => 'Diner',
'makebot-revoke' => 'Rissaetchî',
'makebot-comment' => 'Comintaire:',
'makebot-logpage' => 'Djournå des liveas d\' robot',
'makebot-granted' => '[[{{ns:user}}:$1|$1]] a-st asteure li livea d\' robot.',
'makebot-isbot' => '[[{{ns:user}}:$1|$1]] a l\' livea d\' robot.',
'makebot-logentrygrant' => 'a dné l\' livea d\' robot a [[$1]]',
'makebot-logentryrevoke' => 'a rsaetchî l\' livea d\' robot da [[$1]]',
'makebot-logpagetext' => 'Çouchal, c\' est on djournå des dinaedjes eyet rsaetchaedjes do [[{{ns:help}}:Robots|livea d\' robot]] a des uzeus.',
'makebot-notbot' => '[[{{ns:user}}:$1|$1]] n\' a nén l\' livea d\' robot',
'makebot-privileged' => '[[{{ns:user}}:$1|$1]] a ddja on livea d\' [[{{ns:special}}:Listadmins|manaedjeu ou mwaisse-manaedjeu]], ça fwait k\' i n\' pout nén eployî ç\' conte la po on robot.',
'makebot-revoked' => '[[{{ns:user}}:$1|$1]] n\' a pus d\' livea d\' robot.',
),

	);
	return $messages;
}
?>
