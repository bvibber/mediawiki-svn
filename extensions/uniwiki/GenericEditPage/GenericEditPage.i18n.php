<?php
/**
 * Internationalisation for Uniwiki/GenericEditPage extension
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English */
$messages['en'] = array(
	'gep-desc' => 'Supplements the edit page to be more user friendly',
	'gep-emailsubject' => '[$1] Category suggestion: $2',
	'gep-emailbody' => 'User "$1" suggested category "$2" for site "$3".',
	'gep-emailfailure' => 'Sorry, your suggestion could not be processed.',
	'gep-emailsuccess' => 'Thanks for suggesting the category: $1.',
	'gep-categories' => 'Categories',
	'gep-addcategory' => 'Add a category',
	'gep-addcategorybutton' => 'Add',
	'gep-suggestcategory' => 'Suggest a category',
	'gep-suggestcategorybutton' => 'Submit',
	'gep-sections' => 'Sections',
	'gep-sectionnotdisabled' => 'This section cannot be disabled',
	'gep-addsection' => 'Add a section',
	'gep-addsectionbutton' => 'Add',
	'gep-classicmode' => 'Classic mode',
	'gep-genericmode' => 'Generic mode',
	'gep-nosectioninstructions' => 'There are no sections on this page.
Add some using the tools on the right.', // on the right? How does that work in RTL UIs?
	'gep-nocategories' => 'Please select at least one category before saving',
	'gep-categorypage' => 'MediaWiki:Editpagetags',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ramsis II
 */
$messages['arz'] = array(
	'gep-emailsubject' => '[$1] اقتراح التصنيف: $2',
	'gep-emailbody' => 'اليوزر "$1" اقترح التصنيف"$2" للموقع"$3".',
	'gep-emailfailure' => 'متاسفين،الاقتراح بتاعك ماينفعش يتنفذ',
	'gep-emailsuccess' => 'شكرا لاقتراحك التصنيف : $1.',
	'gep-categories' => 'تصانيف',
	'gep-addcategory' => 'ضيف تصنيف',
	'gep-addcategorybutton' => 'ضيف',
	'gep-suggestcategory' => 'اقترح تصنيف',
	'gep-suggestcategorybutton' => 'تقديم',
	'gep-sections' => 'اقسام',
	'gep-sectionnotdisabled' => 'القسم دا ماينفعش يتعطل',
	'gep-addsection' => 'ضيف قسم',
	'gep-addsectionbutton' => 'ضيف',
	'gep-classicmode' => 'وضع كلاسيكي',
	'gep-genericmode' => 'الوضع العام',
	'gep-nosectioninstructions' => 'ضيف شوية باستعمال الادوات الموجودة على اليمين',
	'gep-nocategories' => 'لو سمحت تختار تصنيف واحد على الاقل قبل الحفظ',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'gep-emailbody' => 'Потребител „$1“ предложи категория „$2“ за сайта „$3“.',
	'gep-categories' => 'Категории',
	'gep-addcategory' => 'Добавяне на категория',
	'gep-addcategorybutton' => 'Добавяне',
	'gep-suggestcategorybutton' => 'Изпращане',
	'gep-categorypage' => 'MediaWiki:Editpagetags',
);

/** German (Deutsch) */
$messages['de'] = array(
	'gep-emailsubject' => '[$1] Vorschlag Kategorie: $2',
	'gep-emailbody' => 'Benutzer „$1“ hat die Kategorie „$2“ für die Seite „$3“ ausgewählt.',
	'gep-emailfailure' => 'Leider konnte dein Vorschlag nicht verarbeitet werden.',
	'gep-emailsuccess' => 'Danke für den Vorschlag der Kategorie: $1.',
	'gep-categories' => 'Kategorien',
	'gep-addcategory' => 'Eine Kategorie einfügen',
	'gep-addcategorybutton' => 'Einfügen',
	'gep-suggestcategory' => 'Eine Kategorie vorschlagen',
	'gep-suggestcategorybutton' => 'Senden',
	'gep-sections' => 'Abschnitte',
	'gep-sectionnotdisabled' => 'Dieser Abschnitt kann nicht aufgehoben werden',
	'gep-addsection' => 'Einen Abschnitt einfügen',
	'gep-addsectionbutton' => 'Einfügen',
	'gep-classicmode' => 'Normaler Modus',
	'gep-genericmode' => 'Genereller Modus',
	'gep-nosectioninstructions' => 'Diese Seite hat keine Abschnitte.
Gib ein paar ein mit den Werkzeugen an der rechten Seite.',
	'gep-nocategories' => 'Bitte vor dem Abspeichern mindestens eine Kategorie aussuchen',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'gep-emailsubject' => '[$1] Kategoria sugesto: $2',
	'gep-emailbody' => 'Uzanto "$1" sugestis kategorion "$2" por vikio "$3".',
	'gep-emailsuccess' => 'Dankon pro sugesti la kategorion: $1.',
	'gep-categories' => 'Kategorioj',
	'gep-addcategory' => 'Aldoni kategorion',
	'gep-addcategorybutton' => 'Aldoni',
	'gep-suggestcategory' => 'Sugesti kategorion',
	'gep-sections' => 'Sekcioj',
	'gep-addsection' => 'Aldoni sekcion',
	'gep-addsectionbutton' => 'Aldoni',
	'gep-classicmode' => 'Klasika regximo',
	'gep-genericmode' => 'Gxenerala regximo',
);

/** Spanish (Español) */
$messages['es'] = array(
	'gep-emailsubject' => 'Sugerencia de categoría de [$1]: $2',
	'gep-emailbody' => 'El usuario "$1" sugirió la categoría "$2" para el sitio "$3".',
	'gep-emailfailure' => 'No fue posible procesar su sugerencia.',
	'gep-emailsuccess' => 'Gracias por sugerir la categoría: $1.',
	'gep-categories' => 'Categorías',
	'gep-addcategory' => 'Agregar una Categoría',
	'gep-addcategorybutton' => 'Agregar',
	'gep-suggestcategory' => 'Sugerir una Categoría',
	'gep-suggestcategorybutton' => 'Enviar',
	'gep-sections' => 'Secciones',
	'gep-sectionnotdisabled' => 'Esta sección no se puede desactivar',
	'gep-addsection' => 'Agregar una Sección',
	'gep-addsectionbutton' => 'Agregar',
	'gep-classicmode' => 'Modo Clásico',
	'gep-genericmode' => 'Modo Genérico',
	'gep-nosectioninstructions' => 'No hay secciones en esta página.
Agrega algunas secciones utilizando las herramientas a la derecha.',
	'gep-nocategories' => 'Selecciona por lo menos una categoría antes de guardar',
);

/** French (Français)
 * @author Grondin
 * @author IAlex
 * @author Sherbrooke
 */
$messages['fr'] = array(
	'gep-desc' => 'Suppléer le formulaire de modification pour être plus facile à utiliser',
	'gep-emailsubject' => '[$1] Suggestion de catégorie : $2',
	'gep-emailbody' => "L'utilisateur « $1 » a suggéré la catégorie « $2 » pour le site « $3 ».",
	'gep-emailfailure' => "Désolé, votre suggestion n'a pas pu être effectuée.",
	'gep-emailsuccess' => 'Merci pour avoir suggéré la catégorie : $1.',
	'gep-categories' => 'Catégories',
	'gep-addcategory' => 'Ajouter une catégorie',
	'gep-addcategorybutton' => 'Ajouter',
	'gep-suggestcategory' => 'Suggérer une catégorie',
	'gep-suggestcategorybutton' => 'Soumettre',
	'gep-sections' => 'Sections',
	'gep-sectionnotdisabled' => 'Cette section ne peut pas être désactivée',
	'gep-addsection' => 'Ajouter une section',
	'gep-addsectionbutton' => 'Ajouter',
	'gep-classicmode' => 'Mode classique',
	'gep-genericmode' => 'Mode générique',
	'gep-nosectioninstructions' => "Il n'y a aucune section dans cette page.
Ajoutez-en en utilisant l'outil sur la droite.",
	'gep-nocategories' => 'Choisir au moins une catégorie avant de sauvegarder la page',
	'gep-categorypage' => 'MediaWiki:Editpagetags',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'gep-emailsuccess' => 'Grazas por suxerir a categoría: $1.',
	'gep-categories' => 'Categorías',
	'gep-addcategory' => 'Engadir unha categoría',
	'gep-addcategorybutton' => 'Engadir',
	'gep-suggestcategory' => 'Suxerir unha categoría',
	'gep-suggestcategorybutton' => 'Enviar',
	'gep-sections' => 'Seccións',
	'gep-sectionnotdisabled' => 'Esta sección non pode ser deshabilitada',
	'gep-addsection' => 'Engadir unha sección',
	'gep-addsectionbutton' => 'Engadir',
	'gep-classicmode' => 'Modo clásico',
	'gep-genericmode' => 'Modo xenérico',
	'gep-nosectioninstructions' => 'Non hai seccións nesta páxina.
Engada algunha usando as ferramentas da dereita.',
	'gep-nocategories' => 'Por favor, seleccione, polo menos, unha categoría antes de gardar',
	'gep-categorypage' => 'MediaWiki:Editpagetags',
);

/** Javanese (Basa Jawa)
 * @author Pras
 */
$messages['jv'] = array(
	'gep-desc' => 'Imbuhi kaca suntingan supaya luwih kepénak tumrap panganggo',
	'gep-emailsubject' => '[$1] Saran kategori: $2',
	'gep-emailbody' => 'Panganggo "$1" nyaranaké kategori "$2" kanggo situs "$3".',
	'gep-emailfailure' => 'Nyuwun pangapura, saran panjenengan ora bisa diprosès.',
	'gep-emailsuccess' => 'Matur nuwun wis nyaranaké kategori: $1.',
	'gep-categories' => 'Kategori',
	'gep-addcategory' => 'Nambahaké kategori',
	'gep-addcategorybutton' => 'Tambahaké',
	'gep-suggestcategory' => 'Saranaké kategori',
	'gep-suggestcategorybutton' => 'Kirim',
	'gep-sections' => 'Bagéan',
	'gep-sectionnotdisabled' => "Bagéan iki ora bisa dilumpuhaké (''disabled'')",
	'gep-addsection' => 'Tambahaké bagéan',
	'gep-addsectionbutton' => 'Tambahaké',
	'gep-classicmode' => 'Cara klasik',
	'gep-genericmode' => 'Cara generik',
	'gep-nosectioninstructions' => "Ora ana bagéan ing kaca iki.
Tambahaké kanthi migunakaké piranti (''tools'') sing ana ing tengen.",
	'gep-nocategories' => 'Pilih saora-orané siji kategori sadurungé nyimpen',
	'gep-categorypage' => 'MediaWiki:Tagkacasunting',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'gep-emailsubject' => '[$1] Virschlag Kategorie: $2',
	'gep-emailbody' => 'De Benotzer "$1" huet d\'Kategorie "$2" fir de Site "$3" virgeschlo.',
	'gep-emailfailure' => 'Pardon, Äre Virschlag konnt net verschafft ginn.',
	'gep-emailsuccess' => "Merci datt Dir d'Kategorie: $1 virgeschlo hutt",
	'gep-categories' => 'Kategorien',
	'gep-addcategory' => 'Eng Kategorie derbäisetzen',
	'gep-addcategorybutton' => 'Derbäisetzen',
	'gep-suggestcategory' => 'Eng Kategorie virschloen',
	'gep-suggestcategorybutton' => 'Schécken',
	'gep-sections' => 'Abschnitter',
	'gep-addsectionbutton' => 'Derbäisetzen',
	'gep-nocategories' => 'Wielt w.e.g. mindestens eng Kategorie aus ier Dir ofspäichert',
	'gep-categorypage' => 'MediaWiki:Säitenännerungs-Tagen',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'gep-desc' => 'Maakt de bewerkingspagina gebruikersvriendelijker',
	'gep-emailsubject' => '[$1] Categoriesuggestie: $2',
	'gep-emailbody' => 'Gebruiker "$1" heeft de categoriesuggestie "$2" gemaakt voor site "$3".',
	'gep-emailfailure' => 'Uw suggestie kon niet verwerkt worden.',
	'gep-emailsuccess' => 'Dank uw wel voor de categoriesuggestie $1.',
	'gep-categories' => 'Categorieën',
	'gep-addcategory' => 'Categorie toevoegen',
	'gep-addcategorybutton' => 'Toevoegen',
	'gep-suggestcategory' => 'Categoriesuggestie maken',
	'gep-suggestcategorybutton' => 'OK',
	'gep-sections' => 'Kopjes',
	'gep-sectionnotdisabled' => 'Dit kopje kan niet uitgeschakeld worden',
	'gep-addsection' => 'Kopje toevoegen',
	'gep-addsectionbutton' => 'Toevoegen',
	'gep-classicmode' => 'Klassieke modus',
	'gep-genericmode' => 'Algemene modus',
	'gep-nosectioninstructions' => 'Er staan geen kopjes op deze pagina.
Gebruik de hulpmiddelen aan de rechterkant om er een toe te voegen.',
	'gep-nocategories' => 'Selecteer tenminste één categorie voordat u de pagina opslaat',
	'gep-categorypage' => 'MediaWiki:Editpagelabels',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'gep-categories' => 'وېشنيزې',
);

/** Brazilian Portuguese (Português do Brasil) */
$messages['pt-br'] = array(
	'gep-emailsubject' => 'Sugestão de categoria de [$1] : $2',
	'gep-emailbody' => 'O usuário "$1" sugeriu a categoria "$2" para o site "$3".',
	'gep-emailfailure' => 'Não foi possível processar a sua sugestão.',
	'gep-emailsuccess' => 'Obrigado por sugerir essa categoria: $1.',
	'gep-categories' => 'Categorias',
	'gep-addcategory' => 'Adicionar uma Categoria',
	'gep-addcategorybutton' => 'Adicionar',
	'gep-suggestcategory' => 'Sugerir uma Categoria',
	'gep-suggestcategorybutton' => 'Encaminhar',
	'gep-sections' => 'Seções',
	'gep-sectionnotdisabled' => 'Esta seção não pode ser desativada',
	'gep-addsection' => 'Adicionar uma Seção',
	'gep-addsectionbutton' => 'Acrescentar',
	'gep-classicmode' => 'Modo Clássico',
	'gep-genericmode' => 'Modo Genérico',
	'gep-nosectioninstructions' => 'Não há seções nesta página.
Use as ferramentas à direita para incluir algumas seções.',
	'gep-nocategories' => 'Selecione, no mínimo, uma categoria antes de salvar',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'gep-desc' => 'Дополнения режима редактирования страницы, делающие его более удобным',
	'gep-emailsubject' => '[$1] Рекомендуемая категория: $2',
	'gep-emailbody' => 'Участник «$1» посоветовал категорию «$2» для сайта «$3».',
	'gep-emailfailure' => 'Извините, ваш совет не может быть обработан.',
	'gep-emailsuccess' => 'Спасибо, что посоветовали категорию: $1.',
	'gep-categories' => 'Категории',
	'gep-addcategory' => 'Добавить категорию',
	'gep-addcategorybutton' => 'Добавить',
	'gep-suggestcategory' => 'Посоветовать категорию',
	'gep-suggestcategorybutton' => 'Отправить',
	'gep-sections' => 'Разделы',
	'gep-sectionnotdisabled' => 'Этот раздел не может быть отключён',
	'gep-addsection' => 'Добавить раздел',
	'gep-addsectionbutton' => 'Добавить',
	'gep-classicmode' => 'Классический режим',
	'gep-genericmode' => 'Общий режим',
	'gep-nosectioninstructions' => 'На этой странице нет разделов.
Вы можете их добавить с помощью инструментов, размещённых справа.',
	'gep-nocategories' => 'Пожалуйста, выберите перед сохранением хотя бы одну категорию',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'gep-desc' => 'Dopĺňa stránku na úpravy, aby bola používateľsky príjemnejšia',
	'gep-emailsubject' => '[$1] Návrh kategórie: $2',
	'gep-emailbody' => 'Používateľ „$1“ navrhol kategóriu „$2“ pre stránku „$3“.',
	'gep-emailfailure' => 'Prepáčte, váš návrh nebolo možné spracovať.',
	'gep-emailsuccess' => 'Ďakujeme za váš návrh kategórie: $1.',
	'gep-categories' => 'Kategórie',
	'gep-addcategory' => 'Pridať kategóriu',
	'gep-addcategorybutton' => 'Pridať',
	'gep-suggestcategory' => 'Navrhnúť kategóriu',
	'gep-suggestcategorybutton' => 'Odoslať',
	'gep-sections' => 'Sekcie',
	'gep-sectionnotdisabled' => 'Túto sekciu nemožno vypnúť',
	'gep-addsection' => 'Pridať sekciu',
	'gep-addsectionbutton' => 'Pridať',
	'gep-classicmode' => 'Klasický režim',
	'gep-genericmode' => 'Všeobecný režim',
	'gep-nosectioninstructions' => 'Na tejto stránke nie sú žiadne sekcie.
Pomocou nástrojov vpravo ich môžete pridať.',
	'gep-nocategories' => 'Prosím, vyberte pred uložením aspoň jednu kategóriu.',
	'gep-categorypage' => 'MediaWiki:Editpagetags',
);

/** Swedish (Svenska)
 * @author Najami
 */
$messages['sv'] = array(
	'gep-categories' => 'Kategorier',
	'gep-addcategory' => 'Lägg till en kategori',
	'gep-addcategorybutton' => 'Lägg till',
	'gep-addsection' => 'Lägg till ett avsnitt',
	'gep-addsectionbutton' => 'Lägg till',
);

