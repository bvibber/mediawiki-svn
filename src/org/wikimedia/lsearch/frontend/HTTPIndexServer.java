/*
 * Created on Feb 3, 2007
 *
 */
package org.wikimedia.lsearch.frontend;

import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;

/**
 * Starts up the HTTP frontend for indexer. 
 * 
 * @author rainman
 *
 */
public class HTTPIndexServer extends Thread {
	public static boolean serviceReady = false;
	
	
	
	/* (non-Javadoc)
	 * @see java.lang.Runnable#run()
	 */
	public void run() {
		startServer();
	}
	
	public static void startServer(){
		int maxThreads = 25;
		ServerSocket sock;
		
		Configuration config = Configuration.open();
		org.apache.log4j.Logger log = Logger.getLogger(HTTPIndexServer.class);
		
		int port = config.getInt("Index","port",8321);
		
		try {
			sock = new ServerSocket(port);
		} catch (Exception e) {
			log.fatal("Dying: bind error: " + e.getMessage());
			return;
		}
		
		ExecutorService pool = Executors.newFixedThreadPool(maxThreads);
		
		log.info("Started server at port "+port);
		
		for (;;) {
			Socket client = null; 
			try {
				log.debug("Listening...");
				serviceReady = true;
				client = sock.accept();
			} catch (Exception e) {
				log.error("accept() error: " + e.getMessage());
				// be sure to close all sockets
				if(client != null){
					try{ client.getInputStream().close(); } catch(Exception e1) {}
					try{ client.getOutputStream().close(); } catch(Exception e1) {}
					try{ client.close(); } catch(Exception e1) {}
				}
				continue;
			}
			
			int threadCount = HTTPIndexDaemon.getOpenCount();
			if (threadCount > maxThreads) {
				//stats.add(false, 0, threadCount);
				log.error("too many connections, skipping a request");
				try {
					client.close();
				} catch (IOException e1) {
				}				
			} else {
				HTTPIndexDaemon worker = new HTTPIndexDaemon(client);
				//worker.setPriority(Thread.MAX_PRIORITY);
				pool.execute(worker);
			}
		}

	}
	
	public static void main(String[] args) {
		startServer();
	}
}
