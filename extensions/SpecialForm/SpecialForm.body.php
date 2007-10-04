<?php
/**
 * SpecialForm.php -- Use a form-based interface to start new articles
 * Copyright 2007 Vinismo, Inc. (http://vinismo.com/)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@vinismo.com>
 * @addtogroup Extensions
 */

if (!defined('MEDIAWIKI')) {
	exit( 1 );
}

require_once('XmlFunctions.php');

class SpecialForm extends SpecialPage
{
	function SpecialForm() {
		SpecialPage::SpecialPage("Form");
		self::loadMessages();
	}

	function execute( $par ) {
		global $wgRequest, $wgOut;

		if (!$par) {
			$wgOut->showErrorPage('formnoname', 'formnonametext');
			return;
		}

		$form = $this->loadForm($par);

		if (!$form) {
			$wgOut->showErrorPage('formbadname', 'formbadnametext');
			return;
		}

		if ($wgRequest->wasPosted()) {
			$this->createArticle($form);
		} else {
			$this->showForm($form);
		}
	}

	function loadMessages() {
		static $messagesLoaded = false;
		global $wgMessageCache;

		if ( $messagesLoaded ) return;

		require( dirname( __FILE__ ) . '/SpecialForm.i18n.php' );
		foreach ( $SpecialFormMessages as $lang => $langMessages ) {
			$wgMessageCache->addMessages( $langMessages, $lang );
		}

		$messagesLoaded = true;

		return true;
	}

	function loadForm($name) {
		$nt = Title::makeTitleSafe(NS_MEDIAWIKI, wfMsg('formpattern', $name));

		# article exists?

		if (!$nt || $nt->getArticleID() == 0) {
			return NULL;
		}

		$article = new Article($nt);

		assert($article);

		$text = $article->getContent(true);

		return new Form($name, $text);
	}

	function showForm($form, $errmsg = NULL) {
		global $wgOut, $wgRequest, $wgParser, $wgTitle;

		$self = SpecialPage::getTitleFor("Form/$form->name");

		$wgOut->setPageTitle($form->title);

		if (!is_null($form->instructions)) {

			$wgOut->addHtml(wfOpenElement('div', array('class' => 'instructions')) .
							$wgOut->parse($form->instructions) .
							wfCloseElement('div') .
							wfElement('br'));
		}

		if (!is_null($errmsg)) {
			$wgOut->addHtml(wfOpenElement('div', array('class' => 'error')) .
							$wgOut->parse($errmsg) .
							wfCloseElement('div') .
							wfElement('br'));
		}
		
		$wgOut->addHtml(wfOpenElement('form',
									  array('method' => 'POST',
											'action' => $self->getLocalURL())));

		foreach ($form->fields as $field) {
			$wgOut->addHtml($field->render($wgRequest->getText($field->name)) . wfElement('br') . "\n");
		}

		$wgOut->addHtml(wfElement('input', array('type' => 'submit',
												 'value' => wfMsg('formsave'))));

		$wgOut->addHtml(wfCloseElement('form'));
	}

	function createArticle($form) {

		global $wgOut, $wgRequest, $wgLang;
		
		# Check for required fields

		$missedFields = array();
		
		foreach ($form->fields as $name => $field) {
			$value = $wgRequest->getText($name);
			if ($field->isOptionTrue('required') && (is_null($value) || strlen($value) == 0)) {
				$missedFields[] = $field->label;
			}
		}

		# On error, show the form again with some error text.
		
		if ($missedFields) {
			if (count($missedFields) > 1) {
				$msg = wfMsg('formrequiredfieldpluralerror', $wgLang->listToText($missedFields));
			} else {
				$msg = wfMsg('formrequiredfielderror', $missedFields[0]);
			}
			$this->showForm($form, $msg);
			return;
		}
		
		$title = $this->makeTitle($form);

		wfDebug("SpecialForm: saving article '$title'\n");

		$nt = Title::newFromText($title);

		if (!$nt) {
			$wgOut->showErrorPage('formbadpagename', 'formbadpagenametext', array($title));
			return;
		}
		
		if ($nt->getArticleID() != 0) {
			$wgOut->showErrorPage('formarticleexists', 'formarticleexists', array($title));
			return;
		}

		$text = "{{subst:$form->template";

		foreach ($form->fields as $name => $field) {
			# FIXME: strip/escape template-related chars (|, =, }})
			$text .= "|$name=" . $wgRequest->getText($name);
		}

		$text .= "}}";

		$article = new Article($nt);

		if ($article->doEdit($text, wfMsg('formsavesummary', $form->name), EDIT_NEW)) {
			$wgOut->redirect($nt->getFullURL());
		} else {
			$wgOut->showErrorPage('formsaveerror', 'formsaveerrortext', array($title));
		}
	}

	function makeTitle($form) {
		global $wgRequest;

		$title = $form->namePattern;

		foreach ($form->fields as $name => $field) {
			$title = preg_replace("/{{\{$name\}}}/", $wgRequest->getText($name), $title);
		}

		return $title;
	}

}

class Form {
	var $name;
	var $title;
	var $template;
	var $instructions;
	var $fields;
	var $namePattern;

	function Form($name, $text) {

		$this->name = $name;
		$this->title = wfMsg('formtitlepattern', $name);
		$this->template = wfMsg('formtemplatepattern', $name);

		$this->fields = array();
		$this->namePattern = NULL;
		$this->instructions = NULL;

		# XXX: may be some faster ways to do this

		$lines = explode("\n", $text);

		foreach ($lines as $line) {

			if (preg_match('/^(\w+)=(.*)$/', $line, $matches)) {
				if (strcasecmp($matches[1], 'template') == 0) {
					$this->template = $matches[2];
				} else if (strcasecmp($matches[1], 'title') == 0) {
					$this->title = $matches[2];
				} else if (strcasecmp($matches[1], 'namePattern') == 0) {
					$this->namePattern = $matches[2];
				} else if (strcasecmp($matches[1], 'instructions') == 0) {
					$this->instructions = $matches[2];
					wfDebug("Got instructions: '" . $this->instructions . "'.\n");
				} else {
					wfDebug("SpecialForm: unknown form attribute '$matches[1]'; skipping.\n");
				}
			} else if (preg_match('/^(\w+)\|([^\|]+)\|(\w+)(\|([^\|]+)(\|(.*))?)?$/', $line, $matches)) {
				# XXX: build an inheritance tree for different kinds of fields
				$field = new FormField();
				$field->setName($matches[1]);
				$field->setLabel($matches[2]);
				$field->setFieldType($matches[3]);
				if ($matches[4]) {
					$field->setDescription($matches[5]);
					if ($matches[6]) {
						$rawOptions = explode(',', $matches[7]);
						foreach ($rawOptions as $rawOption) {
							if (preg_match('/^(\w+)=(.+)/', $rawOption, $optMatches)) {
								$field->setOption($optMatches[1], $optMatches[2]);
							} else {
								wfDebug("SpecialForm: unrecognized form field option: '$rawOption'; skipping.\n");
							}
						}
					}
				}
				$this->fields[$field->name] = $field;
			} else {
					wfDebug("SpecialForm: unrecognized form line: '$line'; skipping.\n");
			}
		}
	}
}

class FormField {

	var $name;
	var $type;
	var $label;
	var $description;
	var $options;

	function FormField() {
		$this->name = NULL;
		$this->type = NULL;
		$this->label = NULL;
		$this->description = NULL;
		$this->options = array();
	}

	function setName($name) {
		$this->name = $name;
	}

	function setFieldType($type) {
		$this->type = $type;
	}

	function setLabel($label) {
		$this->label = $label;
	}

	function setDescription($description) {
		$this->description = $description;
	}

	function setOption($key, $value) {
		$this->options[$key] = $value;
	}

	function getOption($key, $default = NULL) {
		if (array_key_exists($key, $this->options)) {
			return $this->options[$key];
		} else {
			return $default;
		}
	}

	function isOptionTrue($key, $default = false) {
		$value = $this->getOption($key, $default);
		return ((strcasecmp($value, 'on') == 0) ||
				(strcasecmp($value, 'yes') == 0) ||
				(strcasecmp($value, 'true') == 0) ||
				(strcasecmp($value, '1') == 0));
	}
	
	function render($def = NULL) {
		global $wgOut;

		switch ($this->type) {
		 case 'textarea':
			return wfOpenElement('h2') .
			  wfElement('label', array('for' => $this->name), $this->label) .
		      wfCloseElement('h2') .
			  (($this->description) ?
			   (wfOpenElement('div') . $wgOut->parse($this->description) . wfCloseElement('div')) : '') .
			  wfOpenElement('textarea', array('name' => $this->name,
											  'id' => $this->name,
											  'rows' => $this->getOption('rows', 6),
											  'cols' => $this->getOption('cols', 80))) .
			  ((is_null($def)) ? '' : $def) .
			  wfCloseElement('textarea');
			break;
		 case 'text':
			return wfElement('label', array('for' => $this->name), $this->label) . ": " .
			  wfElement('input', array('type' => 'text',
									   'name' => $this->name,
									   'id' => $this->name,
									   'value' => ((is_null($def)) ? '' : $def),
									   'size' => $this->getOption('size', 30)));
			break;
		 case 'checkbox':
			$attrs = array('type' => 'checkbox',
						   'name' => $this->name,
						   'id' => $this->name);
			if ($def == 'checked') {
				$attrs['checked'] = 'checked';
			}
			return wfElement('label', array('for' => $this->name), $this->label) . ": " .
			  wfElement('input', $attrs);
			break;
		 case 'radio':
			$items = array();
			$rawitems = explode(';', $this->getOption('items'));
			foreach ($rawitems as $item) {
				$attrs = array('type' => 'radio',
							   'name' => $this->name,
							   'value' => $item);
				if ($item == $def) {
					$attrs['checked'] = 'checked';
				}
				$items[] = wfOpenElement('input', $attrs) .
				  wfElement('label', null, $item) .
				  wfCloseElement('input');
			}
			return wfElement('span', null, $this->label) . wfElement('br') . implode("", $items);
			break;
		 case 'select':
			$items = array();
			$rawitems = explode(';', $this->getOption('items'));
			foreach ($rawitems as $item) {
				$items[] = wfElement('option',
									 ($item == $def) ? array('selected' => 'selected') : null,
									 $item);
			}

			return wfElement('label', array('for' => $this->name), $this->label) . ": " .
			  wfOpenElement('select', array('name' => $this->name, 'id' => $this->name)) .
			  implode("", $items) .
		      wfCloseElement('select');

			break;
		 default:
			wfDebug("SpecialForm: unknown form field type '$this->type', skipping.\n");
			return '';
		}
	}
}

?>