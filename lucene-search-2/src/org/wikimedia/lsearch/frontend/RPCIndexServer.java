/*
 * Created on Jan 26, 2007
 *
 */
package org.wikimedia.lsearch.frontend;
import java.io.IOException;

import org.apache.log4j.Logger;
import org.apache.xmlrpc.XmlRpcException;
import org.apache.xmlrpc.server.PropertyHandlerMapping;
import org.apache.xmlrpc.server.XmlRpcServer;
import org.apache.xmlrpc.server.XmlRpcServerConfigImpl;
import org.apache.xmlrpc.webserver.WebServer;
import org.wikimedia.lsearch.config.Configuration;


/**
 * Starts up the XMLRPC frontend for indexer. 
 * 
 * @author rainman
 *
 */
public class RPCIndexServer extends Thread {

	/* (non-Javadoc)
	 * @see java.lang.Runnable#run()
	 */
	public void run() {
		startServer();
	}
	
    public static void startServer(){
    	org.apache.log4j.Logger log = Logger.getLogger(RPCIndexServer.class);
    	try{
    		Configuration config = Configuration.open();
    		int port = config.getInt("Index","port",8321);    		
    		
    		WebServer webServer = new WebServer(port);
    		
    		XmlRpcServer xmlRpcServer = webServer.getXmlRpcServer();
    		
    		// RPC services mapping is in the property file
    		PropertyHandlerMapping phm = new PropertyHandlerMapping();
    		phm.load(Thread.currentThread().getContextClassLoader(),"org/apache/xmlrpc/webserver/XmlRpcServlet.properties");        
    		xmlRpcServer.setHandlerMapping(phm);
    		
    		// make a simple web server
    		XmlRpcServerConfigImpl serverConfig =
    			(XmlRpcServerConfigImpl) xmlRpcServer.getConfig();
    		serverConfig.setEnabledForExtensions(true);
    		serverConfig.setContentLengthOptional(false);
    		
    		webServer.start();
    		System.out.println("Started webserver at port "+port);
    	} catch(Exception e){
    		e.printStackTrace();
    		log.fatal("Dying: error starting up XMLRPC Server, check configuration");
			return;
		}
    }
    
    public static void main(String[] args) throws Exception {
    	startServer();    	
    }
}

