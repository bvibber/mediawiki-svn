package net.psammead.commonist;

import java.awt.Image;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Reader;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;
import java.util.Vector;

import net.psammead.commonist.data.LicenseData;
import net.psammead.commonist.task.ChangeDirectoryTask;
import net.psammead.commonist.task.UploadFilesTask;
import net.psammead.commonist.text.ParsedLicenses;
import net.psammead.commonist.text.Templates;
import net.psammead.commonist.thumb.FileCache;
import net.psammead.commonist.thumb.Thumbnails;
import net.psammead.commonist.ui.CommonUI;
import net.psammead.commonist.ui.DirectoryUI;
import net.psammead.commonist.ui.ImageListUI;
import net.psammead.commonist.ui.MainWindow;
import net.psammead.commonist.ui.StatusUI;
import net.psammead.commonist.ui.UploadUI;
import net.psammead.commonist.util.Loader;
import net.psammead.commonist.util.Messages;
import net.psammead.commonist.util.Settings;
import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.connection.ConfigException;
import net.psammead.util.Logger;
import net.psammead.util.apple.AppleQuit;
import net.psammead.util.ui.UIUtil;
import bsh.EvalError;
import bsh.Interpreter;

/** the main application class */
public final class Commonist {
	private static final Logger log = new Logger(Commonist.class);
	
	/** main entry point */
	public static void main(String[] args) {
		UIUtil.startApp(log, new Runnable() { public void run() {
			try { 
				new Commonist(null, "The Commonist").init();
			}
			catch (Exception e) {
				log.error("cannot start program", e);
			}
		}});
	}
	
	private final MediaWiki 	mw;

	private final Loader		loader;
	private	final File			settingsDir;
	
	private final Settings		settings;
	private final Thumbnails	thumbnails;
	private final FileCache		cache;
	private	final Templates		templates;

	private final MainWindow	mainWindow;
	private final CommonUI		commonUI;
	private final DirectoryUI	directoryUI;
	private	final ImageListUI	imageListUI;
	private	final StatusUI		statusUI;
	private final UploadUI		uploadUI;

	private ChangeDirectoryTask changeDirectoryTask;
	private UploadFilesTask 	uploadFilesTask;
	
	public Commonist(Image programIcon, String programHeading) {
		settingsDir	= new File(new File(System.getProperty("user.home")), ".commonist");
		settingsDir.mkdirs();
		
		// HACK: running from webstart or load from the Filesystem
//		boolean fromWebStart	= !(new File("etc/licenses.txt").exists());
		
		final File	projectDir	= new File(new File(System.getProperty("user.dir")), "etc");
		
		loader	= new Loader(settingsDir, projectDir, "/etc/");

		final String	userLanguage	= System.getProperty("user.language");
		log.info("using user language: " + userLanguage);
		try { initMessages(userLanguage); }
		catch (IOException e) { throw new Error("cannot load messages.properties", e); }
		
		final List<LicenseData>	licenses;
		try { licenses	= initLicenses(); }
		catch (IOException e) { throw new Error("cannot load licenses.txt", e); }

		try { mw = new MediaWiki(); }
		catch (ConfigException e) { throw new Error("cannot instantiate MediaWiki", e); }
		mw.setLog(System.err);
		mw.setupProxy();
		
		addFamilies();
		final List<String>	wikiList	= new Vector<String>(mw.supportedWikis());
		
		try { sourceStartup(); }
		catch (IOException e) { throw new Error("cannot load startup.bsh", e); }
		
		settings	= new Settings(
								settingsFile("settings.properties"));

		cache		= new FileCache(
								settingsFile("thumbnails.txt"), 
								settingsFile("cache"),
								Constants.THUMBNAIL_CACHE_SIZE);
								
		thumbnails	= new Thumbnails(cache);
		
		commonUI	= new CommonUI(wikiList, licenses);
		
		directoryUI	= new DirectoryUI(new DirectoryUI.Callback() {
			public void changeDirectory(File currentDirectory) {
				doChangeDirectory(currentDirectory);
			}
		});
		
		statusUI	= new StatusUI();
		
		uploadUI	= new UploadUI(new UploadUI.Callback() {
			public void startUpload()	{ doStartUpload();	}
			public void stopUpload()	{ doStopUpload();	}
		});
		
		imageListUI	= new ImageListUI(programHeading, programIcon);
		
		mainWindow	= new MainWindow(commonUI, directoryUI, imageListUI, statusUI, uploadUI,
								programHeading, programIcon, new MainWindow.Callback() {
			public void quit() { doQuit(); }
		});
		
		templates	= new Templates(loader);
		
		changeDirectoryTask	= null;
		uploadFilesTask		= null;
		
		// install AppleQuit
		AppleQuit.install(new AppleQuit.Handler() {
			public void applicationQuit() { doQuit(); }
		});
	}
	
	//-------------------------------------------------------------------------
	//## life cycle
	
	/** startup, called after UI constructors */
	public void init() {
		log.info("starting up");
		
		try { cache.load(); }
		catch (IOException e) { log.error("cannot load cache", e); }
		
		try { settings.load(); }
		catch (IOException e) { log.error("cannot load settings", e); }
		
		thumbnails.loadSettings(settings);
		commonUI.loadSettings(settings);
		directoryUI.loadSettings(settings);
		mainWindow.loadSettings(settings);
		
		mainWindow.makeVisible();
		
		log.info("running");
	}

	/** shutdown */
	public void exit() {
		log.info("shutting down");
		
		thumbnails.saveSettings(settings);
		commonUI.saveSettings(settings);
		directoryUI.saveSettings(settings);
		mainWindow.saveSettings(settings);
		
		try { settings.save(); }
		catch (IOException e) { log.error("cannot save settings", e); }
		try { cache.save(); }
		catch (IOException e) { log.error("cannot save cache", e); }
		
		log.info("finished");
	}
	
	//-------------------------------------------------------------------------
	//## actions
	
	/** Action: quit the program */
	private void doQuit() {
		exit();
		System.exit(0);
	}
	
	/** 
	 * Action: change to a new directory
	 * load and display imageUIs for all files in the new directory
	 */
	private void doChangeDirectory(final File directory) {
		final Task	old	= changeDirectoryTask;
		changeDirectoryTask	= new ChangeDirectoryTask(mainWindow, imageListUI, statusUI, thumbnails, directory);
		if (old != null)	old.replace(changeDirectoryTask);
		else				changeDirectoryTask.start();
	}
	
	/** Action: start uploading selected files */
	private void doStartUpload() {
		if (imageListUI.getData().getSelected().size() == 0) {
			log.info("uploading does not make sense when no file is selected");
			return;
		}
		final Task	old	= uploadFilesTask;
		uploadFilesTask	= new UploadFilesTask(mw, templates, mainWindow, commonUI.getData(), imageListUI.getData(), statusUI);
		if (old != null)	old.replace(uploadFilesTask);
		else				uploadFilesTask.start();
	}
	
	/** Action: stop uploading selected files */
	private void doStopUpload() {
		if (uploadFilesTask == null)	return;
		uploadFilesTask.abort();
	}
	
	//-------------------------------------------------------------------------
	//## init
	
	/** loads all family files from $settingsDir/family/ */
	private void addFamilies() {
		final File	dir		= settingsFile("family");
		if (!dir.exists()) {
			log.info("directory for additional wiki families not found: " + dir);
			return;
		}
		log.info("loading additional families from: " + dir);
		final File[]	files	= dir.listFiles();
		for (int i=0; i<files.length; i++) {
			final File	file	= files[i];
			if (!file.getName().endsWith(".family"))	continue;
			log.info("loading family: " + file);
			try {
				mw.loadFamily(file.toURI().toURL());
			}
			catch (ConfigException e) {
				log.error("could not load family from: " + file, e);
			}
			catch (MalformedURLException e) {
				log.error("malformed URL for family file: " + file, e);
			}
		}
	}

	/** load language file for the language or en if not successful and returns the used language */
	private void initMessages(String language) throws IOException {
		final URL	defaultURL	= loader.mandatoryURL("messages.properties");
		final URL	userLangURL	= loader.optionalURL("messages_" + language + ".properties");
		Messages.init(defaultURL, userLangURL);
	}
	
	/** load licenses */
	private List<LicenseData> initLicenses() throws IOException {
		final URL				url		= loader.mandatoryURL("licenses.txt");
		final ParsedLicenses	parsed	= new ParsedLicenses(url);

		final List<LicenseData>	out	= new ArrayList<LicenseData>();
//		out.add(new LicenseData("", ""));
		out.addAll(parsed.licenseDatas);
		return out;
	}
	
	/** loads and executes startup.bsh */
	private void sourceStartup() throws IOException {
		final URL	url	= loader.optionalURL("startup.bsh");
		if (url == null)	{ log.info("skipping, not found: startup.bsh"); return; }
		
		try {
			final Interpreter	interpreter	= new Interpreter();
			interpreter.set("mw", mw);
			
			Reader	in	= null;
			try {
				in	= new InputStreamReader(url.openStream(), "UTF-8");
				interpreter.eval(in, interpreter.getNameSpace(), url.toExternalForm());
			}
			finally {
				if (in != null)
				try { in.close(); }
				catch (Exception e) { log.error("cannot close", e); }
			}
		}
		catch (EvalError e) {
			log.error("could not load startup.bsh", e);
		}
	}
	
	//-------------------------------------------------------------------------
	//## resources
	
	/** returns a File in the settings directory */
	private File settingsFile(String path) {
		return new File(settingsDir, path); 
	}
}
