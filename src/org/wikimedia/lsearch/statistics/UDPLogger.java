package org.wikimedia.lsearch.statistics;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.net.SocketException;
import java.net.UnknownHostException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Random;
import java.util.TimeZone;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;

public class UDPLogger {
	Logger log = Logger.getLogger(UDPLogger.class);
	static private UDPLogger instance = null;
	protected int port = 51234;
	protected String host = "localhost";
	protected ArrayList<String> queryQueue = new ArrayList<String>();
	protected boolean disabled = true;
	protected InetAddress dest;
	
	protected Object lock = new Object();
	
	public static synchronized UDPLogger getInstance(){
		if(instance == null){
			instance = new UDPLogger();
		}
		return instance;
	}
	
	private UDPLogger(){
		Configuration config = Configuration.open();
		host = config.getString("UDPLogger", "host", null);
		port = config.getInt("UDPLogger", "port", 0);
		
		// do a sanity check, see if udp logging is enabled at all
		if(host!=null && port>0)
			disabled = false;
		
		dest = null;
		try {
			dest = InetAddress.getByName(host);
		} catch (UnknownHostException e1) {
			log.error("Cannot find destination host "+host,e1);
			return;
		}
		
	}
	
	protected void sendLog(String query){
		DatagramSocket socket;
		DatagramPacket packet;
		
		try{
			socket = new DatagramSocket();
			
			byte[] raw = query.getBytes("UTF-8");
			packet = new DatagramPacket(raw, raw.length, dest, port);
			socket.send(packet);
		} catch (SocketException e1) {
			log.warn("Socket problem in connecting to host="+host+", port="+port, e1);
		} catch (UnsupportedEncodingException e) {
			log.error("",e);
		} catch (IOException e) {
			log.warn("IO Exception in connecting to host="+host+", port="+port, e);
		}
	}
	
	/** Queue a query for logging */
	public void log(String dbname, String query){
		// if disabled just do nothing 
		if(disabled)
			return;

		// output in iso8601
		Calendar currentDate = Calendar.getInstance();		
		SimpleDateFormat formatter = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");
		String date = formatter.format(currentDate.getTime());
		
		synchronized(lock){
			// make sure queries are of reasonable size
			if(query.length() > 1024)
				query = query.substring(0, 1024);
			
			// replace any newlines and such
			query = query.replace("\n", " ").replace("\r", " ");
			
			sendLog(dbname+" "+date+" "+query.trim()+"\n");
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
