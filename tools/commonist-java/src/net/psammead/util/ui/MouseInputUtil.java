package net.psammead.util.ui;

import javax.swing.JComponent;
import javax.swing.event.MouseInputListener;

import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class MouseInputUtil {
	private MouseInputUtil() {}
	
	public static void install(JComponent component, MouseInputListener listener) {
		component.addMouseListener(listener);
		component.addMouseMotionListener(listener);
	}
	
	public static void deinstall(JComponent component, MouseInputListener listener) {
		component.removeMouseListener(listener);
		component.removeMouseMotionListener(listener);
	}
}
