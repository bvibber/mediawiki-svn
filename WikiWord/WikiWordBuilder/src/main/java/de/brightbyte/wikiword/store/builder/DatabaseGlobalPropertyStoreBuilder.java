package de.brightbyte.wikiword.store.builder;

import java.sql.SQLException;
import java.util.HashMap;
import java.util.Map;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.GlobalConceptStoreSchema;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.PropertyStoreSchema;

public class DatabaseGlobalPropertyStoreBuilder extends DatabaseWikiWordStoreBuilder implements GlobalPropertyStoreBuilder {

	protected RelationTable propertyTable;
	protected GlobalConceptStoreSchema conceptStoreSchema;

	public DatabaseGlobalPropertyStoreBuilder(GlobalConceptStoreSchema conceptStoreSchema, TweakSet tweaks, Agenda agenda) throws SQLException, PersistenceException {
		super(new PropertyStoreSchema(conceptStoreSchema.getDataset(), conceptStoreSchema.getConnection(), true, tweaks, false ), 
				tweaks, agenda);

		database.setBackgroundErrorHandler(conceptStoreSchema.getBackgroundErrorHandler());
		
		Inserter propertyInserter = configureTable("property", 128, 3*32);
		this.propertyTable =  (RelationTable)propertyInserter.getTable();
		
		this.conceptStoreSchema = conceptStoreSchema;
	}

	protected DatabaseTable getTable(String name) {
		if (database.hasTable(name))
			return database.getTable(name);
		else
			return conceptStoreSchema.getTable(name);
	}
	
	protected Map<String, PropertyStoreSchema> localPropertyStores = new HashMap<String, PropertyStoreSchema>();
	
	public PropertyStoreSchema getLocalPropertyStoreSchema(Corpus c) throws PersistenceException {
		PropertyStoreSchema store =  localPropertyStores.get(c.getLanguage());
		
		if (store==null) {
			try {
				LocalConceptStoreSchema db = conceptStoreSchema.getLocalConceptDatabase(c);
				store = new PropertyStoreSchema(c, db.getConnection(), false, tweaks, false);
				localPropertyStores.put(c.getLanguage(), store);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		return store;
	}
	
	public int importProperties(Corpus from) throws PersistenceException {
		PropertyStoreSchema db = getLocalPropertyStoreSchema(from);
		
		DatabaseTable property = db.getTable(propertyTable.getName());
		DatabaseTable origin = conceptStoreSchema.getTable("origin");
		
		String sql = "INSERT IGNORE INTO "+propertyTable.getSQLName()+" (concept, property, value) "
		+ " SELECT O.global_concept, P.property, P.value "
		+ " FROM "+origin.getSQLName()+" as O " 
		+ " JOIN "+property.getSQLName()+" as P ON P.concept = O.local_concept "
		+ " 																			AND O.lang = "+database.quoteString(from.getLanguage()) + " ";
	
		int n = executeChunkedUpdate("importProperties", "import("+from.getLanguage()+")", sql, null, property, "P.concept");
		return n;
	}	

	
	
}