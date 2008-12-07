<?php
/**
 * Internationalisation for RecordAdmin extension
 *
 * @author Bertrand GRONDIN
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Nad
 * @author Bertrand GRONDIN
 */
$messages['en'] = array(
	'recordadmin' => 'Record administration',
	'recordadmin-desc' => 'A [[Special:RecordAdmin|special page]] for finding and editing record pages using a form',
	'recordadmin-category' => 'Records',
	'recordadmin-select' => 'Select the type of record to search for ',
	'recordadmin-newsearch' => 'New $1 search',
	'recordadmin-newrecord' => 'Select another record type',
	'recordadmin-submit' => 'Submit',
	'recordadmin-create' => 'Find or create a "$1" record',
	'recordadmin-alreadyexist' => 'Sorry, "$1" already exists!',
	'recordadmin-createsuccess' => '$1 created',
	'recordadmin-createerror' => 'An error occurred while attempting to create the $1!',
	'recordadmin-badtitle' => 'Bad title!',
	'recordadmin-recordid' => 'Record ID:',
	'recordadmin-invert' => 'Invert selection',
	'recordadmin-buttonsearch' => 'Search',
	'recordadmin-buttoncreate' => 'Create',
	'recordadmin-buttonreset' => 'Reset',
	'recordadmin-searchresult' => 'Search results',
	'recordadmin-nomatch' => 'No matching records found!',
	'recordadmin-edit' => 'Editing $1',
	'recordadmin-typeupdated' => '$1 properties updated',
	'recordadmin-updatesuccess' => '$1 updated',
	'recordadmin-updateerror' => 'An error occurred during update',
	'recordadmin-buttonsave' => 'Save',
	'recordadmin-noform' => 'There is no form associated with "$1" records!',
	'recordadmin-createlink' => '<a href=$1>create one</a>',
	'recordadmin-newcreated' => 'New $1 created from public form',
	'recordadmin-summary-typecreated' => 'New $1 created',
	'recordadmin-viewlink' => 'view',
	'recordadmin-editlink' => 'edit',
	'recordadmin-created' => 'Created',
	'right-recordadmin' => 'Find and edit record pages',
);

/** Message documentation (Message documentation) */
$messages['qqq'] = array(
	'recordadmin-category' => 'Category which contains the templates used as records and having corresponding forms',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'recordadmin-submit' => 'Изпращане',
	'recordadmin-invert' => 'Обръщане на избора',
	'recordadmin-buttonsearch' => 'Търсене',
	'recordadmin-buttoncreate' => 'Създаване',
	'recordadmin-edit' => 'Редактиране на $1',
	'recordadmin-updateerror' => 'Възникна грешка по време на обновяването',
	'recordadmin-buttonsave' => 'Съхраняване',
	'recordadmin-viewlink' => 'преглеждане',
	'recordadmin-editlink' => 'редактиране',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'recordadmin-category' => 'Rekordoj',
	'recordadmin-newsearch' => 'Nova $1 serĉo',
	'recordadmin-createsuccess' => '$1 kreita',
	'recordadmin-badtitle' => 'Fuŝa titolo!',
	'recordadmin-invert' => 'Inversigi selekton',
	'recordadmin-buttonsearch' => 'Serĉi',
	'recordadmin-buttoncreate' => 'Krei',
	'recordadmin-edit' => 'Redaktante $1',
	'recordadmin-buttonsave' => 'Konservi',
	'recordadmin-summary-typecreated' => 'Nova $1 kreita',
	'recordadmin-viewlink' => 'vidi',
	'recordadmin-editlink' => 'redakti',
	'recordadmin-created' => 'Kreita',
);

/** French (Français)
 * @author Grondin
 */
$messages['fr'] = array(
	'recordadmin' => 'Gestion des enregistrements',
	'recordadmin-desc' => 'Une page spéciale pour trouver et modifier l’enregistrement des pages par l’utilisation d’un formulaire',
	'recordadmin-category' => 'Enregistrements',
	'recordadmin-select' => 'Sélectionner le type d’enregistrement à rechercher pour',
	'recordadmin-newsearch' => 'Nouvelle recherche $1',
	'recordadmin-newrecord' => 'Sélectionner un autre type d’enregistrement',
	'recordadmin-submit' => 'Soumettre',
	'recordadmin-create' => 'Chercher ou créer un enregistrement « $1 »',
	'recordadmin-alreadyexist' => 'Désolé, « $1 » existe déjà !',
	'recordadmin-createsuccess' => '$1 creé avec succès',
	'recordadmin-createerror' => 'Une erreur est intervenue lors de la tentative de création de $1 !',
	'recordadmin-badtitle' => 'Mauvais titre!',
	'recordadmin-recordid' => 'Enregistrement ID :',
	'recordadmin-invert' => 'Inverser la sélection',
	'recordadmin-buttonsearch' => 'Rechercher',
	'recordadmin-buttoncreate' => 'Créer',
	'recordadmin-buttonreset' => 'Réinitialiser',
	'recordadmin-searchresult' => 'Résultats de la recherche',
	'recordadmin-nomatch' => 'Aucun enregistrement correspondant de trouvé !',
	'recordadmin-edit' => 'Modifier $1',
	'recordadmin-typeupdated' => 'propriété de $1 mises à jour',
	'recordadmin-updatesuccess' => '$1 mis à jour avec succès',
	'recordadmin-updateerror' => 'Une erreur a été rencontrée lors de la mise à jour',
	'recordadmin-buttonsave' => 'Sauvegarder',
	'recordadmin-noform' => 'Il n’y a aucun formulaire avec l’enregistrement « $1 » !',
	'recordadmin-createlink' => 'cliquez <a href=$1>ici</a> pour en créer un',
	'recordadmin-newcreated' => 'Nouveau $1 créé à partir d’un formulaire public',
	'recordadmin-summary-typecreated' => 'Nouveau $1 de créer',
	'recordadmin-viewlink' => 'voir',
	'recordadmin-editlink' => 'modifier',
	'recordadmin-created' => 'Créé',
	'right-recordadmin' => 'Trouver et modifier les pages d’enregistrement',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'recordadmin' => 'Xestión dos rexistros',
	'recordadmin-desc' => 'Unha [[Special:RecordAdmin|páxina especial]] para atopar e editar páxinas de rexistros usando un formulario',
	'recordadmin-category' => 'Rexistros',
	'recordadmin-select' => 'Seleccione o tipo de rexistro para procurar por',
	'recordadmin-newsearch' => 'Nova procura $1',
	'recordadmin-newrecord' => 'Seleccione outro tipo de rexistro',
	'recordadmin-submit' => 'Enviar',
	'recordadmin-create' => 'Atopar ou crear un rexistro "$1"',
	'recordadmin-alreadyexist' => 'Sentímolo, "$1" xa existe!',
	'recordadmin-createsuccess' => '"$1" creado',
	'recordadmin-createerror' => 'Ocorreu un erro ao intentar crear o $1!',
	'recordadmin-badtitle' => 'Título incorrecto!',
	'recordadmin-recordid' => 'ID do rexistro:',
	'recordadmin-invert' => 'Invertir a selección',
	'recordadmin-buttonsearch' => 'Procurar',
	'recordadmin-buttoncreate' => 'Crear',
	'recordadmin-buttonreset' => 'Restablecer',
	'recordadmin-searchresult' => 'Resultados da procura',
	'recordadmin-nomatch' => 'Non se atoparon rexistros que coincidisen!',
	'recordadmin-edit' => 'Editando "$1"',
	'recordadmin-typeupdated' => 'Propiedades de "$1" actualizadas',
	'recordadmin-updatesuccess' => '"$1" actualizado',
	'recordadmin-updateerror' => 'Ocorreu un erro durante a actualización',
	'recordadmin-buttonsave' => 'Gardar',
	'recordadmin-noform' => 'Non hai ningún formulario asociado cos rexistros "$1"!',
	'recordadmin-createlink' => '<a href=$1>crear un</a>',
	'recordadmin-newcreated' => 'Novo $1 creado a partir dun formulario público',
	'recordadmin-summary-typecreated' => 'Novo $1 creado',
	'recordadmin-viewlink' => 'ver',
	'recordadmin-editlink' => 'editar',
	'recordadmin-created' => 'Creado',
	'right-recordadmin' => 'Atopar e editar páxinas de rexistro',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Lovekhmer
 */
$messages['km'] = array(
	'recordadmin-buttonsearch' => 'ស្វែងរក',
	'recordadmin-buttoncreate' => 'បង្កើត',
	'recordadmin-searchresult' => 'លទ្ធផល​ស្វែងរក',
	'recordadmin-edit' => 'កំពុង​កែប្រែ​ $1',
	'recordadmin-buttonsave' => 'រក្សាទុក',
	'recordadmin-viewlink' => 'មើល',
	'recordadmin-editlink' => 'កែប្រែ',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'recordadmin-newsearch' => 'Nei $1 sichen',
	'recordadmin-alreadyexist' => 'Pardon, "$1" gëtt et schonn!',
	'recordadmin-buttonsearch' => 'Sichen',
	'recordadmin-buttonreset' => 'Zrécksetzen',
	'recordadmin-searchresult' => 'Resultater vun der Sich',
	'recordadmin-edit' => '$1 änneren',
	'recordadmin-updatesuccess' => '$1 ass aktualiséiert',
	'recordadmin-updateerror' => 'Beim Aktualiséieren ass e Feeler geschitt',
	'recordadmin-buttonsave' => 'Späicheren',
	'recordadmin-viewlink' => 'weisen',
	'recordadmin-editlink' => 'änneren',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'recordadmin' => 'Gegevensbeheer',
	'recordadmin-desc' => "Een [[Special:RecordAdmin|speciale pagina]] voor het zoeken en bewerken van gegevenspagina's die een formulier gebruiken",
	'recordadmin-category' => 'Gegevens',
	'recordadmin-select' => 'Geef aan welk type gegeven te zoeken',
	'recordadmin-newsearch' => 'Nieuwe zoekopdracht voor $1',
	'recordadmin-newrecord' => 'Een ander gegevenstype selecteren',
	'recordadmin-submit' => 'OK',
	'recordadmin-create' => 'Een gegeven van het type "$1" zoeken of aanmaken',
	'recordadmin-alreadyexist' => '"$1" bestaat al!',
	'recordadmin-createsuccess' => '$1 aangemaakt',
	'recordadmin-createerror' => 'Er is een fout opgetreden bij het aanmaken van het $1.',
	'recordadmin-badtitle' => 'Onjuiste paginanaam!',
	'recordadmin-recordid' => 'Gegevensnummer:',
	'recordadmin-invert' => 'Omgekeerde selectie',
	'recordadmin-buttonsearch' => 'Zoeken',
	'recordadmin-buttoncreate' => 'Aanmaken',
	'recordadmin-buttonreset' => 'Fomulier wissen',
	'recordadmin-searchresult' => 'Zoekresultaten',
	'recordadmin-nomatch' => 'Er zijn geen gegevens gevonden die aan de voorwaarden voldoen.',
	'recordadmin-edit' => 'Bezig met bewerken van $1',
	'recordadmin-typeupdated' => 'De eigenschappen van $1 zijn bijgewerkt',
	'recordadmin-updatesuccess' => '$1 is bijgewerkt',
	'recordadmin-updateerror' => 'Er is een fout opgetreden tijdens het bijwerken',
	'recordadmin-buttonsave' => 'Opslaan',
	'recordadmin-noform' => 'Er is geen formulier gekoppeld aan gegevens van het type "$1"!',
	'recordadmin-createlink' => '<a href=$1>aanmaken</a>',
	'recordadmin-newcreated' => 'Nieuwe $1 aangemaakt van openbaar formulier',
	'recordadmin-summary-typecreated' => 'Nieuwe $1 aangemaakt',
	'recordadmin-viewlink' => 'bekijken',
	'recordadmin-editlink' => 'bewerken',
	'recordadmin-created' => 'Aangemaakt',
	'right-recordadmin' => "Gegevenspagina's zoeken en bewerken",
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'recordadmin' => 'Gestion dels enregistraments',
	'recordadmin-desc' => 'Una pagina especiala per trobar e modificar l’enregistrament de las paginas per l’utilizacion d’un formulari',
	'recordadmin-category' => 'Enregistraments',
	'recordadmin-select' => 'Seleccionar lo tipe d’enregistrament de recercar per',
	'recordadmin-newsearch' => 'Recèrca novèla $1',
	'recordadmin-newrecord' => 'Seleccionar un autre tipe d’enregistrament',
	'recordadmin-submit' => 'Sometre',
	'recordadmin-create' => 'Cercar o crear un enregistrament « $1 »',
	'recordadmin-alreadyexist' => 'O planhèm, « $1 » existís ja !',
	'recordadmin-createsuccess' => '$1 creat amb succès',
	'recordadmin-createerror' => 'Una error es intervenguda al moment de la temptativa de creacion de $1 !',
	'recordadmin-badtitle' => 'Títol marrit!',
	'recordadmin-recordid' => 'Enregistrament ID :',
	'recordadmin-invert' => 'Inversar la seleccion',
	'recordadmin-buttonsearch' => 'Recercar',
	'recordadmin-buttoncreate' => 'Crear',
	'recordadmin-buttonreset' => 'Tornar inicializar',
	'recordadmin-searchresult' => 'Resultats de la recèrca',
	'recordadmin-nomatch' => "Cap d'enregistrament correspondent pas trobat !",
	'recordadmin-edit' => 'Modificar $1',
	'recordadmin-typeupdated' => 'proprietat de $1 mesas a jorn',
	'recordadmin-updatesuccess' => '$1 mes a jorn amb succès',
	'recordadmin-updateerror' => 'Una error es estat rencontrada al moment de la mesa a jorn',
	'recordadmin-buttonsave' => 'Salvar',
	'recordadmin-noform' => 'I a pas cap de formulari amb l’enregistrament « $1 » !',
	'recordadmin-createlink' => 'clicatz <a href=$1>aicí</a> per ne crear un',
	'recordadmin-newcreated' => '$1 novèl creat a partir d’un formulari public',
	'recordadmin-summary-typecreated' => '$1 novèl de crear',
	'recordadmin-viewlink' => 'veire',
	'recordadmin-editlink' => 'modificar',
	'recordadmin-created' => 'Creat',
	'right-recordadmin' => 'Trobar e modificar las paginas d’enregistrament',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'recordadmin' => 'Správa záznamov',
	'recordadmin-desc' => '[[Special:RecordAdmin|Špeciálna stránka]] na hľadanie a úpravu stránok záznamov pomocou fomulára',
	'recordadmin-category' => 'Záznamy',
	'recordadmin-select' => 'Vyberte typ záznamu, ktorý sa má hľadať',
	'recordadmin-newsearch' => 'Nové hľadanie $1',
	'recordadmin-newrecord' => 'Vyberte iný typ záznamu',
	'recordadmin-submit' => 'Odoslať',
	'recordadmin-create' => 'Nájsť alebo vytvoriť záznam „$1“',
	'recordadmin-alreadyexist' => 'Ľutujeme, „$1“ už existuje.',
	'recordadmin-createsuccess' => '„$1“ vytvorený',
	'recordadmin-createerror' => 'Vyskytla sa chyba pri pokuse o vytvorenie „$1“.',
	'recordadmin-badtitle' => 'Chybný názov!',
	'recordadmin-recordid' => 'ID záznamu:',
	'recordadmin-invert' => 'Invertovať výber',
	'recordadmin-buttonsearch' => 'Hľadať',
	'recordadmin-buttoncreate' => 'Vytvoriť',
	'recordadmin-buttonreset' => 'Reset',
	'recordadmin-searchresult' => 'Výsledky hľadania',
	'recordadmin-nomatch' => 'Neboli nájdené žiadne zodpovedajúce záznamy!',
	'recordadmin-edit' => 'Upravuje sa $1',
	'recordadmin-typeupdated' => 'vlastnosti $1 aktualizované',
	'recordadmin-updatesuccess' => '$1 aktualizované',
	'recordadmin-updateerror' => 'Počas aktualizácie sa vyskytla chyba',
	'recordadmin-buttonsave' => 'Uložiť',
	'recordadmin-noform' => 'So záznamami „$1“ nie je asociovaný žiadny formulár!',
	'recordadmin-createlink' => '<a href=$1>vytvoriť ho</a>',
	'recordadmin-newcreated' => 'Nový $1 vytvorený z verejného fóra',
	'recordadmin-summary-typecreated' => 'Nový $1 vytvorený',
	'recordadmin-viewlink' => 'zobraziť',
	'recordadmin-editlink' => 'upraviť',
	'recordadmin-created' => 'Vytvorené',
	'right-recordadmin' => 'Nájsť a upravovať stránky záznamov',
);

/** Vietnamese (Tiếng Việt)
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'recordadmin' => 'Quản lý bản ghi',
	'recordadmin-desc' => 'Một [[Special:RecordAdmin|trang đặc biệt]] để tìm kiếm và sửa đổi các trang bản ghi bằng cách dùng một mẫu có sẵn',
	'recordadmin-category' => 'Bản ghi',
	'recordadmin-select' => 'Chọn một loại bản ghi cần tìm',
	'recordadmin-newsearch' => 'Tìm $1 mới',
	'recordadmin-newrecord' => 'Chọn một loại bản ghi khác',
	'recordadmin-submit' => 'Đăng',
	'recordadmin-create' => 'Tìm hoặc tạo một bản ghi “$1”',
	'recordadmin-alreadyexist' => 'Xin lỗi,  	 	 	“$1” đã tồn tại!',
);

