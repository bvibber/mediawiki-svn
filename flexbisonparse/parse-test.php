<?php

dl("php_wikiparse.so");

$result = wikiparse_do_parse (implode ("\n", file ('test.txt')));
print $result;

?>
