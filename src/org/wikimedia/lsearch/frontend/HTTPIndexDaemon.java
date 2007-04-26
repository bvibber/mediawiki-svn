/*
 * Created on Feb 2, 2007
 *
 */
package org.wikimedia.lsearch.frontend;

import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.net.Socket;
import java.util.ArrayList;
import java.util.Collection;

import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.util.QueryStringMap;

/**
 * HTTP frontend for {@link IndexDaemon}. Makes use of HTTP POST method to transfer articles,
 * and URLs to encode functions.  
 * 
 * @author rainman
 *
 */
public class HTTPIndexDaemon extends HttpHandler {
	
	public static IndexDaemon daemon = null;
	
	public HTTPIndexDaemon(Socket sock) {
		super(sock);
		if(daemon == null)
			daemon = new IndexDaemon();
	}
	
	/** 
	 * Hardcoded remote method invocation, hardcoded parameter extraction
	 */
	protected void processRequest() {
		QueryStringMap query = new QueryStringMap(uri);
		String methodName = uri.getPath().substring(1);
		
		// custom "bean" mapping
		// namespace + title are by default packed into Title object
		if(query.get("namespace")!=null && query.get("title")!=null){
			String namespace = (String)query.get("namespace");
			String titleText = (String)query.get("title");
			Title title = new Title(Integer.parseInt(namespace),titleText);
			query.remove("namespace");
			query.put("title",title);
		}
		
		// attempt to read text variable from POST content
		if(postData!=null){
			query.put("text",postData);
		}
		
		Collection keys = query.keySet();
		// find the method of class update daemon
		Class[] paramTypes = null;
		Object[] params = null;
		if(keys.size()!=0){
			paramTypes = new Class[keys.size()];
			params = new Object[keys.size()];
			Object[] keyStrings = keys.toArray();
			
			for(int i=0;i<keyStrings.length;i++){
				String key = (String)keyStrings[i]; 
				if(key.equals("title"))
					paramTypes[i] = Title.class;
				else
					paramTypes[i] = String.class;
				
				params[i] = query.get(key);
			}
		}
		try {
			Method method = daemon.getClass().getMethod(methodName,paramTypes);
			
			Object ret = method.invoke(daemon,params);
						
			if(ret!=null){
				// assume string return value, send it back
				String retVal = (String) ret;
				byte[] bytes = retVal.getBytes();
				sendHeaders(200,"OK",bytes.length);
				sendBytes(bytes);				
			} else{
				sendHeaders(200,"OK");
			}
			
		} catch (SecurityException e) {
			log.error("Called method "+methodName+" which is not visible");
			sendHeaders(400,"Bad Request");
		} catch (NoSuchMethodException e) {
			log.error("Called unrecognized method "+methodName+". Uri was: "+uri);
			sendHeaders(404,"Not Found");
		} catch (IllegalArgumentException e) {
			log.error("Called method "+methodName+" with illegel arguments");
			sendHeaders(400,"Bad Request");
		} catch (IllegalAccessException e) {
			log.error("Cannot call method "+methodName+", illegal access.");
			sendHeaders(400,"Bad Request");
		} catch (InvocationTargetException e) {
			e.printStackTrace();
			log.error("Error while calling method "+methodName+": invocation target exception");
			sendHeaders(400,"Bad Request");
		}
	}
	
	// never use keepalive
	public boolean isKeepAlive(){
		return false;
	}
}
