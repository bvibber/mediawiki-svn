<?php
/**
 * Internationalisation file for PasswordStrength extension.
 *
 * @addtogroup Extensions
 */

$messages = array();

/**
 * English
 * @author Chad Horohoe
 */
$messages['en'] = array(
	'passwordstr-desc' => 'Perform additional security checks on passwords with regular expressions',
	'passwordstr-regex-hit' => 'Your password did not match the complexity requirements.',
	'passwordstr-needmore-ints' => 'You need at least {{PLURAL:$1|one number|$1 numbers}} (0-9)',
	'passwordstr-needmore-upper' => 'You need at least $1 upper cased {{PLURAL:$1|letter|letters}} (A-Z)',
	'passwordstr-needmore-lower' => 'You need at least $1 lower cased {{PLURAL:$1|letter|letters}} (a-z)',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'passwordstr-desc' => 'يؤدي تحققات أمنية إضافية على كلمات السر بالتعبيرات المنتظمة',
	'passwordstr-regex-hit' => 'كلمة السر الخاصة بك لم تطابق متطلبات التعقيد.',
	'passwordstr-needmore-ints' => 'أنت تحتاج على الأقل {{PLURAL:$1|رقما واحدا|$1 رقم}} (0-9)',
	'passwordstr-needmore-upper' => 'أنت تحتاج على الأقل $1 {{PLURAL:$1|حرف|حرف}} بحروف كبيرة (A-Z)',
	'passwordstr-needmore-lower' => 'أنت تحتاج على الأقل $1 {{PLURAL:$1|حرف|حرف}} بحروف صغيرة (a-z)',
);

/** German (Deutsch)
 * @author ChrisiPK
 * @author Umherirrender
 */
$messages['de'] = array(
	'passwordstr-desc' => 'Zusätzliche Sicherheitsprüfungen mit regulären Ausdrücken für Passwörter durchführen',
	'passwordstr-regex-hit' => 'Dein Passwort genügt den Komplexitätsanforderungen nicht.',
	'passwordstr-needmore-ints' => 'Du benötigst mindestens {{PLURAL:$1|eine Ziffer|$1 Ziffern}} (0-9)',
	'passwordstr-needmore-upper' => 'Du benötigst mindestens {{PLURAL:$1|einen Großbuchstaben|$1 Großbuchstaben}} (A-Z)',
	'passwordstr-needmore-lower' => 'Du benötigst mindestens {{PLURAL:$1|einen Kleinbuchstaben|$1 Kleinbuchstaben}} (a-z)',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author ChrisiPK
 * @author Umherirrender
 */
$messages['de-formal'] = array(
	'passwordstr-regex-hit' => 'Ihr Passwort genügt den Komplexitätsanforderungen nicht.',
	'passwordstr-needmore-ints' => 'Sie benötigen mindestens {{PLURAL:$1|eine Ziffer|$1 Ziffern}} (0-9)',
	'passwordstr-needmore-upper' => 'Sie benötigen mindestens {{PLURAL:$1|einen Großbuchstaben|$1 Großbuchstaben}} (A-Z)',
	'passwordstr-needmore-lower' => 'Sie benötigen mindestens {{PLURAL:$1|einen Kleinbuchstaben|$1 Kleinbuchstaben}} (a-z)',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'passwordstr-desc' => 'Pśidatne wěstotne kontrole za gronidła z regularny wurazami pśewjasć',
	'passwordstr-regex-hit' => 'Twójo gronidło njewótpowědujo pominanjam kompleksnosći.',
	'passwordstr-needmore-ints' => 'Trjebaš nanejmjenjej {{PLURAL:$1|jadnu cyfru|$1 cyfrje|$1 cyfry|$1 cyfrow}} (0-9)',
	'passwordstr-needmore-upper' => 'Trjebaš nanejmjenjej $1 {{PLURAL:$1|wjeliki pismik|wjelikej pismika|wjelike pismiki|wjelikich pismikow}} (A-Z)',
	'passwordstr-needmore-lower' => 'Trjebaš nanejmjenjej $1 {{PLURAL:$1|mały pismik|małej pismika|małe pismiki|małaych pismikow}} (a-z)',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'passwordstr-desc' => 'Realiza comprobacións de seguridade adicional nos contrasinais con expresións regulares',
	'passwordstr-regex-hit' => 'O seu contrasinal non coincide cos requirimentos de complexidade.',
	'passwordstr-needmore-ints' => 'Necesita, polo menos, {{PLURAL:$1|unha número|$1 números}} (0-9)',
	'passwordstr-needmore-upper' => 'Necesita, polo menos, {{PLURAL:$1|unha letra maiúscula|$1 letras maiúsculas}} (A-Z)',
	'passwordstr-needmore-lower' => 'Necesita, polo menos, {{PLURAL:$1|unha letra minúscula|$1 letras minúsculas}} (a-z)',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'passwordstr-desc' => 'Zuesätzligi Sicherheitspriefige mit reguläre Uusdruck fir Passwerter durfiere',
	'passwordstr-regex-hit' => 'Dyy Passwort längt nit wäge dr Komplexitätsaaforderige.',
	'passwordstr-needmore-ints' => 'Du bruuchsch zmindescht {{PLURAL:$1|ei Ziffere|$1 Ziffere}} (0-9)',
	'passwordstr-needmore-upper' => 'Du bruuchsch zmindescht {{PLURAL:$1|ei Großbuechstab|$1 Großbuechstabe}} (A-Z)',
	'passwordstr-needmore-lower' => 'Du bruuchsch zmindescht {{PLURAL:$1|ei Chleibuechstab|$1 Chleibuechstabe}} (a-z)',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'passwordstr-desc' => 'Přidatne wěstotne kontrole za hesä z regularnymi wurazami přewjesć',
	'passwordstr-regex-hit' => 'Twoje hesło njewotpowěduje žadanjam kompleksnosće.',
	'passwordstr-needmore-ints' => 'Trjebaš znajmjeńša {{PLURAL:$1|jednu cyfru|$1 cyfrje|$1 cyfry|$1 cyfrow}} (0-9)',
	'passwordstr-needmore-upper' => 'Trjebaš znajmjeńša $1 {{PLURAL:$1|wulki pismik|wulkej pismikaj|wulke pismiki|wulkich pismikow}} (A-Z)',
	'passwordstr-needmore-lower' => 'Trjebaš znajmjeńša $1 {{PLURAL:$1|mały pismik|małej pismikaj|małe pismiki|małych pismikow}} (a-z)',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'passwordstr-desc' => 'Executar verificationes de securitate additional super contrasignos con expressiones regular',
	'passwordstr-regex-hit' => 'Tu contrasigno non satisfaceva le requisitos de complexitate.',
	'passwordstr-needmore-ints' => 'Tu debe inserer al minus {{PLURAL:$1|un numero|$1 numeros}} (0-9)',
	'passwordstr-needmore-upper' => 'Tu debe inserer al minus $1 {{PLURAL:$1|littera|litteras}} majuscule (A-Z)',
	'passwordstr-needmore-lower' => 'Tu debe inserer al minus $1 {{PLURAL:$1|littera|litteras}} minuscule (a-z)',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'passwordstr-desc' => 'Zosätlijje Prööfunge för de Passwööter met <i lang="en">regular expressions</i>.',
	'passwordstr-regex-hit' => 'Ding Passwoot es nit kumplezeet jenoch.',
	'passwordstr-needmore-ints' => 'Do bruchs {{PLURAL:$1|winnischsdens ein|winnischsdens $1|kein}} Zeffer (0…9)',
	'passwordstr-needmore-upper' => 'Do bruchs {{PLURAL:$1|winnischsdens eine|winnischsdens $1|keine}} jruuße Bochshtabe (A…Z)',
	'passwordstr-needmore-lower' => 'Do bruchs {{PLURAL:$1|winnischsdens eine|winnischsdens $1|keine}} kleine Bochshtabe (a…z)',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Harald Khan
 */
$messages['nn'] = array(
	'passwordstr-desc' => 'Utfør ekstra tryggleikskontrollar på passord gjennom regulære uttrykk',
	'passwordstr-regex-hit' => 'Passordet ditt har ikkje ein påkravd kompleksitet.',
	'passwordstr-needmore-ints' => 'Du treng minst {{PLURAL:$1|eitt tal|$1 tal}} (0-9)',
	'passwordstr-needmore-upper' => 'Du treng minst {{PLURAL:$1|éin|$1}} {{PLURAL:$1|stor bokstav|store bokstavar}} (A-Z)',
	'passwordstr-needmore-lower' => 'Du treng minst {{PLURAL:$1|éin|$1}} {{PLURAL:$1|liten bokstav|små bokstavar}} (a-z)',
);

/** Portuguese (Português)
 * @author Waldir
 */
$messages['pt'] = array(
	'passwordstr-desc' => 'Realizar verificações de segurança adicionais nas palavras-chave, usando expressões regulares',
	'passwordstr-regex-hit' => 'A sua palavra-chave não correspondeu aos parâmetros de complexidade requisitos.',
	'passwordstr-needmore-ints' => 'Você precisa de pelo menos {{PLURAL:$1|um número|$1 números}} (0-9)',
	'passwordstr-needmore-upper' => 'Você precisa de pelo menos $1 {{PLURAL:$1|letra maiúscula|letras maiúsculas}} (A-Z)',
	'passwordstr-needmore-lower' => 'Você precisa de pelo menos $1 {{PLURAL:$1|letra minúscula|letras minúsculas}} (a-z)',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'passwordstr-desc' => 'Vykonávať ďalšie bezpečnostné kontroly hesiel pomocou regulárnych výrazov',
	'passwordstr-regex-hit' => 'Vaše heslo nezodpovedá požiadavkám na zložitosť.',
	'passwordstr-needmore-ints' => 'Musí obsahovať aspoň {{PLURAL:$1|jednu číslicu|$1 číslice|$1 číslic}} (0-9)',
	'passwordstr-needmore-upper' => 'Musí mať aspoň $1 {{PLURAL:$1|veľké písmeno|veľké písmená|veľkých písmen}} (A-Z)',
	'passwordstr-needmore-lower' => 'Musí mať aspoň $1 {{PLURAL:$1|malé písmeno|malé písmená|malých písmen}} (a-z)',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'passwordstr-desc' => 'సంకేతపదాలపై రెగ్యులర్ ఎక్స్‌ప్రెషన్లతో అదనపు భద్రతా పరీక్షలు చేయండి',
	'passwordstr-regex-hit' => 'మీ సంకేతపదం సంక్లిష్ఠతా నియమాలకు సరితూగలేదు.',
	'passwordstr-needmore-ints' => 'మీరు కనీసం {{PLURAL:$1|ఒక అంకె|$1 అంకెలు}} (0-9) అయినా ఇవ్వాలి',
	'passwordstr-needmore-upper' => 'మీరు కనీసం $1 పెద్ద బడి {{PLURAL:$1|అక్షరం|అక్షరాలు}} (A-Z) ఇవ్వాలి',
	'passwordstr-needmore-lower' => 'మీరు కనీసం $1 చిన్న బడి {{PLURAL:$1|అక్షరం|అక్షరాలు}} (a-z) ఇవ్వాలి',
);

/** Tagalog (Tagalog)
 * @author AnakngAraw
 */
$messages['tl'] = array(
	'passwordstr-desc' => 'Magsagawa ng karagdagang mga pagsusuring pangseguridad sa mga hudyat na may karaniwang mga pagsasaad',
	'passwordstr-regex-hit' => 'Hindi tumugma ang hudyat mo sa mga pangangailangang kasalimuotan.',
	'passwordstr-needmore-ints' => 'Kailangan mo ang kahit na {{PLURAL:$1|isang bilang|$1 mga bilang}} (0-9)',
	'passwordstr-needmore-upper' => 'Kailangan mo ang kahit na $1 malaking {{PLURAL:$1|titik|mga titik}} (A-Z)',
	'passwordstr-needmore-lower' => 'Kailangan mo ang kahit na $1 maliit na {{PLURAL:$1|titik|mga titik}} (a-z)',
);

