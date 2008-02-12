<?php
/**Internationalization messages file for
  *Import User extension
  *
  * @addtogroup Extensions
*/

$messages = array();

$messages['en'] = array(
	'importusers' => 'Import Users' ,
	'importusers-desc' => 'Imports users in bulk from CSV-file; encoding: UTF-8',
	'importusers-uploadfile' => 'Upload file',
	'importusers-form-caption' => 'Input CSV-file (UTF-8)' ,
	'importusers-form-file' => 'User file format (csv): ',
	'importusers-form-replace-present' => 'Replace existing users' ,
	'importusers-form-button' => 'Import' ,
	'importusers-user-added' => 'User <b>%s</b> has been added.' ,
	'importusers-user-present-update' => 'User <b>%s</b> already exists. Updated.' ,
	'importusers-user-present-not-update' => 'User <b>%s</b> already exists. Did not update.' ,
	'importusers-user-invalid-format' => 'User data in the line #%s has invalid format or is blank. Skipped.' ,
	'importusers-log' => 'Import log' ,
	'importusers-log-summary' => 'Summary' ,
	'importusers-log-summary-all' => 'All' ,
	'importusers-log-summary-added' => 'Added' ,
	'importusers-log-summary-updated' => 'Updated',
	'importusers-login-name' => 'Login name',
	'importusers-password' => 'password',
	'importusers-email' => 'email',
	'importusers-realname' => 'real name',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'importusers'                         => 'استيراد مستخدمين',
	'importusers-desc'                    => 'يستورد المستخدمين في صيغة bulk من ملف CSV؛ الإنكودينج: UTF-8',
	'importusers-uploadfile'              => 'رفع ملف',
	'importusers-form-caption'            => 'الناتج ملف CSV (UTF-8)',
	'importusers-form-file'               => 'صيغة ملف المستخدم (csv):',
	'importusers-form-replace-present'    => 'استبدل المستخدمين الموجودين',
	'importusers-form-button'             => 'استيراد',
	'importusers-user-added'              => 'المستخدم <b>%s</b> تمت إضافته.',
	'importusers-user-present-update'     => 'المستخدم <b>%s</b> موجود بالفعل. تم التحديث.',
	'importusers-user-present-not-update' => 'المستخدم <b>%s</b> موجود بالفعل. لم يتم التحديث.',
	'importusers-user-invalid-format'     => 'بيانات المستخدم في السطر #%s لها صيغة غير صحيحة أو فارغة. تم تجاهلها.',
	'importusers-log'                     => 'استيراد السجل',
	'importusers-log-summary'             => 'ملخص',
	'importusers-log-summary-all'         => 'الكل',
	'importusers-log-summary-added'       => 'تمت الإضافة',
	'importusers-log-summary-updated'     => 'تم التحديث',
	'importusers-login-name'              => 'اسم الدخول',
	'importusers-password'                => 'كلمة السر',
	'importusers-email'                   => 'البريد الإلكتروني',
	'importusers-realname'                => 'الاسم الحقيقي',
);

/** German (Deutsch)
 * @author MF-Warburg
 */
$messages['de'] = array(
	'importusers'                         => 'Benutzer importieren',
	'importusers-desc'                    => 'Importiert Benutzer aus einer CSV-Datei; Codierung: UTF-8',
	'importusers-uploadfile'              => 'Datei hochladen',
	'importusers-form-caption'            => 'CSV-Datei (UTF-8)',
	'importusers-form-file'               => 'Benutzerdateiformat (csv):',
	'importusers-form-replace-present'    => 'Bestehende Benutzer ersetzen',
	'importusers-form-button'             => 'Importieren',
	'importusers-user-added'              => 'Der Benutzer <b>%s</b> wurde importiert.',
	'importusers-user-present-update'     => 'Ein Benutzer <b>%s</b> existiert bereits. Aktualisiert.',
	'importusers-user-present-not-update' => 'Ein Benutzer <b>%s</b> existiert bereits. Nicht aktualisiert.',
	'importusers-user-invalid-format'     => 'Die Benutzerdaten in Zeile #%s haben ein ungültiges Format oder sind leer. Übersprungen.',
	'importusers-log'                     => 'Benutzerimport-Logbuch',
	'importusers-log-summary'             => 'Zusammenfassung',
	'importusers-log-summary-all'         => 'Alle',
	'importusers-log-summary-added'       => 'Hinzugefügt',
	'importusers-log-summary-updated'     => 'Aktualisiert',
	'importusers-login-name'              => 'Benutzername',
	'importusers-password'                => 'Passwort',
	'importusers-email'                   => 'E-Mail',
	'importusers-realname'                => 'Echter Name',
);

$messages['fr'] = array(
	'importusers' => 'Importer des utilisateurs' ,
	'importusers-desc' => 'Importe des utilisateurs en bloc depuis un fichier CVS ; encodage : UTF-8.',
	'importusers-uploadfile' => 'Importer le fichier',
	'importusers-form-caption' => 'Entrez un fichier CVS (UTF-8)' ,
	'importusers-form-file' => 'Format du fichier utilisateur (csv) : ',
	'importusers-form-replace-present' => 'Remplace les utilisateurs existants' ,
	'importusers-form-button' => 'Importer' ,
	'importusers-user-added' => 'L’utilisateur <b>%s</b> a été ajouté.' ,
	'importusers-user-present-update' => 'l’utilisateur <b>%s</b> existe déjà. Mise à jour effectuée.' ,
	'importusers-user-present-not-update' => 'L’utilisateur <b>%s</b> existe déjà. Non mis à jour.' ,
	'importusers-user-invalid-format' => 'Les données utilisateur dans la ligne #%s sont dans un mauvais format ou bien sont inexistantes. Aucune action.' ,
	'importusers-log' => 'Journal des imports' ,
	'importusers-log-summary' => 'Sommaire' ,
	'importusers-log-summary-all' => 'Total' ,
	'importusers-log-summary-added' => 'Ajouté' ,
	'importusers-log-summary-updated' => 'Mise à jour',
	'importusers-login-name' => 'Nom du pseudo',
	'importusers-password' => 'mot de passe',
	'importusers-email' => 'adresse courriel',
	'importusers-realname' => 'nom réel',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'importusers'                         => 'Wužiwarjow importować',
	'importusers-desc'                    => 'Wužiwarjow we wulkich mnóstwach z CSV-dataje importować; kodowanje: UTF-8',
	'importusers-uploadfile'              => 'Dataju nahrać',
	'importusers-form-caption'            => 'CSV-dataju (UTF-8) zapodać',
	'importusers-form-file'               => 'Format wužiwarskeje dataje (csv):',
	'importusers-form-replace-present'    => 'Eksistowacych wužiwarjow narunać',
	'importusers-form-button'             => 'Importować',
	'importusers-user-added'              => 'Wužiwar <b>%s</b> je so přidał.',
	'importusers-user-present-update'     => 'Wužiwar <b>%s</b> hižo eksistuje. Zaktualizowany.',
	'importusers-user-present-not-update' => 'Wužiwar <b>%s</b> hižo eksistuje. Žana aktualizacija.',
	'importusers-user-invalid-format'     => 'Wužiwarske daty w lince #%s ma njepłaćiwy format abo su prózdne. Přeskočene.',
	'importusers-log'                     => 'Importowy protokol',
	'importusers-log-summary'             => 'Zjeće',
	'importusers-log-summary-all'         => 'Wšě',
	'importusers-log-summary-added'       => 'Přidaty',
	'importusers-log-summary-updated'     => 'Zaktualizowany.',
	'importusers-login-name'              => 'Přizjewjenske mjeno',
	'importusers-password'                => 'hesło',
	'importusers-email'                   => 'e-mejl',
	'importusers-realname'                => 'woprawdźite mjeno',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'importusers'                      => 'Benotzer importéieren',
	'importusers-uploadfile'           => 'Fichier eroplueden',
	'importusers-form-replace-present' => 'Benotzer déi et scho gëtt ersetzen',
	'importusers-form-button'          => 'Importéieren',
	'importusers-log'                  => 'Lëscht vun den Importen',
	'importusers-log-summary'          => 'Resumé',
	'importusers-log-summary-all'      => 'Alleguer',
	'importusers-log-summary-added'    => 'derbäigesat',
	'importusers-log-summary-updated'  => 'Aktualiséiert',
	'importusers-password'             => 'Passwuert',
	'importusers-email'                => 'E-Mailadress',
	'importusers-realname'             => 'richtege Numm',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'importusers'                         => 'Gebruikers importeren',
	'importusers-desc'                    => 'Gebruikers in bulk importeren vanuit een CSV-bestand. Codering: UTF-8',
	'importusers-uploadfile'              => 'Bestand uploaden',
	'importusers-form-caption'            => 'Invoerbestand (CSV, UTF-8)',
	'importusers-form-file'               => 'Gebruikersbestandsopmaak (csv):',
	'importusers-form-replace-present'    => 'Bestaande gebruikers vervangen',
	'importusers-form-button'             => 'Importeren',
	'importusers-user-added'              => 'Gebruiker <b>%s</b> is toegevoegd.',
	'importusers-user-present-update'     => 'Gebruiker <b>%s</b> bestaat al. Bijgewerkt.',
	'importusers-user-present-not-update' => 'Gebruiker <b>%s</b> bestaat al. Niet bijgewerkt.',
	'importusers-user-invalid-format'     => 'De gebruikersgegevens in de regel #%s hebben een ongeldige opmaak of zijn leeg. Overgeslagen.',
	'importusers-log'                     => 'Importlogboek',
	'importusers-log-summary'             => 'Samenvatting',
	'importusers-log-summary-all'         => 'Alle',
	'importusers-log-summary-added'       => 'Toegevoegd',
	'importusers-log-summary-updated'     => 'Bijgewerkt',
	'importusers-login-name'              => 'Gebruikersnaam',
	'importusers-password'                => 'wachtwoord',
	'importusers-email'                   => 'e-mail',
	'importusers-realname'                => 'echte naam',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'importusers-log-summary'     => 'لنډيز',
	'importusers-log-summary-all' => 'ټول',
	'importusers-password'        => 'پټنوم',
	'importusers-email'           => 'برېښناليک',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'importusers'                         => 'Importar Utilizadores',
	'importusers-desc'                    => 'Importa utilizadores em bloco de um ficheiro CSV; codificação: UTF-8',
	'importusers-uploadfile'              => 'Carregar ficheiro',
	'importusers-form-caption'            => 'Ficheiro CSV de entrada (UTF-8)',
	'importusers-form-file'               => 'Formato do ficheiro de utilizadores (csv):',
	'importusers-form-replace-present'    => 'Substituir utilizadores existentes',
	'importusers-form-button'             => 'Importar',
	'importusers-user-added'              => 'Utilizador <b>%s</b> foi adicionado.',
	'importusers-user-present-update'     => 'Utilizador <b>%s</b> já existe. Actualizado.',
	'importusers-user-present-not-update' => 'Utilizador <b>%s</b> já existe. Não foi actualizado.',
	'importusers-user-invalid-format'     => 'Dados de utilizador na linha #%s têm um formato inválido ou estão vazios. Passado à frente.',
	'importusers-log'                     => 'Registo de importação',
	'importusers-log-summary'             => 'Sumário',
	'importusers-log-summary-all'         => 'Todos',
	'importusers-log-summary-added'       => 'Adicionado',
	'importusers-log-summary-updated'     => 'Actualizado',
	'importusers-login-name'              => 'Nome de conta',
	'importusers-password'                => 'palavra-chave',
	'importusers-email'                   => 'email',
	'importusers-realname'                => 'nome real',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Lejonel
 */
$messages['sv'] = array(
	'importusers'                     => 'Importera användare',
	'importusers-uploadfile'          => 'Ladda upp fil',
	'importusers-form-button'         => 'Importera',
	'importusers-log'                 => 'Import logg',
	'importusers-log-summary'         => 'Sammanfattning',
	'importusers-log-summary-all'     => 'Alla',
	'importusers-log-summary-added'   => 'Tillagd',
	'importusers-log-summary-updated' => 'Uppdaterad',
	'importusers-login-name'          => 'Inloggningsnamn',
	'importusers-password'            => 'lösenord',
	'importusers-email'               => 'e-post',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'importusers-log-summary-all' => 'Valik',
);

