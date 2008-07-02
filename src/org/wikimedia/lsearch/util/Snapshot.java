package org.wikimedia.lsearch.util;

import java.rmi.RemoteException;

import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.interoperability.RMIMessengerClient;

public class Snapshot {
	
	public static void printHelp(){
		System.out.println("Utility to request and wait for indexes to be get snapshoted");
		System.out.println("Syntax: Snapshot [-pre] [-h <host>] [-p <pattern>] [-no]");
		System.out.println("  -pre           - snapshot precursors");
		System.out.println("  -h <hostname>  - target indexer host" );
		System.out.println("  -p <pattern>   - wildcard pattern to match indexes" );
		System.out.println("  -no            - no optimization" );
	}

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// config
		Configuration.open();
		GlobalConfiguration.getInstance();
		
		String host = "localhost";
		String pattern = "*";
		boolean precursor = false;
		boolean optimize = true;
		
		for(int i=0;i<args.length;i++){
			if(args[i].equals("-h"))
				host = args[++i];
			else if(args[i].equals("-p"))
				pattern = args[++i];
			else if(args[i].equals("--help")){
				printHelp();
				return;
			} else if(args[i].equals("-pre"))
				precursor = true;
			else if(args[i].equals("-no"))
				optimize = false;
			else{
				System.out.println("Unknown options "+args[i]);
				return;
			}
		}
		
		RMIMessengerClient messenger = new RMIMessengerClient(true);
		
		System.out.println("Sending request to "+host+" to snapshot "+pattern+" (pre="+precursor+", optimize="+optimize+")");
		try {
			messenger.requestSnapshotAndNotify(host,optimize,pattern,precursor);
			System.out.println("Waiting for snapshot process to complete...");
			
			while( !messenger.snapshotFinished(host,optimize,pattern,precursor) ){
				try {
					Thread.sleep(3000);
				} catch (InterruptedException e) {
					e.printStackTrace();
				}
			}
			
			System.out.println("Finished!");
		} catch (RemoteException e) {
			e.printStackTrace();
			System.exit(1);
		}

	}

}
