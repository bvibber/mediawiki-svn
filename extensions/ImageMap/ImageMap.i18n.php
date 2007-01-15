<?php

/**
 * Internationalisation file for ImageMap extension
 */

function efImageMapMessages() {
	return array(

/* English (Tim Starling) */
'en' => array(
'imagemap_no_image'             => '&lt;imagemap&gt;: must specify an image in the first line',
'imagemap_invalid_image'        => '&lt;imagemap&gt;: image is invalid or non-existent',
'imagemap_no_link'              => '&lt;imagemap&gt;: no valid link was found at the end of line $1',
'imagemap_invalid_title'        => '&lt;imagemap&gt;: invalid title in link at line $1',
'imagemap_missing_coord'        => '&lt;imagemap&gt;: not enough coordinates for shape at line $1',
'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: unrecognised shape at line $1, each line must start with one of: '.
								   'default, rect, circle or poly',
'imagemap_no_areas'             => '&lt;imagemap&gt;: at least one area specification must be given',
'imagemap_invalid_coord'        => '&lt;imagemap&gt;: invalid coordinate at line $1, must be a number',
'imagemap_invalid_desc'         => '&lt;imagemap&gt;: invalid desc specification, must be one of: $1',
'imagemap_description'          => 'About this image',
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
),

/* Indonesian (Ivan Lanin) */
'id' => array(
'imagemap_no_image'             => '&lt;imagemap&gt;: harus memberikan suatu gambar di baris pertama',
'imagemap_invalid_image'        => '&lt;imagemap&gt;: gambar tidak sah atau tidak ditemukan',
'imagemap_no_link'              => '&lt;imagemap&gt;: tidak ditemukan pranala yang sah di akhir baris $1',
'imagemap_invalid_title'        => '&lt;imagemap&gt;: judul tidak sah pada pranal di baris $1',
'imagemap_missing_coord'        => '&lt;imagemap&gt;: tidak cukup koordinat untuk bentuk pada baris $1',
'imagemap_unrecognised_shape'   => '&lt;imagemap&gt;: bentuk tak dikenali pada baris $1, tiap baris harus dimulai dengan salah satu dari: '.
								   'default, rect, circle atau poly',
'imagemap_no_areas'             => '&lt;imagemap&gt;: harus diberikan paling tidak satu spesifikasi area',
'imagemap_invalid_coord'        => '&lt;imagemap&gt;: koordinat tidak sah pada baris $1, haruslah berupa angka',
'imagemap_invalid_desc'         => '&lt;imagemap&gt;: spesifikasi desc tidak sah, harus salah satu dari: $1',
'imagemap_description'          => 'Tentang gambar ini',
# Note to translators: keep the same order
'imagemap_desc_types'           => 'top-right, bottom-right, bottom-left, top-left, none',
),

	);
}

?>