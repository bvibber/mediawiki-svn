package net.psammead.commonist.task;

import java.io.File;
import java.util.Iterator;
import java.util.List;

import javax.swing.JOptionPane;
import javax.swing.SwingUtilities;

import net.psammead.commonist.Task;
import net.psammead.commonist.data.CommonData;
import net.psammead.commonist.data.ImageData;
import net.psammead.commonist.data.ImageListData;
import net.psammead.commonist.task.edt.StatusUILater;
import net.psammead.commonist.text.Gallery;
import net.psammead.commonist.text.TemplateException;
import net.psammead.commonist.text.Templates;
import net.psammead.commonist.text.Upload;
import net.psammead.commonist.ui.MainWindow;
import net.psammead.commonist.ui.StatusUI;
import net.psammead.commonist.util.Messages;
import net.psammead.mwapi.Location;
import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.NameSpace;
import net.psammead.mwapi.ui.Page;
import net.psammead.mwapi.ui.ProgressCallback;
import net.psammead.mwapi.ui.UnsupportedWikiException;
import net.psammead.mwapi.ui.UploadCallback;
import net.psammead.mwapi.ui.Uploaded;
import net.psammead.util.Logger;
import net.psammead.util.TextUtil;

/** upload files selected in the ImageListUI */
public class UploadFilesTask extends Task {
	private static final Logger log = new Logger(UploadFilesTask.class);
	
	private final MediaWiki	mw;
	private final MainWindow	mainWindow;
	private final CommonData 	commonData;
	private	final Gallery		gallery;
	
	private final StatusUILater	statusUILater;

	public UploadFilesTask(MediaWiki mw, Templates templates, MainWindow mainWindow, 
			CommonData commonData, ImageListData listData, 
			StatusUI statusUI) {
		this.mw				= mw;
		this.mainWindow		= mainWindow;
		this.commonData		= commonData;
		this.statusUILater	= new StatusUILater(statusUI);
		
		gallery	= createGallery(templates, commonData, listData);
	}

	/** build a Gallery from selected images */
	private Gallery createGallery(Templates templates, CommonData commonData, ImageListData listData) {
		final Gallery			 out	= new Gallery(templates, commonData);
		final List<ImageData>	todo	= listData.getSelected();
		for (Iterator<ImageData> it=todo.iterator(); it.hasNext();) {
			final ImageData	imageData	= it.next();
			final Upload		upload		= new Upload(commonData, imageData);
			out.addUpload(upload);
		}
		out.chain();
		return out;
	}

	@Override
	protected void execute() {
		if (gallery.isEmpty()) {
			log.info("nothing to upload");
			statusUILater.halt("status.upload.empty", new Object[] {});
			return; 
		}
		
		//### BÄH
		final String	wiki		= commonData.wiki;
		final String	user		= commonData.user;
		final String	password	= commonData.password;
		
		//------------------------------------------------------------------------------
		//## login
		
		log.info("logging in");
		try {
			// login
			statusUILater.indeterminate("status.login.started", new Object[] { wiki });
			final boolean	remember	= false;
			try {
				boolean success	= mw.login(wiki, user, password, remember);
				if (!success) { statusUILater.halt("status.login.wrongpw", new Object[] { wiki }); return; }
			}
			catch (Exception e) {
				statusUILater.halt("status.login.error", new Object[] { wiki, e.getMessage() });
				log.error("login failed", e);
				return;
			}
			
			check();
		}
		catch (AbortedException e) {
			log.info("login aborted");
			statusUILater.halt("status.login.aborted", new Object[0]);
			return;
		}
		statusUILater.halt("status.login.successful", new Object[] { wiki });
		
		//------------------------------------------------------------------------------
		//## uploads

		log.info("uploading files");
		try {
			// upload files and build gallery
			for (Iterator<Upload> it=gallery.getUploads().iterator(); it.hasNext();) {
				final Upload	upload	= it.next();
				final File	file	= upload.getFile();
				
				statusUILater.halt("status.upload.started", new Object[] { file.getPath() });
				final String	title	= upload.getTitle();
				final String	text	= gallery.imageDescription(upload);
				try {
					final ProgressCallback	progressCallback	= new MyProgressCallback(upload);
					final UploadCallback		uploadCallback		= new MyUploadCallback(upload);
					final boolean		watchThis	= true;	// TODO: should be configurable
					final Uploaded	uploaded	= mw.upload(wiki, title, text, file, watchThis, progressCallback, uploadCallback);
					final Location	uploc		= uploaded.location;
					// TODO uploc may be null if the user aborted
					statusUILater.halt("status.upload.successful", new Object[] { file.getPath(), uploc.toString() });
					upload.setLocation(uploc);
				}
				catch (Exception e) {
					statusUILater.halt("status.upload.error", new Object[] { file.getPath(), e.getMessage() });
					try {
						// HACK..
						final Location	uploc	= mw.absoluteLocation(
								wiki + ":" + mw.nameSpace(wiki, NameSpace.FILE).addTo(title));
						upload.setLocation(uploc);
						upload.setError(e);
						log.error("upload failed: " + title, e);
					}
					catch (UnsupportedWikiException e2) {
						log.error("gallery could not be changed", e2);
					}
				}
				
				check();
			}
			log.info("upload complete");
		}
		catch (TemplateException e) {
			log.error("template problem", e);
			statusUILater.halt("status.upload.aborted", new Object[0]);
			return;
		}
		catch (AbortedException e) {
			log.info("upload aborted");
			statusUILater.halt("status.upload.aborted", new Object[0]);
			return;
		}
		
		//------------------------------------------------------------------------------
		//## gallery

		log.info("changing gallery");
		try {
			// load gallery
			final Location	homeLocation	= mw.homeLocation(wiki);
			final Location	galleryLocation	= mw.relativeLocation(homeLocation, "/gallery");
			statusUILater.indeterminate("status.gallery.loading", new Object[] { "[[" + galleryLocation.toString() + "]]" });
			final Page		loaded			= mw.load(galleryLocation);
			
			// change gallery
			statusUILater.indeterminate("status.gallery.storing", new Object[] { "[[" + loaded.location.toString() + "]]" });
			final String	text	= gallery.galleryDescription();
			final Page		store	= loaded.edit(text + "\n\n" + TextUtil.trimLF(loaded.body));
			final String	summary	= gallery.gallerySummary();
			final boolean	minor	= false;
			final Page	stored	= mw.store(store, summary, minor);
			if (stored == null)	statusUILater.halt("status.gallery.updated",		new Object[] { "[[" + loaded.location.toString() + "]]" });
			else				statusUILater.halt("status.gallery.editConflict",	new Object[] { "[[" + loaded.location.toString() + "]]" });
		}
		catch (Exception e) {
			log.error("gallery update error");
			statusUILater.halt("status.gallery.error", new Object[] { e.getMessage() });
		}
		
//		//------------------------------------------------------------------------------
//		//## logout
//		
//		log.info("logging out");
//		try {
//			statusUILater.indeterminate("status.logout.started", new Object[] { wiki });
//			boolean success	= mw.logout(wiki);
//			if (!success) { statusUILater.halt("status.logout.failed", new Object[] { wiki }); return; }
//		}
//		catch (Exception e) {
//			statusUILater.halt("status.logout.error", new Object[] { wiki, e.getMessage() });
//			log.error("logout failed", e);
//			return;
//		}
//		statusUILater.halt("status.logout.successful", new Object[] { wiki });
		
		//------------------------------------------------------------------------------
		
		log.info("upload finished");
	}
	
	//------------------------------------------------------------------------------
	//## callbacks
	
	/** updates the statusUI when file upload progresses */
	private class MyProgressCallback implements ProgressCallback {
		private final Upload	upload;
		
		public MyProgressCallback(Upload upload) {
			this.upload	= upload;
		}

		public void bytesWritten(long bytes, long ofBytes) {
			// System.err.println("written " + bytes + " of " + ofBytes);
			final int	percent	= (int)(bytes * 100 / ofBytes);
			final String	path	= upload.getFile().getPath();
			statusUILater.determinate("status.upload.progress", new Object[] { path, new Integer(percent) }, percent, 100);
			// rate = (bytes - oldBytes) / (time  - oldTime)
		}
	}
	
	/** asks the user when somwethiung about a file upload is unclear */
	private class MyUploadCallback implements UploadCallback { 
		private final Upload	upload;
		
		public MyUploadCallback(Upload upload) {
			this.upload	= upload;
		}
		
		/** when true, the file overwites another file */
		public boolean ignoreFileexists() { 
			final String	name	= upload.getName();
			return callbackQuery(name, 
					"query.upload.ignoreFileexists.title", 
					"query.upload.ignoreFileexists.message");	// false
		}
		
		/** when non-null the result is used as new file name */
		public String renameFileexists() { 
			log.debug("renameFileexists"); 
			return null; 
		}
		
		/** if true, a large file will be written */
		public boolean ignoreLargefile() { 
			log.debug("ignoreLargefile"); 
			return true; 
		}
		
		/** if true, previously uploaded and deleted files will be uploaded nevertheless */
		public boolean ignoreFilewasdeleted() { 
			final String	name	= upload.getName();
			return callbackQuery(name, 
					"query.upload.ignoreFilewasdeleted.title", 
					"query.upload.ignoreFilewasdeleted.message");	// true
		}
		
		/** ask the user a yes/no message */
		private boolean callbackQuery(final String fileName, final String titleKey, final String messageKey) {
			final boolean[]	answer	= new boolean[] { false };
			try {
				SwingUtilities.invokeAndWait(new Runnable() {
					public void run() {
						answer[0]	= JOptionPane.YES_OPTION == JOptionPane.showConfirmDialog(
								mainWindow.window,
								Messages.message(messageKey, new Object[] { fileName }),
								Messages.text(titleKey),
								JOptionPane.YES_NO_OPTION);
					}
				});
			}
			catch (Exception e) {
				log.error("callback error", e);
				throw new RuntimeException(e);
			}
			return answer[0];
		}
	}
}
