<?php
/*
 * MV_EditStreamPage.php Created on Nov 28, 2007
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 */
 
 //edit stream adds some additional form items (if admin) 
 //enables inline editing of media files
 //enables 
 
 //enables "protected" metadata ie strips all 
 //occurrences of semantic property from page (such as stream duration)
 class MV_EditStreamPage extends EditPage{
 	function edit(){
 		global $wgOut, $wgUser;
 		//@@todo more direct permisions (ie is allowed to edit stream files)
 		if( $wgUser->isAllowed('mv_edit_stream') ){
 			//add edit hook: 
 			$this->displayEditStreamFiles(); 		
 		}
 		//check permisions if admin show 'edit files'
 		parent::edit();
 	} 	
 	function displayEditStreamFiles(){
 		global $wgOut, $wgTitle;
 		$html='';
 		$mvTitle =& $this->mArticle->mvTitle;
 		//list add new file:
 		//$wgOut->addHTML("<b>".wfMsg('mv_edit_stream_files')."</b><br>");
 		//list existing files		
 		//grab all know files: 
		$streamFiles = $mvTitle->mvStream->getFileList(); 		
		if(count($streamFiles)==0){
			$html.='<b>'.wfMsg('mv_no_stream_files').'</b>';
		}else{
			$html.= '<fieldset><legend>'.wfMsg('mv_file_list').'</legend>' . "\n";	
			$html.= '<table width="600" border="0">';	
			foreach($streamFiles as $sf){
				$html.='<tr>';
					$html.='<td width="150">'.$sf->get_link().'</td>';
					$html.='<td>'.wfMsg( $sf->get_desc() ).'</td>';											
				$html.='</tr>';
			}
			$html .='</table></fieldset>';
		}
		//add new stream: 
		$html.= '<fieldset><legend>'.wfMsg('mv_add_stream_file').'</legend>' . "\n";	
		$html.= '<table width="600" border="0">';	
			$html.='<tr>';
					//titles:
					$html.='<td>'.wfMsg('mv_file_desc_label').'</td>';
					$html.='<td>'.wfMsg('mv_base_offset_label').'</td>';
					$html.='<td>'.wfMsg('mv_path_type_label').'</td>';
					$html.='<td>'.wfMsg('mv_media_path').'</td>';
					$html.='</tr><tr>';
					$html.='<td><input type="text" id="streamFile_file_desc_msg" value="mv_ogg_low_quality" maxlength="60" size="20" /></td>';					
					$html.='<td><input type="text" id="streamFile_base_offset" value="0" maxlength="11" size="7" /></td>';
					$html.='<td><select id="streamFile_path_type">' .
							'<option value="url_anx" selected>'.wfMsg('mv_path_type_url_anx').'</option>' . 
							'<option value="wiki_title">'.wfMsg('mv_path_type_wiki_title').'</option>' . 							
							'</select></td>';																		
					$html.='<td><input type="text" id="streamFile_file_path" value="'.
						wfMsg('mv_media_path').'" maxlength="250" size="50" /></td>';
			$html.='</tr>';
		$html .='</table></fieldset>';		
		$wgOut->addHTML($html);
 	}
 }
?>
