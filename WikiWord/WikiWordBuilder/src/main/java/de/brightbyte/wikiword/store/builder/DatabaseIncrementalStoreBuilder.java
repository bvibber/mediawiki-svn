package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import de.brightbyte.application.Agenda;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;

public abstract class DatabaseIncrementalStoreBuilder extends DatabaseWikiWordStoreBuilder implements IncrementalStoreBuilder {

	protected DatabaseIncrementalStoreBuilder(WikiWordStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
		super(database, tweaks, agenda);
	}

	protected abstract void deleteDataFrom(int rcId, String op) throws PersistenceException;
	
	
	public void deleteDataFrom(int rcId) throws PersistenceException {
		log("deleting data from "+rcId);
		deleteDataFrom(rcId, "=");
	}

	public void deleteDataAfter(int rcId, boolean inclusive) throws PersistenceException {
		String op = inclusive ? ">=" : ">";
		log("deleting data from with id "+op+" "+rcId);
		deleteDataFrom(rcId, op);
	}

	@Override
	public void prepareImport() throws PersistenceException, PersistenceException {
		if (getAgenda().beginTask("DatabaseLocalConceptStore.prepare", "prepare")) {
			try {
				database.disableKeys();
				getAgenda().endTask("DatabaseLocalConceptStore.prepare", "prepare");
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	}
	
	public void finalizeImport() throws PersistenceException {
		flush();
		
		closeInserters(); //kill inserters and their internal buffers
		
		try {
			database.joinExecutor(true); //kill background flush workers
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} catch (InterruptedException e) {
			//ignore
		}  
		
		Runtime.getRuntime().gc(); //run garbage collection
	}
	
	public void preparePostProcessing() throws PersistenceException {
		try {
			flush();
			if (beginTask("DatabaseLocalConceptStore.preparePostProcessing", "enableKeys")) {
				database.enableKeys();
				endTask("DatabaseLocalConceptStore.preparePostProcessing", "enableKeys");
			}
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}
	
}
