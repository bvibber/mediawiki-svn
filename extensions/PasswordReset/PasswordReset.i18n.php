<?php
#coding: utf-8
/** \file
* \brief Internationalization file for the Password Reset Extension.
*/

$messages = array();

$messages['en'] = array(
	'passwordreset' => 'Password Reset',
	'passwordreset' => "Resets Wiki user's passwords - requires 'passwordreset' privileges",
	'passwordreset-invalidusername' => 'Invalid Username',
	'passwordreset-emptyusername' => 'Empty Username',
	'passwordreset-nopassmatch' => 'Passwords do not match',
	'passwordreset-badtoken' => 'Invalid Edit Token',
	'passwordreset-username' => 'Username',
	'passwordreset-newpass' => 'New Password',
	'passwordreset-confirmpass' => 'Confirm Password',
	'passwordreset-submit' => 'Reset Password',
	'passwordreset-success' => 'Password has been reset for user_id: $1',
	'passwordreset-disableuser' => 'Disable User Account?',
	'passwordreset-disableuserexplain' => '(sets an invalid password hash - user can\'t login)',
	'passwordreset-disablesuccess' => 'User account has been disabled (user_id: $1)',
	'passwordreset-accountdisabled' => 'Account has been disabled',
	'disabledusers' => 'Disabled users',
	'disabledusers-summary' => 'This is a list of users that have been disabled via PasswordReset.',	
);

$messages['af'] = array(
	'passwordreset-username' => 'Gebruikersnaam',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'passwordreset'                    => 'تمت إعادة ضبط كلمة السر',
	'passwordreset-invalidusername'    => 'اسم مستخدم غير صحيح',
	'passwordreset-emptyusername'      => 'اسم مستخدم فارغ',
	'passwordreset-nopassmatch'        => 'كلمات السر لا تتطابق',
	'passwordreset-badtoken'           => 'نص تعديل غير صحيح',
	'passwordreset-username'           => 'اسم مستخدم',
	'passwordreset-newpass'            => 'كلمة سر جديدة',
	'passwordreset-confirmpass'        => 'أكد كلمة السر',
	'passwordreset-submit'             => 'أعد ضبط كلمة السر',
	'passwordreset-success'            => 'كلمة السر تم ضبطها ل user_id: $1',
	'passwordreset-disableuser'        => 'عطل حساب المستخدم؟',
	'passwordreset-disableuserexplain' => '(يضبط هاش كلمة سر غير صحيح - المستخدم لا يمكنه الدخول)',
	'passwordreset-disablesuccess'     => 'حساب المستخدم تم تعطيله (رقم_المستخدم: $1)',
	'passwordreset-accountdisabled'    => 'الحساب تم تعطيله',
	'disabledusers'                    => 'مستخدمون معطلون',
	'disabledusers-summary'            => 'هذه قائمة بالمستخدمين الذين تم تعطيلهم من خلال إعادة ضبط كلمة السر.',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'passwordreset-invalidusername' => 'Невалидно потребителско име',
	'passwordreset-emptyusername'   => 'Празно потребителско име',
	'passwordreset-nopassmatch'     => 'Паролите не съвпадат',
	'passwordreset-username'        => 'Потребителско име',
	'passwordreset-newpass'         => 'Нова парола',
	'passwordreset-confirmpass'     => 'Парола (повторно)',
);

/** German (Deutsch)
 * @author Raimond Spekking
 */
$messages['de'] = array(
	'passwordreset'                    => 'Passwort zurücksetzen',
	'passwordreset'                    => "Zurücksetzen eines Benutzer-Passwortes - ''passwordreset''-Recht notwendig",
	'passwordreset-invalidusername'    => 'Ungültiger Benutzername',
	'passwordreset-emptyusername'      => 'Leerer Benutzername',
	'passwordreset-nopassmatch'        => 'Passwörter stimmen nicht überein',
	'passwordreset-badtoken'           => 'Ungültiger „Edit Token“',
	'passwordreset-username'           => 'Benutzername',
	'passwordreset-newpass'            => 'Neues Passwort',
	'passwordreset-confirmpass'        => 'Passwort bestätigen',
	'passwordreset-submit'             => 'Passwort zurücksetzen',
	'passwordreset-success'            => 'Passwort für Benutzer-ID $1 wurde zurückgesetzt',
	'passwordreset-disableuser'        => 'Benutzerkonto deaktivieren?',
	'passwordreset-disableuserexplain' => '(setzen eines ungültigen Passwort-Hashs - Anmelden unmöglich)',
	'passwordreset-disablesuccess'     => 'Benutzerkonto wurde deaktiviert (Benutzer-ID: $1)',
	'passwordreset-accountdisabled'    => 'Benutzerkonto ist deaktiviert',
	'disabledusers'                    => 'Deaktivierte Benutzerkonten',
	'disabledusers-summary'            => 'Dies ist die Liste der deaktivierten Benutzerkonten (via PasswordReset).',	
);

$messages['el'] = array(
	'passwordreset' => 'Κωδικός επαναφοράς',
	'passwordreset-invalidusername' => 'Άκυρο όνομα χρήστη',
	'passwordreset-emptyusername' => 'Κενό όνομα χρήστη',
	'passwordreset-nopassmatch' => 'Οι Κωδικοί δεν αντιστοιχούν',
	'passwordreset-username' => 'Όνομα χρήστη',
	'passwordreset-newpass' => 'Νέος Κωδικός',
	'passwordreset-confirmpass' => 'Επιβεβαιώστε τον κωδικό πρόσβασης',
	'passwordreset-submit' => 'Επαναφορά κωδικού',
	'passwordreset-success' => 'Ο κωδικός έχει επαναφερθεί για τον user_id: $1',
);

/** French (Français)
 * @author Sherbrooke
 * @author Dereckson
 * @author Urhixidur
 * @author Grondin
 */
$messages['fr'] = array(
	'passwordreset'                    => 'Remise à zéro du mot de passe',
	'passwordreset-invalidusername'    => "Nom d'usager inconnu",
	'passwordreset-emptyusername'      => "Nom d'usager vide",
	'passwordreset-nopassmatch'        => 'Les mots de passe que vous avez saisis ne sont pas identiques.',
	'passwordreset-badtoken'           => 'Jeton de modification inconnu',
	'passwordreset-username'           => "Nom d'usager",
	'passwordreset-newpass'            => 'Nouveau mot de passe',
	'passwordreset-confirmpass'        => 'Confirmez le mot de passe',
	'passwordreset-submit'             => 'Remise à zéro du mot de passe',
	'passwordreset-success'            => 'Le mot de passe a été remis à zéro pour l’usager $1.',
	'passwordreset-disableuser'        => 'Désactiver le compte utilisateur ?',
	'passwordreset-disableuserexplain' => '(spécifie un hachage de mot de passe invalide - l’utilisateur ne pourra pas se connecter)',
	'passwordreset-disablesuccess'     => 'Compte utilisateur désactivé (user_id : $1)',
	'passwordreset-accountdisabled'    => 'Ce compte a été désactivé.',
	'disabledusers'                    => 'Utilisateurs désactivés',
	'disabledusers-summary'            => 'Ceci est la liste des utilisateurs qui ont été désactivés par PasswordReset.',
);

/** Galician (Galego)
 * @author Xosé
 * @author Toliño
 * @author Alma
 */
$messages['gl'] = array(
	'passwordreset'                 => 'Eliminar o contrasinal',
	'passwordreset-invalidusername' => 'Nome de usuario non válido',
	'passwordreset-emptyusername'   => 'Nome de usuario baleiro',
	'passwordreset-nopassmatch'     => 'Os contrasinais non coinciden',
	'passwordreset-username'        => 'Nome de usuario',
	'passwordreset-newpass'         => 'Contrasinal Novo',
	'passwordreset-confirmpass'     => 'Confirme o Contrasinal',
	'passwordreset-submit'          => 'Limpar o Contrasinal',
	'passwordreset-success'         => 'Limpouse o contrasinal para o id de usuario: $1',
	'passwordreset-disableuser'     => 'Desactivar a Conta de Usuario?',
	'passwordreset-disablesuccess'  => 'Desactivouse a conta do usuario (user_id: $1)',
	'passwordreset-accountdisabled' => 'A conta foi desabilitada',
	'disabledusers'                 => 'Usuarios desabilitados',
	'disabledusers-summary'         => 'Esta é unha listaxe dos usuarios que teñen desabilitada a vía PasswordReset.',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'passwordreset'                    => 'Hesło wróćo stajić',
	'passwordreset-invalidusername'    => 'Njepłaćiwe wužiwarske mjeno',
	'passwordreset-emptyusername'      => 'Žane wužiwarske mjeno',
	'passwordreset-nopassmatch'        => 'Hesle njerunatej so',
	'passwordreset-badtoken'           => 'Njepłaćiwe wobdźěłanske znamjo',
	'passwordreset-username'           => 'Wužiwarske mjeno',
	'passwordreset-newpass'            => 'Nowe hesło',
	'passwordreset-confirmpass'        => 'Hesło wobkrućić',
	'passwordreset-submit'             => 'Hesło wróćo stajić',
	'passwordreset-success'            => 'Hesło bu za wužiwarski ID $1 wróćo stajene.',
	'passwordreset-disableuser'        => 'Wužiwarske konto znjemóžnić?',
	'passwordreset-disableuserexplain' => '(nastaja njepłaćiwy hesłowy šmjat - wužiwar njemóže so přizjewić)',
	'passwordreset-disablesuccess'     => 'Wužiwarske konto bu znjemóžnjene (wužiwarski_id: $1)',
	'passwordreset-accountdisabled'    => 'Konto bu znjemóžnjene',
	'disabledusers'                    => 'Znjemóžnene wužiwarske konta',
	'disabledusers-summary'            => 'To je lisćina wužiwarskich kontow, kotrež buchu přez PasswordReset znjemóžnjene.',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'passwordreset'                    => 'Jelszó beállítása',
	'passwordreset-invalidusername'    => 'Érvénytelen felhasználói név',
	'passwordreset-emptyusername'      => 'Nincs megadva felhasználói név',
	'passwordreset-nopassmatch'        => 'A jelszavak nem egyeznek meg',
	'passwordreset-badtoken'           => 'Hibás szerkesztési token',
	'passwordreset-username'           => 'Felhasználói név',
	'passwordreset-newpass'            => 'Új jelszó',
	'passwordreset-confirmpass'        => 'Jelszó megerősítése',
	'passwordreset-submit'             => 'Jelszó visszaálíltása',
	'passwordreset-success'            => 'A(z) $1 azonosítószámú felhasználó jelszava be lett állítva',
	'passwordreset-disableuser'        => 'Felhasználói fiók letiltása?',
	'passwordreset-disableuserexplain' => '(egy érvénytelen hasht állít be jelszónak, így a felhasználó nem tud bejelentkezni)',
	'passwordreset-disablesuccess'     => 'A felhasználói fiók le lett tiltva (azonosító: $1)',
	'passwordreset-accountdisabled'    => 'A felhasználói fiók le lett tiltva',
);

$messages['ie'] = array(
	'passwordreset-username' => 'Vor nómine usatori',
	'passwordreset-newpass' => 'Nov passa-parol',
);

$messages['io'] = array(
	'passwordreset-username' => 'Uzantonomo',
	'passwordreset-newpass' => 'Nova Kontrolajo',
);

/** Icelandic (Íslenska)
 * @author SPQRobin
 */
$messages['is'] = array(
	'passwordreset-username' => 'Notandanafn',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'passwordreset-invalidusername' => 'ឈ្មោះអ្នកប្រើប្រាស់ គ្មានសុពលភាព',
	'passwordreset-emptyusername'   => 'ឈ្មោះអ្នកប្រើប្រាស់ ទទេ',
	'passwordreset-username'        => 'ឈ្មោះអ្នកប្រើប្រាស់',
	'passwordreset-confirmpass'     => 'បញ្ជាក់ទទួលស្គាល់ ពាក្យសំងាត់',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'passwordreset-emptyusername' => 'Eidele Benotzernumm',
	'passwordreset-nopassmatch'   => 'Déi Passwierder déi Dir aginn hutt sinn net identesch',
	'passwordreset-username'      => 'Benotzernumm',
	'passwordreset-newpass'       => 'Neit Passwuert',
	'passwordreset-confirmpass'   => 'Passwuert confirméieren',
	'passwordreset-submit'        => 'Passwuert zrécksetzen',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'passwordreset'                    => 'Wachtwoord opnieuw instellen',
	'passwordreset-invalidusername'    => 'Onjuiste gebruiker',
	'passwordreset-emptyusername'      => 'Gebruiker niet ingegeven',
	'passwordreset-nopassmatch'        => 'De wachtwoorden komen niet overeen',
	'passwordreset-badtoken'           => 'Ongeldig bewerkingstoken',
	'passwordreset-username'           => 'Gebruiker',
	'passwordreset-newpass'            => 'Nieuw wachtwoord',
	'passwordreset-confirmpass'        => 'Bevestig wachtwoord',
	'passwordreset-submit'             => 'Wachtwoord opnieuw instellen',
	'passwordreset-success'            => 'Wachtwoord voor gebruikers-ID $1 is opnieuw ingesteld',
	'passwordreset-disableuser'        => 'Gebruiker deactiveren?',
	'passwordreset-disableuserexplain' => '(stelt een onjuiste wachtwoordhash in - gebruik kan niet aanmelden)',
	'passwordreset-disablesuccess'     => 'Gebruik is gedeactiveerd (gebruikers-ID: $1)',
	'passwordreset-accountdisabled'    => 'Gebruiker is gedeactiveerd',
	'disabledusers'                    => 'Gedeactiveerde gebruikers',
	'disabledusers-summary'            => 'Dit is een lijst van gebruikers die zijn gedeactiveerd via PasswordReset',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'passwordreset'                    => 'Passordresetting',
	'passwordreset-invalidusername'    => 'Ugyldig brukernavn',
	'passwordreset-emptyusername'      => 'Tomt brukernavn',
	'passwordreset-nopassmatch'        => 'Passordene er ikke de samme',
	'passwordreset-badtoken'           => 'Ugyldig redigeringstegn',
	'passwordreset-username'           => 'Brukernavn',
	'passwordreset-newpass'            => 'Nytt passord',
	'passwordreset-confirmpass'        => 'Bekreft passord',
	'passwordreset-submit'             => 'Resett passord',
	'passwordreset-success'            => 'Passordet for brukeren «$1» har blitt resatt.',
	'passwordreset-disableuser'        => 'Deaktiver kontoen?',
	'passwordreset-disableuserexplain' => '(setter et ugyldig passord – brukeren kan ikke logge inn)',
	'passwordreset-disablesuccess'     => 'Kontoen er blitt deaktivert (bruker-ID: $1)',
	'passwordreset-accountdisabled'    => 'Kontoen er blitt deaktivert',
	'disabledusers'                    => 'Deaktiverte kontoer',
	'disabledusers-summary'            => 'Dette er en liste over kontoer som har blitt deaktiverte via passordresetting.',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'passwordreset'                    => 'Remesa a zèro del senhal',
	'passwordreset-invalidusername'    => "Nom d'utilizaire desconegut",
	'passwordreset-emptyusername'      => "Nom d'utilizaire void",
	'passwordreset-nopassmatch'        => "Los senhals qu'avètz picats son pas identics.",
	'passwordreset-badtoken'           => 'Token de modificacion desconegut',
	'passwordreset-username'           => "Nom d'utilizaire",
	'passwordreset-newpass'            => 'Senhal novèl',
	'passwordreset-confirmpass'        => 'Confirmatz lo senhal',
	'passwordreset-submit'             => 'Remesa a zèro del senhal',
	'passwordreset-success'            => "Lo senhal es estat remés a zèro per lo ''user_id'' $1.",
	'passwordreset-disableuser'        => "Desactivar lo compte d'utilizaire ?",
	'passwordreset-disableuserexplain' => "(règla un hash de senhal pas valid - l'utilizaire pòt pas se connectar)",
	'passwordreset-disablesuccess'     => "Compte d'utilizaire desactivat (user_id : $1)",
	'passwordreset-accountdisabled'    => 'Aqueste compte es estat desactivat.',
	'disabledusers'                    => 'Utilizaires desactivats',
	'disabledusers-summary'            => 'Aquò es la tièra dels utilizaires que son estats desactivats per PasswordReset.',
);

$messages['pl'] = array(
	'passwordreset' => 'Wyczyszczenie hasła',
	'passwordreset-invalidusername' => 'Nieprawidłowa nazwa użytkownika',
	'passwordreset-emptyusername' => 'Pusta nazwa użytkownika',
	'passwordreset-nopassmatch' => 'Hasła nie pasują',
	'passwordreset-badtoken' => 'Nieprawidłowy żeton edycji',
	'passwordreset-username' => 'Nazwa użytkownika',
	'passwordreset-newpass' => 'Nowe hasło',
	'passwordreset-confirmpass' => 'Potwierdź hasło',
	'passwordreset-submit' => 'Wyczyść hasło',
	'passwordreset-success' => 'Hasło zostało wyczyszczone dla użytkownika od ID $1',
);


$messages['pms'] = array(
	'passwordreset' => 'Cambi ëd ciav',
	'passwordreset-invalidusername' => 'Stranòm nen giust',
	'passwordreset-emptyusername' => 'Stranòm veujd',
	'passwordreset-nopassmatch' => 'Le doe ciav a son pa mideme',
	'passwordreset-badtoken' => 'Còdes ëd modìfica nen bon',
	'passwordreset-username' => 'Stranòm',
	'passwordreset-newpass' => 'Ciav neuva',
	'passwordreset-confirmpass' => 'Confermè la ciav',
	'passwordreset-submit' => 'Cambié la ciav',
	'passwordreset-success' => 'La ciav ëd l\'utent $1 a l\'é staita cambià',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'passwordreset-invalidusername' => 'ناسم کارن-نوم',
	'passwordreset-emptyusername'   => 'تش کارن-نوم',
	'passwordreset-nopassmatch'     => 'پټنومونه مو کټ مټ د يو بل سره سمون نه خوري',
	'passwordreset-username'        => 'کارن-نوم',
	'passwordreset-newpass'         => 'نوی پټنوم',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'passwordreset'                 => 'Repor Palavra-Chave',
	'passwordreset-invalidusername' => 'Nome de Utilizador Inválido',
	'passwordreset-emptyusername'   => 'Nome de Utilizador Vazio',
	'passwordreset-nopassmatch'     => 'Palavras-Chave não coincidem',
	'passwordreset-username'        => 'Nome de utilizador',
	'passwordreset-newpass'         => 'Nova Palavra-Chave',
	'passwordreset-confirmpass'     => 'Confirme Palavra-Chave',
	'passwordreset-submit'          => 'Repor Palavra-Chave',
	'passwordreset-disableuser'     => 'Desactivar Conta de Utilizador?',
	'passwordreset-accountdisabled' => 'A conta foi desactivada',
	'disabledusers'                 => 'Utilizadores desactivados',
);

$messages['rm'] = array(
	'passwordreset-username' => 'Num d\'utilisader',
);

/** Russian (Русский)
 * @author Illusion
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'passwordreset'                    => 'Сброс пароля',
	'passwordreset-invalidusername'    => 'Недопустимое имя участника',
	'passwordreset-emptyusername'      => 'Пустое имя участника',
	'passwordreset-nopassmatch'        => 'Пароли не совпадают',
	'passwordreset-badtoken'           => 'Ошибочный признак правки',
	'passwordreset-username'           => 'Имя участника',
	'passwordreset-newpass'            => 'Новый пароль',
	'passwordreset-confirmpass'        => 'Подтверждение пароля',
	'passwordreset-submit'             => 'Сбросить пароль',
	'passwordreset-success'            => 'Пароль сброшен для user_id: $1',
	'passwordreset-disableuser'        => 'Отключить учётную запись?',
	'passwordreset-disableuserexplain' => '(установлен неверный хеш пароля — участник не может зайти)',
	'passwordreset-disablesuccess'     => 'Учётная запись отключена (user_id: $1)',
	'passwordreset-accountdisabled'    => 'Учётная запись отключена',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'passwordreset'                    => 'Reset hesla',
	'passwordreset-invalidusername'    => 'Neplatné používateľské meno',
	'passwordreset-emptyusername'      => 'Nevyplnené používateľské meno',
	'passwordreset-nopassmatch'        => 'Heslá sa nezhodujú',
	'passwordreset-badtoken'           => 'Neplatný upravovací token',
	'passwordreset-username'           => 'Používateľské meno',
	'passwordreset-newpass'            => 'Nové heslo',
	'passwordreset-confirmpass'        => 'Potvrdiť heslo',
	'passwordreset-submit'             => 'Resetovať heslo',
	'passwordreset-success'            => 'Heslo používateľa s user_id $1 bolo resetované',
	'passwordreset-disableuser'        => 'Zablokovať používateľský účet?',
	'passwordreset-disableuserexplain' => '(nastaví neplatnú haš hodnotu hesla - používateľ sa nebude môcť prihlásiť)',
	'passwordreset-disablesuccess'     => 'Používateľský účet bol zablokovaný (user_id: $1)',
	'passwordreset-accountdisabled'    => 'Účet bol zablokovaný',
	'disabledusers'                    => 'Vypnutí používatelia',
	'disabledusers-summary'            => 'Toto je zoznam používateľov, ktorí boli vypnutí prostredníctvom PasswordReset.',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'passwordreset'                    => 'Paaswoud touräächsätte',
	'passwordreset-invalidusername'    => 'Uungultigen Benutsernoome',
	'passwordreset-emptyusername'      => 'Loosen Benutsernoome',
	'passwordreset-nopassmatch'        => 'Paaswoude stimme nit uureen',
	'passwordreset-badtoken'           => 'Ungultigen „Edit Token“',
	'passwordreset-username'           => 'Benutsernoome',
	'passwordreset-newpass'            => 'Näi Paaswoud',
	'passwordreset-confirmpass'        => 'Paaswoud bestäätigje',
	'passwordreset-submit'             => 'Paaswoud touräächsätte',
	'passwordreset-success'            => 'Paaswoud foar Benutser-ID $1 wuude touräächsät',
	'passwordreset-disableuser'        => 'Benutserkonto deaktivierje?',
	'passwordreset-disableuserexplain' => '(sät n uungultich Paaswoud-Hash - Anmäldjen uunmuugelk)',
	'passwordreset-disablesuccess'     => 'Benutserkonto wuude deaktivierd (Benutser-ID: $1)',
	'passwordreset-accountdisabled'    => 'Benutserkonto is deaktivierd',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Max sonnelid
 */
$messages['sv'] = array(
	'passwordreset-invalidusername' => 'Ogiltigt användarnamn',
	'passwordreset-emptyusername'   => 'Tomt användarnamn',
	'passwordreset-nopassmatch'     => 'Lösenordet matchar inte',
	'passwordreset-username'        => 'Användarnamn',
	'passwordreset-newpass'         => 'Nytt lösenord',
	'passwordreset-confirmpass'     => 'Konfirmera lösenord',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'passwordreset-invalidusername' => 'తప్పుడు వాడుకరిపేరు',
	'passwordreset-emptyusername'   => 'ఖాళీ వాడుకరి పేరు',
	'passwordreset-nopassmatch'     => 'సంకేతపదాలు సరిపోలలేదు',
	'passwordreset-username'        => 'వాడుకరిపేరు',
	'passwordreset-newpass'         => 'కొత్త సంకేతపదం',
	'passwordreset-confirmpass'     => 'సంకేతపదాన్ని నిర్ధారించండి',
	'passwordreset-disableuser'     => 'వాడుకరి ఖాతాని అచేతనం చేయాలా?',
	'passwordreset-accountdisabled' => 'ఖాతాని అచేతనం చేసారు',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'passwordreset-username' => 'Gebananem',
);

$messages['yue'] = array(
	'passwordreset' => '密碼重設',
	'passwordreset-invalidusername' => '無效嘅用戶名',
	'passwordreset-emptyusername' => '空白嘅用戶名',
	'passwordreset-nopassmatch' => '密碼唔對',
	'passwordreset-badtoken' => '無效嘅編輯幣',
	'passwordreset-username' => '用戶名',
	'passwordreset-newpass' => '新密碼',
	'passwordreset-confirmpass' => '確認新密碼',
	'passwordreset-submit' => '重設密碼',
	'passwordreset-success' => 'User_id: $1 嘅密碼已經重設咗'
);

$messages['zh-hans'] = array(
	'passwordreset' => '密码重设',
	'passwordreset-invalidusername' => '无效的用户名',
	'passwordreset-emptyusername' => '空白的用户名',
	'passwordreset-nopassmatch' => '密码不匹配',
	'passwordreset-badtoken' => '无效的编辑币',
	'passwordreset-username' => '用户名',
	'passwordreset-newpass' => '新密码',
	'passwordreset-confirmpass' => '确认新密码',
	'passwordreset-submit' => '重设密码',
	'passwordreset-success' => 'User_id: $1 的密码已经重设'
);

$messages['zh-hant'] = array(
	'passwordreset' => '密碼重設',
	'passwordreset-invalidusername' => '無效的用戶名',
	'passwordreset-emptyusername' => '空白的用戶名',
	'passwordreset-nopassmatch' => '密碼不匹配',
	'passwordreset-badtoken' => '無效的編輯幣',
	'passwordreset-username' => '用戶名',
	'passwordreset-newpass' => '新密碼',
	'passwordreset-confirmpass' => '確認新密碼',
	'passwordreset-submit' => '重設密碼',
	'passwordreset-success' => 'User_id: $1 的密碼已經重設'
);

