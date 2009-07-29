package de.brightbyte.wikiword.store.builder;

import de.brightbyte.application.Agenda;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordStore;

public interface WikiWordStoreBuilder extends WikiWordStore {
	
	public abstract void initialize(boolean purge, boolean dropAll) throws PersistenceException;

	public abstract void prepareImport() throws PersistenceException;
	public void finalizeImport() throws PersistenceException;
	
	public abstract void close(boolean flush) throws PersistenceException;

	public abstract void open() throws PersistenceException;

	//public abstract void finish() throws PersistenceException;

	public abstract void flush() throws PersistenceException;

	//public abstract void checkConsistency() throws PersistenceException;

	//public abstract void deleteDataFrom(int rcId) throws PersistenceException;

	//public abstract void deleteDataAfter(int rcId) throws PersistenceException;
	
	public abstract Agenda createAgenda() throws PersistenceException;
	
	public abstract Agenda getAgenda() throws PersistenceException;

	public abstract void optimize() throws PersistenceException;

	public abstract int getNumberOfWarnings() throws PersistenceException;
	
	//public LogPoint getLastLogPoint() throws SQLException;

	//public abstract void clearStatistics() throws PersistenceException;

	public abstract void checkConsistency() throws PersistenceException;

	public abstract void dumpTableStats(Output out) throws PersistenceException;

	public abstract void setLogLevel(int loglevel);

	public void storeWarning(int rcId, String problem, String details) throws PersistenceException;
	
}