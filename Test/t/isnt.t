#!/usr/bin/env php
<?php

require 'Test.php';

plan( 2 );

isnt( 1, 2 );
isnt( 2 + 2, 5, '2 and 2 does not make 5' );

?>
