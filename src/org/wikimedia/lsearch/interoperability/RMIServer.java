package org.wikimedia.lsearch.interoperability;

import java.io.IOException;
import java.rmi.NotBoundException;
import java.rmi.Remote;
import java.rmi.RemoteException;
import java.rmi.registry.LocateRegistry;
import java.rmi.registry.Registry;
import java.rmi.server.UnicastRemoteObject;

import org.apache.log4j.Logger;
import org.apache.lucene.search.RemoteSearchableMul;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.SearcherCache;

/** Starts the RMI registry and binds all RMI objects */
public class RMIServer {
	protected static org.apache.log4j.Logger log = Logger.getLogger(RMIServer.class);
	
	protected static SearcherCache cache = null;
	protected static IndexRegistry indexes = null; 
	
	public static void register(Remote engine, String name){
		try {
         Registry registry = LocateRegistry.getRegistry();
         registry.rebind(name, UnicastRemoteObject.exportObject(engine, 0));
         log.info(name+" bound");
		} catch (Exception e) {
			e.printStackTrace();
         log.warn("Cannot bind "+name+" exception:"+e.getMessage(),e);
     }
	}
	
	public static void register(UnicastRemoteObject engine, String name){
		try {
         Registry registry = LocateRegistry.getRegistry();
         registry.rebind(name, engine);
         log.info(name+" bound");
		} catch (Exception e) {
			e.printStackTrace();
         log.warn("Cannot bind "+name+" exception:"+e.getMessage(),e);
     }
	}
	
	/** Unbind a searcher pool */
	public static boolean unbind(IndexId iid, IndexSearcherMul[] pool){
		String name = "RemoteSearchable<"+iid+">";
		
		if(pool == null)
			return true;		
		Registry registry;
		try {
			for(int i=0;i<pool.length;i++){
				registry = LocateRegistry.getRegistry();
				registry.unbind(name+"$"+i);
			}
			return true;
		} catch (RemoteException e) {
			log.warn("Remote error unbinding iid="+iid,e);
		} catch (NotBoundException e) {
		}
		return false;		
	}
	
	/** After updating local copy of iid, rebind its rmi object */
	public static boolean rebind(IndexId iid){
		if(cache == null)
			cache = SearcherCache.getInstance();
		if(indexes == null)
			indexes = IndexRegistry.getInstance();
		try {
			IndexSearcherMul[] pool = cache.getLocalSearcherPool(iid);
			if(pool != null)
				unbind(iid,pool);
			if(indexes.getCurrentSearch(iid) != null){
				bind(iid,pool);
			}
			return true;
		} catch(IOException e){
			log.warn("Error rebinding searchers for "+iid+" : "+e.getMessage(),e);
			e.printStackTrace();
		}
		return false;
	}
	
	/** Bind a brand new search pool */
	public static boolean bind(IndexId iid, IndexSearcherMul[] pool){
		String name = "RemoteSearchable<"+iid+">"; 
		try {
			for(int i=0;i<pool.length;i++){
				RemoteSearchableMul rs = new RemoteSearchableMul(pool[i]);
				register(rs,name+"$"+i);		
			}
			return true;
		} catch (RemoteException e) {
			e.printStackTrace();
			log.warn("Error binding searchable with basename "+name+" : "+e.getMessage(),e);
		} catch(Exception e){
			e.printStackTrace();
		}
		return false;
	}

	/** Bind all RMI objects (Messenger, RemoteSeachables and RMIIndexDaemon) */
	public static void bindRMIObjects(){
		register(RMIMessengerImpl.getInstance(),"RMIMessenger");
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
