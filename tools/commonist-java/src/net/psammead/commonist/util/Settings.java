package net.psammead.commonist.util;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.Properties;

import net.psammead.util.Logger;

/** encapsulates a properties file */
public class Settings {
	private static final Logger log = new Logger(Settings.class);
	 
	private final File			file;
	private final Properties	properties;

	/** stores settings in a properties file */
	public Settings(File file) {
		this.file	= file;
		properties	= new Properties();
	}
	
	/** get a property or its default value */
	public String get(String name, String defaultValue) {
		final String	value	= properties.getProperty(name);
		return value != null ? value : defaultValue;
	}
	
	/** set a property */
	public void set(String name, String value) {
		properties.setProperty(name, value);
	}

	/** loads our properties file */
	public void load() throws IOException {
		if (!file.exists())	{ log.info("setting file does not exist: " + file.getPath()); return; }
		
		InputStream	in	= null;
		try {
			in	= new FileInputStream(file);
			properties.load(in);
			in.close();
		}
		finally {
			if (in != null) {
				try { in.close(); }
				catch (Exception e) { log.error("cannot close", e); }
			}
		}
	}

	/** saves our properties file */
	public void save() throws IOException {
		OutputStream	out	= null;
		try {
			out	= new FileOutputStream(file);
			properties.store(out, "the commonist");
			out.close();
		}
		finally {
			if (out != null) {
				try { out.close(); }
				catch (Exception e) { log.error("cannot close", e); }
			}
		}
	}
}
