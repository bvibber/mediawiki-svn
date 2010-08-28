package net.psammead.util.classurl;

import java.net.*;

/** 
 * an URLStreamHandlerFactory beign a ClassURLStreamHandler factory<br>
 * <br>
 * usage: URL.setURLStreamHandlerFactory(new ClassURLStreamHandlerFactory());
 */
public class ClassURLStreamHandlerFactory implements URLStreamHandlerFactory {
	/**
	 * create an URLStreamHandler for the specified protocol.
	 * @param   protocol   the protocol
	 * @return  an URLStreamHandler
	 */
	public URLStreamHandler createURLStreamHandler(String protocol) {
		if (protocol.equals("class"))	return new ClassURLStreamHandler();
		else							return null;
	}
}
