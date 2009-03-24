<?php
require( './commandLine.inc' );

// Do each user sequentially, since accounts can't be deleted
$i = 1;

$u = User::newFromId( $i );

while( $u->loadFromDatabase() ) {
	$u->saveSettings();
	
	++$i;
	$u = User::newFromId( $i );
}
