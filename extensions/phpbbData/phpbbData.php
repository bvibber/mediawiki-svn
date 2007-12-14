<?php
/** \file
* \brief Contains code for the phpbbData Extension.
*/

# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
	echo "phpbbData extension";
	exit(1);
}

$wgPhpbbDataRootPath = '/var/www/forum/';

$wgExtensionCredits['other'][] = array(
	'name'        => 'phpbbData',
	'version'     => '1.0',
	'author'      => 'Tim Laqua',
	'description' => 'Allows you to include phpBB data in wiki pages',
	'url'         => 'http://www.mediawiki.org/wiki/Extension:phpbbData',
);

$wgExtensionFunctions[] = 'efPhpbbData_Setup';
$wgHooks['LanguageGetMagic'][]       = 'efPhpbbData_LanguageGetMagic';

function efPhpbbData_Setup() {
        global $wgParser, $wgMessageCache;
	
		#Add Messages
		require( dirname( __FILE__ ) . '/phpbbData.i18n.php' );
		foreach( $messages as $key => $value ) {
			  $wgMessageCache->addMessages( $messages[$key], $key );
		}
		
        # Set a function hook associating the "example" magic word with our function
        $wgParser->setFunctionHook( 'phpbb', 'efPhpbbData_Render' );
		
		return true;
}

function efPhpbbData_LanguageGetMagic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is case sensitive, in this case it is not case sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['phpbb'] = array( 0, 'phpbb' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}

function efPhpbbData_Render( &$parser, $action = 'announcements', $name = '', 
	$template = "* '''{topic_time}:''' {topic_title}\n") {
	$dateFields = array('topic_time','topic_last_post_time');
	
	$parser->disableCache();
	
	switch ($action) {
		case 'announcements':
			global $wgPhpbbDataRootPath, $wgPhpbbData;
			
			if (!isset($wgPhpbbData))
				$wgPhpbbData = new phpbbDataProvider($wgPhpbbDataRootPath);
			
			if ($announcements = $wgPhpbbData->getAnnouncements($name)) {
				foreach ($announcements as $announcement) {
					$rowString = $template;
					foreach($announcement as $key => $value) {
						if (in_array($key,$dateFields)) {
							$rowString = str_ireplace('{'.$key.'}',date("m/d/Y",$value),$rowString);
						} else {
							$rowString = str_ireplace('{'.$key.'}',$value,$rowString);
						}
					}
					$returnString .= $rowString;
				}
				return $returnString;
			} else {
				return 'No Announcements';
			}
			
			break;
		default:
			break;
	}
}

class phpbbDataProvider {
	var $mDB = '';
	var $mRootPath = '';
	var $mPhpEx = '';
	var $mTablePrefix = '';
	
	function __construct($phpbb_root_path) {
		define('IN_PHPBB', true);
		$this->mRootPath = $phpbb_root_path;
		$this->mPhpEx = substr(strrchr(__FILE__, '.'), 1);
		
		$this->connect();
	}
	
	private function connect() {
		$phpEx = $this->mPhpEx;
		$phpbb_root_path = $this->mRootPath;
		
		include($phpbb_root_path . 'config.' . $phpEx);
		include($phpbb_root_path . 'includes/db/' . $dbms . '.' . $phpEx);

		$this->mTablePrefix = $table_prefix;
		
		$this->mDB = new $sql_db();
		$this->mDB->sql_connect($dbhost, $dbuser, $dbpasswd, $dbname, $dbport, false, false);

		// We do not need this any longer, unset for safety purposes
		unset($dbpasswd);
		
		return true;
	}
	
	public function getAnnouncements($name) {
		$phpEx = $this->mPhpEx;
		$phpbb_root_path = $this->mRootPath;
		
		$topicstable = $this->tableName('topics');
		$forumstable = $this->tableName('forums');
		$iconstable = $this->tableName('icons');
		$poststable = $this->tableName('posts');
		
		if ($name != '') {
			//sanitize input
			$forumclause = "$forumstable.forum_name = '" . 
				$this->mDB->sql_escape($name) . "'";
		} else {
			$forumclause = "$topicstable.forum_id=0";
		}
		
		$sql = 
			"SELECT DISTINCT topic_time, topic_title, topic_first_poster_name, topic_replies, topic_last_post_time " .
			"FROM $topicstable LEFT JOIN $forumstable USING (forum_id) " .
			"WHERE $forumclause " .
			"AND topic_type IN (2,3)";
			
		$result = $this->mDB->sql_query( $sql );
		if ($result) {
			while ($row = $this->mDB->sql_fetchrow($result)) {
				$rowArray[] = $row;
			}
			
			return $rowArray;
		} else {
			return false;
		}
	}
	
	public function tableName($table) {
		return $this->mTablePrefix . $table;
	}
}
