<?php error_reporting(E_ALL);

require_once dirname( dirname( dirname( __FILE__ ) ) ) . "/maintenance/commandLine.inc";

if ( ! $wgFundraiserProtalDirectory ) {
	echo "\$wgFundraiserProtalDirectory isn't set -- we're not configured to build static templates.";
}

if ( isset( $options['help'] ) ) {
	echo "Rebuild all static button templates\n";
	echo "Usage:\n";
	echo "  php extensions/FundraiserPortal/rebuildButtons [-o|Output to disk]\n";
} else {
	echo "Rebuilding templates ...\n";	
  
	$builder = new DonateButton();
	$js = $builder->getJsOutput();

	if ( isset( $options['o'] ) ) {
		$outputDir = "$wgNoticeCentralDirectory";
		if ( wfMkDirParents( $outputDir ) ) {
			$outputFile = "$outputDir/fundraiserportal.js";
			$ok = file_put_contents( $outputFile, $js );
			if ( !$ok ) {
                        	echo "FAILED to write $outputFile!\n";
                        }
		} else {
			echo "FAILED to create $outputDir!\n";
		}
	} else {
		echo $js;
	}
}
