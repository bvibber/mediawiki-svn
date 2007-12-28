<?php
/**
 * Internationalisation file for extension SpamRegex.
 *
 * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'spamregex' => 'SpamRegex',
	'spamregex_summary' => 'The text was found in the page\'s summary.',
	'spamregex-intro' => 'Use this form to effectively block expressions from saving into a page\'s text. If the text contains the given expression, change would not be saved and an explanation will be displayed to user that tried to save the page. Caution advised, expressions should not be too short or too common.',
	'spamregex-page-title' => 'Spam Regex Unwanted Expressions Block',
	'spamregex-currently-blocked' => "'''Currently blocked phrases:'''",
	'spamregex-no-currently-blocked' => "'''There are no blocked phrases.'''",
	'spamregex-log-1' => '* \'\'\'$1\'\'\' $2 ([{{SERVER}}$3&text=$4 remove]) added by ',
	'spamregex-log-2' => ' on $1',
	'spamregex-page-title-1' => 'Block phrase using regular expressions',
	'spamregex-unblock-success' => 'Unblock succedeed',
	'spamregex-unblock-message' => 'Phrase \'\'\'$1\'\'\' has been unblocked from editing.',
	'spamregex-page-title-2' => 'Block phrases from saving using regular expressions',
	'spamregex-block-success' => 'Block succedeed',
	'spamregex-block-message' => 'Phrase \'\'\'$1\'\'\' has been blocked.',
	'spamregex-warning-1' => 'Give a phrase to block.',
	'spamregex-error-1' =>'Invalid regular expression.',
	'spamregex-warning-2' => 'Please check at least one blocking mode.',
	'spamregex-already-blocked' => '"$1" is already blocked',
	'spamregex-phrase-block' => 'Phrase to block:',
	'spamregex-phrase-block-text' => 'block phrase in page text',
	'spamregex-phrase-block-summary' => 'block phrase in summary',
	'spamregex-block-submit' => 'Block&nbsp;this&nbsp;phrase',
	'spamregex-text' => '(Text)',
	'spamregex-summary-log' => '(Summary)',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'spamregex'                      => 'سبام ريجيكس',
	'spamregex_summary'              => 'النص تم العثور عليه في ملخص الصفحة.',
	'spamregex-intro'                => 'استخدم هذه الاستمارة لمنع تعبيرات من الحفظ في نص صفحة بكفاءة. لو أن النص يحتوي على التعبير المعطى، لن يتم حفظ التغيير وسيتم عرض تفسير للمستخدم الذي حاول حفظ الصفحة. ينصح بالحذر، التعبيرات لا ينبغي أن تكون قصيرة جدا أو شائعة جدا.',
	'spamregex-page-title'           => 'منع سبام ريجيكس التعبيرات غير المرغوب فيها',
	'spamregex-currently-blocked'    => "'''العبارات الممنوعة حاليا:'''",
	'spamregex-no-currently-blocked' => "'''لا توجد عبارات ممنوعة.'''",
	'spamregex-log-1'                => "* '''$1''' $2 ([{{SERVER}}$3&text=$4 إزالة]) تمت إضافتها بواسطة",
	'spamregex-log-2'                => ' في $1',
	'spamregex-page-title-1'         => 'منع عبارة باستخدام التعبيرات المنتظمة',
	'spamregex-unblock-success'      => 'رفع المنع نجح',
	'spamregex-unblock-message'      => "العبارة '''$1''' تم رفع المنع عنها ضد التحرير.",
	'spamregex-page-title-2'         => 'منع العبارات من الحفظ باستخدام التعبيرات المنتظمة',
	'spamregex-block-success'        => 'المنع نجح',
	'spamregex-block-message'        => "العبارة '''$1''' تم منعها.",
	'spamregex-warning-1'            => 'أعط عبارة للمنع.',
	'spamregex-error-1'              => 'تعبير منتظم غير صحيح.',
	'spamregex-warning-2'            => 'من فضلك علم على نمط منع واحد على الأقل.',
	'spamregex-already-blocked'      => '"$1" ممنوعة بالفعل',
	'spamregex-phrase-block'         => 'العبارة للمنع:',
	'spamregex-phrase-block-text'    => 'منع عبارة في نص صفحة',
	'spamregex-phrase-block-summary' => 'منع عبارة في ملخص',
	'spamregex-block-submit'         => 'منع&nbsp;هذه&nbsp;العبارة',
	'spamregex-text'                 => '(نص)',
	'spamregex-summary-log'          => '(ملخص)',
);

/** Bulgarian (Български)
 * @author Spiritia
 * @author DCLXVI
 */
$messages['bg'] = array(
	'spamregex-currently-blocked'    => "'''Текущо блокирани фрази:'''",
	'spamregex-no-currently-blocked' => "'''Няма блокирани фрази.'''",
	'spamregex-page-title-1'         => 'Блокиране на фрази чрез регулярни изрази',
	'spamregex-unblock-success'      => 'Успешно разблокиране',
	'spamregex-page-title-2'         => 'Блокиране на съхранението на фраза посредством регулярни изрази',
	'spamregex-block-success'        => 'Успешно блокиране',
	'spamregex-block-message'        => "Фразата '''$1''' беше блокирана.",
	'spamregex-error-1'              => 'Невалиден регулярен израз.',
	'spamregex-phrase-block'         => 'Фраза за блокиране:',
	'spamregex-phrase-block-text'    => 'блокиране на фраза в текста на статията',
	'spamregex-phrase-block-summary' => 'блокиране на фраза в резюмето',
	'spamregex-text'                 => '(Текст)',
	'spamregex-summary-log'          => '(Резюме)',
);

/** Greek (Ελληνικά)
 * @author Consta
 */
$messages['el'] = array(
	'spamregex-text'        => '(κείμενο)',
	'spamregex-summary-log' => '(περίληψη)',
);

$messages ['fr'] = array(
	'spamregex' => 'Expressions régulières de Spams',
	'spamregex_summary' => 'Le texte en question a été détecté dans le commentaire de la page.',
	'spamregex-intro' => 'Utilisez ce formulaire pour bloquer effectivement les expressions pouvant être sauvegardées dans une page texte. Si le texte contient les expressions définies, les changements ne pourront être sauvegardés et un motif explicatif sera affiché à l’utilisateur qui a voulu sauvegarder la page. Il est important de prendre en considération que les expressions ne devront être ni trop longues ni trop courantes.',
	'spamregex-page-title' => 'Blocage des expressions régulières de spams',
	'spamregex-currently-blocked' => "'''Phrases actuellement bloquées :'''",
	'spamregex-no-currently-blocked' => "'''Il n’y a aucune phrase bloquée.'''",
	'spamregex-log-1' => '* \'\'\'$1\'\'\' $2 ([{{SERVER}}$3&text=$4 supprimer]) ajouté par ',
	'spamregex-log-2' => ' le $1',
	'spamregex-page-title-1' => 'Blocage d’une phrase utilisant des expressions régulières',
	'spamregex-unblock-success' => 'Le déblocage a réussi',
	'spamregex-unblock-message' => 'La phrase \'\'\'$1\'\'\' a été débloquée à l’édition.',
	'spamregex-page-title-2' => 'Blocage des phrases en utilisant des expression régulières',
	'spamregex-block-success' => 'Le blocage a réussi',
	'spamregex-block-message' => 'La phrase \'\'\'$1\'\'\' a été bloquée.',
	'spamregex-warning-1' => 'Indiquez une phrase à bloquer.',
	'spamregex-error-1' =>'Expression régulière invalide.',
	'spamregex-warning-2' => 'Choisissez au moins un mode de blocage.',
	'spamregex-already-blocked' => '« $1 » est déjà bloqué',
	'spamregex-phrase-block' => 'Phrase à bloquer :',
	'spamregex-phrase-block-text' => 'bloquer la phrase dans le texte de l’article',
	'spamregex-phrase-block-summary' => 'bloquer la phrase dans le commentaire',
	'spamregex-block-submit' => 'Bloquer&nbsp;cette&nbsp;phrase',
	'spamregex-text' => '(Texte)',
	'spamregex-summary-log' => '(Commentaire)',
);

/** Galician (Galego)
 * @author Alma
 * @author Xosé
 */
$messages['gl'] = array(
	'spamregex_summary'              => 'O texto foi atopado no resumo do artigo.',
	'spamregex-currently-blocked'    => "'''Frases actualmente bloqueadas:'''",
	'spamregex-no-currently-blocked' => "'''Non hai frases bloqueadas.'''",
	'spamregex-log-2'                => 'en $1',
	'spamregex-page-title-1'         => 'Bloquear frase usando expresións regulares',
	'spamregex-unblock-success'      => 'Desbloqueo con éxito',
	'spamregex-unblock-message'      => "A frase '''$1''' foi desbloqueada para a edición.",
	'spamregex-block-success'        => 'Bloqueo con éxito',
	'spamregex-block-message'        => "A frase '''$1''' foi bloqueada.",
	'spamregex-warning-1'            => 'Dar unha frase para bloquear.',
	'spamregex-error-1'              => 'Expresión regular non válida.',
	'spamregex-already-blocked'      => '"$1" está aínda bloqueado',
	'spamregex-phrase-block'         => 'Frase para bloquear:',
	'spamregex-phrase-block-text'    => 'bloquear unha frase no texto do artigo',
	'spamregex-phrase-block-summary' => 'bloquear frase no resumo',
	'spamregex-block-submit'         => 'Bloquear&nbsp;esta&nbsp;frase',
	'spamregex-text'                 => '(Texto)',
	'spamregex-summary-log'          => '(Resumo)',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'spamregex_summary'              => 'Tekst je so w zjeću nastawka namakał.',
	'spamregex-intro'                => 'Wužij tutón formular, zo by wurazy skutkownje přećiwo składowanju w teksće strony blokował. Jel tekst daty wuraz wobsahuje, změna njeby so składowała a wujasnjenje so wužiwarjej, kiž je spytał stronu składować, pokaza. Jewi so warnowanje, zo wurazy njesmědźa překrótke abo přepowšitkowne być.',
	'spamregex-page-title'           => 'Spam Regex Blokowanje njepožadanych wurazow',
	'spamregex-currently-blocked'    => "'''Tuchwilu zablokowane frazy:'''",
	'spamregex-no-currently-blocked' => "'''Zablokowane frazy njejsu.'''",
	'spamregex-log-1'                => "* '''$1''' $2 ([{{SERVER}}$3&text=$4 wotstronić]) přidaty wot",
	'spamregex-log-2'                => 'na $1',
	'spamregex-page-title-1'         => 'Frazu, kotraž regularne wurazy wužiwa, blokować',
	'spamregex-unblock-success'      => 'Wotblokowanje wuspěšne',
	'spamregex-unblock-message'      => "Fraza '''$1''' bu za wobdźěłowanje dopušćena.",
	'spamregex-page-title-2'         => 'Frazy z pomocu regularnych wurazow za składowanje blokować',
	'spamregex-block-success'        => 'Blokowanje wuspěšne',
	'spamregex-block-message'        => "Fraza '''$1''' bu zablokowana.",
	'spamregex-warning-1'            => 'Podaj frazu za blokowanje.',
	'spamregex-error-1'              => 'Njepłaćiwy regularny wuraz.',
	'spamregex-warning-2'            => 'Prošu přepruwuj znajmjeńša jedyn blokowanski modus.',
	'spamregex-already-blocked'      => '"$1" je hižo zablokowany',
	'spamregex-phrase-block'         => 'Fraza, kotraž ma so blokować:',
	'spamregex-phrase-block-text'    => 'frazu w teksće nastawka blokować',
	'spamregex-phrase-block-summary' => 'frazu w zjeću blokować',
	'spamregex-block-submit'         => 'Tutu&nbsp;frazu&nbsp;blokować',
	'spamregex-text'                 => '(Tekst)',
	'spamregex-summary-log'          => '(Zjeće)',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'spamregex'                      => 'SpamRegex',
	'spamregex_summary'              => 'De tekst is gevonden in de paginasamenvatting.',
	'spamregex-intro'                => 'Gebruik dit formulier om doeltreffend uitdrukkingen te blokkeren van opslaan in een paginatekst. Als de tekst de gegeven uitdrukkingen bevat, dan zal de wijziging niet worden opgeslaan en zal een uitleg getoond worden aan de gebruiker die de pagina probeerde op te slaan. Het wordt aangeraden dat de uitdrukkingen niet te kort of te veelvoorkomend zouden mogen zijn.',
	'spamregex-page-title'           => 'Blokkeren van uitdrukkingen met regex',
	'spamregex-currently-blocked'    => "'''Huidig geblokkeerde zinnen:'''",
	'spamregex-no-currently-blocked' => "'''Er zijn geen geblokkeerde zinnen.'''",
	'spamregex-log-1'                => "* '''$1''' $2 ([{{SERVER}}$3&text=$4 verwijderen]) toegevoegd door",
	'spamregex-log-2'                => ' op $1',
	'spamregex-page-title-1'         => 'Zinnen blokkeren met reguliere expressies',
	'spamregex-unblock-success'      => 'Deblokkade gelukt',
	'spamregex-unblock-message'      => "Zin '''$1''' is gedeblokkeerd van bewerkingen.",
	'spamregex-page-title-2'         => 'Zinnen blokkeren van opslaan met reguliere expressies',
	'spamregex-block-success'        => 'Blokkade gelukt.',
	'spamregex-block-message'        => "Zin '''$1''' is geblokkeerd.",
	'spamregex-warning-1'            => 'Geef een zin om te blokkeren.',
	'spamregex-error-1'              => 'Ongeldige reguliere expressie.',
	'spamregex-warning-2'            => 'Gelieve tenminste één blokkeermogelijkheid aan te klikken.',
	'spamregex-already-blocked'      => '"$1" is al geblokkeerd',
	'spamregex-phrase-block'         => 'Zin om te blokkeren:',
	'spamregex-phrase-block-text'    => 'zin blokkeren in paginatekst',
	'spamregex-phrase-block-summary' => 'zin blokkeren in samenvatting',
	'spamregex-block-submit'         => 'Deze&nbsp;zin&nbsp;blokkeren',
	'spamregex-text'                 => '(Tekst)',
	'spamregex-summary-log'          => '(Samenvatting)',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'spamregex'                      => 'Expressions regularas de Spams',
	'spamregex_summary'              => 'Lo tèxt en question es estat detectat dins lo comentari de la pagina.',
	'spamregex-intro'                => "Utilizatz aqueste formulari per blocar efièchament las expressions que pòdon èsser salvagardadas dins una pagina tèxt. Se lo tèxt conten las expressions definidas, los cambiaments poiràn pas èsser salvagardats e un motiu explicatiu serà afichat a l’utilizaire qu'a volgut salvagardar la pagina. Es important de prendre en consideracion que las expressions deuràn pas èsser ni tròp longas ni tròp correntas.",
	'spamregex-page-title'           => 'Blocatge de las expressions regularas de spams',
	'spamregex-currently-blocked'    => "'''Frasas actualament blocadas :'''",
	'spamregex-no-currently-blocked' => "'''I a pas cap de frasa blocada.'''",
	'spamregex-log-1'                => "* '''$1''' $2 ([{{SERVER}}$3&text=$4 suprimir]) ajustat per",
	'spamregex-log-2'                => 'lo $1',
	'spamregex-page-title-1'         => "Blocatge d’una frasa utilizant d'expressions regularas",
	'spamregex-unblock-success'      => 'Lo desblocatge a capitat',
	'spamregex-unblock-message'      => "La frasa '''$1''' es estada desblocada a l’edicion.",
	'spamregex-page-title-2'         => "Blocatge de las frasas en utilizant d'expression regularas",
	'spamregex-block-success'        => 'Lo blocatge a capitat',
	'spamregex-block-message'        => "La frasa '''$1''' a estada blocada.",
	'spamregex-warning-1'            => 'Indicatz una frasa de blocar.',
	'spamregex-error-1'              => 'Expression regulara invalida.',
	'spamregex-warning-2'            => 'Causissètz al mens un mòde de blocatge.',
	'spamregex-already-blocked'      => '« $1 » ja es blocat',
	'spamregex-phrase-block'         => 'Frasa de blocar :',
	'spamregex-phrase-block-text'    => 'blocar la frasa dins lo tèxt de l’article',
	'spamregex-phrase-block-summary' => 'blocar la frasa dins lo comentari',
	'spamregex-block-submit'         => 'Blocar&nbsp;aquesta&nbsp;frasa',
	'spamregex-text'                 => '(Tèxt)',
	'spamregex-summary-log'          => '(Comentari)',
);

