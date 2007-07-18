<?php
static $bla="hello";

function a($foo) {
	$bla=$foo;
	echo $bla;
}

a("hello");
a("bye");


?>
