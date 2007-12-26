<?php

if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgMathStatFunctionsMessages = array();
$wgMathStatFunctionsMagic = array();

$wgMathStatFunctionsMessages['en'] = array(
        'msfunc_nan'      => 'Resulting value is not a number',
        'msfunc_inf'      => 'Resulting value is infinity',
        'msfunc_div_zero' => 'Division by zero',
);
$wgMathStatFunctionsMessages['ar'] = array(
	'msfunc_nan' => 'القيمة الناتجة ليست رقما',
	'msfunc_inf' => 'القيمة الناتجة هي المالانهاية',
	'msfunc_div_zero' => 'القسمة على صفر',
);
$wgMathStatFunctionsMessages['bg'] = array(
	'msfunc_nan' => 'Стойността на резултата не е число',
	'msfunc_inf' => 'Стойността на резултата е безкрайност',
	'msfunc_div_zero' => 'Деление на нула',
);
$wgMathStatFunctionsMessages['de'] = array(
        'msfunc_nan'      => 'Ergebniswert ist keine Zahl',
        'msfunc_inf'      => 'Ergebniswert ist unendlich',
        'msfunc_div_zero' => 'Division durch Null',
);
$wgMathStatFunctionsMessages['fr'] = array(
	'msfunc_nan' => 'Le résultat n’est pas un nombre.',
	'msfunc_inf' => 'Le résultat est l’infini.',
	'msfunc_div_zero' => 'Division par zéro',
);
$wgMathStatFunctionsMessages['gl'] = array(
	'msfunc_nan' => 'O valor resultante non é un número',
	'msfunc_inf' => 'O valor resultante é infinito',
	'msfunc_div_zero' => 'División por cero',
);

/** Croatian (Hrvatski)
 * @author Dnik
 */
$wgMathStatFunctionsMessages['hr'] = array(
	'msfunc_nan'      => 'Vrijednost rezultata nije broj',
	'msfunc_inf'      => 'Vrijednost rezultata je beskonačna',
	'msfunc_div_zero' => 'Dijeljenje nulom',
);

$wgMathStatFunctionsMessages['hsb'] = array(
	'msfunc_nan' => 'Wuslědk ličba njeje.',
	'msfunc_inf' => 'Wuslědk je njekónčna hódnota.',
	'msfunc_div_zero' => 'Diwizija přez nulu',
);
$wgMathStatFunctionsMessages['id'] = array(
        'msfunc_nan' => "Nilai hasil tidak berupa angka" ,
        'msfunc_inf' => "Nilai hasil tak hingga" ,
        'msfunc_div_zero' => "Pembagian dengan nol",
);

$wgMathStatFunctionsMessages['it'] = array(
        'msfunc_nan' => "Il risultato non è un numero" ,
        'msfunc_inf' => "Il risultato è infinito" ,
        'msfunc_div_zero' => "Divisione per zero",
);

$wgMathStatFunctionsMessages['ja'] = array(
        'msfunc_nan' => "返り値が数値ではありませんResulting value is not a number" ,
        'msfunc_inf' => "返り値が無限大です" ,
        'msfunc_div_zero' => "0で割り算しました",
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$wgMathStatFunctionsMessages['lb'] = array(
	'msfunc_nan'      => "D'Resultat ass keng Zuel",
	'msfunc_inf'      => "D'Resultat ass onendlech",
	'msfunc_div_zero' => 'Divisioun duerch null',
);

$wgMathStatFunctionsMessages['nl'] = array(
        'msfunc_nan' => "Resulterende waarde is geen getal" ,
        'msfunc_inf' => "Resulterende waarde is oneindig" ,
        'msfunc_div_zero' => "Deling door nul",
);

$wgMathStatFunctionsMessages['no'] = array(
	'msfunc_nan' => 'Resultatverdien er ikke et tall',
	'msfunc_inf' => 'Resultatverdien er uendelig',
	'msfunc_div_zero' => 'Deling på null',
);
$wgMathStatFunctionsMessages['oc'] = array(
	'msfunc_nan' => 'Lo resultat es pas un nombre.',
	'msfunc_inf' => 'Lo resultat es l’infinit.',
	'msfunc_div_zero' => 'Division per zèro',
);
$wgMathStatFunctionsMessages['pl'] = array(
	'msfunc_nan' => 'Wartość wynikowa nie jest liczbą',
	'msfunc_inf' => 'Wartość wynikowa to nieskończoność',
	'msfunc_div_zero' => 'Dzielenie przez zero',
);
$wgMathStatFunctionsMessages['pms'] = array(
	'msfunc_nan' => 'L\'arzultà a l\'é nen un nùmer',
	'msfunc_inf' => 'Arzultà anfinì',
	'msfunc_div_zero' => 'Division për zero',
);

/** Russian (Русский)
 * @author VasilievVV
 * @author .:Ajvol:.
 */
$wgMathStatFunctionsMessages['ru'] = array(
	'msfunc_nan'      => 'Результат не является числом',
	'msfunc_inf'      => 'Результат является бесконечностью',
	'msfunc_div_zero' => 'Деление на ноль',
);

$wgMathStatFunctionsMessages['sr-ec'] = array(
        'msfunc_nan' => "Резултат није број" ,
        'msfunc_inf' => "Резултат је бесконачан" ,
        'msfunc_div_zero' => "Дељиво са нулом",
);

$wgMathStatFunctionsMessages['sr-el'] = array(
        'msfunc_nan' => "Rezultat nije broj" ,
        'msfunc_inf' => "Rezultat je beskonačan" ,
        'msfunc_div_zero' => "Deljivo sa nulom",
);

$wgMathStatFunctionsMessages['sr'] = $wgMathStatFunctionsMessages['sr-ec'];

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$wgMathStatFunctionsMessages['stq'] = array(
	'msfunc_nan'      => 'Resultoat is neen Taal',
	'msfunc_inf'      => 'Resultoatwäid is uuneendelk',
	'msfunc_div_zero' => 'Division truch Nul',
);

$wgMathStatFunctionsMessages['yue'] = array(
        'msfunc_nan' => "結果唔係個數" ,
        'msfunc_inf' => "結果數值係無限" ,
        'msfunc_div_zero' => "除以零",
);

$wgMathStatFunctionsMessages['zh-hans'] = array(
        'msfunc_nan' => "结果数值不是一个数字" ,
        'msfunc_inf' => "结果数值是无限" ,
        'msfunc_div_zero' => "除以零",
);

$wgMathStatFunctionsMessages['zh-hant'] = array(
        'msfunc_nan' => "結果數值不是一個數字" ,
        'msfunc_inf' => "結果數值是無限" ,
        'msfunc_div_zero' => "除以零",
);

$wgMathStatFunctionsMessages['zh'] = $wgMathStatFunctionsMessages['zh-hans'];
$wgMathStatFunctionsMessages['zh-cn'] = $wgMathStatFunctionsMessages['zh-hans'];
$wgMathStatFunctionsMessages['zh-hk'] = $wgMathStatFunctionsMessages['zh-hant'];
$wgMathStatFunctionsMessages['zh-sg'] = $wgMathStatFunctionsMessages['zh-hans'];
$wgMathStatFunctionsMessages['zh-tw'] = $wgMathStatFunctionsMessages['zh-hant'];
$wgMathStatFunctionsMessages['zh-yue'] = $wgMathStatFunctionsMessages['yue'];

$wgMathStatFunctionsMagic['en'] = array(
        'const'         => array( 0, 'const' ),
        'median'        => array( 0, 'median' ),
        'mean'          => array( 0, 'mean' ),
        'exp'           => array( 0, 'exp' ),
        'log'           => array( 0, 'log' ),
        'ln'            => array( 0, 'ln' ),
        'tan'           => array( 0, 'tan' ),
        'atan'          => array( 0, 'atan', 'arctan' ),
        'tanh'          => array( 0, 'tanh' ),
        'atanh'         => array( 0, 'atanh', 'arctanh' ),
        'cot'           => array( 0, 'cot' ),
        'acot'          => array( 0, 'acot', 'arccot' ),
        'cos'           => array( 0, 'cos', ),
        'acos'          => array( 0, 'acos', 'arccos' ),
        'cosh'          => array( 0, 'cosh', ),
        'acosh'         => array( 0, 'acosh', 'arccosh' ),
        'sec'           => array( 0, 'sec' ),
        'asec'          => array( 0, 'asec', 'arcsec' ),
        'sin'           => array( 0, 'sin' ),
        'asin'          => array( 0, 'asin', 'arcsin' ),
        'sinh'          => array( 0, 'sinh' ),
        'asinh'         => array( 0, 'asinh', 'arcsinh' ),
        'csc'           => array( 0, 'csc' ),
        'acsc'          => array( 0, 'acsc', 'arccsc' ),
);



