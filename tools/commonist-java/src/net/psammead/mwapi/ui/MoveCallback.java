package net.psammead.mwapi.ui;

/** callback for the PageMove action */
public interface MoveCallback {
	/** when true, the file overwites another file */
	boolean ignoreDeleteAndMoveText();
}
