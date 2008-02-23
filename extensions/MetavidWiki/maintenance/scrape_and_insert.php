t		<?php
/*
 * scrape_and_insert.php Created on Oct 1, 2007
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 */ 

 $cur_path = $IP = dirname( __FILE__ );
 //include commandLine.inc from the mediaWiki maintenance dir: 
require_once( '../../../maintenance/commandLine.inc' );
require_once('maintenance_util.inc.php');
require_once('scrape_and_insert.inc.php');

 if ( count( $args ) == 0 || isset( $options['help'] ) ) {
 	print <<<EOT
 	
Scrapes External WebSites and updates relavent local semantic content.
 
Usage php scrape_and_insert.php insert_type [site] [options]
site:
	'cspan_chronicle' will take all it can from  http://www.c-spanarchives.org/
options: 		
	'-s --stream_name steam_name|all' the strean name or keyword "all" to proc all streams
	'--limit X' to only process X number of streams (when stream_name set to all)
	'--offset Y' to start on Y of streams (when stream_name set to all)

EOT;
exit();
}
/*
 * procc the request
 */ 
 function proc_args(){
 	global $args; 	
	switch($args[0]){
		case 'cspan_chronicle':
			$MV_CspanScraper = new MV_CspanScraper();
			$MV_CspanScraper->procArguments();
			$MV_CspanScraper->doScrapeInsert();
		break;
	}
 }
//do procc args (now that classes are defined)
 proc_args();
?>
