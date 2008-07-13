<?php
require_once("$IP/extensions/BugzillaReports/BSQLQuery.php");
/**
 * A bugzilla query
 */
class BugzillaQuery extends BSQLQuery {
	var $supportedParameters=array (
		'alias'			=> 'field',
		'bar'			=> 'column',
		'blocks'		=> 'field-depends',
		'cc'			=> 'field',
		'columns'		=> 'columns',
		'component'		=> 'field',
		'deadline'		=> 'field-date',
		'depends'		=> 'field-depends',
		'detailsrow'		=> 'columns',
		'detailsrowprepend'	=> 'free',
		'flag'			=> 'field-special',
		'format'		=> 'value',			# table (default) or list
		'from'			=> 'field',
		'group'			=> 'sort',
		'groupformat'		=> 'value',
		'heading'		=> 'free',
		'headers'		=> 'value',
		'id'			=> 'field',
		'lastcomment'		=> 'boolean',
		'maxrows'		=> 'value',
		'maxrowsbar'		=> 'value',
		'milestone'		=> 'field',
		'modified'		=> 'field-date',
		'nameformat'		=> 'value',			# real (default),tla or login
		'order'			=> 'value',
		'priority'		=> 'field',
		'product'		=> 'field',
		'quickflag'		=> 'value',
		'noresultsmessage'	=> 'free',
		'search'		=> 'field-text',
		'severity'		=> 'field',
		'sort'			=> 'sort',
		'status'		=> 'field',
		'to'			=> 'field',
		'url'			=> 'field',
		'version'		=> 'field',
		'votes'			=> 'field-int',
	);
	var $defaultParameters=array (
		'columns'		=> 'id,priority,status,severity,version,product,summary,url',
		'noresultsmessage'	=> 'no bugzilla tickets were found',
		'order'			=> 'asc',
		'status'		=> '!CLOSED',
		'sort'			=> 'priority'
	);
	var $columnName=array (
		'alias'		=> 'Alias',
		'blocks'	=> 'Blocks',
		'component'	=> 'Component',
		'cc'		=> 'CC',
		'deadline'	=> 'Deadline',
		'depends'	=> 'Depends',
		'flag'		=> 'Flagged For',
		'flagdate'	=> 'Flag Date',
		'flagfrom'	=> 'Flagged By',
		'flagname'	=> 'Flag',
		'from'		=> 'Requester',
		'id'		=> 'ID',
		'severity'	=> 'Severity',
		'status'	=> 'Status',
		'milestone'	=> 'Milestone',
		'modified'	=> 'Modified',
		'product'	=> 'Product',
		'priority'	=> 'P',
		'summary'	=> 'Summary',
		'to'		=> 'Assignee',
		'url'		=> '&nbsp;',
		'version'	=> 'Version',
		'votes'		=> 'Votes',
		);
	# Fields and their mapping to the value in the results sets
	var $fieldMapping=array (
		'cc'	=> 'cc',
		'from'	=> 'raisedby',
		'to'	=> 'assignedto',
		);
	var $fieldSQLColumn=array (
		'cc'		=> 'ccprofiles.login_name',
		'component'	=> 'components.name',
		'from'		=> 'reporterprofiles.login_name',
		'milestone'	=> 'target_milestone',
		'modified'	=> 'lastdiffed',
		'product'	=> 'products.name',
		'severity'	=> 'bug_severity',
		'status'	=> 'bug_status',
		'to'		=> 'profiles.login_name',
		'url'		=> 'bug_file_loc',
	);
	var $fieldDefaultSort=array (
		'modified'	=> 'desc',
		'votes'		=> 'desc'
	);
	var $formats=array (
		'alias'		=> 'id',
		'blocks'	=> 'id',
		'cc'		=> 'name',
		'deadline'	=> 'date',
		'depends'	=> 'id',
		'flagdate'	=> 'date',
		'flagfrom'	=> 'name',
		'from'		=> 'name',
		'id'		=> 'id',
		'modified'	=> 'date',
		'to'		=> 'name',
		'url'		=> 'url',
	);
	var $fieldValues=array (
		'priority'	=> 'P1,P2,P3,P4,P5',
		'status'	=> 'ASSIGNED,NEW,RESOLVED,CLOSED',
		'severity'	=> 'blocker,critical,major,normal,minor,trivial,enhancement'
	);
	var $sortMapping=array (
		'deadline'	=> "COALESCE(deadline, '2100-01-01')",
		'milestone'	=> "COALESCE(NULLIF(milestone,'---'),'XXXXX')",
		'id'		=> 'bugs.bug_id'
	);
	var $dependsRowColumns=array (
		'depends'		=> "block",
		'dependsto'		=> "title",   # Output in the title
		'dependsstatus'		=> "title",   # Output in the title
		'dependssummary'	=> "block",
	);
	var $blocksRowColumns=array (
		'blocks'	=> "block",
		'blocksto'	=> "title",   # Output in the title
		'blocksstatus'	=> "title",
		'blockssummary'	=> "block"
	);
	#
	# Title for a given value rendering
	#
	var $valueTitle=array (
		'alias'		=> "id,alias",
		'blocks'	=> "blocks,blocksalias",
		'depends'	=> "depends,dependsalias",
		'id'		=> "id,alias"
	);

	private $output;

	# WHY IS THIS NEEDED?
	#var $dbhost="localhost";

	#
	# Parse in a context object which implements the following
	#
	# Public Variables
	# - debug, bzserver, interwiki,
	# - database, host, dbuser, password;
	#
	# Functions
	# - debug
	# - warn,
	# - getErrorMessage
	#
	function BugzillaQuery( $context ) {
		$this->setContext($context);
	}

	#
	# Get rendering formats
	#
	function getFormats() {
		return $this->formats;
	}

	#
	# Get default priority
	#
	function getDefaultSort() {
		return $this->defaultSort;
	}


	#
	# Render the results
	#
	function render() {
		$this->context->debug &&
			$this->context->debug("Rendering BugzillaQuery");

		$where="";
		#
		# If ID is set then don't do any other query since we're being pretty
		# Specific
		#

		if ($this->get('id')) {
			$where.=$this->getWhereClause($this->get('id'),"bug_id");
		} else {
			#
			# Now process other fields and make sure we have SQL and implicit
			# usage built up
			#
			foreach (array_keys($this->supportedParameters) as $column) {
				if ($this->get($column)) {
					switch ($this->supportedParameters[$column]) {
						case "field" :
						case "field-int" :
							if (preg_match("/[,!+]/",$this->get($column))) {
								$this->implictlyAddColumn($column);
							} else {
								$this->implictlyRemoveColumn($column);
							}
						case "field-depends" :
							$sqlColumn=$column;
							if (array_key_exists($column,
									$this->fieldSQLColumn)) {
								$sqlColumn=$this->fieldSQLColumn[$column];
							}
							switch ($this->supportedParameters[$column]) {
								case "field" :
									$where.=$this->
										getWhereClause($this->get($column),
										$sqlColumn);
									break;
								case "field-int" :
									$where.=$this->
										getIntWhereClause($this->get($column),
										$sqlColumn);
									break;
							}
							$this->requireField($column);
							if (array_key_exists($column,
									$this->fieldDefaultSort)) {
								$this->setImplicit('sort',"$column");
								$this->setImplicit('order',
									$this->fieldDefaultSort[$column]);
							}
							break;
					}
				}
			}
			if ($this->get('format') == "list") {
				$this->requireField("to");
				$this->requireField("deadline");
			}
			if ($this->get('deadline')) {
				$where.=$this->getDateWhereClause($this->get('deadline'),"deadline");
				$this->requireField("deadline");
				$this->setImplicit('sort',"deadline");
				if (preg_match("/[,!+]/",$this->get("deadline"))) {
					$this->implictlyAddColumn("deadline");
				} else {
					$this->implictlyRemoveColumn("deadline");
				}

			}
			if ($this->get('flag')) {
				$this->requireField("flag");
				$this->implictlyAddColumn("flagfrom");
				$this->implictlyAddColumn("flagname");
				$this->implictlyAddColumn("flagdate");
			}
			if ($this->get('lastcomment')) {
				$this->requireField("lastcomment");
			}
			if ($this->get('modified')) {
				$where.=
					$this->getDateWhereClause($this->get('modified'),"lastdiffed");
				$this->requireField("modified");
				$this->setImplicit('sort',"modified");
				$this->setImplicit('order',"desc");
				preg_match("/[+]/",$this->get('modified')) &&
					$this->implictlyAddColumn("modified");
			}
			if ($this->get('search')) {
				$where.=" and short_desc like '%".$this->search.="%'";
			}
		}

		#
		# Quick flag enabled by default
		#
		$this->requireField("quickflag");

		#
		# Alias enabled by default
		#
		$this->requireField("alias");

		#
		# Prepare the query;
		#
		$this->preSQLGenerate();

		$this->context->debug &&
			$this->context->debug("Columns required are "
				.join(",",array_keys($this->fieldsRequired)));
		$sql="SELECT *,bugs.bug_id as id";
		if ($this->isRequired("blocks")) {
			$sql.=", blockstab.blocks as blocks, blockstab.blocksalias as blocksalias, blockstab.blockssummary as blockssummary,blockstab.blocksstatus as blocksstatus, blockstab.blockspriority as blockspriority, blockstab.realname as blocksto";
		}
		if ($this->isRequired("cc")) {
			if ($this->get('nameformat')=='login') {
				$sql.=", ccprofiles.login_name as cc";
			} else {
				$sql.=", ccprofiles.realname as cc";
			}
		}
		if ($this->isRequired("component")) {
			$sql.=", components.name as component";
		}
		if ($this->isRequired("depends")) {
			$sql.=", dependstab.depends as depends, dependstab.dependsalias as dependsalias, dependstab.dependssummary as dependssummary,dependstab.dependsstatus as dependsstatus, dependstab.dependspriority as dependspriority, dependstab.realname as dependsto";
		}
		if ($this->isRequired("flag")) {
			if ($this->get('nameformat')=='login') {
				$sql.=", flagprofiles.flagfrom_login as flagfrom";
				$sql.=", flagprofiles.flag_login as flag";
			} else {
				$sql.=", flagprofiles.flagfrom_realname as flagfrom";
				$sql.=", flagprofiles.flag_realname as flag";
			}
			$sql.=", flagprofiles.flagname as flagname";
			$sql.=", flagprofiles.flagdate as flagdate";
		} else if ($this->isRequired("quickflag")) {
			$sql.=", quickflag.flagdate as flagdate";
		}
		if ($this->isRequired("from")) {
			if ($this->get('nameformat')=='login') {
				$sql.=", reporterprofiles.login_name as raisedby";
			} else {
				$sql.=", reporterprofiles.realname as raisedby";
			}
		}
		if ($this->isRequired("milestone")) {
			$sql.=", target_milestone as milestone";
		}
		if ($this->isRequired("modified")) {
			$sql.=", lastdiffed as modified";
		}
		if ($this->isRequired("product")) {
			$sql.=", products.name as product";
		}
		if ($this->isRequired("severity")) {
			$sql.=", bug_severity as severity";
		}
		if ($this->isRequired("status")) {
			$sql.=", bug_status as status";
		}
		if ($this->isRequired("summary")) {
			$sql.=", short_desc as summary";
		}
		if ($this->isRequired("to")) {
			if ($this->get('nameformat')=='login') {
				$sql.=", profiles.login_name as assignedto";
			} else {
				$sql.=", profiles.realname as assignedto";
			}
		}
		if ($this->isRequired("url")) {
			$sql.=", bug_file_loc as url";
		}
		$sql.=" FROM ".$this->context->database.
			".bugs";
		if ($this->isRequired("blocks")) {
			$sql.=" LEFT JOIN (SELECT dependson,blocked as blocks, blockedbugs.alias as blocksalias, blockedbugs.short_desc as blockssummary, blockedbugs.bug_status as blocksstatus, blockedbugs.priority as blockspriority,login_name,realname from ".
				$this->context->database.
				".dependencies"
				." INNER JOIN ".$this->context->database.".bugs as blockedbugs ON dependencies.blocked=blockedbugs.bug_id"
				." INNER JOIN ".
				$this->context->database.
				".profiles ON blockedbugs.assigned_to=profiles.userid"
				." where blockedbugs.bug_status <> 'CLOSED' order by blockedbugs.priority) as blockstab ON blockstab.dependson=bugs.bug_id";
		}
		if ($this->isRequired("component")) {
			$sql.=" LEFT JOIN ".
				$this->context->database.
				".components on bugs.component_id=components.id";
		}
		if ($this->isRequired("cc")) {
			$sql.=" INNER JOIN (SELECT bug_id,login_name,realname from ".
				$this->context->database.
				".cc INNER JOIN ".
				$this->context->database.
				".profiles ON cc.who=profiles.userid";
			if ($this->get('cc')) {
				$sql.=$this
					->getWhereClause($this->get('cc'),"profiles.login_name");
			}
			$sql.=") as ".
				"ccprofiles on ccprofiles.bug_id=bugs.bug_id";
		}
		if ($this->isRequired("depends")) {
			$sql.=" LEFT JOIN (SELECT blocked,dependson as depends, dependsonbugs.alias as dependsalias, dependsonbugs.short_desc as dependssummary, dependsonbugs.bug_status as dependsstatus, dependsonbugs.priority as dependspriority, login_name, realname from ".
				$this->context->database.
				".dependencies"
				." INNER JOIN ".$this->context->database.".bugs as dependsonbugs ON dependencies.dependson=dependsonbugs.bug_id"
				." INNER JOIN ".
				$this->context->database.
				".profiles ON dependsonbugs.assigned_to=profiles.userid"
				." where dependsonbugs.bug_status <> 'CLOSED' order by dependsonbugs.priority) as dependstab ON dependstab.blocked=bugs.bug_id";
		}
		if ($this->isRequired("flag")) {
			$sql.=" INNER JOIN (SELECT bug_id,creation_date as flagdate,flagsto.login_name as flag_login,flagsto.realname as flag_realname,flagsfrom.login_name as flagfrom_login, flagsfrom.realname as flagfrom_realname,flagtypes.name as flagname from ".
				$this->context->database.
				".flags INNER JOIN ".
				$this->context->database.
				".flagtypes ON flags.type_id=flagtypes.id INNER JOIN ".
				$this->context->database.
				".profiles as flagsto ON flags.requestee_id=flagsto.userid INNER JOIN ".
				$this->context->database.
				".profiles as flagsfrom ON flags.setter_id=flagsfrom.userid where status='?'";
			if ($this->get('flag')) {
					$sql.=$this
						->getWhereClause($this->get('flag'),
							"flagsto.login_name");
				}
			$sql.=") as ".
				"flagprofiles on flagprofiles.bug_id=bugs.bug_id";
		} else if ($this->isRequired("quickflag")) {
			$sql.=" LEFT JOIN (SELECT bug_id as quickflagbugid, MAX(creation_date) as flagdate from ".
				$this->context->database.
				".flags where status='?' group by quickflagbugid) as ".
				"quickflag on quickflag.quickflagbugid=bugs.bug_id";
		}
		if ($this->isRequired("from")) {
			$sql.=" LEFT JOIN ".
				$this->context->database.
				".profiles as reporterprofiles on bugs.reporter=reporterprofiles.userid";
		}
		if ($this->isRequired("lastcomment")) {
			$sql.=" LEFT JOIN (SELECT MAX(longdescs.comment_id) as sub_comment_id, ".
			"longdescs.bug_id as sub_bug_id FROM ".$this->context->database.
			".longdescs GROUP BY longdescs.bug_id) ".
			"descs ON bugs.bug_id=descs.sub_bug_id LEFT JOIN longdescs ON ".
			"longdescs.comment_id=sub_comment_id";
		}
		if ($this->isRequired("product")) {
			$sql.=" LEFT JOIN ".
				$this->context->database.
				".products on bugs.product_id=products.id";
		}
		if ($this->isRequired("to")) {
			$sql.=" LEFT JOIN ".
				$this->context->database.
				".profiles on bugs.assigned_to=profiles.userid";
		}
		$sql.=" where 1=1 ".$where." order by ".
			$this->getMappedSort()." ".$this->getOrder();
		$sql.=";";
		$this->context->debug && $this->context->debug("SQL : ".$sql);

		$link = mysql_connect($this->context->host,
			$this->context->dbuser, $this->context->password);

		if (!$link)
			return ""; $this->context->getErrorMessage('bReport_noconnection',
				$this->context->dbuser,$this->context->host,mysql_error());

		if (!mysql_select_db($this->context->database, $link)) {
			mysql_close($link);
			return $this->context->getErrorMessage('bReport_nodb');
		}
		$result = mysql_query($sql, $link);

		#
		# Check that the record set is open
		#
		if ($result) {
			if (mysql_num_rows($result) > 0) {
				$output=$this->renderHTML($result);
			} else {
				$output=$this->renderNoResultsHTML();
			}

			mysql_free_result($result);
		} else {
			return $this->context->getErrorMessage('bReport_sqlerror',$sql);
		}

		mysql_close($link);

		if ($this->context->debug) {
			$output.="<div>SQL = ".$sql."</div>";
		}
		return $output;
	}

	/**
	 * Extract options from a blob of text
	 *
	 * @param recordset
	 * @return rendered markup
	 */
	public function renderHTML($result) {
		$this->output = "";
		$nRows=mysql_num_rows($result);

		if ($this->get('heading')) {
			$this->output.="<h1>".$this->get('heading')."</h1>";
		}
		# Table start
	    $this->output .= "<table class=\"bugzilla";
		if ($this->get('bar')) {
			$this->output.=" bz_bar";
		}
		$this->output.="\">";

		# Initialise details row logic
		$detailsRowColumns=array();
		$arrayOfDetailRowColumns=explode(",",$this->get('detailsrow'));
		foreach ($arrayOfDetailRowColumns as $detailRowColumn) {
			$detailsRowColumns[$detailRowColumn]=1;
		}
		$this->numberOfMainRowColumns=0;
		if ($this->get('bar')) {
			$this->numberOfMainRowColumns=2;
		} else {
			foreach ($this->getColumns() as $column) {
				if (!array_key_exists($column,$detailsRowColumns)) {
					$this->numberOfMainRowColumns++;
				}
			}
		}

		# Display table header
		if (!$this->get('bar') &&
				$this->get('format')!='list' &&
				$this->get('headers')!="hide") {
	    	$this->output .= "<tr>";
			foreach ($this->getColumns() as $column) {
				$name=trim($column);
				if (array_key_exists($column,$this->columnName)) {
					$name=$this->columnName[$column];
				}
				if (($column=="summary") && ($nRows > 1)) {
					$name.=" ($nRows tasks)";
				}
	      		$this->output .= "<th><b>$name</b></th>";
	    	}
	    	$this->output .= "</tr>";
		}

	    # Create Table Data Rows
		$even=true;
		$count=0;
		$localMaxRows=$this->getMaxRows();
		$localMaxRowsForBarChart=$this->getMaxRowsForBarChart();
		$groupValue="";
		$groupTotal=0;
		$groups;
		$doGrouping=0;
		if ($this->getGroup()) {
			$doGrouping=1;
			$groups=split(",",$this->getGroup());
		}
		$barArray=$this->getBarArray();
		$currentId=0;
		$currentBlocksId=0;
		$currentDependsId=0;
	    while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
			#
			# Add group heading
			#
			if ($doGrouping) {
				$groupTotal++;
				$newGroupValue=$this->formatForHeading(
					$line[$this->mapField(
						$groups[0])],$groups[0]);
				if ($newGroupValue != $groupValue) {
					$colspan=$this->numberOfMainRowColumns;
					if ($this->get('bar')) {
						if ($groupValue) {
							$this->renderBar($barArray);
							foreach (array_keys($barArray) as $key) {
								$barArray[$key]=0;
							}
						}
						$colspan-=1;
					}
					$groupValue=$newGroupValue;
					$this->output.="<tr class=\"bz_group\"><td colspan=\""
						.$colspan.
						"\">".$groupValue.
						"</td>";
					if ($this->get('bar')) {
						$this->output.="<td class=\"total\">&nbsp;</td>";
					}
					$this->output.="</tr>";
					$groupTotal=0;
				}
			}
			++$count;
			# Bar counter
			if ($this->get('bar')) {
				if ($count > $localMaxRowsForBarChart) {
					$this->context->warn("Bar count greater than max allowed ".
						"$count > $localMaxRowsForBarChart");
					break;
				}
				$barArray[$line[$this->mapField($this->get('bar'))]]+=1;
				continue;
			}
			# Safety check break out of loop if there's too much
			if ($count > $localMaxRows) {
				$this->context->warn("Report truncated - count greater than max allowed ".
					"$count > $localMaxRows");
				break;
			}
			#
			# Only render the row if the ID has changed from previous row
			# to support LEFT JOINS
			#
		if ($line["id"] != $currentId) {
			$currentId=$line["id"];
			$firstcolumn=true;
			$even=!$even;
			$class="bz_bug ".$line["priority"]." ".$line["bug_status"]." ";
		 	if ($even) {
				$class.="bz_row_even";
			} else {
				$class.="bz_row_odd";
			}
	    	$this->output .= "<tr class=\"".$class."\">";
			if ($this->get('format') == "list") {
				$this->output.="<td class=\"bz_list\" colspan=\"".
					$this->numberOfMainRowColumns."\">";
					$this->output.=
						"[".$this->format($line[$this->mapField('to')],
						'to')."] ";
					$deadline=$line[$this->mapField('deadline')];
					if ($deadline) {
						$this->output.=
							"(".$this->format($deadline,"deadline").") ";
					}
					$this->output.=
						$this->format($line[$this->mapField('summary')],
						'summary')." (#".
						$this->format($line[$this->mapField('id')],
						'id').")";
				$this->output.="</td>";
			} else {
				foreach ($this->getColumns() as $column) {
					$dbColumn=$this->mapField($column);
					if (!$this->get('detailsrow')
							or !array_key_exists($dbColumn,
								$detailsRowColumns)) {
						$title=$this->getValueTitle($line,$column);
						$value=$this->format($line[$dbColumn],$column,$title);
						$this->output.="<td";
						if ($title) {
							$this->output.=" title=\"$title\"";
						}
						$this->output.=">$value";
						if ($firstcolumn) {
							$firstcolumn=false;
							/**
							 * Start with a carriage return so that comments starting with
							 * list characters, e.g. *, # render in wiki style
							 */
							if ($this->get('lastcomment')) {
								$lastcomment=trim(htmlentities($line["thetext"], ENT_QUOTES, 'UTF-8'));
								if (strlen($lastcomment) > 0) {
									$this->output.="*<div class=\"bz_comment\"><span class=\"bug_id\">".
										$line["bug_id"]."</span>\n".$lastcomment.
										"</div>";
								}
							}
							#
							# Render quick flag
							#
							if ($this->isRequired('quickflag')) {
								if ($line["flagdate"]) {
									$this->output.="<span class=\"flag\" title=\"Flag : ".
										$line["flagdate"]."\">?</span>";
								}
							}
						}
						$this->output.="</td>";
					}
				}
			}
			$this->output .= "</tr>";
		}
			if ($this->get('detailsrow')) {
				$this->renderDetailsRow($detailsRowColumns,$line,
					$this->get('detailsrowprepend'));
			}
			#
			# We have LEFT JOIN so we need to ignore repeats
			#
			if ($this->get('blocks') &&
					($line["blocks"] != $currentBlocksId)) {
				$currentBlocksId=$line["blocks"];
				$this->renderDetailsRow(
				$this->blocksRowColumns,$line,"&rArr; ");
			}
			#
			# We have LEFT JOIN so we need to ignore repeats
			#
			if ($this->get('depends') &&
					($line["depends"] != $currentDependsId)) {
				$currentDependsId=$line["depends"];
				$this->renderDetailsRow(
				$this->dependsRowColumns,$line,"&lArr; ");
			}
		}

		#
		# Display bar
		#
		if ($this->get('bar')) {
			$this->renderBar($barArray);
			$this->output.="<tr class=\"bz_bar_total\"><td colspan=\""
				.($this->numberOfMainRowColumns-1).
				"\">total</td><td class=\"total\">$nRows</td></tr>";
		}

		# Table end
		$this->output .= "</table>";

		return $this->output;
	}

	#
	# Render a details row
	#
	private function renderDetailsRow($array,$line,$prepend) {
		$details="";
		$title="";
		foreach (array_keys($array) as $column) {
			$dbColumn=$this->mapField($column);
			if ($line[$column]) {
				$valueTitle=$this->getValueTitle($line,$column);
				if ($valueTitle) {
					$this->context->debug &&
						$this->context
							->debug("Setting title for $column to $valueTitle");
				}
				switch ($array[$column]) {
					case "title" :
						$title.=$this
							->format($line[$dbColumn],
								$column,$valueTitle)." ";
						break;
					default :
						$details.=$this
							->format($line[$dbColumn],
								$column,$valueTitle)." ";
				}
			}
		}
		if (trim($details)) {
			$this->output.="<tr class=\"bz_details\"><td title=\"$title\" class=\"bz_details\" colspan=\"".
			$this->numberOfMainRowColumns."\">";
			$this->output.=$prepend.$details;
			$this->ouput.="</td></tr>";
		}
	}

	private function renderBar($barArray) {
		$total=0;
		$nonZeroKeyCount=0;
		$arrayKeys=array_keys($barArray);
		foreach ($arrayKeys as $key) {
			$total+=$barArray[$key];
			$barArray[$key]>0 && $nonZeroKeyCount++;
		}
		$classColourCode="few";
		$keyCount=count($arrayKeys);
		if ($keyCount > 6) {
			$classColourCode="some";
		} else if ($keyCount > 10) {
			$classColourCode="many";
		}
		$this->output.="<tr class=\"bz_bar $classColourCode\"><td colspan=\""
			.($this->numberOfMainRowColumns-1).
			"\">";
		$i=0;
		$iNonZero=0;
		sort($arrayKeys);
		$rowTotal=0;
		$rowWidthTotal=0;
		foreach ($arrayKeys as $key) {
			$count=$barArray[$key];
			$i++;
			if ($count > 0) {
				$iNonZero++;
				$widthString="";
				$rowTotal+=$count;
				if ($iNonZero==$nonZeroKeyCount) {
					$class="C$i last";
					# Make sure we don't get caught by rounding error
					$width=100-$rowWidthTotal;
				} else {
					$width=number_format((100*$count)/$total,0);
					$rowWidthTotal+=$width;
					$class="C$i notlast";
				}
				$content;
				if (strlen($key) > ($width /2) ) {
					$content="&nbsp;";
				} else {
					$keyAndCount="$key ($count)";
					if (strlen($keyAndCount) < ($width /2) ) {
						$content=$keyAndCount;
					} else {
						$content=$key;
					}
				}
				$widthString=" style=\"width:".$width."%\"";
				$this->output.="<div title=\"$key = $count";
				if ($this->context->debug) {
					$this->output.="class=$class / width=$width /  key count=$keyCount / nz key count=$nonZeroKeyCount / i=$i / inz=$iNonZero";
				}
				$this->output.=
					"\"$widthString class=\"$class\"><div>".
					"$content</div></div>";
			}
		}
		$this->output.="</td><td class=\"total\">$rowTotal</td></tr>";
	}

	/**
	 * Render output for no results
	 */
	public function renderNoResultsHTML() {
		if ($this->get('noresultsmessage')) {
			return "<div class=\"bz_noresults\">".
				$this->get('noresultsmessage')."</div>";
		} else {
			return "";
		}
	}

	public function getMatchExpression($match,$name,$negate) {
		$timmedMatch=trim($match);
		$pos=strpos($timmedMatch, "!");
		if ($pos === false) {
			if ($negate) {
				return $name."<>'".$timmedMatch."'";
			} else {
				return $name."='".$timmedMatch."'";
			}
		} else if ($pos==0) {
			if ($negate) {
				return $name."='".substr($timmedMatch,1)."'";
			} else {
				return $name."<>'".substr($timmedMatch,1)."'";
			}
		} else {
			$this->context->debug && $this->context->warn("The '!' operator must be the first character -> ignoring $name=$match");
		}
	}

	#
	# Initialise the bar array
	#
	private function getBarArray() {
		$barArray=array();
		if (array_key_exists($this->get('bar'),$this->fieldValues)) {
			foreach (split(",",
					$this->fieldValues[$this->get('bar')]) as $key) {
				$barArray[$key]=0;
			}
			$this->context->debug &&
				$this->context->debug("Initialised bar keys (".
					$this->get('bar')."): ".
					join(",",$arrayKeys));
		}
		return $barArray;
	}
}
