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
	public function allowAdd();
	public function allowRemove();
	public function repeatInput();
	public function getController();
}

interface PageElementController {
	public function add($values);
	public function remove($tuple);
	public function update($tuple, $updatedValues);
}

class DefaultPageElement implements PageElement {
	protected $id;
	protected $caption;
	protected $relationModel;
	protected $allowAdd;
	protected $allowRemove;
	protected $updatableHeading;
	protected $inputRow;
	protected $repeatInput;
	protected $controller;
	
	public function __construct($id, $caption, $relationModel, $allowAdd, $allowRemove, $updatableHeading, $repeatInput, $controller) {
		$this->id = $id;
		$this->caption = $caption;
		$this->relationModel = $relationModel;
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
	
	public function getRelationModel() {
		return $this->relationModel;
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
