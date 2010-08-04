package net.psammead.commonist.task;

import java.io.File;
import java.util.Arrays;

import javax.swing.Icon;

import net.psammead.commonist.Constants;
import net.psammead.commonist.Task;
import net.psammead.commonist.task.edt.ImageListUILater;
import net.psammead.commonist.task.edt.StatusUILater;
import net.psammead.commonist.thumb.Thumbnails;
import net.psammead.commonist.ui.ImageListUI;
import net.psammead.commonist.ui.MainWindow;
import net.psammead.commonist.ui.StatusUI;
import net.psammead.commonist.util.FileFilters;
import net.psammead.util.Logger;

/** change the directory displayed in the ImageListUI */
public class ChangeDirectoryTask extends Task {
	private static final Logger log = new Logger(ChangeDirectoryTask.class);
	
	private final MainWindow		mainWindow;
	private final ImageListUILater	imageListUILater;
	private final StatusUILater		statusUILater;
	private final Thumbnails		thumbnails;
	private	final File				directory;
	
	public ChangeDirectoryTask(MainWindow mainWindow, ImageListUI imageListUI, StatusUI statusUI, Thumbnails thumbnails,  File directory) {
		this.mainWindow			= mainWindow;
		this.imageListUILater	= new ImageListUILater(imageListUI);
		this.statusUILater		= new StatusUILater(statusUI);
		this.thumbnails			= thumbnails;
		this.directory			= directory;
	}
	
	@Override
	protected void execute() {
		log.debug("clear");
		
		imageListUILater.clear();
		Thread.yield();	//Thread.sleep(50);
		
		log.debug("listFiles"); 
		final File[]	files	= directory.listFiles(FileFilters.VISIBLE_FILES_ONLY);
		if (files == null) { log.warn("directory does not exist: " + directory); return; }
		Arrays.sort(files);	// use a Comparator?
		
		final int 	max		= files.length;
		int 	cur		= 0;
		long	last	= 0;
		try {
			for (int i=0; i<files.length; i++) {
				check();
				
				final File	file	= files[i];
	
//				log.debug("loading: " + file.getName()); 
				statusUILater.determinate("imageList.loading", new Object[] {file.getPath(), new Integer(cur), new Integer(max) }, cur, max);
				cur++;
	
				if (!file.canRead()) { log.warn("cannot read: " + file); continue; }
				// using Thread.interrupt while this is running kills the EDT??
				final Icon	thumbnail			= thumbnails.thumbnail(file);
				final int		thumbnailMaxSize	= thumbnails.getMaxSize();
				imageListUILater.add(file, thumbnail, thumbnailMaxSize);
				try { Thread.sleep(100); }
				catch (InterruptedException e) { log.warn("interrupted", e); break; }
	
				// update when a given number of ImageUIs have been added
				// or a given delay has elapsed or 
				final long	now	= System.currentTimeMillis();
				if (now - last > Constants.IMAGELIST_UPDATE_DELAY
				|| (cur % Constants.IMAGELIST_UPDATE_COUNT) == 0) {
					imageListUILater.updateSelectStatus();
					// this doesn't have to run in the EDT, 
					// but is needed to make our changes visible
					mainWindow.revalidate();
					last	= now;
				}
			}
			
			statusUILater.halt("imageList.loaded", new Object[] { directory.getPath(), new Integer(max) });
		}
		catch (AbortedException e) {
			log.info("loading image list aborted");
			// TODO: statusUI?
		}
		imageListUILater.updateSelectStatus();
	}
}
