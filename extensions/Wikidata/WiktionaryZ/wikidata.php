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
	public function getViewer();
	public function getEditor();
}

interface PageElementController {
	public function add($keyPath, $tuple);
	public function remove($keyPath);
	public function update($keyPath, $tuple);
}

class DefaultPageElement implements PageElement {
	protected $id;
	protected $caption;
	protected $relation;
	protected $viewer;
	protected $editor;
	
	public function __construct($id, $caption, $relation, $viewer, $editor) {
		$this->id = $id;
		$this->caption = $caption;
		$this->relation = $relation;
		$this->viewer = $viewer;
		$this->editor = $editor;
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
	
	public function getViewer() {
		return $this->viewer;
	}
	
	public function getEditor() {
		return $this->editor;
	}
}

?>
