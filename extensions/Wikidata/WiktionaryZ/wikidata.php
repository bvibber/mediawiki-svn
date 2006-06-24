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
	public function getRelation();
	public function getDisplayRelation();
	public function allowAdd();
	public function allowRemove();
	public function repeatInput();
	public function getController();
}

interface PageElementController {
	public function add($tuple);
	public function remove($tuple);
	public function update($tuple, $tupleUpdate);
}

class DefaultPageElement implements PageElement {
	protected $id;
	protected $caption;
	protected $relation;
	protected $displayRelation;
	protected $allowAdd;
	protected $allowRemove;
	protected $updatableHeading;
	protected $inputRow;
	protected $repeatInput;
	protected $controller;
	
	public function __construct($id, $caption, $relation, $displayRelation, $allowAdd, $allowRemove, $updatableHeading, $repeatInput, $controller) {
		$this->id = $id;
		$this->caption = $caption;
		$this->relation = $relation;
		$this->displayRelation = $displayRelation;
		$this->allowAdd = $allowAdd;
		$this->allowRemove = $allowRemove;
		$this->updatableHeading = $updatableHeading;
		$this->repeatInput = $repeatInput;
		$this->controller = $controller;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getCaption() {
		return $this->caption;
	}
	
	public function getRelation() {
		return $this->relation;
	}
	
	public function getDisplayRelation() {
		return $this->displayRelation;
	}
	
	public function allowAdd() {
		return $this->allowAdd;
	}
	
	public function allowRemove() {
		return $this->allowRemove;
	}
	
	public function updatableHeading() {
		return $this->updatableHeading;	
	}
	
	public function repeatInput() {
		return $this->repeatInput;
	}
	
	public function getController() {
		return $this->controller;
	}
}

?>
