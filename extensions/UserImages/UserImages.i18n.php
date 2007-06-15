<?php

/**
 * Internationalisation file for User Image Gallery extension
*
 * @addtogroup Extensions
 * @author Rob Church <robchur@gmail.com>
 */

function efUserImagesMessages() {
	$messages = array(

/* English */
'en' => array(
'userimages-caption' => 'Images uploaded by $1',
'userimages-noname' => 'Invalid username or none provided.',
'userimages-noimages' => '$1 has no image uploads.',
),
/* Arabic */
'ar' => array(
'userimages-caption' => 'الصور المرفوعة بواسطة $1',
'userimages-noname' => 'اسم مستخدم خاطئ أو لم يتم تحديد أحد.',
'userimages-noimages' => 'ليس لدى $1 صور مرفوعة.',
),
/* French */
'fr' => array(
'userimages-caption' => 'Images importées par $1',
'userimages-noname' => 'Nom d’utilisateur invalide ou manquant.',
'userimages-noimages' => '$1 n’a importé aucune image.',
),
/* Indonesian */
'id' => array(
'userimages-caption' => 'Berkas yang dimuatkan oleh $1',
'userimages-noname' => 'Nama pengguna tidak sah atau tidak diberikan.',
'userimages-noimages' => '$1 tidak pernah memuatkan berkas.',
),
/* Italian */
'it' => array(
'userimages-caption' => 'Immagini caricate dall\'utente $1',
'userimages-noname' => 'Nome utente errato o mancante.',
'userimages-noimages' => 'L\'utente $1 non ha caricato immagini.',
),
/* Japanese */
'ja' => array(
'userimages-caption' => '$1 によってアップロードされた画像',
'userimages-noname' => '利用者名が存在しないか不正な入力です。',
'userimages-noimages' => '$1 は画像をアップロードしていません。',
),
/* nld / Dutch */
'nl' => array(
'userimages-caption' => 'Afbeeldingen toegevoegd door $1',
'userimages-noname' => 'Onjuiste gebruikersnaam of geen naam opgegeven.',
'userimages-noimages' => '$1 heeft geen afbeeldingen toegevoegd.',
),
/* Norwegian (Jon Harald Søby) */
'no' => array(
'userimages-caption' => 'Bilder lastet opp av $1',
'userimages-noname' => 'Ugyldig brukernavn, eller intet oppgitt brukernavn.',
'userimages-noimages' => '$1 har ingen bildeopplastinger.',
),
'oc' => array(
'userimages-caption' => 'Imatges importats per $1',
'userimages-noname' => 'Nom d’utilizaire invalid o mancant.',
'userimages-noimages' => '$1 a pas importat cap d\'imatge.',
),
/* Slovak (by helix84) */
'sk' => array(
'userimages-caption' => 'Obrázky nahrané používateľom $1',
'userimages-noname' => 'Neplatné alebo neposkytnuté používateľské meno.',
'userimages-noimages' => '$1 nenahral žiadne obrázky.',
),
/* Serbian default */
'sr' => array(
'userimages-caption' => 'Слике које је послао корисник $1',
'userimages-noname' => 'Погрешно корисничко име или корисник није послао ни једну слику.',
'userimages-noimages' => '$1 нема послатих слика.',
),
/* Serbian cyrillic */
'sr-ec' => array(
'userimages-caption' => 'Слике које је послао корисник $1',
'userimages-noname' => 'Погрешно корисничко име или корисник није послао ни једну слику.',
'userimages-noimages' => '$1 нема послатих слика.',
),
/* Serbian latin */
'sr-el' => array(
'userimages-caption' => 'Slike koje je poslao korisnik $1',
'userimages-noname' => 'Pogrešno korisničko ime ili korisnik nije poslao ni jednu sliku.',
'userimages-noimages' => '$1 nema poslatih slika.',
),

/* Sundanese */
'su' => array(
'userimages-caption' => 'Gambar nu dimuatkeun ku $1',
'userimages-noname' => 'Ngaran landihanana salah atawa mémang teu aya.',
'userimages-noimages' => '$1 can kungsi ngamuatkeun gambar.',
),

/* Chinese (China) (下一次登录) */
'zh-cn' => array(
'userimages-caption' => '$1上传的图像',
'userimages-noname' => '没有或者错误的用户名。',
'userimages-noimages' => '$1没有上传过图像。',
),

/* Chinese (Taiwan) (KilluaZaoldyeck) */
'zh-tw' => array(
'userimages-caption' => '$1上傳的圖像',
'userimages-noname' => '無效或未有填寫帳號名稱。',
'userimages-noimages' => '$1沒有上傳圖像。',
),

/* Cantonese (Shinjiman) */
'zh-yue' => array(
'userimages-caption' => '由$1上載嘅圖像',
'userimages-noname' => '唔正確嘅用戶名或者冇畀到用戶名。',
'userimages-noimages' => '$1並冇圖像上載。',
),

	);
/* Chinese (Hong Kong), inherited from Chinese (Taiwan) */
$messages['zh-hk'] = $messages['zh-tw'];
/* Chinese (Singapore), inherited from Chinese (China) */
$messages['zh-sg'] = $messages['zh-cn'];

return $messages;
}

?>
