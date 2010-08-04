package net.psammead.commonist.ui;

import java.awt.BorderLayout;
import java.awt.Dimension;
import java.awt.Image;
import java.awt.Rectangle;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;

import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.JSplitPane;
import javax.swing.WindowConstants;

import net.psammead.commonist.util.Settings;
import net.psammead.commonist.util.UIUtil2;

/** the application window */
public final class MainWindow {
	public final  JFrame	window;
	
	// components
	private final JPanel		commonPanel;
	private final JSplitPane	mainSplit;
	
	/** action events this UI sends */
	public interface Callback {
		void quit();
	}

	/** contains all UIs */
	public MainWindow(CommonUI commonUI, DirectoryUI directoryUI, 
			ImageListUI imageListUI, StatusUI statusUI, UploadUI uploadUI, 
			String programHeading, Image programIcon, final Callback callback) {
		//------------------------------------------------------------------------------
		//## panels
		
		commonPanel	= new JPanel();
		commonPanel.setLayout(new BorderLayout());
		commonPanel.add(commonUI,		BorderLayout.NORTH);
		commonPanel.add(directoryUI,	BorderLayout.CENTER);
		
		mainSplit	= new JSplitPane(JSplitPane.HORIZONTAL_SPLIT, commonPanel, imageListUI);
		mainSplit.setResizeWeight(0);
		
		final JPanel	uploadPanel	= new JPanel();
		uploadPanel.setLayout(new BorderLayout());
		uploadPanel.add(statusUI, BorderLayout.CENTER);
		uploadPanel.add(uploadUI, BorderLayout.EAST);
		
		final JPanel	windowPanel	= new JPanel();
		windowPanel.setLayout(new BorderLayout());
		windowPanel.add(mainSplit, BorderLayout.CENTER);
		windowPanel.add(uploadPanel, BorderLayout.SOUTH);
		
		//------------------------------------------------------------------------------
		//## frame
		
		window	= new JFrame(programHeading);
		window.setIconImage(programIcon);
		window.getContentPane().add(windowPanel);
		window.pack();
		window.setSize(new Dimension(800, 600));
		window.setDefaultCloseOperation(WindowConstants.DO_NOTHING_ON_CLOSE);	// EXIT_ON_CLOSE or DISPOSE_ON_CLOSE;
		
		window.setLocationRelativeTo(null);
		
		// quit on window close 
		window.addWindowListener(new WindowAdapter() {
			@Override
			public void windowClosing(WindowEvent ev) {
				window.dispose();
				// global
				callback.quit();
			}
		});
	}
	
	/** should be called after loadProperties */
	public void makeVisible() {
		window.setVisible(true);
	}

	/** call when ImageUIs have been added or removed */
	public void revalidate() {
		mainSplit.revalidate();
	}
	
	//------------------------------------------------------------------------------
	//## Settings

	/** loads this UI's state from the properties */
	public void loadSettings(Settings settings) {
		final Rectangle	bounds	= window.getBounds();
		bounds.x		= Integer.parseInt(settings.get("mainUI.x", ""+bounds.x));
		bounds.y		= Integer.parseInt(settings.get("mainUI.y", ""+bounds.y));
		bounds.width	= Integer.parseInt(settings.get("mainUI.w", ""+bounds.width));
		bounds.height	= Integer.parseInt(settings.get("mainUI.h", ""+bounds.height));
        UIUtil2.limitAndChangeBounds(window, bounds);
	}
	
	/** stores this UI's state in the properties */
	public void saveSettings(Settings settings) {
		final Rectangle	bounds	= window.getBounds();
		settings.set("mainUI.x",	""+bounds.x);
		settings.set("mainUI.y",	""+bounds.y);
		settings.set("mainUI.w",	""+bounds.width);
		settings.set("mainUI.h",	""+bounds.height);
	}
}
