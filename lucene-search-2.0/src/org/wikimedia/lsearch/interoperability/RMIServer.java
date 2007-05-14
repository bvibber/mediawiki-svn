package org.wikimedia.lsearch.interoperability;

import java.rmi.Remote;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.rmi.server.UnicastRemoteObject;

import org.apache.log4j.Logger;
import org.apache.lucene.search.RemoteSearchableMul;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.SearcherCache;

/** Starts the RMI registry and binds all RMI objects */
public class RMIServer {
	protected static org.apache.log4j.Logger log = Logger.getLogger(RMIServer.class);
	
	protected static SearcherCache cache = null;
	
	public static void register(Remote engine, String name){
		try {
         Registry registry = LocateRegistry.getRegistry();
         registry.rebind(name, UnicastRemoteObject.exportObject(engine, 0));
         log.info(name+" bound");
		} catch (Exception e) {
			e.printStackTrace();
         log.warn("Cannot bind "+name+" exception:"+e.getMessage());
     }
	}
	
	public static void register(UnicastRemoteObject engine, String name){
		try {
         Registry registry = LocateRegistry.getRegistry();
         registry.rebind(name, engine);
         log.info(name+" bound");
		} catch (Exception e) {
			e.printStackTrace();
         log.warn("Cannot bind "+name+" exception:"+e.getMessage());
     }
	}
	
	/** After updating local copy of iid, rebind it's rmi object */
	public static void rebind(IndexId iid){
		if(cache == null)
			cache = SearcherCache.getInstance();
		String name = "RemoteSearchable<"+iid+">";
		try {
			RemoteSearchableMul rs = new RemoteSearchableMul(cache.getLocalSearcher(iid));
			register(rs,name);			
		} catch (RemoteException e) {
			log.warn("Error making remote searchable for "+name);
		} catch(Exception e){
			// do nothing, error is logged by some other class (possible SearchCache)
		}
	}

	/** Bind all RMI objects (Messenger, RemoteSeachables and RMIIndexDaemon) */
	public static void bindRMIObjects(){
		register(RMIMessengerImpl.getInstance(),"RMIMessenger");
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		for(IndexId iid : global.getMySearch()){
			if(!iid.isLogical())
				rebind(iid);			
		}		
	}
	
	public static void createRegistry(){
		try {
			 java.rmi.registry.LocateRegistry.createRegistry(1099);
			 System.out.println("RMI registry started.");
		  } catch (Exception e) {
			 // probably already running
		  }	
	}
}
