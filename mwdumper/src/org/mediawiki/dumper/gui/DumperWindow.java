/*
 * DumperWindow.java
 *
 * Created on January 6, 2006, 2:57 PM
 *
 * Implementation on top of the NetBeans-generated code in DumperWindowForm.
 * I hate editing generated code!
 */

package org.mediawiki.dumper.gui;

import java.io.File;
import java.io.IOException;
import javax.swing.JFileChooser;
import javax.swing.SwingUtilities;
import org.mediawiki.importer.DumpWriter;

/**
 *
 * @author brion
 */
public class DumperWindow extends DumperWindowForm {
	protected DumperGui backend;
	
	/** Creates a new instance of DumperWindow */
	public DumperWindow(DumperGui backend) {
		this.backend = backend;
	}
	
	public DumpWriter getProgressWriter(DumpWriter sink, int interval) {
		return new GraphicalProgressFilter(sink, interval, progressLabel);
	}
	
	public void start() {
		// todo: set the button up to a stop mode. ;)
		startButton.setEnabled(false);
	}
	
	public void stop() {
		startButton.setEnabled(true);
	}
	
	/**
	 * Set the progress bar text asynchronously, eg from a background thread
	 */
	public void setProgress(String text) {
		final String _text = text;
		SwingUtilities.invokeLater(new Runnable() {
			public void run() {
				progressLabel.setText(_text);
			}
		});
	}
	
	/* -- event handlers -- */
	
	protected void onBrowseButtonActionPerformed(java.awt.event.ActionEvent evt) {
		JFileChooser chooser = new JFileChooser();
		chooser.showOpenDialog(this);
		File selection = chooser.getSelectedFile();
		try {
			fileText.setText(selection.getCanonicalPath());
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
	}

	protected void onConnectButtonActionPerformed(java.awt.event.ActionEvent evt) {
		System.out.println("Connect!");
	}
	
	protected void onStartButtonActionPerformed(java.awt.event.ActionEvent evt) {
		try {
			backend.startImport(fileText.getText());
		} catch (IOException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
	}

	protected void onQuitItemActionPerformed(java.awt.event.ActionEvent evt) {
		System.exit(0);
	}                                        
	
}
