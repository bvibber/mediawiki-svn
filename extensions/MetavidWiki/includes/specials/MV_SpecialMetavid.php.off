<?php
/**
 * @author Michael Dale
 *
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 * 
 * MV SpecialMetavid provides a front end interface to streams and their metadata.  
 */

if (!defined('MEDIAWIKI')) die();

global $IP;
require_once( "$IP/includes/SpecialPage.php" );

function doSpecialMetavid() {
	MV_SpecialMetavid::execute();
}

SpecialPage::addPage( new SpecialPage('Metavid','',true,'doSpecialMetavid',false) );


class MV_SpecialMetavid {

	function execute() {
		global $wgRequest, $wgOut, $wgUser;
                
                #$this->setHeaders();

                # Get request data from, e.g.
                $param = $wgRequest->getValues();
        		
                # process request title:              
                MV_SpecialMetavid::do_page($param['title']);                              

                # Output menu items 
				# @@todo link with language file                
                #$wgOut->addHTML( 'list streams ' . $out );
	}
	
	//@@todo each should have their own special ... no?
	//pulls up the requested metavid page:
	function do_page($page_name){
		$page_name = str_replace('Special:', '', $page_name);
		switch($page_name){
				case 'Metavid':
				case 'Metavid/':
					MV_SpecialMetavid::do_base_interface();
				break;
				case 'Metavid/list_streams':
					MV_SpecialMetavid::list_streams();
				break;
				case 'Metavid/new_stream':
										
				break;
			
		}
	}
	function do_base_interface(){
		global $mvgIP;
		require_once($mvgIP.'/includes/MV_MetavidInterface/MV_MetavidInterface.php');
		$MV_MetavidInterface = new MV_MetavidInterface('special');
		//render the interface to the screen: 	
		$MV_MetavidInterface->render_full();
	}	
	function list_streams(){
		global $wgOut, $wgUser;
		$dbr =& wfGetDB( DB_SLAVE );		
	}
	
}

?>
