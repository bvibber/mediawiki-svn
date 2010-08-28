package net.psammead.util.ui;

import java.awt.Rectangle;
import java.awt.event.MouseEvent;
import java.awt.event.MouseMotionAdapter;

import javax.swing.JComponent;

import net.psammead.util.annotation.FullyStatic;

/** provide srolling with mouse-drags for {@link JComponent}s */
@FullyStatic 
public final class AutoScroll {
	private AutoScroll() {}
	
	public static void install(JComponent target) {
		target.setAutoscrolls(true);
		target.addMouseMotionListener(new MouseMotionAdapter() {
			@Override
			public void mouseDragged(MouseEvent e) {
			    ((JComponent)e.getSource()).scrollRectToVisible(
			    		new Rectangle(e.getX(), e.getY(), 1, 1));
			}
		});
	}
}
