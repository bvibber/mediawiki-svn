#!/usr/bin/env php
<?php

require 'Test.php';

plan( 1 );

set_include_path('t');

require_ok( 'Dummy.php', 'Including a dummy file' );

?>
