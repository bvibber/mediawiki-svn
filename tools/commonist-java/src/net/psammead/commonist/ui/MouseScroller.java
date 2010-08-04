package net.psammead.commonist.ui;

import java.awt.Container;
import java.awt.Dimension;
import java.awt.Point;
import java.awt.event.MouseEvent;

import javax.swing.JComponent;
import javax.swing.JViewport;
import javax.swing.event.MouseInputAdapter;

import net.psammead.commonist.util.UIUtil2;

/** 
 * moves a JComponent withing a JViewport with the mouse 
 * usage: add an instance as MouseListener and MouseMotionListener to the target componente
 */
class MouseScroller extends MouseInputAdapter {
	private final JComponent	picture;
	
	private int x;
	private int y;
	
	public MouseScroller(JComponent picture) {
		this.picture = picture;
		x	= 0;
		y	= 0;
	}
	
	//public void mouseClicked(MouseEvent ev) {}
	
	@Override
	public void mousePressed(MouseEvent ev) {
		x	= ev.getX();
		y	= ev.getY();
	}
	
	//public void mouseReleased(MouseEvent ev) {}
	//public void mouseEntered(MouseEvent ev) {}
	//public void mouseExited(MouseEvent ev) {}
	
	@Override
	public void mouseDragged(MouseEvent ev) {
		final Container parent	= this.picture.getParent();
		if (!(parent instanceof JViewport))	return;
		final JViewport	viewPort	= (JViewport)parent;
		
		final Dimension	full	= this.picture.getSize();
		final Dimension	extent	= viewPort.getExtentSize();
		final Point		pos		= viewPort.getViewPosition();
		
		pos.translate(
				x - ev.getX(), 
				y - ev.getY());
		
		final Dimension	posLimits	= new Dimension(
				full.width  - extent.width,
				full.height - extent.height);
		final Point posLimited	= UIUtil2.limitToBounds(pos, posLimits);

		viewPort.setViewPosition(posLimited);
	}
	
	//public void mouseMoved(MouseEvent ev) {}
}