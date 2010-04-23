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
	'storyboard-desc' => 'Provides a [[Special:Story|landing page for donors]], a page where stories can be [[Special:StorySubmission|submitted]] and a [[Special:StoryReview|story moderation interface]]',
	'right-storyreview' => 'Review, edit, publish, and hide stories',
	'specialpages-group-contribution' => 'Contribution',

	// Story states
	'storyboard-unpublished' => 'Unpublished',
	'storyboard-published' => 'Published',
	'storyboard-hidden' => 'Hidden',
	'storyboard-unpublish' => 'Unpublish',
	'storyboard-publish' => 'Publish',
	'storyboard-hide' => 'Hide',
		
	// Special:Story
	'story' => 'Story',
	'storyboard-submittedbyon' => 'Submitted by $1 on $2, $3.',
	'storyboard-viewstories' => 'View stories',
	'storyboard-nosuchstory' => 'The story you requested does not exist.
It might have been removed.',
	'storyboard-storyunpublished' => 'The story you requested has not been published yet.',
	'storyboard-nostorytitle' => 'You need to specify the title or ID of the story you want to view.',
	'storyboard-cantedit' => 'You are not allowed to edit stories.',
	'storyboard-canedit' => 'You can [$1 edit] and publish this story.',
	'storyboard-createdandmodified' => 'Created on $1, $2 and last modified on $3, $4',
	'storyboard-authorname' => 'Author name',
	'storyboard-authorlocation' => 'Author location',
	'storyboard-authoroccupation' => 'Author occupation',
	'storyboard-authoremail' => 'Author e-mail address',
	'storyboard-thestory' => 'The story',
	'storyboard-storystate' => 'State:',

	// Special:StorySubmission
	'storyboard-submissioncomplete' => 'Submission complete',

	// Story review
	'storyreview' => 'Story review',
	'storyboard-hideimage' => 'Hide image',
	'storyboard-unhideimage' => 'Show image',
	'storyboard-deleteimage' => 'Delete image',

	// Story submission
	'storyboard-yourname' => 'Your name',
	'storyboard-location' => 'Your location',
	'storyboard-occupation' => 'Your occupation',
	'storyboard-story' => 'Your story',
	'storyboard-photo' => 'Have a photo of yourself?
Why not share it?',
	'storyboard-email' => 'Your e-mail address',
	'storyboard-storytitle' => 'A short, descriptive title:',
	'storyboard-agreement' => 'I agree with the publication and use of this story under the terms of the [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|character|characters}} left)',
	'storyboard-cannotbelonger' => 'Your story is <b>$1</b> {{PLURAL:$1|character|characters}} too long!',
	'storyboard-charsneeded' => '($1 more {{PLURAL:$1|character|characters}} needed)',
	'storyboard-needtoagree' => 'You need to agree to the publication of your story to submit it.',
	'storyboard-createdsucessfully' => 'Thank you for sharing your story with us!
We will review it shortly.
You can [$1 read published stories].',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'storyboard-name' => 'Storiebord',
	'storyboard-desc' => "Bied 'n landingsbladsy vir skenkers, 'n bladsy waar verhale ingestuur kan word en 'n koppelvlak om stories te beheer",
	'right-storyreview' => 'Hersien, wysig, publiseer en verberg stories',
	'specialpages-group-contribution' => 'Skenkings/Fondsinsameling',
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
	'specialpages-group-contribution' => 'Ахвяраваньні/Збор сродкаў',
	'storyboard-unpublish' => 'Прыбраць',
	'storyboard-publish' => 'Апублікаваць',
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
 * @author Jean-Frédéric
 * @author Peter17
 * @author PieRRoMaN
 */
$messages['fr'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => "Offre une page d'atterrissage pour les donateurs où les histoires peuvent être soumises et une interface de modération des histoires",
	'right-storyreview' => 'Relire, modifier, publier, et masquer les histoires',
	'specialpages-group-contribution' => 'Contributions / levée de fonds',
	'storyboard-unpublished' => 'Non publié',
	'storyboard-hidden' => 'Masqué',
	'storyboard-unpublish' => 'Dépublier',
	'storyboard-publish' => 'Publier',
	'storyboard-hide' => 'Masquer',
	'storyboard-yourname' => 'Votre nom',
	'storyboard-location' => 'Où habitez-vous ?',
	'storyboard-occupation' => 'Votre métier',
	'storyboard-story' => 'Quelle est votre histoire',
	'storyboard-photo' => 'Vous avez une photo de vous-même ? Pourquoi ne pas la partager ?',
	'storyboard-agreement' => "Je suis d'accord avec la publication de cette histoire.",
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'storyboard-name' => 'Taboleiro de historias',
	'storyboard-desc' => 'Proporciona unha páxina de chegada para os doantes, unha páxina desde a que se poden enviar historias e unha interface para moderar o seu envío',
	'right-storyreview' => 'Revisar, editar, publicar e agochar historias',
	'specialpages-group-contribution' => 'Contribucións/Recadación',
	'storyboard-unpublish' => 'Non publicar',
	'storyboard-publish' => 'Publicar',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Stellt e Ziilsyte z Verfiegig fir Spänder, wu Gschichte chenne yygee wäre un e Benutzeroberflechi fir d Moderation vu Gschichte',
	'right-storyreview' => 'Gschichte priefe, bearbeite un uusblände',
	'specialpages-group-contribution' => 'Spände/Spändeufruef',
	'storyboard-unpublish' => 'Vereffetlichung zruckneh',
	'storyboard-publish' => 'Vereffetlige',
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
	'specialpages-group-contribution' => 'Contributiones/Collecta de fundos',
	'storyboard-unpublish' => 'Dispublicar',
	'storyboard-publish' => 'Publicar',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Stellt eng [[Special:Story|Entréessäit fir Donateuren]] zur Verfügung, eng Säit wou Anekdoten [[Special:StorySubmission|presentéiert]] kënne ginn, an e [[Special:StoryReview|Moderatiouns-Interface fir Anekdoten]]',
	'right-storyreview' => 'Anekdoten nokucken, änneren, publizéieren a verstoppen',
	'specialpages-group-contribution' => 'Donen/Quêteur',
	'storyboard-hide' => 'Verstoppen',
	'story' => 'Anekdot',
	'storyboard-authorname' => 'Numm vum Auteur',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'storyboard-name' => 'Раскажувачница',
	'storyboard-desc' => 'Ги носи дарителите на страница кадешто да испратат своја приказна. Содржи и интерфејс за модерирање на приказните',
	'right-storyreview' => 'Прегледување, уредување, објавување и сокривање на приказни',
	'specialpages-group-contribution' => 'Придонеси/Обезб. средства',
	'storyboard-unpublish' => 'Тргни',
	'storyboard-publish' => 'Објави',
);

/** Dutch (Nederlands)
 * @author McDutchie
 * @author Siebrand
 */
$messages['nl'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Biedt een aankomstpagina voor donateurs, een pagina waar verhalen ingezonden kunnen worden en een interface voor beoordeling van verhalen',
	'right-storyreview' => 'Verhalen beoordelen, bewerken, publiceren en verbergen',
	'specialpages-group-contribution' => 'Bijdrage',
	'storyboard-unpublished' => 'Ongepubliceerd',
	'storyboard-published' => 'Gepubliceerd',
	'storyboard-hidden' => 'Verborgen',
	'storyboard-unpublish' => 'Publicatie terugtrekken',
	'storyboard-publish' => 'Publiceren',
	'storyboard-hide' => 'Verbergen',
	'story' => 'Verhaal',
	'storyboard-viewstories' => 'Verhalen bekijken',
	'storyboard-thestory' => 'Het verhaal',
	'storyboard-storystate' => 'Status:',
	'storyboard-submissioncomplete' => 'Verzenden is voltooid',
	'storyreview' => 'Verhalen beoordelen',
	'storyboard-hideimage' => 'Afbeelding verbergen',
	'storyboard-unhideimage' => 'Afbeelding weergeven',
	'storyboard-deleteimage' => 'Afbeelding verwijderen',
	'storyboard-yourname' => 'Uw naam',
	'storyboard-location' => 'Uw locatie',
	'storyboard-occupation' => 'Uw beroep',
	'storyboard-story' => 'Uw verhaal',
	'storyboard-photo' => 'Wilt u een foto van uzelf toevoegen?',
	'storyboard-email' => 'Uw e-mailadres',
	'storyboard-storytitle' => 'Een korte, beschrijvende titel:',
	'storyboard-agreement' => 'Ik ga akkoord met de publicatie van dit verhaal.',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|teken|tekens}} over)',
	'storyboard-createdsucessfully' => 'Bedankt voor het delen van uw verhaal!
We gaan het snel beoordelen.
U kunt [$1 gepubliceerde verhalen lezen].',
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
	'specialpages-group-contribution' => 'Contribucions / Levada de fonses',
	'storyboard-unpublish' => 'Despublicar',
	'storyboard-publish' => 'Publicar',
);

/** Piedmontese (Piemontèis)
 * @author Borichèt
 * @author Dragonòt
 */
$messages['pms'] = array(
	'storyboard-name' => 'Disegn ëd la senegiadura',
	'storyboard-desc' => "A dà na pàgina d'ariv për ij donator, na pàgina andoa le stòrie a peulo esse butà, e n'antërfacia ëd moderassion dle stòrie",
	'right-storyreview' => 'Lese torna, modifiché, publiché e stërmé le stòrie',
	'specialpages-group-contribution' => 'Contribussion/Racòlta fond',
	'storyboard-unpublish' => 'Pùblica pa',
	'storyboard-publish' => 'Publiché',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'storyboard-name' => 'Доска историй',
	'storyboard-desc' => 'Предоставляет страницу для размещения историй жертвователей, а также интерфейс её модерации',
	'right-storyreview' => 'проверка, правка, публикация и сокрытие историй',
	'specialpages-group-contribution' => 'Пожертвования/Сбор средств',
	'storyboard-unpublish' => 'Убрать',
	'storyboard-publish' => 'Опубликовать',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'storyboard-publish' => 'ప్రచురించు',
);

