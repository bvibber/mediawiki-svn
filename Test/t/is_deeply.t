#!/usr/bin/env php
<?php

require 'Test.php';

plan( 3 );

is_deeply("foo", "foo", "string (simple)");

$range = range(1, 4);
is_deeply($range, array(1, 2, 3, 4), "is_deeply (simple array)");

$data = array(
			  'a' => 'b',
			  'c' => 'd',
			  'n' => range( 1, 6 ),
			  );

is_deeply( $data, $data, "is_deeply (more complex)");

?>
