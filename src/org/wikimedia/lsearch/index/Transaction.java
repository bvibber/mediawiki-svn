package org.wikimedia.lsearch.index;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.util.Properties;

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
	
	public Transaction(IndexId iid){
		this.iid = iid;
		inTransaction = false;
	}
	
	/**
	 * Begin transaction. Will check if previous transaction was completed, and
	 * if not, will return index to consistent state. 
	 */
	public void begin(){
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
			FSUtils.createHardLinkRecursive(iid.getIndexPath(),backup.getAbsolutePath());
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
			log.error("Error removing old transaction data from "+iid.getTransactionPath()+" : "+e.getMessage());
		}

	}
	/** This is where index backup is stored */
	protected String getBackupDir(){
		return iid.getTransactionPath() + Configuration.PATH_SEP + "backup" ;
	}
	/** Property file holding info about the status of transaction */
	protected String getInfoFile(){
		return iid.getTransactionPath() + Configuration.PATH_SEP + "transaction.info";
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
		File index = new File(iid.getIndexPath());
		try{
			if(index.exists()) // clear locks before recovering
				WikiIndexModifier.unlockIndex(iid.getIndexPath());
			
			// delete old indexpath 
			FSUtils.deleteRecursive(new File(iid.getIndexPath()));
			
			FSUtils.createHardLinkRecursive(backup.getAbsolutePath(),iid.getIndexPath());
			FSUtils.deleteRecursive(backup.getAbsoluteFile()); // cleanup 
		} catch(Exception e){
			log.error("Recovery of index "+iid+" failed with error "+e.getMessage());
		}
	}
	
	/** 
	 * Commit changes to index. 
	 */
	public void commit(){
		cleanup();
		inTransaction = false;
		log.info("Successfully commited changes on "+iid);
	}
	
	/**
	 * Rollback changes to index. Returns to previous consistent state.
	 */
	public void rollback(){
		recover();
		inTransaction = false;
		log.info("Succesbully rollbacked changes on "+iid);
	}

	public boolean isInTransaction() {
		return inTransaction;
	}
	
	
}
