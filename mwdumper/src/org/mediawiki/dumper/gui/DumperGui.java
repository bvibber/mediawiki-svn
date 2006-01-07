package org.mediawiki.dumper.gui;

import javax.swing.JPanel;
import javax.swing.JFrame;
import java.awt.GridBagLayout;
import javax.swing.JLabel;
import java.awt.GridBagConstraints;
import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;

import javax.swing.JTextField;
import javax.swing.JButton;
import javax.swing.JFileChooser;
import javax.swing.SwingUtilities;

import org.mediawiki.dumper.Tools;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.MultiWriter;
import org.mediawiki.importer.XmlDumpReader;
import org.mediawiki.importer.XmlDumpWriter;

public class DumperGui {
	private DumperWindow gui;
	private boolean running = false;

	void startImport(String inputFile) throws IOException {
		// TODO work right ;)
		final InputStream stream = Tools.openInputFile(inputFile);
		DumpWriter writer = new MultiWriter();
		DumpWriter progress = gui.getProgressWriter(writer, 1000);
		final XmlDumpReader reader = new XmlDumpReader(stream, progress);
		new Thread() {
			public void run() {
				running = true;
				gui.start();
				try {
					reader.readDump();
					stream.close();
				} catch(IOException e) {
					gui.setProgress("FAILED: " + e.getMessage());
				}
				running = false;
				gui.stop();
			}
		}.start();
	}

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		DumperGui manager = new DumperGui();
	}
	
	public DumperGui() {
		gui = new DumperWindow(this);
		gui.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
		gui.setVisible(true);
	}
}