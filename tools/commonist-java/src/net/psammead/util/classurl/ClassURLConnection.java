package net.psammead.util.classurl;

import java.io.*;
import java.net.*;

/** this URLConnection handles URLs like
 * class:/path/to/resource or
 * class://package.of.anchor.Class/path/to/resource.file
 * by accessing streams for resources (the 'path') from the ClassLoader 
 * of a Class (the 'hostname'). <br>
 * <br>
 * leaving out the hostname resolves to the 
 * ClassLoader of the defaultAnchor Class
 */
public class ClassURLConnection extends URLConnection {
	/** the ClassLoader of this Class is used when the hostname in the URL is missing */
	private static String	defaultAnchor	= null;

	/**
	 * sets the Class whose ClassLoader is used to load resources
	 * without a hostname to be used as the name of the anchor Class.
	 * @param   className   the name of the Class whose ClassLoader is to be used
	 */
	public static void setDefaultAnchor(String className) {
		defaultAnchor	= className;
	}

	/** the ClassLoader of this Class is used to get the InputStream */
	private Class<?>	anchorClass	= null;

	/** 
	 * create a connection to the URL, but do not connect
	 * @param   url   the URL you want to connect to
	 */
	public ClassURLConnection(URL url) {
		super(url);
	}

	/** 
	 * returns the value of the content-type header field
	 * @return  the content-type or null if unknoen
	 */
	@Override
	public String getContentType() {
		return guessContentTypeFromName(url.getPath());
	}

	/** 
	 * returns an InputStream from the connection, if needed connect first
	 * @return     the InputStream
	 * @exception  IOException
	 */
	@Override
	public synchronized InputStream getInputStream() throws IOException {
		if (!connected)	connect();
		final String	path1	= url.getPath();
		final String	path	= path1.startsWith("/") ? path1.substring(1) : path1;
		//return anchorClass.getClassLoader().getResourceAsStream(path);
		final InputStream	stream	= anchorClass.getClassLoader().getResourceAsStream(path);
		if (stream == null)	throw new IOException("cannot get stream for url: " + url + " from anchor: " + anchorClass.getName());
		return stream;

	}

	/** connects to the resource referenced by this connection 
	 * @exception  IOException	the class could not be found
	 */
	@Override
	public synchronized void connect() throws IOException {
		String	className	= url.getHost();
		if ("".equals(className)) {
			if (defaultAnchor == null)	throw new IOException("defaultAnchor must be set before using urls without a hostname"); 
			className	= defaultAnchor;
		}
		try {
			anchorClass	= Class.forName(className);
			connected	= true;
		}
		catch (ClassNotFoundException e) {
			IOException	e2	= new IOException("Class " + className + " not found, cannot connect to URL " + url);
			e2.initCause(e);	throw e2;
		}
	}
}