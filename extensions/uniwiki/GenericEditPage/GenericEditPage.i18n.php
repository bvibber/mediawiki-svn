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
	'gep-categories' => 'تصانيف',
	'gep-addcategory' => 'ضيف تصنيف',
	'gep-addcategorybutton' => 'ضيف',
	'gep-suggestcategory' => 'اقترح تصنيف',
	'gep-addsectionbutton' => 'ضيف',
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
 * @author IAlex
 */
$messages['fr'] = array(
	'gep-desc' => 'Suppléante le formulaire de modification pour être plus facile à utiliser',
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
	'gep-nocategories' => 'Choisissez au moins une catégorie avant de sauvegarder la page',
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
	'gep-addcategory' => 'Nambahaké kategori',
	'gep-addcategorybutton' => 'Tambahaké',
	'gep-suggestcategorybutton' => 'Kirim',
	'gep-addsectionbutton' => 'Tambahaké',
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

