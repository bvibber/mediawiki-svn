<?php

class TableModException extends Exception {}

class TableMod {

	var $input;
	var $args;
	var $parser;
	var $frame;

	var $id;
	
	var $index_column;
	var $index_actions = array('sort');
	
	var $table = array('rows' => array());
	public static $sort_direction; 
	public static $sort_key; 
	public static $sort_col; 
	
	var $output = "";
	
	function __construct($input, $args, $parser, $frame) {
		$this->input = trim($input);	
		$this->args = $args;	
		$this->parser = $parser;	
		$this->frame = $frame;	
		
		if (!isset($args['id']))
			throw new TableModException(wfMsg('tablemod-error-missingid'));
		$this->id = $args['id'];

		$this->index_column = isset($args['index']) ? $args['index'] : 0;
		if (isset($args['actions'])) {
			$this->index_actions = explode(',', preg_replace('/ /', '', $args['actions']));
			$this->index_actions = array_flip($this->index_actions);	
		}
			
		TableMod::$sort_direction = 'asc';
		TableMod::$sort_key = 0;
		
		$this->prepareInput();

		global $tablemodAction;
		if ($tablemodAction[0] == $this->id) {
			$this->performActions();
		}

		$this->generateTable();

	}

	private function prepareInput() {
		$lines = preg_split('/\\n/', $this->input);
		
		$lines_count = count($lines);
		if (strpos(trim($lines[0]), '{|') === FALSE || trim($lines[$lines_count-1]) != '|}') {
			throw new TableModException(wfMsg('tablemod-error-format'));
		}

		$row_index = 0;
		foreach ($lines as $line) {
			if (strpos($line, '|-') === 0) {
				$row_index++;
				$this->table['rows'][$row_index] = array();
			} elseif (strpos($line, '{|') === 0 && !isset($this->table['start'])) {
				$this->table['start'] = ltrim($line, '{|');
			} elseif (strpos($line, '|}') === 0 && !isset($this->table['end'])) {
				$this->table['end'] = rtrim($line, '|}');
			} elseif (strpos($line, '{|') !== FALSE && isset($this->table['start'])) {
				throw new TableModException(wfMsg('tablemod-error-format'));
			} elseif (strpos($line, '!') === 0) {
				if (!isset($this->table['headers']))
					$this->table['headers'] = array();
				$raw_line = ltrim($line, '!');
				if (count($matches = explode('!!', $raw_line)) > 0)
					foreach ($matches as $match)
						$this->table['headers'][] = $match;
				else
					$this->table['headers'][] = $raw_line;
			} elseif (strpos($line, '|+') === 0) {
				$this->table['caption'] = ltrim($line, '|+');
			} elseif (strpos($line, '|') === 0) {
				$raw_line = ltrim($line, '|');
				$matches = array();
				if (count($matches = explode('||', $raw_line)) > 0)
					foreach ($matches as $match)
						$this->table['rows'][$row_index][] = $match;
				else
					$this->table['rows'][$row_index][] = $raw_line;
			}
		}

		$this->table['rows'] = array_values($this->table['rows']);

		$row_cols = count($this->table['rows'][0]);
		
		if (isset($this->index_actions['remove']))
			foreach ($this->table['rows'] as $row) {
				if ($row_cols != count($row))
					throw new TableModException(wfMsg('tablemod-error-colcount'));
			}
		
		if (isset($this->index_actions['sort'])) {
			foreach ($this->table['headers'] as $row) {
				if (strpos('colspan=', $row) !== FALSE)
					throw new TableModException(wfMsg('tablemod-error-headcount'));
			}
			if ( isset($tablemodAction[1]) && 
			     $tablemodAction[1] == 'sort' && 
			     (!is_numeric($tablemodAction[2]) || 
			      $tablemodAction[2] > $row_cols || 
			      $tablemodAction[2] < 0) )
				throw new TableModException(wfMsg('tablemod-error-invalidsort'));
		}
		
		if ($this->index_column == 0 ) {
			foreach ($this->table['rows'] as &$row) {
				$row[$row_cols] = '';
			}
		}
	}
	
	private function performActions() {
		global $tablemodAction, $tablemodContentChanged;
		if ($tablemodAction[1] == 'remove' && isset($this->index_actions['remove'])) {
			if ($this->index_column === 0)
				unset($this->table['rows'][$tablemodAction[2]]);
			else {
				foreach ($this->table['rows'] as $rowid=>$row) {
					if (trim(preg_replace('/.*\|\s*/', '', $row[$this->index_column-1])) == $tablemodAction[2]) {
						unset($this->table['rows'][$rowid]);
					}
				}
			}
			$tablemodContentChanged = TRUE;
		} elseif ($tablemodAction[1] == 'sort' && isset($this->index_actions['sort'])) {
			TableMod::$sort_key = isset($tablemodAction[2]) && $tablemodAction[2] > 0 && $tablemodAction[2] <= count($this->table['headers']) ? $tablemodAction[2] : 0;
			TableMod::$sort_direction = isset($tablemodAction[3]) && $tablemodAction[3] == 'asc' ? 'asc' : 'desc';
		
			if ($tablemodAction[2] == 0)
				$this->table['rows'] = array_reverse($this->table['rows']);
			else {
				$numericCompare = true;
				TableMod::$sort_col = count($this->table['rows'][0]);
				foreach ($this->table['rows'] as &$row) {
					$colval = trim(preg_replace('/.*\|\s*/', '', $row[TableMod::$sort_key-1]));
					$row[TableMod::$sort_col] = $colval;
					if (!is_numeric($colval)) {
						$numericCompare = false;
					}
				}

				if ($numericCompare)
					usort($this->table['rows'], array('TableMod', 'doRowCompare'));
				else
					usort($this->table['rows'], array('TableMod', 'doRowCompareString'));
					
				foreach ($this->table['rows'] as &$row)
					unset($row[TableMod::$sort_col]);
				
				
			}
			$tablemodContentChanged = TRUE;
		}
	}

	private static function doRowCompare($row_a, $row_b) {
		global $tablemodAction;
		if ((float)$row_a[TableMod::$sort_col] == (float)$row_b[TableMod::$sort_col]) return 0;
		elseif (TableMod::$sort_direction == 'asc')
			return ((float)$row_a[TableMod::$sort_col] < (float)$row_b[TableMod::$sort_col]) ? -1 : 1;
		else
			return ((float)$row_a[TableMod::$sort_col] > (float)$row_b[TableMod::$sort_col]) ? -1 : 1;
	}

	private static function doRowCompareString($row_a, $row_b) {
		global $tablemodAction;
		if ($row_a[TableMod::$sort_col] == $row_b[TableMod::$sort_col]) return 0;
		elseif (TableMod::$sort_direction == 'asc')
			return ($row_a[TableMod::$sort_col] < $row_b[TableMod::$sort_col]) ? -1 : 1;
		else
			return ($row_a[TableMod::$sort_col] > $row_b[TableMod::$sort_col]) ? -1 : 1;
	}

	public function doParse($input) {
		global $wgVersion;
		$matches = array();

		if (preg_match ('/([0-9]*)\.([0-9]*).*/', $wgVersion, $matches) == 1 && 
		    $matches[1] == 1 && 
		    $matches[2] > 15) {
			return $this->parser->recursiveTagParse($input, $this->frame);
		} else {
			return $this->parser->recursiveTagParse($input);
		}
	}

	private function generateTable() {
		$this->output = '{|'.$this->table['start']."\n";
		if (isset($this->table['caption']))
			$this->output .= '|+'.$this->table['caption']."\n";

		if (isset($this->table['headers']))
			foreach ($this->table['headers'] as $key=>$header)
				$this->output .= "!$header >>H:$key<<\n";

		foreach ($this->table['rows'] as $rowkey=>$row) {
			$this->output .= "|-\n";

			foreach ($row as $key=>$col)
				$this->output .= "|$col >>R:$rowkey:$key<<\n";
		}

		$this->output .= '>>EMPTY<<'.$this->table['end']."|}";
	}

	public function tableSave() {
		global $tablemodContent;
		$output = preg_replace(array('/>>H:\d*<</', '/>>R:\d*:\d*<</', '/>>EMPTY<</'), '', $this->output);
			
		$article_content = $tablemodContent;
		$start_tag = array();
		preg_match('/\<table-mod [^>]*?id="'.$this->id.'"[^>]*?>[^{]*/', $tablemodContent, $start_tag);
		$tablemodContent = str_replace($start_tag[0].$this->input, $start_tag[0].$output, $tablemodContent);
	}

	public function tableOutput() {
		global $wgTitle;

		$output = $this->output;

		if (($colspan = count($this->table['rows'])) == 0) {
			$colspan = isset($this->table['headers']) ? count($this->table['headers']) : 1;
			for ($key = 0; $key < $colspan; $key++)
				$output = str_replace(">>H:$key<<", '', $output);
			$output = str_replace(">>EMPTY<<", "\n|-\n| colspan=\"$colspan\" | ".wfMsg('tablemod-error-tableempty')."\n", $output);
		} else {

			$output = str_replace(">>EMPTY<<", '', $output);

			if (isset($this->index_actions['sort'])) {
				if (isset($this->table['headers'])) {
					foreach ($this->table['headers'] as $key=>$header)
						$output = str_replace(">>H:$key<<", '<span class="plainlinks">['.$wgTitle->getFullURL(array('tablemod'=>$this->id.'|sort|'.($key+1).'|asc')).' &uarr;]</span><span class="plainlinks">['.$wgTitle->getFullURL(array('tablemod'=>$this->id.'|sort|'.($key+1).'|desc')).' &darr;]</span>', $output);
				}			
			}

			if (isset($this->index_actions['remove'])) {
				$removeMsg = wfMsg('tablemod-msg-remove');
				for ($key = 0; $key < $colspan; $key++)
					if ($key != $this->index_column-1)		
						$output = preg_replace("/>>R:\d*:$key<</", '', $output);
				
				$key = $this->index_column-1;
				foreach ($this->table['rows'] as $rowkey=>$row) {
					$colval = trim(preg_replace('/.*\|\s*/', '', $row[$key]));
					$output = str_replace(">>R:$rowkey:$key<<", '<span class="plainlinks">['.$wgTitle->getFullURL(array('tablemod'=>$this->id.'|remove|'.$colval))." $removeMsg]</span>", $output);
				}
			}

		}
	
		return $this->doParse($output);

	}
}

