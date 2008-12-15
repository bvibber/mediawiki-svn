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

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'importusers'                 => 'Внасяне на потребители',
	'importusers-uploadfile'      => 'Качване на файл',
	'importusers-form-button'     => 'Внасяне',
	'importusers-log'             => 'Дневник на внасянията',
	'importusers-log-summary-all' => 'Всички',
	'importusers-password'        => 'парола',
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

/** French (Français)
 * @author Urhixidur
 */
$messages['fr'] = array(
	'importusers'                         => 'Importer des utilisateurs',
	'importusers-desc'                    => 'Importe des utilisateurs en bloc depuis un fichier CVS ; encodage : UTF-8.',
	'importusers-uploadfile'              => 'Importer le fichier',
	'importusers-form-caption'            => 'Entrez un fichier CVS (UTF-8)',
	'importusers-form-file'               => 'Format du fichier utilisateur (csv) : ',
	'importusers-form-replace-present'    => 'Remplace les utilisateurs existants',
	'importusers-form-button'             => 'Importer',
	'importusers-user-added'              => 'L’utilisateur <b>%s</b> a été ajouté.',
	'importusers-user-present-update'     => 'l’utilisateur <b>%s</b> existe déjà. Mise à jour effectuée.',
	'importusers-user-present-not-update' => 'L’utilisateur <b>%s</b> existe déjà. Non mis à jour.',
	'importusers-user-invalid-format'     => 'Les données utilisateur dans la ligne #%s sont dans un mauvais format ou bien sont inexistantes. Aucune action.',
	'importusers-log'                     => 'Journal des importations',
	'importusers-log-summary'             => 'Sommaire',
	'importusers-log-summary-all'         => 'Total',
	'importusers-log-summary-added'       => 'Ajouté',
	'importusers-log-summary-updated'     => 'Mise à jour',
	'importusers-login-name'              => 'Nom d’utilisateur',
	'importusers-password'                => 'mot de passe',
	'importusers-email'                   => 'adresse courriel',
	'importusers-realname'                => 'nom réel',
);

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'importusers'                   => 'Importar Usuarios',
	'importusers-uploadfile'        => 'Cargar ficheiro',
	'importusers-form-button'       => 'Importar',
	'importusers-user-added'        => 'Usuario <b>%s</b> foi engadido.',
	'importusers-log'               => 'Importar rexistro',
	'importusers-log-summary'       => 'Resumo',
	'importusers-log-summary-all'   => 'Todo',
	'importusers-log-summary-added' => 'Engadido',
	'importusers-login-name'        => 'Rexistrar nome',
	'importusers-password'          => 'contrasinal',
	'importusers-email'             => 'correo electrónico',
	'importusers-realname'          => 'nome real',
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

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'importusers-login-name' => 'ឈ្មោះ ពិនិត្យចូល',
	'importusers-email'      => 'អ៊ីមែវល៍',
	'importusers-realname'   => 'ឈ្មោះពិត',
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

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'importusers'                         => 'Importer brukere',
	'importusers-desc'                    => 'Importer brukere fra CSV-fil; tegnkoding: UTF-8',
	'importusers-uploadfile'              => 'Last opp fil',
	'importusers-form-caption'            => 'Sett inn CSV-fil (UTF-8)',
	'importusers-form-file'               => 'Brukerfilformat (csv):',
	'importusers-form-replace-present'    => 'Erstatt eksisterende brukere',
	'importusers-form-button'             => 'Importer',
	'importusers-user-added'              => 'Brukeren <b>%s</b> har blitt lagt til.',
	'importusers-user-present-update'     => 'Brukeren <b>%s</b> finnes allerede. Oppdatert.',
	'importusers-user-present-not-update' => 'Brukeren <b>%s</b> finnes allerede. Ikke oppdatert.',
	'importusers-user-invalid-format'     => 'Brukerdataene på linje #%s har ugyldig format eller er blank. Hoppet over.',
	'importusers-log'                     => 'Brukerimporteringslogg',
	'importusers-log-summary'             => 'Sammendrag',
	'importusers-log-summary-all'         => 'Alle',
	'importusers-log-summary-added'       => 'Lagt til',
	'importusers-log-summary-updated'     => 'Oppdatert',
	'importusers-login-name'              => 'Innloggingsnavn',
	'importusers-password'                => 'passord',
	'importusers-email'                   => 'e-post',
	'importusers-realname'                => 'virkelig navn',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'importusers'                         => "Importar d'utilizaires",
	'importusers-desc'                    => "Impòrta d'utilizaires en blòt dempuèi un fichièr CVS ; encodatge : UTF-8.",
	'importusers-uploadfile'              => 'Importar lo fichièr',
	'importusers-form-caption'            => 'Entratz un fichièr CVS (UTF-8)',
	'importusers-form-file'               => 'Format del fichièr utilizaire (csv) :',
	'importusers-form-replace-present'    => 'Remplaça los utilizaires existents',
	'importusers-form-button'             => 'Importar',
	'importusers-user-added'              => 'L’utilizaire <b>%s</b> es estat ajustat.',
	'importusers-user-present-update'     => 'l’utilizaire <b>%s</b> existís ja. Mesa a jorn efectuada.',
	'importusers-user-present-not-update' => 'L’utilizaire <b>%s</b> existís ja. Pas mes a jorn.',
	'importusers-user-invalid-format'     => "Las donadas d'utilizaire dins la linha #%s son dins un format marrit o alara son inexistentas. Cap d'accion.",
	'importusers-log'                     => 'Jornal dels impòrts',
	'importusers-log-summary'             => 'Somari',
	'importusers-log-summary-all'         => 'Total',
	'importusers-log-summary-added'       => 'Ajustat',
	'importusers-log-summary-updated'     => 'Mesa a jorn',
	'importusers-login-name'              => "Nom de l'escais",
	'importusers-password'                => 'Senhal',
	'importusers-email'                   => 'adreça de corrièr electronic',
	'importusers-realname'                => 'nom vertadièr',
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

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'importusers'                         => 'Импортирование участников',
	'importusers-desc'                    => 'Импорт большого количества участников из CSV-файла; кодировка: UTF-8',
	'importusers-uploadfile'              => 'Загрузить файл',
	'importusers-form-caption'            => 'Введите CVS-файл (UTF-8)',
	'importusers-form-file'               => 'Формат файла участников (csv):',
	'importusers-form-replace-present'    => 'Заменять существующих участников',
	'importusers-form-button'             => 'Импортировать',
	'importusers-user-added'              => 'Был добавлен участник <b>%s</b>.',
	'importusers-user-present-update'     => 'Участник <b>%s</b> уже существует. Обновлён.',
	'importusers-user-present-not-update' => 'Участник <b>%s</b> уже существует. Не обновлён.',
	'importusers-user-invalid-format'     => 'Данные участника в строке #%s имеют неправильный формат или пусты. Пропущен.',
	'importusers-log'                     => 'Журнал импорта',
	'importusers-log-summary'             => 'Итого',
	'importusers-log-summary-all'         => 'Всего',
	'importusers-log-summary-added'       => 'Добавлено',
	'importusers-log-summary-updated'     => 'Обновлено',
	'importusers-login-name'              => 'Имя учётной записи',
	'importusers-password'                => 'пароль',
	'importusers-email'                   => 'эл. почта',
	'importusers-realname'                => 'настоящее имя',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'importusers'                         => 'Import používateľov',
	'importusers-desc'                    => 'Hromadné importovanie používateľov z CSV súboru; kódovanie: UTF-8',
	'importusers-uploadfile'              => 'Nahrať súbor',
	'importusers-form-caption'            => 'Vstupný CSV súbor (UTF-8)',
	'importusers-form-file'               => 'Formát súboru (csv):',
	'importusers-form-replace-present'    => 'Nahradiť existujúcich používateľov',
	'importusers-form-button'             => 'Importovať',
	'importusers-user-added'              => 'Používateľ <b>%s</b> bol pridaný.',
	'importusers-user-present-update'     => 'Používateľ <b>%s</b> už existuje. Aktualizovaný.',
	'importusers-user-present-not-update' => 'Používateľ <b>%s</b> už existuje. Ponecháva sa bez aktualizácie.',
	'importusers-user-invalid-format'     => 'Údaje na riadku #%s majú neplatný formát alebo je riadok prázdny. Riadok preskočený.',
	'importusers-log'                     => 'Záznam importov',
	'importusers-log-summary'             => 'Zhrnutie',
	'importusers-log-summary-all'         => 'Všetky',
	'importusers-log-summary-added'       => 'Pridané',
	'importusers-log-summary-updated'     => 'Aktualizované',
	'importusers-login-name'              => 'Prihlasovacie meno',
	'importusers-password'                => 'heslo',
	'importusers-email'                   => 'email',
	'importusers-realname'                => 'skutočné meno',
);

/** Swedish (Svenska)
 * @author M.M.S.
 * @author Lejonel
 */
$messages['sv'] = array(
	'importusers'                     => 'Importera användare',
	'importusers-uploadfile'          => 'Ladda upp fil',
	'importusers-form-caption'        => 'Sätt in CSV-fil (UTF-8)',
	'importusers-form-button'         => 'Importera',
	'importusers-user-added'          => 'Användare <b>%s</b> hat blivigt tillagd.',
	'importusers-user-present-update' => 'Användare <b>%s</b> existerar redan. Uppdaterad.',
	'importusers-log'                 => 'Import logg',
	'importusers-log-summary'         => 'Sammanfattning',
	'importusers-log-summary-all'     => 'Alla',
	'importusers-log-summary-added'   => 'Tillagd',
	'importusers-log-summary-updated' => 'Uppdaterad',
	'importusers-login-name'          => 'Inloggningsnamn',
	'importusers-password'            => 'lösenord',
	'importusers-email'               => 'e-post',
	'importusers-realname'            => 'riktigt namn',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'importusers-user-added'      => '<b>%s</b> అనే వాడుకరిని చేర్చాం.',
	'importusers-log-summary'     => 'సంగ్రహం',
	'importusers-log-summary-all' => 'అన్నీ',
	'importusers-login-name'      => 'ప్రవేశపు పేరు',
	'importusers-password'        => 'సంకేతపదం',
	'importusers-email'           => 'ఈ-మెయిల్',
	'importusers-realname'        => 'నిజమైన పేరు',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'importusers-log-summary-all' => 'Valik',
);

