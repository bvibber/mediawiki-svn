<?php
/*
 * Internationalization for NssMySQLAuth extension.
 */

$messages = array();

/**
 * English
 * @author Bryan Tong Minh
 */
$messages['en'] = array(
	'accountmanager' => 'Account manager',
	
	'am-username' 	=> 'username',
	'am-email' => 'e-mail',
	'am-active' 	=> 'active',
	'am-updated' => 'Your changes have been saved successfully',

	'nss-desc' => 'A plugin to authenticate against a libnss-mysql database. Contains an [[Special:AccountManager|account manager]]',
	'nss-rights'	=>  'rights',
	'nss-save-changes'	=> 'Save changes',
	'nss-create-account-header'	=> 'Create new account',
	'nss-create-account'	=> 'Create account',
	'nss-no-mail'	=> 'Do not send email',
	'nss-welcome-mail'	=> 'An account with username $1 and password $2 has been created for you.',
	'nss-welcome-mail-subject' => 'Account creation',
	
	'nss-db-error' => 'Error reading from authentication database'
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'accountmanager' => 'مدير الحساب',
	'am-username' => 'اسم المستخدم',
	'am-email' => 'البريد الإلكتروني',
	'am-active' => 'نشط',
	'am-updated' => 'تغييراتك تم حفظها بنجاح',
	'nss-desc' => 'إضافة للتحقق ضد قاعدة بيانات libnss-mysql. يحتوي على [[Special:AccountManager|مدير حساب]]',
	'nss-rights' => 'صلاحيات',
	'nss-save-changes' => 'حفظ التغييرات',
	'nss-create-account-header' => 'إنشاء حساب جديد',
	'nss-create-account' => 'إنشاء الحساب',
	'nss-welcome-mail' => 'الحساب باسم المستخدم $1 وكلمة السر $2 تم إنشاؤه من أجلك.',
	'nss-welcome-mail-subject' => 'إنشاء الحساب',
	'nss-db-error' => 'خطأ قراءة من قاعدة بيانات التحقق.',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Meno25
 */
$messages['arz'] = array(
	'accountmanager' => 'مدير الحساب',
	'am-username' => 'اسم المستخدم',
	'am-email' => 'البريد الإلكترونى',
	'am-active' => 'نشط',
	'am-updated' => 'تغييراتك تم حفظها بنجاح',
	'nss-desc' => 'إضافة للتحقق ضد قاعدة بيانات libnss-mysql. يحتوى على [[Special:AccountManager|مدير حساب]]',
	'nss-rights' => 'صلاحيات',
	'nss-save-changes' => 'حفظ التغييرات',
	'nss-create-account-header' => 'إنشاء حساب جديد',
	'nss-create-account' => 'إنشاء الحساب',
	'nss-welcome-mail' => 'الحساب باسم المستخدم $1 وكلمة السر $2 تم إنشاؤه من أجلك.',
	'nss-welcome-mail-subject' => 'إنشاء الحساب',
	'nss-db-error' => 'خطأ قراءة من قاعدة بيانات التحقق.',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'am-username' => 'потребителско име',
	'am-email' => 'е-поща',
	'am-updated' => 'Промените бяха съхранени успешно!',
	'nss-rights' => 'права',
	'nss-save-changes' => 'Съхраняване на промените',
	'nss-create-account-header' => 'Създаване на нова сметка',
	'nss-create-account' => 'Създаване на сметка',
	'nss-welcome-mail' => 'Беше ви създадена сметка с потребителско име $1 и парола $2.',
);

/** German (Deutsch) */
$messages['de'] = array(
	'accountmanager' => 'Benutzerkonten-Verwaltung',
	'am-username' => 'Benutzername',
	'am-email' => 'E-Mail',
	'am-active' => 'aktiv',
	'am-updated' => 'Die Änderungen wurden erfolgreich gespeichert',
	'nss-desc' => 'Eine Erweiterung, um gegen eine libnss-mysql-Datenbank zu authentifizieren. Inklusive einer [[Special:AccountManager|Benutzerkonten-Verwaltung]]',
	'nss-rights' => 'Rechte',
	'nss-save-changes' => 'Änderungen speichern',
	'nss-create-account-header' => 'Neues Benutzerkonto erstellen',
	'nss-create-account' => 'Benutzerkonto erstellen',
	'nss-no-mail' => 'Sende keine E-Mail',
	'nss-welcome-mail' => 'Ein Benutzerkonto mit dem Benutzernamen „$1“ und dem Passwort „$2“ wurde für dich erstellt.',
	'nss-welcome-mail-subject' => 'Benutzerkonto erstellen',
	'nss-db-error' => 'Fehler beim Lesen aus der Authentifizierungs-Datenbank',
);

/** French (Français)
 * @author Grondin
 * @author IAlex
 */
$messages['fr'] = array(
	'accountmanager' => 'Gestionnaire de comptes',
	'am-username' => "Nom d'utilisateur",
	'am-email' => 'Courriel',
	'am-active' => 'actif',
	'am-updated' => 'Vos modifications ont été sauvegardées avec succès',
	'nss-desc' => "Une extension qui permet d'authentifier au moyen d'une base de données libnss-mysql. Contient un [[Special:AccountManager|gestionnaire de comptes]]",
	'nss-rights' => 'droits',
	'nss-save-changes' => 'Sauvegarder les modifications',
	'nss-create-account-header' => 'Créer un nouveau compte',
	'nss-create-account' => 'Créer le compte',
	'nss-no-mail' => 'Ne pas envoyer de courriel',
	'nss-welcome-mail' => 'Un compte avec le nom $1 et le mot de passe $2 a été créé pour vous.',
	'nss-welcome-mail-subject' => 'Création de compte',
	'nss-db-error' => "Erreur pendant la lecture de la base de données d'authentification",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'accountmanager' => 'Xestor de contas',
	'am-username' => 'nome de usuario',
	'am-email' => 'correo electrónico',
	'am-active' => 'activar',
	'am-updated' => 'Os seus cambios foron gardados con éxito',
	'nss-rights' => 'dereitos',
	'nss-save-changes' => 'Gardar os cambios',
	'nss-create-account-header' => 'Crear unha conta nova',
	'nss-create-account' => 'Crear a conta',
	'nss-welcome-mail-subject' => 'Creación de contas',
);

/** Kinaray-a (Kinaray-a)
 * @author Joebertj
 */
$messages['krj'] = array(
	'accountmanager' => 'Gadumala sa Account',
	'am-username' => 'username',
	'am-email' => 'e-mail',
	'am-active' => 'aktibo',
	'am-updated' => 'Ang imo mga gin-ilis nabaton run',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'am-username' => 'Benotzernumm',
	'am-email' => 'E-Mail',
	'am-active' => 'aktiv',
	'am-updated' => 'är Ännerunge goufe gespäichert',
	'nss-rights' => 'Rechter',
	'nss-save-changes' => 'Ännerunge späicheren',
	'nss-create-account-header' => 'Een neie Benotzerkont opmaachen',
	'nss-create-account' => 'Benotzerkont opmaachen',
	'nss-welcome-mail' => 'E Benotzerkont mat dem Benotzernumm $1 an dem Passwuert $2 gouf fir Iech opgemaach.',
	'nss-welcome-mail-subject' => 'Benotzerkont opmaachen',
	'nss-db-error' => 'Feeler beim Liese vun der Datebank mat den Authentifikatiounen',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'accountmanager' => 'Gebruikersbeheer',
	'am-username' => 'gebruikersnaam',
	'am-email' => 'e-mail',
	'am-active' => 'actief',
	'am-updated' => 'Uw wijzigingen zijn opgeslagen',
	'nss-desc' => 'Een plug-in om te authenticeren tegen een libnss-mysql database. Bevat [[Special:AccountManager|gebruikersbeheer]]',
	'nss-rights' => 'rechten',
	'nss-save-changes' => 'Wijzigingen opslaan',
	'nss-create-account-header' => 'Nieuwe gebruiker aanmaken',
	'nss-create-account' => 'Gebruiker aanmaken',
	'nss-no-mail' => 'Geen e-mail versturen',
	'nss-welcome-mail' => 'Er is een gebruiker met gebruikersnaam $1 en wachtwoord $2 voor u aangemaakt.',
	'nss-welcome-mail-subject' => 'Gebruiker aangemaakt',
	'nss-db-error' => 'Fout bij het lezen van de authenticatiedatabase',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'accountmanager' => 'Gestionari de comptes',
	'am-username' => "Nom d'utilizaire",
	'am-email' => 'Corrièr electronic',
	'am-active' => 'actiu',
	'am-updated' => 'Vòstras modificacions son estadas salvadas amb succès',
	'nss-desc' => "Una extension que permet d'autentificar gràcias a una banca de donadas libnss-mysql. Conten un [[Special:AccountManager|gestionari de comptes]]",
	'nss-rights' => 'dreches',
	'nss-save-changes' => 'Enregistrar los cambiaments',
	'nss-create-account-header' => 'Crear un compte novèl',
	'nss-create-account' => 'Crear un compte',
	'nss-no-mail' => 'Mandar pas de corrièr electronic',
	'nss-welcome-mail' => 'Un compte amb lo nom $1 e lo senhal $2 es estat creat per vos.',
	'nss-welcome-mail-subject' => 'Creacion de compte',
	'nss-db-error' => "Error pendent la lectura de la banca de donadas d'autentificacion",
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'accountmanager' => 'Gestor de contas',
	'am-username' => 'nome de utilizador',
	'am-email' => 'e-mail',
	'am-active' => 'activo',
	'am-updated' => 'As suas alterações foram gravadas com sucesso',
	'nss-desc' => 'Um "plugin" para autenticar numa base de dados libnss-mysql. Contém um [[Special:AccountManager|gestor de contas]]',
	'nss-save-changes' => 'Gravar alterações',
	'nss-create-account-header' => 'Criar nova conta',
	'nss-create-account' => 'Criar conta',
	'nss-welcome-mail' => 'Uma conta com nome de utilizador $1 e palavra-chave $2 foi criada para si.',
	'nss-welcome-mail-subject' => 'Criação de conta',
	'nss-db-error' => 'Erro na leitura da base de dados de autenticação',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 * @author Silviubogan
 */
$messages['ro'] = array(
	'am-username' => 'nume de utilizator',
	'am-email' => 'e-mail',
	'am-active' => 'activ',
	'nss-save-changes' => 'Salvează modificările',
	'nss-create-account-header' => 'Creează cont nou',
	'nss-create-account' => 'Creează cont',
	'nss-welcome-mail-subject' => 'Crearea contului',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'accountmanager' => 'Управление учётными записями',
	'am-username' => 'имя участника',
	'am-email' => 'электронная почта',
	'nss-rights' => 'права',
	'nss-save-changes' => 'Сохранить изменения',
	'nss-create-account-header' => 'Создать новую учётную запись',
	'nss-create-account' => 'Создание учётной записи',
	'nss-welcome-mail' => 'Для вас создана учётная запись с именем $1 и паролем $2.',
	'nss-welcome-mail-subject' => 'Создание учётной записи',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'accountmanager' => 'Správca účtov',
	'am-username' => 'používateľské meno',
	'am-email' => 'email',
	'am-active' => 'aktívny',
	'am-updated' => 'Vaše zmeny boli úspešne uložené',
	'nss-desc' => 'Zásuvný modul na overovanie voči databáze libnss-mysql. Obsahuje [[Special:AccountManager|správcu účtov]].',
	'nss-rights' => 'práva',
	'nss-save-changes' => 'Uložiť zmeny',
	'nss-create-account-header' => 'Vytvoriť nový účet',
	'nss-create-account' => 'Vytvoriť účet',
	'nss-no-mail' => 'Neposielať email',
	'nss-welcome-mail' => 'Bol pre vás vytvorený účet s používateľským menom $1 a heslom $2.',
	'nss-welcome-mail-subject' => 'Vytvorenie účtu',
	'nss-db-error' => 'Chyba pri čítaní z overovacej databázy',
);

/** Swedish (Svenska)
 * @author Najami
 */
$messages['sv'] = array(
	'am-username' => 'användarnamn',
	'am-email' => 'e-post',
	'am-active' => 'aktiv',
	'am-updated' => 'Dina ändringar har sparats',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'accountmanager' => 'Trình quản lý tài khoản',
	'am-username' => 'tên người dùng',
	'am-email' => 'địa chỉ thư điện tử',
	'am-active' => 'tích cực',
	'am-updated' => 'Đã lưu các thay đổi của bạn thành công',
	'nss-desc' => 'Phần bổ trợ để xác nhận tính danh theo cơ sở dữ liệu libnss-mysql, bao gồm [[Special:AccountManager|trình quản lý tài khoản]]',
	'nss-rights' => 'quyền',
	'nss-save-changes' => 'Lưu các thay đổi',
	'nss-create-account-header' => 'Mở tài khoản mới',
	'nss-create-account' => 'Mở tài khoản',
	'nss-welcome-mail' => 'Bạn đã mở tài khoản với tên $1 và mật khẩu $2.',
	'nss-welcome-mail-subject' => 'Tài khoản mới',
	'nss-db-error' => 'Lỗi truy cập cơ sở dữ liệu tài khoản',
);

