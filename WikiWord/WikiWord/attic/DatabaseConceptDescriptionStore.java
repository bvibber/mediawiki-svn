package de.brightbyte.wikiword.store;

import java.io.File;
import java.io.IOException;
import java.net.URISyntaxException;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Map;

import javax.sql.DataSource;

import de.brightbyte.application.Arguments;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.io.Output;
import de.brightbyte.io.PrintStreamOutput;
import de.brightbyte.rdf.RdfSinkException;
import de.brightbyte.rdf.RdfVocabException;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TsvDumper;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.LocalConceptReference;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordReference;
import de.brightbyte.wikiword.schema.ConceptDescriptionStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;

/**
 * A LocalConceptStore implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used by 
 * {@link de.brightbyte.wikiword.store.DatabaseConceptDescriptionStore}, see there.
 */
public class DatabaseConceptDescriptionStore<T extends WikiWordConcept> extends DatabaseWikiWordStore implements ConceptDescriptionStore<T>, ConceptDescriptionStoreBuilder<T> {
	
	protected class ConceptDataSet extends DatabaseDataSet<T> {
		public ConceptDataSet(DatabaseAccess access, String name, DatabaseWikiWordConceptStore.ConceptQuerySpec query) {
			super(access, name, getQuerySQL(query));
		}

		@Override
		protected T newInstance(ResultSet row) throws SQLException, PersistenceException {
			Map<String, Object> m = DatabaseSchema.rowMap(row);
			
			return makeConcept(m);
		}
	}

	protected Corpus corpus;
	protected WikiWordConceptStoreSchema localConceptDatabase;
	protected EntityTable conceptInfoTable;
	protected EntityTable conceptDescriptionTable;
	protected WikiWordConcept.Factory<T> conceptFactory;

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseConceptDescriptionStore(Corpus corpus, DataSource dbInfo, TweakSet tweaks) throws SQLException {
		this(new ConceptDescriptionStoreSchema(corpus, dbInfo, tweaks, false), tweaks);
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
	public DatabaseConceptDescriptionStore(Corpus corpus, Connection db, TweakSet tweaks) throws SQLException {
		this(new ConceptDescriptionStoreSchema(corpus, db, tweaks, false), tweaks);
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
	protected DatabaseConceptDescriptionStore(ConceptDescriptionStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);
		this.corpus = database.getCorpus();
		
		//XXX: wen don't need inserters, really...
		Inserter conceptInfoInserter = configureTable("concept_info", 64, 1024);
		Inserter conceptDescriptionInserter = configureTable("concept_description", 64, 1024);
		
		conceptInfoTable = (EntityTable)conceptInfoInserter.getTable();
		conceptDescriptionTable = (EntityTable)conceptDescriptionInserter.getTable();
		
		localConceptDatabase = new WikiWordConceptStoreSchema(corpus.getDbPrefix(), database.getConnection(), tweaks, false);
	}
	
	/*
	protected Object executeSingletonQuery(String sql) throws SQLException {
		return database.executeSingletonQuery(sql);
	}
	*/

	public DataSet<T> getMeanings(String term)
		throws PersistenceException { //TODO: relevance limit? order?
		
		DatabaseWikiWordConceptStore.ConceptQuerySpec q = new DatabaseWikiWordConceptStore.ConceptQuerySpec(
				"meaning",
				"concept", "concept_name",
				"freq", null,
				null,
				"term_text = "+database.quoteString(term)
				);
		
		return new ConceptDataSet(database, "getMeanins", q);
	}

	public DataSet<T> getAllConcepts() throws PersistenceException {
		DatabaseWikiWordConceptStore.ConceptQuerySpec q = new DatabaseWikiWordConceptStore.ConceptQuerySpec(
				"concept",
				"id", "name",
				null, null,
				null,
				null
				);

		return new ConceptDataSet(database, "getAllConcepts", q);
	}

	public DataSet<T> getConcepts(DataSet<? extends WikiWordReference<T>> refs) {
		DatabaseWikiWordConceptStore.ConceptQuerySpec q = ((DatabaseLocalConceptStore.LocalConceptReferenceDataSet)refs).getQuerySpec();
		return new ConceptDataSet(database, "getLocalConcepts", q);
	}

	public T getLocalConcept(LocalConceptReference ref) throws PersistenceException {
		int id = ref.getId();
		return getConcept(id);
	}
	
	protected String getQuerySQL(DatabaseWikiWordConceptStore.ConceptQuerySpec q) {
		String by; 
		
		if (q.relevField!=null) by = " ORDER BY "+q.relevField+" DESC"; 
		else if (q.cardField!=null) by = " ORDER BY "+q.cardField+" DESC"; 
		else if (q.nameField!=null) by = " ORDER BY "+q.nameField+" ASC"; 
		else if (q.idField!=null) by = " ORDER BY "+q.idField+" ASC"; 
		else by = "";
	
		String sql = "SELECT " + 
				(q.cardField==null?"-1":q.cardField) + " as qFreq, " + 
				(q.relevField==null?"-1":q.relevField) + " as qConf, " + 
				" C.id as cId, C.name as cName, C.type as cType, " +
				" R.id as rcId, R.name as rcName, R.type as rcType, " +
				" F.definition as fDefinition, " +
				" I.broader as rBroader, I.narrower as rNarrower, I.langlinks as rLanglinks, " +
				" D.terms as dTerms" +
		" FROM " + localConceptDatabase.getSQLTableName(q.table) + " as T " +
		" JOIN " + localConceptDatabase.getSQLTableName("concept") + " as C ON C.id = " + q.idField +
		" JOIN " + conceptInfoTable.getSQLName() + " as I ON I.concept = " + q.idField +
		" JOIN " + conceptDescriptionTable.getSQLName() + " as D ON D.concept = " + q.idField +
		" LEFT JOIN " + localConceptDatabase.getSQLTableName("definition") + " as F ON F.concept = " + q.idField +
		" LEFT JOIN " + localConceptDatabase.getSQLTableName("resource") + " as R ON R.id = C.resource " +
		( q.join == null ? "" : q.join ) +
		(q.where==null ? "" : " WHERE " + q.where) +
		by;
		
		return sql;
	}
	
	public T getConcept(int id) throws PersistenceException {
		try {
			if (id<=0) throw new IllegalArgumentException("bad concept id: "+id);

			DatabaseWikiWordConceptStore.ConceptQuerySpec q = new DatabaseWikiWordConceptStore.ConceptQuerySpec(
					"concept",
					"id", "name",
					null, null,
					null,
					"if = "+id
					);
			
			String sql = getQuerySQL(q);
			Map<String, Object> m = database.executeSingleRowQuery("getLocalConcept", sql);
			return makeConcept(m);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}

	}
	
	public T makeConcept(Map<String, Object> m) throws PersistenceException {
		if (corpus!=null) m.put("corpus", corpus); //XXX: evil hack!
		return conceptFactory.newInstance(m);
	}

	@Override
	protected ConceptType getConceptType(int type) {
		if (corpus==null) return super.getConceptType(type);
		else return corpus.getConceptTypes().getType(type);
	}

	public Corpus getCorpus() {
		return corpus;
	}

	@Override
	protected void deleteDataFrom(int rcId, String op) throws PersistenceException {
		deleteDataFrom(rcId, op, conceptInfoTable, "concept", localConceptDatabase.getTable("concept"), "resource");
		deleteDataFrom(rcId, op, conceptDescriptionTable, "concept", localConceptDatabase.getTable("concept"), "resource");
	}
	
	public void buildConceptDescriptions() throws PersistenceException {
		try {
			if (shouldRun("finish.prepareConceptCache:concept_info")) prepareConceptCache(conceptInfoTable, "concept");
			if (shouldRun("finish.buildConceptPropertyCache:concept_info,narrower")) buildConceptPropertyCache(conceptInfoTable, "concept", "narrower", localConceptDatabase.getTable("broader"), "broad", ConceptDescriptionStoreSchema.narrowerReferenceListEntry.sqlField);
			if (shouldRun("finish.buildConceptPropertyCache:concept_info,broader")) buildConceptPropertyCache(conceptInfoTable, "concept", "broader", localConceptDatabase.getTable("broader"), "narrow", ConceptDescriptionStoreSchema.broaderReferenceListEntry.sqlField);
			if (shouldRun("finish.buildConceptPropertyCache:concept_info,langlinks")) buildConceptPropertyCache(conceptInfoTable, "concept", "langlinks", localConceptDatabase.getTable("langlink"), "concept", ConceptDescriptionStoreSchema.langlinkReferenceListEntry.sqlField);
			
			if (shouldRun("finish.prepareConceptCache:concept_description")) prepareConceptCache(conceptDescriptionTable, "concept"); 
			if (shouldRun("finish.buildConceptPropertyCache:concept_description,terms")) buildConceptPropertyCache(conceptDescriptionTable, "concept", "terms", localConceptDatabase.getTable("meaning"), "concept", ConceptDescriptionStoreSchema.termReferenceListEntry.sqlField);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected void prepareConceptCache(DatabaseTable cacheTable, String conceptIdField) throws PersistenceException {
		log("preparing concept cache table "+cacheTable.getName());
		
		String sql = "INSERT ignore INTO "+cacheTable.getSQLName()+" ( " + conceptIdField + " ) "
			+" SELECT id "
			+" FROM "+localConceptDatabase.getSQLTableName("concept");

		long t = System.currentTimeMillis();
		int n = executeUpdate("prepareConceptCache", sql);
		
		log("prepared "+n+" rows in concept cache table "+cacheTable.getName()+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void buildConceptPropertyCache(DatabaseTable cacheTable, String cacheIdField, String propertyField, DatabaseTable relationTable, String relConceptField, String fieldPattern) throws PersistenceException {
		log("building concept property cache from "+relationTable+" into "+cacheTable.getName()+"."+propertyField);
		
		//XXX: if no frequency-field, evtl use inner grouping by nameField to determin frequency! (expensive, though)
		
		String sql = "UPDATE "+cacheTable.getSQLName()+" AS C "
			+" JOIN ( SELECT "+relConceptField+", group_concat("+fieldPattern+" separator '"+ConceptDescriptionStoreSchema.referenceSeparator+"' ) as s" +
					"	 FROM "+relationTable.getSQLName()+" group by "+relConceptField+" )" +
					" AS R " 
			+" ON R."+relConceptField+" = C."+cacheIdField
			+" SET "+propertyField+" = s";

		long t = System.currentTimeMillis();
		int n = executeUpdate("buildConceptPropertyCache", sql);
		
		log("updated "+n+" rows in "+cacheTable.getName()+" from "+relationTable+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	@Override
	public boolean isComplete() throws PersistenceException {
		try {
			if (!database.tableExists("concept_info")) return false;
			if (!database.tableExists("concept_description")) return false;
			
			String sql = "select count(*) from "+localConceptDatabase.getSQLTableName("concept");
			int c = ((Number)database.executeSingleValueQuery("isComplete", sql)).intValue();

			sql = "select count(*) from "+database.getSQLTableName("concept_info");
			int ci = ((Number)database.executeSingleValueQuery("isComplete", sql)).intValue();
			if (ci!=c) return false;
			
			sql = "select count(*) from "+database.getSQLTableName("concept_description");
			int cd = ((Number)database.executeSingleValueQuery("isComplete", sql)).intValue();
			if (cd!=c) return false;		
			
			return true;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public static void main(String[] argv) throws URISyntaxException, IOException, InstantiationException, IllegalAccessException, SQLException, InterruptedException, RdfSinkException, RdfVocabException, PersistenceException {
		Arguments args = new Arguments();
		args.parse(argv);
		
		Output out = new PrintStreamOutput(System.out);
		
		File dbf = new File(args.getParameter(1));
		
		DataSource db = new DatabaseConnectionInfo(dbf);
		Corpus corpus = Corpus.forName(args.getParameter(0));

		TweakSet tweaks = new TweakSet();
		DatabaseConceptDescriptionStore store = new DatabaseConceptDescriptionStore(corpus, db, tweaks);
		store.setLogLevel(args.getIntOption("loglevel", DatabaseSchema.LOG_INFO)); 
			
		store.open();	

		try {
			if (args.isSet("check")) {
				store.checkConsistency();
				out.println("OK.");
			}
			else if (args.isSet("stats")) {
				store.dumpTableStats(out);
				out.println("END.");
			}
			if (args.isSet("meanings")) {
				String term = args.getStringOption("meanings", null);
				
				TsvDumper dumper = new TsvDumper();
				DataSet<LocalConcept> meanings = store.getMeanings(term);
				dumper.dumpLocalConcepts(meanings, new PrintStreamOutput(System.out), true);
			}
			/*else if (args.isSet("termsFor")) {
				String concept = args.getStringOption("termsFor", null);
				TsvDumper dumper = new TsvDumper(store);
				dumper.dumpTermsForConcept(concept, new PrintStreamOutput(System.out));
			}
			else if (args.isSet("meaningsOf")) {
				String term = args.getStringOption("meaningsOf", null);
				TsvDumper dumper = new TsvDumper(store);
				dumper.dumpConceptsForTerm(term, new PrintStreamOutput(System.out));
			}
			else if (args.isSet("rdfTermsRefersTo") || args.isSet("meaning")) {
				RdfSink rdf = new N3Sink(System.out);
				RdfDumper dumper = new RdfDumper(store, rdf, true, false);
				dumper.dumpRelationTermRefersTo(store.queryTermRefersTo());
			}
			else if (args.isSet("rdfConceptBroader") || args.isSet("broader")) {
				RdfSink rdf = new N3Sink(System.out);
				RdfDumper dumper = new RdfDumper(store, rdf, true, false);
				dumper.dumpRelationConceptBroader(store.queryConceptBroader());
			}
			else if (args.isSet("rdfConcepts") || args.isSet("concepts")) {
				RdfSink rdf = new N3Sink(System.out);
				RdfDumper dumper = new RdfDumper(store, rdf, true, false);
				dumper.dumpConcepts(store.queryConcepts());
			}
			else if (args.isSet("rdfResources") || args.isSet("resources")) {
				RdfSink rdf = new N3Sink(System.out);
				RdfDumper dumper = new RdfDumper(store, rdf, true, false);
				dumper.dumpResources(store.queryResources());
			}*/
			else {
				out.println("NOTHING TO DO.");
			}
		}
		finally {
			store.close();
		}
	}
	
}
