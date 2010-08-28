package net.psammead.util.classurl;

import java.io.*;
import java.net.*;

/** an URLStreamHandler being a ClassURLConnection factory */ 
public class ClassURLStreamHandler extends URLStreamHandler {
	/** 
	 * open a connection the the referenced object 
	 * @param      url	the URL to connect to
	 * @return     an URLConnection
	 * @exception  IOException
	 */ 
	@Override
	protected URLConnection openConnection(URL url) throws IOException {
		return new ClassURLConnection(url);
	}
}
