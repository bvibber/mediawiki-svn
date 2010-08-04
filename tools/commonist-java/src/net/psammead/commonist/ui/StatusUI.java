package net.psammead.commonist.ui;

import javax.swing.JProgressBar;

import net.psammead.commonist.util.Messages;

/** a JProgressBar displaying Messages */
public final class StatusUI extends JProgressBar {
	/** displays program status */
	public StatusUI() {
		super(JProgressBar.HORIZONTAL);
		setStringPainted(true);
	}
	
	/** changes the upload progressbar to indeterminate state */
	public void indeterminate(String messageKey, Object[] data) {
		setIndeterminate(true);
		setString(Messages.message(messageKey, data));
	}
	
	/** changes the upload progressbar to determinate state */
	public void determinate(String messageKey, Object[] data, int value, int maximum) {
		setIndeterminate(false);
		setString(Messages.message(messageKey, data));
		setMaximum(maximum);
		setValue(value);
	}
	
	/** changes the upload progressbar to determinate state */
	public void halt(String messageKey, Object[] data) {
		setIndeterminate(false);
		setString(Messages.message(messageKey, data));
		setMaximum(0);
		setValue(0);
	}
}
