package net.psammead.commonist.util;

import java.awt.Dimension;
import java.awt.GraphicsConfiguration;
import java.awt.Insets;
import java.awt.Point;
import java.awt.Rectangle;
import java.awt.Toolkit;
import java.awt.Window;

import javax.swing.JScrollPane;
import javax.swing.JViewport;

/** swing UI utility functions */
public class UIUtil2 {
	/** fully static utility class, shall not be instantiated */
	private UIUtil2() {}
	
	/** sets window bounds limited to the screen estate */ 
	public static void limitAndChangeBounds(Window window, Rectangle bounds) {
		final Rectangle	screen = screenRect(
				Toolkit.getDefaultToolkit(),
				window.getGraphicsConfiguration());
		final Rectangle	limited	= boundsWithinScreen(bounds, screen);
		window.setBounds(limited);
	}
	
	/** limit a Rectangle to the screen boundaries */
	public static Rectangle boundsWithinScreen(Rectangle window, Rectangle screen) {
		final Rectangle	out	= new Rectangle(window);
		if (out.width  > screen.width)			out.width	= screen.width;
		if (out.height > screen.height)			out.height	= screen.height;
		if (out.x < screen.x)					out.x	= screen.x;
		if (out.y < screen.y)					out.y	= screen.y;
		if (out.x + out.width  > screen.width)	out.x	= screen.width  - out.width;
		if (out.y + out.height > screen.height)	out.y	= screen.height - out.height;
		return  out;
	}

	/** gets the screen estate */
	public static Rectangle screenRect(Toolkit toolkit, GraphicsConfiguration gc) {
		final Rectangle	bounds	= gc.getBounds();
		final Insets	insets	= toolkit.getScreenInsets(gc);
		bounds.x		+= insets.left;
		bounds.y		+= insets.top;
		bounds.width	-= insets.left + insets.right;
		bounds.height	-= insets.top  + insets.bottom;
		return bounds;
	}
	
	/** scrolls a JScrollPane such that the center of the content is the center of the viewPort */
	public static void scrollToCenter(JScrollPane scroll) {
		final JViewport	vp			= scroll.getViewport();
		final Dimension	viewSize	= vp.getViewSize();
		final Dimension	extentSize	= vp.getExtentSize();
		int	left	= (viewSize.width  - extentSize.width)  / 2;
		int	top		= (viewSize.height - extentSize.height) / 2;
		final Point	pos		= vp.getViewPosition();
		if (left >= 0) pos.x	= left;	
		if (top  >= 0) pos.y	= top;
		vp.setViewPosition(pos);
	}
	
	/** limits a Point to the insides of a Rectangle */
	public static Point limitToBounds(Point point, Dimension bounds) {
		return new Point(
				limitToBounds(point.x, 0, bounds.width),
				limitToBounds(point.y, 0, bounds.height));
	}
	
	/** limits an int value to given lower and upper bounds */
	private static int limitToBounds(int value, int minInclusive, int maxExclusive) {
			 if (value < minInclusive)	return minInclusive;
		else if (value >= maxExclusive)	return maxExclusive-1;
		else							return value;
	}
	
	
}
