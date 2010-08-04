package net.psammead.commonist.task.edt;

import javax.swing.SwingUtilities;

import net.psammead.commonist.ui.StatusUI;
import net.psammead.util.Logger;

/** wraps a StatusUI's methods in SwingUtilities.invokeAndWait */
public final class StatusUILater {
	private static final Logger log = new Logger(StatusUILater.class);

	private final StatusUI ui;

	public StatusUILater(StatusUI ui) {
		this.ui = ui;
	}
	
	public void indeterminate(final String messageKey, final Object[] messageArgs) {
		try { SwingUtilities.invokeAndWait(new Runnable() { public void run() {
			ui.indeterminate(messageKey, messageArgs);
		}}); }
		catch (Exception e)	{ log.error("problem", e); }
	}

	public void determinate(final String messageKey, final Object[] messageArgs, final int cur, final int max) {
		try { SwingUtilities.invokeAndWait(new Runnable() { public void run() {
			ui.determinate(messageKey, messageArgs, cur, max);
		}}); }
		catch (Exception e)	{ log.error("problem", e); }
	}
	
	public void halt(final String messageKey, final Object[] messageArgs) {
		try { SwingUtilities.invokeAndWait(new Runnable() { public void run() {
			ui.halt(messageKey, messageArgs);
		}}); }
		catch (Exception e)	{ log.error("problem", e); }
	}

}