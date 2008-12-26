<?php
/**
 * Internationalisation file for SecurePasswords extension.
 */

$messages = array();

/** English
 * @author Ryan Schmidt
 */
$messages['en'] = array(
	'securepasswords-desc' => 'Creates more secure password hashes and adds a password strength checker',
	'securepasswords-valid' => 'Your password is invalid or too short.
It must:',
	'securepasswords-minlength' => 'be at least $1 {{PLURAL:$1|character|characters}} long',
	'securepasswords-lowercase' => 'contain at least 1 lowercase letter',
	'securepasswords-uppercase' => 'contain at least 1 uppercase letter',
	'securepasswords-digit' => 'contain at least 1 digit',
	'securepasswords-special' => 'contain at least 1 special character (special characters are: $1)',
	'securepasswords-username' => 'be different from your username',
	'securepasswords-word' => 'not be a word',
);

/** Arabic (العربية)
 * @author Ouda
 */
$messages['ar'] = array(
	'securepasswords-username' => 'تكون مختلفة عن اسم المستخدم',
	'securepasswords-word' => 'لا تكون كلمة',
);

/** German (Deutsch)
 * @author Melancholie
 */
$messages['de'] = array(
	'securepasswords-desc' => 'Erzeugt sicherere Passwort-Hashes und fügt eine Passwortstärkenprüfung hinzu',
	'securepasswords-valid' => 'Dein Passwort ist ungültig oder zu kurz.
Es muss:',
	'securepasswords-minlength' => 'mindestens $1 Zeichen lang sein',
	'securepasswords-lowercase' => 'mindestens einen Kleinbuchstaben enthalten',
	'securepasswords-uppercase' => 'mindestens einen Großbuchstaben enthalten',
	'securepasswords-digit' => 'mindestens eine Ziffer enthalten',
	'securepasswords-special' => 'mindestens ein Sonderzeichen enthalten (Sonderzeichen sind: $1)',
	'securepasswords-username' => 'sich von deinem Benutzernamen unterscheiden',
	'securepasswords-word' => 'etwas anderes sein als ein Wort',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'securepasswords-desc' => 'Napórajo wěsćejše gronidłowe hašy a pśidawa funkciju za kontrolěrowanje mócy gronidła.',
	'securepasswords-valid' => 'Twójo gronidło jo njepłaśiwe abo pśekrotko. Musy:',
	'securepasswords-minlength' => 'nanejmjenjej $1 {{PLURAL:$1|znamuško|znamušce|znamuška|znamuškow}} dłujke byś',
	'securepasswords-lowercase' => 'nanejmjenjej 1 mały pismik wopśimjeś',
	'securepasswords-uppercase' => 'nanejmjenjej 1 wjeliki pismik wopśimjeś',
	'securepasswords-digit' => 'nanejmjenjej 1 cyfru wopśimjeś',
	'securepasswords-special' => 'nanejmjenjej 1 specialne znamuško wopśimjeś (Specialne znamuška su: $1)',
	'securepasswords-username' => 'se wót twójogo wužywarske mjenja rozeznawaś',
	'securepasswords-word' => 'něco druge byś ako słowo',
);

/** French (Français)
 * @author Crochet.david
 * @author Grondin
 * @author IAlex
 */
$messages['fr'] = array(
	'securepasswords-desc' => 'Crée des hachages de mots de passe plus sûrs et ajoute un vérificateur de complexité de mots de passe',
	'securepasswords-valid' => 'Votre mot de passe est invalide ou trop court. Il doit :',
	'securepasswords-minlength' => 'être long d’au moins $1 {{PLURAL:$1|caractère|caractères}}',
	'securepasswords-lowercase' => 'contenir au moins 1 lettre minuscule',
	'securepasswords-uppercase' => 'contenir au moins 1 lettre majuscule',
	'securepasswords-digit' => 'contenir au moins 1 chiffre',
	'securepasswords-special' => 'contenir au moins 1 caractère spécial (les caractères spéciaux sont : $1)',
	'securepasswords-username' => "être différent de votre nom d'utilisateur",
	'securepasswords-word' => 'ne pas être un mot',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'securepasswords-desc' => 'Crea un contrasinal cardinal máis seguro e engade un comprobador da fortaleza deste',
	'securepasswords-valid' => 'O seu contrasinal é inválido ou moi curto.
Debe:',
	'securepasswords-minlength' => 'ter, polo menos, {{PLURAL:$1|un carácter|$1 caracteres}}',
	'securepasswords-lowercase' => 'conter, polo menos, unha letra minúscula',
	'securepasswords-uppercase' => 'conter, polo menos, unha letra maiúscula',
	'securepasswords-digit' => 'conter, polo menos, un díxito',
	'securepasswords-special' => 'conter, polo menos, un carácter especial (caracteres especiais son: $1)',
	'securepasswords-username' => 'ser diferente do seu nome de usuario',
	'securepasswords-word' => 'non ser unha palabra',
);

/** Hebrew (עברית)
 * @author Rotemliss
 * @author YaronSh
 */
$messages['he'] = array(
	'securepasswords-desc' => 'יצירת גיבובי סיסמאות מאובטחים יותר והוספת בודק חוזק סיסמאות',
	'securepasswords-valid' => 'הסיסמה שלכם אינה תקינה או קצרה מדי. עליה:',
	'securepasswords-minlength' => 'להיות לפחות באורך של {{PLURAL:$1|ספרה אחת|$1 ספרות}}',
	'securepasswords-lowercase' => 'להכיל לפחות אות קטנה אחת',
	'securepasswords-uppercase' => 'להכיל לפחות אות גדולה אחת',
	'securepasswords-digit' => 'להכיל לפחות ספרה אחת',
	'securepasswords-special' => 'להכיל לפחות תו מיוחד אחד (התווים המיוחדים הם: $1)',
	'securepasswords-username' => 'להיות שונה משם המשתמש שלכם',
	'securepasswords-word' => 'לא להיות מילה',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'securepasswords-desc' => 'iše hesłowe haše a přidawa funkciju za kontrolowanje hesłoweje mocy',
	'securepasswords-valid' => 'Twoje hesło je njepłaćiwe abo překrótke. Dyrbi:',
	'securepasswords-minlength' => 'znajmjeńša $1 {{PLURAL:$1|znamješko|znamješce|znamješka|znamješkow}} dołhe być',
	'securepasswords-lowercase' => 'znajmjeńša 1 mały pismik wobsahować',
	'securepasswords-uppercase' => 'znajmjeńša 1 wulki pismik wobsahować',
	'securepasswords-digit' => 'znajmjeńša 1 cyfru wobsahować',
	'securepasswords-special' => 'znajmjeńša 1 specialne znamješko wobsahować (Specialne znamješka su: $1)',
	'securepasswords-username' => 'so wot twojeho wužywarskeho mjena rozeznać',
	'securepasswords-word' => 'něšto druhe być hač słowo',
);

/** Japanese (日本語)
 * @author Mizusumashi
 */
$messages['ja'] = array(
	'securepasswords-desc' => 'より安全なパスワードのハッシュを生成し、パスワード文字列チェッカーを追加する',
);

/** Korean (한국어)
 * @author Kwj2772
 */
$messages['ko'] = array(
	'securepasswords-desc' => '안전한 비밀번호 해쉬를 만들고 비밀번호 강도 검사를 실시',
	'securepasswords-valid' => '당신의 비밀번호가 잘못되었거나 너무 짧습니다.
비밀번호는 반드시:',
	'securepasswords-minlength' => '적어도 $1글자 이상이어야 합니다.',
	'securepasswords-lowercase' => '적어도 1개의 소문자가 있어야 합니다.',
	'securepasswords-uppercase' => '적어도 1개의 대문자를 포함해야 합니다.',
	'securepasswords-digit' => '적어도 1개의 숫자를 포함해야 합니다.',
	'securepasswords-special' => '적어도 1개의 특수 문자를 포함해야 합니다. (특수 문자: $1)',
	'securepasswords-username' => '당신의 계정 이름과 달라야 합니다.',
	'securepasswords-word' => '단어가 아니어야 합니다.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'securepasswords-desc' => 'Gebruikt veiliger wachtwoordhashes en voegt een wachtwoordsterktecontrole toe',
	'securepasswords-valid' => 'Uw wachtwoord voldoet niet aan de voorwaarden.
Het moet:',
	'securepasswords-minlength' => 'tenminste $1 {{PLURAL:$1|karakter|karakters}} bevatten',
	'securepasswords-lowercase' => 'tenminste 1 kleine letter bevatten',
	'securepasswords-uppercase' => 'tenminste 1 hoofdletter bevatten',
	'securepasswords-digit' => 'tenminste 1 cijfer bevatten',
	'securepasswords-special' => 'tenminste 1 speciaal karakter bevatten (speciale karakters zijn: $1)',
	'securepasswords-username' => 'verschillen van uw gebruikersnaam',
	'securepasswords-word' => 'geen woord zijn',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Harald Khan
 */
$messages['nn'] = array(
	'securepasswords-desc' => 'Opprettar meir sikre passordhashar og legg til ein funksjon for sjekking av passordstyrke',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'securepasswords-desc' => "Crèa d'hachages de senhals mai segurs e apond un verificator de complexitat de senhal",
);

/** Slovak (Slovenčina)
 * @author Helix84
 * @author Rudko
 */
$messages['sk'] = array(
	'securepasswords-desc' => 'Vytvára bezpečnejšie haše hesiel a pridáva kontrolu sily hesla',
	'securepasswords-valid' => 'Vaše heslo je nesprávne alebo príliš krátke.
Ono musí:',
	'securepasswords-username' => 'nesprávne užívateľské meno',
	'securepasswords-word' => 'to nieje slovo',
);

/** Swedish (Svenska)
 * @author Najami
 */
$messages['sv'] = array(
	'securepasswords-desc' => 'Skapar säkrare lösenordshashar och lägger till en funktion för att kontrollera lösenordets styrka',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'securepasswords-valid' => 'మీ సంకేతపదం సరైనది కాదు లేదా మరీ చిన్నగా ఉంది.
అది:',
	'securepasswords-minlength' => 'కనీసం $1 {{PLURAL:$1|అక్షరం|అక్షరాల}} పొడవుండాలి',
	'securepasswords-lowercase' => 'కనీసం ఒక్క చిన్న బడి అక్షరాన్నైనా కలిగివుండాలి.',
	'securepasswords-uppercase' => 'కనీసం ఒక్క పెద్దబడి అక్షరాన్నైనా కలిగివుండాలి.',
	'securepasswords-digit' => 'కనీసం ఒక్క అంకెనైనా కలిగివుండాలి.',
	'securepasswords-special' => 'కనీసం 1 ప్రత్యేక అక్షరాన్నైనా కలిగివుండాలి (ప్రత్యేక అక్షరాలు ఇవీ: 1)',
	'securepasswords-username' => 'మీ వాడుకరిపేరు అయివుండకూడదు',
	'securepasswords-word' => 'ఒక పదం అయివుండకూడదు',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Gzdavidwong
 */
$messages['zh-hans'] = array(
	'securepasswords-minlength' => '长度至少需要$1个字符',
	'securepasswords-lowercase' => '包含最少一个小写字母',
	'securepasswords-digit' => '包含最少一个数字',
	'securepasswords-username' => '不与您的用户名相同',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Wrightbus
 */
$messages['zh-hant'] = array(
	'securepasswords-minlength' => '長度需要最少$1個字元',
	'securepasswords-lowercase' => '包含最少1個小寫字母',
	'securepasswords-uppercase' => '包含最少1個大寫字母',
	'securepasswords-digit' => '包含最少1個數字',
	'securepasswords-username' => '不與您的使用者名稱相同',
);

