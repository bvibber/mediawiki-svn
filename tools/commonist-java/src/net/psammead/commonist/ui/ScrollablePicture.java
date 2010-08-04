package net.psammead.commonist.ui;

import java.awt.Dimension;
import java.awt.Rectangle;

import javax.swing.JLabel;
import javax.swing.Scrollable;
import javax.swing.SwingConstants;
import javax.swing.event.MouseInputListener;

import net.psammead.commonist.Constants;

/** a JLabel used to display an image in fullsize */
public class ScrollablePicture extends JLabel implements Scrollable {
    public ScrollablePicture() {
		setAutoscrolls(true);
        
		final MouseInputListener	mouse	= new MouseScroller(this);
		addMouseListener(mouse);
		addMouseMotionListener(mouse);
    }

    public int getScrollableUnitIncrement(Rectangle visibleRect, int orientation, int direction) {
    	final int currentPosition	= orientation == SwingConstants.HORIZONTAL
									? visibleRect.x
									: visibleRect.y;
        if (direction < 0) {
        	final int newPosition = currentPosition - (currentPosition / Constants.FULLSIZE_MAX_UNIT_INCREMENT) * Constants.FULLSIZE_MAX_UNIT_INCREMENT;
            return newPosition == 0 
            		? Constants.FULLSIZE_MAX_UNIT_INCREMENT 
            		: newPosition;
        } 
		else {
            return ((currentPosition / Constants.FULLSIZE_MAX_UNIT_INCREMENT) + 1) * Constants.FULLSIZE_MAX_UNIT_INCREMENT - currentPosition;
        }
    }

    public int getScrollableBlockIncrement(Rectangle visibleRect, int orientation, int direction) {
		return orientation == SwingConstants.HORIZONTAL
				? visibleRect.width  - Constants.FULLSIZE_MAX_UNIT_INCREMENT
				: visibleRect.height - Constants.FULLSIZE_MAX_UNIT_INCREMENT;
    }

    public Dimension getPreferredScrollableViewportSize()	{ return getPreferredSize(); }
    public boolean getScrollableTracksViewportWidth()		{ return false; }
    public boolean getScrollableTracksViewportHeight()		{ return false; };
    
    /*
    private class MouseBorderScroll extends MouseInputAdapter {
    	public void mouseDragged(MouseEvent ev) {
    		Rectangle r = new Rectangle(ev.getX(), ev.getY(), 1, 1);
    		scrollRectToVisible(r);
    	}
    }
    */
}
