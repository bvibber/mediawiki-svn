package net.psammead.commonist.ui;

import java.awt.Image;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.KeyEvent;
import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;

import javax.imageio.ImageIO;
import javax.swing.ImageIcon;
import javax.swing.JComponent;
import javax.swing.JFrame;
import javax.swing.JScrollPane;
import javax.swing.KeyStroke;
import javax.swing.SwingConstants;
import javax.swing.SwingUtilities;
import javax.swing.WindowConstants;

import net.psammead.commonist.util.UIUtil2;
import net.psammead.util.Logger;

/** displays a single image in full size */
public final class FullImageWindow {
	private static final Logger log = new Logger(FullImageWindow.class);
	
	public static void display(final File file, final String programHeading, final Image programIcon) {
		new Thread() { 
			@Override
			public void run() {
				final BufferedImage image;
				try {
					image = ImageIO.read(file);
				}
				catch (IOException e) {
					log.warn("cannot load image: " + file, e);
					return;
				}
				if (image == null) {
					log.warn("cannot load image: " + file);
					return;
				}
				SwingUtilities.invokeLater(
					new Runnable() {
						public void run() {
							new FullImageWindow(file, programHeading, programIcon, image);
						}
					}
				);
			}
		}.start();
	}
	
	/** displays an image in full size */
	public FullImageWindow(File file, String programHeading, Image programIcon, Image image) {
		final ImageIcon			icon	= new ImageIcon(image);
		
		final ScrollablePicture	label	= new ScrollablePicture();
		label.setHorizontalAlignment(SwingConstants.CENTER);
		label.setVerticalAlignment(SwingConstants.CENTER);
		label.setIcon(icon);
		
		final JScrollPane	scroll	= new JScrollPane(label);
		
		final String	heading	= file.getName() + " - " + programHeading;
		final JFrame	window	= new JFrame(heading);
		window.setIconImage(programIcon);
		window.getContentPane().add(scroll);
		window.pack();
		
		
//		// TODO: seems to break with small images
//		Rectangle	bounds	= UIUtil.boundsInScreen(window.getBounds());
//		bounds.width	= Math.max(bounds.width,	Constants.FULLSIZE_MIN_FRAME_SIZE);
//		bounds.height	= Math.max(bounds.height,	Constants.FULLSIZE_MIN_FRAME_SIZE);
//		window.setBounds(bounds);
		//window.MaximumSize				= window.Size;
		//statt dessen evtl. MaximizedBounds einsetzen
		
		UIUtil2.limitAndChangeBounds(window, window.getBounds());
		
		window.setDefaultCloseOperation(WindowConstants.DISPOSE_ON_CLOSE);
		window.setLocationRelativeTo(null);
		UIUtil2.scrollToCenter(scroll);
		window.setVisible(true);
		
		window.getRootPane().registerKeyboardAction(
			new ActionListener() { public void actionPerformed(ActionEvent ev) {
				window.dispose();
			}},
			KeyStroke.getKeyStroke(KeyEvent.VK_ESCAPE, 0),
			JComponent.WHEN_IN_FOCUSED_WINDOW
		);
	}
	
}
