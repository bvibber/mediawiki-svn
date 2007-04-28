package org.wikimedia.lsearch.util;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.net.ServerSocket;
import java.net.Socket;
import java.net.SocketException;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.StringTokenizer;
import java.util.Map.Entry;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;

/**
 * UDP Server for MWSearch extension. Receives update notification 
 * from MWSearchUpdateHook.php, and offers TCP interface 
 * (for luceneUpdate.php) to retrieve all articles updates since
 * last call to the daemon (FETCH NEW), or if there was a problem
 * with last round of update the previous updates (FETCH OLD). 
 * <p>
 * Daemon can be paired with another one (each one is hotspare for
 * the other). Using UDP signalling they try to manage aproximatelly 
 * the same queue status (maintaing exactly same status would require
 * much larger overhead). Hook should send update notifications to both.
 * Indexer should always use a single daemon, and revert to the other
 * only if the first is down. 
 * 
 * <p>
 * Each line in UDP packet is of syntax:<br> 
 * dbname [UPDATE|DELETE] namespace:title<br>
 * 
 * or for maintaining hotspares: dbname FETCHED
 * 
 * <p>
 * TCP query is of syntax:<br>
 * [FETCH|RESTORE] [NEW|OLD] [ON dbname]<br>
 * 
 * <p>
 * TCP response:<br>
 * dbname [UPDATE|DELETE] namespace:title<br>
 * ... 
 * 
 * @author rainman
 *
 */
public class RecentUpdatesDaemon {
	
	/** dbname -> ns:title -> operation  */
	protected Hashtable<String,Hashtable<String,String>> queue = new Hashtable<String,Hashtable<String,String>>();
	protected Hashtable<String,Hashtable<String,String>> oldqueue = new Hashtable<String,Hashtable<String,String>>();

	/** use this lock when modifying queue */
	protected Object lock = new Object();
	
	/** The UDP daemon that recieves update notifications from MediaWiki hook */
	class UDPServer extends Thread {
		org.apache.log4j.Logger log = Logger.getLogger(UDPServer.class);
		protected DatagramSocket socket = null;
		
		public UDPServer(){
			Configuration conf = Configuration.open();
			int udpPort = conf.getInt("RecentUpdateDaemon","udp",8111);
			try {
				socket = new DatagramSocket(udpPort);
				log.info("UDP server up at port "+udpPort);
			} catch (SocketException e) {
				log.fatal("Cannot make UDP server at port "+udpPort+" : "+e.getMessage());
			}
		}

		@Override
		public void run() {
			byte[] buf = new byte[1500];

			for(;;){
				// receive request
				DatagramPacket packet = new DatagramPacket(buf, buf.length);
				try {
					socket.receive(packet);				
					// handle request 
					String mesg = new String(packet.getData(),packet.getOffset(),packet.getLength(),"UTF-8");
					StringTokenizer st = new StringTokenizer(mesg,"\n\r");
					while(st.hasMoreTokens()){
						String s = st.nextToken();
						if(s.trim().equals(""))
							continue;
						String[] parts = s.split(" +",3);
						// check if it's request form other (hotspare) daemon
						if(parts.length == 2 && parts[1].equals("FETCHED")){
							if(queue.get(parts[0])!=null)
								oldqueue.put(parts[0],queue.remove(parts[0]));
							else
								oldqueue.remove(parts[0]);
							log.debug("Update for "+parts[0]+" fetched on other daemon");
							continue;
						// syntax check
						} else if(parts.length != 3){
							log.warn("Recieved bad syntax: "+s);
							continue;
						}
						String dbname = parts[0];
						String oper = parts[1];
						String title = parts[2];
						if(!oper.equals("UPDATE") && !oper.equals("DELETE")){
							log.warn("Unrecognized operation (should be UPDATE or DELETE): "+parts[2]);
							continue;
						}
						log.debug("Processing "+dbname+" "+oper+" "+title);
						// update queue
						synchronized(lock){
							Hashtable<String,String> titles = queue.get(dbname);
							if(titles == null){
								titles = new Hashtable<String,String>();
								queue.put(dbname,titles);
							}
							titles.put(title,oper);
						}					
					}
				} catch (IOException e) {
					log.warn("I/O error receiving UDP packet: "+e.getMessage());
				}
			}
		}


	}

	/** TCP worker thread, handles requests */
	class TCPDaemon extends Thread {
		org.apache.log4j.Logger log = Logger.getLogger(TCPDaemon.class);
		protected BufferedReader in;
		protected PrintWriter out;
		
		public TCPDaemon(Socket sock) {
			try {
				in = new BufferedReader(new InputStreamReader(sock.getInputStream()));
				out = new PrintWriter(sock.getOutputStream(),true);
			} catch (IOException e) {
				log.warn("Error openning input/output streams");
			}			
		}

		@Override
		public void run() {
			
			try {
				handle();				
			} catch (Exception e) {
				log.warn("Error processing request: "+e.getMessage());
			} finally{
				try {  out.close(); } catch(Exception e) { }
				try {  in.close(); } catch(Exception e) { }
			}
		}

		/** Single TCP request handler */ 
		protected void handle() throws IOException {
			String line = in.readLine();
			boolean fetchnew = false, notify = false;
			boolean restorenew = false;
			
			log.debug("Got request "+line);
			
			String db = null;
			if(line.contains("ON")){
				String[] p = line.split(" ");
				db = p[p.length-1];
			}
			
			if(line.startsWith("FETCH NEW")){
				fetchnew = true;
				notify = true;
			} else if(line.startsWith("RESTORE NEW"))
				restorenew = true;
			else if(line.startsWith("RESTORE OLD"))
				fetchnew = false;
			else if(line.startsWith("FETCH OLD"))
				fetchnew = false;
			else{
				log.warn("Invalid request: "+line);
				return;				
			}
			HashSet<String> changedDBs = new HashSet<String>();
			if(fetchnew){
				synchronized(lock){				
					if(db == null){
						changedDBs.addAll(oldqueue.keySet());
						changedDBs.addAll(queue.keySet());
						oldqueue = queue;
						queue = new Hashtable<String,Hashtable<String,String>>();
					} else if(queue.get(db)!=null){
						changedDBs.add(db);
						oldqueue.put(db,queue.remove(db));
					}
				}
			}
			
			// notify the backup daemon
			if(notify){
				for(String dbname : changedDBs)
					sendHotspareNotification(dbname+" FETCHED\n");
			}
			
			// respond
			if(restorenew){
				// need to clone queue to make its iterator thread-safe
				Hashtable<String,Hashtable<String,String>> q = (Hashtable<String, Hashtable<String, String>>) queue.clone();
				for(Entry<String,Hashtable<String,String>> et : q.entrySet()){
					write(et.getKey(),(Hashtable<String, String>)et.getValue().clone());
				}
			} else if(db!=null && oldqueue.get(db)!=null){
				write(db,oldqueue.get(db));
			} else if(db==null){
				for(Entry<String,Hashtable<String,String>> et : oldqueue.entrySet()){
					write(et.getKey(),et.getValue());
				}
			}
		}
		
		/** Write out one db hashtable as: dbname operation ns:title */
		protected void write(String db, Hashtable<String,String> titles){
			if(titles == null)
				return;
			for(Entry<String,String> to : titles.entrySet()){
				String line = db+" "+to.getValue()+" "+to.getKey();
				log.debug("<<< "+line);
				out.println(line);
			}
		}
		
		/** Send UDP packet to hotspare RecentUpdatesDaemon keeping it in sync */
		public void sendHotspareNotification(String data){
			if(hotspareHost==null || hotspareUdpPort==0)
				return; // no hotspare
			try{
				if(udpSocket == null)
					udpSocket = new DatagramSocket();
				byte[] buf = data.getBytes();
				InetAddress address = InetAddress.getByName(hotspareHost);
				DatagramPacket packet = new DatagramPacket(buf, buf.length, address, hotspareUdpPort);
				udpSocket.send(packet);
			} catch(Exception e){
				log.warn("Error sending datagram to hotspare: "+e.getMessage());
			}
	      
		}
	
	}
	
	/** Recieves TCP connections */
	class TCPServer extends Thread {
		org.apache.log4j.Logger log = Logger.getLogger(TCPServer.class);
		@Override
		public void run() {
			int maxThreads = 10;
			ServerSocket sock;
			
			Configuration config = Configuration.open();
			int port = config.getInt("RecentUpdateDaemon","tcp",8112);
			try {
				sock = new ServerSocket(port);
			} catch (Exception e) {
				log.fatal("Cannot make TCP server at port "+port+" : " + e.getMessage());
				return;
			}
			ExecutorService pool = Executors.newFixedThreadPool(maxThreads);			
			log.info("TCP server up at port "+port);
			
			for (;;) {
				Socket client;
				try {
					client = sock.accept();
				} catch (Exception e) {
					log.error("accept() error: " + e.getMessage());
					continue;
				}
				
				TCPDaemon worker = new TCPDaemon(client);
				pool.execute(worker);
			}
			
		}
		
	}
	protected DatagramSocket udpSocket=null;
	protected String hotspareHost=null;
	protected int hotspareUdpPort, hotspareTcpPort;
	
	/** Fetch queue from hotspare RecentUpdatesDaemon using command */
	public Hashtable<String,Hashtable<String,String>> fetchQueue(String command){
		Hashtable<String,Hashtable<String,String>> res = new Hashtable<String,Hashtable<String,String>>();
		
      Socket socket = null;
      PrintWriter out = null;
      BufferedReader in = null;

      try {
      	socket = new Socket(hotspareHost, hotspareTcpPort);
      	out = new PrintWriter(socket.getOutputStream(), true);
      	in = new BufferedReader(new InputStreamReader(socket.getInputStream()));

      	out.println(command);

      	String line;
      	int count=0;
      	while((line = in.readLine()) != null){
      		String[] parts = line.split(" ",3);
      		String dbname = parts[0];
      		String oper = parts[1];
      		String title = parts[2];

      		Hashtable<String,String> titles = res.get(dbname);
      		if(titles == null){
      			titles = new Hashtable<String,String>();
      			res.put(dbname,titles);
      		}
      		titles.put(title,oper);
      		count++;
      	}
      	System.out.println("Retrieved queue of size "+count+" from hotspare daemon");
      	out.close();
      	in.close();
      	socket.close();
      } catch (IOException e) {
      	System.out.println("Warning: Could not get queue from the hotspare daemon at "+hotspareHost+": "+e.getMessage());
      }
      return res;
	}
	
	public RecentUpdatesDaemon(){
		Configuration config = Configuration.open(); // init log4j
		hotspareHost = config.getString("RecentUpdateDaemon","hostspareHost");
		hotspareUdpPort = config.getInt("RecentUpdateDaemon","hostspareUdpPort",8111);
		hotspareTcpPort = config.getInt("RecentUpdateDaemon","hostspareTcpPort",8112);
		
		// try to restore queues from the hotspare
		if(hotspareHost != null){
			queue = fetchQueue("RESTORE NEW");
			oldqueue = fetchQueue("RESTORE OLD");
		}
		
		new UDPServer().start();
		new TCPServer().start();
	}
	
	public static void main(String args[]){
		new RecentUpdatesDaemon();
	}
	
	
}
