<?php
/**
 * Internationalisation file for extension WebStore.
 *
 * @addtogroup Extensions
 */

$messages = array();

$messages['en'] = array(
	'inplace_access_disabled' => 'Access to this service has been disabled for all clients.',
	'inplace_access_denied' => 'This service is restricted by client IP.',
	'inplace_scaler_no_temp' => 'No valid temporary directory, set $wgLocalTmpDirectory to a writeable directory.',
	'inplace_scaler_not_enough_params' => 'Not enough parameters.',
	'inplace_scaler_invalid_image' => 'Invalid image, could not determine size.',
	'inplace_scaler_failed' => 'An error was encountered during image scaling: $1',
	'inplace_scaler_no_handler' => 'No handler for transforming this MIME type',
	'inplace_scaler_no_output' => 'No transformation output file was produced.',
	'inplace_scaler_zero_size' => 'Transformation produced a zero-sized output file.',

	'webstore_access' => 'This service is restricted by client IP.',
	'webstore_path_invalid' => 'The filename was invalid.',
	'webstore_dest_open' => 'Unable to open destination file "$1".',
	'webstore_dest_lock' => 'Failed to get lock on destination file "$1".',
	'webstore_dest_mkdir' => 'Unable to create destination directory "$1".',
	'webstore_archive_lock' => 'Failed to get lock on archive file "$1".',
	'webstore_archive_mkdir' => 'Unable to create archive directory "$1".',
	'webstore_src_open' => 'Unable to open source file "$1".',
	'webstore_src_close' => 'Error closing source file "$1".',
	'webstore_src_delete' => 'Error deleting source file "$1".',

	'webstore_rename' => 'Error renaming file "$1" to "$2".',
	'webstore_lock_open' => 'Error opening lock file "$1".',
	'webstore_lock_close' => 'Error closing lock file "$1".',
	'webstore_dest_exists' => 'Error, destination file "$1" exists.',
	'webstore_temp_open' => 'Error opening temporary file "$1".',
	'webstore_temp_copy' => 'Error copying temporary file "$1" to destination file "$2".',
	'webstore_temp_close' => 'Error closing temporary file "$1".',
	'webstore_temp_lock' => 'Error locking temporary file "$1".',
	'webstore_no_archive' => 'Destination file exists and no archive was given.',

	'webstore_no_file' => 'No file was uploaded.',
	'webstore_move_uploaded' => 'Error moving uploaded file "$1" to temporary location "$2".',

	'webstore_invalid_zone' => 'Invalid zone "$1".',

	'webstore_no_deleted' => 'No archive directory for deleted files is defined.',
	'webstore_curl' => 'Error from cURL: $1',
	'webstore_404' => 'File not found.',
	'webstore_php_warning' => 'PHP Warning: $1',
	'webstore_metadata_not_found' => 'File not found: $1',
	'webstore_postfile_not_found' => 'File to post not found.',
	'webstore_scaler_empty_response' => 'The image scaler gave an empty response with a 200 ' .
	'response code. This could be due to a PHP fatal error in the scaler.',

	'webstore_invalid_response' => "Invalid response from server:\n\n$1\n",
	'webstore_no_response' => 'No response from server',
	'webstore_backend_error' => "Error from storage server:\n\n$1\n",
	'webstore_php_error' => 'PHP errors were encountered:',
	'webstore_no_handler' => 'No handler for transforming this MIME type',
);

/** Afrikaans (Afrikaans)
 * @author SPQRobin
 */
$messages['af'] = array(
	'inplace_scaler_not_enough_params' => 'Nie genoeg parameters nie.',
);

$messages['ar'] = array(
	'inplace_access_disabled' => 'الدخول إلى هذه الخدمة تم تعطيله لكل العملاء.',
	'inplace_access_denied' => 'هذه الخدمة مقيدة بواسطة أيبي عميل.',
	'inplace_scaler_no_temp' => 'لا مجلد مؤقت صحيح، ضبط $wgLocalTmpDirectory لمجلد قابل للكتابة.',
	'inplace_scaler_not_enough_params' => 'لا محددات كافية.',
	'inplace_scaler_invalid_image' => 'صورة غير صحيحة، لم يمكن تحديد الحجم.',
	'inplace_scaler_failed' => 'حدث خطأ أثناء وزن الصورة: $1',
	'inplace_scaler_no_handler' => 'لا وسيلة لتحويل نوع MIME هذا',
	'inplace_scaler_no_output' => 'لا ملف تحويل خارج تم إنتاجه.',
	'inplace_scaler_zero_size' => 'التحويل أنتج ملف خروج حجمه صفر.',
	'webstore_access' => 'هذه الخدمة مقيدة بواسطة أيبي عميل.',
	'webstore_path_invalid' => 'اسم الملف كان غير صحيح.',
	'webstore_dest_open' => 'غير قادر على فتح الملف الهدف "$1".',
	'webstore_dest_lock' => 'فشل في الغلق على ملف الوجهة "$1".',
	'webstore_dest_mkdir' => 'غير قادر على إنشاء مجلد الوجهة "$1".',
	'webstore_archive_lock' => 'فشل في الغلق على ملف الأرشيف "$1".',
	'webstore_archive_mkdir' => 'غير قادر على إنشاء مجلد الأرشيف "$1".',
	'webstore_src_open' => 'غير قادر على فتح ملف المصدر "$1".',
	'webstore_src_close' => 'خطأ أثناء إغلاق ملف المصدر "$1".',
	'webstore_src_delete' => 'خطأ أثناء حذف ملف المصدر "$1".',
	'webstore_rename' => 'خطأ أثناء إعادة تسمية الملف "$1" إلى "$2".',
	'webstore_lock_open' => 'خطأ أثناء فتح غلق الملف "$1".',
	'webstore_lock_close' => 'خطأ أثناء إغلاق غلق الملف "$1".',
	'webstore_dest_exists' => 'خطأ، ملف الوجهة "$1" موجود.',
	'webstore_temp_open' => 'خطأ أثناء فتح الملف المؤقت "$1".',
	'webstore_temp_copy' => 'خطأ أثناء نسخ الملف المؤقت "$1" لملف الوجهة "$2".',
	'webstore_temp_close' => 'خطأ أثناء إغلاق الملف المؤقت "$1".',
	'webstore_temp_lock' => 'خطأ غلق الملف المؤقت "$1".',
	'webstore_no_archive' => 'ملف الوجهة موجود ولم يتم إعطاء أرشيف.',
	'webstore_no_file' => 'لم يتم رفع أي ملف.',
	'webstore_move_uploaded' => 'خطأ أثناء نقل الملف المرفوع "$1" إلى الموقع المؤقت "$2".',
	'webstore_invalid_zone' => 'منطقة غير صحيحة "$1".',
	'webstore_no_deleted' => 'لم يتم تعريف مجلد أرشيف للملفات المحذوفة.',
	'webstore_curl' => 'خطأ من cURL: $1',
	'webstore_404' => 'لم يتم إيجاد الملف.',
	'webstore_php_warning' => 'تحذير PHP: $1',
	'webstore_metadata_not_found' => 'الملف غير موجود: $1',
	'webstore_postfile_not_found' => 'الملف للإرسال غير موجود.',
	'webstore_scaler_empty_response' => 'وازن الصورة أعطى ردا فارغا مع 200 كود رد. هذا يمكن أن يكون نتيجة خطأ PHP قاتل في الوازن.',
	'webstore_invalid_response' => 'رد غير صحيح من الخادم:

$1',
	'webstore_no_response' => 'لا رد من الخادم',
	'webstore_backend_error' => 'خطأ من خادم التخزين:

$1',
	'webstore_php_error' => 'حدثت أخطاء PHP:',
	'webstore_no_handler' => 'لا وسيلة لتحويل نوع MIME هذا',
);

$messages['bcl'] = array(
	'webstore_no_response' => 'Mayong simbag hali sa server',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'inplace_scaler_not_enough_params' => 'Няма достатъчно параметри',
	'inplace_scaler_invalid_image'     => 'Невалидна картинка, размерът й е невъзможно да бъде определен.',
	'webstore_path_invalid'            => 'Името на файла е невалидно.',
	'webstore_dest_open'               => 'Целевият файл „$1“ не може да бъде отворен.',
	'webstore_dest_mkdir'              => 'Невъзможно е да бъде създадена целевата директория „$1“.',
	'webstore_archive_mkdir'           => 'Невъзможно е да бъде създадена архивната директория „$1“.',
	'webstore_src_open'                => 'Файлът-източник „$1“ не може да бъде отворен.',
	'webstore_src_close'               => 'Грешка при затваряне на файла-източник „$1“.',
	'webstore_src_delete'              => 'Грешка при изтриване на файла-източник „$1“.',
	'webstore_rename'                  => 'Грешка при преименуване на файла „$1“ като „$2“.',
	'webstore_dest_exists'             => 'Грешка, целевият файл „$1“ съществува.',
	'webstore_temp_open'               => 'Грешка при отваряне на временния файл „$1“.',
	'webstore_temp_copy'               => 'Грешка при копиране на временния файл „$1“ като целеви файл „$2“.',
	'webstore_temp_close'              => 'Грешка при затваряне на временния файл "$1".',
	'webstore_temp_lock'               => 'Грешка при заключване на временния файл "$1".',
	'webstore_no_file'                 => 'Не беше качен файл.',
	'webstore_invalid_zone'            => 'Невалидна зона "$1".',
	'webstore_no_deleted'              => 'Не е указана архивна директория за изтритите файлове.',
	'webstore_curl'                    => 'Грешка от cURL: $1',
	'webstore_404'                     => 'Файлът не беше намерен.',
	'webstore_php_warning'             => 'PHP Предупреждение: $1',
	'webstore_metadata_not_found'      => 'Файлът не беше намерен: $1',
	'webstore_invalid_response'        => 'Невалиден отговор от сървъра:

$1',
	'webstore_no_response'             => 'Няма отговор от сървъра',
);

/** Bengali (বাংলা)
 * @author Zaheen
 */
$messages['bn'] = array(
	'webstore_php_warning'           => 'পিএইচপি সতর্কীকরণ: $1',
	'webstore_metadata_not_found'    => 'ফাইল খুঁজে পাওয়া যায়নি: $1',
	'webstore_postfile_not_found'    => 'পোস্ট করার জন্য ফাইল খুঁজে পাওয়া যায়নি।',
	'webstore_scaler_empty_response' => 'ছবি মাপবর্ধকটি ২০০নং উত্তর কোডসহ একটি খালি উত্তর পাঠিয়েছে। মাপবর্ধকে পিএইচপি অসমাধানযোগ্য ত্রুটির কারণে এটি হতে পারে।',
	'webstore_invalid_response'      => 'সার্ভার থেকে অবৈধ উত্তর এসেছে:


$1',
	'webstore_no_response'           => 'সার্ভার কোন উত্তর দিচ্ছে না',
	'webstore_backend_error'         => 'স্টোরেজ সার্ভার থেকে প্রাপ্ত ত্রুটি:

$1',
	'webstore_php_error'             => 'পিএইচপি ত্রুটি ঘটেছে:',
	'webstore_no_handler'            => 'এই MIME ধরনটি রূপান্তরের জন্য কোন হ্যান্ডলার নেই',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'inplace_access_disabled'          => "Diweredekaet eo ar moned d'ar servij-mañ evit an holl bratikoù.",
	'inplace_access_denied'            => 'Bevennet eo ar servij-mañ diouzh IP ar pratik.',
	'inplace_scaler_no_temp'           => 'N\'eus teul padennek reizh ebet, ret eo da $wgLocalTmpDirectory bezañ ennañ anv un teul gant gwirioù skrivañ.',
	'inplace_scaler_not_enough_params' => 'Diouer a arventennoù zo',
	'inplace_scaler_invalid_image'     => 'Skeudenn direizh, dibosupl termeniñ ar vent.',
	'inplace_scaler_failed'            => "C'hoarvezet ez eus ur fazi e-ser gwaskañ/diwaskañ ar skeudenn : $1",
	'inplace_scaler_no_handler'        => "Arc'hwel ebet evit treuzfurmiñ ar furmad MIME-se",
	'inplace_scaler_no_output'         => "N'eus bet krouet restr dreuzfurmiñ ebet.",
	'inplace_scaler_zero_size'         => 'Krouet ez eus bet ur restr gant ur vent mann gant an treuzfumadur.',
	'webstore_access'                  => "Bevennet eo ar servij-mañ diouzh chomlec'h IP ar pratik.",
	'webstore_path_invalid'            => 'Direizh eo anv ar restr.',
	'webstore_dest_open'               => 'Dibosupl digeriñ ar restr bal "$1".',
	'webstore_dest_lock'               => 'C\'hwitet ar prennañ war ar restr bal "$1".',
	'webstore_dest_mkdir'              => 'Dibosupl krouiñ ar c\'havlec\'h pal "$1".',
	'webstore_archive_lock'            => 'C\'hwitet ar prennañ war ar restr diellaouet "$1".',
	'webstore_archive_mkdir'           => 'Dibosupl krouiñ ar c\'havlec\'h diellaouiñ "$1".',
	'webstore_src_open'                => 'Dibosupl digeriñ ar restr tarzh "$1".',
	'webstore_src_close'               => 'Fazi en ur serriñ ar restr tarzh "$1".',
	'webstore_src_delete'              => 'Fazi en ur ziverkañ ar restr tarzh "$1".',
	'webstore_rename'                  => 'Fazi en ur adenvel ar restr "$1" e "$2"..',
	'webstore_lock_open'               => 'Fazi en ur zigeriñ ar restr prennet "$1".',
	'webstore_lock_close'              => 'Fazi en ur serriñ ar restr prennet "$1".',
	'webstore_dest_exists'             => 'Fazi, krouet eo bet ar restr bal "$1" dija.',
	'webstore_temp_open'               => 'Fazi en ur zigeriñ ar restr padennek "$1".',
	'webstore_temp_copy'               => 'Fazi en ur eilañ ar restr padennek "$1" war-du ar restr bal "$2".',
	'webstore_temp_close'              => 'Fazi en ur serriñ ar restr padennek "$1".',
	'webstore_temp_lock'               => 'Fazi en ur brennañ ar restr padennek "$1".',
	'webstore_no_archive'              => "Bez'ez eus eus ar restr bal met n'eus bet roet diell ebet.",
	'webstore_no_file'                 => "N'eus bet enporzhiet restr ebet.",
	'webstore_move_uploaded'           => 'Fazi en ur zilec\'hiañ ar restr enporzhiet "$1" war-du al lec\'h da c\'hortoz "$2".',
	'webstore_invalid_zone'            => 'Takad "$1" direizh.',
	'webstore_no_deleted'              => "N'eus bet spisaet kavlec'h diellaouiñ ebet evit ar restroù diverket.",
	'webstore_curl'                    => 'Fazi adal cURL: $1',
	'webstore_404'                     => "N'eo ket bet kavet ar restr.",
	'webstore_php_warning'             => 'Kemenn PHP : $1',
	'webstore_metadata_not_found'      => "N'eo ket bet kavet ar restr : $1",
	'webstore_postfile_not_found'      => "N'eo ket bet kavet ar restr da enrollañ.",
	'webstore_scaler_empty_response'   => "Distroet ez eus bet ur respont goullo hag ur c'hod 200 respont gant standilhonadur ar skeudenn. Marteze diwar ur fazi standilhonañ.",
	'webstore_invalid_response'        => 'Respont direizh digant ar servijer :

$1',
	'webstore_no_response'             => 'Direspont eo ar servijer.',
	'webstore_backend_error'           => 'Fazi gant ar servijer stokañ :  

$1',
	'webstore_php_error'               => 'Setu ar fazioù PHP bet kavet :',
	'webstore_no_handler'              => "N'haller ket treuzfurmiñ ar seurt MIME-mañ.",
);

$messages['el'] = array(
	'webstore_invalid_zone' => 'Άκυρη ζώνη "$1".',
	'webstore_404' => 'Το αρχείο δεν βρέθηκε.',
	'webstore_metadata_not_found' => 'Το Αρχείο δεν βρέθηκε: $1',
);

$messages['ext'] = array(
	'webstore_rename' => 'Marru rehucheandu el archivu "$1" a "$2".',
	'webstore_no_file' => 'Nu s´á empuntau dengún archivu.',
	'webstore_404' => 'Archivu nu alcuentrau.',
);

/** French (Français)
 * @author Grondin
 * @author Dereckson
 * @author Sherbrooke
 */
$messages['fr'] = array(
	'inplace_access_disabled'          => "L'accès à ce service est désactivé pour tous les clients.",
	'inplace_access_denied'            => 'Ce service est restreint sur la base du IP du client.',
	'inplace_scaler_no_temp'           => "Aucun dossier temporaire valide, \$wgLocalTmpDirectory doit contenir le nom d'un dossier avec droits d'écriture.",
	'inplace_scaler_not_enough_params' => 'Pas suffisamment de paramètres',
	'inplace_scaler_invalid_image'     => 'Image incorrecte, ne peut déterminer sa taille',
	'inplace_scaler_failed'            => "Une erreur est survenue pendant la dilatation/contraction (« scaling ») de l'image.",
	'inplace_scaler_no_handler'        => 'Aucune fonction (« handler ») pour transformer ce format MIME.',
	'inplace_scaler_no_output'         => 'Aucun fichier de transformation généré',
	'inplace_scaler_zero_size'         => 'La transformation a créé un fichier de taille zéro.',
	'webstore_access'                  => 'Ce service est restreint par adresse IP.',
	'webstore_path_invalid'            => "Le nom de fichier n'est pas correct.",
	'webstore_dest_open'               => 'Impossible d\'ouvrir le fichier de destination "$1".',
	'webstore_dest_lock'               => 'Échec pour obtenir le verrouillage sur le fichier de destination « $1 ».',
	'webstore_dest_mkdir'              => 'Impossible de créer le répertoire "$1".',
	'webstore_archive_lock'            => 'Échec pour obtenir le verrouillage du fichier archivé « $1 ».',
	'webstore_archive_mkdir'           => "Impossible de créer le répertoire d'archivage « $1 ».",
	'webstore_src_open'                => 'Impossible d’ouvrir le fichier source « $1 ».',
	'webstore_src_close'               => 'Erreur de fermeture du fichier source « $1 ».',
	'webstore_src_delete'              => 'Erreur de suppression du fichier source « $1 ».',
	'webstore_rename'                  => 'Erreur de renommage du fichier « $1 » en « $2 ».',
	'webstore_lock_open'               => "Erreur d'ouverture du fichier verrouillé « $1 ».",
	'webstore_lock_close'              => 'Erreur de fermeture du fichier verrouillé « $1 ».',
	'webstore_dest_exists'             => 'Erreur, le fichier de destination « $1 » existe.',
	'webstore_temp_open'               => "Erreur d'ouverture du fichier temporaire « $1 ».",
	'webstore_temp_copy'               => 'Erreur de copie du fichier temporaire « $1 » vers le fichier de destination « $2 ».',
	'webstore_temp_close'              => 'Erreur de fermeture du fichier temporaire « $1 ».',
	'webstore_temp_lock'               => 'Erreur de verrouillage du fichier temporaire « $1 ».',
	'webstore_no_archive'              => "Le fichier de destination existe et aucune archive n'a été donnée.",
	'webstore_no_file'                 => "Aucun fichier n'a été téléchargé.",
	'webstore_move_uploaded'           => 'Erreur de déplacement du fichier téléchargé « $1 » vers l’emplacement temporaire « $2 ».',
	'webstore_invalid_zone'            => 'Zone « $1 » invalide.',
	'webstore_no_deleted'              => "Aucun répertoire d’archive pour les fichiers supprimés n'a été défini.",
	'webstore_curl'                    => 'Erreur depuis cURL : $1',
	'webstore_404'                     => 'Fichier non trouvé.',
	'webstore_php_warning'             => 'PHP Warning: $1',
	'webstore_metadata_not_found'      => 'Fichier non trouvé : $1',
	'webstore_postfile_not_found'      => 'Fichier à enregistrer non trouvé.',
	'webstore_scaler_empty_response'   => "L’échantillonnage de l'image a donné une réponse nulle avec un code de 200 réponses. Ceci pourrait être du à une erreur de l'échantillonage.",
	'webstore_invalid_response'        => 'Réponse invalide depuis le serveur : 

$1',
	'webstore_no_response'             => 'Le serveur ne répond pas',
	'webstore_backend_error'           => 'Erreur depuis le serveur de stockage : 

$1',
	'webstore_php_error'               => 'Les erreurs PHP suivantes sont survenues :',
	'webstore_no_handler'              => 'Ce type MIME ne peut être transformé.',
);

/** Galician (Galego)
 * @author Xosé
 * @author Toliño
 */
$messages['gl'] = array(
	'inplace_access_disabled'          => 'Desactivouse o acceso a este servizo para todos os clientes.',
	'inplace_access_denied'            => 'Este servizo está restrinxido polo IP do cliente.',
	'inplace_scaler_no_temp'           => 'Non é un directorio temporal válido; configure $wgLocalTmpDirectory nun directorio no que se poida escribir.',
	'inplace_scaler_not_enough_params' => 'Os parámetros son insuficientes.',
	'inplace_scaler_invalid_image'     => 'A imaxe non é válida, non se puido determinar o seu tamaño.',
	'inplace_scaler_failed'            => 'Atopouse un erro mentres se ampliaba a imaxe: $1',
	'inplace_scaler_no_handler'        => 'Non se definiu un programa para transformar este tipo MIME',
	'inplace_scaler_no_output'         => 'Non se produciu ningún ficheiro de saída da transformación.',
	'inplace_scaler_zero_size'         => 'A transformación produciu un ficheiro de saída de tamaño 0.',
	'webstore_access'                  => 'Este servizo está restrinxido polo IP do cliente.',
	'webstore_path_invalid'            => 'O nome do ficheiro non era válido.',
	'webstore_dest_open'               => 'Foi imposíbel abrir o ficheiro de destino "$1".',
	'webstore_dest_lock'               => 'Non se puido bloquear o ficheiro de destino "$1".',
	'webstore_dest_mkdir'              => 'Non se puido crear o directorio de destino "$1".',
	'webstore_archive_lock'            => 'Non se puido bloquear o ficheiro de arquivo "$1".',
	'webstore_archive_mkdir'           => 'Non se puido crear o directorio de arquivo "$1".',
	'webstore_src_open'                => 'Non se puido abrir o ficheiro de orixe "$1".',
	'webstore_src_close'               => 'Erro ao pechar o ficheiro de orixe "$1".',
	'webstore_src_delete'              => 'Erro ao eliminar o ficheiro de orixe "$1".',
	'webstore_rename'                  => 'Erro ao lle mudar o nome a "$1" para "$2".',
	'webstore_lock_open'               => 'Erro ao abrir o ficheiro de bloqueo "$1".',
	'webstore_lock_close'              => 'Erro ao fechar o ficheiro de bloqueo "$1".',
	'webstore_dest_exists'             => 'Erro, xa existe o ficheiro de destino "$1".',
	'webstore_temp_open'               => 'Erro ao abrir o ficheiro temporal "$1".',
	'webstore_temp_copy'               => 'Erro ao copiar o ficheiro temporal "$1" no ficheiro de destino "$2".',
	'webstore_temp_close'              => 'Erro ao fechar o ficheiro temporal "$1".',
	'webstore_temp_lock'               => 'Erro ao bloquear o ficheiro temporal "$1".',
	'webstore_no_archive'              => 'O ficheiro de destino xa existe e non se deu un arquivo.',
	'webstore_no_file'                 => 'Non se enviou ningún ficheiro.',
	'webstore_move_uploaded'           => 'Erro ao mover o ficheiro enviado "$1" para a localización temporal "$2".',
	'webstore_invalid_zone'            => 'Zona "$1" non válida.',
	'webstore_no_deleted'              => 'Non se definiu un directorio de arquivo para os ficheiros eliminados.',
	'webstore_curl'                    => 'Erro de cURL: $1',
	'webstore_404'                     => 'Non se atopou o ficheiro.',
	'webstore_php_warning'             => 'Aviso de PHP: $1',
	'webstore_metadata_not_found'      => 'Non se atopou o ficheiro: $1',
	'webstore_postfile_not_found'      => 'Non se atopou o ficheiro enviado.',
	'webstore_scaler_empty_response'   => 'O redimensionador de imaxes deu unha resposta baleira co código de resposta 200. Isto podería deberse a un erro fatal de PHP no redimensionador.',
	'webstore_invalid_response'        => 'Resposta non válida do servidor:

$1',
	'webstore_no_response'             => 'Sen resposta desde o servidor',
	'webstore_backend_error'           => 'Erro do servidor de almacenamento:

$1',
	'webstore_php_error'               => 'Atopáronse erros de PHP:',
	'webstore_no_handler'              => 'Non hai un programa para transformar este tipo MIME',
);

$messages['hsb'] = array(
	'inplace_access_disabled' => 'Přistup k tutej słužbje bu za wšě klienty znjemóžnjeny.',
	'inplace_access_denied' => 'Tuta słužba so přez klientowy IP wobmjezuje.',
	'inplace_scaler_no_temp' => 'Žadyn płaćiwy temporerny zapis, staj wariablu $wgLocalTmpDirectory na popisajomny zapis',
	'inplace_scaler_not_enough_params' => 'Falowace parametry.',
	'inplace_scaler_invalid_image' => 'Njepłaćiwy wobraz, wulkosć njeda so zwěsćić.',
	'inplace_scaler_failed' => 'Při skalowanju je zmylk wustupił: $1',
	'inplace_scaler_no_handler' => 'Žadyn rjadowak, zo by so tutón MIME-typ přetworił',
	'inplace_scaler_no_output' => 'Njeje so žana wudawanska dataja spłodźiła.',
	'inplace_scaler_zero_size' => 'Přetworjenje spłodźi prózdnu wudawansku dataju.',
	'webstore_access' => 'Tuta słužba so přez klientowy IP wobmjezuje.',
	'webstore_path_invalid' => 'Datajowe mjeno bě njepłaćiwe.',
	'webstore_dest_open' => 'Njeje móžno cilowu dataju "$1" wočinić.',
	'webstore_dest_lock' => 'Zawrjenje ciloweje dataje "$1" njeje so poradźiło.',
	'webstore_dest_mkdir' => 'Njeje móžno cilowy zapis "$1" wutworić.',
	'webstore_archive_lock' => 'Zawrjenje archiwneje dataje "$1" njeje so poradźiło.',
	'webstore_archive_mkdir' => 'Njeje móžno archiwowy zapis "$1" wutworić.',
	'webstore_src_open' => 'Njeje móžno žórłowu dataju "$1" wočinić.',
	'webstore_src_close' => 'Zmylk při začinjenju žórłoweje dataje "$1".',
	'webstore_src_delete' => 'Zmylk při zničenju dataje "$1".',
	'webstore_rename' => 'Zmylk při přemjenowanju dataje "$1" do "$2".',
	'webstore_lock_open' => 'Zmylk při wočinjenju blokowaceje dataje "$1".',
	'webstore_lock_close' => 'Zmylk při začinjenju blokowaceje dataje "$1".',
	'webstore_dest_exists' => 'Zmylk, cilowa dataja "$1" eksistuje.',
	'webstore_temp_open' => 'Zmylk při wočinjenju temporerneje dataje "$1".',
	'webstore_temp_copy' => 'Zmylk při kopěrowanju temporerneje dataje "$1" do ciloweje dataje "$2".',
	'webstore_temp_close' => 'Zmylk při začinjenju temporerneje dataje "$1".',
	'webstore_temp_lock' => 'Zmylk při zawrjenju temporerneje dataje "$1".',
	'webstore_no_archive' => 'Cilowa dataja eksistuje a žadyn archiw njebu podaty.',
	'webstore_no_file' => 'Žana dataja njebu nahrata.',
	'webstore_move_uploaded' => 'Zmylk při přesunjenju nahrateje dataje "$1" k nachwilnemu městnu "$2".',
	'webstore_invalid_zone' => 'Njepłaćiwy wobłuk "$1".',
	'webstore_no_deleted' => 'Njebu žadyn archiwowy zapis za zničene dataje definowany.',
	'webstore_curl' => 'Zmylk z cURL: $1',
	'webstore_404' => 'Dataja njenamakana.',
	'webstore_php_warning' => 'Warnowanje PHP: $1',
	'webstore_metadata_not_found' => 'Dataja njenamakana: $1',
	'webstore_postfile_not_found' => 'Dataja, kotraž ma so wotesłać, njebu namakana.',
	'webstore_scaler_empty_response' => 'Wobrazowy skalowar wróći prózdnu wotmołwu z wotmołwnym kodom 200. Přičina móhła ćežki zmylk PHP w skalowarju być.',
	'webstore_invalid_response' => 'Njepłaćiwa wotmołwa ze serwera:

$1',
	'webstore_no_response' => 'Žana wotmołwa ze serwera',
	'webstore_backend_error' => 'Zmylk ze składowanskeho serwera:

$1',
	'webstore_php_error' => 'Zmylki PHP su wustupili:',
	'webstore_no_handler' => 'Žadyn rjadowak, zo by so tutón MIME-typ přetworił',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'inplace_scaler_not_enough_params' => 'Net genuch Parameteren.',
	'webstore_no_file'                 => 'Et gouf kee Fichier eropgelueden.',
	'webstore_404'                     => 'De Fichier gouf net fonnt.',
	'webstore_php_warning'             => 'PHP Warnung: $1',
	'webstore_metadata_not_found'      => 'De Fichier $1 gouf net fonnt',
	'webstore_no_response'             => 'De Server äntwert net',
	'webstore_php_error'               => 'Dës PHP Feeler sinn opgetratt:',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'inplace_access_disabled'          => 'Toegang tot deze dienst is uitgeschakeld voor alle clients.',
	'inplace_access_denied'            => 'Deze dienst is afgeschermd op basis van het IP-adres van een client.',
	'inplace_scaler_no_temp'           => 'Geen juiste tijdelijke map, geef schrijfrechten op $wgLocalTmpDirectory.',
	'inplace_scaler_not_enough_params' => 'Niet genoeg parameters.',
	'inplace_scaler_invalid_image'     => 'Onjuiste afbeelding. Grootte kon niet bepaald worden.',
	'inplace_scaler_failed'            => 'Er is een fout opgetreden bij het schalen van de afbeelding: $1',
	'inplace_scaler_no_handler'        => 'Dit MIME-type kan niet getransformeerd worden',
	'inplace_scaler_no_output'         => 'Er is geen uitvoerbestand voor de transformatie gemaakt.',
	'inplace_scaler_zero_size'         => 'De grootte van het uitvoerbestand van de transformatie was nul.',
	'webstore_access'                  => 'Deze dienst is afgeschermd op basis van het IP-adres van een client.',
	'webstore_path_invalid'            => 'De bestandnaam was ongeldig.',
	'webstore_dest_open'               => 'Het doelbestand "$1" kon niet geopend worden.',
	'webstore_dest_lock'               => 'Het doelbestand "$1" was niet te locken.',
	'webstore_dest_mkdir'              => 'De doelmap "$1" kon niet aangemaakt worden.',
	'webstore_archive_lock'            => 'Het archiefbestand "$1" was niet te locken.',
	'webstore_archive_mkdir'           => 'De archiefmap "$1" kon niet aangemaakt worden.',
	'webstore_src_open'                => 'Het bronbestand "$1" was niet te openen.',
	'webstore_src_close'               => 'Fout bij het sluiten van bronbestand "$1".',
	'webstore_src_delete'              => 'Fout bij het verwijderen van bronbestand "$1".',
	'webstore_rename'                  => 'Fout bij het hernoemen van "$1" naar "$2".',
	'webstore_lock_open'               => 'Fout bij het openen van lockbestand "$1".',
	'webstore_lock_close'              => 'Fout bij het sluiten van lockbestand "$1".',
	'webstore_dest_exists'             => 'Fout, doelbestand "$1" bestaat al.',
	'webstore_temp_open'               => 'Fout bij het openen van tijdelijk bestand "$1".',
	'webstore_temp_copy'               => 'Fout bij het kopiren van tijdelijk bestand "$1" naar doelbestand "$2".',
	'webstore_temp_close'              => 'Fout bij het sluiten van tijdelijk bestand "$1".',
	'webstore_temp_lock'               => 'Fout bij het locken van tijdelijk bestand "$1".',
	'webstore_no_archive'              => 'Doelbestand bestaat en er is geen archief opgegeven.',
	'webstore_no_file'                 => 'Er is geen bestand geuploaded.',
	'webstore_move_uploaded'           => 'Fout bij het verplaatsen van geupload bestand "$1" naar tijdelijke locatie "$2".',
	'webstore_invalid_zone'            => 'Ongeldige zone "$1".',
	'webstore_no_deleted'              => 'Er is geen archiefmap voor verwijderde bestanden gedefinieerd.',
	'webstore_curl'                    => 'Fout van cURL: $1',
	'webstore_404'                     => 'Bestand niet gevonden.',
	'webstore_php_warning'             => 'PHP-waarschuwing: $1',
	'webstore_metadata_not_found'      => 'Bestand  niet gevonden: $1',
	'webstore_postfile_not_found'      => 'Te posten bestand niet gevonden.',
	'webstore_scaler_empty_response'   => 'De afbeeldingenschaler gaf een leeg antwoord met een 200 antwoordcode. Dit kan te maken hebben met een fatale PHP-fout in de schaler.',
	'webstore_invalid_response'        => 'Ongeldig antwoord van de server:

$1',
	'webstore_no_response'             => 'Geen antwoord van de server',
	'webstore_backend_error'           => 'Fout van de opslagserver:

$1',
	'webstore_php_error'               => 'Er zijn PHP-fouten opgetreden:',
	'webstore_no_handler'              => 'Dit MIME-type kan niet getransformeerd worden',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'inplace_access_disabled'          => 'Tilgangen til denne tjenesten har blitt slått av for alle klienter.',
	'inplace_access_denied'            => 'Denne tjenesten begrenses av klientens IP.',
	'inplace_scaler_no_temp'           => 'Ingen gyldig midlertidig mappe, sett $wgLocalTmpDirectory til en skrivbar mappe.',
	'inplace_scaler_not_enough_params' => 'Ikke not parametere.',
	'inplace_scaler_invalid_image'     => 'Ugyldig bilde, kunne ikke fastslå størrelse.',
	'inplace_scaler_failed'            => 'En feil oppsto under bildeskalering: $1',
	'inplace_scaler_no_handler'        => 'Ingen behandler for endring av denne MIME-typen',
	'inplace_scaler_no_output'         => 'Ingen endringsresultatfil ble produsert.',
	'inplace_scaler_zero_size'         => 'Endringen produserte en tom resultatfil.',
	'webstore_access'                  => 'Tjenesten begrenses av klientens IP.',
	'webstore_path_invalid'            => 'Filnavnet var ugyldig.',
	'webstore_dest_open'               => 'Kunne ikke åpne målfil «$1».',
	'webstore_dest_lock'               => 'Kunne ikke låses på målfil «$1».',
	'webstore_dest_mkdir'              => 'Kunne ikke opprette målmappe «$1».',
	'webstore_archive_lock'            => 'Kunne ikke låses på arkivfil «$1».',
	'webstore_archive_mkdir'           => 'Kunne ikke opprette arkivmappe «$1».',
	'webstore_src_open'                => 'Kunne ikke åpne kildefil «$1».',
	'webstore_src_close'               => 'Feil under lukking av kildefil «$1».',
	'webstore_src_delete'              => 'Feil under sletting av kildefil «$1».',
	'webstore_rename'                  => 'Feil under omdøping av «$1» til «$2».',
	'webstore_lock_open'               => 'Feil under åpning av låsfil «$1».',
	'webstore_lock_close'              => 'Feil under lukking av låsfil «$1».',
	'webstore_dest_exists'             => 'Feil, målfilen «$1» finnes.',
	'webstore_temp_open'               => 'Feil under åpning av midlertidig fil «$1».',
	'webstore_temp_copy'               => 'Feil under kopiering av midlertidig fil «$1» til målfil «$2».',
	'webstore_temp_close'              => 'Feil under lukking av midlertidig fil «$1».',
	'webstore_temp_lock'               => 'Feil under låsing av midlertidig fil «$1».',
	'webstore_no_archive'              => 'Målfilen finnes og ikke noe arkiv ble gitt.',
	'webstore_no_file'                 => 'Ingen fil ble lastet opp.',
	'webstore_move_uploaded'           => 'Feil under flytting av opplastet fil «$1» til midlertidig sted «$2».',
	'webstore_invalid_zone'            => 'Ugyldig sone «$1».',
	'webstore_no_deleted'              => 'Ingen arkivmappe for slettede filer er angitt.',
	'webstore_curl'                    => 'Feil fra cURL: $1',
	'webstore_404'                     => 'Fil ikke funnet.',
	'webstore_php_warning'             => 'PHP-advarsel: $1',
	'webstore_metadata_not_found'      => 'Fil ikke funnet: $1',
	'webstore_postfile_not_found'      => 'Fil  som skal postes ikke funnet.',
	'webstore_scaler_empty_response'   => 'Bildeskalereren ga et tomt svar med en 200-responskode. Dette kan være på grunn av en fatal PHP-feil i  skalereren.',
	'webstore_invalid_response'        => 'Ugyldig svar fra tjener:

$1',
	'webstore_no_response'             => 'Ingen respons fra tjener.',
	'webstore_backend_error'           => 'Feil fra lagringstjener:

$1',
	'webstore_php_error'               => 'PHP-feil ble funnet:',
	'webstore_no_handler'              => 'Ingen behandler for endring av denne MIME-typen',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'inplace_access_disabled'          => "L'accès a aqueste servici es desactivat per totes los clients.",
	'inplace_access_denied'            => 'Aqueste servici es restrenhut sus la basa del IP del client.',
	'inplace_scaler_no_temp'           => "Pas cap de dorsièr temporari valid, \$wgLocalTmpDirectory deu conténer lo nom d'un dorsièr amb dreches d'escritura.",
	'inplace_scaler_not_enough_params' => 'Pas pro de paramètres',
	'inplace_scaler_invalid_image'     => 'Imatge incorrècte, pòt pas determinar sa talha',
	'inplace_scaler_failed'            => "Una error es susvenguda pendent la dilatacion/contraccion (« scaling ») de l'imatge.",
	'inplace_scaler_no_handler'        => 'Cap de foncion (« handler ») per transformar aqueste format MIME.',
	'inplace_scaler_no_output'         => 'Cap de fichièr de transformacion generit',
	'inplace_scaler_zero_size'         => 'La transformacion a creat un fichièr de talha zèro.',
	'webstore_access'                  => 'Aqueste servici es restrenhut per adreça IP.',
	'webstore_path_invalid'            => 'Lo nom de fichièr es pas corrècte.',
	'webstore_dest_open'               => 'Impossible de dobrir lo fichièr de destinacion "$1".',
	'webstore_dest_lock'               => 'Fracàs per obténer lo varrolhatge sul fichièr de destinacion « $1 ».',
	'webstore_dest_mkdir'              => 'Impossible de crear lo repertòri "$1".',
	'webstore_archive_lock'            => 'Fracàs per obténer lo varrolhatge del fichièr archivat « $1 ».',
	'webstore_archive_mkdir'           => "Impossible de crear lo repertòri d'archivatge « $1 ».",
	'webstore_src_open'                => 'Impossible de dobrir lo fichièr font « $1 ».',
	'webstore_src_close'               => 'Error de tampadura del fichièr font « $1 ».',
	'webstore_src_delete'              => 'Error de supression del fichièr font « $1 ».',
	'webstore_rename'                  => 'Error de renomatge del fichièr « $1 » en « $2 ».',
	'webstore_lock_open'               => 'Error de dobertura del fichièr varrolhat « $1 ».',
	'webstore_lock_close'              => 'Error de tampadura del fichièr varrolhat « $1 ».',
	'webstore_dest_exists'             => 'Error, lo fichièr de destinacion « $1 » existís.',
	'webstore_temp_open'               => 'Error de dobertura del fichièr temporari « $1 ».',
	'webstore_temp_copy'               => 'Error de còpia del fichièr temporari « $1 » vèrs lo fichièr de destinacion « $2 ».',
	'webstore_temp_close'              => 'Error de tampadura del fichièr temporari « $1 ».',
	'webstore_temp_lock'               => 'Error de varrolhatge del fichièr temporari « $1 ».',
	'webstore_no_archive'              => 'Error de varrolhatge del fichièr temporari « $1 ».',
	'webstore_no_file'                 => 'Cap de fichièr es pas estat telecargat.',
	'webstore_move_uploaded'           => 'Error de desplaçament del fichièr telecargat « $1 » vèrs l’emplaçament temporari « $2 ».',
	'webstore_invalid_zone'            => 'Zòna « $1 » invalida.',
	'webstore_no_deleted'              => 'Cap de repertòri d’archius pels fichièrs suprimits es pas estat definit.',
	'webstore_curl'                    => 'Error dempuèi cURL : $1',
	'webstore_404'                     => 'Fichièr pas trobat.',
	'webstore_metadata_not_found'      => 'Fichièr pas trobat : $1',
	'webstore_postfile_not_found'      => "Fichièr d'enregistrar pas trobat.",
	'webstore_scaler_empty_response'   => "L’escandilhatge de l'imatge a balhat una responsa nulla amb un còde de 200 responsas. Aquò poiriá èsser degut a una error de l'escandilhatge.",
	'webstore_invalid_response'        => 'Responsa invalida dempuèi lo serveire :  

$1',
	'webstore_no_response'             => 'Lo serveire respondís pas',
	'webstore_backend_error'           => 'Error dempuèi lo serveire de stocatge :  

$1',
	'webstore_php_error'               => 'Las errors PHP seguentas son susvengudas',
	'webstore_no_handler'              => 'Aqueste tipe MIME pòt pas èsser transformat.',
);

$messages['pl'] = array(
	'inplace_access_disabled' => 'Dostęp do tej usługi został wyłączony dla wszystkich klientów.',
	'inplace_access_denied' => 'Ta usługa jest ograniczona przez IP klienta.',
	'inplace_scaler_no_temp' => 'Nie istnieje poprawny katalog tymczasowy, ustaw $wgLocalTmpDirectory na zapisywalny katalog.',
	'inplace_scaler_not_enough_params' => 'Niewystarczająca liczba parametrów.',
	'inplace_scaler_invalid_image' => 'Niepoprawna grafika, nie można określić rozmiaru.',
	'inplace_scaler_failed' => 'Wystąpił błąd przy skalowaniu grafiki: $1',
	'inplace_scaler_no_handler' => 'Brak handlera dla transformacji tego typu MIME',
	'inplace_scaler_no_output' => 'Nie stworzono pliku wyjściowego transformacji.',
	'inplace_scaler_zero_size' => 'Transformacja stworzyła plik o zerowej wielkości.',
);

$messages['pms'] = array(
	'inplace_access_disabled' => 'Ës servissi-sì a l\'é stàit dësmortà për tuti ij client.',
	'inplace_access_denied' => 'Ës servissi-sì a l\'é limità a sconda dl\'adrëssa IP dël client.',
	'inplace_scaler_no_temp' => 'A-i é gnun dossié provisòri bon, ch\'a buta un valor ëd $wgLocalTmpDirectory ch\'a men-a a un dossié ch\'as peulo scriv-se.',
	'inplace_scaler_not_enough_params' => 'A-i é pa basta ëd paràmetr.',
	'inplace_scaler_invalid_image' => 'Figura nen bon-a, a l\'é nen podusse determiné l\'amzura.',
	'inplace_scaler_failed' => 'A l\'é riva-ie n\'eror ën ardimensionand la figura: $1',
	'inplace_scaler_no_handler' => 'A-i manca l\'utiss (handler) për ardimensioné sta sòrt MIME-sì',
	'inplace_scaler_no_output' => 'La trasformassion a l\'ha nen dàit gnun archivi d\'arzultà.',
	'inplace_scaler_zero_size' => 'La transformassion a l\'ha dàit n\'archivi d\'arzultà veujd.',
	'webstore_access' => 'Ës servissi-sì a l\'é limità a sconda dl\'adrëssa IP dël client.',
	'webstore_path_invalid' => 'Ël nòm dl\'archivi a l\'é nen bon.',
	'webstore_dest_open' => 'As peul nen deurbe l\'archivi ëd destinassion "$1".',
	'webstore_dest_lock' => 'A l\'é nen podusse sëré ël luchèt ansima a l\'archivi ëd destinassion "$1".',
	'webstore_dest_mkdir' => 'A l\'é nen podusse creé ël dossié ëd destinassion "$1".',
	'webstore_archive_lock' => 'A l\'é nen podusse sëré ël luchèt ansima a l\'archivi "$1".',
	'webstore_archive_mkdir' => 'A l\'é nen podusse creé ël dossié da archivi "$1".',
	'webstore_src_open' => 'A l\'é nen podusse deurbe l\'archivi sorgiss "$1".',
	'webstore_src_close' => 'A l\'é riva-ie n\'eror ën sërand l\'archivi sorgiss "$1".',
	'webstore_src_delete' => 'A l\'é riva-ie n\'eror ën scanceland l\'archivi sorgiss "$1".',
	'webstore_rename' => 'A l\'é riva-ie n\'eror ën arbatiand l\'archivi "$1" coma "$2".',
	'webstore_lock_open' => 'A l\'é riva-ie n\'eror ën duvertand l\'archivi-luchèt "$1".',
	'webstore_lock_close' => 'A l\'é riva-ie n\'eror ën sërand l\'archivi-luchèt "$1".',
	'webstore_dest_exists' => 'Eror, l\'archivi ëd destinassion "$1" a-i é già.',
	'webstore_temp_open' => 'A l\'é riva-ie n\'eror ën duvertand l\'archivi provisòri "$1".',
	'webstore_temp_copy' => 'A l\'é riva-ie n\'eror ën tracopiand l\'archivi provisòri "$1" a l\'archivi ëd destinassion "$2".',
	'webstore_temp_close' => 'A l\'é riva-ie n\'eror ën sërand l\'archivi provisòri "$1".',
	'webstore_temp_lock' => 'A l\'é riva-ie n\'eror ën butand-je \'l luchèt a l\'archivi provisòri "$1".',
	'webstore_no_archive' => 'L\'archivi ëd destinassion a-i é già e a l\'é butasse gnun archivi.',
	'webstore_no_file' => 'Pa gnun archivi carià.',
	'webstore_move_uploaded' => 'A l\'é riva-ie n\'eror an tramudand l\'archivi carià "$1" a la locassion provisòria "$2".',
	'webstore_invalid_zone' => 'Zòna "$1" nen bon-a.',
	'webstore_no_deleted' => 'A l\'é pa specificasse gnun dossié da archivi për coj ch\'as ëscancelo.',
	'webstore_curl' => 'Eror da \'nt l\'adrëssa (cURL): $1',
	'webstore_404' => 'Archivi nen trovà.',
	'webstore_php_warning' => 'Avis dël PHP: $1',
	'webstore_metadata_not_found' => 'Archivi nen trovà: $1',
	'webstore_postfile_not_found' => 'Archivi da mandé nen trovà.',
	'webstore_scaler_empty_response' => 'Ël programa d\'ardimensionament dle figure a l\'ha dàit n\'arspòsta veujda con un còdes d\'arspòsta 200. Sòn a podrìa esse rivà për via d\'un eror fatal ant ël PHP dël programa.',
	'webstore_invalid_response' => 'Arspòsta nen bon-a da \'nt ël servent: $1',
	'webstore_no_response' => 'Pa d\'arspòsta da \'nt ël servent.',
	'webstore_backend_error' => 'Eror da \'nt ël servent da stocagi: $1',
	'webstore_php_error' => 'A son riva-ie dj\'eror dël PHP:',
	'webstore_no_handler' => 'A-i manca l\'utiss (handler) për ardimensioné sta sòrt MIME-sì',
);
$messages['pt'] = array(
	'inplace_access_disabled' => 'O acesso a este serviço foi desabilitado para todos os clientes.',
	'inplace_access_denied' => 'Este serviço está restringido por IP de cliente.',
	'inplace_scaler_no_temp' => 'Não existe directoria temporária, defina $wgLocalTmpDirectory com uma directoria onde seja possível escrever.',
	'inplace_scaler_not_enough_params' => 'Parâmetros insuficientes.',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'inplace_access_disabled'          => 'O acesso a este serviço foi desabilitado para todos os clientes.',
	'inplace_access_denied'            => 'Este serviço está restringido por IP de cliente.',
	'inplace_scaler_no_temp'           => 'Não existe directoria temporária, defina $wgLocalTmpDirectory com uma directoria onde seja possível escrever.',
	'inplace_scaler_not_enough_params' => 'Parâmetros insuficientes.',
	'webstore_invalid_zone'            => 'Zona "$1" inválida.',
	'webstore_php_warning'             => 'Aviso PHP: $1',
	'webstore_metadata_not_found'      => 'Ficheiro não encontrado: $1',
	'webstore_no_response'             => 'Sem resposta do servidor',
	'webstore_php_error'               => 'Foram encontrados erros PHP:',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'inplace_access_disabled'          => 'Prístup k tejto službe bol vypnutý pre všetkých klientov.',
	'inplace_access_denied'            => 'Táto služba je obmedzená na určené klientské IP adresy.',
	'inplace_scaler_no_temp'           => 'Dočasný adresár nie je platný, nastavte $wgLocalTmpDirectory na zapisovateľný adresár.',
	'inplace_scaler_not_enough_params' => 'Nedostatok parametrov.',
	'inplace_scaler_invalid_image'     => 'Neplatný obrázok, nebolo možné určiť veľkosť.',
	'inplace_scaler_failed'            => 'Počas zmeny veľkosti obrázka sa vyskytla chyba: $1',
	'inplace_scaler_no_handler'        => 'Pre transformáciu tohto typu MIME neexistuje obsluha',
	'inplace_scaler_no_output'         => 'Nebol vytvorený výstupný súbor tejto transformácie.',
	'inplace_scaler_zero_size'         => 'Transformácia vytvorila výstupný súbor s nulovou veľkosťou.',
	'webstore_access'                  => 'Táto služba je obmedzená na určené klientské IP adresy.',
	'webstore_path_invalid'            => 'Názov súboru bol neplatný.',
	'webstore_dest_open'               => 'Nebolo možné otvoriť cieľový súbor „$1“.',
	'webstore_dest_lock'               => 'Nebolo možné záskať zámok na cieľový súbor „$1“.',
	'webstore_dest_mkdir'              => 'Nebolo možné vytvoriť cieľový adresár „$1“.',
	'webstore_archive_lock'            => 'Nebolo možné získať zámok na súbor archívu „$1“.',
	'webstore_archive_mkdir'           => 'Nebolo možné vytvoriť archívny adresár „$1“.',
	'webstore_src_open'                => 'Nebolo možné otvoriť zdrojový súbor „$1“.',
	'webstore_src_close'               => 'Chyba pri zatváraní zdrojového súboru „$1“.',
	'webstore_src_delete'              => 'Chyba pri mazaní zdrojového súboru „$1“.',
	'webstore_rename'                  => 'Chyba pri premenovávaní súboru „$1“ na „$2“.',
	'webstore_lock_open'               => 'Chyba pri otváraní súboru zámku „$1“.',
	'webstore_lock_close'              => 'Chyba pri zatváraní súboru zámku „$1“.',
	'webstore_dest_exists'             => 'Chyba, cieľový súbor „$1“ existuje.',
	'webstore_temp_open'               => 'Chyba pri otváraní dočasného súboru „$1“.',
	'webstore_temp_copy'               => 'Chyba pri kopírovaní dočasného súboru „$1“ do cieľového súboru „$2“.',
	'webstore_temp_close'              => 'Chyba pri zatváraní dočasného súboru „$1“.',
	'webstore_temp_lock'               => 'Chyba pri zamykaní dočasného súboru „$1“.',
	'webstore_no_archive'              => 'Cieľový súbor existuje a nebol zadaný archív.',
	'webstore_no_file'                 => 'Žiadny súbor nebol nahraný.',
	'webstore_move_uploaded'           => 'Chyba pri presúvaní nahraného súboru „$1“ na dočasné miesto „$2“.',
	'webstore_invalid_zone'            => 'Neplatné zóna „$1“.',
	'webstore_no_deleted'              => 'Nebol definovaný žiadny archívny adresár pre zmazané súbory.',
	'webstore_curl'                    => 'Chýba od cURL: $1',
	'webstore_404'                     => 'Súbor nenájdený.',
	'webstore_php_warning'             => 'Upozornenie PHP: $1',
	'webstore_metadata_not_found'      => 'Súbor nebol nájdený: $1',
	'webstore_postfile_not_found'      => 'Súbor na odoslanie nebol nájdený.',
	'webstore_scaler_empty_response'   => 'Zmena veľkosti obrázka vrátila prázdnu odpoveď s kódom 200. Toto by mohlo znamenať kritickú chybu PHP pri zmene veľkosti obrázka.',
	'webstore_invalid_response'        => 'Neplatná odpoveď od servera:

$1',
	'webstore_no_response'             => 'Žiadna odpoveď od servera',
	'webstore_backend_error'           => 'Chyba od úložného servera:

$1',
	'webstore_php_error'               => 'Vyskytli sa chyby PHP:',
	'webstore_no_handler'              => 'Pre transformáciu tohto typu MIME neexistuje obsluha',
);

/** Swedish (Svenska)
 * @author Max sonnelid
 */
$messages['sv'] = array(
	'webstore_invalid_zone' => 'Ogiltig zon "$1".',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'webstore_path_invalid'       => 'ఫైలుపేరు తప్పుగా ఉంది.',
	'webstore_dest_exists'        => 'పొరపాటు, "$1" అనే గమ్యస్థానపు ఫైలు ఇప్పటికే ఉంది.',
	'webstore_404'                => 'ఫైలు కనబడలేదు.',
	'webstore_php_warning'        => 'PHP హెచ్చరిక: $1',
	'webstore_metadata_not_found' => 'ఫైలు కనబడలేదు: $1',
	'webstore_no_response'        => 'సర్వరునుండి స్పందన లేదు',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$messages['tr'] = array(
	'webstore_404'                => 'Dosya bulunamadı.',
	'webstore_metadata_not_found' => '$1 dosyası bulunamadı',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'webstore_curl'        => 'Lỗi cURL: $1',
	'webstore_php_warning' => 'Cảnh báo PHP: $1',
);

