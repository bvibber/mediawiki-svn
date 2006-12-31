#!/usr/bin/env php
<?php

require 'Test.php';

plan( 2 );

unlike( "HelloWorld", '/\s/', 'like()' );
unlike( "HelloWorld", '~\s~', 'like() with unusual delimiters' );

?>
