package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;

public abstract class DatabaseLocalStoreBuilder extends DatabaseWikiWordStoreBuilder implements IncrementalStoreBuilder {

	public DatabaseLocalStoreBuilder(WikiWordStoreSchema database, TweakSet tweaks) throws SQLException {
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
	
}
