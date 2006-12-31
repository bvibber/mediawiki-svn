#!/usr/bin/env php
<?php

require 'Test.php';

plan( 2 );

like( "Hello World", '/\s/', 'like()' );
like( "Hello World", '~\s~', 'like() with unusual delimiters' );

?>
