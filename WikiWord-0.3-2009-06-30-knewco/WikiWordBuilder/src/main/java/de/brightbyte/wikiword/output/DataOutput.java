package de.brightbyte.wikiword.output;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;

public interface DataOutput {
	
	public abstract void flush() throws PersistenceException;	
	public abstract void close() throws PersistenceException;

	public void prepare() throws PersistenceException;
	public void finish() throws PersistenceException;
	
	public DatasetIdentifier getDatasetIdentifier();
}
