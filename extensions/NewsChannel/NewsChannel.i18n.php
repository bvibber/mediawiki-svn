<?php
/**
* News Channel extension 1.6
* This MediaWiki extension represents a RSS 2.0/Atom 1.0 news channel for wiki project.
* 	The channel is implemented as a dynamic [[Special:NewsChannel|special page]].
* 	All pages from specified category (e.g. "Category:News") are considered
* 	to be articles about news and published on the site's news channel.
* Internationalization file, containing message strings for extension.
* Requires MediaWiki 1.8 or higher.
* Extension's home page: http://www.mediawiki.org/wiki/Extension:News_Channel
*
* Distributed under GNU General Public License 2.0 or later (http://www.gnu.org/copyleft/gpl.html)
*/

$messages = array();

/** English
 * @author Iaroslav Vassiliev
 */
$messages['en'] = array(
	'newschannel' => 'News channel',
	'newschannel-desc' => 'Implements a news channel as a dynamic [[Special:NewsChannel|special page]]',
	'newschannel_format' => 'Format:',
	'newschannel_limit' => 'Limit:',
	'newschannel_include_category' => 'Additional category:',
	'newschannel_exclude_category' => 'Exclude category:',
	'newschannel_submit_button' => 'Create feed',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'newschannel' => 'قناة أخبار',
	'newschannel-desc' => 'يطبق قناة أخبار [[Special:NewsChannel|كصفحة خاصة]] ديناميكية',
	'newschannel_format' => 'الصيغة:',
	'newschannel_limit' => 'الحد:',
	'newschannel_include_category' => 'تصنيف إضافي:',
	'newschannel_exclude_category' => 'استبعد التصنيف:',
	'newschannel_submit_button' => 'إنشاء التلقيم',
);

/** German (Deutsch)
 * @author Cornelius Sicker
 */
$messages['de'] = array(
	'newschannel' => 'Nachrichten',
	'newschannel-desc' => 'Ergänzt einen Nachrichtenkanal als dynamische [[Special:NewsChannel|Spezialseite]]',
	'newschannel_format' => 'Format:',
	'newschannel_limit' => 'Limit:',
	'newschannel_include_category' => 'Zusätzliche Kategorie:',
	'newschannel_exclude_category' => 'Auszuschließende Kategorie:',
	'newschannel_submit_button' => 'Feed erstellen',
);

/** French (Français)
 * @author Grondin
 * @author Mauro Bornet
 */
$messages['fr'] = array(
	'newschannel' => "Chaîne d'information",
	'newschannel-desc' => 'Implémente un nouveau canal comme une [[Special:NewsChannel|page spéciale]] dynamique',
	'newschannel_format' => 'Format:',
	'newschannel_limit' => 'Limite:',
	'newschannel_include_category' => 'Catégorie(s) additionnelle(s):',
	'newschannel_exclude_category' => 'Catégorie(s) exclue(s):',
	'newschannel_submit_button' => 'Créer le flux',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'newschannel' => 'Nieuwskanaal',
	'newschannel-desc' => 'Voegt een nieuwskanaal toe als een dynamische [[Special:NewsChannel|speciale pagina]]',
	'newschannel_format' => 'Formaat:',
	'newschannel_limit' => 'Limiet:',
	'newschannel_include_category' => 'Additionele categorie:',
	'newschannel_exclude_category' => 'Uitgesloten categorie:',
	'newschannel_submit_button' => 'Feed aanmaken',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'newschannel' => 'Nyhtskanal',
	'newschannel-desc' => 'Implementerer en nyhetskanal som en dynamisk [[Special:NewsChannel|spesialside]]',
	'newschannel_format' => 'Format:',
	'newschannel_limit' => 'Grense:',
	'newschannel_include_category' => 'Ekstra kategori:',
	'newschannel_exclude_category' => 'Ekskluder kategori:',
	'newschannel_submit_button' => 'Opprett nyhetskilde',
);

/** Russian (Русский)
 * @author Iaroslav Vassiliev
 */
$messages['ru'] = array(
	'newschannel' => 'Канал новостей',
	'newschannel_format' => 'Формат новостей:',
	'newschannel_limit' => 'Кол-во последних новостей:',
	'newschannel_include_category' => 'Дополнительная категория:',
	'newschannel_exclude_category' => 'Исключить категорию:',
	'newschannel_submit_button' => 'Вывести',
);

