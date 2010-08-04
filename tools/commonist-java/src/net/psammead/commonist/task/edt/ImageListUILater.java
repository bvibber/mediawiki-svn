package net.psammead.commonist.task.edt;

import java.io.File;

import javax.swing.Icon;
import javax.swing.SwingUtilities;

import net.psammead.commonist.ui.ImageListUI;
import net.psammead.util.Logger;

/** wraps a ImageListUI's methods in SwingUtilities.invokeAndWait */
public final class ImageListUILater {
	private static final Logger log = new Logger(ImageListUILater.class);

	private final ImageListUI ui;
	
	public ImageListUILater(ImageListUI ui) {
		this.ui	= ui;
	}
	
	public void clear() {
		try { SwingUtilities.invokeAndWait(new Runnable() { public void run() {
			ui.clear();
		}}); }
		catch (Exception e)		{ log.error("problem", e); }
	}
	
	public void add(final File file, final Icon thumbnail, final int thumbnailMaxSize) {
		try { SwingUtilities.invokeAndWait(new Runnable() { public void run() {
			ui.add(file, thumbnail, thumbnailMaxSize);
		}}); }
		catch (Exception e)		{ log.error("problem", e); }
		
	}
	
	public void updateSelectStatus() {
		try { SwingUtilities.invokeAndWait(new Runnable() { public void run() {
			ui.updateSelectStatus();	
		}}); }
		catch (Exception e)		{ log.error("problem", e); }
		
	}
}