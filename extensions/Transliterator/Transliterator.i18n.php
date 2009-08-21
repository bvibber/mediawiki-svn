<?php
/**
 * Internationalization file for Transliterator
 */

require_once( dirname(__FILE__) . '/Transliterator.i18n.magic.php' );

$messages = array();

/**
 * English
 * @author: Conrad.Irwin
 * @author: Purodha
 */
$messages['en'] = array(
	'transliterator-desc' => "Provides a configurable parser function for transliteration",
	'transliterator-prefix' => 'Transliterator:', // [[MediaWiki:Transliterator:blah]] NOTE: changing this requires moving all maps
	// $1 is the line from the map, 'a => z', $2 is the map-page including prefix.
	'transliterator-error-ambiguous' => "Ambiguous rule <code>$1</code> in [[MediaWiki:$2]]",
	'transliterator-error-syntax' => "Invalid syntax <code>$1</code> in [[MediaWiki:$2]]",
	// $1 is the limit on number of rules
	'transliterator-error-rulecount' => "More than $1 {{PLURAL:$1|rule|rules}} in [[MediaWiki:$2]]",
	// $3 is the limit on the length of the left hand side (e.g. 'alpha => beta' has 5)
	'transliterator-error-rulesize' => "Rule <code>$1</code> has more than $3 {{PLURAL:$3|character|characters}} on the left in [[MediaWiki:$2]]",
);

/** Message documentation (Message documentation)
 * @author Purodha
 * @author Fryed-peach
 */
$messages['qqq'] = array(
	'transliterator-desc' => 'This is a short description of the extension. It is shown in [[Special:Version]].',
	'transliterator-prefix' => "{{optional}}
This is a prefix for the transliteration maps, used in the MediaWiki namespace like [<nowiki />[MediaWiki:Transliterator:''blah'']]. Changing this requires moving all maps.",
	'transliterator-error-ambiguous' => 'Parameters:
* $1 is the line from the map, such as: <code>a => z</code>
* $2 is the map-page including the prefix {{msg-mw|transliterator-invoke}}',
	'transliterator-error-syntax' => 'Parameters:
* $1 is the line from the map, such as: <code>a => z</code>
* $2 is the map-page including the prefix {{msg-mw|transliterator-invoke}}',
	'transliterator-error-rulecount' => 'Parameters:
* $1 is the limit on number of rules
* $2 is the map-page including the prefix {{msg-mw|transliterator-invoke}}',
	'transliterator-error-rulesize' => 'Parameters:
* $1 is the line from the map, such as: <code>a => z</code>
* $2 is the map-page including the prefix {{msg-mw|transliterator-invoke}}
* $3 is the limit on the length of the left hand side (e.g. <code>alpha => beta</code> has 5)',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'transliterator-desc' => 'Дадае функцыю парсэра для трансьлітарацыі, якую магчыма канфігураваць',
	'transliterator-error-ambiguous' => 'Неадназначнае правіла <code>$1</code> у [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Няслушны сынтаксіс <code>$1</code> у [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Больш за $1 {{PLURAL:$1|правіла|правілы|правілаў}} у [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'Правіла <code>$1</code> мае больш за $3 {{PLURAL:$3|сымбаль у|сымбалі ў|сымбаляў у}} у левай частцы ў [[MediaWiki:$2]]',
);

/** German (Deutsch)
 * @author Als-Holder
 */
$messages['de'] = array(
	'transliterator-desc' => 'Stellt eine konfigurierbare Parserfunktion zur Transliteration bereit.',
	'transliterator-error-ambiguous' => 'Mehrdeutige Regel <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Fehlerhafte Syntax in Regel <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Mehr als die {{PLURAL:$1|erlaubte eine Regel|die erlaubten $1 Regeln}} in [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'In der Regel <code>$1</code> {{PLURAL:$3|ist|sind}} mehr als $3 Zeichen auf der linken Seite in [[MediaWiki:$2]]',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'transliterator-desc' => 'Staja konfigurěrujobnu parserowu funkciju za transliteraciju k dispoziciji',
	'transliterator-error-ambiguous' => 'Dwójozmysłowe pšawidło <code>$1</code> w [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Njepłaśiwa syntaksa <code>$1</code> w [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Wěcej ako $1 {{PLURAL:$1|pšawidło|pšawidle|pšawidła|pšawidłow}} w [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'Pšawidło <code>$1</code> ma wěcej ako $3 {{PLURAL:$3|znamuško|znamušce|znamuška|znamuškow}} nalěwo w [[MediaWiki:$2]]',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'transliterator-desc' => 'Provee una función analizadora configurable para transliteración',
	'transliterator-error-ambiguous' => 'Regla ambigua <code>$1</code> en [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Sintaxis inválido <code>$1</code> en [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Más de $1 {{PLURAL:$1|regla|reglas}} en [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'Regla <code>$1</code> tiene más de $3 {{PLURAL:$3|caracter|caracteres}} en la izquierda en [[MediaWiki:$2]]',
);

/** French (Français)
 * @author Crochet.david
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'transliterator-desc' => 'Fournit une fonction parseur configurable pour la translittération',
	'transliterator-error-ambiguous' => 'Règle ambiguë <code>$1</code> dans [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Syntaxe incorrecte <code>$1</code> dans [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Plus de $1 {{PLURAL:$1|règle|règles}} dans [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'La règle <code>$1</code> a plus de $3 {{PLURAL:$3|caractère|caractères}} sur la gauche dans [[MediaWiki:$2]]',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'transliterator-desc' => 'Stellt e konfigurierbari Parserfunktion z Verfiegig fir s Transliteration.',
	'transliterator-error-ambiguous' => 'Mehdytigi Regle <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Fählerhafti Syntax in Regle <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Meh wie di {{PLURAL:$1|ei erlaubt Regle|erlaubte $1 Regle}} in [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'In dr Regle <code>$1</code> {{PLURAL:$3|isch|sin}} meh wie $3 Zeiche uf dr linke Syte in [[MediaWiki:$2]]',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'transliterator-error-rulecount' => 'יש יותר מ{{PLURAL:$1|כלל אחד|־$1 כללים}} בדף [[MediaWiki:$2]]',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'transliterator-desc' => 'Staja konfigurujomnu parserowu funkciju za transliteraciju k dispoziciji',
	'transliterator-error-ambiguous' => 'Dwuzmyslne prawidło <code>$1</code> w [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Njepłaćiwa syntaksa <code>$1</code> w [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Wjace hač $1 {{PLURAL:$1|prawidło|prawidle|prawidła|prawidłow}} w [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'Prawidło <code>$1</code> ma wjace hač $3 {{PLURAL:$3|znamješko|znamješce|znamješka|znamješkow}} nalěwo w [[MediaWiki:$2]]',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 */
$messages['id'] = array(
	'transliterator-desc' => 'Menyediakan fungsi parser yang dapat dikonfigurasi untuk transliterasi',
	'transliterator-error-ambiguous' => 'Aturan ambigu <code>$1</code> pada [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Sintaks tidak sah <code>$1</code> pada [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Lebih dari $1 {{PLURAL:$1||}}aturan pada [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'Aturan <code>$1</code> memiliki lebih dari $3 {{PLURAL:$3||}}karakter di sebelah kiri di [[MediaWiki:$2]]',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'transliterator-desc' => '翻字のための設定可能なパーサー関数を提供する',
	'transliterator-error-ambiguous' => '曖昧な規則 <code>$1</code> が [[MediaWiki:$2]] にあります',
	'transliterator-error-syntax' => '不正な構文 <code>$1</code> が [[MediaWiki:$2]] にあります',
	'transliterator-error-rulecount' => '[[MediaWiki:$2]] には$1個を超える規則があります',
	'transliterator-error-rulesize' => '[[MediaWiki:$2]] の規則 <code>$1</code> は左辺に$3個を超える文字があります',
);

/** Ripoarisch (Ripoarisch) */
$messages['ksh'] = array(
	'transliterator-desc' => 'Deiht en ennstellbaa Paaserfunxjuhn en et Wiki, di Boochshtabe tuusche kann.',
	'transliterator-error-ambiguous' => 'En unkloh Rejel <code>$1</code> es en [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'En kappodde Syntax <code>$1</code> es en [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Et {{PLURAL:$1|es mieh wi ein Rejel|sinn_er mieh wi $1 Rejelle|es kei Rejel}} en [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'En de Rejel <code>$1</code> {{PLURAL:$3|es|sinn_er}} mieh wi $3 Zeische op de lengke Sigg, en [[MediaWiki:$2]]',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'transliterator-desc' => 'Biedt een instelbare parserlaag voor transliteratie',
	'transliterator-error-ambiguous' => 'Dubbelzinnige regel <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Ongeldige syntaxis <code>$1</code> in [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Meer dan $1 {{PLURAL:$1|regel|regels}} in [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'Regel <code>$1</code> heeft meer dan $3 {{PLURAL:$3|teken|tekens}} aan de linkerkant in [[MediaWiki:$2]]',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'transliterator-desc' => 'Provesís una foncion parser configurabla per la transliteracion',
	'transliterator-error-ambiguous' => 'Règla ambigua <code>$1</code> dins [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Sintaxi incorrècta <code>$1</code> dins [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Mai de $1 {{PLURAL:$1|règla|règlas}} dins [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => "La règla <code>$1</code> a mai de $3 {{PLURAL:$3|caractèr|caractèrs}} sus l'esquèrra dins [[MediaWiki:$2]]",
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'transliterator-desc' => 'Обеспечивает настраиваемую функцию парсера для транслитерации',
	'transliterator-error-ambiguous' => 'Неоднозначно правило <code>$1</code> в [[MediaWiki:$2]]',
	'transliterator-error-syntax' => 'Ошибочный синтаксис <code>$1</code> в [[MediaWiki:$2]]',
	'transliterator-error-rulecount' => 'Более $1 {{PLURAL:$1|правила|правил|правил}} в [[MediaWiki:$2]]',
	'transliterator-error-rulesize' => 'Правило <code>$1</code> содержит более $3 {{PLURAL:$3|символа|символов|символов}} слева в [[MediaWiki:$2]]',
);

