<?php
/**
 * MV_LanguageEn.php
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 * 
 */
if ( !defined( 'MEDIAWIKI' ) )  die( 1 );

class MV_LanguageEn {

/* private */ var $mvContentMessages = array(
	'mv_missing_stream_text'=>'The stream you requested <b>$1</b> is not available.<br>
		 You may want to check the <a href="$2">Stream List</a><br>
		 Or you many want to <a href="$3">Add The Stream</a> '
);

/* private */ var $mvUserMessages = array(
	'metavid' => 'Metavid Page',
	'mv_missing_stream' => 'Missing Stream: $1',
	
	#stream/files key descriptions: 
	'mv_ogg_low_quality'=>'Web Stremable ogg theora, hosted on metavid',
	'mv_ogg_high_quality'=>'High Quality ogg theora, hosted on metavid',
	'mv_archive_org_link'=>'Links into Archive.org mpeg2 originals',
	
	'mv_error_stream_missing'=>'<span class="error">Error: There is no video stream associated with this metadata.</span><br> Please report this to the site adimistrator.<br> <i>stream metadata interface is disabled</i>',
		
	#add/edit stream text:
	'mv_stream_meta'=>'Stream Page',		
	'mv_add_stream'=>'Metavd Add Stream',
	'mv_edit_stream'=>'Metavid Edit Stream',
	'mv_add_stream_page'=>'Mv Add Stream',
	'mv_edit_strea_docu'=>'<p>Edit stream <b>admin</b> <br> for normal user view/edit see $1 page',
	'mv_add_stream_docu' => '<p>Add a new Stream with the field below.</p><p> More information is given on the <a href="$1">help page for add stream</a>.</p>',
	'mv_add_stream_submit'=>'Add stream',
	'mv_no_stream_files' =>'No Existing Stream files',
	'mv_edit_stream_files'=>'Edit stream files',
	'mv_path_type_url_anx'=>'full media url ',
	'mv_path_type_wiki_title'=>'wiki media title',
	'mv_path_type_label'=>'path type',
	'mv_base_offset_label'=>'base offset',
	'mv_duration_label'=>'duration',
	'mv_file_desc_label'=>'stream desc msg',
	'mv_delete_stream_file'=>'delete stream file reference',
	'mv_save_changes'=>'Save Changes',
	'mv_file_with_same_desc'=>'Error: stream file with same description key <i>$1</i> already present',
	'mv_updated_stream_files'=>'Updated Stream Files Record',
	'mv_removed_file_stream'=>'Removed Stream file: $1',

	'mv_add_stream_file'=>'Add Stream File',
	'mv_media_path'=>'media path',
	'mv_file_list'=>'Stream Files',
	'mv_label_stream_name'=>'Stream Name',
	'mv_label_stream_desc'=>'Stream Description',
	'add_stream_permission'=>'You lack permission to add a new stream ',
	'edit_stream_missing' => 'Missing stream name',
	'mv_stream_already_exists'=>'The stream <a href="$2">$1</a> already exists',
	'mv_summary_add_stream'=>'stream added by form',
	'mv_error_stream_insert'=>'failed to insert stream',
	'mv_redirect_and_delete_reason'=>'removed redirect page',	
	'mv_remove_reason'=>'Reason For deletion:',
	'mv_stream_delete_warrning'=>'<b>Removing this Stream will also remove $1 pieces of assocative metadata</b><br>',
	
	#stream type
	'mv_label_stream_type'=>'Stream Type',
	'mv_metavid_file'=>'Existing File on Server',
	'mv_metavid_live'=>'Set Up Live Stream',
	'mv_upload_file'=>'Upload file',
	'mv_external_file'=>'External File',
	'mv_stream_delete_warning'=>'Deleting this Stream will additionally remove $1 pages of metadata',
	
	#tools
	'mv_tool_search'=>'Search',
	'mv_tool_search_title'=>'Search within this Stream', 
	'mv_tool_navigate'=>'Navigate',
	'mv_tool_navigate_title'=>'Navigate the full stream',
	'mv_tool_export'=>'Export',
	'mv_tool_export_title'=>'Export Stream Metadata',
	'mv_tool_embed'=>'Embed',
	'mv_tool_embed_title'=>'Embed options for the current requested segment',
	'mv_tool_overlay'=>'Template Overlays',
	'mv_tool_overlay_title'=>'Template based metadata Overlays',
	'mv_results_found_for'=>'Search Results <b>$1</b> to <b>$2</b> of <b>$3</b> for:',
	
	#mvd types: 
	'ht_en'=>'Transcript',
	'ht_en_desc'=>'English transcripts. This overlay type is for text which spoken in the video stream. Links can be added, but all text should be what is spoken in the video stream.',	
	'anno_en'=>'Annotations and Categories',
	'anno_en_desc'=>'English categorizations and annotations. This overlay can be used to \"tag\"/Categorize sections of video or to add annotative information that is not spoken text',
 
	
	'mv_data_page_title'=>'$1 for $2 from $3 ',	
	'mv_time_separator'=>' to ',
	
	# Messages for  Special List stream
	'mv_list_streams' =>'Metavid List Streams',
	'mv_list_streams_page'=>'Mv List Streams',
	'mv_list_streams_docu' => 'The following streams exist:',
	'mv_list_streams_none' => 'No streams exist',
	
	#messages for metavid export feed:
	'mvvideofeed'=> 'Metavid Video Feed Export',
	'video_feed_cat'=>'Video Feed for Category:',
	'mv_cat_search_note'=>'Note: Categories only lists top level categories, for all metadata in category ranges search for $1',	
	
	# Messages for MV_DataPage
	'mv_mvd_linkback'=>'Part of stream $1 <br>Jump to Stream View: $2<br>',
	
	#messages for MVD pages
	'mvBadMVDtitle'=>'missing type, stream missing, or not valid time format',
	'mvMVDFormat' => 'MVD title should be of format: mvd:type:stream_name/start_time/end_time',
	
	#messeges for interface mvd pages:
	'mv_play'=>'Play',
	'mv_edit'=>'Edit',
	'mv_history'=>'History',
	'mv_history_title'=>'Edit and Video Alignment History',
	'mv_edit_title'=>'Edit Text',
	'mv_edit_adjust_title'=>'Edit Text and Video Alignment',
	'mv_remove'=>'remove',
	'mv_remove_title'=>'remove this meta data segment',
	'mv_adjust'=>'adjust',
	'mv_adjust_submit'=>'Save Adjustment',
	'mv_adjust_title'=>'Adjust Start and End time',
	'mv_adjust_preview'=>'Preview Adjustment',
	'mv_adjust_preview_stop'=>'Stop Preview',
	'mv_adjust_default_reason'=>'metavid interface adjust',
	'mv_adjust_old_title_missing'=>'The page you are tyring to move from ($1) does not exist',
	'mv_adjust_ok_move'=>'Success, adjusting...',
	
	'mv_start_desc'=>'Start Time',
	'mv_end_desc'=>'End Time',
	
	#search 
	'mediasearch'=>'Media Search',
	'mv_search_sel_t'=>'Select Search Type',
	'mv_run_search'=>'Run Search',
	'mv_add_filter'=>'Add Filter',
	
	'mv_search_match'=>'Search Text', 
 	'mv_search_spoken_by'=>'Spoken By',
 	'mv_search_category'=>'Category',
	'mv_search_smw_property'=>'Semantic Properties',
	'mv_search_smw_property_numeric'=>'Numeric Semantic Value',
	'mv_search_and'=>'and',
	'mv_search_or'=>'or',
	'mv_search_not'=>'not',
	'mv_search_stream_name'=>'Stream Name',
	'mv_stream_name'=>'stream name',
	
	
	'mv_match'=>'match',
	'mv_spoken_by'=>'spoken by',
	'mv_category'=>'category',
	
	
	'mv_search_no_results'=>'No media matches ',
	'mv_media_matches'=>'Media matches ',
	'mv_remove_filter'=>'remove filter',
	'mv_advaced_search'=>'Advanced Media Search',
	'mv_expand_play'=>'Expand and Play in-line',
	'mv_view_in_stream_interface'=>'View in Stream Interface',
	'mv_view_wiki_page'=>'View wiki page',
	'mv_error_mvd_not_found'=>'Error mvd not found',
	'mv_match_text'=>' ~  $1 matches ',
	
	#sequence text: 
	'mv_edit_sequence'=>'Editing Sequence:$1',
	'mv_sequence_player_title'=>'sequence player',
	
	'mv_save_sequence'=>'Save Sequence',
	'mv_sequence_page_desc'=>'Save The Current Sequence',
	'mv_sequence_add'=>'Add clips',
	'mv_sequence_add_manual'=>'Add By Name',
	'mv_sequence_add_manual_desc'=>'Add clips by Stream Name',
	'mv_sequence_add_search'=>'Add By Search',
	'mv_sequence_add_search_desc'=>'Add clips by Media Search',
	'mv_seq_add_end'=>'Add to End of Sequence',
	
	'mv_sequence_timeline'=>'Sequence Timeline:',
	'mv_edit_sequence_desc_help'=>'Sequence Description<br>',
	'mv_edithelpsequence'=>'Help:Sequence_Editing',	
	'mv_seq_summary'=>'Sequence Edit Summary',
	'mv_add_clip_by_name'=>'Add Clip By Name',
		
	
	
	#mv tools	
	'mv_export_cmml'=>'export cmml',
	'mv_search_stream'=>'Search Stream',
	'mv_navigate_stream'=>'Navigate Full Stream',
	'mv_embed_options'=>'Embed Options',
	'mv_overlay'=>'Overlay Controls',
	'mv_stream_tool_heading'=>'Stream Tools',
	'mv_tool_missing'=>'tool request ($1) does not exist',
	'mv_bad_tool_request'=>'bad tool line should be form: tool_name|tool_display_name',
	
	#msg for overlay interface: 
	'mv_search_stream'=>'Search Stream',
	'mv_search_stream_title'=>'Search the Current Stream',
	'mv_new_ht_en'=>'New Transcript',	
	'mv_new_anno_en'=>'New Tag or Annotation',
	
);

/* private */ var $mvDatatypeLabels = array(
	
);

/* private */ var $mvSpecialProperties = array(

);


	/**
	 * Function that returns the namespace identifiers.
	 * 
	 */
	function getNamespaceArray() { 
		return array(
			MV_NS_STREAM			=> 'Stream',
			MV_NS_STREAM_TALK 	   	=> 'Stream_talk',
			MV_NS_SEQUENCE			=> 'Sequence',
			MV_NS_SEQUENCE_TALK		=> 'Sequence_talk',
			MV_NS_MVD	 			=> 'MVD',
  		    MV_NS_MVD_TALK 			=> 'MVD_talk'
		);
	}

	/**
	 * Function that returns the localized label for a datatype.
	 */
	function getDatatypeLabel($msgid) {
		return $this->mvDatatypeLabels[$msgid];
	}

	/**
	 * Function that returns the labels for the special relations and attributes.
	 */
	function getSpecialPropertiesArray() {
		return $this->mvSpecialProperties;
	}

	/**
	 * Function that returns all content messages (those that are stored
	 * in some article, and can thus not be translated to individual users).
	 */
	function getContentMsgArray() {
		return $this->mvContentMessages;
	}

	/**
	 * Function that returns all user messages (those that are given only to
	 * the current user, and can thus be given in the individual user language).
	 */
	function getUserMsgArray() {
		return $this->mvUserMessages;
	}
}

?>
