package de.brightbyte.wikiword.store;

import java.util.Map;

import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;

public interface WikiWordStore {
	public abstract Map<String, ? extends Number> getTableStats()
		throws PersistenceException;	

	public abstract void dumpTableStats(Output out)
		throws PersistenceException;

	public abstract void close(boolean flush) throws PersistenceException;
 
	public abstract void open() throws PersistenceException;

	public abstract void checkConsistency() throws PersistenceException;
	
	//public abstract boolean isComplete(String app) throws PersistenceException;

	public abstract boolean isComplete() throws PersistenceException;
	
	public DatasetIdentifier getDatasetIdentifier();
}
