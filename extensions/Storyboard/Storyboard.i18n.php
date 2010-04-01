<?php
/**
 * Internationalization file for the Storyboard extension.
 *
 * @file Storyboard.i18n.php
 * @ingroup Storyboard
 *
 * @author Jeroen De Dauw
 */

$messages = array();

/** English
 * @author Jeroen De Dauw
 */
$messages['en'] = array(
	// General
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Provides a landing page for donors, a page where stories can be submitted, and a story moderation interface',
	'right-storyreview' => 'Review, edit, publish, and hide stories',
	'specialpages-group-contribution' => 'Contribution',

	// Special:Story
	'story' => 'Story',
	'storyboard-viewstories' => 'View stories',
	'storyboard-nosuchstory' => 'The story you requested does not exist. It might have been removed.',
	'storyboard-unpublished' => 'The story you requested has not been published yet.',
	'storyboard-nostorytitle' => 'You need to specify the title or id of the story you want to view.',
	'storyboard-cantedit' => 'You are not allowed to edit stories.',
	'storyboard-canedit' => 'You can $1 and publish this story.',
	'storyboard-ispublished' => 'This story has been published and is publicly visible.',
	'storyboard-ishidden' => 'This story has been hidden from the story review interface.', 

	// Story review
	'storyreview' => 'Story review',
	'storyboard-publish' => 'Publish',
	'storyboard-unpublish' => 'Unpublish',
	'storyboard-reviewed' => 'Reviewed',
	'storyboard-unreviewed' => 'Unreviewed',
	'storyboard-hideimage' => 'Hide image',
	'storyboard-unhideimage' => 'Show image',
	'storyboard-deleteimage' => 'Delete image',

	// Story submission
	'storyboard-yourname' => 'Your name',
	'storyboard-location' => 'Your location',
	'storyboard-occupation' => 'Your occupation',
	'storyboard-story' => "Your story",
	'storyboard-photo' => 'Have a photo of yourself? Why not share it?',
	'storyboard-contact' => 'Your E-mail address or telephone number',
	'storyboard-storytitle' => 'A short, descriptive title',
	'storyboard-agreement' => 'I agree with the publication of this story.',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|character|characters}} left)',
	'storyboard-cannotbelonger' => 'Your story is <b>$1</b> {{PLURAL:$1|character|characters}} to long!',
	'storyboard-charsneeded' => '($1 more {{PLURAL:$1|character|characters}} needed)',
	'storyboard-needtoagree' => 'You need to agree to the publication of your story to submit it.',
	'storyboard-createdsucessfully' => 'Your story titled "$1" has been submitted sucesfully. It will be publicly accessible once it has been reviewed. View my story now.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'storyboard-name' => 'Storiebord',
	'storyboard-desc' => "Bied 'n landingsbladsy vir skenkers, 'n bladsy waar verhale ingestuur kan word en 'n koppelvlak om stories te beheer",
	'right-storyreview' => 'Hersien, wysig, publiseer en verberg stories',
	'storyboard-publish' => 'Publiseer',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'storyboard-name' => 'Дошка гісторыяў',
	'storyboard-desc' => 'Прадстаўляе старонку для донараў, старонку для іх гісторыяў, і інтэрфэйс яе мадэрацыі',
	'right-storyreview' => 'рэцэнзаваньне, рэдагаваньне, публікацыя і хаваньне гісторыяў',
	'storyboard-storyreview' => 'Рэцэнзаваць гісторыю',
	'storyboard-publish' => 'Апублікаваць',
	'storyboard-unpublish' => 'Прыбраць',
	'storyboard-reviewed' => 'Рэцэнзаваная',
	'storyboard-unreviewed' => 'Не рэцэнзаваная',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'storyboard-name' => 'Storyboard',
	'right-storyreview' => 'Adlenn, kemmañ, embann, ha kuzhat an istorioù',
	'storyboard-storyreview' => 'Adweladenn an istor',
	'storyboard-publish' => 'Embann',
	'storyboard-unpublish' => 'Diembann',
	'storyboard-reviewed' => 'Adwelet',
	'storyboard-unreviewed' => 'Nann-adwelet',
);

/** Finnish (Suomi)
 * @author Centerlink
 * @author Crt
 */
$messages['fi'] = array(
	'right-storyreview' => 'Tarkistaa, muokata, julkaista ja piilotaa tarinoita',
);

/** French (Français)
 * @author IAlex
 * @author Peter17
 */
$messages['fr'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => "Offre une page d'atterrissage pour les donateurs où les histoires peuvent être soumises et une interface de modération des histoires",
	'right-storyreview' => 'Relire, modifier, publier, et masquer les histoires',
	'storyboard-storyreview' => "Relecture de l'histoire",
	'storyboard-publish' => 'Publier',
	'storyboard-unpublish' => 'Dépublier',
	'storyboard-reviewed' => 'Révisée',
	'storyboard-unreviewed' => 'Non-révisée',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'storyboard-name' => 'Taboleiro de historias',
	'storyboard-desc' => 'Proporciona unha páxina de chegada para os doantes, unha páxina desde a que se poden enviar historias e unha interface para moderar o seu envío',
	'right-storyreview' => 'Revisar, editar, publicar e agochar historias',
	'storyboard-storyreview' => 'Revisión da historia',
	'storyboard-publish' => 'Publicar',
	'storyboard-unpublish' => 'Non publicar',
	'storyboard-reviewed' => 'Revisada',
	'storyboard-unreviewed' => 'Non revisada',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Stellt e Ziilsyte z Verfiegig fir Spänder, wu Gschichte chenne yygee wäre un e Benutzeroberflechi fir d Moderation vu Gschichte',
	'right-storyreview' => 'Gschichte priefe, bearbeite un uusblände',
	'storyboard-storyreview' => 'Gschicht beurteilde',
	'storyboard-publish' => 'Vereffetlige',
	'storyboard-unpublish' => 'Vereffetlichung zruckneh',
	'storyboard-reviewed' => 'Prieft',
	'storyboard-unreviewed' => 'Nit prieft',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'storyboard-publish' => 'Közzététel',
	'storyboard-unpublish' => 'Közzététel visszavonása',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Provide un pagina de arrivata pro donatores, un pagina ubi historias pote esser submittite, e un interfacie pro moderation de historias.',
	'right-storyreview' => 'Revider, modificar, publicar e celar historias',
	'storyboard-storyreview' => 'Recension de historias',
	'storyboard-publish' => 'Publicar',
	'storyboard-unpublish' => 'Dispublicar',
	'storyboard-reviewed' => 'Recensite',
	'storyboard-unreviewed' => 'Non recensite',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Stellt eng Entréessäit fir Donateuren zur Verfügung, eng Säit wou Anekdoten presentéiert kënne ginn, an e Moderatiouns-Interface fir Anekdoten',
	'right-storyreview' => 'Anekdoten nokucken, änneren, publizéieren a verstoppen',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'storyboard-name' => 'Раскажувачница',
	'storyboard-desc' => 'Ги носи дарителите на страница кадешто да испратат своја приказна. Содржи и интерфејс за модерирање на приказните',
	'right-storyreview' => 'Прегледување, уредување, објавување и сокривање на приказни',
	'storyboard-storyreview' => 'Проверка на напис',
	'storyboard-publish' => 'Објави',
	'storyboard-unpublish' => 'Тргни',
	'storyboard-reviewed' => 'Проверена',
	'storyboard-unreviewed' => 'Непроверена',
);

/** Dutch (Nederlands)
 * @author McDutchie
 * @author Siebrand
 */
$messages['nl'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Biedt een aankomstpagina voor donateurs, een pagina waar verhalen ingezonden kunnen worden en een interface voor beoordeling van verhalen',
	'right-storyreview' => 'Verhalen beoordelen, bewerken, publiceren en verbergen',
	'storyboard-storyreview' => 'Verhalen beoordelen',
	'storyboard-publish' => 'Publiceren',
	'storyboard-unpublish' => 'Publicatie terugtrekken',
	'storyboard-reviewed' => 'Beoordeeld',
	'storyboard-unreviewed' => 'Niet beoordeeld',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Nghtwlkr
 */
$messages['no'] = array(
	'storyboard-publish' => 'Publiser',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => "Ofrís una pagina d'aterrissatge pels donators ont las istòrias pòdon èsser somesas e una interfàcia de moderacion de las istòrias",
	'right-storyreview' => 'Relegir, modificar, publicar, e amagar las istòrias',
	'storyboard-storyreview' => "Relectura de l'istòria",
	'storyboard-publish' => 'Publicar',
	'storyboard-unpublish' => 'Despublicar',
	'storyboard-reviewed' => 'Relegit',
	'storyboard-unreviewed' => 'Pas revisada',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'storyboard-name' => 'Disegn ëd la senegiadura',
	'storyboard-desc' => "A dà na pàgina d'ariv për ij donator, na pàgina andoa le stòrie a peulo esse butà, e n'antërfacia ëd moderassion dle stòrie",
	'right-storyreview' => 'Lese torna, modifiché, publiché e stërmé le stòrie',
	'storyboard-storyreview' => 'Riletura ëd la stòria',
	'storyboard-publish' => 'Publiché',
	'storyboard-unpublish' => 'Pùblica pa',
	'storyboard-reviewed' => 'Revisionà',
	'storyboard-unreviewed' => 'Pa revisionà',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'storyboard-name' => 'Доска историй',
	'storyboard-desc' => 'Предоставляет страницу для размещения историй жертвователей, а также интерфейс её модерации',
	'right-storyreview' => 'проверка, правка, публикация и сокрытие историй',
	'storyboard-storyreview' => 'Проверка историй',
	'storyboard-publish' => 'Опубликовать',
	'storyboard-unpublish' => 'Убрать',
	'storyboard-reviewed' => 'Проверенных',
	'storyboard-unreviewed' => 'Непроверенных',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'storyboard-publish' => 'ప్రచురించు',
);

