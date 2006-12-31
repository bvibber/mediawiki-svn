#!/usr/bin/env php
<?php

require 'Test.php';

plan( 11 );

#
# Built-in comparison operators, see http://www.php.net/manual/en/language.operators.comparison.php
#

cmp_ok(5, '==', 5, '== in cmp_ok()');
cmp_ok("5", '===', "5", '=== on str/str in cmp_ok()');
cmp_ok(4, '!=', 5, '!= in cmp_ok()');
cmp_ok(4, '<>', 5, '<> in cmp_ok()');
cmp_ok((string)5, '!==', 5, '!== on int/st in cmp_ok()');
cmp_ok(4, '<', 5, '< in cmp_ok()');
cmp_ok(5, '>', 4, '> in cmp_ok()');
cmp_ok(5, '<=', 5, '<= in cmp_ok()');
cmp_ok(5, '>=', 5, '>= in cmp_ok()');

#
# Comparison functions
#

function cooler( $a, $b ) {
	return $a == 'ice' and $b == 'lava';
}

cmp_ok( 'ice', 'cooler', 'lava', 'Ice is colder than lava says a comparison function');

cmp_ok(
	'lava',
	create_function(
		'$a, $b',
		'return $a == "lava" and $b == "ice";'
	),
	'ice',
	'Lava is hotter than Ice says an anonymous comparison function'
);

?>