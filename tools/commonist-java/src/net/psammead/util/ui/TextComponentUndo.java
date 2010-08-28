package net.psammead.util.ui;

import java.awt.event.ActionEvent;
import java.beans.PropertyChangeEvent;
import java.beans.PropertyChangeListener;

import javax.swing.AbstractAction;
import javax.swing.ActionMap;
import javax.swing.InputMap;
import javax.swing.KeyStroke;
import javax.swing.event.UndoableEditEvent;
import javax.swing.event.UndoableEditListener;
import javax.swing.text.Document;
import javax.swing.text.JTextComponent;
import javax.swing.undo.CannotRedoException;
import javax.swing.undo.CannotUndoException;
import javax.swing.undo.UndoManager;

import net.psammead.util.Logger;

/** adds undo to a {@link JTextComponent} */
public final class TextComponentUndo {
	private static final Logger	log	= new Logger(TextComponentUndo.class);
	
	private static final KeyStroke	REDO_KEY_STROKE	= KeyStroke.getKeyStroke("control Y");
	private static final KeyStroke	UNDO_KEY_STROKE	= KeyStroke.getKeyStroke("control Z");
	private static final String		REDO_ACTION		= "Redo";
	private static final String		UNDO_ACTION		= "Undo";
	private static final int		DEFAULT_LIMIT	= 100;
	
	private final UndoManager 				undoManager;
	private final PropertyChangeListener	propertyListener;
	private final UndoableEditListener		undoableEditListener;
	private final AbstractAction			undoAction;
	private final AbstractAction			redoAction;

	public TextComponentUndo() {
		undoManager	= new UndoManager();
		undoManager.setLimit(DEFAULT_LIMIT);

		propertyListener	= new PropertyChangeListener() {
			public void propertyChange(PropertyChangeEvent ev) {
				if (!"document".equals(ev.getPropertyName()))	return;
				final Document oldDocument	= (Document)ev.getOldValue();
				final Document newDocument	= (Document)ev.getNewValue();
				oldDocument.removeUndoableEditListener(undoableEditListener);
				newDocument.addUndoableEditListener(undoableEditListener);
			}
		};
		
		undoableEditListener = new UndoableEditListener() {
			public void undoableEditHappened(UndoableEditEvent ev) {
				undoManager.addEdit(ev.getEdit());
			}
		};

		undoAction = new AbstractAction() {
			public void actionPerformed(ActionEvent ev) {
				try {
					if (undoManager.canUndo())	undoManager.undo();
				} 
				catch (CannotUndoException e) {
					log.error("undo failed", e);
				}
			}
		};

		redoAction = new AbstractAction() {
			public void actionPerformed(ActionEvent ev) {
				try {
					if (undoManager.canRedo())	undoManager.redo();
				}
				catch (CannotRedoException e) {
					log.error("redo failed", e);
				}
			}
		};
	}
	
	public int getLimit() {
		return undoManager.getLimit();
	}
	
	public void setLimit(int limit) {
		undoManager.setLimit(limit);
	}
	
	public void install(JTextComponent text) {
		text.addPropertyChangeListener(propertyListener);
		
		final Document document	= text.getDocument();
		document.addUndoableEditListener(undoableEditListener);
		
		final ActionMap actionMap = text.getActionMap();
		actionMap.put(UNDO_ACTION,	undoAction);
		actionMap.put(REDO_ACTION,	redoAction);
		
		final InputMap inputMap = text.getInputMap();
		inputMap.put(UNDO_KEY_STROKE, UNDO_ACTION);
		inputMap.put(REDO_KEY_STROKE, REDO_ACTION);
	}
	
	public void uninstall(JTextComponent text) {
		text.removePropertyChangeListener(propertyListener);
		
		final Document document	= text.getDocument();
		document.removeUndoableEditListener(undoableEditListener);
		
		final ActionMap actionMap = text.getActionMap();
		actionMap.remove(UNDO_ACTION);
		actionMap.remove(REDO_ACTION);
		
		final InputMap inputMap = text.getInputMap();
		inputMap.remove(UNDO_KEY_STROKE);
		inputMap.remove(REDO_KEY_STROKE);
	}
}
