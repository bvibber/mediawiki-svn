package de.brightbyte.wikiword.store.builder;

import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Arrays;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.sql.DataSource;

import de.brightbyte.application.Agenda;
import de.brightbyte.application.Agenda.Record;
import de.brightbyte.data.ChunkyBitSet;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.GlobalConceptStoreSchema;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.ProximityStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;
import de.brightbyte.wikiword.store.DatabaseGlobalConceptStore;
import de.brightbyte.wikiword.store.DatabaseLocalConceptStore;

/**
 * A GlobalConceptStore implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used by 
 * {@link de.brightbyte.wikiword.store.DatabaseGlobalConceptStore}, see there.
 */
public class DatabaseGlobalConceptStoreBuilder extends DatabaseWikiWordConceptStoreBuilder<GlobalConcept> 
	implements GlobalConceptStoreBuilder {
	
	protected Map<String, DatabaseLocalConceptStore> localStores = new HashMap<String, DatabaseLocalConceptStore>();
	
	protected Inserter mergeInserter;
	
	protected RelationTable originTable;
	protected RelationTable meaningTable;
	protected RelationTable aboutTable;
	protected EntityTable definitionTable;
	
	protected RelationTable mergeTable;
	//protected RelationTable langprepTable;

	protected TweakSet tweaks;
	
	protected int idOffsetGranularity;

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseGlobalConceptStoreBuilder(DatasetIdentifier set, DataSource dbInfo, TweakSet tweaks, Agenda agenda) throws SQLException {
		this(new GlobalConceptStoreSchema(set, dbInfo, tweaks, true), tweaks, agenda);
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
	public DatabaseGlobalConceptStoreBuilder(DatasetIdentifier set, Connection db, TweakSet tweaks, Agenda agenda) throws SQLException {
		this(new GlobalConceptStoreSchema(set, db, tweaks, true), tweaks, agenda);
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
	public DatabaseGlobalConceptStoreBuilder(GlobalConceptStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
		super(database, tweaks, agenda);
		
		this.tweaks = tweaks;
		
		this.idOffsetGranularity = tweaks.getTweak("dbstore.idOffsetGranularity", 1000000); //start id "blocks" at million boundaries

		Inserter originInserter = configureTable("origin", 16, 4*1024);
		originTable = (RelationTable)originInserter.getTable();
		
		Inserter meaningInserter = configureTable("meaning", 16, 4*1024);
		meaningTable = (RelationTable)meaningInserter.getTable();
		
		Inserter aboutInserter = configureTable("about", 16, 4*1024);
		aboutTable = (RelationTable)aboutInserter.getTable();
		
		mergeInserter = configureTable("merge", 16, 4*1024);
		mergeTable = (RelationTable)mergeInserter.getTable();
		
		Inserter definitionInserter = configureTable("definition", 16, 16*1024);
		definitionTable = (EntityTable)definitionInserter.getTable();
		//Inserter langprepInserter = configureTable("langprep", 16, 4*1024);
		//langprepTable = (RelationTable)langprepInserter.getTable();
	}
	
	public LocalConceptStoreSchema getLocalConceptDatabase(Corpus c) throws PersistenceException {
		return ((GlobalConceptStoreSchema)database).getLocalConceptDatabase(c);
	}
	

	/**
	 * @see de.brightbyte.db.DatabaseSchema#createTables(boolean)
	 */
	public void createTables(boolean opt) throws PersistenceException {
		super.createTables(opt);
		
		DatabaseGlobalPropertyStoreBuilder propertyStoreBuilder = getPropertyStoreBuilder();
		propertyStoreBuilder.createTables(opt);		
	}
	
	/**
	 * @see de.brightbyte.db.DatabaseSchema#dropTables(boolean)
	 */
	public void dropTables(boolean opt, boolean all) throws PersistenceException {
		super.dropTables(opt, all);
		
		DatabaseGlobalPropertyStoreBuilder propertyStoreBuilder = getPropertyStoreBuilder();
		propertyStoreBuilder.dropTables(opt, all);		
	}
	
	//-------------------------------
	public Corpus[] detectLanguages() throws PersistenceException {
		try {
			Corpus[] languages = ((GlobalConceptStoreSchema)database).detectLanguages();
			return languages;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void setLanguages(Corpus[] languages) {
		if (languages==null) throw new NullPointerException();
		((GlobalConceptStoreSchema)database).setLanguages(languages);
	}
	
	public void setLanguages(String[] languages) throws PersistenceException {
		if (languages==null) throw new NullPointerException();
		try {
			((GlobalConceptStoreSchema)database).setLanguages(languages);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public Corpus[] getLanguages() throws PersistenceException {
		try {
			return ((GlobalConceptStoreSchema)database).getLanguages();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public int getNextIdOffset() throws PersistenceException {
		int id = getMaxConceptId();
		return ((id / idOffsetGranularity) +1) * idOffsetGranularity;
	}

	protected int deletePendingConceptsAfter(int id) throws PersistenceException {
		try {
			String sql = "DELETE FROM "+conceptTable.getSQLName()+" WHERE id > "+id;
			Integer n = (Integer)database.executeUpdate("deletePendingConceptsAfter", sql);

			sql = "DELETE FROM "+mergeTable.getSQLName()+" WHERE new > "+id;
			database.executeUpdate("deletePendingConceptsAfter", sql);
			
			return n;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public int getMaxConceptId() throws PersistenceException {
		try {
			flush();
			
			String sql = "select max(id) from "+conceptTable.getSQLName();
			Integer id = (Integer)database.executeSingleValueQuery("getMaxConceptId", sql);
			return id == null ? 0 : id;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	//---------------------------------
	
	public void importConcepts() throws PersistenceException {
		Corpus[] cc = getLanguages();
		
		for (Corpus c: cc) {
			LocalConceptStoreSchema localdb = getLocalConceptDatabase(c);
			int ofs = -1;
			
			try {
				//NOTE: should be redundant, but make very sure all indexes are usable!
				//      if indexes are disabled, the stuff below takes a VERY long time!
				localdb.enableKeys();
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
			
			ofs = getNextIdOffset();
			if (beginTask("importConcepts", "importOrigins#"+c.getLanguage(), "lang="+c.getLanguage()+",offset_="+ofs)) {
				//NOTE: allow agenda to override offset
				Agenda.Record rec = getAgenda().getCurrentRecord();
				ofs = (Integer)rec.parameters.get("offset_");
				
				int n = importOrigins(localdb, ofs);
				endTask("importConcepts", "importOrigins#"+c.getLanguage(), n+" records");
			}

			if (beginTask("importConcepts", "importConcepts#"+c.getLanguage(), "lang="+c.getLanguage())) {
				int n = importConcepts(localdb, ofs);
				endTask("importConcepts", "importConcepts#"+c.getLanguage(), n+" records");
			}
			
			if (beginTask("importConcepts", "importLanglinks#"+c.getLanguage(), "lang="+c.getLanguage())) {
				int n = importLanglinks(localdb, ofs);
				endTask("importConcepts", "importLanglinks#"+c.getLanguage(), n+" records");
			}

			//TODO: import later, after clustering, using join over origin?
			//       or import now, and then merge/relink in every pass?
		}

		//NOTE: need to re-iterate over all!
		for (Corpus c: cc) {
			LocalConceptStoreSchema localdb = getLocalConceptDatabase(c);
			
			if (beginTask("importConcepts", "resolveLanglinkAbout#"+c.getLanguage(), "lang="+c.getLanguage())) {
				int n = resolveLanglinkAbout(localdb);
				endTask("importConcepts", "resolveLanglinkAbout#"+c.getLanguage(), n+" records");
			}
			
			if (beginTask("importConcepts", "deleteAliasedLanglinks#"+c.getLanguage(), "lang="+c.getLanguage())) {
				int n = deleteAliasedLanglinks(localdb);
				endTask("importConcepts", "deleteAliasedLanglinks#"+c.getLanguage(), n+" records");
			}
		}
	}
	
	protected int resolveLanglinkAbout(LocalConceptStoreSchema localdb) throws PersistenceException {
		DatabaseTable aboutTable = localdb.getTable("about");
		DatabaseTable langlinkTable = database.getTable("langlink");
		String lang = localdb.getCorpus().getLanguage();
		
		String sql = "UPDATE IGNORE "+langlinkTable.getSQLName()+" as R force index(language_target) "
					+ " JOIN "+aboutTable.getSQLName()+" as A force index(resource_name) "
					+ " ON R.language = "+database.quoteString(lang)+" "
					+ " AND R.target = A.resource_name "
					+ " SET R.target = A.concept_name ";
		
		String where = null;
		//String where = " WHERE A.resource_name != A.concept_name ";
		
		return executeChunkedUpdate("resolveLanglinkAbout", "resolve#"+lang, sql, where, langlinkTable, "R.concept");
	}
	
	protected int deleteAliasedLanglinks(LocalConceptStoreSchema localdb) throws PersistenceException {
		DatabaseTable aboutTable = localdb.getTable("about");
		DatabaseTable langlinkTable = database.getTable("langlink");
		String lang = localdb.getCorpus().getLanguage();
		
		String sql = "DELETE FROM R "
					+ " USING "+langlinkTable.getSQLName()+" as R force index(language_target) " 
					+ " JOIN "+aboutTable.getSQLName()+" as A force index(resource_name) "
					+ " ON R.language = "+database.quoteString(lang)+" "
					+ " AND R.target = A.resource_name ";
		
		String where = " A.resource_name != A.concept_name ";
		
		return executeChunkedUpdate("deleteAliasedLanglinks", "resolve#"+lang, sql, where, langlinkTable, "R.concept");
	}
	
	protected int importOrigins(LocalConceptStoreSchema localdb, int idOffset) throws PersistenceException {		
		String lang = localdb.getCorpus().getLanguage();
		
		int langBit = getLanguageBit(lang);
		DatabaseTable t = localdb.getTable("concept");
		String sql;			
		
		//TODO: reset before retry?
		
		//TODO: store language bit too!
		sql = "insert into "+originTable.getSQLName()+" (global_concept, local_concept, local_concept_name, lang, lang_bit)" +
		" select (id + "+idOffset+") as global_concept, " +
				"id as local_concept, " +
				"name as local_concept_name, " +
				database.quoteString(lang) + " as lang, " + 
				langBit + " as lang_bit " +
		" from " + t.getSQLName() + " as L ";

		return executeChunkedUpdate("importConcepts("+localdb.getCorpus().getLanguage()+")", "origin", sql, null, t, "id");
	}

	protected int importConcepts(LocalConceptStoreSchema localdb, int idOffset) throws PersistenceException {		
		String lang = localdb.getCorpus().getLanguage();
		
		int langBit = getLanguageBit(lang);
		String sql;
		
		//TODO: reset before retry?
		
		DatabaseTable t = localdb.getTable("concept");
		
		sql = "insert into "+conceptTable.getSQLName()+" (id, random, name, type, language_bits, language_count)" +
				" select (L.id + "+idOffset+"), random, concat("+database.quoteString(lang+":")+", L.name), L.type, " + langBit + ", 1 "+
				" from "+t.getSQLName()+" as L ";
		
		return executeChunkedUpdate("importConcepts", localdb.getCorpus().getLanguage(), sql, null, t, "id");
	}
	
	protected int getLanguageBit(String lang) throws PersistenceException {
		try {
			return ((GlobalConceptStoreSchema)database).getLanguageBit(lang);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected int importLanglinks(LocalConceptStoreSchema localdb, int idOffset) throws PersistenceException {		
		String sql;
		DatabaseTable t;

		String lang = localdb.getCorpus().getLanguage();
		
		t = localdb.getTable("langlink");

		sql = "insert ignore into "+langlinkTable.getSQLName()+" (concept, language, target)" +
			" select (LL.concept + "+idOffset+"), LL.language, LL.target " +
			" from "+t.getSQLName()+" as LL ";

		int n = executeChunkedUpdate("importLanglinks("+lang+")", "import", sql, null, t, "concept");
		return n;
	}
	
	protected void importDataAboutConcepts(LocalConceptStoreSchema localdb) throws PersistenceException {
		String lang = localdb.getCorpus().getLanguage();
		//int idOffset = idOffsets.get(lang); //XXX: hack!
		
		String sql;
		DatabaseTable t;
		
		/*
		if (beginTask("importDataAboutConcepts", "importDataAboutConcepts("+lang+").meaning")) {
			sql = "insert ignore into "+meaningTable.getSQLName()+" (concept, freq, rule, term_text, lang)" +
			" select O.global_concept, L.freq, L.rule, L.term_text, " + database.quoteString(lang) +
			" from "+localdb.getSQLTableName("meaning")+" as L "+
			" join "+originTable.getSQLName()+" as O " +
					"ON O.lang = "+database.quoteString(lang)+" AND O.local_concept = L.concept";

			int n = executeUpdate("importDataAboutConcepts#meaning", sql);
			endTask("importDataAboutConcepts", "importDataAboutConcepts("+lang+").meaning", n+" entries");
		}
		*/
		
		if (beginTask("importDataAboutConcepts("+lang+")", "link")) {
			t = localdb.getTable("link");
			
			sql = "insert ignore into "+linkTable.getSQLName()+" (anchor, target)" +
			" select O.global_concept, Q.global_concept " +
			" from "+t.getSQLName()+" as L "+
			" join "+originTable.getSQLName()+" as O " +
				"ON O.lang = "+database.quoteString(lang)+" AND O.local_concept = L.anchor"+
			" join "+originTable.getSQLName()+" as Q " +
				"ON Q.lang = "+database.quoteString(lang)+" AND Q.local_concept = L.target";

			int n = executeChunkedUpdate("importDataAboutConcepts("+lang+")", "link", sql, null, t, "anchor");
			endTask("importDataAboutConcepts("+lang+")", "link", n+" entries");
		}
		
		if (beginTask("importDataAboutConcepts("+lang+")", "broader")) {
			t = localdb.getTable("broader");

			sql = "insert ignore into "+broaderTable.getSQLName()+" (narrow, broad)" +
			" select O.global_concept, Q.global_concept " +
			" from "+t.getSQLName()+" as L "+
			" join "+originTable.getSQLName()+" as O " +
				"ON O.lang = "+database.quoteString(lang)+" AND O.local_concept = L.narrow"+
			" join "+originTable.getSQLName()+" as Q " +
				"ON Q.lang = "+database.quoteString(lang)+" AND Q.local_concept = L.broad";

			int n = executeChunkedUpdate("importDataAboutConcepts("+lang+")", "broader", sql, null, t, "narrow");
			endTask("importDataAboutConcepts("+lang+")", "broader", n+" entries");
		}
		
	}
	
	public void prepareGlobalConcepts() throws PersistenceException {
		if (beginTask("prepareGlobalConcepts", "buildLangRef")) {
			int n = buildLangRef();
			endTask("prepareGlobalConcepts", "buildLangRef", n+" records");
		}

		//NOTE: langref similarity remains asymmetric until after merge!
		
		/*
		if (beginTask("prepareGlobalConcepts", "buildLangRef-reverse")) {
			int n = buildDirectLangMatch(true);
			endTask("prepareGlobalConcepts", "buildLangRef-reverse", n+" records");
		}
		*/
		
		if (beginTask("prepareGlobalConcepts", "buildLangMatch")) {
			int n = buildLangMatch();
			endTask("prepareGlobalConcepts", "buildLangMatch", n+" records");
		}
	}

	protected int buildLangRef() throws PersistenceException {
		String sql = "INSERT IGNORE INTO "+relationTable.getSQLName()+" (concept1, concept2, langref)" +
				" SELECT LL.concept, " +
				"		R.global_concept, " +
				"       1 " +
				" FROM "+langlinkTable.getSQLName()+" as LL force index(concept_language_target) " +
				" JOIN "+originTable.getSQLName()+" as R force index(lang_name) " +
				" ON R.lang = LL.language AND R.local_concept_name = LL.target";
		
		String suffix = " WHERE LL.concept != R.global_concept "
			+" ON DUPLICATE KEY UPDATE langref = langref + VALUES(langref)";
		return executeChunkedUpdate("buildLangRef", "insert:langref", sql, suffix, langlinkTable, "concept");
	}
	
	protected int buildReverseLangRef() throws PersistenceException {
		String sql = "insert ignore into "+relationTable.getSQLName()+" (concept1, concept2, langref)" +
		" select concept2, concept1, A.langref from "+relationTable.getSQLName()+" as A ";
		
		String suffix = " on duplicate key update langref = A.langref + values(langref)"; 

		return executeChunkedUpdate("buildReverseLangRef", "relation:langref#reverse", sql, suffix, relationTable, "concept1");
	}

	/*
	protected int buildLangPrep() throws PersistenceException {
			String sql = "INSERT IGNORE INTO "+langprepTable.getSQLName()+" (concept, concept_bits, target, target_bits)" +
					" SELECT LL.concept, LL.language_bits," +
					"		R.global_concept, R.lang_bit" +
					" FROM "+langlinkTable.getSQLName()+" as LL " +
					" JOIN "+originTable.getSQLName()+" as R ON R.lang = LL.language AND R.local_concept_name = LL.target";
			
			//NOTE: safeguards:
			String where = " R.global_concept != LL.concept "+
					" AND R.lang_bit != LL.language_bits ";
		
			return executeChunkedUpdate("buildLangPrep", "query", sql, where, langlinkTable, "concept");
	}
	*/
	
	protected class FindMergeCandidatesQuery implements DatabaseAccess.ChunkedQuery {
		protected String context;
		protected String name;
		protected ChunkyBitSet stop = new ChunkyBitSet();
		protected int conceptId;
		
		public FindMergeCandidatesQuery(String context, int idOffset) {
			super();
			this.context = context;
			this.name = "offset:"+idOffset;
			this.conceptId = idOffset +1;
		}

		public String getChunkField() {
			return "concept1";
		}

		public DatabaseTable getChunkTable() {
			return relationTable;
		}

		public String getContext() {
			return context;
		}

		public String getName() {
			return name;
		}
		
		public void cleanupDirtyStep(Record rec) throws PersistenceException {
			if (rec.parameters.get("lastConceptId_")!=null) {
				int lastId = (Integer)rec.parameters.get("lastConceptId_");
				log(context+"::"+name+"#FindMergeCandidatesQuery continues in dirty step, deleting pending concepts from id > "+lastId);
				deletePendingConceptsAfter(lastId);
				conceptId = lastId +1;
			}  else {
				int lastId = getMaxConceptId();
				warning(0, context+"::"+name+"#FindMergeCandidatesQuery continues in dirty step, no lastConceptId_", "getMaxConceptId() = "+lastId, null);
				conceptId = lastId +1;
			}
			
			String sql = "SELECT old, new FROM " + mergeTable.getSQLName();
			ResultSet res = DatabaseGlobalConceptStoreBuilder.this.executeQuery(context+"::"+name+"#cleanupDirtyStep.stop", sql);
		
			try {
				while (res.next()) { //TODO: progress? safepoint?
					int leftId = res.getInt("old");
					int rightId = res.getInt("new");
					stop.set(leftId, true);
					stop.set(rightId, true);
				}			
				res.close();
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		public String getQueryState() {
			return "lastConceptId_=I"+conceptId;
		}
		
		public int executeUpdate(int chunk, long first, long end) throws PersistenceException {
			String sql = "SELECT " +
					" L.id as concept1_id, " +
					" L.name as concept1_name, " +
					" L.type as concept1_type, " +
					" L.language_bits as concept1_language_bits, " +
					" L.language_count as concept1_language_count, " +
					" L.random as concept1_random, " +
					" R.id as concept2_id, " +
					" R.name as concept2_name, " +
					" R.type as concept2_type, " +
					" R.language_bits as concept2_language_bits, " +
					" R.language_count as concept2_language_count, " +
					" R.random as concept2_random " +
				" FROM "+relationTable.getSQLName()+" as J1 force index(PRIMARY) "+
				" JOIN "+relationTable.getSQLName()+" as J2  force index(PRIMARY) ON J2.concept1 = J1.concept2 "+
				" JOIN "+conceptTable.getSQLName()+" as L force index(PRIMARY) ON L.id = J1.concept1 " +
				" JOIN "+conceptTable.getSQLName()+" as R force index(PRIMARY) ON R.id = J2.concept1 " +
				" WHERE (L.language_bits & R.language_bits) = 0 " +
				" AND (J1.concept1 >= "+first+" AND J1.concept1 < "+end+")" +
				" AND (J1.langref >= 1 AND J2.langref >= 1) " +
				" AND (J1.concept1 > J1.concept2)";
			
			//XXX: maybe don't join concepts, and fetch them one by one? overhead, but less initial cost
			
			int n = 0;
			try {
				ResultSet res = DatabaseGlobalConceptStoreBuilder.this.executeQuery(context+"::"+name+"#chunk"+chunk, sql);
				while (res.next()) { //TODO: progress? safepoint?
					int leftId = res.getInt("concept1_id");
					int rightId = res.getInt("concept2_id");
					
					if (stop.set(leftId, true)) continue;
					if (stop.set(rightId, true)) continue;
		
					String leftName =   asString(res.getObject("concept1_name"));
					int leftType =      res.getInt("concept1_type");
					int leftLanguageBits = res.getInt("concept1_language_bits");
					int leftLanguageCount = res.getInt("concept1_language_count");
					double leftRandom = res.getDouble("concept1_random");
					
					String rightName =   asString(res.getObject("concept2_name"));
					int rightType =      res.getInt("concept2_type");
					int rightLanguageBits = res.getInt("concept2_language_bits");
					int rightLanguageCount = res.getInt("concept2_language_count");
					double rightRandom = res.getDouble("concept2_random");
					
					if ((rightLanguageBits & leftLanguageBits)>0) {
						continue; //ERROR! should never happen!
					}
					
					int type;
					if (leftType==rightType) type = rightType;
					else if (ConceptType.isWeak(leftType)) type = rightType;
					else if (ConceptType.isWeak(rightType)) type = leftType;
					else {
						storeWarning(-1, "conflicting concept types during merge", 
								"concept "+leftName+" has type "+getConceptType(leftType)+", "
								+"concept "+rightName+" has type "+getConceptType(rightType));
						
						type = rightType;
					}
					
					int language_bits = leftLanguageBits | rightLanguageBits;
					int language_count = leftLanguageCount + rightLanguageCount;
					String name = mergeName(leftName, rightName);
					
					double random = leftRandom == 0 ? rightRandom : leftRandom;
					
					conceptId++;
					conceptInserter.updateInt("id", conceptId);
					conceptInserter.updateDouble("random", random); 
					conceptInserter.updateString("name", name);
					conceptInserter.updateInt("type", type);
					conceptInserter.updateInt("language_bits", language_bits);
					conceptInserter.updateInt("language_count", language_count);
					conceptInserter.updateRow();
				
					mergeInserter.updateInt("old", leftId);
					mergeInserter.updateInt("new", conceptId);
					mergeInserter.updateRow();
					
					mergeInserter.updateInt("old", rightId);
					mergeInserter.updateInt("new", conceptId);
					mergeInserter.updateRow();
					
					n++;
				}
				
				res.close();
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
			
			flush();
			
			return n;
		}

		public ResultSet executeQuery(int chunk, long first, long end) throws Exception {
			throw new UnsupportedOperationException();
		}

	}
	
	public int findMergeCandidates(int idOffset) throws PersistenceException {
		log("finding merge candidates, starting at id offeset "+idOffset);

		FindMergeCandidatesQuery query = new FindMergeCandidatesQuery("findMergeCandidates", idOffset);
		return executeChunkedUpdate(query, 5);
	}

	protected static final Pattern nameSeparator = Pattern.compile("\\|");
	protected final Matcher nameSeparatorMatcher = nameSeparator.matcher(""); 
	
	protected String mergeName(String a, String b) {
		//XXX: hot spot!
		String[] aa = nameSeparator.split(a); 
		String[] bb = nameSeparator.split(b);
		
		int i = 0;
		int j = 0;
		String[] nn = new String[aa.length+bb.length];
		
		for (int k=0; k<nn.length; k++) {
			if (i<aa.length && (j>=bb.length || aa[i].compareTo(bb[j])<0)) {
				nn[k] = aa[i++];
			}
			else {
				nn[k] = bb[j++];				
			}
		}
		
		return StringUtils.join("|", nn);
	}

	public void buildGlobalConcepts() throws PersistenceException {
		if (beginTask("buildGlobalConcepts", "prepareGlobalConcepts")) {
			prepareGlobalConcepts();
			endTask("buildGlobalConcepts", "prepareGlobalConcepts");
		}
		
		if (beginTask("buildGlobalConcepts", "clusterGlobalConcepts")) {
			clusterGlobalConcepts();
			endTask("buildGlobalConcepts", "clusterGlobalConcepts");
		}

		if (beginTask("buildGlobalConcepts", "finishGlobalConcepts")) {
			finishGlobalConcepts();
			endTask("buildGlobalConcepts", "finishGlobalConcepts");
		}
	}
	
	public void buildGlobalIndexes() throws PersistenceException {
		if (beginTask("buildGlobalIndexes", "importAbout")) {
			importAbout();
			endTask("buildGlobalIndexes", "importAbout");
		}

		if (beginTask("buildGlobalIndexes", "importProperties")) {
			importProperties();
			endTask("buildGlobalIndexes", "importProperties");
		}

		if (beginTask("buildGlobalIndexes", "buildMeaningIndex")) {
			buildMeaningIndex();
			endTask("buildGlobalIndexes", "buildMeaningIndex");
		}
	}
	
	protected void buildMeaningIndex() throws PersistenceException {
		Corpus[] cc = getLanguages();
		
		for (Corpus c: cc) {
			if (beginTask("buildMeaningIndex", "importMeanings#"+c.getLanguage())) {
				importMeanings(c);
				endTask("buildMeaningIndex", "importMeanings#"+c.getLanguage());
			}
		}		
	}
	
	private int importMeanings(Corpus c) throws PersistenceException {
		LocalConceptStoreSchema localdb = getLocalConceptDatabase(c);
		DatabaseTable localMeanings = localdb.getTable("meaning");
		DatabaseTable origin = database.getTable("origin");
		
		String sql = "INSERT INTO "+meaningTable.getSQLName()+" (concept, lang, rule, freq, term_text) "
			+ " SELECT O.global_concept, "+database.quoteString(c.getLanguage())+", M.rule, M.freq, M.term_text "
			+ " FROM "+origin.getSQLName()+" as O " 
			+ " JOIN "+localMeanings.getSQLName()+" as M ON M.concept = O.local_concept "
			+ " AND O.lang = "+database.quoteString(c.getLanguage());
		
		int n = executeChunkedUpdate("importMeanings", "import("+c.getLanguage()+")", sql, null, localMeanings, "M.concept");
		return n;
	}
	
	public void importAbout() throws PersistenceException {
		Corpus[] cc = getLanguages();
		
		for (Corpus c: cc) {
			if (beginTask("importAbout", "importAbout#"+c.getLanguage())) {
				importAbout(c);
				endTask("importAbout", "importAbout#"+c.getLanguage());
			}
		}		
	}

	public void importProperties() throws PersistenceException {
		Corpus[] cc = getLanguages();
		DatabaseGlobalPropertyStoreBuilder properties = getPropertyStoreBuilder();
		
		for (Corpus c: cc) {
			if (beginTask("importProperties", "importProperties#"+c.getLanguage())) {
				properties.importProperties(c);
				endTask("importProperties", "importProperties#"+c.getLanguage());
			}
		}		
	}

	protected DatabaseGlobalPropertyStoreBuilder propertyStoreBuilder = null;
	
	protected DatabaseGlobalPropertyStoreBuilder getPropertyStoreBuilder() throws PersistenceException {
		try {
			if ( propertyStoreBuilder == null ) propertyStoreBuilder = 
				new DatabaseGlobalPropertyStoreBuilder((GlobalConceptStoreSchema)database, tweaks, getAgenda());
			
			return propertyStoreBuilder;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	private int importAbout(Corpus c) throws PersistenceException {
		LocalConceptStoreSchema localdb = getLocalConceptDatabase(c);
		DatabaseTable localAbout = localdb.getTable("about");
		DatabaseTable localResource = localdb.getTable("resource");
		DatabaseTable origin = database.getTable("origin");
		
		String sql = "INSERT INTO "+aboutTable.getSQLName()+" (concept, lang, local_resource, local_resource_name, type) "
			+ " SELECT O.global_concept, "+database.quoteString(c.getLanguage())+", R.id, R.name, R.type "
			+ " FROM "+origin.getSQLName()+" as O " 
			+ " JOIN "+localAbout.getSQLName()+" as A ON A.concept = O.local_concept "
			+ " 																			AND O.lang = "+database.quoteString(c.getLanguage()) + " "
			+ " JOIN "+localResource.getSQLName()+" as R ON R.id = A.resource ";
		
		int n = executeChunkedUpdate("importAbout", "import("+c.getLanguage()+")", sql, null, localAbout, "A.concept");
		return n;
	}

	public void clusterGlobalConcepts() throws PersistenceException {
		int i = 0;
		while (true) {
			i++;
			log("CLUSTERING ROUND #"+i);
			//TODO: recovery
			
			int ofs = getNextIdOffset(); 
			if (beginTask("clusterGlobalConcepts", "findMergeCandidates#"+i, "offset_=I"+ofs)) {
				//NOTE: allow agenda to override offset
				Agenda.Record rec = getAgenda().getCurrentRecord();
				ofs = (Integer)rec.parameters.get("offset_");
				
				//NOTE: don't call deletePendingConceptsAfter(ofs) here, do cleanup per chunk!
				
				int c = findMergeCandidates(ofs);
				flush();
				
				endTask("clusterGlobalConcepts", "findMergeCandidates#"+i, c+" candidates");
				
				if (c==0) {
					log("CLUSTERING COMPLETE: round #"+i+" yielded no candidates");
					break;
				}
			}
				
			if (beginTask("clusterGlobalConcepts", "performMerge#"+i)) {
				performMerge();
				flush();
				
				endTask("clusterGlobalConcepts", "performMerge#"+i);
			}
		}
	}
	
	protected void performMerge() throws PersistenceException  {
		try {
			flush();
			
			if (beginTask("performMerge", "resolveMerge:origin")) {
				int n = resolveMerge(originTable, "global_concept");
				endTask("performMerge", "resolveMerge:origin", n+" entries");
			}
			//NOTE: resolve all tables that are already populated
			//      broader, langlinks and meaning are imported later
			
			/*
			if (beginTask("performMerge", "deleteMerged:relation.concept1")) {
				int n = deleteMerged(relationTable, "concept1");
				endTask("performMerge", "deleteMerged:relation.concept1", n+" entries");
			}

			if (beginTask("performMerge", "deleteMerged:relation.concept2")) {
				int n = deleteMerged(relationTable, "concept2");
				endTask("performMerge", "deleteMerged:relation.concept2", n+" entries");
			}
			*/
			
			if (beginTask("performMerge", "insertMerge:langlink.concept")) {
				int n = insertMerged(langlinkTable, "concept", "language_bits", "BIT_OR", "language, target");
				endTask("performMerge", "insertMerge:langlink.concept", n+" entries");
			}
			
			if (beginTask("performMerge", "deleteMerged:langlink.concept")) {
				int n = deleteMerged(langlinkTable, "concept");
				endTask("performMerge", "deleteMerged:langlink.concept", n+" entries");
			}
			
			if (beginTask("performMerge", "insertMerge:relation.concept1")) {
				int n = insertMerged(relationTable, "concept1", "langref,langmatch,bilink", "SUM", "concept2");
				endTask("performMerge", "insertMerge:relation.concept1", n+" entries");
			}

			if (beginTask("performMerge", "insertMerge:relation.concept2")) {
				int n = insertMerged(relationTable, "concept2", "langref,langmatch,bilink", "SUM", "concept1"); 
				endTask("performMerge", "insertMerge:relation.concept2", n+" entries");
			}
			
			//TODO: recalculate langmatch and bilink after merging.
			
			if (beginTask("performMerge", "deleteMerged:relation.concept1")) {
				int n = deleteMerged(relationTable, "concept1");
				endTask("performMerge", "deleteMerged:relation.concept1", n+" entries");
			}

			if (beginTask("performMerge", "deleteMerged:relation.concept2")) {
				int n = deleteMerged(relationTable, "concept2");
				endTask("performMerge", "deleteMerged:relation.concept2", n+" entries");
			}
		
			if (beginTask("performMerge", "deleteLoops:relation.concept1,concept2")) {
				int n = deleteLoops(relationTable, "concept1", "concept2");
				endTask("performMerge", "deleteLoops:relation.concept1,concept2", n+" entries");
			}

			if (beginTask("performMerge", "deleteMerged:concept.id")) {
				int n = deleteMerged(conceptTable, "id");
				endTask("performMerge", "deleteMerged:concept.id", n+" entries");
			}

			/*
			if (beginTask("performMerge", "buildSimilarities")) {
				buildSimilarities(true);  
				endTask("performMerge", "buildSimilarities");
			}
			*/
			database.truncateTable(mergeTable.getName(), false);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected int resolveMerge(DatabaseTable t, String field) throws SQLException {
		DatabaseField f = t.getField(field);
		String sql = "UPDATE "+t.getSQLName()+" AS T "+
				" JOIN  " + mergeTable.getSQLName()+" AS M ON M.old = T."+f.getName()+" "+
				" SET T."+f.getName()+" = M.new";
		
		return database.executeUpdate("resolveMerge", sql);
	}

	protected int insertMerged(DatabaseTable t, String idField, String aggregateField, String aggegateFunction, String otherId) throws SQLException {
		DatabaseField id = t.getField(idField);
		
		Set<String> aggr = new HashSet<String>(Arrays.asList(aggregateField.split("\\s*[\\s,;:/|]\\s*"))); 
		
		StringBuilder fields = new StringBuilder();
		StringBuilder values = new StringBuilder();
		
		boolean first = true;
		Map<String, DatabaseField> ff = t.getFields();
		for (DatabaseField f: ff.values()) {
			if (first) first = false;
			else {
				fields.append(", ");
				values.append(", ");
			}

			String n = f.getName();
			fields.append(n);
			
			if (n.equals(idField)) {
				values.append("M.new");
			}
			else if (aggr.contains(n)) {
				values.append(aggegateFunction).append("(").append(n).append(")");
			}
			else {
				values.append(n);
			}
		}
		
		String sql = "INSERT INTO "+t.getSQLName()+" ( "+fields+" ) "+
				" SELECT "+values+" " +
				" FROM " + t.getSQLName()+" AS T "+
				" JOIN " + mergeTable.getSQLName()+" AS M ON M.old = T."+id.getName()+" "+
				" GROUP BY M.new";
		
		if (otherId!=null) sql += ", " + otherId;
		
		return database.executeUpdate("insertMerged", sql);
	}

	protected int deleteMerged(DatabaseTable t, String field) throws SQLException {
		DatabaseField f = t.getField(field);
		String sql = "DELETE FROM T USING  "+t.getSQLName()+" AS T "+
				" JOIN  " + mergeTable.getSQLName()+" AS M ON M.old = T."+f.getName();
		
		return database.executeUpdate("deleteMerged", sql);
	}

	public void finishGlobalConcepts() throws PersistenceException {
		/*
		if (beginTask("finishGlobalConcepts", "origin")) {
			String sql = "update "+originTable.getSQLName()+" as O " +
			" join "+conceptTable.getSQLName()+" as C " +
				"ON O.global_concept = C.id " +
			" set O.global_concept_name = C.name ";
			
			int n = executeChunkedUpdate("finishGlobalConcepts", "origin", sql, null, conceptTable, "id");
			endTask("finishGlobalConcepts", "origin", n+" entries");
		}
		*/
		
		Corpus[] cc = getLanguages();
		for (Corpus c: cc) {
			String task = "finishGlobalConcepts#"+c.getLanguage();
			if (beginTask("finishGlobalConcepts", task)) {
				LocalConceptStoreSchema localdb = getLocalConceptDatabase(c);
				importDataAboutConcepts(localdb);
				endTask("finishGlobalConcepts", task);
			}
		}
		
		if (beginTask("finishGlobalConcepts", "deleteLoops:link.anchor,target")) {
			try {
				int n = deleteLoops(linkTable, "anchor", "target");
				endTask("finishGlobalConcepts", "deleteLoops:link.anchor,target", n+" entries");
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		if (beginTask("finishGlobalConcepts", "deleteLoops:broader.broad,narrow")) {
			try {
				int n = deleteLoops(broaderTable, "broad", "narrow");
				endTask("finishGlobalConcepts", "deleteLoops:broader.broad,narrow", n+" entries");
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

		if (beginTask("finishGlobalConcepts", "deleteBroaderCycles")) {
			long n = deleteBroaderCycles();
			endTask("finishGlobalConcepts", "deleteBroaderCycles", n+" entries");
		}

		if (beginTask("finishGlobalConcepts", "buildBiLink")) {
			int n = buildBiLink();
			endTask("finishGlobalConcepts", "buildBiLink", n+" entries");
		}
		
		if (beginTask("finishGlobalConcepts", "buildReverseLangRef")) {
			int n = buildReverseLangRef();
			endTask("finishGlobalConcepts", "buildReverseLangRef", n+" entries");
		}
		
	}
	
	public ConceptType getConceptType(int type) throws PersistenceException {
		try {
			return database.getConceptType(type);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	protected class DatabaseGlobalStatisticsStoreBuilder extends DatabaseStatisticsStoreBuilder {

		protected DatabaseGlobalStatisticsStoreBuilder(StatisticsStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(DatabaseGlobalConceptStoreBuilder.this, database, tweaks, agenda);
		}
		
		@Override
		public void buildConceptStatistics() throws PersistenceException {
			//TODO: langlink-stats, etc
			super.buildConceptStatistics();
		}
		
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	
	protected class DatabaseGlobalConceptInfoStoreBuilder extends DatabaseConceptInfoStoreBuilder<GlobalConcept> {
		
		protected DatabaseGlobalConceptInfoStoreBuilder(ConceptInfoStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
			super(DatabaseGlobalConceptStoreBuilder.this, database, tweaks, agenda);
		}
			
		@Override
		public void buildConceptRelationCache() throws PersistenceException {
			super.buildConceptRelationCache();

			//XXX: different similarities / thresholds!
			if (beginTask("buildConceptRelationCache", "prepareConceptCache:concept_info,similar#langref")) {
				int n = buildConceptPropertyCache(conceptInfoTable, "concept", "similar", "relation", "concept1", ((ConceptInfoStoreSchema)database).similarReferenceListEntry, true, "langref > 0", 1);
				endTask("buildConceptRelationCache", "prepareConceptCache:concept_info,similar#langref", n+" entries");
			}
		}

		public void buildConceptDescriptionCache() throws PersistenceException {
			throw new UnsupportedOperationException();
		}

	}

	//////////////////////////////////////////////////////////////////////////////
	
	@Override
	protected DatabaseConceptInfoStoreBuilder<GlobalConcept> newConceptInfoStoreBuilder() throws SQLException {
		ConceptInfoStoreSchema schema = new ConceptInfoStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), false, tweaks, false, false);
		return new DatabaseGlobalConceptInfoStoreBuilder(schema, tweaks, getAgenda());
	}

	@Override
	protected DatabaseStatisticsStoreBuilder newStatisticsStoreBuilder() throws SQLException {
		StatisticsStoreSchema schema = new StatisticsStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), true, tweaks, false); 
		return new DatabaseGlobalStatisticsStoreBuilder(schema, tweaks, getAgenda());
	}
	
	protected DatabaseProximityStoreBuilder newProximityStoreBuilder() throws SQLException {
		ProximityStoreSchema schema = new ProximityStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), null, true, tweaks, false);
		schema.setBackgroundErrorHandler(database.getBackgroundErrorHandler());
		return new DatabaseProximityStoreBuilder(this, schema, tweaks, getAgenda());
	}

	@Override
	protected DatabaseGlobalConceptStore newConceptStore() throws SQLException {
		return new DatabaseGlobalConceptStore((GlobalConceptStoreSchema)database, tweaks);
	}
}
