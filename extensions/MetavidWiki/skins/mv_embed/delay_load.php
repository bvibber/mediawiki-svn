<?php
// die('delay load is dissabled by default, should only be used for testing');

sleep( 1 );
if ( isset( $_SERVER['PATH_INFO'] ) ) {
	$file_path = dirname( __FILE__ ) . str_replace( 'delay_load.php', '', $_SERVER['PATH_INFO'] );
	$ext = substr( $file_path, - 4 );
	switch( $ext ) {
		case '.css':
			header( 'Content-type: text/css' );
		break;
		case '.js':
			header( 'Content-type:text/javascript' );
		break;
	}
	if ( is_file( $file_path ) ) {
		// use 'include' to execute php (avoid sending out text of php files) 
		@include( $file_path );
	}
}
?>