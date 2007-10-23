<?
$wgShowSqlErrors=true;
$GLOBALS['wgShowSqlErrors']=true;

function importLanguageTags($keepTables=false) {
### Language codes per popular standards
global $wgDBtype,$wgDBprefix,$wgDBTableOptions;
$wgDBTableOptions='';

$db = &$GLOBALS['wgDatabase'];
$here=dirname( __FILE__ ).'/tags/';

function doQueries(&$db,$sql) {
	global $wgDBprefix,$wgDBTableOptions;
	$sql=str_replace(
		array("{$wgDBprefix}","/*$wgDBTableOptions*/"),
		array($wgDBprefix,$wgDBTableOptions),$sql);
	$sql=explode(';',$sql);

	$db->query("begin");
	foreach($sql AS $v) if(strlen($v=trim($v))) $db->query($v);
	return $db->query("commit");
}

$temporary = $keepTables ? '' : 'TEMPORARY';
$temporary = '';

$sql=<<<E
CREATE {$temporary} TABLE {$wgDBprefix}langtags_iso639 (
  tag varchar(42),
  iso639 char(3),
  iso639_m varchar(3),
  iso639_2b varchar(3),
  iso639_2t varchar(3),
  iso639_3 char(3), 
  lang_scope char(1),
  lang_type char(1),
  english_name varchar(255),
  iso639_3_revision date
) /*$wgDBTableOptions*/;

CREATE INDEX tag_idx ON {$wgDBprefix}langtags_iso639(tag);
CREATE INDEX iso639_3_idx ON {$wgDBprefix}langtags_iso639(iso639_3);

CREATE {$temporary} TABLE {$wgDBprefix}langtags_rfc4646 (
  tag varchar(42),
  prefix varchar(42),
  preferred_value varchar(42),
  suppress_script varchar(4),
  added date, deprecated date, 
  description varchar(255)
) /*$wgDBTableOptions*/;

CREATE {$temporary} TABLE {$wgDBprefix}langtags_wikimedia (
  wikimedia_key varchar(15),
  native_name varchar(255)
) /*$wgDBTableOptions*/;
E;

if($wgDBtype=='mysql') $sql.=<<<E
ALTER TABLE {$wgDBprefix}langtags
  CHANGE language_id language_id int NOT NULL AUTO_INCREMENT;
E;

else if($wgDBtype=='postgres') $sql.=<<<E
CREATE SEQUENCE {$wgDBprefix}langtags_language_id_seq;
ALTER TABLE {$wgDBprefix}langtags
  ALTER language_id SET DEFAULT nextval('{$wgDBprefix}langtags_language_id_seq');
E;

doQueries($db,$sql);

### Wikimedia Language Codes
global $wgLanguageNames;
if(!$wgLanguageNames) include(dirname(__FILE__)."/../../languages/Names.php");
foreach($wgLanguageNames AS $k=>$v) {
	$v=array('wikimedia_key'=>$k,'native_name'=>$v);
	$db->insert('langtags_wikimedia',$v);
}

### ISO 639-3 Language Codes
## Source: http://www.sil.org/iso639-3/iso-639-3_20070516.tab
function parseIso639($a) {
	$a=explode("\t",rtrim($a));
	$b=array('iso639_3','iso639_2b','iso639_2t','iso639','lang_scope','lang_type','english_name');
	if(count($a) != count($b)) return false;
	$c=array(); foreach($b AS $k=>$v) if(strlen($a[$k])) $c[$v]=$a[$k];
	return $c;
}

$iso639_revision='2007-05-16'; $file=$here.'iso-639-3_20070516.tab';
if(!$a=@fopen($file,'r')) {
	print "Language standards file missing: $file\n";
	return false;
}

fgets($a,1024);
while(!feof($a)) {
	$b=parseIso639(fgets($a,1024));
	if($b) $db->insert('langtags_iso639',$b);
}
$db->update('langtags_iso639',array('iso639_3_revision'=>$iso639_revision),array('iso639_3_revision is null'));

### ISO 639-3 Macrolanguage Mappings
## Source: http://www.sil.org/iso639-3/iso-639-3-macrolanguages_20070323.tab
$iso639_m_revision='2007-03-23'; $file=$here.'iso-639-3-macrolanguages_20070323.tab';
if(!$a=@fopen($file,'r')) {
	print "Language standards file missing: $file\n";
	return false;
}

fgets($a,1024);
while(!feof($a)) {
	$b=fgets($a,1024);
	if(false===strpos($b,"\t")) continue;
	$b=explode("\t",trim($b));
	$db->update('langtags_iso639',array('iso639_m'=>$b[0]),array('iso639_3'=>$b[1]));

}

### Apply RFC4646 Tagging
## Note: Does not use language subtag registry
if($wgDBtype=='mysql') $sql=<<<E
UPDATE {$wgDBprefix}langtags_iso639 SET
  iso639 = coalesce(iso639,iso639_3),
  tag = coalesce(iso639,iso639_3);

UPDATE {$wgDBprefix}langtags_iso639 AS langtags_iso639, {$wgDBprefix}langtags_iso639 AS a SET
  langtags_iso639.tag = concat(COALESCE(a.tag,langtags_iso639.iso639_m), '-', langtags_iso639.tag)
  WHERE langtags_iso639.iso639_m is not null
  AND langtags_iso639.iso639_m = a.iso639_3;
E;

else if($wgDBtype=='postgres') $sql=<<<E
UPDATE {$wgDBprefix}langtags_iso639 SET
  iso639 = coalesce(iso639,iso639_3),
  tag = coalesce(iso639,iso639_3);

UPDATE {$wgDBprefix}langtags_iso639 SET
  tag = coalesce( (SELECT tag FROM {$wgDBprefix}langtags_iso639 a WHERE a.iso639_3 = langtags_iso639.iso639_m), iso639_m) || '-' || tag 
  WHERE iso639_m is not null;
E;

doQueries($db,$sql);


### IANA Language Subtag Registry
## Source: http://www.iana.org/assignments/language-subtag-registry
function parseRfc4646($a) {
	$fields = array('type','tag','subtag','added','deprecated','preferred_value','suppress_script','description','prefix');
	$a=explode("\n",$a);
	$b=array();
	$prefix=array();
	foreach($a AS $v) {
		if(false===strpos($v,':')) continue;
		list($k,$v)=explode(': ',rtrim($v),2);
		if($k=='Prefix') {
			$prefix[]=$v;
			continue;
		}
		$k=str_replace('-','_',strtolower($k));
		if(!in_array($k,$fields)) continue;
		if(!array_key_exists($k,$b)) {
			$b[$k]=$v;
		}
	}
	if(!array_key_exists('type',$b)) return false;
	if($b['type'] == 'grandfathered') { $b['type']='language'; $b['subtag']=$b['tag']; unset($b['tag']); }
	if($b['type'] == 'variant') $b['type']='language';
	if($b['type'] != 'language') return false;
	$b['tag']=$b['subtag'];
	unset($b['subtag']);
	unset($b['type']); 
	return array($prefix,$b);
}

$file=$here.'language-subtag-registry'; $rfc4646_filedate='2007-05-04';
if(!$a=@fopen($file,'r')) {
	print "Language standards file missing: $file\n";
	return false;
}

$rfc4646_filedate=substr(fgets($a,1024),11,10);
fgets($a,1024); $b='';
while(!feof($a)) {
	$line=fgets($a,1024);
	if($line=="%%\n") {
		$insert=parseRfc4646($b); $ccc=$b; $b='';
		if($insert===false) continue;
		$prefixes = array_shift($insert);
		$insert = array_pop($insert);
		if(!count($prefixes)) {
			$db->insert('langtags_rfc4646',$insert);
		}
		$tag=$insert['tag'];
		foreach($prefixes AS $prefix) {
			$insert['prefix']=$prefix;
			$insert['tag']=$insert['prefix'].'-'.$tag;
		 	$db->insert('langtags_rfc4646',$insert);
		}
	} else $b.=$line;
}

dbsource( dirname(__FILE__).'/importLanguageTags.sql', $db );

$db->update('langtags',array('tag_touched'=>$db->timestamp()),array('tag_touched'=>$wgDBtype=='mysql'?'':null));

$sql='';

if(!$keepTables) $sql=<<<E
DROP TABLE {$wgDBprefix}langtags_iso639;
DROP TABLE {$wgDBprefix}langtags_rfc4646;
DROP TABLE {$wgDBprefix}langtags_wikimedia;
E;

if($wgDBtype=='mysql') $sql.=<<<E
ALTER TABLE {$wgDBprefix}langtags MODIFY language_id INT;
ALTER TABLE {$wgDBprefix}langtags DROP PRIMARY KEY;
ALTER TABLE {$wgDBprefix}langtags CHANGE language_id language_id int;
CREATE UNIQUE INDEX language_id ON {$wgDBprefix}langtags(language_id);
E;

### Undefined language tag (main namespace) 
$sql.=<<<E
UPDATE {$wgDBprefix}langtags SET language_id = NULL
  WHERE iso639 = 'und';
E;

doQueries($db,$sql);

return true;
}

?>
