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
	private $use_timeframes = false;
	private $begin_timeframe = '20090815'; //YYYYMMDD (+1 for today)
	private $end_timeframe = '20090902';
	
	
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
		parent::__construct( 'ClickTracking' );
		wfLoadExtensionMessages( 'ClickTracking' );
		UsabilityInitiativeHooks::initialize();
		UsabilityInitiativeHooks::addStyle( 'ClickTracking/SpecialClickTracking.css' );
		UsabilityInitiativeHooks::addScript( 'ClickTracking/SpecialClickTracking.js' );
	}
	
	
	
	
	
	function execute( $par ) {
		global $wgOut, $wgUser;
		
		// Check permissions
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}
		
		$this->setHeaders();
		$wgOut->setPageTitle( wfMsg( 'clicktracking-title' ) );
		
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
		
		$header_row["event_header"] = wfMsg( 'event-name' );
		$header_row["expert_header"] = wfMsg( 'expert-header' );
		$header_row["intermediate_header"] = wfMsg( 'intermediate-header' );
		$header_row["beginner_header"] = wfMsg( 'beginner-header' );
		$header_row["total_header"] = wfMsg( 'total-header' );
		$outputTable .= Xml::buildTableRow( array( "class"=>"table_headers" ), $header_row );
		
		//foreach event, build a row
		while(($db_result = $events->fetchRow()) != null){
			++$i;
			$outputTable .= $this->buildRow( $db_result, $i );
		}
		
		
		//close table
		$outputTable .= Xml::closeElement("table");
		
		$wgOut->addHTML( $outputTable );

		
		//build chart
		$wgOut->addHTML($this->buildChart("advanced.hide",10, "20090815", "20090902", 1));
		
		$wgOut->addHTML($this->buildControlBox());
		
	}
	

	/**
	 * Gets the data to build a chart for PHP or JS purposes
	 * @param $event_id  event id this chart is for
	 * @param $minTime minimum day
	 * @param $maxTime maximum day
	 * @param $increment number of day(s) to increment in units
	 * @return array with chart info
	 */
	static function getChartData($event_id, $minTime, $maxTime, $increment){
		//get data	
		date_default_timezone_set('UTC');
		
		//FIXME: On PHP 5.3+, this will be MUCH cleaner
		$currBeginDate = new DateTime( $minTime );
		$currEndDate = new DateTime( $minTime );
		$endDate = new DateTime( $maxTime );
		
		$basicUserData = array();
		$intermediateUserData = array();
		$expertUserData = array();
		
		// PHP 5.3...hurry!
		$plural = ( $increment == 1 ? "" : "s" );
		
		while( $currEndDate->format( "U" )  < $endDate->format( "U" )  ){
			$currEndDate->modify( "+$increment day$plural" );
			$time_constraints_statement = self::getTimeConstraintsStatement( $currBeginDate->format("Ymd"), $currEndDate->format("Ymd") );
			$basicUserData[] = self::getTableValue( $event_id, self::$basic_user_conds, $time_constraints_statement );
			$intermediateUserData[] = self::getTableValue( $event_id, self::$intermediate_user_conds, $time_constraints_statement );
			$expertUserData[] = self::getTableValue( $event_id, self::$expert_user_conds, $time_constraints_statement );
			$currBeginDate->modify( "+$increment day$plural" );
		}
		return array("expert" => $expertUserData, "basic" => $basicUserData, "intermediate" => $intermediateUserData);
	}		

	function buildChart($event_name, $event_id, $minTime, $maxTime, $increment){
		$chartData = self::getChartData($event_id, $minTime, $maxTime, $increment);
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
	
	
	function buildControlBox(){
		
		$control = Xml::openElement("form", array("id" => "control_box_form"));
		$control .= Xml::openElement("table", array("id" => "control_box_table"));
		$control .= Xml::openElement("tbody", array("id" => "control_box_tbody"));
		
		
		$control .= Xml::openElement("tr", array("id" => "start_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "start_date_label", "class" => "control_box_label"));
		$control .= wfMsg( "start-date" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "start_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "start_date", "class" => "control_box_input"));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");

		
		
		$control .= Xml::openElement("tr", array("id" => "end_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "end_date_label", "class" => "control_box_label"));
		$control .= wfMsg( "end-date" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "end_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "end_date", "class" => "control_box_input"));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
		
		
		
		$control .= Xml::openElement("tr", array("id" => "increment_date_row"));
		
		$control .= Xml::openElement("td", array("id" => "increment_date_label", "class" => "control_box_label"));
		$control .= wfMsg( "increment-by" );
		$control .= Xml::closeElement("td");
		
		$control .= Xml::openElement("td", array("id" => "increment_date_textarea"));
		$control .= Xml::openElement("input", array("type" => "text", "id" => "increment_date", "class" => "control_box_input"));
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
		
		
		
		$control .= Xml::openElement("tr", array("id" => "change_graph_row"));
		$control .= Xml::openElement("td", array("id" => "change_graph_cell", "colspan" => 2));
		
		$control .= Xml::openElement("input", array("type" => "button", "id" => "change_graph", "value" => wfMsg( "change-graph" ) )  );
		$control .= Xml::closeElement("input");
		$control .= Xml::closeElement("td");
		
		$control .= Xml::closeElement("tr");
				
		$control .= Xml::closeElement("tbody");
		$control .= Xml::closeElement("table");
		$control .= Xml::closeElement("form");
		
		return $control;
	}
	
	
	function buildRow($data_result, $row_count){
			
			$outputRow = Xml::openElement("tr", array("class" => "table_data_row"));
			
			//event name
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_name", "id" => "event_name_$row_count", "value" =>$data_result['event_id']));
			$outputRow .= $data_result['event_name'];
			$outputRow .=Xml::closeElement("td");
			
			//advanced users
			$cellValue = self::getTableValue($data_result['event_id'], self::$expert_user_conds);
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_data expert_data", "id" => "event_expert_$row_count",
										"value" => $cellValue));
			$outputRow .= $cellValue;
			$outputRow .=Xml::closeElement("td");
			
			//intermediate users
			$cellValue = self::getTableValue($data_result['event_id'], self::$intermediate_user_conds);
			$outputRow .=Xml::openElement("td", 
									array("class" => "event_data intermediate_data", "id" => "event_intermediate_$row_count", 
										"value" => $cellValue));
			$outputRow .= $cellValue;
			$outputRow .=Xml::closeElement("td");
			
			//basic users
			$cellValue = self::getTableValue($data_result['event_id'], self::$basic_user_conds);
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
		
		$sql ="select count(*) from $normalize $where $time_constraint $and $user_conditions and event_id=$event_id";
		
		$dbr = wfGetDB( DB_SLAVE );
		$result = $dbr->query($sql);
		$resRow = $result->fetchRow();
		return $resRow["count(*)"];
	}
	
}