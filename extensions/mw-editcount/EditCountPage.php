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
	function __construct()
	{
		parent::__construct("EditCount", "", true);
		wfLoadExtensionMessages( 'mw-editcount' );
	}
	
	/**
	 * execute and outputs the special page
	 * 
	 * @param string $par the user that was entered into the form
	 * @access public
	 */
	function execute($par) 
	{
		global $wgRequest, $wgOut;
		wfProfileIn(__METHOD__);
		$this->target = $wgRequest->getVal("target", isset($par) ? $par : null);
		
		if ($this->target === null || !strlen($this->target)) {
			$this->showInputForm();
			wfProfileOut(__METHOD__);
			return;
		}
		
		$this->showInputForm($this->target);
		
		$nt = Title::newFromText($this->target);
		if (!$nt) {
			$wgOut->addWikiMsg("editcount-notuser", $this->target);
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
			$wgOut->addWikiMsg("editcount-noedits", $ec->getName());
			wfProfileOut(__METHOD__);
			return;
		}
		
		$table = "";
		$table .= Html::openElement("table", array("style" => "{$this->border} text-align: center; margin-left: 25%; margin-right: auto; margin-top: 7px; border-collapse: collapse;"));
		$table .= Html::openElement("tr", array("style" => $this->border));
		$table .= Html::element("th", array("style" => $this->border), wfMsg("editcount-namespace"));
		$table .= Html::element("th", array("style" => $this->border), wfMsg("editcount-edits"));
		$table .= Html::element("th", array("style" => $this->border), wfMsg("editcount-percent"));
		$table .= Xml::closeElement("tr");
		
		$totalEC = $ec->getTotal();
		$nsCount = $ec->getNamspaces();
		$nsNames = $wgContLang->getFormattedNamespaces();
		$nsNames[0] = wfMsg("blanknamespace");
		
		foreach ($nsCount as $nsInt => $nsEC) {
			$table .= $this->doRow(array(
				$nsNames[$nsInt],
				$nsEC,
				round(($nsEC/$totalEC*100), 1) . wfMsg("editcount-percentsym")
			));
		}

		$table .= Html::openElement("tr", null, null);
		$table .= Html::element("td", array("style" => "font-weight: bold; {$this->border}"), wfMsg("editcount-total"));
		$table .= Html::element("td", array("colspan" => "2", "style" => $this->border), $totalEC);
		$table .= Xml::closeElement("tr");
		
		$table .= Xml::closeElement("table");
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
		$ret = Html::openElement("tr", null, null);
		if (count($cells) == 2) {
			$ret .= Html::element("td", array("style" => $this->border), $cells[0]);
			$ret .= Html::element("td", array("style" => $this->border, "colspan" => "2"), $cells[1]);
		}
		else {
			for ($i = 0; $i < 3; ++$i) {
				$ret .= Html::element("td", array("style" => $this->border), $cells[$i]);
			}
		}
		$ret .= Xml::closeElement("tr");
		
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
		global $wgOut, $wgScript;
		
		wfProfileIn(__METHOD__);
		
		$ct = $this->getTitle();
		$form = "";
		// FIXME: use Xml::inputLabel etc
		$form .= Html::element("p", null, wfMsg("editcount-des"));
		$form .= Html::openElement("form", array("name" => "editcountform", "method" => "get", "action" => $wgScript));
		$form .= Html::hidden("title", "Special:EditCount") . " ";
		$form .= Html::element("label", array("for" => "target"), wfMsg("editcount-username")). " ";
		$form .= Html::input("target", $user, "textbox", array("size" => "24")) . " ";
		$form .= Html::input("doeditcount", wfMsg("editcount-show"), "submit");
		$form .= Xml::closeElement("form");
		
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
	function __construct($username)
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
		$conds = array("page_namespace" => $ns);
		if ( $this->user->isAnon() )
			$conds["rev_user_text"] = $this->user->getName();
		else
			$conds["rev_user"] = $this->id;
		
		$row = $this->db->selectRow("page", "COUNT(*) AS count",
			$conds, __METHOD__,
			array("GROUP BY" => "page_namespace"),
			array("revision" => array("JOIN", "page_id = rev_page"));
		
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
		global $wgContLang;
		$conds = array();
		if ( $this->user->isAnon() )
			$conds["rev_user_text"] = $this->user->getName();
		else
			$conds["rev_user"] = $this->id;
		
		$res = $this->db->select(array("revision", "page"), array("page_namespace", "COUNT(*) as count"),
			$conds, __METHOD__,
			array("GROUP BY" => "page_namespace"),
			array("page" => array("JOIN", "rev_page = page_id")));
		$nsResults = array();
		
		foreach ($res as $row) {
		//while (($row = $this->db->fetchRow($result)) !== false) {
			//$nsResults[$row["ns"]] = $row["count"];
			$nsResults[$row->page_namespace] = $row->count;
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
		if ($this->id == 0) {
			wfProfileOut(__METHOD__);
			// This actually works?
			return $this->db->selectField("revision", "COUNT(*)", array("rev_user_text" => $this->user->getName()), __METHOD__);
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

