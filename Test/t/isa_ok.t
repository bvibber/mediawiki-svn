#!/usr/bin/env php
<?php

require 'Test.php';

plan( 1 );

class SomeClass {
	function __construct() { }
}

$obj = new SomeClass;

isa_ok( $obj, 'SomeClass' );

?>
