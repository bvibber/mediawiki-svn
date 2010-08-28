package net.psammead.util.ui;

import java.awt.BorderLayout;
import java.awt.Component;
import java.awt.Container;
import java.awt.Dimension;
import java.awt.Window;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyEvent;

import javax.swing.JComponent;
import javax.swing.JFrame;
import javax.swing.KeyStroke;
import javax.swing.RootPaneContainer;
import javax.swing.SwingUtilities;
import javax.swing.WindowConstants;

import net.psammead.util.Logger;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class UIUtil {
	/** display a {@link JComponent} in a {@link JFrame} and exit the application when closed */
	public static void framed(JComponent component) {
		framed(component, true, true);
	}
	
	/** display a {@link JComponent} in a {@link JFrame} */
	public static void framed(JComponent component, boolean exitOnClose, boolean closeOnEscape) {
		final JFrame frame	= new JFrame();
		frame.setDefaultCloseOperation(exitOnClose
				? JFrame.EXIT_ON_CLOSE
				: WindowConstants.DISPOSE_ON_CLOSE);
		frame.setSize(800, 600);
		frame.setLocationRelativeTo(null);
		
		final Container content = frame.getContentPane();
		content.setLayout(new BorderLayout());
		content.add(component, BorderLayout.CENTER);
		
		frame.setVisible(true);
		
		if (closeOnEscape)	disposeOnEscape(frame);
	}
	
	/** make a {@link RootPaneContainer} which is a {@link Window} dispose itself when the user presses ghe ESC key */
	public static void disposeOnEscape(RootPaneContainer container) {
		if (!(container instanceof Window))	return;
		final Window window	= (Window)container; 
		container.getRootPane().registerKeyboardAction(
				new ActionListener() { public void actionPerformed(ActionEvent ev) {
					window.dispose();
				}},
				KeyStroke.getKeyStroke(KeyEvent.VK_ESCAPE, 0),
				JComponent.WHEN_IN_FOCUSED_WINDOW
			);
	}
	
	/** run some code in the EDT and wait for completion */
	public static void edtWait(Runnable runnable) {
		if (SwingUtilities.isEventDispatchThread()) { runnable.run(); return; }
		try { SwingUtilities.invokeAndWait(runnable); }
		catch (Exception e) { throw new RuntimeException(e); }
	}
	
	/** run some code in the EDT and continue */
	public static void edtRun(Runnable runnable) {
		if (SwingUtilities.isEventDispatchThread()) { runnable.run(); return; }
		try { SwingUtilities.invokeLater(runnable); }
		catch (Exception e) { throw new RuntimeException(e); }
	}
	
	/** sets minimum, preferred and maximum size of a {@link Component} */
	public static void setAllSizes(Component component, Dimension size) {
		component.setMinimumSize(size);
		component.setMaximumSize(size);
		component.setPreferredSize(size);
	}
	
	public static void startApp(final Logger log, final Runnable run) {
		SwingUtilities.invokeLater(new Runnable() {
			public void run() { 
				Thread.setDefaultUncaughtExceptionHandler(new Thread.UncaughtExceptionHandler() {
					public void uncaughtException(Thread t, Throwable e) {
						log.error("Exception caught in the Event Dispatch Thread", e);
					}
				});
				run.run();
			}
		});
	}
}
