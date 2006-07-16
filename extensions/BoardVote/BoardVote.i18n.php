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
	'boardvote_entry'         => "<!--* [[Special:Boardvote/vote|Vote]]-->
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
<ul><li><a href=\"http://meta.wikipedia.org/wiki/Election_FAQ_2005\" class=\"external\">Election FAQ</a></li>
<li><a href=\"http://meta.wikipedia.org/wiki/Election_Candidates_2005\" class=\"external\">Candidates</a></li></ul>
",
	'boardvote_intro_change'  => "<p>You have voted before. However you may change 
your vote using the form below. Please check the boxes next to each candidate whom 
you approve of.</p>",
	'boardvote_footer'        => "&nbsp;",
	'boardvote_entered'       => "Thank you, your vote has been recorded.

If you wish, you may record the following details. Your voting record is:

<pre>$1</pre>

It has been encrypted with the public key of the Election Administrators:

<pre>$2</pre>

The resulting encrypted version follows. It will be displayed publicly on [[Special:Boardvote/dump]]. 

<pre>$3</pre>

[[Special:Boardvote/entry|Back]]",
	'boardvote_notloggedin'   => "You are not logged in. To vote, you must use an account
with at least $1 contributions before $2.",
	'boardvote_notqualified'  => "Sorry, you made only $1 edits before $2. You 
need at least $3 to be able to vote.",
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
	'boardvote_strike'        => "Strike",
	'boardvote_unstrike'      => "Unstrike",
	'boardvote_needadmin'     => "Only election administrators can perform this operation.",
	'boardvote_sitenotice'    => "<a href=\"{{localurle:Special:Boardvote/vote}}\">Wikimedia Board Elections</a>:  Vote open until July 12",
	'boardvote_closed'        => 'Voting is now closed, see [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2005/En the elections page for results] soon.',
	'boardvote_edits_many'    => 'many',
	'group-boardvote'         => 'Board vote admins',
	'group-boardvote-member'  => 'Board vote admin',
	'grouppage-boardvote'     => '{{ns:project}}:Board vote admin',
);
$wgBoardVoteMessages['he'] = array(
	'boardvote'               => "בחירות לחבר הנאמנים של ויקימדיה",
	'boardvote_entry'         => "<!--* [[{{ns:special}}:Boardvote/vote|הצבעה]]-->
* [[{{ns:special}}:Boardvote/list|רשימת ההצבעות נכון לעכשיו]]
* [[{{ns:special}}:Boardvote/dump|ההעתק המוצפן של הבחירות]]",
	'boardvote_intro'         => "
<p>ברוכים הבאים לבחירות השניות לחבר הנאמנים של קרן ויקימדיה. בהצבעה זו ייבחרו שני נציגים אשר ייצגו את הקהילה של משתמשי המיזמים השונים של ויקימדיה. הם יעזרו להחליט על כיוון התפתחותם העתידי של המיזמים השונים, כבודדים וכקבוצה, וייצגו את האינטרסים והדאגות <em>שלך</em> בחבר הנאמנים. הם יחליטו על הדרכים לבקשת תרומות ועל חלוקת המשאבים הכספיים.</p>

<p>אנא קראו בעיון, בטרם ההצבעה, את פרטי המועמדים ואת תשובותיהם לשאלות. כל אחד מן המועמדים והמועמדות הוא משתמש מוכר, אשר השקיע זמן רב ומאמץ להפוך את המיזמים הללו לסביבה נעימה המחויבת למטרת ההפצה חופשית של הידע האנושי.</p>

<p>באפשרותכם להצביע עבור מספר מועמדים. המועמדים עם מירב ההצבעות בכל עמדה יוכרזו כמנצחים בעמדה זו. במידה ויתקיים שיוויון בין מספר מועמדים, תתבצע הצבעה נוספת ביניהם.</p>

<p>למידע נוסף, ראו:</p>
<ul><li><a href=\"http://meta.wikipedia.org/wiki/Election_FAQ_2005\" class=\"external\">שאלות נפוצות על הבחירות</a></li>
<li><a href=\"http://meta.wikipedia.org/wiki/Election_Candidates_2005\" class=\"external\">המועמדים</a></li></ul>
",
	'boardvote_intro_change'  => "<p>כבר הצבעתם בעבר. עם זאת, באפשרותכם לשנות את הצבעתכם באמצעות הטופס המצורף למטה. אנא סמנו את תיבת הסימון ליד כל אחד מהמועמדים המועדפים עליכם.</p>",
	'boardvote_footer'        => "&nbsp;",
	'boardvote_entered'       => "תודה לכם, הצבעתכם נרשמה.

אם ברצונכם בכך, אתם יכולים לרשום את הפרטים הבאים. ההצבעה נרשמה כ:

<pre>$1</pre>

היא הוצפנה באמצעות המפתח הציבורי של ועדת הבחירות:

<pre>$2</pre>

תוצאות ההצפנה מופיעות בהמשך. הן גם תופענה בפומבי בקישור [[{{ns:special}}:Boardvote/entry]].

<pre>$3</pre>

[[{{ns:special}}:Boardvote/entry|חזרה]]",
	'boardvote_notloggedin'   => "אינכם רשומים לחשבון. כדי להצביע, עליכם להשתמש בחשבון שיש לו לפחות $1 תרומות לפני $2.",
	'boardvote_notqualified'  => "מצטערים, ביצעתם רק $1 עריכות לפני $2. אתם צריכים לפחות $3 תרומות לפני שתוכלו להצביע.",
	'boardvote_novotes'       => "איש לא הצביע עדיין.",
	'boardvote_time'          => "שעה",
	'boardvote_user'          => "משתמש",
	'boardvote_edits'         => "עריכות",
	'boardvote_days'          => "ימים",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "זיהוי הדפדפן",
	'boardvote_listintro'     => "<p>זוהי רשימה של כל ההצבעות שנרשמו עד כה. $1 כדי להגיע לנתונים המוצפנים.</p>",
	'boardvote_dumplink'      => "לחצו כאן",
	'boardvote_strike'        => "גילוי",
	'boardvote_unstrike'      => "הסתרה",
	'boardvote_needadmin'     => "רק מנהלי הבחירות יכולים לבצע פעולה זו.",
	'boardvote_sitenotice'    => "<a href=\"{{localurle:{{ns:special}}:Boardvote/vote}}\">בחירות לחבר הנאמנים של ויקימדיה</a>: ההצבעה פתוחה עד 12 ביולי",
	'boardvote_closed'        => 'ההצבעה סגורה כעת, ראו [http://meta.wikimedia.org/wiki/Elections_for_the_Board_of_Trustees_of_the_Wikimedia_Foundation%2C_2005/En את הדף על תוצאות הבחירות] בקרוב.',
	'boardvote_edits_many'    => 'הרבה',
	'group-boardvote'         => 'מנהלי הבחירות לחבר המנהלים',
	'group-boardvote-member'  => 'מנהל הבחירות לחבר המנהלים',
	'grouppage-boardvote'     => '{{ns:project}}:מנהל הבחירות לחבר המנהלים',
);
$wgBoardVoteMessages['id'] = array(
	'boardvote'               => "Pemilihan Anggota Dewan Kepercayaan Yayasan Wikimedia",
	'boardvote_intro_change'  => "<p>Anda sudah pernah memilih. Walaupun demikian, Anda dapat mengganti pilihan Anda dengan menggunakan formulir di bawah. Harap cek kotak di samping tiap kandidat yang Anda setujui.</p>",
	'boardvote_notloggedin'   => "Anda tidak masuk log. Untuk dapat memilih Anda harus menggunakan akun dengan paling tidak $1 suntingan sebelum $2.",
	'boardvote_notqualified'  => "Maaf, Anda hanya memiliki $1 suntingan sebelum $2. Anda paling tidak membutuhkan $3 suntingan sebelum dapat memilih.",
	'boardvote_novotes'       => "Belum ada pemilih.",
	'boardvote_time'          => "Waktu",
	'boardvote_user'          => "Pengguna",
	'boardvote_edits'         => "Suntingan",
	'boardvote_days'          => "Hari",
	'boardvote_ip'            => "IP",
	'boardvote_ua'            => "Agen pengguna",
	'boardvote_listintro'     => "<p>Berikut adalah daftar semua suara yang telah masuk sampai hari ini. $1 untuk data terenkripsi.</p>",
	'boardvote_dumplink'      => "Klik di sini",
	'boardvote_strike'        => "Coret",
	'boardvote_unstrike'      => "Hapus coretan",
	'boardvote_needadmin'     => "Hanya pengurus pemilihan yang dapat melakukan tindakan ini.",
	'boardvote_edits_many'    => 'banyak',
);
$wgBoardVoteMessages['nl'] = array(
	"boardvote"               => "Wikimedia Board of Trustees-verkiezing",
	"boardvote_entry"         => "<!--* [[Special:Boardvote/vote|Vote]]-->
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
<ul><li><a href=\"http://meta.wikipedia.org/wiki/Election_FAQ_2005\" class=\"external\">Bestuursverkiezing FAQ</a></li>
<li><a href=\"http://meta.wikipedia.org/wiki/Election_Candidates_2005\" class=\"external\">Kandidaten</a></li></ul>
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
?>
