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
    
	// Hack for parser to avoid barfing from no $wgTitle
	$wgTitle = Title::newFromText( wfMsg( 'mainpage' ) );
	
	foreach( $wgNoticeProjects as $project ) {
		foreach( array_keys( Language::getLanguageNames() ) as $lang ) {
			echo "$project/$lang\n";
			
			$builder = new SpecialNoticeText();
			$js = $builder->getJsOutput( $wgNoticeProject, $wgNoticeLang );
			
			$outputDir = "$wgNoticeStaticDirectory/$project/$lang";
			if( wfMkDirParents( $outputDir ) ) {
				file_put_contents( "$outputDir/notice.js", $js );
			} else {
				echo "FAILED to create $outputDir!\n";
			}
		}
	}
} 
