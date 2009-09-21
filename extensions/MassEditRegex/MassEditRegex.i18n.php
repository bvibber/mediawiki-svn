<?php
/**
 * Internationalisation file for MassEditRegex extension
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Adam Nielsen
 */
$messages['en'] = array(
	'masseditregex' => 'Mass edit using regular expressions',
	'masseditregex-desc' => 'Use regular expressions to [[Special:MassEditRegex|edit many pages in one operation]]',
	'masseditregextext' => 'Enter one or more regular expressions (one per line) for matching, and one or more expressions to replace each match with.
The first match-expression, if successful, will be replaced with the first replace-expression, and so on.
See [http://php.net/manual/en/function.preg-replace.php the PHP function preg_replace()] for details.',
	'masseditregex-pagelisttxt' => 'Pages to edit:',
	'masseditregex-matchtxt' => 'Search for:',
	'masseditregex-replacetxt' => 'Replace with:',
	'masseditregex-executebtn' => 'Execute',
	'masseditregex-err-nopages' => 'You must specify at least one page to change.',

	'masseditregex-before' => 'Before',
	'masseditregex-after' => 'After',
	'masseditregex-max-preview-diffs' => 'Preview has been limited to the first $1 {{PLURAL:$1|match|matches}}.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|change|changes}}',
	'masseditregex-page-not-exists' => '$1 does not exist',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|page|pages}} edited',
	'masseditregex-view-full-summary' => 'View full edit summary',

	'masseditregex-hint-intro' => 'Here are some hints and examples for accomplishing common tasks:',
	'masseditregex-hint-headmatch' => 'Match',
	'masseditregex-hint-headreplace' => 'Replace',
	'masseditregex-hint-headeffect' => 'Effect',
	'masseditregex-hint-toappend' => 'Append some text to the end of the page - great for adding pages to categories',
	'masseditregex-hint-remove' => 'Remove some text from all the pages in the list',
	'masseditregex-hint-removecat' => 'Remove all categories from a page (note the escaping of the square brackets in the wikicode.)
The replacement values should not be escaped.'
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 * @author Purodha
 */
$messages['qqq'] = array(
	'masseditregex-desc' => '{{desc}}',
	'masseditregextext' => 'Replace <code>/en/</code> in the middle in link <code>http://php.net/manual/en/function.preg-replace.php</code> with your language code between slashes, if that page exists. Otherwise leave it as is to link to the English documentation, or choose an appropriate fallback language code.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'masseditregex' => 'Wysig baie bladsye met behulp van gereelde uitdrukkings',
	'masseditregex-pagelisttxt' => 'Bladsye om te wysig:',
	'masseditregex-matchtxt' => 'Soek vir:',
	'masseditregex-replacetxt' => 'Vervang met:',
	'masseditregex-executebtn' => 'Uitvoer',
	'masseditregex-err-nopages' => 'U moet ten minste een bladsy spesifiseer om te verander.',
	'masseditregex-before' => 'Voor',
	'masseditregex-after' => 'Na',
	'masseditregex-max-preview-diffs' => 'Voorskou is beperk tot die eerste $1 {{PLURAL:$1|ooreenkoms|ooreenkomste}}.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|wysiging|wysigings}}',
	'masseditregex-page-not-exists' => '$1 bestaan nie',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|bladsy|bladsye}} gewysig',
	'masseditregex-view-full-summary' => 'Sien samevatting van wysigings',
	'masseditregex-hint-intro' => "Hier is 'n paar wenke en voorbeelde vir die uitvoer van algemene take:",
	'masseditregex-hint-headmatch' => 'Seleksie',
	'masseditregex-hint-headreplace' => 'Vervang',
	'masseditregex-hint-headeffect' => 'Effek',
	'masseditregex-hint-remove' => 'Verwyder teks uit al die bladsye in die lys',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'masseditregex' => 'Масавае рэдагаваньне з выкарыстаньнем рэгулярных выразаў',
	'masseditregex-desc' => 'Выкарыстаньне рэгулярных выразаў для [[Special:MassEditRegex|рэдагаваньня некалькіх старонак за адно дзеяньне]]',
	'masseditregextext' => 'Увядзіце адзін альбо некалькі рэгулярных выразаў (адзін на радок) для пошуку супадзеньняў, і адзін альбо некалькі выразаў для замены кожнага супадзеньня.
Першы пасьпяховае супадзеньне з выразам будзе замененае на першы выраз для замены і гэтак далей. Глядзіце падрабязнасьці пра функцыю [http://php.net/manual/en/function.preg-replace.php PHP preg_replace()].',
	'masseditregex-pagelisttxt' => 'Старонкі для рэдагаваньня:',
	'masseditregex-matchtxt' => 'Пошук:',
	'masseditregex-replacetxt' => 'Замяніць на:',
	'masseditregex-executebtn' => 'Выканаць',
	'masseditregex-err-nopages' => 'Вам неабходна пазначыць хаця б адну старонку для рэдагаваньня.',
	'masseditregex-before' => 'Перад',
	'masseditregex-after' => 'Пасьля',
	'masseditregex-max-preview-diffs' => 'Папярэдні прагляд абмежаваны $1 {{PLURAL:$1|першым супадзеньнем|першымі супадзеньнямі|першымі супадзеньнямі}}.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|зьмена|зьмены|зьменаў}}',
	'masseditregex-page-not-exists' => '$1 не існуе',
	'masseditregex-num-articles-changed' => '{{PLURAL:$1|адрэдагаваная|адрэдагаваныя|адрэдагаваныя}} $1 {{PLURAL:$1|старонка|старонкі|старонак}}',
	'masseditregex-view-full-summary' => 'Паказаць поўнае апісаньне зьменаў',
	'masseditregex-hint-intro' => 'Тут пададзеныя некалькі падказак і прыкладаў для выкананьня агульных заданьняў:',
	'masseditregex-hint-headmatch' => 'Супадзеньне',
	'masseditregex-hint-headreplace' => 'Замена',
	'masseditregex-hint-headeffect' => 'Вынік',
	'masseditregex-hint-toappend' => 'Далучыць нейкі тэкст да канца старонкі —- выдатна пасуе для даданьня катэгорыяў у старонкі',
	'masseditregex-hint-remove' => 'Выдаліць некаторы тэкст з усіх старонак у сьпісе',
	'masseditregex-hint-removecat' => 'Выдаліць усе катэгорыі са старонкі (заўважце, што выдаляюцца толькі квадратныя дужкі з вікі-коду.)
Значэньні да замяшчэньня не павінны быць уключаныя ў двукосьсі альбо апострафы.',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'masseditregex' => 'Edición en masa usando expresiones regulares',
	'masseditregex-pagelisttxt' => 'Páginas a editar:',
	'masseditregex-replacetxt' => 'Reemplazar con:',
	'masseditregex-executebtn' => 'Ejecutar',
	'masseditregex-err-nopages' => 'Debes especificar al menos una página a cambiar.',
	'masseditregex-before' => 'Antes',
	'masseditregex-after' => 'Después',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|cambio|cambios}}',
	'masseditregex-page-not-exists' => '$1 no existe',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|página|páginas}} editadas',
	'masseditregex-view-full-summary' => 'Ver resumen de edición completo',
	'masseditregex-hint-headreplace' => 'Reemplazar',
);

/** French (Français)
 * @author IAlex
 * @author Peter17
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'masseditregex' => "Modification en masse à l'aide des expressions rationnelles",
	'masseditregex-desc' => 'Utiliser les expressions rationnelles pour [[Special:MassEditRegex|modifier de nombreuses pages en une opération]]',
	'masseditregextext' => 'Entrer une ou plusieurs expressions rationnelles (une par ligne) à rechercher, et une ou plusieurs expressions par lesquelles remplacer les résultats. La première expression trouvée sera remplacée par la première expression de remplacement, et ainsi de suite. Voir la description de la [http://php.net/manual/en/function.preg-replace.php fonction PHP preg_replace()] pour plus de détails.',
	'masseditregex-pagelisttxt' => 'Pages à modifier :',
	'masseditregex-matchtxt' => 'Rechercher :',
	'masseditregex-replacetxt' => 'Remplacer par :',
	'masseditregex-executebtn' => 'Exécuter',
	'masseditregex-err-nopages' => 'Vous devez spécifier au moins une page à modifier.',
	'masseditregex-before' => 'Avant',
	'masseditregex-after' => 'Après',
	'masseditregex-max-preview-diffs' => 'La prévisualisation a été limitée {{PLURAL:$1|au premier résultat|aux $1 premiers résultats}}.',
	'masseditregex-num-changes' => '$1 : $2 {{PLURAL:$2|modification|modifications}}',
	'masseditregex-page-not-exists' => "$1 n'existe pas",
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|article modifié|articles modifiés}}',
	'masseditregex-view-full-summary' => 'Voir le résumé complet des modifications',
	'masseditregex-hint-intro' => 'Voici quelques indications et exemples pour réaliser les tâches usuelles :',
	'masseditregex-hint-headmatch' => 'Correspondance',
	'masseditregex-hint-headreplace' => 'Remplacer',
	'masseditregex-hint-headeffect' => 'Effet',
	'masseditregex-hint-toappend' => "Insère du texte à la fin de l'article - pratique pour ajouter les pages à des catégories",
	'masseditregex-hint-remove' => 'Retirer du texte de toutes les pages de la liste',
	'masseditregex-hint-removecat' => "Supprime toutes les catégories de l'article (notez que les crochets dans le wikicode sont échappés.) Les valeurs de remplacement ne doivent pas être échappées.",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'masseditregex' => 'Edición en masa usando expresións regulares',
	'masseditregex-desc' => 'Usa expresións regulares para [[Special:MassEditRegex|editar moitas páxinas nunha única operación]]',
	'masseditregextext' => 'Insira unha ou máis expresións regulares (un por liña) para facer coincidir, e unha ou máis expresións para substituír cada coincidencia. Se a primeira expresión coincidente é correcta, substituirase pola primeira expresión substituta, e así sucesivamente. Olle a [http://php.net/manual/en/function.preg-replace.php función PHP preg_replace()] para obter máis información.',
	'masseditregex-pagelisttxt' => 'Páxinas a editar:',
	'masseditregex-matchtxt' => 'Procurar por:',
	'masseditregex-replacetxt' => 'Substituír por:',
	'masseditregex-executebtn' => 'Executar',
	'masseditregex-err-nopages' => 'Debe especificar, polo menos, unha páxina a modificar.',
	'masseditregex-before' => 'Antes',
	'masseditregex-after' => 'Despois',
	'masseditregex-max-preview-diffs' => 'A vista previa limitouse {{PLURAL:$1|á primeira coincidencia|ás $1 primeiras coincidencias}}.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|cambio|cambios}}',
	'masseditregex-page-not-exists' => '"$1" non existe',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|artigo editado|artigos editados}}',
	'masseditregex-view-full-summary' => 'Ollar o resumo de edición ao completo',
	'masseditregex-hint-intro' => 'Aquí hai algúns consellos e exemplos para a realización de tarefas comúns:',
	'masseditregex-hint-headmatch' => 'Buscar as coincidencias',
	'masseditregex-hint-headreplace' => 'Substituír',
	'masseditregex-hint-headeffect' => 'Levar a cabo',
	'masseditregex-hint-toappend' => 'Engade algo de texto ao final do artigo; útil para engadir páxinas a categorías',
	'masseditregex-hint-remove' => 'Elimina algún texto de todas as páxinas da lista',
	'masseditregex-hint-removecat' => 'Elimina todas as categorías dun artigo (teña en conta o escape dos corchetes no formato wiki). Os valores de substitución non deberían escapar.',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'masseditregex' => 'Greßeri Aazahl vu Syte glychzytig ändere iber reguläri Uusdruck.',
	'masseditregex-desc' => 'Reguläri Uusdruck bruch go [[Special:MassEditRegex|vili Syte in eim Schritt bearbeite]]',
	'masseditregextext' => 'Gib ein oder meh reguläri Uusdruck as Suechuusdruck yy (eine pro Zyylete) un ein oder meh Uusdruck as Ersatzuusdruck.
Dr erscht Suechuusdruck wird, wänn er gfunde wore isch, dur dr erscht Ersatzuusdruck ersetzt usw.
Lueg d [http://php.net/manual/en/function.preg-replace.php PHP-Funktion preg_replace()] fir Details.',
	'masseditregex-pagelisttxt' => 'Syte, wu bearbeitet solle wäre:',
	'masseditregex-matchtxt' => 'Suech no:',
	'masseditregex-replacetxt' => 'Ersetze dur:',
	'masseditregex-executebtn' => 'Uusfiere',
	'masseditregex-err-nopages' => 'Du muesch zmindescht ei Syte aagee, wu gänderet soll wäre.',
	'masseditregex-before' => 'Vorhär',
	'masseditregex-after' => 'Nocher',
	'masseditregex-max-preview-diffs' => 'D Vorschau isch uf {{PLURAL:$1|uf dr erscht Träffer|di erschte $1 Träffer}} yygschränkt wore',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|Aänderig|Anderige}}',
	'masseditregex-page-not-exists' => '$1 git s nit',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|Syte|Syte}} bearbeitet',
	'masseditregex-view-full-summary' => 'Di ganz Zämmefassig aaluege',
	'masseditregex-hint-intro' => 'Do het s e paar Hiwys un Byschpil, wie mer gängigi Ufgabe cha uusfiere:',
	'masseditregex-hint-headmatch' => 'Träffer',
	'masseditregex-hint-headreplace' => 'Ersetze',
	'masseditregex-hint-headeffect' => 'Effäkt',
	'masseditregex-hint-toappend' => 'Täxt am Änd vu dr Syte yyfiege - ideal go Syte ere Kategorie zuefiege',
	'masseditregex-hint-remove' => 'Text us allene Syte in dr Lischt uuseneh',
	'masseditregex-hint-removecat' => 'Alli Kategorie us ere Syte uuseneh (gib Acht uf d Richtig vu dr eckige Chlammere im Wikicode).
Bim Text, wu yygsetzt wird, isch d Richtig nit relevant.',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'masseditregex' => 'Masowe wobdźěłanje z pomocu regularnych wurazow',
	'masseditregex-desc' => 'Regularne wurazy za [[Special:MassEditRegex|wobdźěłowanje wjele stronow w jednej operaciji]] wužiwać',
	'masseditregex-pagelisttxt' => 'Strony, kotrež maja so wobdźěłać:',
	'masseditregex-matchtxt' => 'Pytać za:',
	'masseditregex-replacetxt' => 'Narunać přez:',
	'masseditregex-executebtn' => 'Wuwjesć',
	'masseditregex-err-nopages' => 'Dyrbiš znajmjeńša jednu stronu podać, kotraž ma so změnić.',
	'masseditregex-before' => 'Prjedy',
	'masseditregex-after' => 'Po tym',
	'masseditregex-max-preview-diffs' => 'Přehlad je na {{PLURAL:$1|prěnju wotpowědnik|prěnjej $1 wotpowědnikaj|prěnje $1 wotpowědniki|prěnich $1 wotpowědnikow}} wobmjezowany.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|změna|změnje|změny|změnow}}',
	'masseditregex-page-not-exists' => '$1 njeeksistuje',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|nastawk wobdźěłany|nastawkaj wobdźěłanej|nastawki wobdźěłane|nastawkow wobdźěłanych}}',
	'masseditregex-view-full-summary' => 'Dospołne wobdźěłowanske zjeće pokazać',
	'masseditregex-hint-headmatch' => 'Wotpowědnik',
	'masseditregex-hint-headreplace' => 'Narunać',
	'masseditregex-hint-headeffect' => 'Efekt',
	'masseditregex-hint-toappend' => 'Tekst ke kóncej nastawka připowěsnyć - jara dobre za přidawanje stronow kategorijam',
	'masseditregex-hint-remove' => 'Tekst ze wšěch stronow w lisćinje wotstronić',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'masseditregex' => 'Tömeges szerkesztés reguláris kifejezések használatával',
	'masseditregex-pagelisttxt' => 'Szerkesztendő lapok:',
	'masseditregex-replacetxt' => 'Csere erre:',
	'masseditregex-executebtn' => 'Végrehajtás',
	'masseditregex-before' => 'Előtte',
	'masseditregex-after' => 'Utána',
	'masseditregex-hint-headreplace' => 'Csere',
	'masseditregex-hint-headeffect' => 'Hatás',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'masseditregex' => '正規表現を使用した大量編集',
	'masseditregex-desc' => '正規表現を使って[[Special:MassEditRegex|大量のページを一度の操作で編集する]]',
	'masseditregextext' => '一致用に1つ以上の正規表現（1行に1つ）と、置換用に1つ以上の正規表現を入力してください。1番目の一致用正規表現が一致に成功すると、1番目の置換用正規表現で置換されます。2番目以降も同様です。詳細は [http://php.net/manual/ja/function.preg-replace.php PHP 関数 preg_replace()] を参照してください。',
	'masseditregex-pagelisttxt' => '編集するページ:',
	'masseditregex-matchtxt' => '一致用:',
	'masseditregex-replacetxt' => '置換用:',
	'masseditregex-executebtn' => '実行',
	'masseditregex-err-nopages' => '少なくとも1つ、編集するページを指定しなければなりません。',
	'masseditregex-before' => '前',
	'masseditregex-after' => '後',
	'masseditregex-max-preview-diffs' => 'プレビューは最初の$1件の一致に限定されています。',
	'masseditregex-num-changes' => '$1: $2件の変更',
	'masseditregex-page-not-exists' => '$1 は存在しません',
	'masseditregex-num-articles-changed' => '$1個のページが編集されました',
	'masseditregex-view-full-summary' => '完全な編集要約を表示',
	'masseditregex-hint-intro' => 'よくある課題を達成するためのヒントと使用例を示します:',
	'masseditregex-hint-headmatch' => '一致用',
	'masseditregex-hint-headreplace' => '置換用',
	'masseditregex-hint-headeffect' => '効果',
	'masseditregex-hint-toappend' => '記事末尾に文章を追記する。ページにカテゴリーを加えるにの便利です',
	'masseditregex-hint-remove' => '指定したすべてのページからある文章を除去する',
	'masseditregex-hint-removecat' => '記事からすべてのカテゴリーを除去する (ウィキテキスト中の角括弧のエスケープに注意)。置換する値はエスケープしません。',
);

/** Ripoarisch (Ripoarisch)
 * @author Als-Holder
 * @author Purodha
 */
$messages['ksh'] = array(
	'masseditregex' => 'Donn Sigge em Pöngel ändere, un dat övver <code>regular expressions</code>.',
	'masseditregex-desc' => 'Deiht Sigge [[Special:MassEditRegex|em Pöngel en einem Rötsch ändere]], un dat övver <code>ergular expressions</code>.',
	'masseditregextext' => 'Jif ein <i lang="en">regular expression</i> pro Reih en, esu vill wi nüüdesch. Dohenger en desellve Reih schriif hen, woh jäähje dat jetuusch wääde sull, wat op di <i lang="en">regular expression</i> paß. De [http://de.php.net/manual/de/function.preg-replace.php Funxjuhn <code lang="en">preg_replace()</code> en dä Projammeer_Shprooch <i lang="en">PHP</i>] weed doför jebruch, un doh kam_mer och de Einzelheite janz jenou nohlässe.',
	'masseditregex-pagelisttxt' => 'De Sigge zom Ändere:',
	'masseditregex-matchtxt' => 'Söhk noh:',
	'masseditregex-replacetxt' => 'Tuusch dat jäje:',
	'masseditregex-executebtn' => 'Lohß Jonn!',
	'masseditregex-err-nopages' => 'Winneschßdens ein Sigg för zem Ändere moß de ald aanjävve:',
	'masseditregex-before' => 'Förher',
	'masseditregex-after' => 'Hengerher',
	'masseditregex-max-preview-diffs' => 'De Vör-Aanseesch es op  {{PLURAL:$1|dä eetste Träffer|de eetste $1 Träffer|nix}} bejränz.',
	'masseditregex-num-changes' => '$1: {{PLURAL:$2|ein Änderung|$2 Änderunge|kein Änderung}}',
	'masseditregex-page-not-exists' => '$1 jidd_et nit',
	'masseditregex-num-articles-changed' => '{{PLURAL:$1|Ein Sigg|$1 Sigge|Kein Sigg wood}} jeändert',
	'masseditregex-view-full-summary' => 'De kumplätte Zosammefassung udder Quäll aanloore',
	'masseditregex-hint-intro' => 'Hee sen e paa Henwieß un Beispöll, wi mer üblesche Aufjaabe jedonn kritt:',
	'masseditregex-hint-headmatch' => 'Träffer',
	'masseditregex-hint-headreplace' => 'Ußtuusche',
	'masseditregex-hint-headeffect' => 'Wat eruß kütt',
	'masseditregex-hint-toappend' => 'Donn e Täx-Shtöck aam Engk vun dä Sigg aanhange — wunderbaa för Sigge en Saachjroppe ze donn',
	'masseditregex-hint-remove' => 'Donn e Shtöck vum Täx vun alle Sigge en dä Leß fott nämme',
	'masseditregex-hint-removecat' => 'Donn alle Enndrääsch för Saachjropp us en Sigg eruß schmiiße — jev Aach op et Escaping vun de äkijje Klammere em Wiki_Kood. Wat beim Tuusche för dä ahle täx ennjesaz weed, darf mer ävver nit escape.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'masseditregex-pagelisttxt' => "Säite fir z'änneren:",
	'masseditregex-matchtxt' => 'Sichen no:',
	'masseditregex-replacetxt' => 'Eretzen duerch:',
	'masseditregex-executebtn' => 'Ausféieren',
	'masseditregex-err-nopages' => "Dir musst mindestens eng Säit ugi fir z'änneren.",
	'masseditregex-before' => 'Virdrun',
	'masseditregex-after' => 'Duerno',
	'masseditregex-num-changes' => '$1 : $2 {{PLURAL:$2|Ännerung|Ännerungen}}',
	'masseditregex-page-not-exists' => '$1 gëtt et net',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|Säit|Säite}} geännert',
	'masseditregex-hint-headreplace' => 'Ersetzen',
	'masseditregex-hint-toappend' => "Text un d'Ënn vun der Säit bäisetzen - gutt fir Säiten a Kategorien derbäizesetzen",
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'masseditregex' => "Meerdere pagina's tegelijk bewerken met reguliere expressies",
	'masseditregex-desc' => "Reguliere expressies gebruiken om [[Special:MassEditRegex|meerdere pagina's in één handeling te bewerken]]",
	'masseditregextext' => 'Geef een of meer reguliere expressies op (één per regel) voor de selectie van tekst en een of meer reguliere expressies om de selectie door te vervangen.
De selectie uit de eerste selectie-expressie wordt vervangen door de eerste vervang-expressie, en zo verder.
Zie de [http://php.net/manual/en/function.preg-replace.php PHP-functie preg_replace()] voor details.',
	'masseditregex-pagelisttxt' => "Te bewerken pagina's:",
	'masseditregex-matchtxt' => 'Zoeken naar:',
	'masseditregex-replacetxt' => 'Vervangen door:',
	'masseditregex-executebtn' => 'Uitvoeren',
	'masseditregex-err-nopages' => 'Geef tenminste één te wijzigen pagina op.',
	'masseditregex-before' => 'Voor',
	'masseditregex-after' => 'Na',
	'masseditregex-max-preview-diffs' => 'Alleen de eerste {{PLURAL:$1|te maken wijziging|$1 te maken wijzigingen}} worden weergegeven.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|wijziging|wijzigingen}}',
	'masseditregex-page-not-exists' => '$1 bestaat niet',
	'masseditregex-num-articles-changed' => "$1 {{PLURAL:$1|pagina|pagina's}} bewerkt",
	'masseditregex-view-full-summary' => 'Bewerkingssamenvatting bekijken',
	'masseditregex-hint-intro' => 'Hints en voorbeelden voor het uitvoeren van veel voorkomende taken:',
	'masseditregex-hint-headmatch' => 'Selectie',
	'masseditregex-hint-headreplace' => 'Vervangen',
	'masseditregex-hint-headeffect' => 'Effect',
	'masseditregex-hint-toappend' => "Voeg tekst toe aan het einde van de pagina.
Ideaal voor het toevoegen van pagina's aan een categorie.",
	'masseditregex-hint-remove' => "Tekst verwijderen van alle pagina's in de lijst",
	'masseditregex-hint-removecat' => 'Alle categorieën verwijderen van een pagina.
Let op het escapen van de blokhaken in de wikitekst.
Voor de te vervangen tekst is escapen niet nodig.',
);

/** Veps (Vepsan kel')
 * @author Игорь Бродский
 */
$messages['vep'] = array(
	'masseditregex-matchtxt' => 'Ectä:',
	'masseditregex-executebtn' => 'Tehta',
	'masseditregex-before' => 'Edel',
	'masseditregex-after' => "Jäl'ges",
	'masseditregex-hint-headeffect' => 'Effekt',
);

