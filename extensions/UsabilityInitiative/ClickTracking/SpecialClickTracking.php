<?php
/**
 * Special:ClickTracking
 *
 * @file
 * @ingroup Extensions
 */

class SpecialClickTracking extends SpecialPage {
	
	//set to zero for 'all'
	private $top_results = 10;
	private $normalize_top_results = false;
	private $normalize_results = false;
	private $end_timeframe = '20090902';
	private static $minimum_date = '20090815'; //YYYYMMDD (+1 for today)
	
	private static $userTypes = array("basic" => 0, "intermediate" => 1, "expert" => 2);
	private $user_defs = array();
	
	//array of event_id => event_name
	public static $join_conds = " LEFT JOIN click_tracking_events ON event_id=click_tracking_events.id";
	public static $expert_user_conds = "user_total_contribs > 10 ";
	public static $intermediate_user_conds = "user_total_contribs < 10 AND user_total_contribs > 1 ";
	public static $basic_user_conds = "user_total_contribs <= 1";
	
	/*
	 * " DISTINCT session_id "
	 * 
	 * " select count(*), event_id from click_tracking group by event_id order by count(*) desc limit 10;"
	 * 
	 */
	
	private $event_id_to_name = array();
	private $name_to_event_id = array();
	private $time_constraint_sql = "";
	
	
	
	function __construct() {
		parent::__construct( 'ClickTracking' , 'clicktrack');
		wfLoadExtensionMessages( 'ClickTracking' );
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addStyle( 'ClickTracking/SpecialClickTracking.css' );
		UsabilityInitiativeHooks::addScript( 'ClickTracking/SpecialClickTracking.js' );
	}
	
	
	
	
	function setDefaults(){
		$this->user_defs["basic"] = array(
			"anonymous" => "1",
			"total_contribs" => array(
				array("operation" => "<=", "value" => "1"),
			),
		);
		
		$this->user_defs["intermediate"] = array(
			"anonymous" => "0",
			"total_contribs" => array(
				array("operation" => "<", "value" => "10"),
				array("operation" => ">", "value" => "1"),
			),
		);
		
		$this->user_defs["expert"] = array(
			"anonymous" => "0",
			"total_contribs" => array(
				array("operation" => ">=", "value" => "10"),
			),
		);
		
		
	}
	
	
	
	function execute( $par ) {
		global $wgOut, $wgUser;
		
		// Check permissions
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}
		
		
		$this->setHeaders();
		$this->setDefaults();
		
		$wgOut->addScript('<script type="text/javascript">'. "var wgClickTrackUserDefs = ".json_encode($this->user_defs).  '</script>');
		
		$wgOut->setPageTitle( wfMsg( 'ct-title' ) );
		
		$outputTable ="";
		
		
		//grab top N
		$events = $this->getTopEvents();
		
		//open table
		$outputTable .= Xml::openElement( "table", array( "class" =>"sortable click-data", "id" => "clicktrack_data_table" )  );
		
		//create a row for every event
		$i = 0;
		$db_result;
		
		//build row headers
		$header_row = array();
		
		$header_row["event_header"] = wfMsg( 'ct-event-name' );
		$header_row["expert_header"] = wfMsg( 'ct-expert-header' );
		$header_row["intermediate_header"] = wfMsg( 'ct-intermediate-header' );
		$header_row["basic_header"] = wfMsg( 'ct-beginner-header' );
		$header_row["total_header"] = wfMsg( 'ct-total-header' );
		$outputTable .= Xml::buildTableRow( array( "class"=>"table_headers" ), $header_row );
		
		//foreach event, build a row
		while(($db_result = $events->fetchRow()) != null){
			++$i;
			$outputTable .= $this->buildRow( $db_result, $i, $this->user_defs);
		}
		
		
		//close table
		$outputTable .= Xml::closeElement("table");
		
		$wgOut->addHTML( $outputTable );

		$wgOut->addHTML($this->buildDateRange());
		
		//build chart
		$wgOut->addHTML($this->buildChart("advanced.hide",10, "20090815", "20090902", 1));
		
		//$wgOut->addHTML($this->buildControlBox());
		
		$wgOut->addHTML($this->buildChartDialog());
		$wgOut->addHTML($this->buildUserDefBlankDialog());
		
	}
	

	/**
	 * Gets the data to build a chart for PHP or JS purposes
	 * @param $event_id  event id this chart is for
	 * @param $minTime minimum day
	 * @param $maxTime maximum day
	 * @param $increment number of day(s) to increment in units
	 * @param $userDefs  user defintions
	 * @param $isUserDefsJSON true if userDefs is JSON
	 * @return array with chart info
	 */
	static function getChartData($event_id, $minTime, $maxTime, $increment, $userDefs, $isUserDefsJSON=true){
		//get data	
		date_default_timezone_set('UTC');
		
		if($maxTime == 0){
			$maxTime = gmdate("Ymd",time());  //today
		}
		if($minTime == 0){
			$minTime = self::$minimum_date;
		}
		
		
		//FIXME: On PHP 5.3+, this will be MUCH cleaner
		$currBeginDate = new DateTime( $minTime );
		$currEndDate = new DateTime( $minTime );
		$endDate = new DateTime( $maxTime );
		
		
		//get user definitions
		if($isUserDefsJSON){
			$userDefs = json_decode($userDefs, true);
		}
		
		$userDefQueries = array();
		
		foreach ($userDefs as $name => $def){
				if(!isset($def['total_contribs'])){
					$def['total_contribs'] = array();
				}
				if(!isset($def['contrib_1'])){
					$def['contrib_1'] = array();
				}
				if(!isset($def['contrib_2'])){
					$def['contrib_2'] = array();
				}
				if(!isset($def['contrib_3'])){
					$def['contrib_3'] = array();
				}
				$userDefQueries["$name"] = self::buildUserDefQuery($def['anonymous'], $def['total_contribs'], $def['contrib_1'], $def['contrib_2'], $def['contrib_3']);
		}
		
		
		$basicUserData = array();
		$intermediateUserData = array();
		$expertUserData = array();
		
		// PHP 5.3...hurry!
		$plural = ( $increment == 1 ? "" : "s" );
		
		while( $currEndDate->format( "U" )  < $endDate->format( "U" )  ){
			$currEndDate->modify( "+$increment day$plural" );
			$time_constraints_statement = self::getTimeConstraintsStatement( $currBeginDate->format("Ymd"), $currEndDate->format("Ymd") );
			$basicUserData[] = self::getTableValue( $event_id, $userDefQueries['basic'], $time_constraints_statement );
			$intermediateUserData[] = self::getTableValue( $event_id, $userDefQueries['intermediate'], $time_constraints_statement );
			$expertUserData[] = self::getTableValue( $event_id, $userDefQueries['expert'], $time_constraints_statement );
			$currBeginDate->modify( "+$increment day$plural" );
		}
		return array("expert" => $expertUserData, "basic" => $basicUserData, "intermediate" => $intermediateUserData);
	}		

	function buildChart($event_name, $event_id, $minTime, $maxTime, $increment){
		$chartData = self::getChartData($event_id, $minTime, $maxTime, $increment, $this->user_defs, false);
		$chartSrc = $this->getGoogleChartParams( $event_id, $event_name, $minTime, $maxTime, $chartData["basic"], $chartData["intermediate"], $chartData["expert"]);
		return Xml::element( 'img', array( 'src' => $chartSrc , 'id' => 'chart_img' ) );
	}
	
	
	function getGoogleChartParams( $event_id, $event_name, $minDate, $maxDate, $basicUserData, $intermediateUserData, $expertUserData ) {
		$max = max( max($basicUserData), max($intermediateUserData), max($expertUserData));
		return "http://chart.apis.google.com/chart?" . wfArrayToCGI(
		array(
			'chs' => '400x400', 
			'cht' => 'lc',
			'chco' => 'FF0000,0000FF,00FF00',
			'chtt' => "$event_name from $minDate to $maxDate",
			'chdl' => 'Expert|Intermediate|Beginner',
			'chxt' => 'x,y',
			'chd' => 't:' . implode( "," , $expertUserData ) . "|" . 
						implode( "," , $intermediateUserData ) . "|" . implode( "," , $basicUserData ),
			'chds' => "0,$max,0,$max,0,$max"
			));
	}
	
	
	function buildUserDefBlankDialog(){
		$control = "";
		$control .= Xml::openElement("div", array("id" => "user_def_dialog", "class" => "dialog"));
		
		//currently editing...----|
		$control .= Xml::openElement("form", array("id" => "user_definition_form", "class" => "user_def_form"));
		$control .= Xml::openElement("fieldset", array("id" => "user_def_alter_fieldset"));
		$control .= Xml::openElement("legend", array("id" => "user_def_alter_legend"));
		$control .= wfMsg( "editing" );
		$control .= Xml::closeElement("legend");
		
		//[] anonymous users?
		$control .= Xml::openElement("div", array("id" => "anon_users_div", "class" => "checkbox_div control_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "anon_users_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("anon-users");
		$control .= Xml::closeElement("div");
		
		// ----------------
		$control .= Xml::openElement("hr");
		$control .= Xml::closeElement("hr");
		$control .= Xml::openElement("div", array("id" => "contrib_opts_container"));
		
		// [] users with contributions [>=V] [n    ]
		$control .= Xml::openElement("div", array("id" => "total_users_contrib_div", "class" => "checkbox_div control_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "contrib_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("user-contribs");
		
		
		$control .= Xml::closeElement("div");
		
		// [] contributions in timespan 1
		$control .= Xml::openElement("div", array("id" => "contrib_span_1_div", "class" => "checkbox_div control_div"));
		
		$control .= Xml::openElement("div", array("id" => "contrib_span_1_text_div", "class" => "checkbox_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "contrib_span_1_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("user-span") . " 1";
		$control .= Xml::closeElement("div");
		$control .= Xml::closeElement("div");
		
		// [] contributions in timespan 2
		$control .= Xml::openElement("div", array("id" => "contrib_span_2_div", "class" => "checkbox_div control_div"));
		
		$control .= Xml::openElement("div", array("id" => "contrib_span_2_text_div", "class" => "checkbox_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "contrib_span_2_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("user-span") . " 2";
		$control .= Xml::closeElement("div");
		$control .= Xml::closeElement("div");
		
		// [] contributions in timespan 3
		$control .= Xml::openElement("div", array("id" => "contrib_span_3_div", "class" => "checkbox_div control_div"));
		
		$control .= Xml::openElement("div", array("id" => "contrib_span_3_text_div", "class" => "checkbox_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "contrib_span_3_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("user-span") . " 3";
		$control .= Xml::closeElement("div");
		$control .= Xml::closeElement("div");
		
		
		
		
		$control .= Xml::closeElement("div");//close contrib opts
		
		$control .= Xml::closeElement("fieldset");
		$control .= Xml::closeElement("form");	
		$control .= Xml::closeElement("div");
		return $control;
	}
	
	
	function buildUserDefDialog(){
		$control = "";
		$control .= Xml::openElement("div", array("id" => "user_def_dialog", "class" => "dialog"));
		
		//currently editing...----|
		$control .= Xml::openElement("form", array("id" => "user_definition_form", "class" => "user_def_form"));
		$control .= Xml::openElement("fieldset", array("id" => "user_def_alter_fieldset"));
		$control .= Xml::openElement("legend", array("id" => "user_def_alter_legend"));
		$control .= wfMsg( "ct-editing" );
		$control .= Xml::closeElement("legend");
		
		//[] anonymous users?
		$control .= Xml::openElement("div", array("id" => "anon_users_div", "class" => "checkbox_div control_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "anon_users_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("ct-anon-users");
		$control .= Xml::closeElement("div");
		
		// ----------------
		$control .= Xml::openElement("hr");
		$control .= Xml::closeElement("hr");
		$control .= Xml::openElement("div", array("id" => "contrib_opts_container"));
		
		// [] users with contributions [>=V] [n    ]
		$control .= Xml::openElement("div", array("id" => "total_users_contrib_div", "class" => "checkbox_div control_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "contrib_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("ct-user-contribs");
		
		$control .= Xml::openElement("div", array("id" => "contrib_sub_div", "class" => "checkbox_div sub_option_div"));
		$control .= $this->buildUserDefNumberSelect(false, false, "contrib_sub");
		$control .= Xml::closeElement("div");
		
		$control .= Xml::closeElement("div");
		
		// [] contributions in timespan 1
		//    []     [>=V] [n    ]
		//    [] AND [>=V] [n    ]
		$control .= Xml::openElement("div", array("id" => "contrib_span_1_div", "class" => "checkbox_div control_div"));
		
		$control .= Xml::openElement("div", array("id" => "contrib_span_1_text_div", "class" => "checkbox_div"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "contrib_span_1_checkbox", "class" => "user_def_checkbox"));
		$control .= Xml::closeElement("input");
		$control .= wfMsg("ct-user-span") . " 1";
		$control .= Xml::closeElement("div");
		
		$control .= Xml::openElement("div", array("id" => "contrib_span_1_range_1_div", "class" => "checkbox_div sub_option_div"));
		$control .= $this->buildUserDefNumberSelect(true, false, "contrib_span_1_1");
		$control .= Xml::closeElement("div");
		
		$control .= Xml::openElement("div", array("id" => "contrib_span_1_range_2_div", "class" => "checkbox_div sub_option_div"));
		$control .= $this->buildUserDefNumberSelect(true, true, "contrib_span_1_2");
		$control .= Xml::closeElement("div");
		
		$control .= Xml::closeElement("div");
		
		$control .= Xml::closeElement("div");//close contrib opts
		
		$control .= Xml::closeElement("fieldset");
		$control .= Xml::closeElement("form");	
		$control .= Xml::closeElement("div");
		return $control;
	}
	
	
	function buildUserDefNumberSelect($include_checkbox, $include_and, $ids){
		$control = "";
		if($include_checkbox){
			$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "{$ids}_checkbox", "class" => "number_select_checkbox"));
			$control .= Xml::closeElement("input");
		}
		
		if($include_and){
			$control .= wfMsg("ct-and");
		}
		
		$control .= Xml::openElement("select", array("id" => "{$ids}_ltgt", "class" => "number_select_ltgt"));
		$control .= Xml::openElement("option", array("id" => "{$ids}_lt", "class" => "number_select_ltgt_opt", "value" => "lt"));
		$control .= "&lt;=";
		$control .= Xml::closeElement("option");
		$control .= Xml::openElement("option", array("id" => "{$ids}_gt", "class" => "number_select_ltgt_opt", "value" => "gt"));
		$control .= "&gt;=";
		$control .= Xml::closeElement("option");
		$control .= Xml::closeElement("select");
		$control .= Xml::openElement("input", array("type" => "text", "id" => "{$ids}_text", "class" => "number_select_text"));
		$control .= Xml::closeElement("input");
		return $control;
	}
	
	
	function buildChartDialog(){
		$control = "";
		$control .= Xml::openElement("div", array("id" => "chart_dialog", "class" => "dialog"));
		
		$control .= Xml::openElement("form", array("id" => "chart_dialog_form", "class" => "chart_form"));
		$control .= Xml::openElement("fieldset", array("id" => "chart_dialog_alter_fieldset"));
		$control .= Xml::openElement("legend", array("id" => "chart_dialog_alter_legend"));
		$control .= wfMsg( "ct-increment-by" );
		$control .= Xml::closeElement("legend");
		
		$control .= Xml::openElement("table", array("id" => "chart_dialog_increment_table"));
		$control .= Xml::openElement("tbody", array("id" => "chart_dialog_increment_tbody"));
		
		$control .= Xml::openElement("tr", array("id" => "chart_dialog_increment_row"));
		
		$control .= Xml::openElement("td", array("id" => "chart_dialog_increment_cell"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "chart_increment", "class" => "chart_dialog_area", "value" => '1'));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "chart_dialog_button_cell"));
		$control .= Xml::openElement("input", array("type" => "button", "id" => "change_graph", "value" => wfMsg( "ct-change-graph" ) )  );
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
		
		$control .= Xml::closeElement("tbody");
		$control .= Xml::closeElement("table");
		$control .= Xml::closeElement("fieldset");
		$control .= Xml::closeElement("form");	
		$control .= Xml::closeElement("div");
		return $control;
	}
	
	
	function buildDateRange(){
		$control = Xml::openElement("form", array("id" => "date_range"));
		
		$control .= Xml::openElement("fieldset", array("id" => "date_range_fieldset"));
		$control .= Xml::openElement("legend", array("id" => "date_range_legend"));
		$control .= wfMsg('ct-date-range');
		$control .= Xml::closeElement("legend");
		

		
		$control .= Xml::openElement("table", array("id" => "date_range_table"));
		$control .= Xml::openElement("tbody", array("id" => "date_range_tbody"));
		
		
		$control .= Xml::openElement("tr", array("id" => "start_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "start_date_label", "class" => "date_range_label"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "start_date_checkbox", "class" => "date_range_checkbox", "checked" => ""));
		$control .= Xml::closeElement("input");
		$control .= wfMsg( "ct-start-date" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "start_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "start_date", "class" => "date_range_input", "value" => self::$minimum_date));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
		
		$control .= Xml::openElement("tr", array("id" => "end_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "end_date_label", "class" => "date_range_label"));
		$control .= Xml::openElement("input", array("type" => "checkbox", "id" => "end_date_checkbox", "class" => "date_range_checkbox", "checked" => ""));
		$control .= Xml::closeElement("input");
		$control .= wfMsg( "ct-end-date" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "end_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "end_date", "class" => "date_range_input"));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
		
		$control .= Xml::closeElement("tbody");
		$control .= Xml::closeElement("table");
		$control .= Xml::closeElement("fieldset");
		
		$control .= Xml::closeElement("form");
		
		return $control;
	}
	
	
	function buildControlBox(){
		
		$control = Xml::openElement("form", array("id" => "control_box_form"));
		$control .= Xml::openElement("table", array("id" => "control_box_table"));
		$control .= Xml::openElement("tbody", array("id" => "control_box_tbody"));
		
		
		$control .= Xml::openElement("tr", array("id" => "start_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "start_date_label", "class" => "control_box_label"));
		$control .= wfMsg( "ct-start-date" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "start_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "start_date", "class" => "control_box_input"));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");

		
		
		$control .= Xml::openElement("tr", array("id" => "end_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "end_date_label", "class" => "control_box_label"));
		$control .= wfMsg( "ct-end-date" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "end_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "end_date", "class" => "control_box_input"));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
		
		
		
		$control .= Xml::openElement("tr", array("id" => "increment_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "increment_date_label", "class" => "control_box_label"));
		$control .= wfMsg( "ct-increment-by" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "increment_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "increment_date", "class" => "control_box_input"));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
		
		
		
		$control .= Xml::openElement("tr", array("id" => "change_graph_row"));
		$control .= Xml::openElement("td", array("id" => "change_graph_cell", "colspan" => 2));
		
		$control .= Xml::openElement("input", array("type" => "button", "id" => "change_graph", "value" => wfMsg( "ct-change-graph" ) )  );
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
				
		$control .= Xml::closeElement("tbody");
		$control .= Xml::closeElement("table");
		$control .= Xml::closeElement("form");
		
		return $control;
	}
	

	
	function buildRow($data_result, $row_count, $userDefs){
			
			$userDefQueries = array();
			foreach ($userDefs as $name => $def){
				if(!isset($def['total_contribs'])){
					$def['total_contribs'] = array();
				}
				if(!isset($def['contrib_1'])){
					$def['contrib_1'] = array();
				}
				if(!isset($def['contrib_2'])){
					$def['contrib_2'] = array();
				}
				if(!isset($def['contrib_3'])){
					$def['contrib_3'] = array();
				}
				$userDefQueries["$name"] = self::buildUserDefQuery($def['anonymous'], $def['total_contribs'], $def['contrib_1'], $def['contrib_2'], $def['contrib_3']);
			}
		
			$outputRow = Xml::openElement("tr", array("class" => "table_data_row"));
			
			//event name
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_name", "id" => "event_name_$row_count", "value" =>$data_result['event_id']));
			$outputRow .= $data_result['event_name'];
			$outputRow .=Xml::closeElement("td");
			
			//advanced users
			$cellValue = self::getTableValue($data_result['event_id'], $userDefQueries["expert"]);
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_data expert_data", "id" => "event_expert_$row_count",
										"value" => $cellValue));
			$outputRow .= $cellValue;
			$outputRow .=Xml::closeElement("td");
			
			//intermediate users
			$cellValue = self::getTableValue($data_result['event_id'], $userDefQueries["intermediate"]);
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_data intermediate_data", "id" => "event_intermediate_$row_count", 
										"value" => $cellValue));
			$outputRow .= $cellValue;
			$outputRow .=Xml::closeElement("td");
			
			//basic users
			$cellValue = self::getTableValue($data_result['event_id'], $userDefQueries["basic"]);
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_data basic_data", "id" => "event_basic_$row_count",
									"value" => $cellValue));
			$outputRow .= $cellValue;
			$outputRow .=Xml::closeElement("td");
			
			//totals
			$cellValue = $data_result["count(event_id)"];
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_data total_data", "id" => "total_$row_count",
									"value" => $cellValue));
			$outputRow .= $cellValue;
			$outputRow .=Xml::closeElement("td");
			
			
			$outputRow .= Xml::closeElement("tr");
			
			return $outputRow;
			
	}

	/*
	 * get time constraints
	 * @param minTime minimum day (YYYYMMDD)
	 * @param maxTime max day (YYYYMMDD)
	 * NOTE: once some of the constraints have been finalized, this will use more of the Database functions and not raw SQL
	 */
	static function getTimeConstraintsStatement( $minTime, $maxTime ){
		if($minTime == 0 || $maxTime == 0){
			return '';		
		}
		else {
			return "WHERE action_time >= $minTime AND action_time <= $maxTime";	
		}
		
	}
	
	
	/**
	 * Gets the top N events as set in the page pref
	 * @param $time_constraint_statement
	 * @return unknown_type
	 * NOTE: once some of the constraints have been finalized, this will use more of the Database functions and not raw SQL
	 */
	function getTopEvents($time_constraint_statement=''){
		$normalize = "click_tracking";
		$time_constraint = $time_constraint_statement;
		if($this->normalize_top_results){
			$normalize = "(select distinct session_id, event_id from click_tracking $time_constraint_statement) as t1";
			$time_constraint = "";
		}
		$limit = $this->top_results;
		$join = self::$join_conds;
		$sql = "select count(event_id), event_id,event_name from $normalize $join $time_constraint group by event_id order by count(event_id) desc limit $limit";
		
		//returns count(event_id),event_id, event_name, top one first
		$dbr = wfGetDB( DB_SLAVE );
		$dbresult = $dbr->query($sql);
		
		return $dbresult;
	}

	/**
	 * Gets a table value for a given User ID
	 * NOTE: once some of the constraints have been finalized, this will use more of the Database functions and not raw SQL
	 */
	static function getTableValue($event_id, $user_conditions, $time_constraint_statement = '', $normalize_results=false){
		
		$normalize = "click_tracking";
		$time_constraint = $time_constraint_statement;
		if($normalize_results){
			$normalize = "(select distinct session_id, event_id, user_total_contribs, user_contribs_span1, user_contribs_span2, user_contribs_span3, is_logged_in from click_tracking $time_constraint_statement) as t1";
			$time_constraint = "";
		}
		
		
		$where = ($time_constraint == "" ? "where" : "");
		
		$and = ($time_constraint == "" ? "": "and");
		
		$sql ="select count(*) from $normalize $where $time_constraint $and ($user_conditions) and event_id=$event_id";
		
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->query($sql);
		$resRow = $result->fetchRow();
		return $resRow["count(*)"];
	}
	
	/**
	 * Generates a query for a user type definition
	 * @param $include_anon_users boolean, include anon users or not
	 * @param $total_contribs array, nonempty if total contribs to be included
	 * @param $contrib_1 array, nonempty AND conditions for user_contribs_1
	 * @param $contrib_2 array, nonempty AND conditions for user_contribs_1
	 * @param $contrib_3 array, nonempty AND conditions for user_contribs_1
	 * @return unknown_type query
	 */
	static function buildUserDefQuery($include_anon_users, $total_contribs, $contrib_1, $contrib_2, $contrib_3){
		$or_conds = array();
		$and_conds = array();
		$sql = "";
		
		if( (boolean)$include_anon_users ){
			$or_conds[] = array("field" => "is_logged_in", "operation" => "=", "value" =>"0");
		}
		
		if(!empty($total_contribs)){
			foreach($total_contribs as $contribs){
				$and_conds[] = array("field" => "user_total_contribs", "operation" => $contribs["operation"], "value" => $contribs["value"]);
			}
		}
		
		
		if(!empty($contrib_1)){
			foreach($contrib_1 as $contribs){
				$and_conds[] = array("field" => "user_contribs_span1", "operation" => $contribs["operation"], "value" => $contribs["value"]);
			}
		}
		if(!empty($contrib_2)){
			foreach($contrib_2 as $contribs){
				$and_conds[] = array("field" => "user_contribs_span2", "operation" => $contribs["operation"], "value" => $contribs["value"]);
			}
		}
		if(!empty($contrib_3)){
			foreach($contrib_3 as $contribs){
				$and_conds[] = array("field" => "user_contribs_span3", "operation" => $contribs["operation"], "value" => $contribs["value"]);
			}
		}
		
		foreach($and_conds as $cond){
			if(!empty($sql)){
				$sql .= " AND ";
			}
			$sql .= $cond["field"] . " " . $cond["operation"] . " " . $cond["value"];
		}
		foreach($or_conds as $cond){
			if(!empty($sql)){
				$sql .= " OR ";
			}
			$sql .= $cond["field"] . " " . $cond["operation"] . " " . $cond["value"];
		}
		
		return $sql;
	}
	
	
	
}