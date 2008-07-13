<?php
/**
 * A general SQL query
 */
abstract class BSQLQuery {
	protected $context;

	#
	# Parameter values
	#
	private $parameters=array();

	#
	# Arbitary cached values for private use
	#
	private $bsql_cache=array();

	#
	# Date long time in the future, useful for sorting purposes
	# when null mapped to this
	#
	var $futureDate='2100-01-01';
	#
	# Work out what db data is required and record in
	# $fieldsRequired array so we can optimise the SQL
	# ... no point in wasting energy
	#

	var $fieldsRequired=array();

	# Cached array of columns that we actually want to render
	var $columnsToRender;

	# Number of columns in a report
	var $numberOfMainRowColumns;

	# Columns implicitly removed or added, note that explict setting
	# overrides this
	private $implicityAddedColumns=array();
	private $implicityRemovedColumns=array();

	# Columns implicitly sort and order, note that explict setting
	# overrides this
	var $implicitParameters=array();

	# Cached versions so we only calculate once
	var $cache=array();

	abstract protected function getFormats();
	abstract protected function getDefaultSort();

	#
	# Override this to map sort value to appropriate SQL
	#
	protected function getSortMapping($column) {
		if (array_key_exists($column,$this->sortMapping)) {
			return $this->sortMapping[$column];
		} else {
			return $column;
		}
	}
	protected function setSortMapping($column,$mapping) {
		$this->sortMapping[$column]=$mapping;
	}

	public function setContext($context) {
		$this->context=$context;
	}

	#
	# Set parameter value
	#
	public function set($name,$value) {
		if (array_key_exists($name,$this->supportedParameters)) {
			switch ($this->supportedParameters[$name]) {
				case 'field' :
					$this->parameters[$name]=
						$this->tidyCommaSeparated($value);
					break;
				case 'columns' :
					$this->parameters[$name]=
						$this->tidyCommaSeparated($value);
					break;
				default:
					$this->parameters[$name]=$value;
			}
			$this->context->debug &&
				$this->context->debug("BSQLQuery parameter set $name=".
					$this->parameters[$name]);
		} else {
			$this->context->warn("Setting parameter $name is not supported");
		}
	}

	#
	# Tidy a field value, which essentially means removing the spaces next
	# to the columns
	#
	private function tidyCommaSeparated($value) {
		$newValue;
		foreach (split(",",$value) as $singleValue) {
			if (!isset($newValue)) {
				$newValue=trim($singleValue);
			} else {
				$newValue.=",".trim($singleValue);
			}
		}
		return $newValue;
	}

	#
	# Return supported regex for each parameter
	#
	public function getParameterRegex($name) {
		$regex;
		switch ($this->supportedParameters[$name]) {
			case 'column' :
				$regex="/^[\w,+-]*$/";
				break;
			case 'columns' :
				$regex="/^[\w,\s+-]*$/";
				break;
			case 'field-date' :
				$regex="/^[\*+-]*$/";
				break;
			case 'free' :
				$regex="/^.*$/";
				break;
			default :
				if (substr($this->supportedParameters[$name],0,5)
						== "field") {
					$regex="/^[\w,@\.\s\*!()+-]*$/";
				} else {
					$regex="/^[\w]*$/";
				}
		}
		return $regex;
	}

	#
	# Set implicit parameter value
	#
	protected function setImplicit($name,$value) {
		if (array_key_exists($name,$this->supportedParameters)) {
			$this->implicitParameters[$name]=$value;
		} else {
			$this->context->warn("Setting parameter $name is not supported");
		}
	}

	#
	# Get parameter value
	#
	protected function get($name) {
		if (array_key_exists($name,$this->supportedParameters)) {
			if (array_key_exists($name,$this->parameters)) {
				return $this->parameters[$name];
			} else {
				if (array_key_exists($name,$this->defaultParameters)) {
					return $this->defaultParameters[$name];
				} else {
					return NULL;
				}
			}
		} else {
			$this->context->warn("Getting parameter $name is not supported");
			return NULL;
		}
	}

	#
	# Get implicit parameter value
	#
	protected function getImplicit($name) {
		if (array_key_exists($name,$this->supportedParameters)) {
			if (array_key_exists($name,$this->implicitParameters)) {
				return $this->implicitParameters[$name];
			} else {
				return NULL;
			}
		} else {
			$this->context->warn(
				"Getting implicit parameter $name is not supported");
			return NULL;
		}
	}

	#
	# Get explicit parameter value
	#
	protected function getExplicit($name) {
		if (array_key_exists($name,$this->supportedParameters)) {
			if (array_key_exists($name,$this->parameters)) {
				return $this->parameters[$name];
			} else {
				return NULL;
			}
		} else {
			$this->context->warn(
				"Getting explicit parameter $name is not supported");
			return NULL;
		}
	}

	#
	# Get default parameter value
	#
	protected function getDefault($name) {
		if (array_key_exists($name,$this->supportedParameters)) {
			if (array_key_exists($name,$this->defaultParameters)) {
				return $this->defaultParameters[$name];
			} else {
				return NULL;
			}
		} else {
			$this->context->warn(
				"Getting default parameter $name is not supported");
			return NULL;
		}
	}

	#
	# Identify a field as required
	#
	public function requireField($column) {
		$this->context->debug &&
			$this->context
				->debug("Field required : ".$column);
		$this->fieldsRequired[$column]=1;
	}

	#
	# Determine whether a field is required
	#
	public function isRequired($column) {
		if (array_key_exists($column,$this->fieldsRequired)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	#
	# Convert date to nice words
	#
	private function getRadarFormat($value) {
		if (!$this->bsql_cache["today"]) {
			$this->bsql_cache["yesterday"]=date("Y-m-d",strtotime("-1 day"));
			$this->bsql_cache["today"]=date("Y-m-d");
			$this->bsql_cache["tomorrow"]=date("Y-m-d",strtotime("+1 day"));
			$this->bsql_cache["thisweek"]=date("Y-W");
			$this->bsql_cache["nextweek"]=date("Y-W",strtotime("+1 week"));
			$this->bsql_cache["thismonth"]=date("Y-m");
			$this->bsql_cache["nextmonth"]=date("Y-m",strtotime("+1 month"));
			$this->bsql_cache["thisyear"]=date("Y");
			$this->bsql_cache["nextyear"]=date("Y",strtotime("+1 year"));
		}
		if (date("Y-m-d",$value) == $this->bsql_cache["yesterday"]) {
			return "yesterday";
		} else if ($value < (time()-86400)) {
			return "overdue";
		} else if (date("Y-m-d",$value) == $this->bsql_cache["today"]) {
			return "today";
		} else if (date("Y-m-d",$value) == $this->bsql_cache["tomorrow"]) {
			return "tomorrow";
		} else if (date("Y-W",$value) == $this->bsql_cache["thisweek"]) {
			return "this week";
		} else if (date("Y-W",$value) == $this->bsql_cache["nextweek"]) {
			return "next week";
		} else if (date("Y-m",$value) == $this->bsql_cache["thismonth"]) {
			return "this month";
		} else if (date("Y-m",$value) == $this->bsql_cache["nextmonth"]) {
			return "next month";
		} else if (date("Y",$value) == $this->bsql_cache["thisyear"]) {
			return "this year";
		} else if (date("Y",$value) == $this->bsql_cache["nextyear"]) {
			return "next year";
		} else {
			return "years away";
		}
	}

	private function formatForExplicitFormat($value,$format,$title) {
		switch ($format) {
			case "date" :
			    if ($value) {
					$time=strtotime($value);
					if ($time == strtotime($this->futureDate)) {
						return "";
					} else {
						$formattedDate=date("Y-m-d",strtotime($value));
					}
					return $formattedDate;
				} else {
					return "";
				}
			case "radar" :
				if ($value) {
					$time=strtotime($value);
					if ($time == strtotime($this->futureDate)) {
						return "";
					} else {
						$formattedDate=
							$this->getRadarFormat(strtotime($value));
					}
					return $formattedDate;
				} else {
					return "";
				}
			case "url" :
				if ($value) {
					return "[$value]";
				} else {
					return "&nbsp;";
				}
			case "id" :
				# Render as interwiki or external link
				if ($value) {
					$text;
					if ($title) {
						$flag="";
						if ($title!=$value) {
							$flag="<span class=\"flag\">+</span>";
						}
						$text="<span title=\"$title\">$value$flag</span>";
					} else {
						$text=$value;
					}
					if ($this->context->interwiki) {
						return "[[".$this->context->interwiki.
							":".$value.
							"|".$text."]]";
					} else {
						return "[".$this->context->bzserver.
							"/show_bug.cgi?id=".$value.
							" ".$text."]";
					}
				} else {
					return "&nbsp;";
				}
			case "name" :
				if ($value) {
					if ($this->get("nameformat") == "tla") {
						return $this->convertNameToTla($value);
					} else {
						return $value;
					}
				} else {
					return "&nbsp;";
				}
			default :
				$this->context->warn("Format ".
					$format.
					" not recognised");
				return $value;
		}
	}


	/**
	 * Convert a name to a TLA
	 */

	public function convertNameToTla($value) {
		$names=split(" ",$value);
		$tla;
		if (sizeof($names) ==1) {
			if (strlen($value) > 3) {
				$tla=substring($value,0,3);
			} else {
				$tla=$value;
			}
		} else {
			if (strlen($names[1]) > 1) {
				$tla=substr($names[0],0,1).substr($names[1],0,2);
			} else if (strlen($name[0] > 1)) {
				$tla=substr($names[0],0,2).substr($names[1],0,1);
			} else {
				$tla=$names[0].$names[1]."A";
			}
		}
		return strtoupper($tla);
	}
	/**
	 * Format a value
	 */
	public function format($value,$column,$title) {
		$formats=$this->getFormats();
		if (array_key_exists($column,$formats)) {
			return $this->formatForExplicitFormat($value,
				$formats[$column],$title);
		} else {
			return $value;
		}
	}

	/**
	 * Get a title for a given value
	 */
	public function getValueTitle($line,$column) {
		$title="";
		if (array_key_exists($column,$this->valueTitle)) {
			$columns=split(",",$this->valueTitle[$column]);
			foreach ($columns as $column) {
				$title.=$line[$column]." ";
			}
			$title=trim($title);
		}
		return $title;
	}


	#
	# Formatting with null mapped to a string, useful for headings
	#
	public function formatForHeading($value,$column) {
		if ($this->get('groupformat')) {
			$this->context->debug &&
				$this->context
					->debug("Group format set : ".$this->get('groupformat'));
			$value=$this->formatForExplicitFormat($value,
				$this->get('groupformat'));
		} else {
			$value=$this->format($value,$column);
		}
		if ($value) {
			return $value;
		} else {
			return "not set";
		}
	}

	/**
	 * Get a where clause from a named field matching a comma separated list
	 */
	public function getWhereClause($match,$name) {
		$this->context->debug &&
			$this->context->debug("Generating where clause for $name=$match");
		if (preg_match("/[\*+-]/",$match)) {
			return $this->getWhereClauseSpecial($match,$name);
		} else if (strstr($match,",")) {
			$pos=strpos($match,"!(");
			$where=" and (";
			$operator;
			$negate;
			if ($pos===false) {
				$operator="OR";
				$negate=false;
			} else {
				$match=substr($match,2,-1);
				$operator="AND";
				$negate=true;
			}
			$first=true;
			foreach (explode(",", $match) as $value) {
				if ($first) {
					$first=false;
				} else {
					$where.=" $operator ";
				}
				$where.=$this->getMatchExpression($value,$name,$negate);
			}
			$where.=") ";
			return $where;
		} else {
			return " and ".$this->getMatchExpression($match,$name,false);
		}
	}

	/**
	 * Get a int where clause
	 * ... very simple for now
	 */
	public function getIntWhereClause($match,$name) {
		if (preg_match("/[\*+-]/",$match)) {
			switch ($match) {
				case "+":
					return " and $name > 0";
				default:
					$this->context->warn("Int match not recognised $name=$match");
			}
		} else {
			$this->context->warn("Int match not recognised $name=$match");
			return "";
		}
	}

	/**
	 * Get a date where clause
	 * ... very simple for now
	 */
	public function getDateWhereClause($match,$name) {
		if (preg_match("/[\*+-]/",$match)) {
			return $this->getWhereClauseSpecial($match,$name);
		} else {
			$this->context->warn("Date match not recognised $name=$match");
			return "";
		}
	}

	public function getWhereClauseSpecial($match,$name) {
		switch ($match) {
			case "+":
				return " and $name IS NOT NULL";
			case "-":
				return " and $name IS NULL";
			case "*":
				return "";
			default:
				$this->context->warn("Special match not recognised $name=$match");
				return "";
		}
		return " and $name IS NOT NULL";
	}

	/**
	  * Get maximum number of rows
	  */
	public function getMaxRows() {
		if ($this->get('maxrows')) {
			if ($this->get('maxrows') > $this->context->maxrowsFromConfig) {
				$this->context->warn("Max rows in function parameter greater than in config -> ignoring");
				return $this->context->maxrowsFromConfig;
			} else {
				return $this->get('maxrows');
			}
		} else {
			return $this->context->maxrowsFromConfig;
		}
	}

	/**
	  * Get maximum number of rows
	  */
	public function getMaxRowsForBarChart() {
		if ($this->get('maxrowsbar')) {
			if ($this->get('maxrowsbar') > $this->context->maxrowsForBarChartFromConfig) {
				$this->context->warn("Max rows bar in function parameter greater than in config -> ignoring");
				return $this->context->maxrowsForBarChartFromConfig;
			} else {
				return $this->get('maxrowsbar');
			}
		} else {
			return $this->context->maxrowsForBarChartFromConfig;
		}
	}

	/**
	  * Get sort
	  */
	public function getSort() {
		# Not explicit on function call or notcached
		if (!array_key_exists('sort',$this->cache)) {
			$sort;
			if ($this->getExplicit('sort')) {
				$sort=$this->getExplicit('sort');
			} else if ($this->getImplicit('sort')) {
				# Implicit on usage and other function call parameters
				$sort=$this->getImplicit('sort');
			} else {
				# Default behaviour
				$sort=$this->getDefault('sort');
			}
			#
			# Prepend with group (if set)
			#
			if ($this->getGroup()) {
				$sort=$this->getGroup().",".$sort;
			}
			$this->context->debug &&
				$this->context->debug("Sort set to $sort");
			$this->cache['sort']=$sort;
		}
		return $this->cache['sort'];
	}

	#
	# Return sort with mapping
	#
	public function getMappedSort() {
		$mappedSort=array();
		foreach(explode(",",$this->getSort()) as $column) {
			array_push($mappedSort,$this->getSortMapping($column));
		}
		return join(",",$mappedSort);
	}
	/**
	  * Get order
	  */
	public function getOrder() {
		if (!array_key_exists('order',$this->cache)) {
			$order;
			if ($this->getExplicit('order')) {
				$order=$this->getExplicit('order');
			} else if ($this->getImplicit('order')) {
				$order=$this->getImplicit('order');
			} else {
				$order=$this->getDefault('order');
			}
			if ($order == "desc") {
				$this->cache['order']="DESC";
			} else {
				$this->cache['order']="ASC";
			}
			$this->context->debug &&
				$this->context->debug("Order set to ".$this->cache['order']);
		}
		return $this->cache['order'];
	}

	/**
	  * Get group
	  */
	public function getGroup() {
		if (!array_key_exists('group',$this->cache)) {
			$group;
			if ($this->getExplicit('group')) {
				# Explicit on function call
				$group=$this->getExplicit('group');
			} else if ($this->getImplicit('group')) {
				# Implicit on usage and other function call parameters
				$group=$this->getImplicit('group');
			} else {
				# Default behaviour is nothing
				$group=FALSE;
			}
			$this->cache['group']=$group;
		}
		return $this->cache['group'];
	}

	public function implictlyRemoveColumn($column) {
		$this->implicityRemovedColumns[$column]=$column;
		$this->context->debug("Registering column for implicit removal : ".
			$column);
	}

	public function implictlyAddColumn($column) {
		$this->implicityAddedColumns[$column]=$column;
		$this->context->debug("Registering column for implicit addition : ".
			$column);
	}

	/**
	  * Get columns
	  */
	public function getColumns() {
		if ($this->columnsToRender) {
			return $this->columnsToRender;
		}
		if ($this->getExplicit('columns')) {
			if (preg_match("/^([+-])(.*)$/",
					$this->getExplicit('columns'), $array)) {
				$this->context->debug &&
					$this->context->
						debug("Adjusting columns (comma separated) : ".
							$array[1].":".$array[2]);
				$newColumns=explode(",",$this->getDefault('columns'));
				$deltaColumns=explode(",",$array[2]);
				$defaultOperation=$array[1];
				foreach ($deltaColumns as $deltaColumn) {
					$newColumn=$deltaColumn;
					$operation=$defaultOperation;
					/**
					 * Support operations on subsequent columns (not just the first)
					 */
					if (preg_match("/^([+-])(.*)$/",
							$deltaColumn, $pregDeltaColumn)) {
						$operation=$pregDeltaColumn[1];
						$newColumn=$pregDeltaColumn[2];
					}
					$this->context->debug &&
						$this->context->
							debug("Adjusting columns (single string): ".
							$operation.":".$newColumn);
					/**
					 * Add or remove column
					 */
					if ($operation == "+") {
						$this->context->debug &&
							$this->context->
								debug("Adding column [$newColumn]");
						array_push($newColumns,$newColumn);
						if (array_key_exists($newColumn,
								$this->implicityRemovedColumns)) {
							$this->context->debug &&
								$this->context->
									debug("Removing implicit removal of column : $newColumn");
							unset($this->implicityRemovedColumns[$newColumn]);
						}
					} else if ($operation == "-") {
						$found=-1;
						$i=0;
						foreach ($newColumns as $search) {
							if ($search==$newColumn) {
								$found=$i;
								break;
							}
							$i++;
						}
						if ($found > -1) {
							$this->context->debug &&
							 	$this->context->debug("Removing column [$newColumn,$found]");
							unset($newColumns[$found]);
							if (array_key_exists($newColumn,
								$this->implicityAddedColumns)) {
								unset($this
									->implicityAddedColumns[$newColumn]);
							}
						} else {
							$this->context
								->warn("Can't remove column [$newColumn] it doesn't exists");
						}
					} else {
							$this->context
								->warn("Operation not recognised in column "
								.$operation);
					}
				}
				/**
				 * We may have removed values from the columns so we need to
				 * recreate array by calling array_values function
				 */
				$this->columnsToRender=$this->
					applyImplicitColumns(array_values($newColumns));
				$this->context->debug &&
					$this->context->debug("Columns to display adjusted to "
						.join(",",$this->columnsToRender));
			} else {
				$this->context->debug &&
					$this->context->debug("Columns explicitly set to ".
						$this->get('columns'));
				# Explicit columns - so don't apply implicit rules
				$this->columnsToRender=explode(",",
					$this->getExplicit('columns'));
			}
		} else {
			$this->columnsToRender=$this->
				applyImplicitColumns(explode(",",
					$this->getDefault('columns')));
			$this->context->debug &&
				$this->context->debug("Columns set to default "
					.join(",",$this->columnsToRender));
		}
		return $this->columnsToRender;
	}

	#
	# Apply implicit column rules
	#
	private function applyImplicitColumns($columns) {
		$newColumns=$this->array_fill_keys(array_merge(
			$columns,
			array_keys($this->implicityAddedColumns)
		));
		$this->context->debug &&
			$this->context->debug("Implictly adding column "
				.join(",",array_keys($this->implicityAddedColumns)));
		foreach ($this->implicityRemovedColumns as $column) {
			if (array_key_exists($column,$newColumns)) {
				$this->context->debug &&
					$this->context->
						debug("Implictly removing column ".$column);
				unset($newColumns[$column]);
			}
		}
		return array_keys($newColumns);
	}

	#
	# Local implementation of array_fill_keys since it's only supported
	# from php 5.2
	#
	private function array_fill_keys($array,$value=NULL) {
		$newArray=array();
		foreach ($array as $key) {
			if ($value) {
				$newArray[$key]=$value;
			} else {
				$newArray[$key]=$key;
			}
		}
		return $newArray;
	}

	#
	# Initialisation prior to generating the SQL
	#
	protected function preSQLGenerate() {
		#
		# Process sort variable and add implicit columns
		#
		foreach (explode(",",$this->getSort()) as $column) {
			$this->implictlyAddColumn($column);
		}
		#
		# But remove group
		#
		foreach (explode(",",$this->getGroup()) as $column) {
			$this->implictlyRemoveColumn($column);
			# Although we still need the field in the SQL
			$this->requireField($column);
		}
		#
		# Require fields listed in columns
		#
		foreach ($this->getColumns() as $column) {
			$this->requireField($column);
		}

		#
		# Require the bar field
		#
		if ($this->get('bar')) {
			$this->context->debug &&
				$this->context->debug("Requiring bar field ".
					$this->get('bar'));
			$this->requireField($this->get('bar'));
		}
	}

	public function mapField($column) {
		if (array_key_exists($column,$this->fieldMapping)) {
			return $this->fieldMapping[$column];
		} else {
			return $column;
		}
	}
}
