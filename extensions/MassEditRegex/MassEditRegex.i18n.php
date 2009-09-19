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
See the PHP function preg_replace() for details.',
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
	'masseditregex-hint-removecat' => 'Remove all categories from an page (note the escaping of the square brackets in the wikicode.)
The replacement values should not be escaped.'
);

/** Message documentation (Message documentation)
 * @author Fryed-peach
 */
$messages['qqq'] = array(
	'masseditregex-desc' => '{{desc}}',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'masseditregex' => 'Масавае рэдагаваньне з выкарыстаньнем рэгулярных выразаў',
	'masseditregex-desc' => 'Карыстацца рэгулярным выразам для [[Special:MassEditRegex|рэдагаваньня шматлікіх старонак за адно дзеяньне]]',
	'masseditregextext' => 'Увядзіце адзін ці некалькі рэгулярных выразаў (адзін на радок) для пошуку супадзеньняў, і адзін ці некалькі выразаў для замены кожнага супадзеньня. Першы супаўшы выраз, калі ўсё правільнв, будзе заменены на першы выраз для замены і г.так далей. Глядзіце функцыю PHP preg_replace() для падрабязнасьцяў.',
	'masseditregex-pagelisttxt' => 'Старонкі для рэдагаваньня:',
	'masseditregex-matchtxt' => 'Пошук:',
	'masseditregex-replacetxt' => 'Замяніць на:',
	'masseditregex-executebtn' => 'Выканаць',
	'masseditregex-err-nopages' => 'Вам неабходна пазначыць хаця б адну старонку для замены.',
	'masseditregex-before' => 'Перад',
	'masseditregex-after' => 'Пасьля',
	'masseditregex-max-preview-diffs' => 'Папярэдні прагляд абмежаваны $1 {{PLURAL:$1|першым супадзеньнем|першымі супадзеньнямі|першымі супадзеньнямі}}.',
	'masseditregex-num-changes' => '$1: $2 {{PLURAL:$2|замена|замены|замены}}',
	'masseditregex-page-not-exists' => '$1 не існуе',
	'masseditregex-num-articles-changed' => '$1 {{PLURAL:$1|артыкул|артыкулы|артыкулы}} адрэдагаваны',
	'masseditregex-hint-headmatch' => 'Супадзеньне',
	'masseditregex-hint-headreplace' => 'Замена',
);

/** French (Français)
 * @author IAlex
 * @author Peter17
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'masseditregex' => "Modification en masse à l'aide des expressions rationnelles",
	'masseditregex-desc' => 'Utiliser les expressions rationnelles pour [[Special:MassEditRegex|modifier de nombreuses pages en une opération]]',
	'masseditregextext' => 'Entrer une ou plusieurs expressions rationnelles (une par ligne) à rechercher, et une ou plusieurs expressions par lesquelles remplacer les résultats. La première expression trouvée sera remplacée par la première expression de remplacement, et ainsi de suite. Voir la description de la fonction PHP preg_replace() pour plus de détails.',
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
	'masseditregextext' => 'Insira unha ou máis expresións regulares (un por liña) para facer coincidir, e unha ou máis expresións para substituír cada coincidencia. Se a primeira expresión coincidente é correcta, substituirase pola primeira expresión substituta, e así sucesivamente. Olle a función PHP preg_replace() para obter máis información.',
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

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'masseditregex' => '正規表現を使用した大量編集',
	'masseditregex-desc' => '正規表現を使って[[Special:MassEditRegex|大量のページを一度の操作で編集する]]',
	'masseditregextext' => '一致用に1つ以上の正規表現 (1行に1つ) と、置換用に1つ以上の正規表現を入力してください。1番目の一致用正規表現が一致に成功すると、1番目の置換用正規表現で置換されます。2番目以降も同様です。詳細は PHP 関数 preg_replace() を参照してください。',
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

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'masseditregex' => "Meerdere pagina's tegelijk bewerken met reguliere expressies",
	'masseditregex-desc' => "Reguliere expressies gebruiken om [[Special:MassEditRegex|meerdere pagina's in één handeling te bewerken]]",
	'masseditregextext' => 'Geef een of meer reguliere expressies op (één per regel) voor de selectie van tekst en een of meer reguliere expressies om de selectie door te vervangen.
De selectie uit de eerste selectie-expressie wordt vervangen door de eerste vervang-expressie, en zo verder.
Zie de PHP-functie preg_replace() voor details.',
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

