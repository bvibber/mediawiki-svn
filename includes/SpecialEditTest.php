<?php

class SpecialEditTest extends SpecialPage
{
	var $selfTitle, $skin;
	function __construct()
	{
		global $wgUser;
		parent::__construct("EditTest");
		$this->selfTitle = Title::makeTitleSafe(NS_SPECIAL, "EditTest");
		$this->skin = $wgUser->getSkin();
	}

	function execute()
	{
		global $wgRequest, $wgOut, $wgContLang, $wgUser;
		$this->setHeaders();
		$wgOut->setPageTitle("API action=edit test form");
		$wgOut->addHTML($this->getForm());
	}
	
	function buildTableRow($row1, $row2)
	{
		$retval = Xml::openElement('tr');
		$retval .= Xml::openElement('td');
		$retval .= $row1;
		$retval .= Xml::closeElement('td');
		$retval .= Xml::openElement('td');
		$retval .= $row2;
		$retval .= Xml::closeElement('td');
		$retval .= Xml::closeElement('tr');
		return $retval;
	}
	
	function buildInputLine($label, $name)
	{
		return $this->buildTableRow(
			Xml::label($label, $name),
			Xml::input($name, false, false, array('id' => $name))
		);
	}
	
	function buildCheckBoxLine($label, $name)
	{
		return $this->buildTableRow(
			Xml::label($label, $name),
			Xml::check($name, false, array('id' => $name))
		);
	}
	
	function getForm()
	{
		# TODO: Write wrapper script using FauxRequest
		global $wgScriptPath, $wgCookiePrefix;
		$retval = Xml::openElement('form', array('action' => "$wgScriptPath/api.php", 'method' => "POST"));
		$retval .= Xml::hidden('action', 'edit');

		$retval .= Xml::openElement('table');
		$retval .= $this->buildInputLine('Page title', 'title');
		$retval .= $this->buildInputLine('Edit comment', 'summary');
		$retval .= $this->buildInputLine('Edit token', 'token');
		$retval .= $this->buildInputLine('Base timestamp', 'basetimestamp');
		$retval .= $this->buildCheckBoxLine('Minor edit', 'minor');
		$retval .= $this->buildCheckBoxLine('Bot edit', 'bot');
		$retval .= $this->buildCheckBoxLine('Recreate page if deleted in the meantime', 'recreate');
		$retval .= $this->buildTableRow(Xml::label('Article content', 'text'),
						Xml::openElement('textarea', array(
								'name' => 'text',
								'id' => 'text',
								'rows' => 10,
								'cols' => 50)) .
						Xml::closeElement('textarea')
		);
		$retval .= $this->buildInputLine('CAPTCHA ID', 'captchaid');
		$retval .= $this->buildInputLine('CAPTCHA answer', 'captchaword');
		$retval .= Xml::closeElement('table');
		$retval .= Xml::submitButton("Submit");
		$retval .= Xml::closeElement('form');
		return $retval;
	}
}
