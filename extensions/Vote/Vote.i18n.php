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
	'vote-header' => 'يمكنك التصويت لاختيار \'\'\'الرئيس الأعلى للعالم\'\'\' هنا!',
	'vote-current' => 'تصويتك الحالي ل \'\'\'$1\'\'\'.',
	'vote-legend' => 'وضع أو تغيير التصويت',
	'vote-caption' => 'اختيارك:',
	'vote-choices' => 'جوكر|الجوكر
بطريق|البطريق
ريدلر|ريدلر',
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

'bcl' => array(
	'vote' => 'Magboto',
	'vote-submit' => 'Magboto',
	'vote-registered' => 'Nairehistro na an boto mo.',
	'vote-view-results' => 'Hilingón an mga resulta',
	'vote-results' => 'Mga resulta kan boto',
	'vote-results-choice' => 'Pinilî',
	'vote-results-count' => 'Bilang',
	'vote-results-none' => 'Mayong botong naikaag sa mga oras na ini.',
	'vote-login' => 'Kaipuhan mong $1 magboto.',
	'vote-login-link' => 'maglaog',
),

/** Bulgarian (Български)
 * @author DCLXVI
 */
'bg' => array(
	'vote'              => 'Гласуване',
	'vote-caption'      => 'Текущ избор:',
	'vote-submit'       => 'Гласуване',
	'vote-registered'   => 'Вашият глас беше отчетен.',
	'vote-view-results' => 'Преглед на резултатите',
	'vote-results-none' => 'Все още никой не е гласувал.',

),

'br' => array(
	'vote' => 'Votiñ',
	'vote-current' => 'Votet hoc\'h eus evit \'\'\'$1\'\'\'.',
	'vote-registered' => 'Enrollet eo bet ho vot.',
	'vote-view-results' => 'Gwelet an disoc\'hoù',
	'vote-results' => 'Disoc\'h ar votadeg',
	'vote-results-choice' => 'Dibab',
	'vote-login' => 'Ret eo deoc\'h $1 evit votiñ.',
	'vote-login-link' => 'en em enrollañ',
),

'el' => array(
	'vote' => 'Ψηφοφορία',
	'vote-caption' => 'Η επιλογή σας:',
	'vote-submit' => 'Ψηφοφορία',
	'vote-results-choice' => 'Επιλογή',
	'vote-login' => 'Πρέπει $1 να ψηφίσετε.',
	'vote-login-link' => 'Εγγραφή',
	'vote-invalid-choice' => 'Πρέπει να επιλέξετε μια από της διαθέσιμες επιλογές.',
),

'ext' => array(
	'vote' => 'Votal',
	'vote-submit' => 'Votal',
	'vote-registered' => 'El tu votu s´á rustrau.',
	'vote-results-count' => 'Cuenta',
),

'fa' => array(
	'vote' => 'رای‌گیری',
	'vote-current' => 'رای فعلی شما \'\'\'$1\'\'\' است.',
	'vote-legend' => 'رای خود را وارد یا اصلاح نمایید',
	'vote-caption' => 'انتخاب شما:',
	'vote-submit' => 'ثبت رای',
	'vote-registered' => 'رای شما ثبت شد.',
	'vote-view-results' => 'مشاهده نتایج',
	'vote-results' => 'نتیجه رای‌گیری',
	'vote-results-choice' => 'انتخاب',
	'vote-results-count' => 'شمارش',
	'vote-results-none' => 'در حال حاضر هیچ رایی ثبت نشده است.',
	'vote-login' => 'برای رای دادن باید $1.',
	'vote-login-link' => 'به سیستم وارد شوید',
	'vote-invalid-choice' => 'شما باید یکی از گزینه‌های موجود را انتخاب کنید.',
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

'gl' => array(
	'vote' => 'Votación',
	'vote-header' => 'Pode votar aquí polo \'\'\'Mellor Candidato do Mundo\'\'\'!',
	'vote-current' => 'O seu voto actual é para \'\'\'$1\'\'\'.',
	'vote-legend' => 'Votar ou modificar o voto',
	'vote-caption' => 'A súa escolla:',
	'vote-choices' => 'joker|O Joker
penguin|O Pingüín
riddler|Riddler',
	'vote-submit' => 'Votar',
	'vote-registered' => 'O seu voto foi rexistrado.',
	'vote-view-results' => 'Ver os resultados',
	'vote-results' => 'Resultados da votación',
	'vote-results-choice' => 'Escolla',
	'vote-results-count' => 'Reconto',
	'vote-results-none' => 'Aínda non se rexistrou ningún voto.',
	'vote-login' => 'Ten que $1 para votar.',
	'vote-login-link' => 'rexistrarse',
	'vote-invalid-choice' => 'Ten que escoller unha das opcións disponíbeis.',
),

'hsb' => array(
	'vote' => 'Hłosować',
	'vote-header' => 'Móžeš tu za \'\'\'Supreme Overlord of the World\'\'\' hłosować!',
	'vote-current' => 'Twój aktualny hłós je za \'\'\'$1\'\'\'.',
	'vote-legend' => 'Hłosować abo hłós změnić',
	'vote-caption' => 'Waš wuběr:',
	'vote-choices' => 'joker|Joker penguin|Penguin riddler|Riddler',
	'vote-submit' => 'Hłosować',
	'vote-registered' => 'Waše wuzwolenje bu zregistrowane.',
	'vote-view-results' => 'Wuslědki sej wobhladać',
	'vote-results' => 'Wothłosowanske wuslědki',
	'vote-results-choice' => 'Wólba',
	'vote-results-count' => 'Ličić',
	'vote-results-none' => 'Dotal njebu wothłosowane.',
	'vote-login' => 'Dyrbiš $1, zo by wothłosował.',
	'vote-login-link' => 'přizjewić',
	'vote-invalid-choice' => 'Dyrbiš jednu z k dispoziciji stejacych opcijow wubrać.',
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
	'vote-choices' => 'joker|The Joker
penguin|The Penguin
riddler|Riddler',#identical but defined
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

'pl' => array(
	'vote' => 'Głosuj',
	'vote-header' => 'Tutaj możesz zagłosować na \'\'\'Naczelnego Przywódcę Świata\'\'\'!',
	'vote-current' => 'Aktualnie Twój głos oddany jest za \'\'\'$1\'\'\'.',
	'vote-legend' => 'Zagłosuj lub zmień głos',
	'vote-caption' => 'Twój wybór:',
	'vote-submit' => 'Głosuj',
	'vote-registered' => 'Twój głos został zarejestrowany.',
	'vote-view-results' => 'Zobacz wyniki',
	'vote-results' => 'Wyniki głosowania',
	'vote-results-choice' => 'Wybór',
	'vote-results-count' => 'Policz',
	'vote-results-none' => 'Nikt jeszcze dotąd nie głosował.',
	'vote-login' => 'By głosować, musisz $1.',
	'vote-login-link' => 'zaloguj',
	'vote-invalid-choice' => 'Musisz wybrać jedną z dostępnych opcji.',
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

/** Albanian (Shqip)
 * @author Ergon
 */
'sq' => array(
	'vote' => 'Voto',
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

/* Cantonese (Shinjiman) */
'yue' => array(
	'vote' => '投票',
	'vote-header' => "你可以響呢度投下'''響呢個世界度至高無尚'''嘅一票！",
	'vote-current' => "你而家係投緊'''$1'''。",
	'vote-legend' => '放或改票',
	'vote-caption' => '你嘅選擇:',
	'vote-choices' => "joker|小丑
penguin|企鵝
riddler|謎人",
	'vote-submit' => '投',
	'vote-registered' => '你嘅一票已經記錄咗。',
	'vote-view-results' => '睇結果',
	'vote-results' => '投票結果',
	'vote-results-choice' => '選擇',
	'vote-results-count' => '數量',
	'vote-results-none' => '響呢個時間無放任何票。',
	'vote-login' => '你一定要$1先可以投票。',
	'vote-login-link' => '登入',
	'vote-invalid-choice' => '你一定要響可以揀嘅選項度揀一個。',
),

/* Chinese (Simplified) (Shinjiman) */
'zh-hans' => array(
	'vote' => '投票',
	'vote-header' => "您可以在这里投下'''在这个世界中至高无尚'''的一票！",
	'vote-current' => "您现正为'''$1'''投票。",
	'vote-legend' => '放下或更改投票',
	'vote-caption' => '您的选择:',
	'vote-choices' => "joker|小丑
penguin|企鹅
riddler|谜人",
	'vote-submit' => '投票',
	'vote-registered' => '您的一票已经记录。',
	'vote-view-results' => '看结果',
	'vote-results' => '投票结果',
	'vote-results-choice' => '选择',
	'vote-results-count' => '数量',
	'vote-results-none' => '在这个时间无放任何投票。',
	'vote-login' => '您一定要$1先可以投票。',
	'vote-login-link' => '登入',
	'vote-invalid-choice' => '您一定要在可以选择的选项中选择一个。',
),

/* Chinese (Traditonal) (Shinjiman) */
'zh-hant' => array(
	'vote' => '投票',
	'vote-header' => "您可以在這裡投下'''在這個世界中至高無尚'''的一票！",
	'vote-current' => "您現正為'''$1'''投票。",
	'vote-legend' => '放下或更改投票',
	'vote-caption' => '您的選擇:',
	'vote-choices' => "joker|小丑
penguin|企鵝
riddler|謎人",
	'vote-submit' => '投票',
	'vote-registered' => '您的一票已經記錄。',
	'vote-view-results' => '看結果',
	'vote-results' => '投票結果',
	'vote-results-choice' => '選擇',
	'vote-results-count' => '數量',
	'vote-results-none' => '在這個時間無放任何投票。',
	'vote-login' => '您一定要$1先可以投票。',
	'vote-login-link' => '登入',
	'vote-invalid-choice' => '您一定要在可以選擇的選項中選擇一個。',
),

	);

	/* Chinese defaults, fallback to zh-hans */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];

	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages;
}
