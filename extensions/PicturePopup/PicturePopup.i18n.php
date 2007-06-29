<?php

/**
 * Internationalisation file for the PicturePopup extension
 *
 * @addtogroup Extensions
 */

function efPicturePopupMessages() {
	$messages = array(

/* English */
'en' => array(
	'picturepopup_invalid_title' => 'Invalid image title',
	'picturepopup_no_license' => 'Image has no license tag',
	'picturepopup_no_image' => 'Image does not exist',
	'picturepopup_no_license_list' => 'License list is invalid or missing',
	'picturepopup_license_list' => 'Project:Image copyright tags',
	'picturepopup_no_license_text' => 'License template has no element with id=imageLicenseText',
	'picturepopup_invalid_icon' => 'License template has missing or invalid imageLicenseIcon element',
),

/* Indonesian */
'id' => array(
	'picturepopup_invalid_title' => 'Judul gambar tak sah',
	'picturepopup_no_license' => 'Gambar tak memiliki tag lisensi',
	'picturepopup_no_image' => 'Gambar tak ditemukan',
	'picturepopup_no_license_list' => 'Daftar lisensi tak valid atau tak ditemukan',
	'picturepopup_license_list' => 'Project:Tag hak cipta gambar',
	'picturepopup_no_license_text' => 'Templat lisensi tak memiliki elemen dengan id=imageLicenseText',
	'picturepopup_invalid_icon' => 'Templat lisensi tak memiliki atau memiliki elemen imageLicenseIcon tak sah',
),

/* Kazakh Cyrillic */
'kk-kz' => array(
	'picturepopup_invalid_title' => 'Сурет атауы жарамсыз ',
	'picturepopup_no_license' => 'Сурет лицензия белгілемесісіз',
	'picturepopup_no_image' => 'Сурет жоқ',
	'picturepopup_no_license_list' => 'Лицензиялар тізімі жарамсыз не жоғалтылған',
	'picturepopup_license_list' => '{{ns:project}}:Суреттің ауторлық құқықтар белгілемелері',
	'picturepopup_no_license_text' => 'Лицензия үлгісінде  id=imageLicenseText деген дана жоқ',
	'picturepopup_invalid_icon' => 'Лицензия үлгісінде imageLicenseIcon деген дана жоғалтылған не жарамсыз',
),
/* Kazakh Latin */
'kk-tr' => array(
	'picturepopup_invalid_title' => 'Swret atawı jaramsız ',
	'picturepopup_no_license' => 'Swret lïcenzïya belgilemesisiz',
	'picturepopup_no_image' => 'Swret joq',
	'picturepopup_no_license_list' => 'Lïcenzïyalar tizimi jaramsız ne joğaltılğan',
	'picturepopup_license_list' => '{{ns:project}}:Swrettiñ awtorlıq quqıqtar belgilemeleri',
	'picturepopup_no_license_text' => 'Lïcenzïya ülgisinde  id=imageLicenseText degen dana joq',
	'picturepopup_invalid_icon' => 'Lïcenzïya ülgisinde imageLicenseIcon degen dana joğaltılğan ne jaramsız',
),
/* Kazakh Arabic */
'kk-cn' => array(
	'picturepopup_invalid_title' => 'سۋرەت اتاۋى جارامسىز ',
	'picturepopup_no_license' => 'سۋرەت ليتسەنزييا بەلگٸلەمەسٸسٸز',
	'picturepopup_no_image' => 'سۋرەت جوق',
	'picturepopup_no_license_list' => 'ليتسەنزييالار تٸزٸمٸ جارامسىز نە جوعالتىلعان',
	'picturepopup_license_list' => '{{ns:project}}:سۋرەتتٸڭ اۋتورلىق قۇقىقتار بەلگٸلەمەلەرٸ',
	'picturepopup_no_license_text' => 'ليتسەنزييا ٷلگٸسٸندە  id=imageLicenseText دەگەن دانا جوق',
	'picturepopup_invalid_icon' => 'ليتسەنزييا ٷلگٸسٸندە imageLicenseIcon دەگەن دانا جوعالتىلعان نە جارامسىز',
),
/* Norwegian (Jon Harald Søby) */
'no' => array(
'picturepopup_invalid_title' => 'Ugyldig bildetittel',
'picturepopup_no_license' => 'Bildet har ingen lisensmerking',
'picturepopup_no_image' => 'Bildet finnes ikke',
'picturepopup_no_license_list' => 'Lisenslisten er ugyldig eller mangler',
'picturepopup_license_list' => 'Project:Bildelisensmerking',
'picturepopup_no_license_text' => 'Lisensmalen har intet element med id=imageLicenseText',
'picturepopup_invalid_icon' => 'Lisensmalen mangler eller har ugyldig imageLicenseIcon-element',
),
	);

	/* Kazakh default, fallback to kk-kz */
	$messages['kk'] = $messages['kk-kz'];

	return $messages;
}


