<?php
/**
 * Internationalisation for Usability Initiative Click Tracking extension
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Nimish Gautam
 */
$messages['en'] = array(
	'clicktracking' => 'Usability Initiative click tracking',
	'clicktracking-desc' => 'Click tracking for tracking events that do not cause a page refresh',
	'clicktracking-title' => 'Aggregated user clicks',
	'event-name' => 'Event name',
	'expert-header' => '"Expert" clicks',
	'intermediate-header' => '"Intermediate" clicks',
	'beginner-header' => '"Beginner" clicks',
	'total-header' => 'Total clicks',
	'start-date' => 'Start Date (YYYYMMDD)', 
	'end-date' => 'End Date (YYYYMMDD)',
	'increment-by' =>'Number of days each data point represents', 
	'change-graph' =>'Change graph',
	'beginner' => 'Beginner',
	'intermediate' => 'Intermediate',
	'expert' => 'Expert',
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 */
$messages['qqq'] = array(
	'clicktracking-desc' => '{{desc}}',
	'expert-header' => '"Expert" is a user-definable category, these will show the number of clicks that fall into that category',
	'intermediate-header' => '"Intermediate" is a user-definable category, these will show the number of clicks that fall into that category',
	'beginner-header' => '"Beginner" is a user-definable category, these will show the number of clicks that fall into that category',
	'total-header' => 'total',
	'start-date' => 'YYYYMMDD refers to the date format (4-digit year, 2-digit month, 2-digit day)

{{Identical|Start date}}',
	'end-date' => 'YYYYMMDD  refers to the date format (4-digit year, 2-digit month, 2-digit day)

{{Identical|End date}}',
	'beginner' => 'label for a user at beginner skill level',
	'intermediate' => 'label for a user at intermediate skill level',
	'expert' => 'label for a user at expert skill level',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'start-date' => 'Begindatum (JJJJMMDD)',
	'end-date' => 'Einddatum (JJJJMMDD)',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'event-name' => 'اسم الحدث',
	'beginner' => 'مبتدئ',
	'intermediate' => 'متوسط',
	'expert' => 'خبير',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 * @author Red Winged Duck
 */
$messages['be-tarask'] = array(
	'clicktracking' => 'Сачэньне за націскамі кампутарнай мышшу ў межах ініцыятывы па паляпшэньні зручнасьці і прастаты выкарыстаньня',
	'clicktracking-desc' => 'Сачэньне за націскамі кампутарнай мышшу, прызначанае для сачэньня за здарэньнямі, якія не вядуць да абнаўленьня старонкі',
	'clicktracking-title' => 'Групаваныя націскі кнопак мышы ўдзельнікам',
	'event-name' => 'Назва падзеі',
	'expert-header' => 'Націскі мышшу для «Экспэрта»',
	'intermediate-header' => 'Націскі мышшу для «Сярэдняга»',
	'beginner-header' => 'Націскі мышшу для «Пачынаючага»',
	'total-header' => 'Усяго націскаў мышшу',
	'start-date' => 'Дата пачатку (ГГГГММДзДз)',
	'end-date' => 'Дата сканчэньня (ГГГГММДзДз)',
	'increment-by' => 'Колькасьць дзён, якія адлюстроўваюцца ў кожным пункце зьвестак',
	'change-graph' => 'Зьмяніць графік',
	'beginner' => 'Пачынаючы',
	'intermediate' => 'Сярэдні',
	'expert' => 'Экспэрт',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'clicktracking' => 'Heuliañ klikoù an intrudu implijadusted',
	'clicktracking-desc' => "Heuliañ klikoù, talvezout a ra da heuliañ an darvoudoù na vez ket adkarget ur bajenn d'ho heul",
	'clicktracking-title' => "Sammad ar c'hlikoù implijerien",
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'clicktracking' => 'Praćenje klikova u Inicijativi upotrebljivosti',
	'clicktracking-desc' => 'Praćenje klikova, napravljeno za praćenje događaja koji ne proizvode osvježavanje stanice',
	'clicktracking-title' => 'Sveukupni klikovi korisnika',
	'event-name' => 'Naziv događaja',
	'expert-header' => "''Stručnjački'' klikovi",
	'intermediate-header' => "''Napredni'' klikovi",
	'beginner-header' => "''Početnički'' klikovi",
	'total-header' => 'Ukupno klikova',
	'start-date' => 'Početni datum (YYYYMMDD)',
	'end-date' => 'Završni datum (YYYYMMDD)',
	'increment-by' => 'Broj dana koje svaka tačka podataka predstavlja',
	'change-graph' => 'Promijeni grafikon',
	'beginner' => 'Početnik',
	'intermediate' => 'Napredni',
	'expert' => 'Stručnjak',
);

/** Czech (Česky)
 * @author Mormegil
 */
$messages['cs'] = array(
	'clicktracking' => 'Sledování kliknutí pro Iniciativu použitelnosti',
	'clicktracking-desc' => 'Sledování kliknutí pro sledování událostí, které nezpůsobují znovunačtení stránky',
	'clicktracking-title' => 'Souhrn klikání uživatelů',
	'event-name' => 'Název události',
	'expert-header' => 'Kliknutí „expertů“',
	'intermediate-header' => 'Kliknutí „pokročilých“',
	'beginner-header' => 'Kliknutí „začátečníků“',
	'total-header' => 'Celkem kliknutí',
	'start-date' => 'Datum začátku (RRRRMMDD)',
	'end-date' => 'Datum konce (RRRRMMDD)',
	'increment-by' => 'Počet dní reprezentovaných každým bodem',
	'change-graph' => 'Změnit graf',
	'beginner' => 'Začátečník',
	'intermediate' => 'Pokročilý',
	'expert' => 'Expert',
);

/** German (Deutsch)
 * @author Metalhead64
 */
$messages['de'] = array(
	'clicktracking' => 'Benutzerfreundlichkeitsinitiative Klickverfolgung',
	'clicktracking-desc' => 'Klickverfolgung, gedacht für die Aufzeichnung von Aktionen, die nicht zu einer Seitenaktualisierung führen',
	'clicktracking-title' => 'Erzeugte Benutzerklicks',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'clicktracking' => 'Kliknjeńske pśeslědowanje iniciatiwy wužywajobnosći',
	'clicktracking-desc' => 'Kliknjeńske pśeslědowanje, myslone za slědowanje tšojenjow, kótarež njezawinuju aktualizaciju boka',
	'clicktracking-title' => 'Nakopjone wužywarske kliknjenja',
	'event-name' => 'Mě tšojenja',
	'expert-header' => 'Kliknjenja "ekspertow"',
	'intermediate-header' => 'Kliknjenja "pókšacanych"',
	'beginner-header' => 'Kliknjenja "zachopjeńkarjow"',
	'total-header' => 'Kliknjenja dogromady',
	'start-date' => 'Zachopny datum (YYYYMMDD)',
	'end-date' => 'Kóńcny datum (YYYYMMDD)',
	'increment-by' => 'Licba dnjow, kótaruž kuždy datowy dypk reprezentěrujo',
	'change-graph' => 'Grafisku liniju změniś',
	'beginner' => 'Zachopjeńkaŕ',
	'intermediate' => 'Pókšacony',
	'expert' => 'Ekspert',
);

/** Greek (Ελληνικά)
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'clicktracking' => 'Πατήστε παρακολούθηση της Πρωτοβουλίας Χρηστικότητας',
	'clicktracking-desc' => 'Πατήστε παρακολούθηση, προορίζεται για την παρακολούθηση εκδηλώσεων που δεν προκαλούν ανανέωση σελίδας',
	'clicktracking-title' => 'Συναθροισμένα κλικ χρήστη',
	'expert-header' => 'Κλικ "ειδικοί"',
	'intermediate-header' => 'Κλικ "μέτριοι"',
	'beginner-header' => 'Κλικ "αρχάριοι"',
	'beginner' => 'Αρχάριος',
	'intermediate' => 'Μέτριος',
	'expert' => 'Ειδικός',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'clicktracking-desc' => 'Sekvado de klakoj, por sekvi klakeventojn kiu ne kaŭzas paĝan refreŝigon',
	'event-name' => 'Eventa nomo',
	'expert-header' => 'Klakoj de "Spertuloj"',
	'intermediate-header' => 'Klakoj de "progresantoj"',
	'beginner-header' => 'Klakoj de "novuloj"',
	'start-date' => 'Komenca Dato (JJJJMMTT)',
	'end-date' => 'Fina Dato (JJJJMMTT)',
	'beginner' => 'Novulo',
	'intermediate' => 'Progresanto',
	'expert' => 'Spertulo',
);

/** Spanish (Español)
 * @author Antur
 * @author Crazymadlover
 */
$messages['es'] = array(
	'clicktracking-title' => 'Clicks de usuario agregados',
	'start-date' => 'Fecha de inicio (AAMMDD)',
	'end-date' => 'Fecha de fin (AAMMDD)',
	'beginner' => 'Principiante',
	'intermediate' => 'Intemedio',
	'expert' => 'Experto',
);

/** Basque (Euskara)
 * @author An13sa
 */
$messages['eu'] = array(
	'start-date' => 'Hasiera Data (UUUUHHEE)',
	'end-date' => 'Amaiera Data (UUUUHHEE)',
	'change-graph' => 'Grafikoa aldatu',
	'beginner' => 'Hasiberria',
	'intermediate' => 'Maila ertainekoa',
	'expert' => 'Aditua',
);

/** Finnish (Suomi)
 * @author Str4nd
 */
$messages['fi'] = array(
	'clicktracking' => 'Käytettävyyshankkeen klikkausten seuranta',
	'clicktracking-desc' => 'Klikkausten seuranta, tarkoituksena seurata tapahtumia, jotka eivät aiheuta sivun uudelleenlataamista.',
);

/** French (Français)
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'clicktracking' => "Suivi de clics de l'initiative d'utilisabilité",
	'clicktracking-desc' => 'Suivi de clics, visant à traquer les événements qui ne causent pas un rechargement de page',
	'clicktracking-title' => "Agrégation des clics d'utilisateurs",
	'event-name' => "Nom de l'événement",
	'expert-header' => 'Clics « experts »',
	'intermediate-header' => 'Clics « intermédiaires »',
	'beginner-header' => 'Clics « débutants »',
	'total-header' => 'Total des clics',
	'start-date' => 'Date de début (AAAAMMJJ)',
	'end-date' => 'Date de fin (AAAAMMJJ)',
	'increment-by' => 'Nombre de jours que représente chaque point de donnée',
	'change-graph' => 'Graphe de change',
	'beginner' => 'Débutant',
	'intermediate' => 'Intermédiaire',
	'expert' => 'Expert',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'clicktracking' => 'Seguimento dos clics da Iniciativa de usabilidade',
	'clicktracking-desc' => 'Seguimento dos clics, co obxectivo de seguir os acontecementos que non causan unha actualización da páxina',
	'clicktracking-title' => 'Clics de usuario engadidos',
	'event-name' => 'Nome do evento',
	'expert-header' => 'Clics "expertos"',
	'intermediate-header' => 'Clics "intermedios"',
	'beginner-header' => 'Clics "principiantes"',
	'total-header' => 'Total de clics',
	'start-date' => 'Data de inicio (AAAAMMDD)',
	'end-date' => 'Data de fin (AAAAMMDD)',
	'increment-by' => 'Número de días que representa cada punto de datos',
	'change-graph' => 'Gráfica de cambio',
	'beginner' => 'Principiante',
	'intermediate' => 'Intermedio',
	'expert' => 'Experto',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'clicktracking' => 'D Klickverfolgig vu dr Benutzerfrejndligkeits-Initiative',
	'clicktracking-desc' => 'Klickverfolgig, fir Aktione, wu kei Syteaktualisierig verursache',
	'clicktracking-title' => 'Zämmegfassti Benutzerklicks',
	'event-name' => 'Ereignis',
	'expert-header' => '„Experte“-Klicks',
	'intermediate-header' => 'Klicks vu „Mittlere“',
	'beginner-header' => '„Aafänger“-Klicks',
	'total-header' => 'Klicks insgsamt',
	'start-date' => 'Startdatum (JJJJMMTT)',
	'end-date' => 'Änddatum (JJJJMMTT)',
	'increment-by' => 'Aazahl vu Täg, wu ne jede Punkt derfir stoht',
	'change-graph' => 'Abbildig ändere',
	'beginner' => 'Aafänger',
	'intermediate' => 'Mittlere',
	'expert' => 'Expert',
);

/** Croatian (Hrvatski)
 * @author Suradnik13
 */
$messages['hr'] = array(
	'clicktracking' => 'Praćenje klikova u Inicijativi za uporabljivosti',
	'clicktracking-desc' => 'Praćenje klikova, napravljeno za praćenje događaja koji ne dovode do osvježavanja stanice',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'clicktracking' => 'Kliknjenske přesćěhanje iniciatiwy wužiwajomnosće',
	'clicktracking-desc' => 'Kliknjenske přesćěhanje, myslene za přesćěhowanje podawkow, kotrež aktualizaciju strony njezawinuja',
	'clicktracking-title' => 'Nahromadźene wužiwarske kliknjenja',
	'event-name' => 'Mjenp podawka',
	'expert-header' => 'Kliknjenja "ekspertow"',
	'intermediate-header' => 'Kliknjenja "pokročenych"',
	'beginner-header' => 'Kliknjenja "započatkarjow"',
	'total-header' => 'Kliknjenja dohromady',
	'start-date' => 'Spočatny datum (YYYYMMDD)',
	'end-date' => 'Kónčny datum (YYYYMMDD)',
	'increment-by' => 'Ličba dnjow, kotruž kóždy datowy dypk reprezentuje',
	'change-graph' => 'Grafisku liniju změnić',
	'beginner' => 'Započatkar',
	'intermediate' => 'Pokročeny',
	'expert' => 'Ekspert',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'clicktracking' => 'Usability Initiative kattintásszámláló',
	'clicktracking-desc' => 'Kattintásszámláló, az olyan események rögzítésére, melyekhez nem szükséges a lap frissítése',
	'clicktracking-title' => 'A szerkesztők kattintásainak összesítése',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'clicktracking' => 'Traciamento de clics del Initiativa de Usabilitate',
	'clicktracking-desc' => 'Traciamento de clics, pro traciar eventos que non causa un recargamento de pagina',
	'clicktracking-title' => 'Clics aggregate de usatores',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Irwangatot
 */
$messages['id'] = array(
	'clicktracking' => 'Pelacak klik Inisiatif Kebergunaan',
	'clicktracking-desc' => "Pelacak klik, digunakan untuk melacak kejadian yang tidak menyebabkan ''refresh'' halaman",
	'clicktracking-title' => 'Diagregasikan pengguna mengklik',
);

/** Japanese (日本語)
 * @author Fryed-peach
 * @author 青子守歌
 */
$messages['ja'] = array(
	'clicktracking' => 'Usability Initiative クリック追跡',
	'clicktracking-desc' => 'クリック追跡：ページの再描画を引き起こさないイベントを追跡記録する機能',
	'clicktracking-title' => '利用者によるクリックの総計',
	'event-name' => 'イベント名',
	'expert-header' => '「上級者」のクリック数',
	'intermediate-header' => '「中級者」のクリック数',
	'beginner-header' => '「初級者」のクリック数',
	'total-header' => 'クリック回数合計',
	'start-date' => '開始日 (YYYYMMDD)',
	'end-date' => '終了日 (YYYYMMDD)',
	'increment-by' => '各データ点が表す日数',
	'change-graph' => 'グラフ変更',
	'beginner' => '初級者',
	'intermediate' => '中級者',
	'expert' => '上級者',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'clicktracking' => 'Dä <i lang="en">Wikipedia Usability Initiative</i> ier Kleckverfolljung',
	'clicktracking-desc' => 'Klecks un Akßuhne Verfollje, di kein neu Sigg afroofe donn.',
	'clicktracking-title' => 'Jesammte Klecks',
	'event-name' => 'Da Name vun dämm, wat passeet es',
	'expert-header' => 'Klecks vun „{{int:Expert}}“',
	'intermediate-header' => 'Klecks vun „{{int:Intermediate}}“',
	'beginner-header' => 'Klecks vun „{{int:Beginner}}e“',
	'total-header' => 'Jesampzahl aan Kleks',
	'start-date' => 'Et Dattum vum Aanfang (en dä Forrem: JJJJMMDD)',
	'end-date' => 'Et Dattum vum Engk (en dä Forrem: JJJJMMDD)',
	'increment-by' => 'De Aanzahl Dääsch, woh jede Pungk em Diajramm daashtälle sull',
	'change-graph' => 'Dat Diajramm ändere',
	'beginner' => 'Aanfänger udder Neuling',
	'intermediate' => 'Meddel',
	'expert' => 'Mer kännt sesch uß',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'clicktracking' => 'Benotzerfrëndlechkeetsinitiative Suivi vun de Klicken',
	'clicktracking-desc' => "Suivi vun de Klicken, fir déi Aktiounen z'erfaassen déi net zu engem neie Luede vun der Säit féieren",
	'clicktracking-title' => 'Vun de Benotzer gemaachte Klicken',
	'event-name' => 'Numm vum Evenement',
	'expert-header' => '"Expert"-Klicken',
	'intermediate-header' => '"Duerschnëtt"-Klicken',
	'beginner-header' => '"Ufänker"-Klicken',
	'total-header' => 'Total vun de Klicken',
	'start-date' => 'Ufanksdatum (YYYYMMDD)',
	'end-date' => 'Schlussdatum (YYYYMMDD)',
	'increment-by' => 'Zuel vun Deeg déi vun all Datepunkt duergestallt ginn',
	'change-graph' => 'Ännerungs-Grafik',
	'beginner' => 'Ufänger',
	'intermediate' => 'Dertëschent',
	'expert' => 'Expert',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'clicktracking' => 'Следење на кликнувања на Иницијативата за употребливост',
	'clicktracking-desc' => 'Следење на кликнувања, наменето за следење на постапки кои не предизвикуваат превчитување на страницата',
);

/** Malay (Bahasa Melayu)
 * @author Kurniasan
 */
$messages['ms'] = array(
	'clicktracking' => 'Pengesanan klik Inisiatif Kebolehgunaan',
	'clicktracking-desc' => 'Pengesanan klik, bertujuan untuk mengesan peristiwa-peristiwa yang tidak menyebabkan penyegaran semula sebuah laman.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'clicktracking' => 'Klikvolgen voor het Bruikbaarheidsinitiatief',
	'clicktracking-desc' => 'Klikvolgen voor het volgens van handelingen die niet het oproepen van een nieuwe pagina tot gevolg hebben',
	'clicktracking-title' => 'Samengevoegde gebruikerskliks',
	'event-name' => 'Gebeurtenis',
	'expert-header' => '"Expert"-kliks',
	'intermediate-header' => '"Gemiddeld"-kliks',
	'beginner-header' => '"Beginner"-kliks',
	'total-header' => 'Kliktotaal',
	'start-date' => 'Startdatum (JJJJMMDD)',
	'end-date' => 'Einddatum (JJJJMMDD)',
	'increment-by' => 'Aantal dagen dat ieder punt representeert',
	'change-graph' => 'Grafiek wijzigen',
	'beginner' => 'Beginner',
	'intermediate' => 'Gemiddeld',
	'expert' => 'Expert',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'clicktracking' => "Seguit de clics de l'iniciativa d'utilizabilitat",
	'clicktracking-desc' => 'Seguit de clics, visant a tracar los eveniments que causan pas un recargament de pagina',
	'clicktracking-title' => "Agregacion dels clics d'utilizaires",
	'event-name' => "Nom de l'eveniment",
	'expert-header' => 'Clics « expèrts »',
	'intermediate-header' => 'Clics « intermediaris »',
	'beginner-header' => 'Clics « debutants »',
	'total-header' => 'Total dels clics',
	'start-date' => 'Data de començament (AAAAMMJJ)',
	'end-date' => 'Data de fin (AAAAMMJJ)',
	'increment-by' => 'Nombre de jorns que representa cada punt de donada',
	'change-graph' => 'Graf de cambi',
	'beginner' => 'Debutant',
	'intermediate' => 'Intermediari',
	'expert' => 'Expèrt',
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'clicktracking' => 'Śledzenie kliknięć dla inicjatywy użyteczności',
	'clicktracking-desc' => 'Śledzenie kliknięć, przeznaczone do poszukiwania zdarzeń, które nie powodują odświeżenia strony',
	'clicktracking-title' => 'Suma kliknięć użytkowników',
	'event-name' => 'Nazwa zdarzenia',
	'expert-header' => 'Kliknięcia „specjalistów”',
	'intermediate-header' => 'Kliknięcia „zaawansowanych”',
	'beginner-header' => 'Kliknięcia „nowicjuszy”',
	'total-header' => 'Wszystkich kliknięć',
	'start-date' => 'Data rozpoczęcia (RRRRMMDD)',
	'end-date' => 'Data zakończenia (RRRRMMDD)',
	'change-graph' => 'Wykres zmian',
	'beginner' => 'Nowicjusz',
	'intermediate' => 'Zaawansowany',
	'expert' => 'Specjalista',
);

/** Piedmontese (Piemontèis)
 * @author Dragonòt
 */
$messages['pms'] = array(
	'clicktracking' => "Trassadura dij click ëd l'Usability Initiative",
	'clicktracking-desc' => "Trassadura dij click, për trassé dj'event cha a causo pa ël refresh ëd na pàgina",
	'clicktracking-title' => "Click agregà dl'utent",
	'event-name' => "Nòm ëd l'event",
	'expert-header' => 'Click d\'"Espert"',
	'intermediate-header' => 'Click dj\'"antërmedi"',
	'beginner-header' => 'Click ëd "prinsipiant"',
	'total-header' => 'Click totaj',
	'start-date' => 'Data ëd partensa (AAAAMMDD)',
	'end-date' => 'Data ëd fin (AAAAMMDD)',
	'increment-by' => 'Nùmer ëd di che minca pont a arpresenta',
	'change-graph' => 'Cambia ël graf',
	'beginner' => 'Prinsipiant',
	'intermediate' => 'Antërmedi',
	'expert' => 'Espert',
);

/** Portuguese (Português)
 * @author Giro720
 */
$messages['pt'] = array(
	'change-graph' => 'Mudar gráfico',
	'beginner' => 'Iniciante',
	'intermediate' => 'Intermediário',
	'expert' => 'Experiente',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Eduardo.mps
 */
$messages['pt-br'] = array(
	'clicktracking' => 'Monitoramento de cliques da Iniciativa de Usabilidade',
	'clicktracking-desc' => 'Monitoramento de cliques, destinado ao monitoramento de eventos que não causem uma atualização de página',
);

/** Russian (Русский)
 * @author HalanTul
 * @author Kv75
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'clicktracking' => 'Отслеживание нажатий в рамках Инициативы юзабилити',
	'clicktracking-desc' => 'Отслеживание нажатий. Предназначается для отслеживания событий, не приводящих к обновлению страницы',
	'clicktracking-title' => 'Собранные щелчки участников',
	'event-name' => 'Название события',
	'expert-header' => 'Нажатия «экспертов»',
	'intermediate-header' => 'Нажатия «средних участников»',
	'beginner-header' => 'Нажатия «новичков»',
	'total-header' => 'Всего нажатий',
	'start-date' => 'Дата начала (ГГГГММДД)',
	'end-date' => 'Дата окончания (ГГГГММДД)',
	'increment-by' => 'Количество дней, которое представляет каждая точка данных',
	'change-graph' => 'Изменить график',
	'beginner' => 'Новичок',
	'intermediate' => 'Средний участник',
	'expert' => 'Эксперт',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'clicktracking-desc' => 'Баттааһыны кэтээһин. Сирэйи саҥардыбат түбэлтэлэри кэтииргэ туттуллар',
	'clicktracking-title' => 'Кыттааччылар баттааһыннарын хомуура',
	'event-name' => 'Түбэлтэ аата',
	'expert-header' => '"Экспертар" баттааһыннара (клик)',
	'intermediate-header' => '"Орто кыттааччылар" баттааһыннара (клик)',
	'beginner-header' => '"Саҕалааччылар" баттааһыннара (клик)',
	'total-header' => 'Баттааһын барытын ахсаана',
	'start-date' => 'Саҕаламмыт күнэ-ыйа (ССССЫЫКК)',
	'end-date' => 'Бүппүт күнэ-дьыла (ССССЫЫКК)',
	'change-graph' => 'Графигы уларытыы',
	'beginner' => 'Саҥа кыттааччы',
	'intermediate' => 'Бороохтуйбут кыттааччы',
	'expert' => 'Эксперт',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'clicktracking' => 'Sledovanie kliknutí pre Iniciatívu použiteľnosti',
	'clicktracking-desc' => 'Sledovanie kliknutí, na sledovanie udalostí, ktoré nespôsobujú opätovné načítanie stránky',
	'clicktracking-title' => 'Agregované kliknutia používateľa',
	'event-name' => 'Názov udalosti',
	'expert-header' => 'Kliknutia „expertov“',
	'intermediate-header' => 'Kliknutia „pokročilých“',
	'beginner-header' => 'Kliknutia „začiatočníkov“',
	'total-header' => 'Kliknutí celkom',
	'start-date' => 'Dátum začiatku (YYYYMMDD)',
	'end-date' => 'Dátum konca (YYYYMMDD)',
	'increment-by' => 'Počet dní, ktorý predstavuje každý z bodov v dátach',
	'change-graph' => 'Zmeniť graf',
	'beginner' => 'Začiatočník',
	'intermediate' => 'Pokročilý',
	'expert' => 'Expert',
);

/** Slovenian (Slovenščina)
 * @author Smihael
 */
$messages['sl'] = array(
	'clicktracking' => 'Sledenje klikom Iniciative za uporabnost',
	'clicktracking-desc' => 'Sledenje klikom, namenjeno odkrivanju dogodkov, ki preprečujejo osvežitev strani med urejanjem',
);

/** Turkish (Türkçe)
 * @author Joseph
 */
$messages['tr'] = array(
	'clicktracking' => 'Kullanılabilirlik Girişimi tıklama izleme',
	'clicktracking-desc' => 'Tıklama izleme, bir sayfa yenilemesine sebep olmadan olayları izleme amaçlı',
	'clicktracking-title' => 'Toplu kullanıcı tıklamaları',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'clicktracking' => "Traciamento click de l'Inissiativa par l'Usabilità",
	'clicktracking-desc' => 'Traciamento dei click, par traciare i eventi che no provoca mia un refresh de la pagina.',
	'clicktracking-title' => 'Agregassion dei clic dei utenti',
	'start-date' => 'Data de inissio (AAAAMMGG)',
	'end-date' => 'Data de fine(AAAAMMGG)',
	'beginner' => 'Prinsipiante',
	'intermediate' => 'Intermedio',
	'expert' => 'Esperto',
);

/** Veps (Vepsan kel')
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'beginner' => 'Augotai',
	'expert' => 'Ekspert',
);

/** Vietnamese (Tiếng Việt)
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'clicktracking' => 'Theo dõi nhấn chuột Sáng kiến Khả dụng',
	'clicktracking-desc' => 'Theo dõi hành vi nhấn chuột, dùng để theo dõi các hoạt động không làm tươi trang',
	'clicktracking-title' => 'Tổng số nhấn chuột của thành viên',
	'event-name' => 'Tên sự kiện',
	'expert-header' => 'Cú nhấn "chuyên gia"',
	'intermediate-header' => 'Cú nhấn "trung bình"',
	'beginner-header' => 'Cú nhấn "người mới"',
	'total-header' => 'Tổng số lần nhấn',
	'start-date' => 'Ngày bắt đầu (YYYYMMDD)',
	'end-date' => 'Ngày kết thúc (YYYYMMDD)',
	'increment-by' => 'Số ngày mà mỗi điểm dữ liệu thể hiện',
	'change-graph' => 'Đồ thị thay đổi',
	'beginner' => 'Người mới',
	'intermediate' => 'Trung bình',
	'expert' => 'Chuyên gia',
);

/** Yue (粵語)
 * @author Shinjiman
 */
$messages['yue'] = array(
	'clicktracking' => '可用性倡議撳追蹤',
	'clicktracking-desc' => '撳追蹤，響唔使重載版嘅情況之下追蹤撳',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Shinjiman
 */
$messages['zh-hans'] = array(
	'clicktracking' => '可用性倡议点击追踪',
	'clicktracking-desc' => '点击追踪，不在重载页面的情况中用来追踪点击',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Shinjiman
 */
$messages['zh-hant'] = array(
	'clicktracking' => '可用性倡議點擊追蹤',
	'clicktracking-desc' => '點擊追蹤，不在重載頁面的情況中用來追蹤點擊',
);

