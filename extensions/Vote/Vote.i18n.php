<?php

/**
 * Internationalisation file for the Vote extension
 *
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 * Please see the LICENCE file for terms of use and redistribution
 */

function efVoteMessages() {
	$messages = array(

/* English (Rob Church) */
'en' => array(
'vote' => 'Vote',
'vote-header' => "You can vote for '''Supreme Overlord of the World''' here!",
'vote-current' => "Your current vote is for '''$1'''.",
'vote-legend' => 'Place or amend vote',
'vote-caption' => 'Your selection:',
'vote-choices' => "joker|The Joker
penguin|The Penguin
riddler|Riddler",
'vote-submit' => 'Vote',
'vote-registered' => 'Your vote has been registered.',
'vote-view-results' => 'View results',
'vote-results' => 'Vote results',
'vote-results-choice' => 'Choice',
'vote-results-count' => 'Count',
'vote-results-none' => 'No votes have been placed at this time.',
'vote-login' => 'You must $1 to vote.',
'vote-login-link' => 'log in',
'vote-invalid-choice' => 'You must select one of the available options.',
),

/* Arabic (Meno25) */
'ar' => array(
'vote' => 'تصويت',
'vote-current' => 'تصويتك الحالي ل \'\'\'$1\'\'\'.',
'vote-caption' => 'اختيارك:',
'vote-submit' => 'تصويت',
'vote-registered' => 'تصويتك تم تسجيله',
'vote-view-results' => 'عرض النتائج',
'vote-results' => 'نتائج التصويت',
'vote-results-choice' => 'الاختيار',
'vote-results-count' => 'العدد',
'vote-results-none' => 'لا أصوات تم وضعها في هذا الوقت.',
'vote-login' => 'يجب عليك $1 لكي تصوت.',
'vote-login-link' => 'دخول',
'vote-invalid-choice' => 'يجب عليك اختيار واحد من الخيارات المتاحة.',
),

/* French (Ashar Voultoiz) */
'fr' => array(
'vote' => 'Vote',
'vote-header' => "Vous pouvez voter pour le '''maître de l'Univers''' ici !",
'vote-current' => "Votre vote actuel est pour '''$1'''.",
'vote-legend' => 'Placer ou modifier un vote',
'vote-caption' => 'Votre sélection :',
'vote-choices' => "joker|Le Joker
pingouin|Le Pingouin
sphinx|Sphinx",
'vote-submit' => 'Voter',
'vote-registered' => 'Votre vote a été enregistré.',
'vote-view-results' => 'Résultats',
'vote-results' => 'Résultats',
'vote-results-choice' => 'Choix',
'vote-results-count' => 'Voix',
'vote-results-none' => 'Aucun vote n\'a été placé pour le moment.',
'vote-login' => 'Vous devez $1 pour voter.',
'vote-login-link' => 'vous connecter',
'vote-invalid-choice' => 'Vous devez choisir une des options disponible.',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'vote' => 'Pemilihan',
'vote-header' => "Anda dapat memilih '''Penguasa Tertinggi Dunia''' di sini!",
'vote-current' => "Pilihan Anda saat ini adalah '''$1'''.",
'vote-legend' => 'Berikan atau ubah pilihan',
'vote-caption' => 'Pilihan Anda:',
'vote-choices' => "joker|The Joker
penguin|The Penguin
riddler|Riddler",
'vote-submit' => 'Pilih',
'vote-registered' => 'Pilihan Anda telah didaftarkan.',
'vote-view-results' => 'Lihat hasil',
'vote-results' => 'Hasil pemungutan suara',
'vote-results-choice' => 'Pilihan',
'vote-results-count' => 'Suara',
'vote-results-none' => 'Saat ini belum ada suara yang masuk.',
'vote-login' => 'Anda harus $1 untuk memilih.',
'vote-login-link' => 'masuk log',
'vote-invalid-choice' => 'Anda harus memilih salah satu pilihan yang tersedia.',
),

'nl' => array(
'vote' => 'Stemmen',
'vote-header' => 'U kunt hier stemmen voor \'\'\'Alleenheerser over de Wereld\'\'\'!',
'vote-current' => 'Uw huidige stem is voor \'\'\'$1\'\'\'.',
'vote-legend' => 'Stem of wijzig stem',
'vote-caption' => 'Uw keuze:',
'vote-submit' => 'Stem',
'vote-registered' => 'Uw stem is opgeslagen.',
'vote-view-results' => 'Bekijk resultaten',
'vote-results' => 'Resultaten stemming',
'vote-results-choice' => 'Keuze',
'vote-results-count' => 'Aantal',
'vote-results-none' => 'Er zijn nog geen stemmen uitgebracht.',
'vote-login' => 'U moet $1 om te kunnen stemmen.',
'vote-login-link' => 'aanmelden',
'vote-invalid-choice' => 'U moet een van de beschikbare opties kiezen.',
),

/* Norwegian (Jon Harald Søby) */
'no' => array(
'vote' => 'Avstemning',
'vote-header' => 'Du kan stemme på \'\'\'(eksempelbruker)\'\'\' her!',
'vote-current' => 'Din nåværende stemme er på \'\'\'$1\'\'\'.',
'vote-legend' => 'Velg hvordan du vil stemme',
'vote-caption' => 'Ditt valg:',
'vote-choices' => 'bruker1|Første brukernavn
bruker2|Andre brukernavn
bruker3|Tredje brukernavn',
'vote-submit' => 'Stem',
'vote-registered' => 'Din stemme har blitt registrert.',
'vote-view-results' => 'Vis resultater',
'vote-results' => 'Avstemningsresultater',
'vote-results-choice' => 'Valg',
'vote-results-count' => 'Antall',
'vote-results-none' => 'Ingen stemmer har blitt avgitt enda.',
'vote-login' => 'Du må $1 for å stemme.',
'vote-login-link' => 'logge inn',
'vote-invalid-choice' => 'Du må velge en av de tilgjengelige alternativene.',
),

/* Occitan (Cedric31) */
'oc' => array(
'vote' => 'Vòte',
'vote-header' => 'Podètz votar pel \'\'\'mèstre de l\'Univèrs\'\'\' aicí !',
'vote-current' => 'Vòstre vòte actual es per \'\'\'$1\'\'\'.',
'vote-legend' => 'Plaçar o modificar un vòte',
'vote-caption' => 'Vòstra seleccion:',
'vote-choices' => 'joker|Lo Joquèr
pingouin|Lo Pingoin
sphinx|Esfinx',
'vote-submit' => 'Votar',
'vote-registered' => 'Vòstre vòte es estat enregistrat.',
'vote-view-results' => 'Veire los resultats',
'vote-results' => 'Resultats del vòte',
'vote-results-choice' => 'Causida',
'vote-results-count' => 'Compte',
'vote-results-none' => 'Cap de vòte es pas estat efectuat a aqueste moment.',
'vote-login' => 'Devètz $1 per votar.',
'vote-login-link' => 'vos connectar',
'vote-invalid-choice' => 'Devètz causir una de las opcions disponibla.',
),

/* Piedmontese (Bèrto 'd Sèra) */
'pms' => array(
'vote' => 'Voté',
'vote-header' => 'A peul voté për \'\'\'ël superbargé dl<nowiki>\'</nowiki>ann\'\'\' ambelessì!',
'vote-current' => 'Sò vot corent a l\'é për \'\'\'$1\'\'\'.',
'vote-legend' => 'Voté ò cambié sò vot',
'vote-caption' => 'Soa selession:',
'vote-choices' => 'Mini|Dumini
Chin-a|Chin-a
Gioanin|Gioanin',
'vote-submit' => 'Voté',
'vote-registered' => 'Sò vot a l\'é stait registrà.',
'vote-view-results' => 'Vardé j\'arzultà',
'vote-results' => 'Arzultà dël vot',
'vote-results-choice' => 'Sërnùa',
'vote-results-count' => 'Cont',
'vote-results-none' => 'Al dì d\'ancheuj a l\'ha anco\' pa votà gnun.',
'vote-login' => 'Për voté a venta $1.',
'vote-login-link' => 'ch\'a rintra ant ël sistema',
'vote-invalid-choice' => 'A venta ch\'as selession-a un-a dj\'opsion disponibij.',
),

'sk' => array(
'vote' => 'Hlasovať',
'vote-header' => 'Tu môžete hlasovať o \'\'\'Hlavnom diktátorovi sveta\'\'\'!',
'vote-current' => 'Aktuálne hlasujete za \'\'\'$1\'\'\'.',
'vote-legend' => 'Hlasovať alebo zmeniť hlas',
'vote-caption' => 'Vaša voľba:',
'vote-submit' => 'Hlasovať',
'vote-registered' => 'Váš hlas bol zaznamenaný.',
'vote-view-results' => 'Zobraziť výsledky',
'vote-results' => 'Výsledky hlasovania',
'vote-results-choice' => 'Voľba',
'vote-results-count' => 'Počet',
'vote-results-none' => 'Momentálne nie sú žiadne hlasy.',
'vote-login' => 'Aby ste mohli hlasovať, musíte $1.',
'vote-login-link' => 'sa prihlásiť',
'vote-invalid-choice' => 'Musíte vybrať jednu z dostupných možností.',
),

/* Sundanese (Kandar via BetaWiki) */
'su' => array(
'vote-header' => 'Anjeun bisa milih \'\'\'Supreme Overlord of the World\'\'\' di dieu!',
'vote-current' => 'Anjeun geus milih \'\'\'$1\'\'\'.',
'vote-caption' => 'Pilihan anjeun:',
'vote-registered' => 'Pilihan anjeun geus didaptarkeun.',
'vote-results-count' => 'Itung',
'vote-login' => 'Anjeun kudu $1 pikeun milu milih.',
'vote-login-link' => 'asup log',
'vote-invalid-choice' => 'Anjeun kudu milih salah sahiji ti nu aya.',
),

	);
	return $messages;
}


