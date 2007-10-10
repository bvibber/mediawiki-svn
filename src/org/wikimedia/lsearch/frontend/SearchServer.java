/*
 * Copyright 2004 Kate Turner
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * $Id: MWDaemon.java 8447 2005-04-20 02:50:50Z vibber $
 */
package org.wikimedia.lsearch.frontend;

import java.net.ServerSocket;
import java.net.Socket;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.statistics.Statistics;
import org.wikimedia.lsearch.statistics.StatisticsThread;

/**
 * Starts up the HTTP frontend for searcher. 
 * 
 * @author Kate Turner
 *
 */
public class SearchServer extends Thread {
	private static int port = 8123;
	private static int maxThreads = 25;
	private static ServerSocket sock;
	public static String indexPath;
	public static String[] dbnames;
	private static Configuration config;
	public static int numthreads;
	
	// milliseconds running average & ganglia period
	public final static int statsPeriod = 60000;
	public static Statistics stats = null;
	private static Thread statsThread;
	
	public void startServer(){
		config = Configuration.open();
		/** Logger */
		org.apache.log4j.Logger log = Logger.getLogger(SearchServer.class);
		
		log.info("Binding server to port " + port);
		
		try {
			sock = new ServerSocket(port);
		} catch (Exception e) {
			log.fatal("Error: bind error: " + e.getMessage());
			return;
		}
		
		String max = config.getString("Daemon", "maxworkers");
		if (max != null)
			maxThreads = Integer.parseInt(max);
		
		// Initialise statistics
		stats = new Statistics(1000, statsPeriod);
		if (config.getBoolean("Ganglia", "report")) {
			log.info("Starting ganglia statistics thread...");
			// Run a background thread to push our runtime stats to Ganglia
			statsThread = new StatisticsThread();
			String gangliaPort = config.getString("Ganglia", "port");
			if (gangliaPort != null)
				stats.GangliaPort = Integer.parseInt(gangliaPort);
			String gangliaInterface = config.getString("Ganglia", "interface");
			if (gangliaInterface != null)
				stats.GangliaInterface = gangliaInterface;
			statsThread.start();
		}
		
		ExecutorService pool = Executors.newFixedThreadPool(maxThreads);
		
		for (;;) {
			Socket client = null;
			try {
				log.debug("Listening...");
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
			
			int threadCount = SearchDaemon.getOpenCount();
			if (threadCount > maxThreads) {
				stats.add(false, 0, threadCount);
				log.error("too many connections, skipping a request");
				// be sure to close all sockets
				if(client != null){
					try{ client.getInputStream().close(); } catch(Exception e1) {}
					try{ client.getOutputStream().close(); } catch(Exception e1) {}
					try{ client.close(); } catch(Exception e1) {}
				}
				continue;
			} else {
				SearchDaemon worker = new SearchDaemon(client);
				pool.execute(worker);
			}
		}
	}
	
	/* (non-Javadoc)
	 * @see java.lang.Runnable#run()
	 */
	public void run() {
		startServer();
	}
	
	public static void main(String[] args) {
		System.out.println(
				"MediaWiki Lucene search indexer - runtime search daemon.\n"
				);
		int i = 0;
		while (i < args.length) {
			if (args[i].equals("-port")) {
				port = Integer.valueOf(args[++i]).intValue();
			} else if (args[i].equals("-configfile")) {
				Configuration.setConfigFile(args[++i]);
			} else break;
			++i;
		}
	}
}
