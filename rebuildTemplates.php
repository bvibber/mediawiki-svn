<?php error_reporting(E_ALL); 

require_once dirname( dirname( dirname( __FILE__ ) ) ) . "/maintenance/commandLine.inc";

if( !class_exists( 'CentralNotice' ) ) {
    require dirname( __FILE__ ) . '/SpecialCentralNotice.php';
}

if( isset( $options['help'] ) ) {
    echo "Rebuilds templates for all notices in DB.\n";
    echo "Usage:\n";
    echo "  php extensions/CentralNotice/rebuildTemplates\n";
} else {
    echo "Rebuilding templates ...\n";
    $notice = efSelectNotice( 'cn_notices' );
    $templates = array();
    $templates = CentralNotice::getTemplatesForNotice( $notice );
    
    $script_header = '<style type="text/css">';
	$script_footer = '</style>';
	
	$css_filename = 'tomas.css';
	$css = fopen( $css_filename, 'r' );
	$css_body = fread ($css, filesize( $css_filename) );
	fclose ( $css );
	
	foreach( $templates as $template => $weight) {
		$fh = fopen("js/centralnotice-$template-en.js", "w");
		fwrite( $fh, $script_header );
		fwrite( $fh, $css_body );
		fwrite( $fh, $script_footer );
		fwrite( $fh, wfMsg( "Template:$template" ) );
		fwrite( $fh, wfMsg( "Template:$template/en" ) );
		fclose ( $fh );
	}
} 
