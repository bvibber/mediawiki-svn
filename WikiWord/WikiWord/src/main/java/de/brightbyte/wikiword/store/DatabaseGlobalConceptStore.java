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
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.ConceptRelations;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.model.LocalConcept;
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
public class DatabaseGlobalConceptStore extends DatabaseWikiWordConceptStore<GlobalConcept> 
	implements GlobalConceptStore {
	
	public static final String DEFAULT_DATASET = "thesaurus";
	
	protected Map<String, DatabaseLocalConceptStore> localStores = new HashMap<String, DatabaseLocalConceptStore>();
	
	protected RelationTable originTable;
	protected RelationTable relationTable;	 
	protected RelationTable mergeTable;
	protected RelationTable meaningTable;

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
		meaningTable = (RelationTable)database.getTable("meaning");
	}
	
	@Override
	protected GlobalConcept newConcept(int id, String name, ConceptType type, int card, double relevance) {
		GlobalConcept concept = new GlobalConcept(getDatasetIdentifier(), id, type);
		concept.setName(name);
		concept.setCardinality(card);
		concept.setRelevance(relevance);
		return concept;
	}
	
	@Override
	protected GlobalConcept[] newConceptArray(int n) {
		return new GlobalConcept[n];
	}

	protected String meaningsSQL(String lang, String term, ConceptQuerySpec spec) throws PersistenceException {
		/*
		 DatabaseLocalConceptStore db = getLocalConceptStore(lang);
		
		String sql = " JOIN "+originTable.getSQLName()+" as L ON C.id = L.global_concept AND L.lang = " +database.quoteString(lang) + " " +
				" JOIN "+db.meaningTable.getSQLName()+" as M ON M.concept = L.local_concept " +
				" WHERE M.term_text = "+database.quoteString(term)+" " +
				" ORDER BY freq DESC";

		return sql;
		*/
		
		if ( lang == null && spec!=null && spec.getLanguage()!=null) {
			lang = spec.getLanguage();
		}
			
		try {
			String sql = " JOIN "+meaningTable.getSQLName()+" as M on C.id = M.concept " +
									" WHERE M.term_text = "+database.quoteString(term)+" ";
			
			if (spec!=null && spec.getRequireTypes()!=null 
									&& !spec.getRequireTypes().isEmpty())  { 
				sql += " AND C.type IN "+getTypeCodeSet(spec.getRequireTypes())+" ";
			}

			if ( lang != null ) {
				if ( spec!=null && spec.getLanguageIndependantTypes()!=null 
									&& !spec.getLanguageIndependantTypes().isEmpty() ) {
					//some types of concepts (proper nouns) may be language independant
					sql += " AND ( M.lang = "+database.quoteString(lang)+" OR C.type IN "+getTypeCodeSet(spec.getLanguageIndependantTypes())+" )";
				} else {
					sql += " AND M.lang = "+database.quoteString(lang)+" ";
				}
			}
			
			sql +=	" ORDER BY freq DESC";
			
			if (spec!=null && spec.getLimit()>0) sql += " LIMIT "+spec.getLimit();
			return sql;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected String conceptSQL(String lang, String name, ConceptQuerySpec spec) throws PersistenceException {
		if ( lang == null && spec!=null && spec.getLanguage()!=null) {
			lang = spec.getLanguage();
		}
		
		if ( lang==null ) throw new IllegalArgumentException("languager must be given, either explicitly or in the QuerySpec");
			
		String sql = " JOIN "+originTable.getSQLName()+" as O on C.id = O.global_concept " +
								" WHERE O.local_concept_name = "+database.quoteString(name)+" " +
								" AND O.lang = "+database.quoteString(lang)+" ";
		
		return sql;
	}

	/*
	public DataSet<GlobalConcept> listMeanings(String lang, String term)
		throws PersistenceException { 
		
		String sql = conceptSelect("M.freq") + meaningsSQL(lang, term);
		return new QueryDataSet<GlobalConcept>(database, getRowConceptFactory(), "listMeanings", sql, false);
	}
	*/
	
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
	
	public GlobalConcept getConceptByName(String lang, String name, ConceptQuerySpec spec) throws PersistenceException {
		String sql = conceptSelect(spec, null, null) + conceptSQL(lang, name, spec);
		try {
			ResultSet rs = database.executeQuery("getConceptByName", sql);
			if (!rs.next()) throw new PersistenceException("no concept found with name '"+name+"' in language '"+lang+"'");
			
			GlobalConcept concept = newConcept(rs, spec);
			
			rs.close();
			return concept;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public DataSet<GlobalConcept> getMeanings(String lang, String term, ConceptQuerySpec spec) throws PersistenceException {
		return ((DatabaseGlobalConceptInfoStore)getConceptInfoStore()).getMeanings(lang, term, spec);
	}

	public DataSet<GlobalConcept> getMeanings(String term, ConceptQuerySpec spec) throws PersistenceException {
		return ((DatabaseGlobalConceptInfoStore)getConceptInfoStore()).getMeanings(term, spec);
	}

	public List<LocalConcept> getLocalConcepts(int id, ConceptQuerySpec spec) throws PersistenceException {
		return ((DatabaseGlobalConceptInfoStore)getConceptInfoStore()).getLocalConcepts(id, spec);
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	protected class DatabaseGlobalStatisticsStore extends DatabaseStatisticsStore {

		protected DatabaseGlobalStatisticsStore(StatisticsStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
		}
		
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	protected PropertyStore<GlobalConcept> newPropertyStore() throws SQLException, PersistenceException {
		throw new UnsupportedOperationException("property stores are not yet supported for a global thesaurus.");
	}
	
	protected class DatabaseGlobalConceptInfoStore extends DatabaseConceptInfoStore<GlobalConcept> {
		
		protected DatabaseGlobalConceptInfoStore(ConceptInfoStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
		}
		
		@Override
		protected String conceptSelect(ConceptQuerySpec spec, String card, String relev) {
			boolean useDistrib = (relev!=null || (spec!=null && spec.getIncludeStatistics())) && areStatsComplete();
			
			String fields = "C.id as cId, C.name as cName, C.type as cType, " +
							"C.language_bits as cLangBits, C.language_count as cLangCount ";
			
			String tables = ""+conceptTable.getSQLName()+" as C ";
			
			if (useDistrib) {
				tables += " JOIN " + database.getSQLTableName("degree", true) + " as DT ON DT.concept = C.id ";
				if (relev==null) relev = "DT.idf";
			}  else {
				relev = "-1";
			}
			
			if (relev==null) relev = "-1";
			if (card==null) card = "-1";
			
			fields += ", "+card+" as qFreq, "+relev+" as qConf ";
			
			if (spec!=null && spec.getIncludeRelations()) {
				fields += ", I.inlinks as rInlinks, I.outlinks as rOutlinks, " +
								" I.broader as rBroader, I.narrower as rNarrower, I.langlinks as rLanglinks, " +
								" I.similar as rSimilar, I.related as rRelated ";
				
				tables += " LEFT JOIN "+conceptInfoTable.getSQLName()+" as I ON I.concept = C.id ";
			}
			
			//TODO: include features!
		
			String sql =  "SELECT " + fields + " FROM " + tables;
			return sql;
		}
		
		@Override
		protected GlobalConcept newConcept(Map<String, Object> m, ConceptQuerySpec spec) throws PersistenceException {
			try {
				int id = asInt(m.get("cId"));
				int langBits = asInt(m.get("cLangBits"));
				//int langCount = asInt(m.get("cLangCount"));
				String name = asString(m.get("cName"));
				ConceptType type = getConceptType(asInt(m.get("cType")));
				
				int cardinality = m.get("qFreq") != null ? asInt(m.get("qFreq")) : -1;
				double relevance = m.get("qConf") != null ? asDouble(m.get("qConf")) : -1;
				
				Corpus[] languages = ((GlobalConceptStoreSchema)DatabaseGlobalConceptStore.this.database).getLanguages(langBits);
				
				GlobalConcept concept = new GlobalConcept(getDatasetIdentifier(), id, type);
				concept.setName(name);
				concept.setLanguages(languages);
				concept.setCardinality(cardinality);
				concept.setRelevance(relevance);
				
				if (spec!=null && spec.getIncludeRelations()) {
					GlobalConcept[] inlinks = GlobalConcept.parseList( asString(m.get("rInlinks")), getConceptFactory(), ((ConceptInfoStoreSchema)database).inLinksReferenceListEntry ); 
					GlobalConcept[] outlinks = GlobalConcept.parseList( asString(m.get("rOutlinks")), getConceptFactory(), ((ConceptInfoStoreSchema)database).outLinksReferenceListEntry ); 
					GlobalConcept[] broader = GlobalConcept.parseList( asString(m.get("rBroader")), getConceptFactory(), ((ConceptInfoStoreSchema)database).broaderReferenceListEntry ); 
					GlobalConcept[] narrower = GlobalConcept.parseList( asString(m.get("rNarrower")), getConceptFactory(), ((ConceptInfoStoreSchema)database).narrowerReferenceListEntry ); 
					GlobalConcept[] langlinks = GlobalConcept.parseList( asString(m.get("rLanglinks")), getConceptFactory(), ((ConceptInfoStoreSchema)database).langlinkReferenceListEntry ); 
					GlobalConcept[] similar = GlobalConcept.parseList( asString(m.get("rSimilar")), getConceptFactory(), ((ConceptInfoStoreSchema)database).similarReferenceListEntry ); 
					GlobalConcept[] related = GlobalConcept.parseList( asString(m.get("rRelated")), getConceptFactory(), ((ConceptInfoStoreSchema)database).relatedReferenceListEntry ); 

					ConceptRelations<GlobalConcept> relations = new ConceptRelations<GlobalConcept>(broader, narrower, inlinks, outlinks, similar, related, langlinks);
					concept.setRelations(relations);
				}
				
				return concept;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		protected LocalConcept getLocalConcept(int id, ConceptQuerySpec spec, DatabaseLocalConceptStore store) throws PersistenceException {
			String lang = store.getCorpus().getLanguage();
			String sql = "select local_concept" 
					+" from "+originTable.getSQLName()
					+" where global_concept = "+id
					+" and lang = "+database.quoteString(lang);
			
			try {
				Integer localId = asInt(database.executeSingleValueQuery("getConceptDescription", sql));
				if (localId==null) throw new PersistenceException("concept #"+id+" has no description in language "+lang);
				
				return store.getConceptInfoStore().getConcept(localId, spec);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public List<LocalConcept> getLocalConcepts(int id, ConceptQuerySpec spec) throws PersistenceException {
			List<LocalConcept> m = new ArrayList<LocalConcept>();

			String sql = "select lang, local_concept" 
				+" from "+originTable.getSQLName()
				+" where global_concept = "+id;
			
			try {
				ResultSet res = executeQuery("getLocalConcepts", sql);
				while (res.next()) {
					String lang = asString(res.getObject("lang"));
					DatabaseLocalConceptStore store = getLocalConceptStore(lang);
					LocalConcept c = getLocalConcept(id, spec, store);
					m.add(c);
				}
				
				res.close();
				return m;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
		public DataSet<GlobalConcept> getMeanings(String lang, String term, ConceptQuerySpec spec)
			throws PersistenceException {
		
			String sql = conceptSelect(spec, "M.freq") + meaningsSQL(lang, term, spec);
			return new QueryDataSet<GlobalConcept>(database, new ConceptFactory(spec), "getMeanings", sql, false);
		}
		
		public DataSet<GlobalConcept> getMeanings(String term, ConceptQuerySpec spec)
			throws PersistenceException {
		
			String sql = conceptSelect(spec, "M.freq") + meaningsSQL(null, term, spec);
			return new QueryDataSet<GlobalConcept>(database, new ConceptFactory(spec), "getMeanings", sql, false);
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
