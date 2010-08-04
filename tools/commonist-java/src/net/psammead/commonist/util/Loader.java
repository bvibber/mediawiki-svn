package net.psammead.commonist.util;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;

import net.psammead.util.Logger;

/** loads resources from a set of URL-paths */
public final class Loader {
	private static final Logger log = new Logger(Loader.class);

	private final File	settingsDir;
	private final File	projectDir;
	private final String	resourcePrefix;

	public Loader(File settingsDir, File projectDir, String resourcePrefix) {
		this.settingsDir	= settingsDir;
		this.projectDir		= projectDir;
		this.resourcePrefix	= resourcePrefix;
	}
	
	/** returns a resourceURL or throws an IOException */
	public URL mandatoryURL(String path) throws IOException {
		final URL	url	= optionalURL(path);
		if (url == null)	throw new IOException("cannot locate: " + path);
		return url;
	}
	
	/** returns null when a resource does not exist */
	public URL optionalURL(String path) {
		try {
			final File	settingsFile	= new File(settingsDir, path);
			if (settingsFile.exists()) {
				log.debug("loading from settings: " + path);
				return settingsFile.toURI().toURL();
			}
		}
		catch (MalformedURLException e) {
			log.error("cannot examine", e);
		}
		
		try {
			final File	projectFile	= new File(projectDir, path);
			if (projectFile.exists()) {
				log.debug("loading from project: " + path);
				return projectFile.toURI().toURL();
			}
		}
		catch (MalformedURLException e) {
			log.error("cannot examine", e);
		}
		
		final URL	url	= getClass().getResource(resourcePrefix + path);
		if (url != null) {
			log.debug("loading from classloader: " + path);
			return url;
		}
		
		return null;
	}
}
