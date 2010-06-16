package de.brightbyte.wikiword.store.builder;

import java.io.File;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Date;
import java.util.Random;

import javax.sql.DataSource;

import de.brightbyte.application.Agenda;
import de.brightbyte.data.ChunkyBitSet;
import de.brightbyte.data.KeyValueStore;
import de.brightbyte.data.Pair;
import de.brightbyte.data.PersistentIdManager;
import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.MySqlDialect;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.SystemUtils;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ExtractionRule;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.NameMaps;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.LocalStatisticsStoreSchema;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;
import de.brightbyte.wikiword.store.DatabaseLocalConceptStore;

/**
 * A LocalConceptStore implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used by 
 * {@link de.brightbyte.wikiword.store.DatabaseLocalConceptStore}, see there.
 */
public class DatabaseLocalConceptStoreBuilder extends DatabaseWikiWordConceptStoreBuilder<LocalConcept> 
	implements LocalConceptStoreBuilder {
	
	protected Corpus corpus;

	protected EntityTable resourceTable;
	
	protected EntityTable definitionTable;
	protected EntityTable sectionTable;
	
	protected RelationTable aliasTable;
	protected RelationTable meaningTable;
	
	//protected EntityTable conceptDescriptionTable;

	protected Inserter resourceInserter;
	
	protected Inserter definitionInserter;
	protected Inserter sectionInserter;
	
	protected Inserter aliasInserter;
	protected Inserter meaningInserter;
	
	protected Inserter aboutInserter;
	protected RelationTable aboutTable;
	
	protected Random random;
	
	protected TweakSet tweaks;
	
	protected PersistentIdManager idManager;
	protected ChunkyBitSet conceptDedupe; 
	

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseLocalConceptStoreBuilder(Corpus corpus, DataSource dbInfo, TweakSet tweaks, Agenda agenda) throws SQLException {
		this(new LocalConceptStoreSchema(corpus, dbInfo, tweaks, true), tweaks, agenda);
	}
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database accessed by the given database connection.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param db a database connection
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseLocalConceptStoreBuilder(Corpus corpus, Connection db, TweakSet tweaks, Agenda agenda) throws SQLException {
		this(new LocalConceptStoreSchema(corpus, db, tweaks, true), tweaks, agenda);
	}
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database represented by the DatabaseSchema.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param db empty DatabaseSchema, wrapping a database connection. Will be configured with the appropriate table defitions
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 * @throws SQLException 
	 */
	public DatabaseLocalConceptStoreBuilder(LocalConceptStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
		super(database, tweaks, agenda);
		
		this.corpus = database.getCorpus();
		this.tweaks = tweaks;
		
		sectionInserter =  configureTable("section", 64, 64);
		definitionInserter = configureTable("definition", 1024, 256);
		resourceInserter = configureTable("resource", 256, 32);
		aliasInserter =    configureTable("alias", 1024, 64);
		aliasInserter.setLenient(true);
		meaningInserter =  configureTable("meaning", 8*1024, 64);
	
		resourceTable = (EntityTable)resourceInserter.getTable(); 
		
		definitionTable = (EntityTable)definitionInserter.getTable();
		sectionTable = (EntityTable)sectionInserter.getTable();
		
		aliasTable = (RelationTable)aliasInserter.getTable();
		meaningTable = (RelationTable)meaningInserter.getTable();
		
		aboutInserter =  configureTable("about", 1024, 64);
		aboutTable =    (RelationTable)aboutInserter.getTable();
		
		if (tweaks.getTweak("dbstore.conceptDedupe", false)) {
			conceptDedupe = new ChunkyBitSet();
		}
		
		long seed = tweaks.getTweak("dbstore.randomSeed", -1); //TODO: doc
		if (seed>0) random = new Random(seed);
		else random = new Random();
		
		/*
		Inserter conceptDescriptionInserter = configureTable("concept_description", 64, 1024);
		conceptDescriptionTable = (EntityTable)conceptDescriptionInserter.getTable();
		*/
	}
	
	@Override
	public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
		if (tweaks.getTweak("dbstore.idManager", false)) {
			try {
				String dir = tweaks.getTweak("dbstore.auxFileDir", SystemUtils.getPropertySafely("java.io.temp", "/tmp"));
				String pfx = database.getTablePrefix();
				String db = database.getConnection().getMetaData().getURL().replaceAll("\\?.*$", "").replaceAll("[.:/\\@&;=!]+", "_");
				File f = new File(dir+"/wikiword."+db+"."+pfx+".ids");
				
				log("storing ID mappings in "+f);
				int bsz = tweaks.getTweak("dbstore.idManager.bufferSize", 16*1024);
				
				KeyValueStore<String, Integer> store = NameMaps.newStore(tweaks.getTweak("dbstore.idManager.idStoreParameters", "string"), getCorpus().getLanguage());
				
				if (store==null) idManager = new PersistentIdManager(f, bsz);
				else idManager = new PersistentIdManager(store, f, bsz); //XXX: ugly cast. 
				
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		if (purge) {
			if (idManager!=null) idManager.clear(); 
		}
		
		super.initialize(purge, dropAll);
	}	
	
	protected void loadIdManager() throws PersistenceException {
		//FIXME: should fail on partial load
		//XXX: could probably be skipped if continuing at a stage after dump reading
		if (idManager!=null) {
			if (idManager.fileExists()) {
				log("loading persisted ID map..."+" memory used: "+(Runtime.getRuntime().totalMemory() -  Runtime.getRuntime().freeMemory())/1024+"KB");
				idManager.load(); 
				log("Max persisted ID: "+idManager.getMaxId()+"; memory used: "+(Runtime.getRuntime().totalMemory() -  Runtime.getRuntime().freeMemory())/1024+"KB");
			} else {
				log("building persisted ID map..."+" memory used: "+(Runtime.getRuntime().totalMemory() -  Runtime.getRuntime().freeMemory())/1024+"KB");
				DataCursor<Pair<String, Integer>> cursor = getConceptIdAssociationCursor();
				int c = idManager.slurp(cursor);
				cursor.close();
				log("ID entries loaded: "+c+"; Max persisted ID: "+idManager.getMaxId()+"; memory used: "+(Runtime.getRuntime().totalMemory() -  Runtime.getRuntime().freeMemory())/1024+"KB");
			}
		}
	}
	
	protected void loadConceptDedupe() throws PersistenceException {
		log("building dedupe set..."+" memory used: "+(Runtime.getRuntime().totalMemory() -  Runtime.getRuntime().freeMemory())/1024+"KB");
		DataCursor<Integer> cursor = getConceptIdCursor();
		conceptDedupe.slurp(cursor);
		cursor.close();
		log("Dedupe size: "+conceptDedupe.size()+"; memory used: "+(Runtime.getRuntime().totalMemory() -  Runtime.getRuntime().freeMemory())/1024+"KB");
	}
	
	protected DataCursor<Integer> getConceptIdCursor() throws PersistenceException {
		String sql = "SELECT id from " + conceptTable.getSQLName();
		ResultSet rs = executeBigQuery("getConceptIdCursor", sql);
		
		DatabaseDataSet.Factory<Integer> f = new DatabaseDataSet.Factory<Integer>() {
			public Integer newInstance(ResultSet row) throws Exception {
				return row.getInt(1);
			}
		};
		
		return new DatabaseDataSet.Cursor<Integer>(rs, f);
	}
	
	protected DataCursor<Pair<String, Integer>> getConceptIdAssociationCursor() throws PersistenceException {
		final boolean binaryText = database.getHints().getHint(MySqlDialect.HINT_USE_BINARY_TEXT, false);
		
		String sql = "SELECT name, id from " + conceptTable.getSQLName();
		ResultSet rs = executeBigQuery("getConceptIdAssociationCursor", sql);
		
		DatabaseDataSet.Factory<Pair<String, Integer>> f = new DatabaseDataSet.Factory<Pair<String, Integer>>() {
			public Pair<String, Integer> newInstance(ResultSet row) throws Exception {
				Object n = binaryText ? row.getBytes(1) : row.getString(1);
				String name = DatabaseUtil.asString(n, "UTF-8");
				int id = DatabaseUtil.asInt(row.getObject(2));
				return new Pair<String, Integer>(name, id);
			}
		};
		
		return new DatabaseDataSet.Cursor<Pair<String, Integer>>(rs, f);
	}

	public ConceptType getConceptType(int type) {
		return corpus.getConceptTypes().getType(type);
	}

	public Corpus getCorpus() {
		return corpus;
	}
	
	@Override
	public void flush() throws PersistenceException{
		if (idManager!=null) idManager.flush();
		super.flush();
		
		if (propertyStore!=null)
			propertyStore.flush();
		
		if (textStore!=null)
			textStore.flush();
	}
	
	protected void deleteDataFrom(int rcId, String op) throws PersistenceException {
		deleteDataFrom(rcId, op, definitionTable, "resource");
		
		deleteDataFrom(rcId, op, linkTable, "resource");
		deleteDataFrom(rcId, op, langlinkTable, "resource");
		deleteDataFrom(rcId, op, broaderTable, "resource");
		
		deleteDataFrom(rcId, op, aboutTable, "resource");
		deleteOrphansFrom(rcId, op, conceptTable, aboutTable, "concept");
		
		deleteDataFrom(rcId, op, aliasTable, "resource");
		deleteDataFrom(rcId, op, sectionTable, "resource");
		deleteDataFrom(rcId, op, resourceTable, "id");
	}
	
	//----------------------------------------------------------------------------------
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeSection(int, java.lang.String, java.lang.String)
	 */
	public void storeSection(int rcId, String sectionName, String conceptName) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (sectionInserter==null) sectionInserter = sectionTable.getInserter();

			sectionName = checkName(rcId, sectionName, "section name (from section link in resource #{0})", rcId);
			conceptName = checkName(rcId, conceptName, "concept name (from section link in resource #{0})", rcId);
			
			sectionInserter.updateInt("resource", rcId);
			sectionInserter.updateString("section_name", sectionName);
			sectionInserter.updateString("concept_name", conceptName);
			
			if (idManager!=null) {
				sectionInserter.updateInt("section_concept", idManager.aquireId(sectionName));
				sectionInserter.updateInt("concept", idManager.aquireId(conceptName));
			}
			
			sectionInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeDefinition(int, java.lang.String)
	 */
	public void storeDefinition(int rcId, int conceptId, String definition) throws PersistenceException {
		try {
			if (conceptId<0) throw new IllegalArgumentException("bad concept id "+conceptId);

			definitionInserter.updateInt("resource", rcId);
			definitionInserter.updateInt("concept", conceptId);
			definitionInserter.updateString("definition", clipString(rcId, definition, 1024 * 8, "definition text (concept {0})", conceptId));
			definitionInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}	

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeResourceAbout(java.lang.String, de.brightbyte.wikiword.ResourceType, java.util.Date, int conceptId, String conceptName)
	 */
	public int storeResourceAbout(int pageId, int revId, String name, ResourceType ptype, Date time, int conceptId, String conceptName) throws PersistenceException {
		int rcId = storeResource(pageId, revId, name, ptype, time);
		storeAbout(rcId, name, conceptId, conceptName);
		return rcId;
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeResource(java.lang.String, de.brightbyte.wikiword.ResourceType, java.util.Date)
	 */
	public int storeResource(int pageId, int revId, String name, ResourceType ptype, Date time) throws PersistenceException {
		try {
			name = checkName(resourceInserter.getLastId()+1, name, "resource name ({0})", resourceInserter.getLastId()+1);
			
			if (pageId>0) resourceInserter.updateInt("page_id", pageId );
			if (revId>0) resourceInserter.updateInt("revision_id", revId );
			
			resourceInserter.updateString("name", name );
			resourceInserter.updateInt("type", ptype.getCode());
			resourceInserter.updateString("timestamp", timestampFormatter.format(time));
			resourceInserter.updateRow();
			
			int rcId = resourceInserter.getLastId();
			
			return rcId;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeConcept(int, java.lang.String, de.brightbyte.wikiword.ConceptType)
	 */
	public int storeConcept(int rcId, String name, ConceptType ctype) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId+" for concept "+name);
			if (ctype==null) throw new IllegalArgumentException("null type for for concept "+name+" (rc="+rcId+")");
			
			name = checkName(rcId, name, "concept name (resource #{0})", rcId);
			
			int id = -1;
			if (rcId>=0) id = storeAbout(rcId, name, id, name); 
			
			if (id<=0 && idManager!=null) {
				id = idManager.aquireId(name);
			}

			if (id>0 && conceptDedupe!=null) {
				if (!conceptDedupe.add(id)) {
					warning(rcId, "duplicate concept", "id= "+id+", name= "+name+", rc= "+rcId+", type="+ctype, null);
					return id;
				}
				//XXX: throw an exception? should this always be fatal? config var?
			}
			
			if (id>0) {
				conceptInserter.updateInt("id", id);
			}
			
			conceptInserter.updateDouble("random", random.nextDouble());
			conceptInserter.updateString("name", name);
			conceptInserter.updateInt("type", ctype.getCode());
			conceptInserter.updateRow();

			if (idManager==null && id<=0) {
				id = conceptInserter.getLastId();
			}
			
			return id;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeLink(int, int, java.lang.String, java.lang.String, java.lang.String, de.brightbyte.wikiword.ExtractionRule)
	 */
	public void storeLink(int rcId, int anchorId, String anchorName, 
			String term, String targetName, ExtractionRule rule) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			targetName = checkName(rcId, targetName, "concept name (target in resource #{0})", rcId);
			//TODO: optionally ignore all dirty links (or flag them?)
			
			if (rcId>=0) linkInserter.updateInt("resource", rcId);
			if (anchorId>0) linkInserter.updateInt("anchor", anchorId);
			else if (idManager!=null && anchorName!=null) linkInserter.updateInt("anchor", idManager.aquireId(anchorName)); 
			if (anchorName!=null) linkInserter.updateString("anchor_name", checkName(rcId, anchorName, "concept name (anchor ~ resource #{0})", rcId));
			linkInserter.updateString("term_text", clipString(rcId, term, 255, "term text (resource #{0})", rcId));
			linkInserter.updateString("target_name", targetName);
			if (idManager!=null) linkInserter.updateInt("target", idManager.aquireId(targetName));
			linkInserter.updateInt("rule", rule.getCode());
			linkInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeReference(int, java.lang.String, int, java.lang.String, de.brightbyte.wikiword.ExtractionRule)
	 */
	public void storeReference(int rcId, String term, int targetId, String targetName, 
			ExtractionRule rule) throws PersistenceException {		
		try {
			//if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			
			if (rcId>=0) linkInserter.updateInt("resource", rcId);
			if (targetId>0) linkInserter.updateInt("target", targetId);
			else if (idManager!=null) linkInserter.updateInt("target", idManager.aquireId(targetName)); 
			linkInserter.updateString("target_name", checkName(rcId, targetName, "concept name (target, resource #{0})", rcId));
			linkInserter.updateString("term_text", clipString(rcId, term, 255, "term text (resource #{0})", rcId));
			linkInserter.updateInt("rule", rule.getCode());
			linkInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeConceptBroader(int, int, java.lang.String, java.lang.String, float)
	 */
	public void storeConceptBroader(int rcId, int narrowId, String narrowName, String broadName, ExtractionRule rule) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			narrowName = checkName(rcId, narrowName, "concept name (resource #{0})", rcId);
			broadName = checkName(rcId, broadName, "concept name (resource #{0})", rcId);
			
			broaderInserter.updateInt("resource", rcId);
			broaderInserter.updateInt("narrow", narrowId);
			broaderInserter.updateString("narrow_name", narrowName);
			broaderInserter.updateString("broad_name", broadName);
			//broaderInserter.updateFloat("confidence", confidence);
			if (idManager!=null) broaderInserter.updateInt("broad", idManager.aquireId(broadName));
			broaderInserter.updateInt("rule", rule.getCode());
			broaderInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeConceptBroader(int, java.lang.String, java.lang.String, float)
	 */
	public void storeConceptBroader(int rcId, String narrowName, String broadName, ExtractionRule rule) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			narrowName = checkName(rcId, narrowName, "concept name (resource #{0})", rcId);
			broadName = checkName(rcId, broadName, "concept name (resource #{0})", rcId);

			broaderInserter.updateInt("resource", rcId);
			broaderInserter.updateString("narrow_name", narrowName);
			broaderInserter.updateString("broad_name", broadName);
			//broaderInserter.updateFloat("confidence", confidence);
			if (idManager!=null) broaderInserter.updateInt("narrow", idManager.aquireId(narrowName));
			if (idManager!=null) broaderInserter.updateInt("broad", idManager.aquireId(broadName));
			broaderInserter.updateInt("rule", rule.getCode());
			broaderInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeConceptAlias(int, int, java.lang.String, java.lang.String, float)
	 */
	public void storeConceptAlias(int rcId, int source, String sourceName, int target, String targetName, AliasScope scope) throws PersistenceException {
		try {
			if (sourceName.equals(targetName)) throw new IllegalArgumentException("can't alias "+sourceName+" to itself");
			
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			targetName = checkName(rcId, targetName, "concept name (resource #{0})", rcId);
			sourceName = checkName(rcId, sourceName, "concept name (resource #{0})", rcId);
			
			aliasInserter.updateInt("resource", rcId);
			aliasInserter.updateInt("scope", scope.ordinal());
			
			if (source>0) aliasInserter.updateInt("source", source);
			else if (idManager!=null) aliasInserter.updateInt("source", idManager.aquireId(sourceName));
			aliasInserter.updateString("source_name", sourceName);
			
			if (target>0) aliasInserter.updateInt("target", target);
			else if (idManager!=null) aliasInserter.updateInt("target", idManager.aquireId(targetName));
			aliasInserter.updateString("target_name", targetName);
			
			//aliasInserter.updateFloat("confidence", confidence);
			aliasInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeAbout(int, String)
	 */
	public int storeAbout(int rcId, String rcName, String conceptName) throws PersistenceException {
		return storeAbout(rcId, rcName, -1, conceptName);
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeAbout(int, int, String)
	 */
	public int storeAbout(int rcId, String rcName, int concept, String conceptName) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			conceptName = checkName(rcId, conceptName, "concept name (resource #{0})", rcId);
			
			aboutInserter.updateInt("resource", rcId);
			aboutInserter.updateString("resource_name", rcName);
			aboutInserter.updateString("concept_name", conceptName);
			
			if (concept <=0 && idManager!=null) {
				concept = idManager.aquireId(conceptName);
			}

			if (concept>0) aboutInserter.updateInt("concept", concept);
			
			aboutInserter.updateRow();
			return concept;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	/**
	 * @see de.brightbyte.wikiword.builder.WikiStoreBuilder#storeConceptReference(int, int, java.lang.String, java.lang.String)
	 */
	/*public void storeConceptReference(int rcId, int source, String sourceName, String target) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			
			referenceInserter.updateInt("resource", rcId);
			referenceInserter.updateInt("source", source);
			referenceInserter.updateString("source_name", checkName(rcId, sourceName, "concept name (resource #{0})", rcId));
			referenceInserter.updateString("target_name", checkName(rcId, target, "concept name (resource #{0})", rcId));
			referenceInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}*/

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeLanguageLink(int, int, java.lang.String, java.lang.String, java.lang.String)
	 */
	public void storeLanguageLink(int rcId, int concept, String conceptName, String lang, String target) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			
			//XXX: insert ignore...
			langlinkInserter.updateInt("resource", rcId);
			if (concept<=0 && idManager!=null) concept = idManager.aquireId(conceptName);
			if (concept>0) langlinkInserter.updateInt("concept", concept);
			langlinkInserter.updateString("concept_name", checkName(rcId, conceptName, "concept name (resource #{0})", rcId));
			langlinkInserter.updateString("language", lang);
			langlinkInserter.updateString("target", checkName(rcId, target, "external concept name (resource #{0})", rcId));
			langlinkInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	
	//----------------------------------------------------------------------------------

	/**
	 * @throws PersistenceException 
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#prepareImport()
	 */
	public void prepareImport() throws PersistenceException {
		if (idManager!=null) loadIdManager();
		if (conceptDedupe!=null) loadConceptDedupe();

		try {
				database.disableKeys();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}

		if (propertyStore!=null) {
			propertyStore.prepareImport();
		}
		
		if (textStore!=null) {
			textStore.prepareImport();
		}
	}
	
	public void prepareMassInsert() throws PersistenceException {
		try {
				database.disableKeys();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}

		if (propertyStore!=null) {
			propertyStore.prepareMassInsert();
		}
		
		if (textStore!=null) {
			textStore.prepareMassInsert();
		}
	}
	
	public void prepareMassProcessing() throws PersistenceException {
		this.flush();
		this.enableKeys();

		if (propertyStore!=null) {
			propertyStore.prepareMassProcessing();
		}
		
		if (textStore!=null) {
			textStore.prepareMassProcessing();
		}
	}
	
	public void finalizeImport() throws PersistenceException {
		if (idManager!=null) { 
			idManager.deleteFile(); //delete temporary ID file
			idManager = null;  //release id buffer memory
		}
		
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

		if (propertyStore!=null && beginTask("finalizeImport", "propertyStore.finalizeImport")) {
			propertyStore.finalizeImport();
			endTask("finalizeImport", "propertyStore.finalizeImport");
		}
		
		if (textStore!=null && beginTask("finalizeImport", "textStore.finalizeImport")) {
			textStore.finalizeImport();
			endTask("finalizeImport", "textStore.finalizeImport");
		}
	}
		
	public void finishSections() throws PersistenceException {
		if (beginTask("finishSections", "buildSectionConcepts")) {
				int n = buildSectionConcepts();      
				endTask("finishSections", "buildSectionConcepts", n+" concepts");
		}
		
		if (beginTask("finishSections", "buildSectionBroader")) {
				int n = buildSectionBroader();      
				endTask("finishSections", "buildSectionBroader", n+" references");
		}
		
		//if (beginTask("finishSections", "truncateTable:section")) database.truncateTable(sectionTable.getName());
	}
	
	public void finishBadLinks() throws PersistenceException {
		if (beginTask("finishBadLinks", "deleteLinksToBadResources:link")) {
			int n = deleteLinksToBadResources(linkTable, "target_name", ResourceType.DISAMBIG, ResourceType.LIST, ResourceType.BAD);     
			endTask("finishBadLinks", "deleteLinksToBadResources:link", n+" links");
		}
		
		if (beginTask("finishBadLinks", "deleteLinksToBadResources:alias.target")) {
			int n = deleteLinksToBadResources(aliasTable, "target_name", ResourceType.DISAMBIG, ResourceType.LIST, ResourceType.BAD, ResourceType.REDIRECT);     
			endTask("finishBadLinks", "deleteLinksToBadResources:alias.target", n+" links");
		}
		
		if (beginTask("finishBadLinks", "copyTable:alias:broader")) {
			//NOTE: keep all category aliases for later analysis & evaluation (not needed in production, but doesn't hurt)
			int n = copyTable(aliasTable, aliasTable.getSQLName()+"_original_broader", "scope = "+AliasScope.CATEGORY.ordinal(), true);     
			endTask("finishBadLinks", "copyTable:alias:broader", n+" entries");
		}
		
		if (beginTask("finishBadLinks", "deleteLinksToGoodConcepts:alias.source")) {
			//NOTE: drop category aliases if they are aliasing an existing concept.
			int n = deleteLinksToGoodConcepts(aliasTable, idManager==null ? "source_name": "source", "scope = "+AliasScope.CATEGORY.ordinal());     
			endTask("finishBadLinks", "deleteLinksToGoodConcepts:alias.source", n+" links");
		}

		if (beginTask("finishBadLinks", "deleteAmbiguousAliases")) {
			//NOTE: drop category aliases if they are ambiguous
			int n = deleteAmbiguousAliases(aliasTable, "source", "source_name", "target", "scope = "+AliasScope.CATEGORY.ordinal());     
			endTask("finishBadLinks", "deleteAmbiguousAliases", n+" links");
		}

		if (beginTask("finishBadLinks", "deleteLinksToBadResources:section_concept")) {
			int n = deleteLinksToBadResources(sectionTable, "concept_name", ResourceType.DISAMBIG, ResourceType.LIST, ResourceType.BAD);     
			endTask("finishBadLinks", "deleteLinksToBadResources:section_concept", n+" references");
		}
		
		if (beginTask("finishBadLinks", "deleteBrokenReferences:section")) {
			int n = deleteBrokenReferences(sectionTable, idManager==null ? "concept_name" : "concept"); 
			endTask("finishBadLinks", "deleteBrokenReferences:section", n+" entries");
		}

		/*
		//NOTE: don't delete! apply alias, then delete links to bad targets!
		if (beginTask("finishBadLinks", "deleteLinksToBadResources:alias")) {
			int n = deleteLinksToBadResources(aliasTable, "target_name");     
			endTask("finishBadLinks", "deleteLinksToBadResources:alias", n+" links");
		}
		*/

		/*
		if (beginTask("finishBadLinks", "deleteLinksToBadResources:broader")) {
			//XXX: this is a bit problematic: categories often share the name of a disambiguation...
			int n = deleteLinksToBadResources(broaderTable, "broad_name");     
			endTask("finishBadLinks", "deleteLinksToBadResources:broader", n+" references");
		}
		*/
		
		/*
		if (beginTask("finishBadLinks", "deleteLinksToBadResources:narrower")) {
			int n = deleteLinksToBadResources(broaderTable, "narrow_name");     
			endTask("finishBadLinks", "deleteLinksToBadResources:narrower", n+" references");
		}
		*/
		
		/*
		 //NOTE: don't delete, we actually need them! resolve aliases, then delete all links to non-concepts.
		if (beginTask("finishBadLinks", "deleteDeadendAliases")) {
			int n = deleteDeadendAliases();     
			endTask("finishBadLinks", "deleteDeadendAliases", n+" references");
		}
		*/
	}
	
	private int deleteAmbiguousAliases(RelationTable aliasTable, String sourceField, String sourceNameField, String targetField, String where) throws PersistenceException {
		String sql = "CREATE TEMPORARY TABLE bad_alias ( id INT DEFAULT NULL, name varbinary(255), PRIMARY KEY (id, name) )";
		executeUpdate("deleteAmbiguousAliases:createTemp", sql);
		
		sql = "INSERT IGNORE INTO bad_alias " +
			" SELECT " + sourceField + ", " + sourceNameField + " " +
			" FROM " + aliasTable.getSQLName() + " " +
			(where==null ? "" : " WHERE " + where + " ") +
			" GROUP BY " + (idManager!=null ? sourceField  :  sourceNameField ) + " " +
			" HAVING count(DISTINCT " + targetField + ") > 1";
		
		executeUpdate("deleteAmbiguousAliases:fillTemp", sql);

		sql = "DELETE FROM A " +
			" USING " + aliasTable.getSQLName() + " AS A " +
			" JOIN bad_alias AS T ON " + (idManager!=null ? " T.id = A." + sourceField : " T.name = A." + sourceNameField ) + " " +
			(where==null ? "" : " WHERE " + where + " ");
		
		int n = executeUpdate("deleteAmbiguousAliases:delete", sql);
		
		sql = "DROP TEMPORARY TABLE bad_alias";
		executeUpdate("deleteAmbiguousAliases:dropTemp", sql);
		
		return n;
	}

	private int copyTable(RelationTable table, String as, String where, boolean drop) throws PersistenceException {
		String sql;
		
		if (drop) {
			sql = "DROP TABLE IF EXISTS "+as;
			executeUpdate("copyTable:drop", sql);
		}
		
		sql = "CREATE TABLE "+as+" LIKE "+table.getSQLName();
		executeUpdate("copyTable:create", sql);
		
		sql = "INSERT INTO "+as+" SELECT * FROM "+table.getSQLName();
		if (where!=null) sql += " WHERE " + where;
		
		return executeUpdate("copyTable:insert", sql);
	}

	public void finishMissingConcepts() throws PersistenceException {
			if (beginTask("finishMissingConcpets", "buildMissingConcepts:about")) {
				int n = buildMissingConcepts(aboutTable, "concept", "concept_name");     
				endTask("finishMissingConcpets", "buildMissingConcepts:about", n+" concepts");
			}
			
			if (beginTask("finishMissingConcpets", "buildMissingConcepts:alias.source")) {
				int n = buildMissingConcepts(aliasTable, "source", "source_name");     
				endTask("finishMissingConcpets", "buildMissingConcepts:alias.source", n+" concepts");
			}

			if (beginTask("finishMissingConcpets", "buildMissingConcepts:alias.target")) {
				int n = buildMissingConcepts(aliasTable, "target", "target_name");     
				endTask("finishMissingConcpets", "buildMissingConcepts:alias.target", n+" concepts");
			}

			if (beginTask("finishMissingConcpets", "buildMissingConcepts:link")) {
				int n = buildMissingConcepts(linkTable, "target", "target_name");     
				endTask("finishMissingConcpets", "buildMissingConcepts:link", n+" concepts");
			}
			
			//NOTE: need to resolve category-aliases here, so no concepts are generated for aliased categories!
			//NOTE: bad category redirs have been droped in finishBadLinks
			if (beginTask("finishMissingConcpets", "resolveRedirects:broader")) {
				int n = resolveRedirects(aliasTable, broaderTable, "broad_name", idManager==null ? null : "broad", AliasScope.CATEGORY, 1, null, idManager==null ? "broad_name" : "broad_narrow", null);     
				endTask("finishMissingConcpets", "resolveRedirects:broader", n+" entries");
			}

			if (beginTask("finishMissingConcpets", "buildMissingConcepts:broader")) {
				int n = buildMissingConcepts(broaderTable, "broad", "broad_name");  
				endTask("finishMissingConcpets", "buildMissingConcepts:broader", n+" concepts");
			}
			if (beginTask("finishMissingConcpets", "buildMissingConcepts:narrower")) {
				int n = buildMissingConcepts(broaderTable, "narrow", "narrow_name");  
				endTask("finishMissingConcpets", "buildMissingConcepts:narrower", n+" concepts");
			}
			/*if (beginTask("finishMissingConcpets", "buildMissingConcepts:alias")) {
				int n = buildMissingConcepts(aliasTable, "target_name"); //XXX: needed? 
				endTask("finishMissingConcpets", "buildMissingConcepts:alias", n+" concepts");
			}
			if (beginTask("finishMissingConcpets", "buildMissingConcepts:section")) {
				int n = buildMissingConcepts(sectionTable, "concept_name"); 
				endTask("finishMissingConcpets", "buildMissingConcepts:section", n+" concepts");
			}
			*/
			/*
			if (beginTask("finishMissingConcpets", "deleteBrokenReferences:broader")) {
				int n = deleteBrokenReferences(broaderTable, idManager==null ? "broad_name" : "broad"); 
				endTask("finishMissingConcpets", "deleteBrokenReferences:broader", n+" entries");
			}
			if (beginTask("finishMissingConcpets", "deleteBrokenReferences:narrower")) {
				int n = deleteBrokenReferences(broaderTable, idManager==null ? "narrow_name" : "narrow");  
				endTask("finishMissingConcpets", "deleteBrokenReferences:narrower", n+" entries");
			}
			*/
			/*
			//NOTE: we need those to appropriatly *break* links when resolving aliases
			if (beginTask("finishMissingConcpets", "deleteBrokenReferences:alias")) {
				int n = deleteBrokenReferences(aliasTable, "target_name"); 
				endTask("finishMissingConcpets", "deleteBrokenReferences:alias", n+" entries");
			}
			*/
	}
	
	public void finishIdReferences() throws PersistenceException {
		   enableKeys();
		
			if (idManager==null && beginTask("finishIdReferences", "buildIdLinks:about")) {
				int n = buildIdLinks(aboutTable, "concept_name", "concept", 1);     
				endTask("finishIdReferences", "buildIdLinks:about", n+" references");
			}

			//XXX: if (beginTask("finish.buildIdLinks:link.term_text")) buildIdLinks(useTable, "term_text", "term");           
			//NOTE: don't need this, anchor-id is only null if anchor_name is null too. //XXX: really?! if (beginTask("finish.buildIdLinks:link.anchor_name")) buildIdLinks(linkTable, "anchor_name", "anchor");     //Uses index _use.target (and unique key _concept.name)
		
			if (idManager==null && beginTask("finishIdReferences", "buildIdLinks:link.target")) {
				int n = buildIdLinks(linkTable, "target_name", "target", 5);     
				endTask("finishIdReferences", "buildIdLinks:link.target", n+" references");
			}
			if (idManager==null && beginTask("finishIdReferences", "buildIdLinks:broader")) {
				int n = buildIdLinks(broaderTable, "broad_name", "broad", 2);     
				endTask("finishIdReferences", "buildIdLinks:broader", n+" references");
			}
			if (idManager==null && beginTask("finishIdReferences", "buildIdLinks:narrower")) {
				int n = buildIdLinks(broaderTable, "narrow_name", "narrow", 1);     
				endTask("finishIdReferences", "buildIdLinks:narrower", n+" references");
			}

			if (idManager==null && beginTask("finishIdReferences", "buildIdLinks:alias_source")) {
				int n = buildIdLinks(aliasTable, "source_name", "source", -5);  
				endTask("finishIdReferences", "buildIdLinks:alias_source", n+" references");
			}
			if (idManager==null && beginTask("finishIdReferences", "buildIdLinks:alias_target")) {
				int n = buildIdLinks(aliasTable, "target_name", "target", -5);  
				endTask("finishIdReferences", "buildIdLinks:alias_target", n+" references");
			}
			
			if (idManager==null && beginTask("finishIdReferences", "buildIdLinks:langlink")) {
				int n = buildIdLinks(langlinkTable, "concept_name", "concept", 1);  
				endTask("finishIdReferences", "buildIdLinks:langlink", n+" references");
			}
			//if (beginTask("finishIdReferences", "buildIdLinks:reference")) buildIdLinks(referenceTable, "target_name", "target"); 
			
			if (idManager==null &&  propertyStore!=null && beginTask("finishIdReferences", "propertyStore.finishIdReferences")) {
				propertyStore.finishIdReferences();
				endTask("finishIdReferences", "propertyStore.finishIdReferences");
			}
			
			/*
			if (idManager==null &&  textStore!=null && beginTask("finishIdReferences", "textStore.finishIdReferences")) {
				textStore.finishIdReferences();
				endTask("finishIdReferences", "textStore.finishIdReferences");
			}
			*/
	}
	
	public void finishAliases() throws PersistenceException {
		   enableKeys();
			
			if (beginTask("finishAliases", "resolveRedirects:link")) {
				//XXX: SLOW!
				//TODO: smaller chunks? chunk on target table, not alias table? force index? 
				int n = resolveRedirects(aliasTable, linkTable, "target_name", "target", AliasScope.REDIRECT, 8, null, "target_anchor", null);     
				endTask("finishAliases", "resolveRedirects:link", n+" entries");
			}

			//NOTE: broader.broad_name already done in finishMissingConcepts for AliasScope.BROADER
			
			if (beginTask("finishAliases", "resolveRedirects:about")) {
				int n = resolveRedirects(aliasTable, aboutTable, "concept_name", "concept", null, 1, null, null, null);     
				endTask("finishAliases", "resolveRedirects:about", n+" entries");
			}

			if (beginTask("finishAliases", "resolveRedirects:narrow")) {
				int n = resolveRedirects(aliasTable, broaderTable, "narrow_name", "narrow", null, 1, null, null, null);     
				endTask("finishAliases", "resolveRedirects:narrow", n+" entries");
			}

			if (beginTask("finishAliases", "resolveRedirects:broad")) {
				int n = resolveRedirects(aliasTable, broaderTable, "broad_name", "broad", null, 1, null, null, null);     
				endTask("finishAliases", "resolveRedirects:broad", n+" entries");
			}
						
			if (propertyStore!=null && beginTask("finishAliases", "propertyStore.finishAliases")) {
				propertyStore.finishAliases();
				endTask("finishAliases", "propertyStore.finishAliases");
			}
			
			/*
			if (textStore!=null && beginTask("finishAliases", "textStore.finishAliases")) {
				textStore.finishAliases();
				endTask("finishAliases", "textStore.finishAliases");
			}
			*/
			
			/*
			//NOTE: way too late for that!
			if (beginTask("finishAliases", "resolveRedirects:section")) {
				int n = resolveRedirects(sectionTable, "concept_name", null);     
				endTask("finishAliases", "resolveRedirects:section", n+" entries");
			}
			*/

			//--------------------------------------------
			
			/*//NOTE: redundant!
			if (beginTask("finishAliases", "deleteLinksToBadResources:link")) {
				int n = deleteLinksToBadResources(linkTable, "target_name", ResourceType.DISAMBIG, ResourceType.LIST, ResourceType.BAD);     
				endTask("finishAliases", "deleteLinksToBadResources:link", n+" links");
			}
			*/

			/*
			if (beginTask("finishAliases", "resolveRedirects:broader")) {
				int n = resolveRedirects(broaderTable, "broad_name", "broad");     //uses index _broader.broad
				endTask("finishAliases", "resolveRedirects:broader", n+" entries");
			}
			if (beginTask("finishAliases", "resolveRedirects:narrower")) {
				int n = resolveRedirects(broaderTable, "narrow_name", "narrow");     //uses index _broader.narrow
				endTask("finishAliases", "resolveRedirects:narrower", n+" entries");
			}
			*/
			
			/*if (beginTask("finishAliases", "reportBadLinks:link,ALIAS")) {
				int n = reportBadLinks(linkTable, "target", ConceptType.ALIAS);        //uses index _use.concept, _concept.type
				endTask("finishAliases", "reportBadLinks:link,ALIAS", n+" entries");
			} 
			if (beginTask("finishAliases", "reportBadLinks:broader,ALIAS")) {
				int n = reportBadLinks(broaderTable, "broad", ConceptType.ALIAS);      //uses index _broader.broader, _concept.type
				endTask("finishAliases", "reportBadLinks:broader,ALIAS", n+" entries");
			}*/
			
			//XXX: should be redundant: just delete alias concepts below and then kill broken links.
			//     but somehow, we need it...
			 if (beginTask("finishAliases", "deleteLinksToBadConcepts:link,ALIAS")) {
				int n = deleteLinksToBadConcepts(linkTable, "target", "target_anchor", ConceptType.ALIAS);        //uses index _use.concept, _concept.type
				if (n>0) warning(-1, "links to bad concepts", n+" references to redirects in "+linkTable.getName()+".target deleted", null);
				endTask("finishAliases", "deleteLinksToBadConcepts:link,ALIAS", n+" entries");
			}
			/*
			if (beginTask("finishAliases", "deleteLinksToBadConcepts:broader,ALIAS")) {
				int n = deleteLinksToBadConcepts(broaderTable, "broad", ConceptType.ALIAS);      //uses index _broader.broader, _concept.type
				if (n>0) warning(-1, "links to bad concepts", n+" references to redirects in "+linkTable.getName()+".broader deleted", null);
				endTask("finishAliases", "deleteLinksToBadConcepts:broader,ALIAS", n+" entries");
			}
			*/
			/*if (beginTask("finishAliases", "truncateTable:alias")) {
				int n = database.truncateTable(aliasTable.getName());
				endTask("finishAliases", "xxx", n+" entries");
			}*/
			if (beginTask("finishAliases", "deleteBadConcepts:ALIAS")) {
				int n = deleteBadConcepts(ConceptType.ALIAS); //uses index _concept.type
				endTask("finishAliases", "deleteBadConcepts:ALIAS", n+" entries");
			}
			
			//-----------------------------------------------------------
			//-----------------------------------------------------------

			if (beginTask("finishAliases", "deleteNullReferences:link.target")) {
				int n = deleteNullReferences(linkTable, "target");  
				if (n>0) warning(-1, "unresolved reference", n+" unresolved entries in "+linkTable.getName()+" deleted", null);
				endTask("finishAliases", "deleteNullReferences:link.target", n+" references");
			}
			/*if (beginTask("finishAliases", "deleteNullReferences:broader")) {
				int n = deleteNullReferences(broaderTable, "broad");     
				if (n>0) warning(-1, "unresolved reference", n+" unresolved entries in "+broaderTable.getName()+".broad deleted", null);
				endTask("finishAliases", "deleteNullReferences:broader", n+" references");
			}
			if (beginTask("finishAliases", "deleteNullReferences:narrower")) {
				int n = deleteNullReferences(broaderTable, "narrow");     
				if (n>0) warning(-1, "unresolved reference", n+" unresolved entries in "+broaderTable.getName()+".narrow deleted", null);
				endTask("finishAliases", "deleteNullReferences:narrower", n+" references");
			}*/
			if (beginTask("finishAliases", "deleteNullReferences:alias")) {
				int n = deleteNullReferences(aliasTable, "target");  
				if (n>0) warning(-1, "unresolved reference", n+" unresolved entries in "+aliasTable.getName()+" deleted", null);
				endTask("finishAliases", "deleteNullReferences:alias", n+" references");
			}
			
			//--------------------------------------------
			//NOTE: deleting would be silly
			//NOTE: we need the alias map for importing langlinks when building global concepts! 
			/*if (beginTask("finishAliases", "deleteBrokenReferences:alias:target")) {
				int n = deleteBrokenReferences(aliasTable, "target_name"); 
				endTask("finishAliases", "deleteBrokenReferences:alias:target", n+" entries");
			}

			if (beginTask("finishAliases", "deleteBrokenReferences:alias:source")) {
				int n = deleteBrokenReferences(aliasTable, "source_name"); 
				endTask("finishAliases", "deleteBrokenReferences:alias:source", n+" entries");
			}*/
			
			//--------------------------------------------
			
			//NOTE: some orphan references may be left here, from bad redirects.
			//Example: Foos redirects to Foo, but foo is Disambig, so alias gets ignored.
			//         Foos is used as category, but concept Foos gets deleted because it's an alias.
			//         In that case, re-create the concept here as a dummy
			
			if (beginTask("finishMissingConcpets", "buildMissingConcepts:narrower")) {
				int n = buildMissingConcepts(broaderTable, "narrow", "narrow_name");  
				endTask("finishMissingConcpets", "buildMissingConcepts:narrower", n+" concepts");
			}

			if (beginTask("finishMissingConcpets", "buildMissingConcepts:broader")) {
				int n = buildMissingConcepts(broaderTable, "broad", "broad_name");  
				endTask("finishMissingConcpets", "buildMissingConcepts:broader", n+" concepts");
			}
	}
	
	public void finishRelations() throws PersistenceException {
		if (beginTask("finishRelations", "buildLangMatch")) {
			int n = buildLangMatch();
			endTask("finishRelations", "buildLangMatch", n+" records");
		}

		if (beginTask("finishRelations", "buildBiLink")) {
			int n = buildBiLink();
			endTask("finishRelations", "buildBiLink", n+" entries");
		}
	}
	
	public void finishMeanings() throws PersistenceException {
			if (beginTask("finishMeanings", "buildMeanings")) {
				int n = buildMeanings();
				endTask("finishMeanings", "buildMeanings", n+" meanings");
			}
	}
	
	public void finishFinish() throws PersistenceException {
		try {
			if (beginTask("finishFinish", "deleteLoops:link.anchor,target")) {
				int n = deleteLoops(linkTable, "anchor", "target");
				endTask("finishFinish", "deleteLoops:link.anchor,target", n+" entries");
			}
			
			if (beginTask("finishFinish", "deleteLoops:broader.broad,narrow")) {
				int n = deleteLoops(broaderTable, "broad", "narrow");
				endTask("finishFinish", "deleteLoops:broader.broad,narrow", n+" entries");
			}

			if (beginTask("finishFinish", "deleteBroaderCycles")) {
				long n = deleteBroaderCycles();
				endTask("finishFinish", "deleteBroaderCycles", n+" entries");
			}

			database.finish();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} catch (InterruptedException e) {
			throw new PersistenceException(e);
		} 
	}
	
	protected int reportBadLinks(DatabaseTable table, String conceptIdField, ConceptType type) throws PersistenceException {
		try {
			ReferenceField refField = (ReferenceField)table.getField(conceptIdField);
			DatabaseTable tgtTable = getTable(refField.getTargetTable());
			DatabaseField tgtField = tgtTable.getField(refField.getTargetField());
			
			if (warningInserter!=null) warningInserter.flush(); //flush first!
			
			//FIXME: sometimes, this is very slow. running the alias query first seems to help. odd :(
			String sql = "INSERT INTO " + warningTable.getSQLName()+ " (timestamp, problem, details, resource) " +
					" SELECT "+ database.quoteString(timestampFormatter.format(new Date())) +", " +
							database.quoteString("broken redirected link ("+type.getName()+")") + ", " +
							"CONCAT("+database.quoteString(table.getName()+"."+conceptIdField+"->")+", "+conceptIdField+"), " + 
							table.getSQLName()+".resource " +
					" FROM "+table.getSQLName()
						+" JOIN "+conceptTable.getSQLName()
						+" ON "+table.getSQLName()+"."+conceptIdField+" = "+tgtTable.getSQLName()+"."+tgtField.getName();
			
			String where = conceptTable.getSQLName()+".type = " + type.getCode();

			//System.out.println("*** "+sql+" ***");
			return executeChunkedUpdate("reportBadLinks", table.getName()+"."+conceptIdField+"="+type, sql, where, conceptTable, "id");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected int deleteLinksToBadConcepts(DatabaseTable table, String conceptIdField, String index, ConceptType... types) throws PersistenceException {
		ReferenceField refField = (ReferenceField)table.getField(conceptIdField);
		DatabaseTable tgtTable = getTable(refField.getTargetTable());
		DatabaseField tgtField = tgtTable.getField(refField.getTargetField());
		
		if (types.length==0) throw new IllegalArgumentException("no bad concept types");
		String bad = database.encodeSet(types);
		
		//FIXME: sometimes, this is very slow. running the alias query first seems to help. odd :(
		String sql = "DELETE FROM "+table.getSQLName()
					+" USING "+table.getSQLName()
						+ ( index==null ? "" :  " force index( "+index+" ) " )
						+" JOIN "+conceptTable.getSQLName()
							+" ON "+table.getSQLName()+"."+conceptIdField+" = "+conceptTable.getSQLName()+"."+tgtField.getName();
		String where = " WHERE "+conceptTable.getSQLName()+".type IN " + bad;

		//System.out.println("*** "+sql+" ***");
		return executeChunkedUpdate("deleteLinksToBadConcepts", table.getName()+"."+conceptIdField+" IN "+bad, sql, where, table, conceptIdField);
	}
	
	protected int deleteBrokenReferences(DatabaseTable table, String ref) throws PersistenceException {
		ReferenceField refField = (ReferenceField)table.getField(ref);
		DatabaseTable tgtTable = getTable(refField.getTargetTable());
		DatabaseField tgtField = tgtTable.getField(refField.getTargetField());
		
		String sql = "DELETE FROM "+table.getSQLName()
					+" USING "+table.getSQLName()
						+" LEFT JOIN "+tgtTable.getSQLName()
							+" ON "+table.getSQLName()+"."+ref+" = "+tgtTable.getSQLName()+"."+tgtField.getName()
					+" WHERE "+tgtTable.getSQLName()+"."+tgtField.getName()+" IS NULL";

		//System.out.println("*** "+sql+" ***");
		return executeUpdate("deleteBrokenReferences", sql); //TODO: chunk how?!
	}
	
	protected int deleteNullReferences(DatabaseTable table, String ref) throws PersistenceException {
		String sql = "DELETE FROM "+table.getSQLName()
					+" WHERE "+ref+" IS NULL";

		//System.out.println("*** "+sql+" ***");
		return executeUpdate("deleteBrokenReferences", sql); //TODO: chunk how?!
	}
	
	protected int deleteDeadendAliases() throws PersistenceException {
		String sql = "DELETE FROM "+conceptTable.getSQLName()
					+" USING "+conceptTable.getSQLName()
						+" LEFT JOIN "+aliasTable.getSQLName()
							+" ON "+aliasTable.getSQLName()+".source = "+conceptTable.getSQLName()+".id "
					+" WHERE "+conceptTable.getSQLName()+".type = "+ConceptType.ALIAS.getCode()
					+" AND "+aliasTable.getSQLName()+".source IS NULL";

		//System.out.println("*** "+sql+" ***");
		return executeUpdate("deleteDeadAliases", sql); //XXX: chunk how
	}
	
	protected int deleteLinksToBadResources(DatabaseTable table, String resourceNameField, ResourceType... types) throws PersistenceException {
		if (types.length==0) throw new IllegalArgumentException("no bad resourcetypes given");
		
		//NOTE: ResourceType.BAD is treated like a missing page, links to it are valid!
		//NOTE: links to ResourceType.REDIRECT are ok and resolved later via the alias table
		
		String bad = database.encodeSet(types);
		
		String sql = "DELETE FROM "+table.getSQLName()
					+" USING "+table.getSQLName()
						+" JOIN "+resourceTable.getSQLName()
							+" ON "+table.getSQLName()+"."+resourceNameField+" = "+resourceTable.getSQLName()+".name"
					+" WHERE "+resourceTable.getSQLName()+".type IN " + bad;

		//System.out.println("*** "+sql+" ***");
		return executeUpdate("deleteLinksToBadResources", sql); //XXX: chunk how?
	}
	
	protected int deleteLinksToGoodConcepts(DatabaseTable table, String conceptField, String where) throws PersistenceException {

		String tgt = ((ReferenceField)table.getField(conceptField)).getTargetField();
		
		String good = "type IS NOT NULL AND type NOT IN ("+ConceptType.UNKNOWN.getCode()+", "+ConceptType.ALIAS.getCode()+")";
		
		String sql = "DELETE FROM "+table.getSQLName()
					+" USING "+table.getSQLName()
						+" LEFT JOIN "+conceptTable.getSQLName()
							+" ON "+table.getSQLName()+"."+conceptField+" = "+conceptTable.getSQLName()+"."+tgt+" "
					+" WHERE " + good;
		
		if (where != null) sql += " AND ( "+where+" )"; 

		//System.out.println("*** "+sql+" ***");
		return executeUpdate("deleteLinksToGoodConcepts", sql); //XXX: chunk how?
	}
	
	protected int deleteBadConcepts(ConceptType type) throws PersistenceException {
		//FIXME: sometimes, this is very slow. running the alias query first seems to help. odd :(
		String sql = "DELETE FROM "+conceptTable.getSQLName()
					+" WHERE type = " + type.getCode();

		return executeUpdate("deleteBadConcepts", sql);
	}

	protected int buildSectionConcepts() throws PersistenceException {
		//NOTE: we shouldn't need the "ignore" bit. Let'S keep it for robustness
		String sql = "INSERT ignore INTO "+conceptTable.getSQLName()+" ( id, name, type, random ) "
					+"SELECT S.section_concept, S.section_name, "+ConceptType.UNKNOWN.getCode()+", RAND() "
					+"FROM "+sectionTable.getSQLName()+" AS S ";
		
		//XXX: no more need for joining...
		//if (idManager!=null) sql += "ON S.concept = C.concept ";
		//else sql += "ON S.concept_name = C.concept_name ";
					
		//String where = "WHERE C.type IS NULL " ; //WTF?! why do we need this?!
		String where = "";
		
		return executeUpdate("buildSectionConcepts", sql+where); //TODO: chunk if ids available?!
		
		//TODO: inject about records, so section concepts are linked to resources? 
	}
	
	protected int buildSectionBroader() throws PersistenceException {
		String sql = "INSERT IGNORE INTO "+broaderTable.getSQLName()+" ( rule, resource, narrow, narrow_name, broad, broad_name ) " +
					"SELECT "+ExtractionRule.BROADER_FROM_SECTION.getCode()+", " +
							"NULL, " + //XXX: could determin resource from the concept associated with the section. but that would be massive overhead for little gain
							"section_concept, " +
							"section_name, " +
							"concept, " +
							"concept_name  " + 
					"FROM "+sectionTable.getSQLName()+" ";   

		return executeUpdate("buildSectionBroader", sql); //XXX: chunk how?
	}
	
	protected int buildMissingConcepts(DatabaseTable table, String conceptIdField, String conceptNameField) throws PersistenceException {
		//NOTE: we shouldn't need the "ignore" bit. Let'S keep it for robustness
		String sql = "INSERT ignore INTO "+conceptTable.getSQLName()+" ( id, name, type, random ) "
					+"SELECT T."+conceptIdField+", T."+conceptNameField+", "+ConceptType.UNKNOWN.getCode()+", RAND() "
					+"FROM "+table.getSQLName()+" as T "
					+"LEFT JOIN "+conceptTable.getSQLName()+" as C ";
		
		if (idManager!=null && conceptIdField!=null) {
			sql += " ON T."+conceptIdField+" = C.id ";

			String where = " WHERE C.id  IS NULL "
				//+"GROUP BY "+conceptNameField
				;

			//NOTE: chunking appears to do more harm than good. MySQL is a mystery.
			return executeUpdate("buildMissingConcepts:"+table.getName()+"."+conceptNameField, sql+where); 
			//return executeChunkedUpdate("buildMissingConcepts", "buildMissingConcepts:"+table.getName()+"."+conceptIdField, sql, where, table, conceptIdField);   
		}
		else  {
			sql += " ON T."+conceptNameField+" = C.name ";

			String where = " WHERE C.name  IS NULL "
				//+"GROUP BY "+conceptNameField
				;
			
			return executeUpdate("buildMissingConcepts:"+table.getName()+"."+conceptNameField, sql+where); 
		}
	}
	
	/**
	 * Builds term->concept relation with frequency;
	 * condenses use table. 
	 */
	protected int buildMeanings() throws PersistenceException {
		String sql = "INSERT INTO "+meaningTable.getSQLName()+" ( concept, concept_name, term_text, freq, rule ) " +
				"SELECT target, target_name, term_text, count(*) as freq, max(rule) as rule " +
				"FROM "+linkTable.getSQLName();
		String group = "GROUP BY target, term_text ";
		
		return executeChunkedUpdate("buildMeanings", "buildMeanings", sql, group, linkTable, "target");
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	protected class DatabaseLocalStatisticsStoreBuilder extends DatabaseStatisticsStoreBuilder {
		protected EntityTable termTable;

		protected DatabaseLocalStatisticsStoreBuilder(StatisticsStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(DatabaseLocalConceptStoreBuilder.this, database, tweaks, agenda);

			Inserter termInserter = configureTable("term", 64, 1024);
			termTable =   (EntityTable)termInserter.getTable();
		}

		@Override
		public void buildTermStatistics() throws PersistenceException {		
			//TODO: characteristic path length and cluster coef
			//TODO: stats about collocations

				if (beginTask("buildStatistics", "stats.termRank")) {
					int n = buildTerms();
					endTask("buildStatistics", "stats.termRank", n+" entries");
				}
				
				if (beginTask("buildStatistics", "stats.termZipf")) {
					buildDistributionStats("term zipf", termTable, "rank", "freq");
					endTask("buildStatistics", "stats.termZipf");
				}
		}
		
		@Override
		public void buildConceptStatistics() throws PersistenceException {		
				super.buildConceptStatistics();
		}
		
		
		/**
		 * Builds term frequency statistics. 
		 */
		protected int buildTerms() throws PersistenceException {
			String sql = "INSERT INTO "+termTable.getSQLName()+" ( term, freq, random ) " +
					"SELECT term_text as term, sum(freq) as freq, RAND() as random " +
					"FROM "+DatabaseLocalConceptStoreBuilder.this.database.getSQLTableName("meaning")+" " +
					"GROUP BY term_text " +
					"ORDER BY freq DESC";
			
			return executeUpdate("buildTerms", sql);
		}

		public void clear() throws PersistenceException {
			super.clear();
			
			try {
				database.truncateTable(termTable.getName(), true);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	
	protected class DatabaseLocalConceptInfoStoreBuilder extends DatabaseConceptInfoStoreBuilder<LocalConcept> {

		protected EntityTable conceptDescriptionTable;

		protected DatabaseLocalConceptInfoStoreBuilder(ConceptInfoStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(DatabaseLocalConceptStoreBuilder.this, database, tweaks, agenda);
			
			Inserter conceptDescriptionInserter = configureTable("concept_description", 64, 1024);
			conceptDescriptionTable = (EntityTable)conceptDescriptionInserter.getTable();
		}
	
		public void buildConceptDescriptionCache() throws PersistenceException {
			if (beginTask("buildConceptDescriptionCache", "prepareConceptCache:concept_description")) {
				int n = prepareConceptCache(conceptDescriptionTable, "concept"); 
				endTask("buildConceptDescriptionCache", "prepareConceptCache:concept_description", n+" concpets");
			}

			if (beginTask("buildConceptDescriptionCache", "prepareConceptCache:concept_description,terms")) {
				int n = buildConceptPropertyCache(conceptDescriptionTable, "concept", "terms", "meaning", "concept", ((ConceptInfoStoreSchema)database).termReferenceListEntry, false, null, 5);
				endTask("buildConceptDescriptionCache", "prepareConceptCache:concept_description,terms", n+" concpets");
			}
		}
				
	}

	//////////////////////////////////////////////////////////////////////////////
	
	private DatabaseTextStoreBuilder textStore;
	private DatabasePropertyStoreBuilder propertyStore;

	protected DatabaseTextStoreBuilder newTextStoreBuilder() throws SQLException, PersistenceException {
		return new DatabaseTextStoreBuilder(this, tweaks);
	}
	
	protected DatabasePropertyStoreBuilder newPropertyStoreBuilder() throws SQLException, PersistenceException {
		return new DatabasePropertyStoreBuilder(this, tweaks);
	}

	public TextStoreBuilder getTextStoreBuilder() throws PersistenceException {
		try { 
			if (textStore==null) textStore = newTextStoreBuilder();
			return textStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}	

	public PropertyStoreBuilder getPropertyStoreBuilder() throws PersistenceException {
		try { 
			if (propertyStore==null) propertyStore = newPropertyStoreBuilder();
			return propertyStore;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}
	
	@Override
	protected DatabaseConceptInfoStoreBuilder<LocalConcept> newConceptInfoStoreBuilder() throws SQLException {
		ConceptInfoStoreSchema schema = new ConceptInfoStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), true, tweaks, false, true);
		schema.setBackgroundErrorHandler(database.getBackgroundErrorHandler());
		return new DatabaseLocalConceptInfoStoreBuilder(schema, tweaks, getAgenda());
	}

	@Override
	protected DatabaseStatisticsStoreBuilder newStatisticsStoreBuilder() throws SQLException {
		StatisticsStoreSchema schema = new LocalStatisticsStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), tweaks, false); 
		schema.setBackgroundErrorHandler(database.getBackgroundErrorHandler());
		return new DatabaseLocalStatisticsStoreBuilder(schema, tweaks, getAgenda());
	}
	
	protected DatabaseProximityStoreBuilder newProximityStoreBuilder() throws SQLException {
		ProximityStoreSchema schema = new ProximityStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), null, false, tweaks, false);
		schema.setBackgroundErrorHandler(database.getBackgroundErrorHandler());
		return new DatabaseProximityStoreBuilder(this, schema, tweaks, getAgenda());
	}

	@Override
	protected DatabaseLocalConceptStore newConceptStore() throws SQLException {
		return new DatabaseLocalConceptStore((LocalConceptStoreSchema)database, tweaks);
	}

	protected DatabaseDataSet.Factory<LocalConcept> localConceptReferenceFactory = new DatabaseDataSet.Factory<LocalConcept>() {
		public LocalConcept newInstance(ResultSet row) throws SQLException, PersistenceException {
			int id = row.getInt("id");
			String name = DatabaseUtil.asString(row.getObject("name"));
			//FIXME: type?!

			LocalConcept concept = new LocalConcept(getCorpus(), id, null, name);
			return concept;
		}
	};
	
	public int processUnknownConcepts(final CursorProcessor<LocalConcept> processor) throws PersistenceException {
		String sql = "SELECT * FROM "+conceptTable.getSQLName();
		String where = "type = "+ConceptType.UNKNOWN.getCode();
		
		DatabaseAccess.SimpleChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(getDatabaseAccess(), "processUnknownConcepts", "process", sql, where, null, conceptTable, "id");
		return executeChunkedQuery(query, 1, localConceptReferenceFactory, processor);
	}
	
	/*public DataSet<LocalConceptReference> listUnknownConcepts() {
		String sql = "SELECT * FROM "+conceptTable.getSQLName()
					+" WHERE type = "+ConceptType.UNKNOWN.getCode();
		
		//FIXME: ugly hack! blackcmagic and all that! we shouldn't think about that here!
		//       anyway: when using a streaming result set leads to errors with unbuffered inserters.
		//       streaming is only needed for big datasets, and unbuffered inserters are generally used only with small datasets. 
		boolean big = useEntityBuffer && useRelationBuffer; 
		
		LocalConceptReferenceQueryDataSet ds = new LocalConceptReferenceQueryDataSet("listUnknownConcepts", sql, big);
		return ds;
	}*/
	
	public DataSet<LocalConcept> listUnknownConcepts() throws PersistenceException {
		String sql = "SELECT * FROM "+conceptTable.getSQLName();
		String where = "type = "+ConceptType.UNKNOWN.getCode();
		
		DatabaseAccess.SimpleChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(getDatabaseAccess(), "processUnknownConcepts", "process", sql, where, null, conceptTable, "id");
		return executeChunkedQuery(query, 4, localConceptReferenceFactory);
	}


	public void resetTermsForUnknownConcepts() throws PersistenceException {
		String sql = "DELETE FROM L"
				+" USING "+linkTable.getSQLName()+" as L "
				+" JOIN "+conceptTable.getSQLName()+" as C ON C.id = L.target "
				+" WHERE C.type = "+ConceptType.UNKNOWN.getCode()
				+" AND L.resource = 0 ";
		
		int c = executeUpdate("resetTermsForUnknownConcepts", sql); //XXX: chunk?!
		log("deleted "+c+" entries for unknown concepts from link table");
		
		sql = "DELETE FROM L"
				+" USING "+broaderTable.getSQLName()+" as L "
				+" JOIN "+conceptTable.getSQLName()+" as C ON C.id = L.narrow "
				+" WHERE C.type = "+ConceptType.UNKNOWN.getCode()
				+" AND L.rule = "+ExtractionRule.BROADER_FROM_SUFFIX.getCode()
				+" AND L.resource = 0 ";

		c = executeUpdate("resetTermsForUnknownConcepts", sql); //XXX: chunk?!
		log("deleted "+c+" entries for unknown concepts from broader table");
	}

	
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
