<?php
/**
 * Contians the Special pages classes
 * 
 * @package MediaWiki
 * @subpackage EditCount
 */

/**
 * The EditCount special page class
 * 
 * @package MediaWiki
 * @subpackage EditCount
 */
class EditCountPage extends SpecialPage {
	/**
	 * the border style applied to table, td, and th elements
	 * 
	 * @var string
	 * @access private
	 */
	var $border = "border: solid 2px;";
	
	/**
	 * the target user
	 * 
	 * @var string
	 * @access protected
	 */
	var $target;
	
	/**
	 * Creates the special page class
	 * 
	 * @access public
	 */
	function EditCountPage()
	{
		parent::SpecialPage("EditCount", "", true);
		wfLoadExtensionMessages( 'mw-editcount' );
	}
	
	/**
	 * execute and outputs the special page
	 * 
	 * @param string $par the user that was entered into the form
	 * @access public
	 */
	function execute($par = null) 
	{
		global $wgRequest, $wgOut;
		wfProfileIn(__METHOD__);
		$this->target = ($par !== null) ? $par : $wgRequest->getVal("target");
		
		if ($this->target === null || !strlen($this->target)) {
			$this->showInputForm();
			wfProfileOut(__METHOD__);
			return;
		}
		
		$this->showInputForm($this->target);
		
		$nt = Title::newFromURL($this->target);
		if (!$nt) {
			$wgOut->addWikiMsg("editcount-notuser", $this->target));
			wfProfileOut(__METHOD__);
			return;
		}
		
		$ec = new EditCount($nt->getText());
		$this->showEditCount($ec);
		
		wfProfileOut(__METHOD__);
	}
	
	/**
	 * adds the HTML for the EditCount of a user
	 * 
	 * @param EditCount $ec the EditCount object to be shown
	 * @access private
	 */
	function showEditCount($ec)
	{
		global $wgOut, $wgContLang;
		
		wfProfileIn(__METHOD__);
		
		if ($ec->getTotal() == 0) {
			$wgOut->addWikiMsg("editcount-noedits", $ec->getName()));
			wfProfileOut(__METHOD__);
			return;
		}
		
		$table = "";
		$table .= wfElement("table", array("style" => "$this->border text-align: center; margin-left: 25%; margin-right: auto; margin-top: 7px; border-collapse: collapse"), null, null);
		$table .= wfElement("tr", array("style" => $this->border), null);
		$table .= wfElement("th", array("style" => $this->border), wfMsg("editcount-namespace"));
		$table .= wfElement("th", array("style" => $this->border), wfMsg("editcount-edits"));
		$table .= wfElement("th", array("style" => $this->border), wfMsg("editcount-percent"));
		$table .= wfCloseElement("tr");
		
		$totalEC = $ec->getTotal();
		$nsCount = $ec->getNamspaces();
		$nsNames = $wgContLang->getFormattedNamespaces();
		$nsNames[0] = wfMsg("blanknamespace");
		
		foreach ($nsCount as $nsInt => $nsEC) {
			$table .= $this->doRow(array(
				$nsNames[$nsInt],
				$nsEC,
				round(($nsEC/$totalEC*100), 1) . wfMsg("editcount-percentsym"))
				);
		}

		$table .= wfElement("tr", null, null);
		$table .= wfElement("td", array("style" => "font-weight: bold; {$this->border}"), wfMsg("editcount-total"));
		$table .= wfElement("td", array("colspan" => "2", "style" => $this->border), $totalEC);
		$table .= wfCloseElement("tr");
		
		$table .= wfCloseElement("table");
		$wgOut->addHtml($table);
		
		wfProfileOut(__METHOD__);
	}
	
	/**
	 * creates a HTML table row
	 * 
	 * @param array $cells an array of the cells' content
	 * @return string the created HTML
	 * @access private
	 */
	function doRow($cells)
	{
		wfProfileIn(__METHOD__);
		$ret = wfElement("tr", null, null);
		if (count($cells) == 2) {
			$ret .= wfElement("td", array("style" => $this->border), $cells[0]);
			$ret .= wfElement("td", array("style" => $this->border, "colspan" => "2"), $cells[1]);
		}
		else {
			for ($i = 0; $i < 3; ++$i) {
				$ret .= wfElement("td", array("style" => $this->border), $cells[$i]);
			}
		}
		$ret .= wfCloseElement("tr");
		
		wfProfileOut(__METHOD__);
		return $ret;
	}
	
	/**
	 * shows the HTML for the input form
	 * 
	 * @param string $user (optional) the text to put as a default in the textbox
	 * @access private
	 */
	function showInputForm($user = "") {
		global $wgOut, $wgScriptPath;
		
		wfProfileIn(__METHOD__);
		
		$ct = $this->getTitle();
		$form = "";
		// FIXME: use Xml::inputLabel etc
		$form .= wfElement("p", null, wfMsg("editcount-des"));
		$form .= wfElement("form", array("name" => "editcountform", "method" => "get", "action" => $wgScriptPath . "/index.php"), null); 
		$form .= wfElement("input", array("type" => "hidden", "name" => "title", "value" => "Special:EditCount"), "") . " ";
		$form .= wfElement("label", array("for" => "target"), wfMsg("editcount-username")). " ";
		$form .= wfElement("input", array("type" => "textbox", "name" => "target", "size" => "24", "value" => $user), "") . " ";
		$form .= wfElement("input", array("type" => "submit", "name" => "doeditcount", "value" => wfMsg("editcount-show")));
		$form .= wfCloseElement("form");
		
		$this->setHeaders();
		$wgOut->addHtml($form);
		
		wfProfileOut(__METHOD__);
	}
}

/**
 * Represents the edit count of a user
 * 
 * @package MediaWiki
 * @subpackage EditCount
 */
class EditCount {
	/**
	 * The database object
	 * 
	 * @var Database
	 * @access private
	 */
	var $db;
	
	/**
	 * The user id
	 * 
	 * @var int
	 * @access private
	 */
	var $id;
	
	/**
	 * the user object for the user this EditCount object represents
	 * 
	 * @var User
	 * @access private
	 */
	var $user;
	
	/**
	 * Creates an EditCount object with a username or ip
	 * 
	 * @var string $username the user name or IP to create from
	 * @access public
	 */
	function EditCount($username)
	{
		$this->db = wfGetDB(DB_SLAVE);
		$this->user = User::newFromName($username);
		if ($this->user == null) {
			//FIXME: HUH?
			$this->user = new User;
		}
		$this->id = $this->user->getID();
	}
	
	/**
	 * Gets the number of edits in a namespace
	 * 
	 * @param int $ns the namespace number
	 * @return int the number of edits in the given namespace
	 * @access public
	 */
	function getByNamespace($ns)
	{
		wfProfileIn(__METHOD__);
		global $wgDBprefix;
		//FIXME: don't construct SQL by hand, use selectField
		$cond = ($this->user->isAnon()) ? "r.rev_user_text = '" . $this->user->getName() . "'"
			: "r.rev_user = " . $this->id;
		$result = $this->db->query("SELECT COUNT(*) AS count
			FROM {$wgDBprefix}page p JOIN {$wgDBprefix}revision r ON p.page_id = r.rev_page
			WHERE $cond AND p.page_namespace = $ns
			GROUP BY p.page_namespace");
		$row = $this->db->fetchRow($result);
		
		wfProfileOut(__METHOD__);
		return ($row["count"]) ? $row["count"] : 0;
	}
	
	/**
	 * Returns an array of the edits per namespace
	 * 
	 * @access public
	 * @return int
	 */
	function getNamspaces()
	{
		wfProfileIn(__METHOD__);
		global $wgContLang, $wgDBprefix;
		$cond = ($this->user->isAnon()) ? "rev_user_text = '" . $this->user->getName() . "'" 
			: "rev_user = " . $this->id;
		$result = $this->db->query("SELECT page_namespace as ns, COUNT(*) as count
			FROM {$wgDBprefix}revision JOIN {$wgDBprefix}page p ON rev_page = p.page_id
			WHERE $cond GROUP BY ns", __METHOD__);
		$nsResults = array();

		// Use foreach
		while (($row = $this->db->fetchRow($result)) !== false) {
			$nsResults[$row["ns"]] = $row["count"];
		}
		
		$nsNumbers = array_keys($wgContLang->getNamespaces());
		foreach ($nsNumbers as $nsNum) {
			if (!array_key_exists($nsNum, $nsResults) && $nsNum >= 0) {
				$nsResults[$nsNum] = 0;
			}
		}
		ksort($nsResults, SORT_NUMERIC);
		
		wfProfileOut(__METHOD__);
		return $nsResults;
	}
	
	/**
	 * Gets the total edits for the user
	 * 
	 * @access public
	 * @return int
	 */
	function getTotal()
	{
		wfProfileIn(__METHOD__);
		global $wgDBprefix;
		if ($this->id == 0) {
			wfProfileOut(__METHOD__);
			// This actually works?
			return $this->db->selectField("{$wgDBprefix}revision", "COUNT(*)", array("rev_user_text" => $this->user->getName()), __METHOD__);
		}
		
		wfProfileOut(__METHOD__);
		return $this->user->edits($this->id);
	}
	
	/**
	 * Gets the username
	 * 
	 * @access public
	 * @return string
	 */
	function getName()
	{
		return $this->user->getName();
	}
}

