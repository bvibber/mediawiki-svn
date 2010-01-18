package de.brightbyte.wikiword.store;

import static de.brightbyte.db.DatabaseUtil.asDouble;
import static de.brightbyte.db.DatabaseUtil.asInt;
import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.sql.DataSource;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.ConceptDescription;
import de.brightbyte.wikiword.model.ConceptRelations;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.model.GlobalConceptReference;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.LocalConceptReference;
import de.brightbyte.wikiword.model.TranslationReference;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.GlobalConceptStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;

/**
 * A GlobalConceptStore implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used by 
 * {@link de.brightbyte.wikiword.store.DatabaseGlobalConceptStore}, see there.
 */
public class DatabaseGlobalConceptStore extends DatabaseWikiWordConceptStore<GlobalConcept, GlobalConceptReference> 
	implements GlobalConceptStore {
	
	public static final String DEFAULT_DATASET = "thesaurus";
	
	protected Map<String, DatabaseLocalConceptStore> localStores = new HashMap<String, DatabaseLocalConceptStore>();
	
	protected Inserter mergeInserter;
	
	protected RelationTable originTable;
	protected RelationTable relationTable;	 
	protected RelationTable mergeTable;

	//protected TweakSet tweaks;
	
	protected int idOffsetGranularity;
	private Corpus[] languages;

	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseGlobalConceptStore(DatasetIdentifier set, DataSource dbInfo, TweakSet tweaks) throws SQLException {
		this(new GlobalConceptStoreSchema(set, dbInfo, tweaks, false), tweaks);
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
	public DatabaseGlobalConceptStore(DatasetIdentifier set, Connection db, TweakSet tweaks) throws SQLException {
		this(new GlobalConceptStoreSchema(set, db, tweaks, false), tweaks);
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
	public DatabaseGlobalConceptStore(GlobalConceptStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);
		
		this.tweaks = tweaks;
		
		this.idOffsetGranularity = tweaks.getTweak("dbstore.idOffsetGranularity", 1000000); //start id "blocks" at million boundaries

		originTable = (RelationTable)database.getTable("origin");
		relationTable = (RelationTable)database.getTable("relation");
		mergeTable = (RelationTable)database.getTable("merge");
	}
	
	@Override
	protected GlobalConceptReference newReference(int id, String name, int card, double relevance) {
		return new GlobalConceptReference(id, name, card, relevance);
	}
	
	@Override
	protected GlobalConceptReference[] newReferenceArray(int n) {
		return new GlobalConceptReference[n];
	}

	protected String meaningsSQL(String lang, String term) throws PersistenceException {
		DatabaseLocalConceptStore db = getLocalConceptStore(lang);
		
		String sql = " JOIN "+originTable.getSQLName()+" as L ON C.id = L.global_concept AND L.lang = " +database.quoteString(lang) + " " +
				" JOIN "+db.meaningTable.getSQLName()+" as M ON M.concept = L.local_concept " +
				" WHERE M.term_text = "+database.quoteString(term)+" " +
				" ORDER BY freq DESC";

		return sql;
	}

	public DataSet<GlobalConceptReference> listMeanings(String lang, String term)
		throws PersistenceException { 
		
		String sql = referenceSelect("M.freq") + meaningsSQL(lang, term);
		return new QueryDataSet<GlobalConceptReference>(database, getRowReferenceFactory(), "listMeanings", sql, false);
	}
	
	protected void registerLocalStore(DatabaseLocalConceptStore store) throws PersistenceException {
		trace("registered local store for "+store.getCorpus().getLanguage());
		
		try {
			Corpus corpus = store.getCorpus();
			String lang = corpus.getLanguage();
			if (((GlobalConceptStoreSchema)database).getLanguageBit(lang)==0) {
				throw new IllegalArgumentException("language "+lang+" is not supported by schema!");
			}
			
			localStores.put(lang, store);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public DatabaseLocalConceptStore getLocalConceptStore(String lang) throws PersistenceException {
		DatabaseLocalConceptStore store = localStores.get(lang);
		if (store!=null) return store;
		
		Corpus corpus = Corpus.forName(getDatasetIdentifier().getCollection(), lang, tweaks);
		return getLocalConceptStore(corpus);
	}
	
	public DatabaseLocalConceptStore getLocalConceptStore(Corpus corpus) throws PersistenceException {
		String lang = corpus.getLanguage();
		DatabaseLocalConceptStore store = localStores.get(lang);
		
		if (store==null) {
			try {
				int bit = ((GlobalConceptStoreSchema)database).getLanguageBit(lang);
				if (bit==0) throw new IllegalArgumentException("unknown language code: "+lang);
				
				try {
					store = new DatabaseLocalConceptStore(corpus, database.getConnection(), tweaks);
					registerLocalStore(store);
				} catch (SQLException e) {
					throw new PersistenceException(e);
				}
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		return store;
	}
	
	//-------------------------------
	public Corpus[] detectLanguages() throws PersistenceException {
		try {
			return ((GlobalConceptStoreSchema)database).getLanguages();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void setLanguages(Corpus[] languages) {
		if (languages==null) throw new NullPointerException();
		if (this.languages!=null) throw new IllegalStateException("languages already set");
		//XXX: set languages in GlobalConceptStoreSchema too?
		
		this.languages = languages;
	}
	
	public Corpus[] getLanguages() throws PersistenceException {
		if (languages==null) languages = detectLanguages();
		return languages;
	}
	
	//---------------------------------
	
	public ConceptType getConceptType(int type) throws PersistenceException {
		try {
			return database.getConceptType(type);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public DataSet<GlobalConcept> getMeanings(String lang, String term) throws PersistenceException {
		return ((DatabaseGlobalConceptInfoStore)getConceptInfoStore()).getMeanings(lang, term);
	}

	public ConceptDescription getConceptDescription(int id, Corpus lang) throws PersistenceException {
		return ((DatabaseGlobalConceptInfoStore)getConceptInfoStore()).getConceptDescription(id, lang);
	}

	@Deprecated
	public Map<String, ConceptDescription> getConceptDescriptions(int id) throws PersistenceException {
		return ((DatabaseGlobalConceptInfoStore)getConceptInfoStore()).getConceptDescriptions(id);
	}
	
	public List<LocalConcept> getLocalConcepts(int id) throws PersistenceException {
		return ((DatabaseGlobalConceptInfoStore)getConceptInfoStore()).getLocalConcepts(id);
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	protected class DatabaseGlobalStatisticsStore extends DatabaseStatisticsStore {

		protected DatabaseGlobalStatisticsStore(StatisticsStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
		}
		
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	
	protected class DatabaseGlobalConceptInfoStore extends DatabaseConceptInfoStore<GlobalConcept> {
		
		protected DatabaseGlobalConceptInfoStore(ConceptInfoStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
		}
		
		@Override
		protected String conceptSelect(String card, String relev, boolean useDistrib) {
			String distribJoin = !useDistrib ? "" : " JOIN " + database.getSQLTableName("degree", true) + " as DT ON DT.concept = C.id";

			return "SELECT C.id as cId, C.name as cName, " +
					"C.language_bits as cLangBits, C.language_count as cLangCount, " +
					"C.type as cType, " +
			" "+card+" as qFreq, "+relev+" as qConf, " +
			" I.inlinks as rInlinks, I.outlinks as rOutlinks, " +
			" I.broader as rBroader, I.narrower as rNarrower, " +
			" I.similar as rSimilar, I.related as rRelated, I.langlinks as rLanglinks " +
			" FROM "+conceptTable.getSQLName()+" as C "+
			distribJoin+
			" LEFT JOIN "+conceptInfoTable.getSQLName()+" as I ON I.concept = C.id ";
		}
		
		@Override
		protected GlobalConcept newConcept(Map<String, Object> m) throws PersistenceException {
			try {
				int id = asInt(m.get("cId"));
				int langBits = asInt(m.get("cLangBits"));
				//int langCount = asInt(m.get("cLangCount"));
				String name = asString(m.get("cName"));
				ConceptType type = getConceptType(asInt(m.get("cType")));
				
				int cardinality = m.get("qFreq") != null ? asInt(m.get("qFreq")) : -1;
				double relevance = m.get("qConf") != null ? asDouble(m.get("qConf")) : -1;
				
				Corpus[] languages = ((GlobalConceptStoreSchema)DatabaseGlobalConceptStore.this.database).getLanguages(langBits);
				
				GlobalConceptReference ref = new GlobalConceptReference(id, name, cardinality, relevance);
				GlobalConceptReference[] inlinks = GlobalConceptReference.parseList( asString(m.get("rInlinks")), ((ConceptInfoStoreSchema)database).inLinksReferenceListEntry ); 
				GlobalConceptReference[] outlinks = GlobalConceptReference.parseList( asString(m.get("rOutlinks")), ((ConceptInfoStoreSchema)database).outLinksReferenceListEntry ); 
				GlobalConceptReference[] broader = GlobalConceptReference.parseList( asString(m.get("rBroader")), ((ConceptInfoStoreSchema)database).broaderReferenceListEntry ); 
				GlobalConceptReference[] narrower = GlobalConceptReference.parseList( asString(m.get("rNarrower")), ((ConceptInfoStoreSchema)database).narrowerReferenceListEntry ); 
				TranslationReference[] langlinks = TranslationReference.parseList( asString(m.get("rLanglinks")), ((ConceptInfoStoreSchema)database).langlinkReferenceListEntry ); 
				GlobalConceptReference[] similar = GlobalConceptReference.parseList( asString(m.get("rSimilar")), ((ConceptInfoStoreSchema)database).similarReferenceListEntry ); 
				GlobalConceptReference[] related = GlobalConceptReference.parseList( asString(m.get("rRelated")), ((ConceptInfoStoreSchema)database).relatedReferenceListEntry ); 
				
				ConceptRelations<GlobalConceptReference> relations = new ConceptRelations<GlobalConceptReference>(broader, narrower, inlinks, outlinks, similar, related, langlinks);
				
				return new GlobalConcept(ref, getDatasetIdentifier(), languages, type, DatabaseGlobalConceptStore.this, relations, null);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public ConceptDescription getConceptDescription(int id, Corpus corpus) throws PersistenceException {
			LocalConceptStore store = getLocalConceptStore(corpus);
			return getConceptDescription(id, store);
		}
		
		@Deprecated
		protected ConceptDescription getConceptDescription(int id, LocalConceptStore store) throws PersistenceException {
			return getLocalConcept(id, store).getDescription();
		}
		
		@Deprecated
		public Map<String, ConceptDescription> getConceptDescriptions(int id) throws PersistenceException {
			List<LocalConcept> cc = getLocalConcepts(id);
			Map<String, ConceptDescription> m = new HashMap<String, ConceptDescription>(cc.size());
			for (LocalConcept c: cc) {
				m.put(c.getCorpus().getLanguage(), c.getDescription());
			}
			
			return m;
		}

		protected LocalConcept getLocalConcept(int id, LocalConceptStore store) throws PersistenceException {
			String lang = store.getCorpus().getLanguage();
			String sql = "select local_concept" 
					+" from "+originTable.getSQLName()
					+" where global_concept = "+id
					+" and lang = "+database.quoteString(lang);
			
			try {
				Integer localId = asInt(database.executeSingleValueQuery("getConceptDescription", sql));
				if (localId==null) throw new PersistenceException("concept #"+id+" has no description in language "+lang);
				
				return store.getConceptInfoStore().getConcept(localId);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public List<LocalConcept> getLocalConcepts(int id) throws PersistenceException {
			List<LocalConcept> m = new ArrayList<LocalConcept>();

			String sql = "select lang, local_concept" 
				+" from "+originTable.getSQLName()
				+" where global_concept = "+id;
			
			try {
				ResultSet res = executeQuery("getLocalConcepts", sql);
				while (res.next()) {
					String lang = asString(res.getObject("lang"));
					LocalConceptStore store = getLocalConceptStore(lang);
					LocalConcept c = getLocalConcept(id, store);
					m.add(c);
				}
				
				res.close();
				return m;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public DataSet<GlobalConcept> getMeanings(String lang, String term)
			throws PersistenceException {
		
			String sql = conceptSelect("M.freq") + meaningsSQL(lang, term);
			return new QueryDataSet<GlobalConcept>(database, new ConceptFactory(), "getMeanings", sql, false);
		}
	}

	//////////////////////////////////////////////////////////////////////////////
	
	@Override
	protected DatabaseConceptInfoStore<GlobalConcept> newConceptInfoStore() throws SQLException {
		ConceptInfoStoreSchema schema = new ConceptInfoStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), true, tweaks, false, false);
		return new DatabaseGlobalConceptInfoStore(schema, tweaks);
	}

	@Override
	protected DatabaseStatisticsStore newStatisticsStore() throws SQLException {
		StatisticsStoreSchema schema = new StatisticsStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), true, tweaks, false); 
		return new DatabaseGlobalStatisticsStore(schema, tweaks);
	}
	
}
