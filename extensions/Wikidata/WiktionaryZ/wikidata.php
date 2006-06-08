<?php
function addWikiDataBlock($title, $content) {
	global
		$wgOut;
	
 	$wgOut->addHTML('<div class="wiki-data-block">
					<h4>'. $title . '</h4>'.
					$content .
					'</div>');
}

interface PageElement {
	public function getId();
	public function getCaption();
	public function getRelationModel();
	public function allowRemove();
	public function repeatInput();
	public function getController();
}

interface PageElementController {
	public function add($values);
	public function remove($tuple);
}

class DefaultPageElement implements PageElement {
	public $id;
	public $caption;
	public $relationModel;
	public $allowRemove;
	public $inputRow;
	public $repeatInput;
	public $controller;
	
	public function __construct($id, $caption, $relationModel, $allowRemove, $repeatInput, $controller) {
		$this->id = $id;
		$this->caption = $caption;
		$this->relationModel = $relationModel;
		$this->allowRemove = $allowRemove;
		$this->repeatInput = $repeatInput;
		$this->controller = $controller;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function getRelationModel() {
		return $this->relationModel;
	}
	
	public function allowRemove() {
		return $this->allowRemove;
	}
	
	public function repeatInput() {
		return $this->repeatInput;
	}
	
	public function getController() {
		return $this->controller;
	}
}

?>
