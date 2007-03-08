<?php


/*
 * Created on Sep 7, 2006
 *
 * API for MediaWiki 1.8+
 *
 * Copyright (C) 2006 Yuri Astrakhan <FirstnameLastname@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

if (!defined('MEDIAWIKI')) {
	// Eclipse helper - will be ignored in production
	require_once ('ApiBase.php');
}

class ApiInstantCommons extends ApiBase {
	var $arrOutput = array();
   	var $resParser;
   	var $strXmlData;
	
	public function __construct($main, $action) {
		parent :: __construct($main, $action);
	}	

	/**
	 * InstantCommons execution happens in the following steps:
	 */
	public function execute() {
		$media = $maint = $meta = null;
		extract($this->extractRequestParams());
		$data = array();
		$data = $this->fetchImage($media);		
		if($data!=NULL){
			$this->getResult()->addValue('instantcommons', 'image', $data);
		}
	}


	/**
	 * Override the parent to generate help messages for all available query modules.
	 */
	public function makeHelpMsg() {

		// Use parent to make default message for the query module
		$msg = parent :: makeHelpMsg();

		// Make sure the internal object is empty
		// (just in case a sub-module decides to optimize during instantiation)
		$this->mPageSet = null;

		$astriks = str_repeat('--- ', 8);
		$msg .= "\n$astriks InstantCommons: Prop  $astriks\n\n";
		$msg .= "\n See http://meta.wikimedia.org/wiki/InstantCommons\n\n";
		return $msg;
	}

	private function makeHelpMsgHelper($moduleList, $paramName) {

		$moduleDscriptions = array ();

		foreach ($moduleList as $moduleName => $moduleClass) {
			$msg = "* $paramName=$moduleName *";
			$module = new $moduleClass ($this, $moduleName, null);
			$msg2 = $module->makeHelpMsg();
			if ($msg2 !== false)
				$msg .= $msg2;
			if ($module instanceof ApiInstantCommonsGeneratorBase)
				$msg .= "Generator:\n  This module may be used as a generator\n";
			$moduleDscriptions[] = $msg;
		}

		return implode("\n", $moduleDscriptions);
	}

	protected function getAllowedParams() {
		return array (
			'media' => null,
			'maint' => null,
			'meta' => null,
		);
	}
	protected function getParamDescription() {
		return array (
			'media' => 'Get properties for the media',
			'maint' => 'Which maintenance actions to perform',
			'meta' => 'Which meta data to get about this site',			
		);
	}
	
	protected function getDescription() {
		return array (
			'InstantCommons API InstantCommons is an API feature of MediaWiki to ' .
			'allow the usage of any uploaded media file from the Wikimedia Commons ' .
			'in any MediaWiki installation world-wide. InstantCommons-enabled wikis ' .
			'cache Commons content so that it is only downloaded once, and subsequent ' .
			'pageviews load the locally existing copy.'
		);
	}

	protected function getExamples() {
		return array (
			'api.php?action=instantcommons&media=Image:MusekeBannerl.jpg',
			'api.php?action=instantcommons&media=Image:MusekeBannerl.jpg&maint=update', //performs update on this media
			'api.php?action=instantcommons&media=Image:MusekeBannerl.jpg&maint=delete', //performs delete on this media
			'api.php?action=instantcommons&maint=update', //performs update on all commons media
			'api.php?action=instantcommons&maint=delete', //performs delete on all commons imedia
			'api.php?action=instantcommons&maint=both', //performs update/delete on all commons media
			
		);
	}

	public function getVersion() {
		$psModule = new ApiPageSet($this);
		$vers = array ();
		$vers[] = __CLASS__ . ': $Id: ApiInstantCommons.php 17074 2006-10-27 05:27:43Z paa.kwesi $';
		$vers[] = $psModule->getVersion();
		return $vers;
	}
	
	/**
	 * Fetch the media from the commons server in the background.
	 * Save it as a local media (but noting its source in the appropriate media table)
	 * @fileName is a fully qualified mediawiki object name (e.g. Image:sing.png)
	 * @return an object identical to the that returned by
	 * Database::selectRow() in includes/Database.php
	 */
	public function fetchImage($fileName){		
		global $wgScriptPath;
		
		$nt = Title::newFromText( $fileName );
		
		if(is_object($nt))
		{		
			$image = new Image ($nt);			
			if($image->exists())
			{		
			        unset($image->title);
				//return the part after $wgScriptPath
				$image->url = substr(strstr($image->imagePath, $wgScriptPath), strlen($wgScriptPath));
				unset($image->imagePath);//do not reveal absolute file structure
				$image->metadata = addslashes($image->metadata); 
				$image->metadata='';
				$ari=(array)$image;
				return $ari;
				#return (array)$image;
				//return array('url'=>$image->url,
				//			 'metadata'=>$image->metadata); 
			}
			else
			{
				return array('error'=>1, 'description'=>'File not found'); //file not found			
			}
		}
		else
		{
			return array('error'=>2, 'description'=>'Not a valid title'); //not a valid title			
		}			
	}
	

  
   function parse($strInputXML) {
  
           $this->resParser = xml_parser_create ();
           xml_set_object($this->resParser,$this);
           xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
          
           xml_set_character_data_handler($this->resParser, "tagData");
      
           $this->strXmlData = xml_parse($this->resParser,$strInputXML );
           if(!$this->strXmlData) {
               die(sprintf("XML error: %s at line %d",
           xml_error_string(xml_get_error_code($this->resParser)),
           xml_get_current_line_number($this->resParser)));
           }
                          
           xml_parser_free($this->resParser);
          
           return $this->arrOutput;
   }
   function tagOpen($parser, $name, $attrs) {
       $tag=array("name"=>$name,"attrs"=>$attrs);
       array_push($this->arrOutput,$tag);
   }
  
   function tagData($parser, $tagData) {
       if(trim($tagData)) {
           if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
           }
           else {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
           }
       }
   }
  
   function tagClosed($parser, $name) {
       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
       array_pop($this->arrOutput);
   }
	
}
?>
