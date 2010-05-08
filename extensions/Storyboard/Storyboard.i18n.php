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
	'storyboard-storystate' => 'State',
	'storyboard-language' => 'Language',

	// Storyboard tag
	'storyboard-storymetadata' => 'Submitted by $1 on $3, $4.',
	'storyboard-storymetadatafrom' => 'Submitted by $1 from $2 on $3, $4.',

	// Special:StorySubmission
	'storyboard-submissioncomplete' => 'Submission complete',
	'storyboard-submissionincomplete' => 'Submission failed',
	'storyboard-alreadyexists' => '"$1" is already taken.',
	'storyboard-alreadyexistschange' => '"{0}" is already taken, please choose a different title.', // Use {0} not $1!
	'storyboard-changetitle' => 'Change the title.',
	'storyboard-notsubmitted' => 'Authentication failed, no story has been saved.',
	'storyboard-charstomany' => '$1 characters too many!',
	'storyboard-morecharsneeded' => '$1 more characters needed',
	'storyboard-charactersleft' => '$1 characters left',
	'storyboard-needtoagree' => 'You need to agree to the publication of your story to submit it.',
	'storyboard-done' => 'Done',
	'storyboard-working' => 'Working...',
	'storyboard-imagedeleted' => 'Image deleted',
	'storyboard-showimage' => 'Show image',
	'storyboard-hideimage' => 'Hide image',
	'storyboard-imagedeletionconfirm' => 'Are you sure you want to permanently delete this stories image?',

	// Story review
	'storyreview' => 'Story review',
	'storyboard-hideimage' => 'Hide image',
	'storyboard-unhideimage' => 'Show image',
	'storyboard-deleteimage' => 'Delete image',
	'storyboard-anerroroccured' => 'An error occured: $1',

	// Story submission
	'storyboard-yourname' => 'Your name',
	'storyboard-location' => 'Your location',
	'storyboard-occupation' => 'Your occupation',
	'storyboard-story' => 'Your story',
	'storyboard-photo' => 'Have a photo of yourself?
Why not share it?',
	'storyboard-email' => 'Your e-mail address',
	'storyboard-storytitle' => 'A short, descriptive title',
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
 * @author Hamilton Abreu
 */
$messages['qqq'] = array(
	'storyboard-hidden' => '{{Identical|Hidden}}',
	'storyboard-publish' => '{{Identical|Publish}}',
	'storyboard-hide' => '{{Identical|Hide}}',
	'storyboard-language' => '{{Identical|Language}}',
	'storyboard-alreadyexists' => '$1 is a story title',
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
	'storyboard-hidden' => 'Схаваныя',
	'storyboard-unpublish' => 'Прыбраць',
	'storyboard-publish' => 'Апублікаваць',
	'storyboard-hide' => 'Схаваць',
	'storyboard-option-unpublished' => 'неапублікаваная',
	'storyboard-option-published' => 'апублікаваная',
	'storyboard-option-hidden' => 'схаваная',
	'story' => 'Гісторыя',
	'storyboard-submittedbyon' => 'Адпраўленая $1 $2, $3.',
	'storyboard-viewstories' => 'Паказаць гісторыі',
	'storyboard-nosuchstory' => 'Гісторыя, якую Вы запыталі, не існуе.
Верагодна, яна была выдаленая.',
	'storyboard-storyunpublished' => 'Гісторыя, якую Вы запыталі, яшчэ не была апублікаваная.',
	'storyboard-nostorytitle' => 'Вам неабходна падаць назву альбо ідэнтыфікатар гісторыі, якую Вы жадаеце праглядзець.',
	'storyboard-cantedit' => 'Вам не дазволена рэдагаваць гісторыі.',
	'storyboard-canedit' => 'Вы можаце [$1 рэдагаваць] і апублікаваць гэтую гісторыю.',
	'storyboard-createdandmodified' => 'Створаная $1, $2 і апошні раз зьмянялася $3, $4',
	'storyboard-authorname' => 'Імя аўтара',
	'storyboard-authorlocation' => 'Месцазнаходжаньне аўтара',
	'storyboard-authoroccupation' => 'Род заняткаў аўтара',
	'storyboard-authoremail' => 'Адрас электроннай пошты аўтара',
	'storyboard-thestory' => 'Гісторыя',
	'storyboard-storystate' => 'Стан',
	'storyboard-language' => 'Мова',
	'storyboard-storymetadata' => 'Адпраўленая $1 з $2 $3, $4.',
	'storyboard-submissioncomplete' => 'Адпраўка скончаная',
	'storyboard-submissionincomplete' => 'Памылка адпраўкі',
	'storyboard-alreadyexists' => '«$1» ужо занятая.',
	'storyboard-changetitle' => 'Зьмяніць назву.',
	'storyboard-notsubmitted' => 'Памылка аўтэнтыфікацыі, ніякія гісторыі не былі захаваныя.',
	'storyreview' => 'Рэцэнзаваньне гісторыі',
	'storyboard-hideimage' => 'Схаваць выяву',
	'storyboard-unhideimage' => 'Паказаць выяву',
	'storyboard-deleteimage' => 'Выдаліць выяву',
	'storyboard-yourname' => 'Ваша імя',
	'storyboard-location' => 'Ваша месцазнаходжаньне',
	'storyboard-occupation' => 'Ваш род заняткаў',
	'storyboard-story' => 'Ваша гісторыя',
	'storyboard-photo' => 'Вы маеце сваё фота?
Чаму б яго не разьмясьціць?',
	'storyboard-email' => 'Адрас Вашай электроннай пошты',
	'storyboard-storytitle' => 'Кароткі, апісваючы загаловак',
	'storyboard-agreement' => 'Я згодны з публікацыяй і выкарыстаньнем гэтай гісторыі на ўмовах ліцэнзіі [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution/Share-Alike License].',
	'storyboard-charsleft' => '({{PLURAL:$1|застаўся $1 сымбаль|засталося $1 сымбалі|засталося $1 сымбаляў}})',
	'storyboard-cannotbelonger' => 'Ваша гісторыя даўжэй на <b>$1</b> {{PLURAL:$1|сымбаль|сымбалі|сымбаляў}}!',
	'storyboard-charsneeded' => '({{PLURAL:$1|неабходны яшчэ $1 сымбаль|неабходныя яшчэ $1 сымбалі|неабходныя яшчэ $1 сымбаляў}})',
	'storyboard-needtoagree' => 'Вам неабходна пагадзіцца на публікацыю Вашай гісторыі перад яе адпраўкай.',
	'storyboard-createdsucessfully' => 'Дзякуй Вам за тое, што падзяліліся з намі Вашай гісторыяй!
Мы разгледзім яе ў бліжэйшы час.
Вы можаце [$1 пачытаць ужо апублікаваныя гісторыі].',
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
	'storyboard-option-unpublished' => 'nann-embannet',
	'storyboard-option-published' => 'embannet',
	'storyboard-option-hidden' => 'kuzhet',
	'story' => 'Istor',
	'storyboard-submittedbyon' => "Kinniget gant $1 d'an $2, $3.",
	'storyboard-viewstories' => 'Gwelet an istorioù',
	'storyboard-nosuchstory' => "N'eus ket eus an istor hoc'h eus goulennet. Marteze eo bet dilamet.",
	'storyboard-storyunpublished' => "N'eo ket bet embannet c'hoazh an istor hoc'h eus goulennet.",
	'storyboard-nostorytitle' => "Rankout a reoc'h reiñ titl hag ID an istor hoc'h eus c'hoant diskwel.",
	'storyboard-cantedit' => "N'o peus ket ar gwirioù ret evit kemmañ istorioù.",
	'storyboard-canedit' => "Gellout a reoc'h [$1 kemmañ] hag embann an istor-mañ.",
	'storyboard-createdandmodified' => "Krouet d'an $1, $2 ha kemm diwezhañ d'an $3, $4",
	'storyboard-authorname' => 'Anv an oberour',
	'storyboard-authorlocation' => "Lec'hiadur an oberour",
	'storyboard-authoroccupation' => 'Oberiantiz an oberour',
	'storyboard-authoremail' => "Chomlec'h postel an oberour",
	'storyboard-thestory' => 'An istor',
	'storyboard-storystate' => 'Stad',
	'storyboard-language' => 'Yezh',
	'storyboard-storymetadata' => 'Kaset gant $1 eus $2 war $3, $4.',
	'storyboard-submissioncomplete' => 'Kinnig echuet',
	'storyboard-submissionincomplete' => "C'hwitet en deus ar c'has",
	'storyboard-alreadyexists' => '"$1" a zo kemeret dija.',
	'storyboard-changetitle' => 'Kemmañ an titl.',
	'storyboard-notsubmitted' => "Ar c'hevreañ en deus c'hwitet. N'eo bet enrollet istor ebet.",
	'storyreview' => 'Barnadenn an istor',
	'storyboard-hideimage' => 'Kuzhat ar skeudenn',
	'storyboard-unhideimage' => 'Diskouez ar skeudenn',
	'storyboard-deleteimage' => 'Dilemel ar skeudenn',
	'storyboard-yourname' => "Hoc'h anv",
	'storyboard-location' => "Ho lec'hiadur",
	'storyboard-occupation' => 'Ho micher',
	'storyboard-story' => 'Ho istor',
	'storyboard-photo' => "Ur poltred ouzhoc'h hoc'h eus ?
Perak chom hep rannañ anezhi ?",
	'storyboard-email' => "Ho chomlec'h postel",
	'storyboard-storytitle' => 'Un titl, berr hag evit deskrivañ',
	'storyboard-charsleft' => '($1 arouezenn{{PLURAL:$1||}} a chom{{PLURAL:$1||}})',
	'storyboard-cannotbelonger' => '<b>$1</b> arouezenn{{PLURAL:$1||}} e re en deus ho istor !',
	'storyboard-charsneeded' => "(ezhomm 'zo $1 arouezenn ouzhpenn{{PLURAL:$1||}})",
	'storyboard-needtoagree' => "Rankout a reoc'h aprouiñ embannadur ho istor evit gellet kinnig anezhi.",
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
	'storyboard-option-unpublished' => 'Unveröffentlicht',
	'storyboard-option-published' => 'Veröffentlicht',
	'storyboard-option-hidden' => 'Verborgen',
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
	'storyboard-storystate' => 'Land',
	'storyboard-language' => 'Sprache',
	'storyboard-storymetadata' => 'Eingereicht von $1 aus $2 am $3, $4.',
	'storyboard-submissioncomplete' => 'Die Einreichung ist abgeschlossen',
	'storyboard-submissionincomplete' => 'Einreichung gescheitert',
	'storyboard-alreadyexists' => '„$1“ wird bereits verwendet.',
	'storyboard-changetitle' => 'Ändere den Titel',
	'storyboard-notsubmitted' => 'Die Authentifizierung ist fehlgeschlagen und es wurde keine Botschaft gespeichert.',
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
	'storyboard-storytitle' => 'Ein kurzer, aussagekräftiger Titel',
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
	'storyboard-option-unpublished' => 'njewózjawjony',
	'storyboard-option-published' => 'wózjawjony',
	'storyboard-option-hidden' => 'schowany',
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
	'storyboard-storystate' => 'Stat',
	'storyboard-language' => 'Rěc',
	'storyboard-storymetadata' => 'Wót $1 z $2 dnja $3, $4 zawóstajeny.',
	'storyboard-submissioncomplete' => 'Zapódaśe dopołne',
	'storyboard-submissionincomplete' => 'Zapódaśe jo se njeraźiło',
	'storyboard-alreadyexists' => '"$1" južo eksistěrujo.',
	'storyboard-changetitle' => 'Titel změniś.',
	'storyboard-notsubmitted' => 'Awtentifikacija jo se njeraźiła, žedno tšojenje jo se składowało.',
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
	'storyboard-storytitle' => 'Krotki, wugroniwy titel',
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
 * @author Locos epraix
 * @author Tempestas
 */
$messages['es'] = array(
	'storyboard-name' => 'Panel histórico',
	'storyboard-desc' => 'Proporciona una [[Special:Story|Página de destino para los donantes]], una página donde las historias pueden ser [[Special:StorySubmission|presentadas]] y un [[Special:StoryReview|historia de la moderación de la interfaz]]',
	'right-storyreview' => 'Revisar, editar, publicar y ocultar historias',
	'storyboard-unpublished' => 'Inédito',
	'storyboard-published' => 'Publicado',
	'storyboard-hidden' => 'Oculto',
	'storyboard-unpublish' => 'No publicar',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Ocultar',
	'storyboard-option-unpublished' => 'Sin publicar',
	'storyboard-option-published' => 'Publicado',
	'storyboard-option-hidden' => 'Oculto',
	'story' => 'Historia',
	'storyboard-submittedbyon' => 'Enviado por $1 en $2, $3.',
	'storyboard-viewstories' => 'Ver historias.',
	'storyboard-nosuchstory' => 'La historia solicitada no existe.
Puede haber sido eliminada.',
	'storyboard-storyunpublished' => 'La historia solicitada aún no ha sido publicada.',
	'storyboard-nostorytitle' => 'Necesita especificar el titulo o la ID de la historia que desea ver.',
	'storyboard-cantedit' => 'No tiene permiso para editar historias.',
	'storyboard-canedit' => 'Puede [$1 editar] y publicar esta historia.',
	'storyboard-createdandmodified' => 'Creado en $1, $2 y última modificación en $3, $4',
	'storyboard-authorname' => 'Nombre de autor',
	'storyboard-authorlocation' => 'Ubicación de autor',
	'storyboard-authoroccupation' => 'Ocupación de autor',
	'storyboard-authoremail' => 'Dirección de correo electrónico de autor',
	'storyboard-thestory' => 'La historia',
	'storyboard-storystate' => 'Estado',
	'storyboard-language' => 'Idioma',
	'storyboard-storymetadata' => 'Enviado por $1 de $2 en $3, $4.',
	'storyboard-submissioncomplete' => 'Presentación completada.',
	'storyboard-submissionincomplete' => 'Envío fracasó',
	'storyboard-alreadyexists' => '"$1" ya está tomada.',
	'storyboard-changetitle' => 'Cambiar el título.',
	'storyboard-notsubmitted' => 'Error de autenticación, ninguna historia ha sido grabada.',
	'storyreview' => 'Revisión de historia',
	'storyboard-hideimage' => 'Ocultar imagen',
	'storyboard-unhideimage' => 'Mostrar imagen',
	'storyboard-deleteimage' => 'Borrar imagen',
	'storyboard-yourname' => 'Tu nombre',
	'storyboard-location' => 'Tu ubicación',
	'storyboard-occupation' => 'Tu ocupación',
	'storyboard-story' => 'Su historia.',
	'storyboard-photo' => '¿Tiene una foto propia?
¿Por qué no compartirla?',
	'storyboard-email' => 'Tu dirección de correo electrónico',
	'storyboard-storytitle' => 'Un título corto y descriptivo',
	'storyboard-agreement' => 'Estoy de acuerdo con la publicación y el uso de esta historia bajo los términos de la licencia [http://creativecommons.org/licenses/by-sa/3.0/deed.es Creative Commons Atribución/Compartir-Igual].',
	'storyboard-charsleft' => '({{PLURAL:$1|queda un carácter|quedan $1 caracteres}})',
	'storyboard-cannotbelonger' => 'Su historia es <b>$1</b> {{PLURAL:$1|carácter|caracteres}} ¡demasiado largo!',
	'storyboard-charsneeded' => '({{PLURAL:$1|se necesita un carácter más|se necesitan $1 caracteres más}})',
	'storyboard-needtoagree' => 'Necesita llegar a un acuerdo para la publicación de la presentación de su historia.',
	'storyboard-createdsucessfully' => '¡Gracias por compartir su historia con nosotros!
La revisaremos en breve.
Puede [$1 Leer historias publicadas]',
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
	'storyboard-option-unpublished' => 'non publié',
	'storyboard-option-published' => 'publié',
	'storyboard-option-hidden' => 'caché',
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
	'storyboard-storystate' => 'État',
	'storyboard-language' => 'Langue',
	'storyboard-storymetadata' => 'Soumis par $1 depuis $2 sur $3, $4.',
	'storyboard-submissioncomplete' => 'Proposition achevée',
	'storyboard-submissionincomplete' => 'La soumission a échoué',
	'storyboard-alreadyexists' => '« $1 » est déjà pris.',
	'storyboard-changetitle' => 'Modifier le titre.',
	'storyboard-notsubmitted' => 'L’identification a échoué. Aucune histoire n’a été enregistrée.',
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
	'storyboard-storytitle' => 'Un titre, court et descriptif',
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
 * @author Tempestas
 * @author Toliño
 */
$messages['gl'] = array(
	'storyboard-name' => 'Taboleiro de historias',
	'storyboard-desc' => 'Proporciona unha [[Special:Story|páxina de chegada para os doantes]], unha páxina desde a que se poden [[Special:StorySubmission|enviar]] historias e unha [[Special:StoryReview|interface para moderar o seu envío]]',
	'right-storyreview' => 'Revisar, editar, publicar e agochar historias',
	'storyboard-unpublished' => 'Sen publicar',
	'storyboard-published' => 'Publicada',
	'storyboard-hidden' => 'Agochada',
	'storyboard-unpublish' => 'Retirar a publicación',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Agochar',
	'storyboard-option-unpublished' => 'non publicada',
	'storyboard-option-published' => 'publicada',
	'storyboard-option-hidden' => 'agochada',
	'story' => 'Historia',
	'storyboard-submittedbyon' => 'Enviado por $1 o $2, $3',
	'storyboard-viewstories' => 'Ver as historias',
	'storyboard-nosuchstory' => 'A historia solicitada non existe.
Pode ter sido eliminada.',
	'storyboard-storyunpublished' => 'A historia que solicitou aínda non foi publicada.',
	'storyboard-nostorytitle' => 'Ten que especificar o título ou a ID da historia que desexa ver.',
	'storyboard-cantedit' => 'Non ten os permisos necesarios para editar historias.',
	'storyboard-canedit' => 'Pode [$1 editar] e publicar esta historia.',
	'storyboard-createdandmodified' => 'Creada o $1, $2 e modificada por última vez o $3, $4',
	'storyboard-authorname' => 'Nome do autor',
	'storyboard-authorlocation' => 'Localización do autor',
	'storyboard-authoroccupation' => 'Profesión do autor',
	'storyboard-authoremail' => 'Enderezo de correo electrónico do autor',
	'storyboard-thestory' => 'A historia',
	'storyboard-storystate' => 'Estado',
	'storyboard-language' => 'Lingua',
	'storyboard-storymetadata' => 'Enviada por $1 desde $2 o $3 ás $4.',
	'storyboard-submissioncomplete' => 'Envío completado',
	'storyboard-submissionincomplete' => 'Erro no envío',
	'storyboard-alreadyexists' => '"$1" xa se usou.',
	'storyboard-changetitle' => 'Cambie o título.',
	'storyboard-notsubmitted' => 'Erro na autenticación. Non se gardou a historia.',
	'storyreview' => 'Revisión da historia',
	'storyboard-hideimage' => 'Agochar a imaxe',
	'storyboard-unhideimage' => 'Amosar a imaxe',
	'storyboard-deleteimage' => 'Borrar a imaxe',
	'storyboard-yourname' => 'O seu nome',
	'storyboard-location' => 'A súa localización',
	'storyboard-occupation' => 'A súa profesión',
	'storyboard-story' => 'A súa historia',
	'storyboard-photo' => 'Ten unha foto de si mesmo?
Por que non compartila?',
	'storyboard-email' => 'O seu enderezo de correo electrónico',
	'storyboard-storytitle' => 'Un título curto e descritivo',
	'storyboard-agreement' => 'Acepto a publicación e o uso desta historia baixo os termos da [http://creativecommons.org/licenses/by-sa/3.0/deed.gl licenza Creative Commons recoñecemento compartir igual].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|carácter restante|caracteres restantes}})',
	'storyboard-cannotbelonger' => 'A súa historia ten {{PLURAL:$1|<b>un</b> carácter|<b>$1</b> caracteres}} de máis!',
	'storyboard-charsneeded' => '({{PLURAL:$1|necesítase un carácter máis|necesítanse $1 caracteres máis}})',
	'storyboard-needtoagree' => 'Ten que estar de acordo coa publicación da súa historia para enviala.',
	'storyboard-createdsucessfully' => 'Grazas por compartir a súa historia connosco!
Analizarémola en breve.
Entrementres, pode [$1 ler outras historias publicadas].',
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
	'storyboard-option-unpublished' => 'njewozjewjeny',
	'storyboard-option-published' => 'wozjewjeny',
	'storyboard-option-hidden' => 'schowany',
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
	'storyboard-storystate' => 'Stat',
	'storyboard-language' => 'Rěč',
	'storyboard-storymetadata' => 'Wot $1 z $2 dnja $3, $4 zawostajeny.',
	'storyboard-submissioncomplete' => 'Zapodaće dospołne',
	'storyboard-submissionincomplete' => 'Zapodaće je so njeporadźiło',
	'storyboard-alreadyexists' => '"$1" hižo eksistuje.',
	'storyboard-changetitle' => 'Titul změnić.',
	'storyboard-notsubmitted' => 'Awtentifikacija je so njeporadźiła, žane powědančko je so składowało.',
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
	'storyboard-storytitle' => 'Krótki, wuprajiwy titul',
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
	'storyboard-option-unpublished' => 'non publicate',
	'storyboard-option-published' => 'publicate',
	'storyboard-option-hidden' => 'celate',
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
	'storyboard-storystate' => 'Stato',
	'storyboard-language' => 'Lingua',
	'storyboard-storymetadata' => 'Submittite per $1 ex $2 le $3 a $4.',
	'storyboard-submissioncomplete' => 'Submission complete',
	'storyboard-submissionincomplete' => 'Submission fallite',
	'storyboard-alreadyexists' => '"$1" es ja in uso.',
	'storyboard-changetitle' => 'Cambia le titulo.',
	'storyboard-notsubmitted' => 'Authentication fallite, nulle historia ha essite salveguardate.',
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
	'storyboard-storytitle' => 'Un titulo curte e descriptive',
	'storyboard-agreement' => 'Io accepta le publication e le uso de iste historia sub le conditiones del [http://creativecommons.org/licenses/by-sa/3.0/ licentia Creative Commons Attribution/Share-Alike].',
	'storyboard-charsleft' => '($1 {{PLURAL:$1|character|characteres}} restante)',
	'storyboard-cannotbelonger' => 'Tu historia es troppo longe de <b>$1</b> {{PLURAL:$1|character|characteres}}!',
	'storyboard-charsneeded' => '($1 plus {{PLURAL:$1|character|characteres}} necessari)',
	'storyboard-needtoagree' => 'Tu debe approbar le publication de tu historia pro submitter lo.',
	'storyboard-createdsucessfully' => 'Gratias pro partir tu historia con nos!
Nos lo revidera tosto.
Tu pote [$1 leger le historias ja publicate].',
);

/** Indonesian (Bahasa Indonesia)
 * @author Kenrick95
 */
$messages['id'] = array(
	'storyboard-hide' => 'Sembunyikan',
);

/** Japanese (日本語)
 * @author Aotake
 * @author 青子守歌
 */
$messages['ja'] = array(
	'storyboard-name' => '絵コンテ',
	'storyboard-desc' => 'ストーリーの[[Special:StorySubmission|投稿]]ができる[[Special:Story|提供者のための着地ページ]]と[[Special:StoryReview|ストーリー改変インターフェース]]を準備する。',
	'right-storyreview' => '査読、編集、公開、ストーリーを非表示にする',
	'storyboard-unpublished' => '非公開',
	'storyboard-published' => '公開',
	'storyboard-hidden' => '非表示',
	'storyboard-unpublish' => '非公開',
	'storyboard-publish' => '公開',
	'storyboard-hide' => '非表示にする',
	'storyboard-option-unpublished' => '非公開',
	'storyboard-option-published' => '公開',
	'storyboard-option-hidden' => '非表示',
	'story' => 'ストーリー',
	'storyboard-submittedbyon' => '$1が $2$3に投稿',
	'storyboard-viewstories' => 'ストーリーを表示',
	'storyboard-nosuchstory' => 'リクエストしたストーリーは存在していません。削除されたのかもしれません。',
	'storyboard-storyunpublished' => 'リクエストしたストーリーはまだ公開されていません。',
	'storyboard-nostorytitle' => '閲覧したいストーリーのタイトルまたはIDを指定する必要があります。',
	'storyboard-cantedit' => 'ストーリーを編集する権限がありません。',
	'storyboard-canedit' => 'このストーリーを[$1 編集]および公開することができます。',
	'storyboard-createdandmodified' => '$1$2に作成、$3$4に最終更新',
	'storyboard-authorname' => '著者名',
	'storyboard-authorlocation' => '作者の住所',
	'storyboard-authoroccupation' => '作者の職業',
	'storyboard-authoremail' => '作者の電子メールアドレス',
	'storyboard-thestory' => 'ストーリー',
	'storyboard-storystate' => '状態',
	'storyboard-language' => '言語',
	'storyboard-storymetadata' => '$1により$2から$3$4に投稿。',
	'storyboard-submissioncomplete' => '投稿完了',
	'storyboard-submissionincomplete' => '投稿失敗',
	'storyboard-alreadyexists' => '"$1"は既に使用されています。',
	'storyboard-changetitle' => 'タイトルを変更。',
	'storyboard-notsubmitted' => '認証に失敗したため、ストーリーは保存されませんでした。',
	'storyreview' => 'ストーリーの評価',
	'storyboard-hideimage' => '画像を隠す',
	'storyboard-unhideimage' => '画像を表示',
	'storyboard-deleteimage' => '画像を削除',
	'storyboard-yourname' => 'あなたの名前',
	'storyboard-location' => 'あなたの位置',
	'storyboard-occupation' => 'あなたの職業',
	'storyboard-story' => 'あなたのストーリー',
	'storyboard-photo' => 'ご自分の写真をお持ちですか？公開してみませんか？',
	'storyboard-email' => 'あなたの電子メールアドレス',
	'storyboard-storytitle' => '短く説明的なタイトル',
	'storyboard-agreement' => '私はこのストーリーの公開と使用を[http://creativecommons.org/licenses/by-sa/3.0/deed.ja Creative Commons 表示-継承ライセンス]の条件の下に行なうことに同意します。',
	'storyboard-charsleft' => '(残り$1文字)',
	'storyboard-cannotbelonger' => 'あなたのストーリーは文字数を<b>$1</b>文字分超過しています！',
	'storyboard-charsneeded' => '($1文字不足)',
	'storyboard-needtoagree' => '投稿するにはストーリーの公開に同意する必要があります。',
	'storyboard-createdsucessfully' => 'ストーリーを私たちと共有してくださってありがとうございます！すぐに評価を行なう予定です。[$1 公開されているストーリーを読む]ことができます。',
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
	'storyboard-storytitle' => 'E kuerzen Titel de beschreift wourëms et geet',
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
	'storyboard-option-unpublished' => 'необјавени',
	'storyboard-option-published' => 'објавени',
	'storyboard-option-hidden' => 'скриени',
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
	'storyboard-storystate' => 'Сојуз. држава',
	'storyboard-language' => 'Јазик',
	'storyboard-storymetadata' => 'Поднесена од $1 од $2 на $3, $4.',
	'storyboard-submissioncomplete' => 'Поднесувањето е завршено',
	'storyboard-submissionincomplete' => 'Поднесувањето не успеа',
	'storyboard-alreadyexists' => '„$1“ е веќе зафатено.',
	'storyboard-changetitle' => 'Смени наслов',
	'storyboard-notsubmitted' => 'Потврдувањето не успеа. Приказната не беше зачувана.',
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
	'storyboard-storytitle' => 'Краток и описен наслов',
	'storyboard-agreement' => 'Се согласувам приказнава да се објави и користи согласно условите на лиценцата [http://creativecommons.org/licenses/by-sa/3.0/deed.mk Creative Commons Наведи извор/Сподели под исти услови].',
	'storyboard-charsleft' => '({{PLURAL:$1|Ви преостанува уште|Ви преостануваат уште}} $1 {{PLURAL:$1|знак|знаци}})',
	'storyboard-cannotbelonger' => 'Вашата приказна е за <b>$1</b> {{PLURAL:$1|знак|знаци}} подолга од дозволеното!',
	'storyboard-charsneeded' => '({{PLURAL:$1|потребен е|потребни се}} уште $1 {{PLURAL:$1|знак|знаци}})',
	'storyboard-needtoagree' => 'Мора да се согласите да ја објавите приказната за да ја поднесете.',
	'storyboard-createdsucessfully' => 'Ви благодаримне што ја споделивте Вашата приказна со нас!
Набргу ќе ја прегледаме.
Можете да ги [$1 прочитате објавените приказни].',
);

/** Erzya (Эрзянь)
 * @author Botuzhaleny-sodamo
 */
$messages['myv'] = array(
	'storyboard-hide' => 'Кекшемс',
	'storyboard-option-hidden' => 'кекшезь',
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
	'storyboard-option-unpublished' => 'ongepubliceerd',
	'storyboard-option-published' => 'gepubliceerd',
	'storyboard-option-hidden' => 'verborgen',
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
	'storyboard-storystate' => 'Status',
	'storyboard-language' => 'Taal',
	'storyboard-storymetadata' => 'Ingezonden door $1 van $2 op $3 om $4.',
	'storyboard-submissioncomplete' => 'Verzenden is voltooid',
	'storyboard-submissionincomplete' => 'Het insturen is mislukt',
	'storyboard-alreadyexists' => '"$1" wordt al als verhaalnaam gebruikt.',
	'storyboard-changetitle' => 'Wijzig de verhaalnaam.',
	'storyboard-notsubmitted' => 'Authenticatie mislukt.
Het verhaal is niet opgeslagen.',
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
	'storyboard-storytitle' => 'Een korte, beschrijvende titel',
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
	'storyboard-unpublished' => 'Upubliserte',
	'storyboard-published' => 'Publiserte',
	'storyboard-hidden' => 'Skjulte',
	'storyboard-unpublish' => 'Upubliser',
	'storyboard-publish' => 'Publiser',
	'storyboard-hide' => 'Skjul',
	'storyboard-option-unpublished' => 'upublisert',
	'storyboard-option-published' => 'publisert',
	'storyboard-option-hidden' => 'skjult',
	'story' => 'Historie',
	'storyboard-submittedbyon' => 'Insendt av $1 den $2, $3.',
	'storyboard-viewstories' => 'Vis historier',
	'storyboard-authorname' => 'Forfatters navn',
	'storyboard-authorlocation' => 'Forfatters plassering',
	'storyboard-authoroccupation' => 'Forfatters yrke',
	'storyboard-authoremail' => 'Forfatters e-postadresse',
	'storyboard-thestory' => 'Historien',
	'storyboard-storystate' => 'Status',
	'storyboard-language' => 'Språk',
	'storyboard-storymetadata' => 'Innsendt av $1 fra $2 den $3, $4.',
	'storyboard-submissioncomplete' => 'Innsending fullført',
	'storyboard-submissionincomplete' => 'Innsending mislyktes',
	'storyboard-alreadyexists' => '«$1» er allerede tatt.',
	'storyboard-changetitle' => 'Endre tittelen.',
	'storyboard-hideimage' => 'Skjul bilde',
	'storyboard-unhideimage' => 'Vis bilde',
	'storyboard-deleteimage' => 'Slett bilde',
	'storyboard-yourname' => 'Ditt navn',
	'storyboard-location' => 'Din plassering',
	'storyboard-occupation' => 'Ditt yrke',
	'storyboard-story' => 'Din historie',
	'storyboard-photo' => 'Har du et bilde av degselv?
Hvorfor ikke dele det?',
	'storyboard-email' => 'Din e-postadresse',
	'storyboard-storytitle' => 'En kort, beskrivende tittel',
	'storyboard-agreement' => 'Jeg er enig med publiseringen og bruken av denne historien under lisensvilkårene i [http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Navngivelse-Del på samme vilkår]',
	'storyboard-charsleft' => '({{PLURAL:$1|ett|$1}} tegn igjen)',
	'storyboard-cannotbelonger' => 'Historien din er <b>$1</b> tegn for {{PLURAL:$1|langt|lang}}!',
	'storyboard-charsneeded' => '(trenger {{PLURAL:$1|ett|$1}} tegn til)',
	'storyboard-needtoagree' => 'Du må godta publiseringen av historien din for å sende den inn.',
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
	'storyboard-desc' => "A dà na [[Special:Story|pàgina d'ariv për ij donator]], na [[Special:StorySubmission|pàgina andoa le stòrie a peulo esse butà]], e n'[[Special:StoryReview|antërfacia ëd moderassion dle stòrie]]",
	'right-storyreview' => 'Lese torna, modifiché, publiché e stërmé le stòrie',
	'storyboard-unpublished' => 'Nen publicà',
	'storyboard-published' => 'Publicà',
	'storyboard-hidden' => 'Stërmà',
	'storyboard-unpublish' => 'Pùblica pa',
	'storyboard-publish' => 'Publiché',
);

/** Portuguese (Português)
 * @author Hamilton Abreu
 */
$messages['pt'] = array(
	'storyboard-name' => 'Histórias',
	'storyboard-desc' => 'Fornece uma [[Special:Story|página de destino para beneméritos]], uma página onde se podem [[Special:StorySubmission|enviar]] histórias e uma [[Special:StoryReview|interface de moderação das histórias]]',
	'right-storyreview' => 'Rever, editar, publicar e ocultar histórias',
	'storyboard-unpublished' => 'Não publicada',
	'storyboard-published' => 'Publicada',
	'storyboard-hidden' => 'Ocultada',
	'storyboard-unpublish' => 'Retirar de publicação',
	'storyboard-publish' => 'Publicar',
	'storyboard-hide' => 'Ocultar',
	'storyboard-option-unpublished' => 'não publicada',
	'storyboard-option-published' => 'publicada',
	'storyboard-option-hidden' => 'oculta',
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
	'storyboard-storystate' => 'Estado',
	'storyboard-language' => 'Língua',
	'storyboard-storymetadata' => 'Enviada por $1 de $2 em $3, às $4.',
	'storyboard-submissioncomplete' => 'Envio finalizado',
	'storyboard-submissionincomplete' => 'Envio falhou',
	'storyboard-alreadyexists' => '"$1" já foi usado.',
	'storyboard-changetitle' => 'Alterar o título.',
	'storyboard-notsubmitted' => 'A autenticação falhou; não foi gravada nenhuma história.',
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
	'storyboard-storytitle' => 'Um título curto e descritivo',
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
	'storyboard-option-unpublished' => 'неопубликована',
	'storyboard-option-published' => 'опубликована',
	'storyboard-option-hidden' => 'скрыта',
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
	'storyboard-storystate' => 'Состояние',
	'storyboard-language' => 'Язык',
	'storyboard-storymetadata' => 'Отправлена $1 с $2 $3, $4.',
	'storyboard-submissioncomplete' => 'Подача произведена',
	'storyboard-submissionincomplete' => 'Ошибка при отправке',
	'storyboard-alreadyexists' => '«$1» уже занято.',
	'storyboard-changetitle' => 'Изменить название.',
	'storyboard-notsubmitted' => 'Аутентификация не удалась, ни одна история не была сохранена.',
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
	'storyboard-storytitle' => 'Короткий описательный заголовок',
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

