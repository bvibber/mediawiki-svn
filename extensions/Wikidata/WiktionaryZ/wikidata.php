<?php
function addWikiDataBlock($id, $title, $content, $expanded) {
	global
		$wgOut;
	
	if (!$expanded) {
		$style = ' style="display: none;"';
		$character = '+';
	}
	else {
		$style = '';
		$character = '&ndash;';
	}
		
 	$wgOut->addHTML('<div class="wiki-data-block">'.
					'<h3 id="collapse-'. $id .'" class="toggle" onclick="toggle(this, event);">'. $character . ' '. $title . '</h3>' .
					'<div id="collapsable-'. $id . '"'. $style .'>' . $content . '</div>' .
					'</div>');
}

interface PageElement {
	public function getId();
	public function getCaption();
	public function getRelation();
	public function getViewer();
	public function getEditor();
	public function isExpanded();
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
	protected $isExpanded;
	
	public function __construct($id, $caption, $relation, $viewer, $editor, $isExpanded) {
		$this->id = $id;
		$this->caption = $caption;
		$this->relation = $relation;
		$this->viewer = $viewer;
		$this->editor = $editor;
		$this->isExpanded = $isExpanded;
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

	public function isExpanded() {
		return $this->isExpanded;
	}
}

?>
