<?php

/**
 * Internationalisation file for CountEdits extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

function efCountEditsMessages( $single = false ) {
	$messages = array(

/* English (Rob Church) */
'en' => array(
	'countedits' => 'Count edits',
	'countedits-warning' => "'''Warning:''' Do not judge a book by its cover. Do not judge a contributor by their edit count.",
	'countedits-username' => 'Username:',
	'countedits-ok' => 'OK',
	'countedits-nosuchuser' => 'There is no user with the name $1.',
	'countedits-resultheader' => 'Results for $1',
	'countedits-resulttext' => '$1 has made $2 edits',
	'countedits-mostactive' => 'Most active contributors',
	'countedits-nocontribs' => 'There have been no contributions to this wiki.',
),

/* Arabic (Meno25) */
'ar' => array(
	'countedits' => 'عد التعديلات',
	'countedits-warning' => '\'\'\'تحذير:\'\'\' لا تحكم على كتاب من غلافه. لا تحكم على مساهم من خلال عدد مساهماته.',
	'countedits-username' => 'اسم المستخدم:',
	'countedits-ok' => 'موافق',
	'countedits-nosuchuser' => 'لا يوجد مستخدم بالاسم $1.',
	'countedits-resultheader' => 'النتائج ل $1',
	'countedits-resulttext' => '$1 لديه $2 مساهمة',
	'countedits-mostactive' => 'أكثر المساهمين نشاطا',
	'countedits-nocontribs' => 'لا يوجد مساهمون في هذه الويكي.',
),

'bcl' => array(
	'countedits-resultheader' => 'Mga resulta kan $1',
	'countedits-mostactive' => 'Pinaka mahigos na mga kontributor',
),

'br' => array(
	'countedits' => 'Degasadennoù ar gont',
	'countedits-warning' => '\'\'\'Diwallit :\'\'\' Ne varner ket ul levr diouzh ar golo anezhañ. Arabat barnañ un implijer diouzh an niver a zegasadennoù graet gantañ.',
	'countedits-username' => 'Anv implijer :',
	'countedits-ok' => 'Mat eo',
	'countedits-nosuchuser' => 'N\'eus implijer ebet anvet $1.',
	'countedits-resultheader' => 'Disoc\'hoù evit $1',
	'countedits-resulttext' => '$2 degasadenn zo bet graet gant $1',
	'countedits-mostactive' => 'Implijerien oberiantañ',
	'countedits-nocontribs' => 'Degasadenn ebet evit ar wiki-mañ.',
),

'ca' => array(
	'countedits' => 'Nombre d\'edicions',
	'countedits-warning' => '\'\'\'Avís:\'\'\' No jutgeu un llibre per la seua coberta, ni tampoc a un col·laborador pel seu nombre d\'edicions.',
	'countedits-username' => 'Nom d\'usuari:',
	'countedits-ok' => 'D\'acord',
	'countedits-nosuchuser' => 'No hi ha cap usuari amb el nom $1.',
	'countedits-resultheader' => 'Resultats de $1',
	'countedits-resulttext' => '$1 ha fet $2 edicions',
	'countedits-mostactive' => 'Els col·laboradors més actius',
	'countedits-nocontribs' => 'No hi ha hagut cap col·laboració en aquest wiki.',
),

/* German (Raymond) */
'de' => array(
	'countedits' => 'Beitragszähler',
	'countedits-warning' => 'Hinweis: Sie beurteilen ein Buch nicht nur nach seinem Umschlag, beurteilen Sie einen Autor daher auch nicht nur nach seinem Beitragszähler.',
	'countedits-username' => 'Benutzername:',
	'countedits-ok' => 'OK',
	'countedits-nosuchuser' => 'Es gibt keinen Benutzer mit dem Namen $1.',
	'countedits-resultheader' => 'Ergebnis für $1',
	'countedits-resulttext' => '$1 hat $2 Bearbeitungen',
	'countedits-mostactive' => 'Die aktivsten Benutzer',
	'countedits-nocontribs' => 'In {{ns:project}} sind keine Bearbeitungen vorhanden.',
),

'el' => array(
	'countedits-ok' => 'ΟΚ',
),

'eo' => array(
	'countedits' => 'Nombro de redaktoj',
	'countedits-warning' => '\'\'\'Averto:\'\'\' Ne juĝu libron laŭ ĝia kovrilo. Ne juĝu kontribuanton laŭ lia redaktaro.',
	'countedits-username' => 'Uzantonomo:',
	'countedits-ok' => 'Ek',
	'countedits-nosuchuser' => 'Ne ekzistas uzanto kun la nomo $1.',
	'countedits-resultheader' => 'Rezultoj por $1',
	'countedits-resulttext' => '$1 faris $2 redaktojn',
	'countedits-mostactive' => 'Plej aktivaj kontribuantoj',
	'countedits-nocontribs' => 'Ne estas iuj kontribuaĵoj por ĉi tiu vikio.',
),

'ext' => array(
	'countedits-username' => 'Nombri d´usuáriu:',
	'countedits-resulttext' => '$1 á hechu $2 eicionis',
),

/* Finnish (Niklas Laxström) */
'fi' => array(
	'countedits' => 'Muokkausmäärälaskuri',
	'countedits-warning' => 'Älä arvioi kirjaa kannen perusteella. Älä arvioi käyttäjää muokkausten lukumäärän perusteella.',
	'countedits-username' => 'Käyttäjä',
	'countedits-ok' => 'Hae',
	'countedits-nosuchuser' => 'Käyttäjää $1 ei ole.',
	'countedits-resultheader' => 'Tulos',
	'countedits-resulttext' => '$1 on tehnyt $2 muokkausta.',
	'countedits-mostactive' => 'Aktiivisimmat käyttäjät',
	'countedits-nocontribs' => 'Tätä wikiä ei ole muokattu.',
),

/* French (Bertrand Grondin) */
'fr' => array(
	'countedits' => 'Compteur d’éditions',
	'countedits-warning' => 'Avertissement : ne jugez pas un livre par sa couverture. Ne jugez pas non plus un utilisateur en fonction du nombre de ses contributions.',
	'countedits-username' => 'Utilisateur',
	'countedits-nosuchuser' => 'Il n’y a aucun utilisateur correspondant à $1',
	'countedits-resultheader' => 'Résultats pour $1',
	'countedits-resulttext' => '$1 a fait {{PLURAL:$2|$2 édition|$2 éditions}}',
	'countedits-mostactive' => 'Contributeurs les plus actifs',
	'countedits-nocontribs' => 'Aucune contribution sur ce wiki.',
),

'gl' => array(
	'countedits' => 'Contar edicións',
	'countedits-warning' => '\'\'\'Advertencia:\'\'\' As aparencias enganan. Non xulgue a un colaborador polo seu número de edicións.',
	'countedits-username' => 'Nome de usuario:',
	'countedits-ok' => 'De acordo',
	'countedits-nosuchuser' => 'Non existe ningún usuario chamado $1.',
	'countedits-resultheader' => 'Resultados de $1',
	'countedits-resulttext' => '$1 ten feitas $2 edicións',
	'countedits-mostactive' => 'Colaboradores máis activos',
	'countedits-nocontribs' => 'Non houbo ningunha colaboración neste wiki.',
),

'hsb' => array(
	'countedits' => 'Ličak přinoškow',
	'countedits-warning' => '\'\'\'Kedźbu\'\'\': Njeposudź knihu wobalki dla, njeposudź wužiwarja ličby jeho přinoškow dla!',
	'countedits-username' => 'Wužiwarske mjeno:',
	'countedits-ok' => 'W porjadku',
	'countedits-nosuchuser' => 'Wužiwar z mjenom $1 njeeksistuje.',
	'countedits-resultheader' => 'Wuslědki za wužiwarja $1',
	'countedits-resulttext' => '$1 je $2 wobdźěłanjow sčinił.',
	'countedits-mostactive' => 'Najaktiwniši přinošowarjo',
	'countedits-nocontribs' => 'Njejsu žane změny w tutym wikiju.',
),

'hy' => array(
	'countedits' => 'Հաշվել խմբագրումները',
	'countedits-warning' => '\'\'\'Զգուշացում.\'\'\' մի դատեք գրքի մասին կազմով և մասնակցի մասին՝ խմբագրումների քանակով։',
	'countedits-username' => 'Մասնակից.',
	'countedits-ok' => 'OK',#identical but defined
	'countedits-nosuchuser' => '$1 անվանմամբ մասնակից չկա։',
	'countedits-resultheader' => 'Արդյունքներ $1 մասնակցի համար',
	'countedits-resulttext' => '$1 մասնակիցը կատարել է $2 խմբագրում',
	'countedits-mostactive' => 'Ամենաակտիվ մասնակիցները',
	'countedits-nocontribs' => 'Այս վիքիում ոչ մի խմբագրում չի եղել։',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
	'countedits' => 'Jumlah suntingan',
	'countedits-warning' => 'Peringatan: Jangan menilai suatu buku dari sampulnya. Jangan menilai seorang kontributor berdasarkan jumlah suntingannya.',
	'countedits-username' => 'Nama pengguna:',
	'countedits-ok' => 'OK',
	'countedits-nosuchuser' => 'Tidak ada pengguna dengan nama $1.',
	'countedits-resultheader' => 'Hasil untuk $1',
	'countedits-resulttext' => '$1 telah membuat $2 suntingan',
	'countedits-mostactive' => 'Kontributor paling aktif',
	'countedits-nocontribs' => 'Belum ada kontribusi untuk wiki ini.',
),

/* Italian (BrokenArrow) */
'it' => array(
	'countedits' => 'Conteggio delle modifiche',
	'countedits-warning' => "'''Attenzione:''' Un libro non si giudica dalla copertina. Un utente non si giudica dal numero delle modifiche.",
	'countedits-username' => 'Nome utente:',
	'countedits-ok' => 'OK',
	'countedits-nosuchuser' => '$1 non corrisponde a un nome utente valido.',
	'countedits-resultheader' => 'Risultati per l\'utente $1',
	'countedits-resulttext' => '$1 ha effettuato $2 modifiche',
	'countedits-mostactive' => 'Autori con il maggior numero di contributi',
	'countedits-nocontribs' => 'Il sito non ha subito alcuna modifica.',
),

/* Kazakh default (AlefZet) */
'kk' => array(
	'countedits' => 'Түзету санау',
	'countedits-warning' => "'''Назар салыңыз:''' Кітапті мұқабасынан жорамалдамаңыз. Үлескерді түзету санынан жорамалдамаңыз.",
	'countedits-username' => 'Қатысуша аты:',
	'countedits-ok' => 'Жарайды',
	'countedits-nosuchuser' => 'Мынадай атауы бар қатысушы жоқ: $1.',
	'countedits-resultheader' => '$1 деген үшін табылған натижелері',
	'countedits-resulttext' => '$1 деген $2 түзету істеген',
	'countedits-mostactive' => 'Ең белсенді үлескерлер',
	'countedits-nocontribs' => 'Бұл уикиде еш үлес болған жоқ.',
),

/* Kazakh Cyrillic (AlefZet) */
'kk-kz' => array(
	'countedits' => 'Түзету санау',
	'countedits-warning' => "'''Назар салыңыз:''' Кітапті мұқабасынан жорамалдамаңыз. Үлескерді түзету санынан жорамалдамаңыз.",
	'countedits-username' => 'Қатысуша аты:',
	'countedits-ok' => 'Жарайды',
	'countedits-nosuchuser' => 'Мынадай атауы бар қатысушы жоқ: $1.',
	'countedits-resultheader' => '$1 деген үшін табылған натижелері',
	'countedits-resulttext' => '$1 деген $2 түзету істеген',
	'countedits-mostactive' => 'Ең белсенді үлескерлер',
	'countedits-nocontribs' => 'Бұл уикиде еш үлес болған жоқ.',
),

/* Kazakh Latin (AlefZet) */
'kk-tr' => array(
	'countedits' => 'Tüzetw sanaw',
	'countedits-warning' => "'''Nazar salıñız:''' Kitapti muqabasınan joramaldamañız. Üleskerdi tüzetw sanınan joramaldamañız.",
	'countedits-username' => 'Qatıswşa atı:',
	'countedits-ok' => 'Jaraýdı',
	'countedits-nosuchuser' => 'Mınadaý atawı bar qatıswşı joq: $1.',
	'countedits-resultheader' => '$1 degen üşin tabılğan natïjeleri',
	'countedits-resulttext' => '$1 degen $2 tüzetw istegen',
	'countedits-mostactive' => 'Eñ belsendi üleskerler',
	'countedits-nocontribs' => 'Bul wïkïde eş üles bolğan joq.',
),

/* Kazakh Arabic (AlefZet) */
'kk-cn' => array(
	'countedits' => 'تٷزەتۋ ساناۋ',
	'countedits-warning' => "'''نازار سالىڭىز:''' كٸتاپتٸ مۇقاباسىنان جورامالداماڭىز. ٷلەسكەردٸ تٷزەتۋ سانىنان جورامالداماڭىز.",
	'countedits-username' => 'قاتىسۋشا اتى:',
	'countedits-ok' => 'جارايدى',
	'countedits-nosuchuser' => 'مىناداي اتاۋى بار قاتىسۋشى جوق: $1.',
	'countedits-resultheader' => '$1 دەگەن ٷشٸن تابىلعان ناتيجەلەرٸ',
	'countedits-resulttext' => '$1 دەگەن $2 تٷزەتۋ ٸستەگەن',
	'countedits-mostactive' => 'ەڭ بەلسەندٸ ٷلەسكەرلەر',
	'countedits-nocontribs' => 'بۇل ۋيكيدە ەش ٷلەس بولعان جوق.',
),

'ku-latn' => array(
	'countedits' => 'Guherandinan bihesbîne',
	'countedits-username' => 'Navî bikarhêner:',
	'countedits-ok' => 'OK',#identical but defined
	'countedits-nosuchuser' => 'Li vê derê ne bikarhênerek bi navê $1 heye.',
	'countedits-resulttext' => '$1 $2 guherandinan çêkirîye',
	'countedits-nocontribs' => 'Di vê wîkîyê da guherandin tune ne.',
),

'la' => array(
	'countedits-username' => 'Nomen usoris:',
),

'nds' => array(
	'countedits' => 'Tellen, wo faken de Bruker Sieden ännert hett',
	'countedits-warning' => '\'\'\'Wohrschau:\'\'\' Schasst de Deern nich na ehr Schört reken. Wo faken en Bruker Sieden ännert hett, seggt nix över sien Arbeit ut.',
	'countedits-username' => 'Brukernaam:',
	'countedits-ok' => 'Okay',
	'countedits-nosuchuser' => 'Dat gifft keen Bruker mit’n Naam $1.',
	'countedits-resultheader' => 'Wat för $1 rutkamen is',
	'countedits-resulttext' => '$1 hett $2 Maal wat ännert.',
	'countedits-mostactive' => 'Brukers, de opmehrst Maal wat ännert hebbt',
	'countedits-nocontribs' => 'Kene Bidrääg op dit Wiki.',
),

/* nld / Dutch (Siebrand Mazeland) */
'nl' => array(
	'countedits' => 'Bewerkingen tellen',
	'countedits-warning' => '\'\'\'Waarschuwing:\'\'\' Beoordeel het boek niet op de buitenkant. Beoordeel een redacteur niet alleen op het aantal bijdragen.',
	'countedits-username' => 'Gebruiker:',
	'countedits-ok' => 'OK',#identical but defined
	'countedits-nosuchuser' => 'Er is geen gebruiker met de naam $1.',
	'countedits-resultheader' => 'Resulaten voor $1',
	'countedits-resulttext' => '$1 heeft $2 bewerkingen gemaakt',
	'countedits-mostactive' => 'Meest actieve redacteuren',
	'countedits-nocontribs' => 'Er zijn geen bewerkingen op deze wiki.',
),

/* Norwegian (Jon Harald Søby) */
'no' => array(
	'countedits' => 'Tell redigeringer',
	'countedits-warning' => '\'\'\'Advarsel:\'\'\' Ikke sku hunden på hårene. Ikke døm en bidragsyter på antall redigeringer.',
	'countedits-username' => 'Brukernavn:',
	'countedits-ok' => 'OK',#identical but defined
	'countedits-nosuchuser' => 'Det er ingen bruker ved navnet $1.',
	'countedits-resultheader' => 'Resultater for $1',
	'countedits-resulttext' => '$1 har gjort $2 redigeringer',
	'countedits-mostactive' => 'Mest aktive bidragsytere',
	'countedits-nocontribs' => 'Det har ikke vært noen redigeringer på denne wikien.',
),

'oc' => array(
	'countedits' => 'Comptaire d’edicions',
	'countedits-warning' => '\'\'\'Avertiment\'\'\' : jutjetz pas un libre per sa cobertura. Jutjetz pas tanpauc un utilizaire en foncion del nombre de sas contribucions.',
	'countedits-username' => 'Nom d\'utilizaire:',
	'countedits-ok' => 'D\'acòrdi',
	'countedits-nosuchuser' => 'I a pas d\'utilizaire amb lo nom $1.',
	'countedits-resultheader' => 'Resultats per $1',
	'countedits-resulttext' => '$1 a fach $2 modificacions',
	'countedits-mostactive' => 'Contributors mai actius',
	'countedits-nocontribs' => 'Cap de contribucion sus aqueste wiki.',
),

'pl' => array(
	'countedits' => 'Liczba edycji',
	'countedits-warning' => '\'\'Ostrzeżenie:\'\'\' Nie oceniaj książki po jej okładce. Nie oceniaj użytkownika po jego liczbie edycji.',
	'countedits-username' => 'Nazwa użytkownika:',
	'countedits-ok' => 'OK',#identical but defined
	'countedits-nosuchuser' => 'Nie istnieje użytkownik o nazwie $1.',
	'countedits-resultheader' => 'Wyniki dla $1',
	'countedits-resulttext' => '$1 wykonał (-a) $2 edycji',
	'countedits-mostactive' => 'Najbardziej aktywni użytkownicy',
	'countedits-nocontribs' => 'Nie wykonano edycji na tej wiki',
),

/* Piedmontese (Bèrto 'd Sèra) */
'pms' => array(
	'countedits' => 'Total dle modìfiche',
	'countedits-warning' => '\'\'\'Avis:\'\'\' Mai giudiché un lìber da soa coertin-a. Ch\'a giùdica pa n\'utent da vàire modìfiche ch\'a l\'ha fait.',
	'countedits-username' => 'Stranòm:',
	'countedits-ok' => 'Bin parèj',
	'countedits-nosuchuser' => 'A-i é pa gnun ch\'a l\'abia lë stranòm $1.',
	'countedits-resultheader' => 'Arzultà për $1',
	'countedits-resulttext' => '$1 a l\'ha fait $2 modìfiche',
	'countedits-mostactive' => 'Contributor pì ativ',
	'countedits-nocontribs' => 'A-i é pa anco\' sta-ie gnun-a modìfica a sta wiki-sì.',
),

/* Portuguese (Lugusto) */
'pt' => array(
	'countedits' => 'Contador de edições',
	'countedits-warning' => "'''Atenção:''' Não julgue um livro pela sua capa. Não julgue um contribuidor pela contagem de suas edições.",
	'countedits-username' => 'Utilizador:',
	'countedits-ok' => 'Ok',
	'countedits-nosuchuser' => 'Não foi encontrado um utilizador com o nome $1.',
	'countedits-resultheader' => 'Resultados para $1',
	'countedits-resulttext' => '$1 fez $2 edições',
	'countedits-mostactive' => 'Contribuidores mais activos',
	'countedits-nocontribs' => 'Não possui contribuições neste wiki.',
),

/* Romanian (KlaudiuMihăilă) */
'ro' => array(
	'countedits' => 'Număr de modificări',
	'countedits-warning' => '\'\'\'Atenţie:\'\'\' Nu judeca o carte după copertă. Nu judeca un contribuitor după numărul de modificări.',
	'countedits-username' => 'Nume de utilizator:',
	'countedits-nosuchuser' => 'Nu există nici un utilizator cu numele $1.',
	'countedits-resultheader' => 'Rezultate pentru $1',
	'countedits-resulttext' => '$1 a efectuat {{PLURAL:$2|o modificare|$2 modificări}}',
	'countedits-mostactive' => 'Contribuitorii cei mai activi',
	'countedits-nocontribs' => 'Nu există contribuitori la acest wiki.',
),

'ru' => array(
	'countedits' => 'Подсчитать правки',
	'countedits-warning' => "'''Внимание:''' не судите о книге по её обложке. Не судите об участнике по количеству его правок.",
	'countedits-username' => 'Участник:',
	'countedits-ok' => 'OK',
	'countedits-nosuchuser' => 'Не существует участника с именем $1.',
	'countedits-resultheader' => 'Данные для $1',
	'countedits-resulttext' => '$1 сделал $2 правок',
	'countedits-mostactive' => 'Наиболее активные участники',
	'countedits-nocontribs' => 'Нет правок в этой вики.',
),


'sah' => array(
	'countedits' => 'Хас көннөрүүлээҕэ',
	'countedits-warning' => '\'\'\'Болҕой:\'\'\' Кинигэни таһыттан сыаналаабаттарын курдук, кыттааччыны правката элбэҕинэн сыаналаабаттар.',
	'countedits-username' => 'Аата:',
	'countedits-nosuchuser' => 'Маннык $1 ааттаах кыттааччы суох',
	'countedits-resultheader' => '$1 дааннайдара (түмүктэрэ)',
	'countedits-resulttext' => '$1 $2 көннөрүүнү оҥорбут',
	'countedits-mostactive' => 'Саамай элбэх көннөрүүнү оҥорбут кыттааччылар',
	'countedits-nocontribs' => 'Бу биикигэ көннөрүү оҥоһуллубатах.',
),

/* Slovak (helix84) */
'sk' => array(
	'countedits' => 'Počet príspevkov',
	'countedits-warning' => "'''Varovanie:''' Nesúďte knihu podľa obalu. Nesúďte prispievateľa podľa počtu príspevkov.",
	'countedits-username' => 'Používateľské meno:',
	'countedits-ok' => 'OK',
	'countedits-nosuchuser' => 'Používateľ s menom $1 neexistuje.',
	'countedits-resultheader' => 'Výsledky pre $1',
	'countedits-resulttext' => '$1 urobil $2 úprav',
	'countedits-mostactive' => 'Najaktívnejší prispievatelia',
	'countedits-nocontribs' => 'Táto wiki neobsahuje zatiaľ žiadne príspevky.',
),

/* Serbian default (Sasa Stefanovic) */
'sr' => array(
	'countedits' => 'Бројач измена',
	'countedits-warning' => "'''Упозорење:''' Не судите о књизи по њеном омоту. Не судите о кориснику по његовом броју измена.",
	'countedits-username' => 'Корисник:',
	'countedits-ok' => 'У реду',
	'countedits-nosuchuser' => 'Не постоји корисник са именом $1.',
	'countedits-resultheader' => 'Резултати за $1',
	'countedits-resulttext' => '$1 има $2 измена',
	'countedits-mostactive' => 'Најактивнији корисници',
	'countedits-nocontribs' => 'Не постоје прилози на овој вики.',
),

/* Serbian cyrillic (Sasa Stefanovic) */
'sr-ec' => array(
	'countedits' => 'Бројач измена',
	'countedits-warning' => "'''Упозорење:''' Не судите о књизи по њеном омоту. Не судите о кориснику по његовом броју измена.",
	'countedits-username' => 'Корисник:',
	'countedits-ok' => 'У реду',
	'countedits-nosuchuser' => 'Не постоји корисник са именом $1.',
	'countedits-resultheader' => 'Резултати за $1',
	'countedits-resulttext' => '$1 има $2 измена',
	'countedits-mostactive' => 'Најактивнији корисници',
	'countedits-nocontribs' => 'Не постоје прилози на овој вики.',
),

/* Serbian latin (Sasa Stefanovic) */
'sr-el' => array(
	'countedits' => 'Brojač izmena',
	'countedits-warning' => "'''Upozorenje:''' Ne sudite o knjizi po njenom omotu. Ne sudite o korisniku po njegovom broju izmena.",
	'countedits-username' => 'Korisnik:',
	'countedits-ok' => 'U redu',
	'countedits-nosuchuser' => 'Ne postoji korisnik sa imenom $1.',
	'countedits-resultheader' => 'Rezultati za $1',
	'countedits-resulttext' => '$1 ima $2 izmena',
	'countedits-mostactive' => 'Najaktivniji korisnici',
	'countedits-nocontribs' => 'Ne postoje prilozi na ovoj viki.',
),

/* Sundanese (Kandar via BetaWiki) */
'su' => array(
	'countedits' => 'Itung éditan',
	'countedits-warning' => '\'\'\'Ati-ati\'\'\': ulah nganiléy kontributor dumasar kana jumlah éditanana.',
	'countedits-username' => 'Landihan pamaké:',
	'countedits-ok' => 'Heug',
	'countedits-nosuchuser' => 'Euweuh pamaké nu landihanana $1.',
	'countedits-resultheader' => 'Hasil pikeun $1',
	'countedits-resulttext' => '$1 geus nyieun $2 éditan',
	'countedits-mostactive' => 'Kontributor panggetolna',
	'countedits-nocontribs' => 'Can aya kontribusi ka ieu wiki.',
),

'tet' => array(
	'countedits-username' => 'Naran uza-na\'in:',
	'countedits-ok' => 'OK',#identical but defined
	'countedits-nosuchuser' => 'Uza-na\'in ho naran $1 lá\'os iha ne\'e.',
	'countedits-resulttext' => '$1 edita tiha ona ba dala $2',
),

/* Cantonese (Shinjiman) */
'yue' => array(
	'countedits' => '編輯數',
	'countedits-warning' => "'''警告:''' 唔好只憑封面去判斷一本書。唔好以佢哋嘅編輯數去判斷一位貢獻者。",
	'countedits-username' => '用戶名:',
	'countedits-ok' => 'OK',
	'countedits-nosuchuser' => '無一位叫做$1嘅用戶。',
	'countedits-resultheader' => '$1嘅結果',
	'countedits-resulttext' => '$1有$2次編輯',
	'countedits-mostactive' => '最活躍嘅貢獻者',
	'countedits-nocontribs' => '響呢個wiki度無貢獻。',
),

/* Chinese (Simplified) (Shinjiman) */
'zh-hans' => array(
	'countedits' => '编辑计量',
	'countedits-warning' => "'''警告:''' 不要只凭封面判断书本。不要以他们的编辑计量判断一位贡献者。",
	'countedits-username' => '用户名称:',
	'countedits-ok' => '确定',
	'countedits-nosuchuser' => '没有一位名叫$1的用户。',
	'countedits-resultheader' => '$1的结果',
	'countedits-resulttext' => '$1有$2次编辑',
	'countedits-mostactive' => '最活跃的贡献者',
	'countedits-nocontribs' => '在这个wiki中没有贡献。',
),

/* Chinese (Traditional) (Shinjiman) */
'zh-hant' => array(
	'countedits' => '編輯計量',
	'countedits-warning' => "'''警告:''' 不要只憑封面判斷書本。不要以幾他們的編輯計量判斷一位貢獻者。",
	'countedits-username' => '用戶名稱:',
	'countedits-ok' => '確定',
	'countedits-nosuchuser' => '沒有一位名叫$1的用戶。',
	'countedits-resultheader' => '$1的結果',
	'countedits-resulttext' => '$1有$2次編輯',
	'countedits-mostactive' => '最活躍的貢獻者',
	'countedits-nocontribs' => '在這個wiki中沒有貢獻。',
),

	);

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];
	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $single ? $messages['en'] : $messages;
}
