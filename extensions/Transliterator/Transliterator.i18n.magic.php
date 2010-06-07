<?php

$magicWords = array();

/**
 * English
 */
$magicWords['en'] = array(
	'transliterate' => array( '0', 'transliterate' ),
	// NOTE: only the first case-sensitive synonym is considered.
	'tr_prefix' => array( '1', 'Transliterator:' ),
	'tr_decompose' => array( '0', '__DECOMPOSE__' ),
);

$magicWords['af'] = array(
	'transliterate'   => array( '0', 'translitereer', 'transliterate' ),
);

$magicWords['ar'] = array(
	'transliterate'   => array( '0', 'ترجمة_حرفية', 'transliterate' ),
	'tr_prefix'       => array( '1', 'مترجم_حرفي:', 'Transliterator:' ),
	'tr_decompose'    => array( '0', '__تحلل__', '__DECOMPOSE__' ),
);

$magicWords['arz'] = array(
	'transliterate'   => array( '0', 'ترجمة_حرفية', 'transliterate' ),
);

$magicWords['br'] = array(
	'transliterate'   => array( '0', 'treuzlizherennañ', 'transliterate' ),
);

$magicWords['es'] = array(
	'transliterate'   => array( '0', 'transliterar', 'transliterate' ),
);

$magicWords['ml'] = array(
	'transliterate'   => array( '0', 'ലിപിമാറ്റംചെയ്യുക', 'ലിപ്യന്തരണം', 'ലിപിമാറ്റം', 'transliterate' ),
	'tr_prefix'       => array( '1', 'ലിപിമാറ്റയുപകരണം:', 'Transliterator:' ),
);

$magicWords['mr'] = array(
	'transliterate'   => array( '0', 'लिप्यांतर', 'transliterate' ),
);

$magicWords['nl'] = array(
	'transliterate'   => array( '0', 'translitereren', 'transliterate' ),
);

$magicWords['ru'] = array(
	'transliterate'   => array( '0', 'транслитерация', 'transliterate' ),
);
