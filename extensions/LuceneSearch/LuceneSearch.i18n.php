<?php
/**
 * Internationalisation file for LuceneSearch extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgLuceneSearchMessages = array();

$wgLuceneSearchMessages['en'] = array(
	'searchnumber'          => "<strong>Results $1-$2 of $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Prev</span>",
	'searchnext'            => "<span style='font-size: small'>Next</span> &#x00BB;",
	'searchscore'           => "Relevance: $1",
	'searchsize'            => "$1KB ($2 words)",
	'searchdidyoumean'      => "Did you mean: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Sorry, there were no exact matches to your query.",
	'searchnearmatches'     => "<b>These pages have similar titles to your query:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Search in namespaces:\n
$1\n
Search for $3 $9",
	'lucenefallback'        => "There was a problem with the wiki search.
This is probably temporary; try again in a few moments,
or you can search the wiki through an external search service:\n"
);
$wgLuceneSearchMessages['de'] = array(
	'searchnumber'		=> "<strong>Ergebnisse $1–$2 von $3</strong>",
	'searchprev'            => "&larr; <span style='font-size: smaller'>Vorherige</span>",
	'searchnext'            => "<span style='font-size: smaller'>Nächste</span> &rarr;",
	'searchscore'           => "Relevanz: $1",
	'searchsize'            => "$1 kB ($2 Wörter)",
	'searchdidyoumean'      => "Meinten Sie „<a href=\"$1\">$2</a>“?",
	'searchnoresults'       => "Es wurden keine passenden Seiten für Ihre Suchanfrage gefunden.",
	'searchnearmatches'     => "<b>Diese Seiten haben zu der Suchanfrage ähnliche Titel:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
Suche in Namensräumen:\n
$1\n
Suche nach $3 $9",
	'lucenefallback'        => "Bei der {{SITENAME}}-internen Suche ist ein Problem aufgetreten.
Dies ist normalerweise ein vorübergehendes Problem. Bitte versuchen Sie es nochmal.
Alternativ können Sie auch die externen Suchmöglichkeiten nutzen: :\n"
);	
$wgLuceneSearchMessages['he'] = array(
	'searchnumber'          => "<strong>תוצאות $1-$2 מתוך $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Prev</span>",
	'searchnext'            => "<span style='font-size: small'>הבא</span> &#x00BB;",
	'searchscore'           => "קשר: $1",
	'searchsize'            => "$1 קילובייטים ($2 מילים)",
	'searchdidyoumean'      => "האם התכוונתם ל: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "מצטערים, אין דפים עם הכותרת המדויקת שחיפשת.",
	'searchnearmatches'     => "<b>דפים אלו הם בעלי כותרת דומה למבוקשת:</b>\n",
	'searchnearmatch'       => "<li>$1</li>\n",
	'lucenepowersearchtext' => "
חיפוש במרחבי השם:\n
$1\n
חיפוש של $3 $9",
	'lucenefallback'        => "יש בעייה עם מנוע הוויקי.
סביר להניח שהיא זמנית; אנא נסו שנית בעוד מספר דקות.
באפשרותכם גם לחפש בוויקי באמצעות שירותי חיפוש חיצוניים:\n"
);
$wgLuceneSearchMessages['id'] = array(
	'searchnumber'          => "<strong>Hasil pencarian $1-$2 dari $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Sebelumnya</span>",
	'searchnext'            => "<span style='font-size: small'>Selanjutnya</span> &#x00BB;",
	'searchscore'           => "Relevansi: $1",
	'searchsize'            => "$1KB ($2 kata)",
	'searchdidyoumean'      => "Apakah maksud Anda: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Maaf, tidak ditemukan hasil yang tepat sama dengan permintaan Anda.",
	'searchnearmatches'     => "<b>Halaman-halaman berikut mempunyai judul yang mirip dengan permintaan Anda:</b>\n",
	'lucenepowersearchtext' => "
Pencarian di namespace:\n
$1\n
Pencarian terhadap $3 $9",
	'lucenefallback'        => "Ada masalah pada pencarian wiki.
Masalah ini mungkin hanya sementara; silakan coba lagi dalam beberapa saat,
atau gunakan layanan pencari eksternal:\n"
);
$wgLuceneSearchMessages['nl'] = array(
	'searchnumber'          => "<strong>Resultaten $1-$2 van de $3</strong>",
	'searchprev'            => "&#x00AB; <span style='font-size: small'>Vorige</span>",
	'searchnext'            => "<span style='font-size: small'>Volgende</span> &#x00BB;",
	'searchscore'           => "Relevantie: $1",
	'searchsize'            => "$1KB ($2 woorden)",
	'searchdidyoumean'      => "Bedoelde u: \"<a href=\"$1\">$2</a>\"?",
	'searchnoresults'       => "Sorry, uw zoekopdracht heeft geen resultaten opgeleverd.",
	'searchnearmatches'     => "<b>Deze paginanamen komen overeen met uw zoekopdracht:</b>\n",
	'lucenepowersearchtext' => "
Zoek in de volgende naamruimten:\n
$1\n
Zoek naar $3 $9",
	'lucenefallback'        => "Er is een storing in de wikizoekmachine.
Deze is waarschijnlijk tijdelijk van aard; probeer het over enige tijd opnieuw
of doorzoek de wiki via een externe zoekmachine:\n"
);
?>
