<?php
static $bla="hello";

function a($foo) {
	final $bla=$foo;
	echo $bla;
}

a("hello");
a("bye");


?>
