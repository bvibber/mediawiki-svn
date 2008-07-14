<?php
/**
 * Internationalisation file for the extension ErrorHandler.
 *
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author IAlex
 */
$messages['en'] = array(
	'errorhandler-desc'                  => 'Error handler for MediaWiki',
	'errorhandler-errors'                => 'Errors:',
	'errorhandler-error-fatal'           => 'Fatal error',
	'errorhandler-error-warning'         => 'Warning',
	'errorhandler-error-parse'           => 'Parser error',
	'errorhandler-error-notice'          => 'Notice',
	'errorhandler-error-core-error'      => 'Core error',
	'errorhandler-error-core-warning'    => 'Core warning',
	'errorhandler-error-compile-error'   => 'Compile error',
	'errorhandler-error-compile-warning' => 'Compile warning',
	'errorhandler-error-user-error'      => 'User error',
	'errorhandler-error-user-warning'    => 'User warning',
	'errorhandler-error-user-notice'     => 'User notice',
	'errorhandler-error-strict'          => 'Stricts standarts',
	'errorhandler-error-recoverable'     => 'Catchable fatal error',
	'errorhandler-msg-text'              => '$1 : $2 in $3 (line $4)',
	'errorhandler-msg-html'              => '<b>$1</b> : <i>$2</i> in <b>$3</b> (line <b>$4</b>)',
	'errorhandler-trace'                 => 'trace:',
	'errorhandler-trace-line'            => '$1 (line $2): $3',
	'errorhandler-trace-line-internal'   => '[internal function]: $1',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'errorhandler-errors'              => 'الأخطاء:',
	'errorhandler-error-fatal'         => 'خطأ قاتل',
	'errorhandler-error-warning'       => 'تحذير',
	'errorhandler-error-parse'         => 'خطأ محلل',
	'errorhandler-error-notice'        => 'ملاحظة',
	'errorhandler-error-core-error'    => 'خطأ قلب',
	'errorhandler-error-core-warning'  => 'تحذير قلب',
	'errorhandler-error-user-error'    => 'خطأ مستخدم',
	'errorhandler-error-user-warning'  => 'تحذير مستخدم',
	'errorhandler-error-user-notice'   => 'ملاحظة مستخدم',
	'errorhandler-msg-text'            => '$1 : $2 في $3 (سطر $4)',
	'errorhandler-trace-line'          => '$1 (سطر $2): $3',
	'errorhandler-trace-line-internal' => '[وظيفة داخلية]: $1',
);

/** French (Français)
 * @author IAlex
 */
$messages['fr'] = array(
	'errorhandler-desc'                  => "Gestionnaire d'erreurs pour MediaWiki",
	'errorhandler-errors'                => 'Erreurs :',
	'errorhandler-error-fatal'           => 'Erreur fatale',
	'errorhandler-error-warning'         => 'Avertissement',
	'errorhandler-error-parse'           => 'Erreur de parse',
	'errorhandler-error-notice'          => 'Notice',
	'errorhandler-error-core-error'      => 'Erreur du coeur',
	'errorhandler-error-core-warning'    => 'Avertissement du coeur',
	'errorhandler-error-compile-error'   => 'Erreur de compliation',
	'errorhandler-error-compile-warning' => 'Avertissement de compilation',
	'errorhandler-error-user-error'      => 'Erreur (utilisateur)',
	'errorhandler-error-user-warning'    => 'Avertissement (utilisateur)',
	'errorhandler-error-user-notice'     => 'Notice (utilisateur)',
	'errorhandler-error-strict'          => 'Standarts stricts',
	'errorhandler-error-recoverable'     => 'Erreur fatale attrapable',
	'errorhandler-msg-text'              => '$1 : $2 dans $3 (ligne $4)',
	'errorhandler-msg-html'              => '<b>$1</b> : <i>$2</i> dans <b>$3</b> (ligne <b>$4</b>)',
	'errorhandler-trace'                 => 'trace :',
	'errorhandler-trace-line'            => '$1 (ligne $2): $3',
	'errorhandler-trace-line-internal'   => '[fonction interne] : $1',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'errorhandler-desc'                  => 'Erro do manipulador de MediaWiki',
	'errorhandler-errors'                => 'Erros:',
	'errorhandler-error-fatal'           => 'Erro fatal',
	'errorhandler-error-warning'         => 'Aviso',
	'errorhandler-error-parse'           => 'Erro de análise',
	'errorhandler-error-notice'          => 'Nota',
	'errorhandler-error-core-error'      => 'Erro central',
	'errorhandler-error-core-warning'    => 'Aviso central',
	'errorhandler-error-compile-error'   => 'Erro de recompilación',
	'errorhandler-error-compile-warning' => 'Aviso de recompilación',
	'errorhandler-error-user-error'      => 'Erro de usuario',
	'errorhandler-error-user-warning'    => 'Aviso de usuario',
	'errorhandler-error-user-notice'     => 'Nota de usuario',
	'errorhandler-error-strict'          => 'Estándares estritos',
	'errorhandler-error-recoverable'     => 'Erro fatal recuperable',
	'errorhandler-msg-text'              => '$1 : $2 en $3 (liña $4)',
	'errorhandler-msg-html'              => '<b>$1</b> : <i>$2</i> en <b>$3</b> (liña <b>$4</b>)',
	'errorhandler-trace'                 => 'trazo:',
	'errorhandler-trace-line'            => '$1 (liña $2): $3',
	'errorhandler-trace-line-internal'   => '[función interna]: $1',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'errorhandler-errors'              => 'Feeler:',
	'errorhandler-error-fatal'         => 'Fatale Feeler',
	'errorhandler-error-warning'       => 'Warnung',
	'errorhandler-error-parse'         => 'Parser-Feeler',
	'errorhandler-error-notice'        => 'Notiz',
	'errorhandler-msg-text'            => '$1 : $2 a(n) $3 (Linn $4)',
	'errorhandler-trace-line-internal' => '[intern Fonctioun]: $1',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'errorhandler-desc'                  => 'Foutafhandeling voor MediaWiki',
	'errorhandler-errors'                => 'Fouten:',
	'errorhandler-error-fatal'           => 'Fatale fout',
	'errorhandler-error-warning'         => 'Waarschuwing',
	'errorhandler-error-parse'           => 'Parserfout',
	'errorhandler-error-notice'          => 'Mededeling',
	'errorhandler-error-core-error'      => 'Kernfout',
	'errorhandler-error-core-warning'    => 'Kernwaarschuwing',
	'errorhandler-error-compile-error'   => 'Compileerfout',
	'errorhandler-error-compile-warning' => 'Compileerwaarschuwing',
	'errorhandler-error-user-error'      => 'Gebruikersfout',
	'errorhandler-error-user-warning'    => 'Gebruikerswaarschuwing',
	'errorhandler-error-user-notice'     => 'Gebruikersmededeling',
	'errorhandler-error-strict'          => 'Strikte standaarden',
	'errorhandler-error-recoverable'     => 'Op te vangen fatale fout',
	'errorhandler-msg-text'              => '$1 : $2 in $3 (regel $4)',
	'errorhandler-msg-html'              => '<b>$1</b> : <i>$2</i> in <b>$3</b> (regel <b>$4</b>)',
	'errorhandler-trace'                 => 'trace:',
	'errorhandler-trace-line'            => '$1 (regel $2): $3',
	'errorhandler-trace-line-internal'   => '[interne functie]: $1',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'errorhandler-desc'             => "Gestionari d'errors per MediaWiki",
	'errorhandler-errors'           => 'Errors :',
	'errorhandler-error-fatal'      => 'Error fatala',
	'errorhandler-error-warning'    => 'Avertiment',
	'errorhandler-error-parse'      => 'Error de parser',
	'errorhandler-error-notice'     => 'Notícia',
	'errorhandler-error-core-error' => 'Error del còr',
);

/** Polish (Polski)
 * @author Maikking
 */
$messages['pl'] = array(
	'errorhandler-errors'        => 'Błędy:',
	'errorhandler-error-fatal'   => 'Krytyczny błąd',
	'errorhandler-error-warning' => 'Ostrzeżenie',
	'errorhandler-error-parse'   => 'Błąd parsera',
	'errorhandler-msg-text'      => '$1 : $2 w $3 (linia $4)',
	'errorhandler-trace-line'    => '$1 (linia $2): $3',
);

/** Russian (Русский)
 * @author Innv
 */
$messages['ru'] = array(
	'errorhandler-errors'        => 'Ошибки:',
	'errorhandler-error-fatal'   => 'Серьёзная ошибка',
	'errorhandler-error-warning' => 'Внимание',
);

/** Swedish (Svenska)
 * @author Boivie
 */
$messages['sv'] = array(
	'errorhandler-desc'                  => 'Felhanterare för MediaWiki',
	'errorhandler-errors'                => 'Fel:',
	'errorhandler-error-fatal'           => 'Allvarligt fel',
	'errorhandler-error-warning'         => 'Varning',
	'errorhandler-error-parse'           => 'Parser-fel',
	'errorhandler-error-notice'          => 'Meddelande',
	'errorhandler-error-core-error'      => 'Fel i kärnan',
	'errorhandler-error-core-warning'    => 'Kärn-varning',
	'errorhandler-error-compile-error'   => 'Kompileringsfel',
	'errorhandler-error-compile-warning' => 'Kompileringsvarning',
	'errorhandler-error-user-error'      => 'Användarfel',
	'errorhandler-error-user-warning'    => 'Användarvarning',
	'errorhandler-error-user-notice'     => 'Användarmeddelande',
	'errorhandler-error-strict'          => 'Strikta standarder',
	'errorhandler-error-recoverable'     => 'Hanterbart allvarligt fel',
	'errorhandler-msg-text'              => '$1 : $2 i $3 (rad $4)',
	'errorhandler-msg-html'              => '<b>$1</b> : <i>$2</i> i <b>$3</b> (rad <b>$4</b>)',
	'errorhandler-trace'                 => 'spåra:',
	'errorhandler-trace-line'            => '$1 (rad $2): $3',
	'errorhandler-trace-line-internal'   => '[intern funktion]: $1',
);

