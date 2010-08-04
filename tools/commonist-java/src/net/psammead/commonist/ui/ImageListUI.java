package net.psammead.commonist.ui;

import java.awt.Component;
import java.awt.Dimension;
import java.awt.GridBagLayout;
import java.awt.Image;
import java.awt.Rectangle;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.File;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.Icon;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JSeparator;
import javax.swing.Scrollable;
import javax.swing.SwingConstants;

import net.psammead.commonist.Constants;
import net.psammead.commonist.data.ImageData;
import net.psammead.commonist.data.ImageListData;
import net.psammead.commonist.util.Messages;
import net.psammead.commonist.util.TextUtil2;
import net.psammead.util.ui.GridBagConstraints2;

/** displays a scrollable List of ImageUIs */
public final class ImageListUI extends JPanel {
	// components
	private final JPanel	listPanel;
	private	final JLabel	selectStatus;

	// state
	private final String		programHeading;
	private final Image			programIcon;
	private final List<ImageUI>	imageUIs;
	
	/** contains a List of ImageUI objects */
	public ImageListUI(String programHeading, Image programIcon) {
		this.programHeading		= programHeading;
		this.programIcon		= programIcon;
		
		imageUIs	= new ArrayList<ImageUI>();
		
		//------------------------------------------------------------------------------
		//## components

		listPanel	= new ListPanel();
		listPanel.setLayout(new BoxLayout(listPanel, BoxLayout.Y_AXIS));
		
		final JScrollPane	scroll	= new JScrollPane(listPanel, JScrollPane.VERTICAL_SCROLLBAR_ALWAYS, JScrollPane.HORIZONTAL_SCROLLBAR_NEVER);
		scroll.setBorder(BorderFactory.createEmptyBorder(0,0,0,0));	//### scrollBorder?
		
		//var	bar	= scroll.getVerticalScrollBar();

		final JButton	selectAllButton		= new JButton(Messages.text("imageList.selectAll"));
		final JButton	deselectAllButton	= new JButton(Messages.text("imageList.deselectAll"));
		selectStatus		= new JLabel();
		
		//------------------------------------------------------------------------------
		//## layout
		
		setBorder(Constants.PANEL_BORDER);

		setLayout(new GridBagLayout());
		add(deselectAllButton,	new GridBagConstraints2().pos(0,0).size(1,1).weight(0.001,0).anchorCenter().fillHorizontal().insets(0,0,0,0));
		add(selectAllButton,	new GridBagConstraints2().pos(1,0).size(1,1).weight(0.001,0).anchorCenter().fillHorizontal().insets(0,0,0,0));
		add(selectStatus,		new GridBagConstraints2().pos(2,0).size(1,1).weight(1,0).anchorCenter().fillHorizontal().insets(0,4,0,4));
		add(scroll,				new GridBagConstraints2().pos(0,1).size(3,1).weight(1,1).anchorCenter().fillBoth().insets(0,0,0,0));

		//------------------------------------------------------------------------------
		//## wiring
		
		selectAllButton.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent ev) {
				updateSelectStatus(true);
			}
		});
		
		deselectAllButton.addActionListener(new ActionListener() {
			public void actionPerformed(ActionEvent ev) {
				updateSelectStatus(false);
			}
		});
		
		//------------------------------------------------------------------------------
		//## init
		
		updateSelectStatus();
	}
	
	/** removes all ImageUI objects */
	public void clear() {
		imageUIs.clear();
		listPanel.removeAll();
	}
	
	/** adds a File UI */
	public void add(File file, Icon icon, int thumbnailMaxSize) {
		ImageUI	imageUI	= new ImageUI(file, icon, thumbnailMaxSize, programHeading, programIcon, new ImageUI.Callback() {
			public void updateSelectStatus() { ImageListUI.this.updateSelectStatus(); }
		});
		
		imageUIs.add(imageUI);
		if (!imageUIs.isEmpty()) {
			listPanel.add(new JSeparator());
		}
		listPanel.add(imageUI);
	}
	
	/** get the select status and update the display */
	public void updateSelectStatus() {
		int		allFiles		= 0;
		long	allBytes		= 0;
		int		selectedFiles	= 0;
		long	selectedBytes	= 0;
		
		for (Iterator<ImageUI> it=imageUIs.iterator(); it.hasNext();) {
			final ImageUI	imageUI		= it.next();
			final long	fileSize	= imageUI.getData().file.length();
			allFiles	++;
			allBytes	+= fileSize;
			if (imageUI.isUploadSelected()) {
				selectedFiles	++;
				selectedBytes	+= fileSize;
			}
		}
	
		selectStatus.setText(Messages.message("imageList.selected", new Object[] {
			new Integer(selectedFiles), new Integer(allFiles), TextUtil2.human(selectedBytes), TextUtil2.human(allBytes) 
		}));
	}
	
	public ImageListData getData() {
		final List<ImageData>	out	= new ArrayList<ImageData>();
		for (Iterator<ImageUI> it=imageUIs.iterator(); it.hasNext();) {
			final ImageUI	imageUI	= it.next();
			out.add(imageUI.getData());
		}
		return new ImageListData(out);
	}
	
	//------------------------------------------------------------------------------
	//## private methods
	
	/** checks or unchecks the upload checkbox in all images */
	private void updateSelectStatus(boolean all) {
		for (Iterator<ImageUI> it=imageUIs.iterator(); it.hasNext();) {
			final ImageUI	imageUI	= it.next();
			imageUI.setUploadSelected(all);
		}
		updateSelectStatus();
	}
	
	//------------------------------------------------------------------------------
	//## private classes
	
	/** a Scrollable Panel scrolling to even tickets */
	private static class ListPanel extends JPanel implements Scrollable {
		/**	visibleRect	The view area visible within the viewport
			orientation	SwingConstants.VERTICAL or SwingConstants.HORIZONTAL.
			direction	Less than zero to scroll up/left, greater than zero for down/right.
		*/
		public int getScrollableUnitIncrement(Rectangle visibleRect, int orientation, int direction) {
			if (orientation == SwingConstants.HORIZONTAL)	return 1;
			
			if (direction < 0) {
				final Component	component	= this.getComponentAt(visibleRect.x, visibleRect.y - 2);
				int	visible		= visibleRect.y ;
				int	target		= component != null ? component.getY() : 0;
				return visible - target;
			}
			else {
				final Component	component	= this.getComponentAt(visibleRect.x, visibleRect.y + visibleRect.height);
				int	visible		= visibleRect.y + visibleRect.height;
				int	target		= component != null ? (component.getY() + component.getHeight()) : this.getHeight();
				return target - visible;
			}
		}
		
		/**	visibleRect	The view area visible within the viewport
			orientation	SwingConstants.VERTICAL or SwingConstants.HORIZONTAL.
			direction	Less than zero to scroll up/left, greater than zero for down/right.
		*/
		public int getScrollableBlockIncrement(Rectangle visibleRect, int orientation, int direction) {
			if (orientation == SwingConstants.HORIZONTAL)	return 1;
			
			if (direction < 0)	return Math.min(visibleRect.height, visibleRect.y - 0);
			else				return Math.min(visibleRect.height, this.getHeight()  - (visibleRect.y + visibleRect.height));
		}  
		
		public Dimension getPreferredScrollableViewportSize()	{ return this.getPreferredSize(); }
		public boolean getScrollableTracksViewportWidth()		{ return true;	}
		public boolean getScrollableTracksViewportHeight()		{ return false;	}
	}
}
