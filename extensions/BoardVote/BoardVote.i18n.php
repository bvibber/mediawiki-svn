<?php
/**
 * Internationalisation file for BoardVote extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgBoardVoteMessages = array();

$wgBoardVoteMessages['en'] = array(
	'boardvote'               => "Wikimedia Board of Trustees election",
	'boardvote_entry'         => "* [[Special:Boardvote/vote|Vote]]
* [[Special:Boardvote/list|List votes to date]]
* [[Special:Boardvote/dump|Dump encrypted election record]]",
	'boardvote_intro'         => "
<p>Welcome to the second elections for the Wikimedia Board of Trustees. We are
voting for two people to represent the community of users on the various
Wikimedia projects. They will help to determine the future direction
that the Wikimedia projects will take, individually and as a group, and
represent <em>your</em> interests and concerns to the Board of Trustees. They will
decide on ways to generate income and the allocation of moneys raised.</p>

<p>Please read the candidates' statements and responses to queries carefully
before voting. Each of the candidates is a respected user, who has contributed
considerable time and effort to making these projects a welcoming environment
committed to the pursuit and free distribution of human knowledge.</p>

<p>You may vote for as many candidates as you want. The
candidate with the most votes in each position will be declared the winner of that
position. In the event of a tie, a run-off election will be held.</p>

<p>For more information, see:</p>
<ul><li><a href=\"http://meta.wikimedia.org/wiki/Election_FAQ_2006\" class=\"external\">Election FAQ</a></li>
<li><a href=\"http://meta.wikimedia.org/wiki/Election_Candidates_2006\" class=\"external\">Candidates</a></li></ul>
",
	'boardvote_intro_change'  => "<p>You have voted before. However you may change
your vote using the form below. Please check the boxes next to each candidate whom
you approve of.</p>",
	'boardvote_footer'        => "&nbsp;", # Don't translate this
	'boardvote_entered'       => "Thank you, your vote has been recorded.

If you wish, you may record the following details. Your voting record is:

<pre>$1</pre>

It has been encrypted with the public key of the Election Administrators:

<pre>$2</pre>

The resulting encrypted version follows. It will be displayed publicly on [[Special:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Back]]",
	'boardvote_notloggedin'   => "You are not logged in. To vote, you must use an account with at least $1 contributions before $2, and with a first edit before $3.",
	'boardvote_notqualified'  => "You are not qualified to vote in this election. You need to have made $3 edits before $2, you have made $1. Also, your first edit was at $4, it needs to be before $5. ",
	'boardvote_novotes'       => "Nobody has voted yet.",
	'boardvote_time'          => "Time",
	'boardvote_user'          => "User",
	'boardvote_edits'         => "Edits",
	'boardvote_days'          => "Days",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "User agent",
	'boardvote_listintro'     => "<p>This is a list of all votes which have been recorded 
to date. $1 for the encrypted data.</p>",
	'boardvote_dumplink'      => "Click here",
	'boardvote_submit'        => 'OK',
	'boardvote_strike'        => "Strike",
	'boardvote_unstrike'      => "Unstrike",
	'boardvote_needadmin'     => "Only election administrators can perform this operation.",
	'boardvote_sitenotice'    => "<a href=\"{{localurle:Special:Boardvote/vote}}\">Wikimedia Board Elections</a>:  Vote open until July 12",
	'boardvote_notstarted'    => 'Voting has not yet started',
	'boardvote_closed'        => 'Voting is now closed, see [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2006/En the elections page for results] soon.',
	'boardvote_edits_many'    => 'many',
	'group-boardvote'         => 'Board vote admins',
	'group-boardvote-member'  => 'Board vote admin',
	'grouppage-boardvote'     => '{{ns:project}}:Board vote admin',
);
$wgBoardVoteMessages['cs'] = array(
	'boardvote'               => "Volby do Správní rady nadace Wikimedia",
	'boardvote_entry'         => "* [[Special:Boardvote/vote|Hlasovat]]
* [[Special:Boardvote/list|Seznam již hlasujících]]
* [[Special:Boardvote/dump||Šifrovaný záznam hlasování]]",
	'boardvote_intro'         => "
<p>Vítejte u třetích voleb do Správní rady nadace Wikimedia. Hlasováním
bude zvolen jeden zástupce komunity uživatelů všech projektů nadace. Tento
zástupce budou pomáhat v rozhodování o budoucím směru rozvoje projektů,
jednotlivě i jako skupina, a budou reprezentovat <em>vaše</em> zájmy a
ohledy ve Správní radě. Bude také rozhodovat o způsobech získávání
finančních prostředků a využívání získaných peněz.</p>

<p>Před hlasováním si laskavě důkladně přečtěte vyjádření kandidátů a jejich
odpovědi na dotazy. Všichni kandidáti jsou respektovanými uživateli, kteří
přispěli velkým množstvím času a úsilí při snaze učinit z projektů přátelské
prostředí cílené na shromažďování a volné šíření znalostí lidstva.</p>

<p>Můžete hlasovat pro libovolně mnoho kandidátů. Kandidát, který bude mít
pro příslušnou pozici nejvíce hlasů, bude do této pozice nominován. V případě
shody bude pořádáno druhé kolo hlasování.</p>

<p>Pamatujte, že můžete hlasovat jen jednou. I pokud máte více než 400 editací na více projektech, tak to neznamená, že máte právo volit dvakrát. Pokud se později rozhodnete změnit své hlasy, učiňte tak na projektu, kde jste hlasování provedl(a) předtím.</p>

<p>Další informace (anglicky a v dalších jazycích) najdete na následujících stránkách:</p>
<ul><li><a href=\"http://meta.wikipedia.org/wiki/Election_FAQ_2006\" class=\"external\">Často kladené otázky o hlasování</a></li>
<li><a href=\"http://meta.wikipedia.org/wiki/Election_Candidates_2006\" class=\"external\">Kandidáti</a></li></ul>	
",
	'boardvote_intro_change'  => "<p>Již jste hlasoval(a). Můžete však svůj hlas změnit prostřednictvím níže uvedeného formuláře. Zaškrtněte čtvereček u každého kandidáta, kterého schvalujete.</p>",
	'boardvote_entered'       => "Děkujeme vám, váš hlas byl zaznamenán.

Pokud si přejete, můžete si poznamenat podrobnosti. Váš záznam o hlasování je:

<pre>$1</pre>

Byl zašifrován s použitím veřejného klíče volebních úředníků:

<pre>$2</pre>

Výsledná šifrovaná podoba následuje. Bude veřejně dostupná na stránce [[Speciální:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Zpět]]",
  'boardvote_notloggedin'   => 'Nejste přihlášen(a). Pro hlasování musíte použít účet s nejméně $1 příspěvky před $2 a první editací před $3.',
  'boardvote_notqualified'  => 'Litujeme, nejste oprávněn(a) hlasovat v těchto volbách, protože před $2 jste provedl(a) pouze $1 editací. Je vyžadováno $3 editací. Nebo Vaše první editace nebyla před $5, ale až $4.',
  'boardvote_notstarted'    => 'Volby ještě nezačaly.',
  'boardvote_novotes'       => 'Nikdo dosud nehlasoval.',
	'boardvote_time'          => "Datum a čas",
	'boardvote_user'          => "Uživatel",
	'boardvote_edits'         => "Editací",
	'boardvote_days'          => "Dní",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "Klient",
	'boardvote_listintro'     => "<p>Toto je seznam všech dosud zaznamenaných hlasů. Také můžete získat $1.</p>",
	'boardvote_dumplink'      => "šifrovaný záznam hlasování",
	'boardvote_submit'        => 'OK',
	'boardvote_strike'        => "Zaškrtnout",
	'boardvote_unstrike'      => "Odškrtnout",
	'boardvote_needadmin'     => "Pouze volební správci mohou provést tuto operaci.",
	'boardvote_sitenotice'    => "<a href=\"{{localurle:Special:Boardvote/vote}}\">Volby do správní rady nadace Wikimedia</a>:",
	'boardvote_notstarted'    => 'Volby ještě nezačaly.',
	'boardvote_closed'        => 'Volby skončily. Podívejte se na [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2006/Cs výsledky].',
	'boardvote_edits_many'    => 'mnoho',
	'group-boardvote'         => 'Volební správci',
	'group-boardvote-member'  => 'Volební správce',
	'grouppage-boardvote'     => '{{ns:project}}:Volební správce',
);
$wgBoardVoteMessages['he'] = array(
	'boardvote'               => "בחירות לחבר הנאמנים של ויקימדיה",
	'boardvote_entry'         => "* [[{{ns:special}}:Boardvote/vote|הצבעה]]
* [[{{ns:special}}:Boardvote/list|רשימת ההצבעות נכון לעכשיו]]
* [[{{ns:special}}:Boardvote/dump|ההעתק המוצפן של הבחירות]]",
	'boardvote_intro'         => "
<p>ברוכים הבאים לבחירות השניות לחבר הנאמנים של קרן ויקימדיה. בהצבעה זו ייבחרו שני נציגים אשר ייצגו את הקהילה של משתמשי המיזמים השונים של ויקימדיה. הם יעזרו להחליט על כיוון התפתחותם העתידי של המיזמים השונים, כבודדים וכקבוצה, וייצגו את האינטרסים והדאגות <em>שלך</em> בחבר הנאמנים. הם יחליטו על הדרכים לבקשת תרומות ועל חלוקת המשאבים הכספיים.</p>

<p>אנא קראו בעיון, בטרם ההצבעה, את פרטי המועמדים ואת תשובותיהם לשאלות. כל אחד מן המועמדים והמועמדות הוא משתמש מוכר, אשר השקיע זמן רב ומאמץ להפוך את המיזמים הללו לסביבה נעימה המחויבת למטרת ההפצה חופשית של הידע האנושי.</p>

<p>באפשרותכם להצביע עבור מספר מועמדים. המועמדים עם מירב ההצבעות בכל עמדה יוכרזו כמנצחים בעמדה זו. במידה ויתקיים שיוויון בין מספר מועמדים, תתבצע הצבעה נוספת ביניהם.</p>

<p>למידע נוסף, ראו:</p>
<ul><li><a href=\"http://meta.wikimedia.org/wiki/Election_FAQ_2006\" class=\"external\">שאלות נפוצות על הבחירות</a></li>
<li><a href=\"http://meta.wikimedia.org/wiki/Election_Candidates_2006\" class=\"external\">המועמדים</a></li></ul>
",
	'boardvote_intro_change'  => "<p>כבר הצבעתם בעבר. עם זאת, באפשרותכם לשנות את הצבעתכם באמצעות הטופס המצורף למטה. אנא סמנו את תיבת הסימון ליד כל אחד מהמועמדים המועדפים עליכם.</p>",
	'boardvote_entered'       => "תודה לכם, הצבעתכם נרשמה.

אם ברצונכם בכך, אתם יכולים לרשום את הפרטים הבאים. ההצבעה נרשמה כ:

<pre>$1</pre>

היא הוצפנה באמצעות המפתח הציבורי של ועדת הבחירות:

<pre>$2</pre>

תוצאות ההצפנה מופיעות בהמשך. הן גם תופענה בפומבי בקישור [[{{ns:special}}:Boardvote/entry]].

<pre>$3</pre>

[[{{ns:special}}:Boardvote/entry|חזרה]]",
	'boardvote_notloggedin'   => "אינכם רשומים לחשבון. כדי להצביע, עליכם להשתמש בחשבון שיש לו לפחות $1 תרומות לפני $2, ושעריכתו הראשונה בוצעה לפני $3.",
	'boardvote_notqualified'  => "אינכם רשאים להצביע בבחירות הללו. תנאי הסף הם ביצוע $3 עריכות לפני $2, בעוד שאתם ביצעתם רק $1 עריכות. בנוסף, עריכתכם הראשונה הייתה בתאריך $4, בעוד היא צריכה להיות לנפי $5.",
	'boardvote_novotes'       => "איש לא הצביע עדיין.",
	'boardvote_time'          => "שעה",
	'boardvote_user'          => "משתמש",
	'boardvote_edits'         => "עריכות",
	'boardvote_days'          => "ימים",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "זיהוי הדפדפן",
	'boardvote_listintro'     => "<p>זוהי רשימה של כל ההצבעות שנרשמו עד כה. $1 כדי להגיע לנתונים המוצפנים.</p>",
	'boardvote_dumplink'      => "לחצו כאן",
	'boardvote_submit'        => 'הצבעה',
	'boardvote_strike'        => "גילוי",
	'boardvote_unstrike'      => "הסתרה",
	'boardvote_needadmin'     => "רק מנהלי הבחירות יכולים לבצע פעולה זו.",
	'boardvote_sitenotice'    => "<a href=\"{{localurle:{{ns:special}}:Boardvote/vote}}\">בחירות לחבר הנאמנים של ויקימדיה</a>: ההצבעה פתוחה עד 12 ביולי",
	'boardvote_notstarted'    => 'ההצבעה עדיין לא התחילה',
	'boardvote_closed'        => 'ההצבעה סגורה כעת, ראו [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2006/En את הדף על תוצאות הבחירות] בקרוב.',
	'boardvote_edits_many'    => 'הרבה',
	'group-boardvote'         => 'מנהלי הבחירות לחבר הנאמנים',
	'group-boardvote-member'  => 'מנהל הבחירות לחבר הנאמנים',
	'grouppage-boardvote'     => '{{ns:project}}:מנהל הבחירות לחבר הנאמנים',
);
$wgBoardVoteMessages['id'] = array(
	'boardvote'               => "Pemilihan Anggota Dewan Kepercayaan Yayasan Wikimedia",
	'boardvote_entry'         => "* [[Special:Boardvote/vote|Masukkan pilihan]]
* [[Special:Boardvote/list|Daftar pemilih sampai saat ini]]
* [[Special:Boardvote/dump|Data pemilihan terenkripsi]]",
	'boardvote_intro_change'  => "<p>Anda sudah pernah memilih. Walaupun demikian, Anda dapat mengganti pilihan Anda dengan menggunakan formulir di bawah. Harap cek kotak di samping tiap kandidat yang Anda setujui.</p>",
	'boardvote_entered'       => "Terima kasih, suara Anda telah dicatat.

Jika ingin, Anda dapat menyimpan detil berikut. Catatan pilihan Anda adalah:

<pre>$1</pre>

Data tersebut telah dienkripsi dengan kunci publik Pengurus Pemilihan:

<pre>$2</pre>

Berikut adalah hasil dari enkripsi. Data tersebut akan ditampilkan untuk publik di [[Special:Boardvote/sini]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Kembali]]",
	'boardvote_notloggedin'   => "Anda tidak masuk log. Untuk dapat memilih Anda harus menggunakan akun dengan paling tidak $1 suntingan sebelum $2, dan dengan suntingan pertama sebelum $3.",
	'boardvote_notqualified'  => "Anda tidak memiliki hak untuk memberikan suara dalam pemilihan ini. Anda harus memiliki $3 suntingan sebelum $2, sedangkan Anda hanya memiliki $1. Terlebih lagi, suntingan pertama Anda adalah pada $4, dimana disyaratkan harus sebelum $5.",
	'boardvote_novotes'       => "Belum ada pemilih.",
	'boardvote_time'          => "Waktu",
	'boardvote_user'          => "Pengguna",
	'boardvote_edits'         => "Suntingan",
	'boardvote_days'          => "Hari",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "Agen pengguna",
	'boardvote_listintro'     => "<p>Berikut adalah daftar semua suara yang telah masuk sampai hari ini. $1 untuk data terenkripsi.</p>",
	'boardvote_dumplink'      => "Klik di sini",
	'boardvote_submit'        => 'Kirim',
	'boardvote_strike'        => "Coret",
	'boardvote_unstrike'      => "Hapus coretan",
	'boardvote_needadmin'     => "Hanya pengurus pemilihan yang dapat melakukan tindakan ini.",
	'boardvote_notstarted'    => 'Pemilihan belum dimulai',
	'boardvote_closed'        => 'Pemilihan telah ditutup, lihat [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2006/En halaman pemilihan untuk mengetahui hasilnya] sebentar lagi.',
	'boardvote_edits_many'    => 'banyak',
	'group-boardvote'         => 'Pengurus pemilihan anggota dewan',
	'group-boardvote-member'  => 'Pengurus pemilihan anggota dewan',
	'grouppage-boardvote'     => '{{ns:project}}:Pengurus pemilihan anggota dewan',
);
$wgBoardVoteMessages['nl'] = array(
	"boardvote"               => "Wikimedia Board of Trustees-verkiezing",
	"boardvote_entry"         => "* [[Special:Boardvote/vote|Vote]]
* [[Special:Boardvote/list|Toon uitgebrachte stemmen]]
* [[Special:Boardvote/dump|Dump encrypted election record]]",
	"boardvote_intro"         => "
<p>Welkom bij de tweede verkiezingen voor de Wikimedia Board of Trustees. We
kiezen twee personen die de gebruikersgemeenschap vertegenwoordigen in de
verschillden Wikimedia-projecten. Ze bepalen mede de toekomstige richting
van Wikimedia-projecten, individueel en als groep, en behartigen <em>uw</em>
belangen en zorgen bij de Board of Trustees. Ze beslissen ook over hoe
inkomsten gemaakt kunnen worden en waar het opgehaalde geld aan wordt
besteed.</p>

<p>Lees alstublieft de kandidaatstelling en de antwoorden op vragen zorgvuldig
voordat u stemt. Iedere kandidaat is een gewaardeerde gebruiker die
aanzielijke hoeveelheden tijd en moeite heeft besteed aan het bouwen van
uitnodigende omgevingen die toegewijd zijn aan het nastreven en vrij verspreiden
van menselijke kennis.</p>

<p>U mag voor zoveel kandidaten stemmen als u wilt. De kandidaat met de meeste
stemmen voor iedere positie wordt tot winnaar uitgeroepen voor de betreffende
positie. In geval de stemmen staken wordt er een tweede ronde gehouden.</p>

<p>Meer informatie:</p>
<ul><li><a href=\"http://meta.wikimedia.org/wiki/Election_FAQ_2006\" class=\"external\">Bestuursverkiezing FAQ</a></li>
<li><a href=\"http://meta.wikimedia.org/wiki/Election_Candidates_2006\" class=\"external\">Kandidaten</a></li></ul>
",
	"boardvote_intro_change"  => "<p>U heeft al gestemd. U kunt uw stem wijzigen via
het onderstaande formulier. Vink alstublieft de vakjes naar iedere kandidaat die
u steunt aan.</p>",
	"boardvote_entered"       => "Dank u. Uw stem is verwerkt.

Als u wilt kunt u de volgende gegevens bewaren. Uw stem:

<pre>$1</pre>

Deze is versleuteld met de publieke sleutel van de Verkiezingscommissie:

<pre>$2</pre>

Nu volgt de versleutelde versie. Deze is openbaar en na te zien op [[Special:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Terug]]",
	"boardvote_notloggedin"   => "U bent niet aangemeld. U kunt stemmen als u voor $2 ten minste
$1 bewerkingen heeft gemaakt.",
	"boardvote_notqualified"  => "Sorry, u heeft voor $2 $1 bewerkingen gemaakt. Om te kunnen
stemmen heeft u er $3 nodig.",
	"boardvote_novotes"       => "Er is nog niet gestemd.",
	"boardvote_time"          => "Tijd",
	"boardvote_user"          => "Gebruiker",
	"boardvote_edits"         => "Bewerkingen",
	"boardvote_days"          => "Dagen",
	"boardvote_ua"            => "User-agent",
	"boardvote_listintro"     => "<p>Hieronder staan alle stemmen die tot nu toe zijn
uitgebracht. $1 voor de versleutelde gegevens.</p>",
	"boardvote_dumplink"      => "Klik hier",
	"boardvote_strike"        => "Ongeldig",
	"boardvote_unstrike"      => "Geldig",
	"boardvote_needadmin"     => "Alleen leden van de Verkiezingscommissie kunnen deze handeling uitvoeren.",
	'boardvote_edits_many' => 'veel',
	'group-boardvote'         => 'Board vote beheerders',
	'group-boardvote-member'  => 'Board vote beheerder',
	'grouppage-boardvote'     => '{{ns:project}}:Board vote beheerder',
);
$wgBoardVoteMessages['pl'] = array(
	'boardvote'               => "Wybory do Rady Powierniczej Fundacji Wikimedia",
	'boardvote_entry'         => "* [[Special:Boardvote/vote|Głosuj]]
* [[Special:Boardvote/list|Pokaż listę głosów]]
* [[Special:Boardvote/dump|Zrzut zakodowanych danych wyborów]]",
	'boardvote_intro_change'  => "<p>Już głosowałeś w tych wyborach. Możesz jednak zmienić swoje głosy za pomocą poniższego formularza. Zaznacz kandydatów, na których głosujesz.</p>",
	'boardvote_entered'       => "Dziękujemy, twój głos został zapisany.

Jeśli chcesz, możesz zapisać poniższe informacje. Oto zapis twojego głosu:

<pre>$1</pre>

Został on zakodowany poniższym kluczem publicznym Koordynatorów Wyborów:

<pre>$2</pre>

Oto zakodowana wersja. Będzie ona publicznie wyświetlona w [[Special:Boardvote/dump|zrzucie danych]].

<pre>$3</pre>

[[Special:Boardvote/entry|Wstecz]]",
	'boardvote_notloggedin'   => "Nie jesteś zalogowany. Aby głosować musisz posiadać konto z wkładem minimum $1 edycji od $2 oraz pierwszą edycją wykonaną przed $3.",
	'boardvote_notqualified'  => "Niestety nie jesteś uprawniony do głosowania, ponieważ wykonałeś tylko $1 edycji. Aby móc głosować musisz mieć minimum $3 edycji wykonanych przed $2, a twoja pierwsza edycja powinna mieć miejsce przed $5. Swoją pierwszą edycję wykonałeś $4.",
	'boardvote_novotes'       => "Nikt jeszcze nie głosował.",
	'boardvote_time'          => "Czas",
	'boardvote_user'          => "Użytkownik",
	'boardvote_edits'         => "Edycje",
	'boardvote_days'          => "dni",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "Klient",
	'boardvote_listintro'     => "<p>Oto lista wszystkich głosów oddanych jak dotąd. $1 dla zakodowanych danych.</p>",
	'boardvote_dumplink'      => "Kliknij tutaj",
	'boardvote_submit'        => 'zagłosuj',
	'boardvote_strike'        => "Skreślenie głosu",
	'boardvote_unstrike'      => "Przywrócenie głosu",
	'boardvote_needadmin'     => "Tylko koordynatorzy wyborów mogą wykonać tę akcję.",
	'boardvote_sitenotice'    => '<a href="{{localurle:Special:Boardvote/vote}}">Wybory Rady Powierniczej Fudacji Wikimedia</a>:  głosowanie otwarte do 21 września',
	'boardvote_notstarted'    => 'Głosowanie nie zostało jeszcze rozpoczęte',
	'boardvote_closed'        => 'Głosowanie zostało zakończone, niedługo [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2006/Pl na stronie wyborów] pojawią się wyniki.',
	'boardvote_edits_many'    => 'dużo',
	'group-boardvote'         => 'Koordynatorzy wyborów',
	'group-boardvote-member'  => 'Koordynator wyborów',
);
$wgBoardVoteMessages['pt'] = array(
	'boardvote'               => "Eleições para o Comité da Fundação Wikimedia",
	'boardvote_entry'         => "<!--* [[Special:Boardvote/vote|Votar]]-->
* [[Special:Boardvote/list|Listar votos por data]]
* [[Special:Boardvote/dump|Dados encriptados da eleição]]",
	'boardvote_intro'         => "
<p>Bem-vindo à segunda edição das eleições para o Comité da Fundação Wikimedia. A votação irá designar duas pessoas para representar a comunidade de utilizadores nos vários projectos Wikimedia. Essas duas pessoas irão ajudar a determinar a orientação futura a seguir pelos projectos Wikimedia, individualmente ou como um todo, e representar os <em>seus</em> interesses e preocupações em relação ao Comité. Irão, também, tomar as decisões respeitantes ao financiamento e alocação de fundos.</p>

<p>Por favor, leia cuidadosamente os discursos dos candidatos e respostas a perguntas antes de votar. Cada um dos candidatos é um utilizador respeitado, consideravelmente em tempo e dedicação para tornar estes projectos um ambiente acolhedor empenhado na procura e livre distribuição do conhecimento humano.</p>

<p>Poderá votar em tantos candidatos quantos desejar. O candidato que apurar mais votos em cada posição será declarado vencedor dessa posição. Em caso de empate, serão lançadas votações para desempate.</p>

<p>Para mais informações, consulte:</p>
<ul><li><a href=\"http://meta.wikimedia.org/wiki/Election_FAQ_2006\" class=\"external\">FAQ de eleição</a></li>
<li><a href=\"http://meta.wikimedia.org/wiki/Election_Candidates_2006\" class=\"external\">Candidatos</a></li></ul>
",
	'boardvote_intro_change'  => "<p>Já votou anteriormente. Contudo pode alterar o seu voto utilizando o formulário abaixo. Por favor marque a caixa ao lado de cada candidato que aprovar.</p>",
	'boardvote_entered'       => "Obrigado, o seu voto foi registado.

Se desejar pode guardar os seguintes detalhes. O seu registo de voto é:

<pre>$1</pre>

Foi encriptado com a chave pública dos Administradores da Eleição:

<pre>$2</pre>

A versão da encriptação segue-se, e será publicada em [[Especial:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Voltar]]",
	'boardvote_notloggedin'   => "Não se encontra autentificado. De modo a poder votar, deve utilizar uma conta com pelo menos $1 contribuições antes de $2.",
	'boardvote_notqualified'  => "Desculpe, mas só fez $1 edições antes de $2. Precisa de ter no mínimo $3 edições de modo a poder votar.",
	'boardvote_novotes'       => "Ninguém votou até ao momento.",
	'boardvote_time'          => "Data",
	'boardvote_user'          => "Utilizador",
	'boardvote_edits'         => "Contribuições",
	'boardvote_days'          => "Dias",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "Agente do utilizador",
	'boardvote_listintro'     => "<p>Esta é uma lista de todos votos registados até à data. $1 para os dados encriptados.</p>",
	'boardvote_dumplink'      => "Clique aqui",
	'boardvote_strike'        => "Riscar",
	'boardvote_unstrike'      => "Limpar risco",
	'boardvote_needadmin'     => "Apenas administradores podem efectuar esta operação.",
	'boardvote_sitenotice'    => "<a href=\"{{localurle:Especial:Boardvote/vote}}\">Comité da Fundação Wikimedia</a>: Votação aberta até 12 de Julho",
	'boardvote_closed'        => 'As eleições estão agora encerradas, ver [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2006/Pt a página de eleições para os resultados] brevemente.',
	'boardvote_edits_many'    => 'muitos',
	'group-boardvote'         => 'Board vote administradores',
	'group-boardvote-member'  => 'Board vote administrador',
	'grouppage-boardvote'     => '{{ns:project}}:Board vote admin',
);
$wgBoardVoteMessages['sv'] = array(
	'boardvote'               => "Val till Wikimedias styrelse (Wikimedia Board of Trustees)",
	'boardvote_entry'         => "* [[m:Election_candidates_2006/Sv|Kandidaternas presentationer]]
* [[Special:Boardvote/vote|Rösta]]
* [[Special:Boardvote/list|Lista röster]]
* [[Special:Boardvote/dump|Dumpa krypterad röstpost]]",
	'boardvote_intro'         => "<p>Välkommen till det tredje valet till Wikimedia Foundations styrelse. Vi ska välja en person som ska representera wikigemenskapen, det vill säga användarna på de olika Wikimedia-projekten. Denna person ska, tillsammans med styrelsens andra användarrepresentant, hjälpa till att bestämma Wikimediaprojektens framtida inriktning vart för sig och som grupp, och i styrelsen representera <em>dina</em> intressen och bekymmer. Styrelsen ska besluta om sätt att få in pengar och hur dessa ska fördelas.</p>

<p>Innan du röstar, läs kandidaternas programförklaringar och deras svar på andra användares frågor. Alla kandidaterna är respekterade anvädnare som lagt ner åtskillig tid och möda för att göra projekten till en välkomnande miljö, ägnat åt inskaffande och fri spridning av mänsklig kunskap.</p>

<p>Du kan rösta på så många kandidater som du önskar. Den kandidat som fått flest röster kommer att bli vald. Om det skulle bli oavgjort mellan några kandidater, kommer en extra valomgång att arrangeras.</p>

<p>En påminnelse: du får bara rösta en gång. Även om du har 400 redigeringar på flera olika projekt, innebär inte det att du har rätt att rösta flera gånger. Om du vill ändra din röst innan valet är slut, var snäll gör det från det projekt som du tidigare röstat ifrån.</p>

<p>Mera information hittas på:</p>
<ul><li><a href=\"http://meta.wikipedia.org/wiki/Election_FAQ_2006/Sv\" class=\"external\">Vanliga frågor</a></li>
<li><a href=\"http://meta.wikipedia.org/wiki/Election_candidates_2006/Sv\" class=\"external\">Kandidaterna</a></li></ul>",
	'boardvote_intro_change'  => "<p>Du har redan röstat. Emellertid kan du ändra din röst genom att använda nedanstående formulär. Var god markera rutorna invid de kandidater du röstar på.</p>

<ul><li><a href=\"http://meta.wikimedia.org/wiki/Election_candidates_2006/Sv\" class=\"external\">Kandidaternas presentationer</a></li>",
	'boardvote_entered'       => "Tack för det. Din röst är registrerad.

Om du så önskar, kan du notera följande detaljer. Din röst är registrerad som :

<pre>$1</pre>

Den är krypterad med valadministratörernas publika nyckel:

<pre>$2</pre>

Den resulterande krypterade versionen följer här. Den kommer att visas öppet på [[Special:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Tillbaka]]",
	'boardvote_notloggedin'   => "Du är inte inloggad. För att rösta måste du ha ett konto med minst $1 bidrag före $2.",
	'boardvote_notqualified'  => "Tyvärr har du enbart gjort $1 redigeringar före $2. Du måste ha minst $3 redigeringar för att få rösta.

Om du fick detta meddelande trots att du '''har gjort''' fler än $1 redigeringar i ett Wikimediaprojekt, v g försäkra dig om att du röstar från rätt projekt.",
	'boardvote_novotes'       => "Ingen har röstat ännu.",
	'boardvote_time'          => "Tid",
	'boardvote_user'          => "Användare",
	'boardvote_edits'         => "Redigeringar",
	'boardvote_listintro'     => "<p>Det här är en lista över alla röster som har registrerats hittills.
$1 för de krypterade uppgifterna.</p>",
	'boardvote_dumplink'      => "Klicka här",
	'boardvote_needadmin'     => "Endast valadministratörer kan utföra denna operation.",
	'boardvote_sitenotice'    => "<a href=\"{{localurle:Special:Boardvote/vote}}\">Styrelseval i Wikimediastiftelsen</a>:  Valet pågår till och med den 12 juli kl 02:00 (CEST)",
);
$wgBoardVoteMessages['wa'] = array(
	'boardvote' => 'Vôtaedje po les manaedjeus del fondåcion Wikimedia',
	'boardvote_entry' => '* [[Special:Boardvote/vote|Vôter]]
* [[Special:Boardvote/list|Djivêye des vôtaedjes dedja fwaits]]
* [[Special:Boardvote/dump|Djiveye des bultins]] (tchaeke bultin est on blok ecripté)',
	'boardvote_intro' => '<p>
Bénvnowe å prumî vôtaedje po les manaedjeus del fondåcion Wikimedia. 
Li vôtaedje c\' est po tchoezi deus djins ki cåzront å consey des manaedjeus po les contribouweus des diferins pordjets Wikimedia k\' overnut félmint po lzès fé viker:
on <strong>rprezintant des mimbes ki sont des contribouweus actifs</strong>,
eyet on <strong>rprezintant des uzeus volontaires</strong>.
Il aidront a defini l\' voye ki prindront les pordjets Wikimedia, ossu bén tchaeke pordjet ki zels tos come groupe, dj\' ô bén k\' i rprezintèt <em>vos</em> interesses divant l\' consey des manaedjes. I decidront so des sudjets come l\' ecwårlaedje eyet l\' atribouwaedje des çanses ås diferinnès bouyes.
</p>

<p>
Prindoz s\' i vs plait li tins di bén lére li prezintaedje di tchaesconk des candidats dvant d\' vôter.
Tchaeke des candidats est èn uzeu respecté del kiminaalté, k\' a contribouwé bråmint do tins eyet ds efoirts po fé di ces pordjets èn evironmint amiståve ey ahessåve, et ki croeyèt fel å franc cossemaedje del kinoxhaence amon l\' djin.
</p>

<p>
Vos ploz vôter po ostant d\' candidats ki vos vloz dins tchaeke plaece.
Li candidat avou l\' pus d\' vwès po tchaeke plaece serè rclamé wangneu
Dins l\' cas k\' i gn årè ewalisté inte deus prumîs candidats, on deujhinme vôtaedje serè fwait po les dispårti.
</p>

<p>
Po pus di racsegnes, loukîz a:
</p>
<ul>
<li><a href="http://meta.wikimedia.org/wiki/Election_FAQ" class="external">FAQ sol vôtaedje</a> (en inglès)</li>
<li><a href="http://meta.wikimedia.org/wiki/Election_Candidates" class="external">Candidats</a></li>
</ul>',
	'boardvote_intro_change' => '<p>
Vos avoz ddja voté.
Mins vos ploz tot l\' minme candjî vosse vôte, po çoula
rifjhoz ene tchuze tot clitchant so les boesses a clitchîz des
candidats ki vos estoz d\' acoird avou zels.
</p>',
	'boardvote_entered' => 'Gråces, vosse vôtaedje a stî conté.

Si vos vloz, vos ploz wårder les informåcions shuvantes.
Vosse bultin a stî eredjîstré come:

<pre>$1</pre>

Il a stî ecripté avou l\' clé publike des manaedjeus do vôtaedje:

<pre>$2</pre>

Vosse bultins ecripté est chal pa dzo. Tos les bultins ecriptés polèt
esse publicmint veyous so [[Special:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|En erî]]',
	'boardvote_notloggedin' => 'Vos n\' estoz nén elodjî.
Po pleur vôter vos dvoz esse elodjî eyet vosse conté
doet aveur stî ahivé i gn a 90 djoûs pol moens.',
	'boardvote_notqualified' => 'Dji rgrete, mins vosse prumî contribouwaedje a stî fwait
i gn a $1 djoûs seulmint.
Po pleur vôter vos dvoz aveur contribouwé po pus long ki
90 djoûs.',
	'boardvote_novotes' => 'I gn a co nolu k\' a vôté.',
	'boardvote_time' => 'Date ey eure',
	'boardvote_user' => 'Uzeu',
	'boardvote_edits' => 'Contribs',
	'boardvote_days' => 'Djoûs',
	'boardvote_listintro' => '<p>Çouchal, c\' est ene djivêye di totes les djins
k\' ont ddja vote disk\' asteure.
$1 po les dnêyes sourdant des bultins.</p>',
	'boardvote_dumplink' => 'Clitchîz chal',
);
?>
