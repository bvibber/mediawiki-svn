package net.psammead.util.ui;

import java.awt.Component;
import java.awt.Container;
import java.awt.Dimension;
import java.awt.Insets;
import java.awt.LayoutManager;
import java.awt.Rectangle;

/** layouts two components horizontally or vertically such that the available space is divided at a fixed ratio */
public class RatioLayout implements LayoutManager {
	public static final String	FIRST	= "FIRST";
	public static final String	SECOND	= "SECOND";
	
	private final boolean	horizontal;
	private final int		gap;
	private final double	ratio;
	
	private Component first;
	private Component second;
	
	public static RatioLayout horizontal(double ratio, int gap) {
		return new RatioLayout(true, ratio, gap);
	}
	
	public static RatioLayout vertical(double ratio, int gap) {
		return new RatioLayout(false, ratio, gap);
	}
	
	public RatioLayout(boolean horizontal, double ratio, int gap) {
		if (ratio < 0.0 || ratio > 1.0)	  throw new IllegalArgumentException("precondition violated: 0.0 <= ratio <= 1.0");
		if (gap < 0)					  throw new IllegalArgumentException("precondition violated: gap >= 0");
		this.horizontal	 = horizontal;
		this.ratio		 = ratio;
		this.gap		 = gap;
	}
	
	public void addLayoutComponent(String constraint, Component c) {
			 if (FIRST.equals(constraint))	first	= c;
		else if (SECOND.equals(constraint)) second	= c;
		else throw new IllegalArgumentException("only FIRST and SECOND are allowed contraint values");
	}

	public void removeLayoutComponent(Component c) {
		if (first == c)		first	= null;
		if (second == c)	second	= null;
	}

	public Dimension preferredLayoutSize(Container c) {
		return minimumLayoutSize(c);
	}

	public Dimension minimumLayoutSize(Container c) {
		if (first == null && second == null) {
			return new Dimension(0,0);
		}
		else if (first != null && second == null) {
			return first.getMinimumSize();
		}
		else if (first == null && second != null) {
			return second.getMinimumSize();
		}
		else if (horizontal) {
			final Dimension min1	= first.getMinimumSize();
			final Dimension min2	= second.getMinimumSize();
			
			// in the split direction the mimimum size is choosen such that the minimum size of both component is respected
			final int split1	= min1.width;
			final int split2	= min2.width;
			final int added	= split1 + gap + split2;
			final int more1	= (int)(split1/ratio+gap);
			final int more2	= (int)(split2/(1-ratio)+gap);
			final int split	= Math.max(added, Math.max(more1, more2));

			// in the non-split direction us the maximum of both minimum sizes
			final int keep1	= min1.height;
			final int keep2	= min2.height;
			final int keep	= Math.max(keep1, keep2);
			
			return new Dimension(split, keep);
		}
		else {
			final Dimension min1	= first.getMinimumSize();
			final Dimension min2	= second.getMinimumSize();

			// in the split direction the mimimum size is choosen such that the minimum size of both component is respected
			final int split1	= min1.height;
			final int split2	= min2.height;
			final int added	= split1 + gap + split2;
			final int more1	= (int)(split1/ratio+gap);
			final int more2	= (int)(split2/(1-ratio)+gap);
			final int split	= Math.max(added, Math.max(more1, more2));
			
			// in the non-split direction us the maximum of both minimum sizes
			final int keep1	= min1.width;
			final int keep2	= min2.width;
			final int keep	= Math.max(keep1, keep2);
			
			return new Dimension(keep, split);
		}
	}
	
	public void layoutContainer(Container c) {
		final Dimension	size	= c.getSize();
		final Insets		insets	= c.getInsets();
		final Rectangle	bounds	= new Rectangle(
				insets.left, 
				insets.bottom, 
				size.width-insets.left-insets.right, 
				size.height-insets.top-insets.bottom); 
		if (first == null && second == null) {
			// nothing to do
		}
		else if (first != null && second == null) {
			first.setBounds(bounds);
		}
		else if (first == null && second != null) {
			second.setBounds(bounds);
		}
		else if (horizontal) {
			final int div = (int)((bounds.width - gap) * ratio);
			first.setBounds(bounds.x, bounds.y, div, bounds.height);
			second.setBounds(div+gap, bounds.y, bounds.width-div-gap, bounds.height);
		}
		else {
			final int div = (int)((bounds.height - gap) * ratio);
			first.setBounds(bounds.x, bounds.y, bounds.width, div);
			second.setBounds(bounds.x, div+gap, bounds.width, bounds.height-div-gap);
		}
	}
	
//	  public static void main(String[] args) {
//		  JFrame frame	= new JFrame("haha");
//		  Container contentPane = frame.getContentPane();
//		  contentPane.setLayout(new RatioLayout(false, 0.2, 5));
//		  contentPane.add(new JLabel("first"),	RatioLayout.FIRST);
//		  contentPane.add(new JLabel("second"), RatioLayout.SECOND);
//		  frame.setSize(100, 50);
//		  frame.setVisible(true);
//		  frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
//	  }
}
