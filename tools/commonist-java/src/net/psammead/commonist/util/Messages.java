package net.psammead.commonist.util;

import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.text.MessageFormat;
import java.util.Properties;

import net.psammead.util.IOUtil;

/** encapsulates user messages loaded from a properties document */
public class Messages {
	private static Messages	SELF	= null;
	
	/** userLangURL may be null */
	public static void init(URL defaultURL, URL userLangURL) throws IOException {
		SELF	= new Messages(defaultURL, userLangURL);
	}
	
	public static String text(String key) {
		return SELF.getText(key);
	}
	
	public static String message(String key, Object[] args) {
		return SELF.getMessage(key, args);
	}
	
	//------------------------------------------------------------------------------
	
	private final Properties	defaultProps;
	private final Properties	userLangProps;
	
	/** fully static utility class, shall not be instantiated */
	private Messages(URL defaultURL, URL userLangURL) throws IOException {
		defaultProps	= load(defaultURL);
		userLangProps	= load(userLangURL);
	}

	public String getText(String key) {
		return get(key);
	}
	
	public String getMessage(String key, Object[] args) {
		final String	message	= get(key);
		return MessageFormat.format(message, args);
	}
	
	private String get(String key) {
		String	text1	= userLangProps.getProperty(key);
		if (text1 != null)	return text1;
		String	text2	= defaultProps.getProperty(key);
		if (text2 != null)	return text2;
		throw new RuntimeException("message: " + key + " not available");
	}
	
	/** returns an empty Properties object if the url is null */
	private Properties load(URL url) throws IOException {
		final Properties	props	= new Properties();
		if (url == null)	return props;
		
		InputStream	in		= null;
		try {
			in	= url.openStream();
			props.load(in);
		}
		finally {
			IOUtil.closeSilent(in);
		}
		return props;
	}
}
