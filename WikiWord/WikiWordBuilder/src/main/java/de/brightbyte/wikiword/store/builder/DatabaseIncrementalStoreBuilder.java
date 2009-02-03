package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;

public abstract class DatabaseIncrementalStoreBuilder extends DatabaseWikiWordStoreBuilder implements IncrementalStoreBuilder {

	public DatabaseIncrementalStoreBuilder(WikiWordStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);
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
	
	@Override
	public void finalizeImport() throws PersistenceException {
		try {
			flush();
			if (beginTask("DatabaseLocalConceptStore.finishImport", "enableKeys")) {
				database.enableKeys();
				endTask("DatabaseLocalConceptStore.finishImport", "enableKeys");
			}
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}
	
}
