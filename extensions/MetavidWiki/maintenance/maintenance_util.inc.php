<?php
/*
 * metavid2mvWiki.inc.php Created on Jan 19, 2008
 *
 * All Metavid Wiki code is Released under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 *
 *
 * Maintenance Utility Functions: 
 */
 //returns true if person found in category person: 
 $mv_valid_people_cache = array();
function mv_is_valid_person($person_key){
	global $mv_valid_people_cache;
	if(isset($mv_valid_people_cache[$person_key])){
		return $mv_valid_people_cache[$person_key];
	}
	$dbr = wfGetDB(DB_SLAVE);
	$result = $dbr->select( 'categorylinks', 'cl_sortkey', 
			array('cl_to'=>'Person', 
			'cl_sortkey'=>str_replace('_',' ',$person_key)),
			__METHOD__,
			array('LIMIT'=>'1'));
	if($dbr->numRows($result)!= 0){
		$mv_valid_people_cache[$person_key]=true;
	}else{
		$mv_valid_people_cache[$person_key]=false;
	}	
	return $mv_valid_people_cache[$person_key];
}
?>
