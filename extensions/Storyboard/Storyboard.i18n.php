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

	// Story states
	'storyboard-unpublished' => 'Unpublished',
	'storyboard-published' => 'Published',
	'storyboard-hidden' => 'Hidden',
	'storyboard-unpublish' => 'Unpublish',
	'storyboard-publish' => 'Publish',
	'storyboard-hide' => 'Hide',

	'storyboard-option-unpublished' => 'unpublished',
	'storyboard-option-published' => 'published',
	'storyboard-option-hidden' => 'hidden',

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

/** Message documentation (Message documentation)
 * @author EugeneZelenko
 */
$messages['qqq'] = array(
	'storyboard-publish' => '{{Identical|Publish}}',
	'storyboard-hide' => '{{Identical|Hide}}',
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
	'storyboard-desc' => 'Прадстаўляе [[Special:Story|старонку]] для [[Special:StorySubmission|разьмяшчэньня]] гісторыяў ахвяравальнікаў, а таксама [[Special:StoryReview|інтэрфэйс яе мадэрацыі]].',
	'right-storyreview' => 'рэцэнзаваньне, рэдагаваньне, публікацыя і хаваньне гісторыяў',
	'storyboard-unpublished' => 'Неапублікаваныя',
	'storyboard-published' => 'Апублікаваныя',
	'storyboard-hidden' => 'Схаваная',
	'storyboard-unpublish' => 'Прыбраць',
	'storyboard-publish' => 'Апублікаваць',
	'storyboard-hide' => 'Схаваць',
	'story' => 'Гісторыя',
	'storyboard-submittedbyon' => 'Адпраўлена $1 $2, $3.',
	'storyboard-viewstories' => 'Прагляд гісторыяў',
	'storyboard-nosuchstory' => 'Гісторыя, якую Вы запыталі не існуе.
Верагодна яна была выдалена.',
	'storyboard-storyunpublished' => 'Гісторыя, якую Вы запыталі, яшчэ не была апублікавана.',
	'storyboard-nostorytitle' => 'Вам неабходна падаць назву ці ідэнтыфікатар гісторыі, якую жадаеце праглядзець.',
	'storyboard-cantedit' => 'Вам не дазволена рэдагаваць гісторыі.',
	'storyboard-canedit' => 'Вы можаце [$1 рэдагаваць] і апублікаваць гэтую гісторыю.',
	'storyboard-createdandmodified' => 'Створана $1, $2 і апошні раз зьмянялася $3, $4',
	'storyboard-authorname' => 'Імя аўтара',
	'storyboard-authorlocation' => 'Месцазнаходжаньне аўтара',
	'storyboard-authoroccupation' => 'Род заняткаў аўтара',
	'storyboard-authoremail' => 'Адрас электроннай пошты аўтара',
	'storyboard-thestory' => 'Гісторыя',
	'storyboard-storystate' => 'Штат:',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'storyboard-name' => 'Storyboard',
	'right-storyreview' => 'Adlenn, kemmañ, embann, ha kuzhat an istorioù',
	'storyboard-unpublished' => 'Diembannet',
	'storyboard-published' => 'Embannet',
	'storyboard-hidden' => 'Kuzhet',
	'storyboard-unpublish' => 'Diembann',
	'storyboard-publish' => 'Embann',
	'storyboard-hide' => 'Kuzhat',
	'story' => 'Istor',
	'storyboard-submittedbyon' => "Kinniget gant $1 d'an $2, $3.",
	'storyboard-viewstories' => 'Gwelet an istorioù',
	'storyboard-authorname' => 'Anv an oberour',
	'storyboard-authorlocation' => "Lec'hiadur an oberour",
	'storyboard-authoroccupation' => 'Oberiantiz an oberour',
	'storyboard-authoremail' => "Chomlec'h postel an oberour",
	'storyboard-thestory' => 'An istor',
	'storyboard-storystate' => 'Stad :',
	'storyboard-hideimage' => 'Kuzhat ar skeudenn',
	'storyboard-unhideimage' => 'Diskouez ar skeudenn',
	'storyboard-deleteimage' => 'Dilemel ar skeudenn',
	'storyboard-yourname' => "Hoc'h anv",
	'storyboard-location' => "Ho lec'hiadur",
	'storyboard-occupation' => 'Ho micher',
	'storyboard-story' => 'Ho istor',
	'storyboard-photo' => "Ur poltred ouzhoc'h o peus ?
Perak chom hep rannañ anezhi ?",
	'storyboard-email' => "Ho chomlec'h postel",
);

/** German (Deutsch)
 * @author Kghbln
 */
$messages['de'] = array(
	'storyboard-name' => 'Schwarzes Brett für Botschaften',
	'storyboard-desc' => 'Stellt eine [[Special:Story|Anlaufstelle]] für Förderer, eine Seite auf der Botschaften [[Special:StorySubmission|eingereicht]], sowie eine Seite mit der diese [[Special:StoryReview|betreut]] werden können, zur Verfügung.',
	'right-storyreview' => 'Überprüfen, Bearbeiten, Veröffentlichen und Verbergen von Botschaften',
	'storyboard-unpublished' => 'Unveröffentlicht',
	'storyboard-published' => 'Veröffentlicht',
	'storyboard-hidden' => 'Verborgen',
	'storyboard-unpublish' => 'Veröffentlichung zurückziehen',
	'storyboard-publish' => 'Veröffentlichen',
	'storyboard-hide' => 'Verbergen',
	'story' => 'Botschaft',
	'storyboard-submittedbyon' => 'Eingereicht von $1 am $2, $3.',
	'storyboard-viewstories' => 'Botschaften lesen',
	'storyboard-nosuchstory' => 'Die Botschaft, die du aufrufen wolltest, existiert nicht. Vielleicht wurde sie gelöscht.',
	'storyboard-storyunpublished' => 'Die Botschaft, die du aufrufen wolltest, wurde bislang noch nicht veröffentlicht.',
	'storyboard-nostorytitle' => 'Du musst den Titel oder die Kennung der Botschaft angeben, die du lesen möchtest.',
	'storyboard-cantedit' => 'Du hast nicht die Berechtigung Botschaften zu bearbeiten.',
	'storyboard-canedit' => 'Du kannst diese Botschaft [$1 bearbeiten] und veröffentlichen.',
	'storyboard-createdandmodified' => 'Am $1, $2 erstellt und letztmalig am $3, $4 bearbeitet.',
	'storyboard-authorname' => 'Name des Autors',
	'storyboard-authorlocation' => 'Standort des Autors',
	'storyboard-authoroccupation' => 'Beruf des Autors',
	'storyboard-authoremail' => 'E-Mail-Adresse des Autors',
	'storyboard-thestory' => 'Die Botschaft',
	'storyboard-storystate' => 'Land:',
	'storyboard-submissioncomplete' => 'Die Einreichung ist abgeschlossen',
	'storyreview' => 'Botschaft überprüfen',
	'storyboard-hideimage' => 'Bild verbergen',
	'storyboard-unhideimage' => 'Bild anzeigen',
	'storyboard-deleteimage' => 'Bild löschen',
	'storyboard-yourname' => 'Dein Name',
	'storyboard-location' => 'Dein Standort',
	'storyboard-occupation' => 'Dein Beruf',
	'storyboard-story' => 'Deine Botschaft',
	'storyboard-photo' => 'Gibt es ein Foto von Dir? Was spricht dagegen es zu veröffentlichen?',
	'storyboard-email' => 'Deine E-Mail-Adresse:',
	'storyboard-storytitle' => 'Ein kurzer, aussagekräftiger Titel:',
	'storyboard-agreement' => 'Ich stimme der Veröffentlichung und Nutzung dieser Botschaft unter den Bedingungen der Lizenz [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Namensnennung-Weitergabe unter gleichen Bedingungen] zu.',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|Anschlag|Anschläge}} verfügbar)',
	'storyboard-cannotbelonger' => "Deine Botschaft ist '''$1''' {{PLURAL:$1|Anschlag|Anschläge}} zu lang!",
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|weiterer|weitere}} {{PLURAL:$1|Anschlag|Anschläge}} notwendig)',
	'storyboard-needtoagree' => 'Du musst der Veröffentlichung deiner Botschaft zustimmen, um sie einreichen zu können.',
	'storyboard-createdsucessfully' => 'Vielen Dank, dass du uns deine Botschaft mitgeteilt hast! Wir werden sie in Kürze überprüfen.
Du kannst bereits veröffentlichte Botschaften [$1 hier] lesen.',
);

/** German (formal address) (Deutsch (Sie-Form))
 * @author Kghbln
 */
$messages['de-formal'] = array(
	'storyboard-nosuchstory' => 'Die Botschaft, die Sie aufrufen wollten, existiert nicht. Vielleicht wurde sie gelöscht.',
	'storyboard-storyunpublished' => 'Die Botschaft, die Sie aufrufen wollten, wurde bislang noch nicht veröffentlicht.',
	'storyboard-nostorytitle' => 'Sie müssen den Titel oder die Kennung der Botschaft angeben, die Sie lesen möchten.',
	'storyboard-cantedit' => 'Sie haben nicht die Berechtigung Botschaften zu bearbeiten.',
	'storyboard-canedit' => 'Sie können diese Botschaft [$1 bearbeiten] und veröffentlichen.',
	'storyboard-yourname' => 'Ihre Name',
	'storyboard-location' => 'Ihr Standort',
	'storyboard-occupation' => 'Ihr Beruf',
	'storyboard-story' => 'Ihre Botschaft',
	'storyboard-photo' => 'Gibt es ein Foto von Ihnen? Was spricht dagegen es zu veröffentlichen?',
	'storyboard-email' => 'Ihre E-Mail-Adresse:',
	'storyboard-cannotbelonger' => "Ihre Botschaft ist '''$1''' {{PLURAL:$1|Anschlag|Anschläge}} zu lang!",
	'storyboard-needtoagree' => 'Sie müssen der Veröffentlichung Ihrer Botschaft zustimmen, um sie einreichen zu können.',
	'storyboard-createdsucessfully' => 'Vielen Dank, dass Sie uns Ihre Botschaft mitgeteilt haben! Wir werden sie in Kürze überprüfen.
Sie können bereits veröffentlichte Botschaften [$1 hier] lesen.',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Bitujo [[Special:Story|bok pśidostaśa za pósćiwarjow]], bok, źož tšojenja daju se [[Special:StorySubmission|zawostajiś]] a [[Special:StoryReview|pówjerch za moderaciju tšojenjow]]',
	'right-storyreview' => 'Tšojenja pśeglědaś, wobźěłaś, wózjawiś a schowaś',
	'storyboard-unpublished' => 'Njewózjawjony',
	'storyboard-published' => 'Wózjawjony',
	'storyboard-hidden' => 'Schowany',
	'storyboard-unpublish' => 'Wózjawjenje slědk śěgnuś',
	'storyboard-publish' => 'Wózjawiś',
	'storyboard-hide' => 'Schowaś',
	'story' => 'Tšojenje',
	'storyboard-submittedbyon' => 'Wót $1 dnja $2, $3 zawóstajony.',
	'storyboard-viewstories' => 'Tšojenja se woglědaś',
	'storyboard-nosuchstory' => 'Tšojenje, kótarež sy pominał, njeeksistěrujo.
Móžno, až jo se wótporało.',
	'storyboard-storyunpublished' => 'Tšojenje, kótarež sy pominał, hyšći njejo wózjawjone.',
	'storyboard-nostorytitle' => 'Musyśo titel abo ID tšojenja, kótarež cośo se woglědaś, pódaś.',
	'storyboard-cantedit' => 'Njesmějośo tšojenja wobźěłas.',
	'storyboard-canedit' => 'Móžośo tšojenje [$1 wobźěłaś] a wózjawiś.',
	'storyboard-createdandmodified' => 'Dnja $1, $2 napórane a dnja $3, $4 slědny raz změnjone.',
	'storyboard-authorname' => 'Mě awtora',
	'storyboard-authorlocation' => 'Městno awtora',
	'storyboard-authoroccupation' => 'Pówołanje awtora',
	'storyboard-authoremail' => 'E-mailowa adresa awtora',
	'storyboard-thestory' => 'Tšojenje',
	'storyboard-storystate' => 'Stat:',
	'storyboard-submissioncomplete' => 'Zapódaśe dopołne',
	'storyreview' => 'Pśeglědanje tšojenja',
	'storyboard-hideimage' => 'Wobraz schowaś',
	'storyboard-unhideimage' => 'Wobraz pokazaś',
	'storyboard-deleteimage' => 'Wobraz wulašowaś',
	'storyboard-yourname' => 'Wašo mě',
	'storyboard-location' => 'Wašo městno',
	'storyboard-occupation' => 'Wašo pówołanje',
	'storyboard-story' => 'Wašo tšojenje',
	'storyboard-photo' => 'Maśo foto wót sebje?
Cogodla  njestajaśo jo k našej dispoziciji?',
	'storyboard-email' => 'Waša e-mailowa adresa',
	'storyboard-storytitle' => 'Krotki, wugroniwy titel:',
	'storyboard-agreement' => 'Zwólijom do wózjawjenja a wužywanja toś togo tšojenja pód wuměnjenjami licence [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|znamuško|znamušce|znamuška|znamuškow}} wušej)',
	'storyboard-cannotbelonger' => 'Wašo tšojenjo jo <b>$1</b> {{PLURAL:$1|znamuško|znamušce|znamuška|znamuškow}} pśedłujko!',
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|dalšne znamuško trěbne|dalšnej znamušce trěbnej|dalšne znamuška trěbne|dalšnych znamuškow trěbnych}})',
	'storyboard-needtoagree' => 'Musyśo do wózjawjenja swójogo tšojenja zwóliś, aby wy jo zapódał.',
	'storyboard-createdsucessfully' => 'Źěkujomy se wam, až sćo nam swójo tšojenje k dispoziciji stajił!
Buźomy se skóro pśeglědowaś.
Móžośo [$1 wózjawjone tšojenja cytaś].',
);

/** Spanish (Español)
 * @author Crazymadlover
 */
$messages['es'] = array(
	'storyboard-unpublished' => 'Inédito',
	'storyboard-published' => 'Publicado',
	'storyboard-hidden' => 'Oculto',
	'storyboard-unpublish' => 'No publicar',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Ocultar',
	'story' => 'Historia',
	'storyboard-submittedbyon' => 'Enviado por $1 en $2, $3.',
	'storyboard-authorname' => 'Nombre de autor',
	'storyboard-authorlocation' => 'Ubicación de autor',
	'storyboard-authoroccupation' => 'Ocupación de autor',
	'storyboard-authoremail' => 'Dirección de correo electrónico de autor',
	'storyboard-hideimage' => 'Ocultar imagen',
	'storyboard-unhideimage' => 'Mostrar imagen',
	'storyboard-deleteimage' => 'Borrar imagen',
	'storyboard-yourname' => 'Tu nombre',
	'storyboard-location' => 'Tu ubicación',
	'storyboard-occupation' => 'Tu ocupación',
	'storyboard-email' => 'Tu dirección de correo electrónico',
	'storyboard-storytitle' => 'Un título corto y descriptivo:',
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
	'storyboard-desc' => 'Fournit une [[Special:Story|page cible pour les donateurs]], une page pour [[Special:StorySubmission|proposer une histoire]], et une [[Special:StoryReview|interface de modération des histoires]]',
	'right-storyreview' => 'Relire, modifier, publier, et masquer les histoires',
	'storyboard-unpublished' => 'Non publié',
	'storyboard-published' => 'Publié',
	'storyboard-hidden' => 'Masqué',
	'storyboard-unpublish' => 'Dépublier',
	'storyboard-publish' => 'Publier',
	'storyboard-hide' => 'Masquer',
	'story' => 'Histoire',
	'storyboard-submittedbyon' => 'Proposée par $1 le $2, $3',
	'storyboard-viewstories' => 'Voir les histoires',
	'storyboard-nosuchstory' => 'L’histoire que vous avez demandée n’existe pas. Elle a peut-être été supprimée.',
	'storyboard-storyunpublished' => 'L’histoire que vous avez demandée n’a pas encore été publiée.',
	'storyboard-nostorytitle' => 'Vous devez indiquer le titre ou l’identifiant de l’histoire que vous voulez afficher.',
	'storyboard-cantedit' => 'Vous n’avez pas les droits pour modifier des histoires.',
	'storyboard-canedit' => 'Vous pouvez [$1 modifier] et publier cette histoire.',
	'storyboard-createdandmodified' => 'Créée le $1, $2 et dernière modification le $3, $4',
	'storyboard-authorname' => 'Nom de l’auteur',
	'storyboard-authorlocation' => 'Localisation de l’auteur',
	'storyboard-authoroccupation' => 'Activité de l’auteur',
	'storyboard-authoremail' => 'Adresse de courriel de l’auteur',
	'storyboard-thestory' => 'L’histoire',
	'storyboard-storystate' => 'État :',
	'storyboard-submissioncomplete' => 'Proposition achevée',
	'storyreview' => 'Critique de l’histoire',
	'storyboard-hideimage' => 'Masquer l’image',
	'storyboard-unhideimage' => 'Afficher l’image',
	'storyboard-deleteimage' => 'Supprimer l’image',
	'storyboard-yourname' => 'Votre nom',
	'storyboard-location' => 'Votre localisation',
	'storyboard-occupation' => 'Votre métier',
	'storyboard-story' => 'Votre histoire',
	'storyboard-photo' => 'Vous avez une photo de vous-même ? Pourquoi ne pas la partager ?',
	'storyboard-email' => 'Votre adresse de courriel',
	'storyboard-storytitle' => 'Un titre, court et descriptif :',
	'storyboard-agreement' => 'J’accepte la publication et l’utilisation de cette histoire sous les termes de la [http://creativecommons.org/licenses/by-sa/3.0/ licence Creative Commons Paternité – Partage des conditions initiales à l’identique].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|signe|signes}} {{PLURAL:$1|restant|restants}})',
	'storyboard-cannotbelonger' => 'Votre histoire est trop longue de <b>$1</b> {{PLURAL:$1|signe|signes}} !',
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|signe supplémentaire|signes supplémentaires}} requis)',
	'storyboard-needtoagree' => 'Vous devez approuver la publication de votre histoire pour pouvoir la proposer.',
	'storyboard-createdsucessfully' => 'Merci d’avoir partagé votre histoire avec nous !
Nous allons l’examiner sous peu.
Vous pouvez [$1 lire des histoires publiées].',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'storyboard-name' => 'Taboleiro de historias',
	'storyboard-desc' => 'Proporciona unha páxina de chegada para os doantes, unha páxina desde a que se poden enviar historias e unha interface para moderar o seu envío',
	'right-storyreview' => 'Revisar, editar, publicar e agochar historias',
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
	'storyboard-unpublish' => 'Vereffetlichung zruckneh',
	'storyboard-publish' => 'Vereffetlige',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Skića [[Special:Story|přichadnu stronu za darićelow]], strona, hdźež so hodźa powědančka [[Special:StorySubmission|zawostajić]] a [[Special:StoryReview|powjerch za moderaciju powědančkow]]',
	'right-storyreview' => 'Powědančka přehladać, wobdźěłać, wozjewić a schować',
	'storyboard-unpublished' => 'Njewozjewjena',
	'storyboard-published' => 'Wozjewjeny',
	'storyboard-hidden' => 'Schowany',
	'storyboard-unpublish' => 'Wozjewjenje cofnyć',
	'storyboard-publish' => 'Wozjewić',
	'storyboard-hide' => 'Schować',
	'story' => 'Powědančko',
	'storyboard-submittedbyon' => 'Zawostajene wot $1 dnja $2, $3.',
	'storyboard-viewstories' => 'Powědančka pokazać',
	'storyboard-nosuchstory' => 'Powědančko, kotrež sće požadał, njeeksistuje.
Móžno, zo je so wotstroniło.',
	'storyboard-storyunpublished' => 'Powědančko, kotrež sće požadał, hišće njeje wozjewjene.',
	'storyboard-nostorytitle' => 'Dyrbiće titul abo ID powědančka podać, kotrež chceće sej wobhladać.',
	'storyboard-cantedit' => 'Njesměće powědančka wobdźěłać.',
	'storyboard-canedit' => 'Móžeće tute pwědančko [$1 wobdźěłać] a wozjewić.',
	'storyboard-createdandmodified' => 'Dnja $1, $2 wutworjene a dnja $3, $4 posledni raz změnjene',
	'storyboard-authorname' => 'Mjeno awtora',
	'storyboard-authorlocation' => 'Městno awtora',
	'storyboard-authoroccupation' => 'Powołanje awtora',
	'storyboard-authoremail' => 'E-mejlowa adresa awtora',
	'storyboard-thestory' => 'Powědančko',
	'storyboard-storystate' => 'Stat:',
	'storyboard-submissioncomplete' => 'Zapodaće dospołne',
	'storyreview' => 'Přepruwowanje powědančka',
	'storyboard-hideimage' => 'Wobraz schować',
	'storyboard-unhideimage' => 'Wobraz pokazać',
	'storyboard-deleteimage' => 'Wobraz zhašeć',
	'storyboard-yourname' => 'Waše mjeno',
	'storyboard-location' => 'Waše městno',
	'storyboard-occupation' => 'Waše powołanje',
	'storyboard-story' => 'Waše powědančko',
	'storyboard-photo' => 'Maće foto wot sebje?
Čehodla njedaće druhich na njo dźěl měć?',
	'storyboard-email' => 'Twoja e-mejlowa adresa',
	'storyboard-storytitle' => 'Krótki, wuprajiwy titul:',
	'storyboard-agreement' => 'Zwolim do wozjewjenja a wužiwanja tutoho powědančka pod wuměnjenjemi licency [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|znamješko|znamješce|znamješka|znamješkow}} wyše)',
	'storyboard-cannotbelonger' => 'Waše powědančko je <b>$1</b> {{PLURAL:$1|znamješko|znamješce|znamješka|znamješkow}} předołho!',
	'storyboard-charsneeded' => '($1 {{PLURAL:$1|dalše znamješko trěbne|dalšej znamješce trěbnej|dalše znamješka trěbne|dalšich znamješkow trěbnych}})',
	'storyboard-needtoagree' => 'Dyrbiće do wozjewjenja wašeho powědančka zwolić, zo byšće jo zapodał.',
	'storyboard-createdsucessfully' => 'Dźakujemy so wam, zo sće swoje powědančko nam k dispoziciji stajił!
Budźemy jo bórze přepruwować.
Móžeće [$1 wozjewjene powědančka čitać].',
);

/** Hungarian (Magyar)
 * @author Glanthor Reviol
 */
$messages['hu'] = array(
	'storyboard-unpublish' => 'Közzététel visszavonása',
	'storyboard-publish' => 'Közzététel',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Provide un [[Special:Story|pagina de arrivata pro donatores]], un pagina ubi historias pote esser [[Special:StorySubmission|submittite]] e un [[Special:StoryReview|interfacie pro moderation de historias]].',
	'right-storyreview' => 'Revider, modificar, publicar e celar historias',
	'storyboard-unpublished' => 'Non publicate',
	'storyboard-published' => 'Publicate',
	'storyboard-hidden' => 'Celate',
	'storyboard-unpublish' => 'Dispublicar',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Celar',
	'story' => 'Historia',
	'storyboard-submittedbyon' => 'Submittite per $1 le $2 a $3.',
	'storyboard-viewstories' => 'Vider historias',
	'storyboard-nosuchstory' => 'Le historia que tu ha demandate non existe.
Illo pote haber essite removite.',
	'storyboard-storyunpublished' => 'Le historia que tu ha demandate non ha ancora essite publicate.',
	'storyboard-nostorytitle' => 'Tu debe specificar le titulo o ID del historia que tu vole vider.',
	'storyboard-cantedit' => 'Tu non ha le permission de modificar historias.',
	'storyboard-canedit' => 'Tu pote [$1 modificar] e publicar iste historia.',
	'storyboard-createdandmodified' => 'Creation: le $1 a $2; ultime modification: le $3 a $4',
	'storyboard-authorname' => 'Nomine del autor',
	'storyboard-authorlocation' => 'Loco del autor',
	'storyboard-authoroccupation' => 'Occupation del autor',
	'storyboard-authoremail' => 'Adresse de e-mail del autor',
	'storyboard-thestory' => 'Le historia',
	'storyboard-storystate' => 'Stato:',
	'storyboard-submissioncomplete' => 'Submission complete',
	'storyreview' => 'Revision del historia',
	'storyboard-hideimage' => 'Celar imagine',
	'storyboard-unhideimage' => 'Monstrar imagine',
	'storyboard-deleteimage' => 'Deler imagine',
	'storyboard-yourname' => 'Tu nomine',
	'storyboard-location' => 'Tu loco',
	'storyboard-occupation' => 'Tu occupation',
	'storyboard-story' => 'Tu historia',
	'storyboard-photo' => 'Ha tu un photo de te?
Proque non facer vider lo?',
	'storyboard-email' => 'Tu adresse de e-mail',
	'storyboard-storytitle' => 'Un titulo curte e descriptive:',
	'storyboard-agreement' => 'Io accepta le publication e le uso de iste historia sub le conditiones del [http://creativecommons.org/licenses/by-sa/3.0/ licentia Creative Commons Attribution/Share-Alike].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|character|characteres}} restante)',
	'storyboard-cannotbelonger' => 'Tu historia es troppo longe de <b>$1</b> {{PLURAL:$1|character|characteres}}!',
	'storyboard-charsneeded' => '($1 plus {{PLURAL:$1|character|characteres}} necessari)',
	'storyboard-needtoagree' => 'Tu debe approbar le publication de tu historia pro submitter lo.',
	'storyboard-createdsucessfully' => 'Gratias pro partir tu historia con nos!
Nos lo revidera tosto.
Tu pote [$1 leger le historias ja publicate].',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Stellt eng [[Special:Story|Entréessäit fir Donateuren]] zur Verfügung, eng Säit wou Anekdoten [[Special:StorySubmission|presentéiert]] kënne ginn, an e [[Special:StoryReview|Moderatiouns-Interface fir Anekdoten]]',
	'right-storyreview' => 'Anekdoten nokucken, änneren, publizéieren a verstoppen',
	'storyboard-unpublished' => 'Net verëffentlecht',
	'storyboard-published' => 'Verëffentlecht',
	'storyboard-hidden' => 'Verstoppt',
	'storyboard-unpublish' => 'Verëffentlecung zréckzéien',
	'storyboard-publish' => 'Verëffentlechen',
	'storyboard-hide' => 'Verstoppen',
	'story' => 'Anekdot',
	'storyboard-submittedbyon' => 'Vum $1 den $2, $3 geschéckt',
	'storyboard-viewstories' => 'Anekdote weisen',
	'storyboard-nosuchstory' => "D'Anekdot déi Dir ugefrot hutt gëtt et net.
Et ka sinn datt se ewechgeholl gouf.",
	'storyboard-storyunpublished' => "D'Anekdot déi Dir ugefrot hutt gouf nach net verëffentlecht.",
	'storyboard-nostorytitle' => "Dir musst den Titel oder d'ID vun der Anekdot uginn déi gewise soll ginn.",
	'storyboard-cantedit' => 'Dir däerft Anekdote net änneren.',
	'storyboard-canedit' => 'Dir kënnt dës Anekdot [$1 änneren] a verëffentlechen.',
	'storyboard-authorname' => 'Numm vum Auteur',
	'storyboard-authoremail' => 'E-Mailadress vum Auteur',
	'storyboard-thestory' => "D'Anekdot",
	'storyboard-storystate' => 'Staat',
	'storyreview' => 'Anekdot nokucken',
	'storyboard-hideimage' => 'Bild verstoppen',
	'storyboard-unhideimage' => 'Bild weisen',
	'storyboard-deleteimage' => 'Bild läschen',
	'storyboard-yourname' => 'Ären Numm',
	'storyboard-location' => 'Plaz wou Dir sidd',
	'storyboard-occupation' => 'Äre Beruff',
	'storyboard-story' => 'Är Anekdot',
	'storyboard-photo' => 'Hutt dir eng Photo vun Iech?
Firwat se net hei weisen?',
	'storyboard-email' => 'Är E-Mailadress',
	'storyboard-storytitle' => 'E kuerzen Titel de beschreift wourëms et geet:',
	'storyboard-needtoagree' => "Dir musst d'accord sinn datt Är Anekdot verëffentlecht gëtt fir se ze schécken.",
	'storyboard-createdsucessfully' => 'Merci datt dir Är Anekdot mat eis deelt!
Mir kucken se demnächst no.
Dir kënnt [$1 publizéiert Anekdote liesen].',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'storyboard-name' => 'Раскажувачница',
	'storyboard-desc' => 'Дава [[Special:Story|страница за дарителите]], кадешто тие можат [[Special:StorySubmission|да објават]] своја приказна, како и [[Special:StoryReview|интерфејс за модерирање на приказните]]',
	'right-storyreview' => 'Прегледување, уредување, објавување и сокривање на приказни',
	'storyboard-unpublished' => 'Необјавено',
	'storyboard-published' => 'Објавено',
	'storyboard-hidden' => 'Сокриено',
	'storyboard-unpublish' => 'Тргни',
	'storyboard-publish' => 'Објави',
	'storyboard-hide' => 'Сокриј',
	'story' => 'Приказна',
	'storyboard-submittedbyon' => 'Поднесено од $1 на $2, $3.',
	'storyboard-viewstories' => 'Преглед на приказни',
	'storyboard-nosuchstory' => 'Приказната што ја побаравте не постои.
Може да била отстранета.',
	'storyboard-storyunpublished' => 'Приказната што ја побаравте сè уште не е објавена.',
	'storyboard-nostorytitle' => 'Треба да назначите наслов или ID на приказната што сакате да ја видите.',
	'storyboard-cantedit' => 'Не ви е дозволено да ги менувате приказните.',
	'storyboard-canedit' => 'Можете да ја [$1 уредите] и објавите приказнава.',
	'storyboard-createdandmodified' => 'Создадено на $1, $2, а последно изменето на $3, $4',
	'storyboard-authorname' => 'Име на авторот',
	'storyboard-authorlocation' => 'Место на живеење на авторот',
	'storyboard-authoroccupation' => 'Занимање на авторот',
	'storyboard-authoremail' => 'Е-пошта на авторот',
	'storyboard-thestory' => 'Приказната',
	'storyboard-storystate' => 'Сојуз. држава:',
	'storyboard-submissioncomplete' => 'Поднесувањето е завршено',
	'storyreview' => 'Преглед на приказна',
	'storyboard-hideimage' => 'Сокриј слика',
	'storyboard-unhideimage' => 'Прикажи слика',
	'storyboard-deleteimage' => 'Избриши слика',
	'storyboard-yourname' => 'Вашето име и презме',
	'storyboard-location' => 'Место на живеење',
	'storyboard-occupation' => 'Вашето занимање',
	'storyboard-story' => 'Вашата приказна',
	'storyboard-photo' => 'Имате ваша фотографија?
Зошто не ја споделите?',
	'storyboard-email' => 'Ваша е-пошта',
	'storyboard-storytitle' => 'Краток и описен наслов:',
	'storyboard-agreement' => 'Се согласувам приказнава да се објави и користи согласно условите на лиценцата [http://creativecommons.org/licenses/by-sa/3.0/deed.mk Creative Commons Наведи извор/Сподели под исти услови].',
	'storyboard-charsleft' => '({{PLURAL:$1|Ви преостанува уште|Ви преостануваат уште}} $1 {{PLURAL:$1|знак|знаци}})',
	'storyboard-cannotbelonger' => 'Вашата приказна е за <b>$1</b> {{PLURAL:$1|знак|знаци}} подолга од дозволеното!',
	'storyboard-charsneeded' => '({{PLURAL:$1|потребен е|потребни се}} уште $1 {{PLURAL:$1|знак|знаци}})',
	'storyboard-needtoagree' => 'Мора да се согласите да ја објавите приказната за да ја поднесете.',
	'storyboard-createdsucessfully' => 'Ви благодаримне што ја споделивте Вашата приказна со нас!
Набргу ќе ја прегледаме.
Можете да ги [$1 прочитате објавените приказни].',
);

/** Dutch (Nederlands)
 * @author McDutchie
 * @author Siebrand
 */
$messages['nl'] = array(
	'storyboard-name' => 'Storyboard',
	'storyboard-desc' => 'Biedt een [[Special:Story|aankomstpagina voor donateurs]], een [[Special:StorySubmission|pagina waar verhalen ingezonden kunnen worden]] en een [[Special:StoryReview|interface voor beoordeling van verhalen]]',
	'right-storyreview' => 'Verhalen beoordelen, bewerken, publiceren en verbergen',
	'storyboard-unpublished' => 'Ongepubliceerd',
	'storyboard-published' => 'Gepubliceerd',
	'storyboard-hidden' => 'Verborgen',
	'storyboard-unpublish' => 'Publicatie terugtrekken',
	'storyboard-publish' => 'Publiceren',
	'storyboard-hide' => 'Verbergen',
	'story' => 'Verhaal',
	'storyboard-submittedbyon' => 'Ingezonden door $1 op $2 om $3.',
	'storyboard-viewstories' => 'Verhalen bekijken',
	'storyboard-nosuchstory' => 'Het door u opgevraagde verhaal bestaat niet.
Mogelijk is het verwijderd.',
	'storyboard-storyunpublished' => 'Het verhaal dat u heeft opgevraagd is nog niet gepubliceerd.',
	'storyboard-nostorytitle' => 'U moet de naam of het ID van het verhaal dat u wilt bekijken opgeven.',
	'storyboard-cantedit' => 'U mag verhalen niet bewerken.',
	'storyboard-canedit' => 'U kunt dit verhaal [$1 bewerken] en publiceren.',
	'storyboard-createdandmodified' => 'Aangemaakt op $1 om $2 en voor het laatst bewerkt op $3 om $4',
	'storyboard-authorname' => 'Naam auteur',
	'storyboard-authorlocation' => 'Locatie auteur',
	'storyboard-authoroccupation' => 'Beroep auteur',
	'storyboard-authoremail' => 'E-mailadres auteur',
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
	'storyboard-agreement' => 'Ik ga akkoord met de publicatie van dit verhaal onder de voorwaarden van de licentie [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Naamsvermelding-Gelijk delen].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|teken|tekens}} over)',
	'storyboard-cannotbelonger' => 'Uw verhaal is <b>$1</b> {{PLURAL:$1|teken|tekens}} te lang!',
	'storyboard-charsneeded' => '(er {{PLURAL:$1|is nog 1 teken|zijn nog $1 tekens}} meer nodig)',
	'storyboard-needtoagree' => 'U moet akkoord gaan met het publiceren van uw verhaal voordat u het kunt inzenden.',
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
	'storyboard-unpublish' => 'Pùblica pa',
	'storyboard-publish' => 'Publiché',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'storyboard-name' => 'Histórias',
	'storyboard-desc' => 'Fornece uma [[Special:Story|página de destino para beneméritos]], uma página onde se podem [[Special:StorySubmission|submeter]] histórias e uma [[Special:StoryReview|interface de moderação das histórias]]',
	'right-storyreview' => 'Rever, editar, publicar e ocultar histórias',
	'storyboard-unpublished' => 'Não publicada',
	'storyboard-published' => 'Publicada',
	'storyboard-hidden' => 'Ocultada',
	'storyboard-unpublish' => 'Retirar de publicação',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Ocultar',
	'story' => 'História',
	'storyboard-submittedbyon' => 'Enviada por $1 em $2, às $3.',
	'storyboard-viewstories' => 'Ver histórias',
	'storyboard-nosuchstory' => 'A história que solicitou não existe.
Pode ter sido ocultada.',
	'storyboard-storyunpublished' => 'A história que solicitou ainda não foi publicada.',
	'storyboard-nostorytitle' => 'Tem de especificar o título ou a identificação da história que pretende ver.',
	'storyboard-cantedit' => 'Não lhe é permitido editar histórias.',
	'storyboard-canedit' => 'Pode [$1 editar] e publicar esta história.',
	'storyboard-createdandmodified' => 'Criada em $1, às $2 e modificada pela última vez em $3, às $4',
	'storyboard-authorname' => 'Nome do autor',
	'storyboard-authorlocation' => 'Localização do autor',
	'storyboard-authoroccupation' => 'Profissão do autor',
	'storyboard-authoremail' => 'Endereço de correio electrónico do autor',
	'storyboard-thestory' => 'A história',
	'storyboard-storystate' => 'Estado:',
	'storyboard-submissioncomplete' => 'Envio finalizado',
	'storyreview' => 'Revisão da história',
	'storyboard-hideimage' => 'Ocultar imagem',
	'storyboard-unhideimage' => 'Mostrar imagem',
	'storyboard-deleteimage' => 'Apagar imagem',
	'storyboard-yourname' => 'O seu nome',
	'storyboard-location' => 'A sua localização',
	'storyboard-occupation' => 'A sua profissão',
	'storyboard-story' => 'A sua história',
	'storyboard-photo' => 'Tem uma fotografia sua?
Que tal partilhá-la?',
	'storyboard-email' => 'O seu endereço de correio electrónico',
	'storyboard-storytitle' => 'Um título curto e descritivo:',
	'storyboard-agreement' => 'Concordo com a publicação e uso desta história nos termos da licença [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Atribuição-Partilha nos Termos da Mesma Licença]',
	'storyboard-charsleft' => '(restam $1 {{PLURAL:$1|carácter|caracteres}})',
	'storyboard-cannotbelonger' => 'A sua história tem <b>$1</b> {{PLURAL:$1|carácter|caracteres}} a mais!',
	'storyboard-charsneeded' => '(é preciso mais $1 {{PLURAL:$1|carácter|caracteres}})',
	'storyboard-needtoagree' => 'Para enviar a sua história tem de concordar com a sua publicação.',
	'storyboard-createdsucessfully' => 'Obrigado por partilhar connosco a sua história.
Iremos revê-la em breve.
Pode [$1 ler histórias publicadas].',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'storyboard-name' => 'Доска историй',
	'storyboard-desc' => 'Предоставляет [[Special:Story|страницу]] для [[Special:StorySubmission|размещения историй]] жертвователей, а также [[Special:StoryReview|интерфейс модерации]] этих историй',
	'right-storyreview' => 'проверка, правка, публикация и сокрытие историй',
	'storyboard-unpublished' => 'Неопубликована',
	'storyboard-published' => 'Опубликована',
	'storyboard-hidden' => 'Скрыта',
	'storyboard-unpublish' => 'Убрать',
	'storyboard-publish' => 'Опубликовать',
	'storyboard-hide' => 'Скрыть',
	'story' => 'История',
	'storyboard-submittedbyon' => 'Отправлена $1 $2 $3.',
	'storyboard-viewstories' => 'Просмотр историй',
	'storyboard-nosuchstory' => 'Запрошенной вами истории не существует.
Возможно, она была удалена.',
	'storyboard-storyunpublished' => 'Запрашиваемая вами история ещё не была опубликована.',
	'storyboard-nostorytitle' => 'Вы должны указать название или идентификатор истории, которую вы хотите просмотреть.',
	'storyboard-cantedit' => 'Вы не можете редактировать истории.',
	'storyboard-canedit' => 'Вы можете [$1 изменить] и опубликовать эту историю.',
	'storyboard-createdandmodified' => 'Создана $1 $2, изменена $3 $4',
	'storyboard-authorname' => 'Имя автора',
	'storyboard-authorlocation' => 'Местонахождение автора',
	'storyboard-authoroccupation' => 'Род занятий автора',
	'storyboard-authoremail' => 'Адрес эл. почты автора',
	'storyboard-thestory' => 'История',
	'storyboard-storystate' => 'Состояние:',
	'storyboard-submissioncomplete' => 'Подача произведена',
	'storyreview' => 'Проверка истории',
	'storyboard-hideimage' => 'Скрыть изображение',
	'storyboard-unhideimage' => 'Показать изображение',
	'storyboard-deleteimage' => 'Удалить изображение',
	'storyboard-yourname' => 'Ваше имя',
	'storyboard-location' => 'Ваше местоположение',
	'storyboard-occupation' => 'Ваш род занятий',
	'storyboard-story' => 'Ваша история',
	'storyboard-photo' => 'У вас есть ваша фотография?
Почему бы не разместить её?',
	'storyboard-email' => 'Ваш адрес эл. почты',
	'storyboard-storytitle' => 'Короткий описательный заголовок:',
	'storyboard-agreement' => 'Я согласен с публикацией и использованием этой истории в соответствии с условиями [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '({{PLURAL:$1|осталcя $1 символ|осталось $1 символа|осталось $1 символов}})',
	'storyboard-cannotbelonger' => 'Ваша история длиннее на <b>$1</b> {{PLURAL:$1|символ|символа|символов}}!',
	'storyboard-charsneeded' => '({{PLURAL:$1|необходим ещё $1 символ|необходимо ещё $1 символа|необходимо ещё $1 символов}})',
	'storyboard-needtoagree' => 'Вы должны дать согласие на публикацию своей истории перед её отправкой.',
	'storyboard-createdsucessfully' => 'Спасибо, что рассказали нам свою историю!
Мы рассмотрим её в ближайшее время.
Вы можете [$1 прочитать уже опубликованные истории].',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'storyboard-publish' => 'ప్రచురించు',
);

