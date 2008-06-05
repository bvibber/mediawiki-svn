package org.wikimedia.lsearch.index;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.Properties;
import java.util.concurrent.locks.Lock;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Command;
import org.wikimedia.lsearch.util.FSUtils;

/**
 * Simple transaction support for indexing. Wrap index operations by 
 * this class.
 * 
 * Current implementation: make a hard-linked copy of index. Note that
 * this assumes single indexer at any time, and thus single transaction
 * at a time. Also, this is not a very portable way of doing transactions.
 * 
 * @author rainman
 *
 */
public class Transaction {
	static Logger log = Logger.getLogger(Transaction.class);
	protected IndexId iid;
	protected boolean inTransaction;
	protected IndexId.Transaction type;
	protected Lock lock;
	
	public Transaction(IndexId iid, IndexId.Transaction type){
		this.iid = iid;
		this.type = type;
		this.lock = iid.getTransactionLock(type); 
		inTransaction = false;
	}
	
	/**
	 * Begin transaction. Will check if previous transaction was completed, and
	 * if not, will return index to consistent state. 
	 */
	public void begin(){
		// acquire lock, this will serialize transactions on indexes
		lock.lock();
		File backup = new File(getBackupDir());
		File info = new File(getInfoFile());
		if(backup.exists() && info.exists()){
			// recover old transaction
			Properties prop = new Properties();
			try{
				FileInputStream fileis = new FileInputStream(info);
				prop.load(fileis);
				fileis.close();
				// check if status is set, which means all is OK with transaction copy
				if(prop.getProperty("status")!=null){
					recover();
				}
			} catch(IOException e){
				log.info("I/O error opening status file, aborting recovery of previous transaction");
			}			
		}
		cleanup();
		// start new transaction
		backup.getParentFile().mkdirs();
		try{
			// make a copy
			FSUtils.createHardLinkRecursive(iid.getPath(type),backup.getAbsolutePath(),true);
			Properties prop = new Properties();
			// write out the status file
			prop.setProperty("status","started at "+System.currentTimeMillis());			
			FileOutputStream fileos = new FileOutputStream(info,false);
			prop.store(fileos,"");
			fileos.close();
			// all is good, set transaction flag
			inTransaction = true;
			log.info("Transaction on index "+iid+" started");
		} catch(Exception e){
			log.error("Error while intializing transaction: "+e.getMessage());
			lock.unlock();
		}
	}
	
	/** Cleanup transaction files */
	protected void cleanup() {
		File trans = new File(getBackupDir());
		File info = new File(getInfoFile());
		// cleanup before starting new transaction
		try{
			if(trans.exists())
				FSUtils.deleteRecursive(trans.getAbsoluteFile());
			if(info.exists())
				FSUtils.deleteRecursive(info.getAbsoluteFile());
		} catch(Exception e){
			log.error("Error removing old transaction data from "+iid.getTransactionPath(type)+" : "+e.getMessage());
		}

	}
	/** This is where index backup is stored */
	protected String getBackupDir(){
		return iid.getTransactionPath(type) + Configuration.PATH_SEP + "backup" ;
	}
	/** Property file holding info about the status of transaction */
	protected String getInfoFile(){
		return iid.getTransactionPath(type) + Configuration.PATH_SEP + "transaction.info";
	}

	protected int exec(String command) throws Exception {
		log.debug("Running shell command: "+command);
		Process p = null;
		try{
			p = Runtime.getRuntime().exec(command); 
			p.waitFor();
			int exitValue = p.exitValue();
			return exitValue;
		} finally {
			Command.closeStreams(p);
		}
	}
	
	/** 
	 *  Recover from transaction data, call only when sure that transaction
	 *  data is valid. 
	 */
	protected void recover(){
		File backup = new File(getBackupDir());
		File index = new File(iid.getTransactionPath(type));
		String path = iid.getPath(type);
		try{
			log.info("Recovering "+path+" from "+backup.getPath());
			if(index.exists()) // clear locks before recovering
				WikiIndexModifier.unlockIndex(path);
			
			// delete old indexpath 
			FSUtils.deleteRecursive(new File(path));
			
			FSUtils.createHardLinkRecursive(backup.getAbsolutePath(),path);
			FSUtils.deleteRecursive(backup.getAbsoluteFile()); // cleanup 
		} catch(Exception e){
			log.error("Recovery of index "+iid+" failed with error "+e.getMessage());
		}
	}
	
	/** 
	 * Commit changes to index. 
	 */
	public void commit(){
		try{
			cleanup();
			inTransaction = false;
			log.info("Successfully commited changes on "+iid);
		} finally{
			lock.unlock();
		}
	}
	
	/**
	 * Rollback changes to index. Returns to previous consistent state.
	 */
	public void rollback(){
		try{
			if(inTransaction){
				recover();
				inTransaction = false;
				log.info("Succesfully rollbacked changes on "+iid);
			}
		} finally{
			lock.unlock();
		}
	}

	public boolean isInTransaction() {
		return inTransaction;
	}
	
	
}
