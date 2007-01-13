<?php

if ( !defined( 'MEDIAWIKI' ) ) {
        die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgMathStatFunctionsMessages = array();
$wgMathStatFunctionsMagic = array();

$wgMathStatFunctionsMessages['en'] = array(
        'msfunc_nan' => "Resulting value is not a number" ,
        'msfunc_inf' => "Resulting value is infinity" , 
        'msfunc_div_zero' => "Division by zero",
);
$wgMathStatFunctionsMessages['fr'] = array(
        'msfunc_nan' => "Le résultat n’est pas un nombre" ,
        'msfunc_inf' => "Le résultat est l’infini" , 
        'msfunc_div_zero' => "Division par zéro",
);
$wgMathStatFunctionsMessages['ja'] = array(
        'msfunc_nan' => "返り値が数値ではありませんResulting value is not a number" ,
        'msfunc_inf' => "返り値が無限大です" , 
        'msfunc_div_zero' => "0で割り算しました",
);

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
?>
