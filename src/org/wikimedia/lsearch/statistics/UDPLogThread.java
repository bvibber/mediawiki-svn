package org.wikimedia.lsearch.statistics;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.net.SocketException;
import java.net.UnknownHostException;
import java.util.ArrayList;
import java.util.Random;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;

public class UDPLogThread extends Thread {
	Logger log = Logger.getLogger(UDPLogThread.class);
	static private UDPLogThread instance = null;
	protected int port = 51234;
	protected String host = "localhost";
	protected ArrayList<String> queryQueue = new ArrayList<String>();
	protected boolean disabled = true;
	
	protected Object lock = new Object();
	
	public static synchronized UDPLogThread getInstance(){
		if(instance == null){
			instance = new UDPLogThread();
			if(! instance.disabled )
				instance.start();
		}
		return instance;
	}
	
	private UDPLogThread(){
		Configuration config = Configuration.open();
		host = config.getString("UDPLogger", "host", null);
		port = config.getInt("UDPLogger", "port", 0);
		
		// do a sanity check, see if udp logging is enabled at all
		if(host!=null && port>0)
			disabled = false;
		
	}

	@Override
	public void run() {
		log.info("UDP logger started");
		
		Random r = new Random();
		InetAddress dest = null;
		try {
			dest = InetAddress.getByName(host);
		} catch (UnknownHostException e1) {
			log.error("Cannot find destination host "+host,e1);
			return;
		}
		
		DatagramSocket socket;
		DatagramPacket packet;
		
		while(true){
			try {
				socket = new DatagramSocket();
				
				// send queries in separate packets
				ArrayList<String> queries = getQueries();
				for(String q : queries){
					byte[] raw = q.getBytes("UTF-8");
					packet = new DatagramPacket(raw, raw.length, dest, port);
					socket.send(packet);
				}
				
			} catch (SocketException e1) {
				log.warn("Socket problem in connecting to host="+host+", port="+port, e1);
			} catch (UnsupportedEncodingException e) {
				log.error("",e);
			} catch (IOException e) {
				log.warn("IO Exception in connecting to host="+host+", port="+port, e);
			}
			
			// sleep between one and two seconds
			try {
				Thread.sleep(1000 + r.nextInt(1000));
			} catch (InterruptedException e) {
				log.warn("UDPLogThread interrupted", e);
			}
			
		}
	}
	
	/** Queue a query for logging */
	public void log(String dbname, String query){
		// if disabled just do nothing 
		if(disabled)
			return;
		
		synchronized(lock){
			// make sure queries are of reasonable size
			if(query.length() > 1024)
				query = query.substring(0, 1024);
			
			// replace any newlines and such
			query = query.replace("\n", " ").replace("\r", " ");
			
			queryQueue.add(dbname+" "+query.trim()+"\n");
		}
	}
	
	/** Fetch queries so far and empty the waiting queue */
	protected ArrayList<String> getQueries(){
		synchronized(lock){
			ArrayList<String> ret = queryQueue;
			queryQueue = new ArrayList<String>();
			return ret;
		}
	}
}
