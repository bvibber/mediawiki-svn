package de.brightbyte.wikiword.store;

import static de.brightbyte.db.DatabaseUtil.asDouble;
import static de.brightbyte.db.DatabaseUtil.asInt;
import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Collection;
import java.util.Map;

import javax.sql.DataSource;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.ChunkedQueryDataSet;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.QueryDataSet;
import de.brightbyte.db.RelationTable;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.ConceptRelations;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermMeaning;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordResource;
import de.brightbyte.wikiword.schema.ConceptInfoStoreSchema;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.LocalStatisticsStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;

/**
 * A LocalConceptStore implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used by 
 * {@link de.brightbyte.wikiword.store.DatabaseLocalConceptStore}, see there.
 */
public class DatabaseLocalConceptStore extends DatabaseWikiWordConceptStore<LocalConcept> 
	implements LocalConceptStore {
	
	protected Corpus corpus;

	protected EntityTable resourceTable;
	protected RelationTable aboutTable;
	
	protected EntityTable definitionTable;
	protected EntityTable sectionTable;
	
	protected RelationTable linkTable;
	protected RelationTable aliasTable;
	protected RelationTable meaningTable;
	
	//protected EntityTable conceptDescriptionTable;

	//protected TweakSet tweaks;
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 */
	public DatabaseLocalConceptStore(Corpus corpus, DataSource dbInfo, TweakSet tweaks) throws SQLException {
		this(new LocalConceptStoreSchema(corpus, dbInfo, tweaks, false), tweaks);
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
	public DatabaseLocalConceptStore(Corpus corpus, Connection db, TweakSet tweaks) throws SQLException {
		this(new LocalConceptStoreSchema(corpus, db, tweaks, false), tweaks);
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
	public DatabaseLocalConceptStore(LocalConceptStoreSchema database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);
		
		this.corpus = database.getCorpus();
		this.tweaks = tweaks;
		
		resourceTable = (EntityTable)database.getTable("resource"); 
		aboutTable = (RelationTable)database.getTable("about"); 
		
		definitionTable = (EntityTable)database.getTable("definition");
		sectionTable = (EntityTable)database.getTable("section");
		
		linkTable = (RelationTable)database.getTable("link");
		aliasTable = (RelationTable)database.getTable("alias");
		
		meaningTable = (RelationTable)database.getTable("meaning");
		/*
		Inserter conceptDescriptionInserter = configureTable("concept_description", 64, 1024);
		conceptDescriptionTable = (EntityTable)conceptDescriptionInserter.getTable();
		*/
	}
		
	protected String meaningWhere(String term, ConceptQuerySpec spec) throws PersistenceException {
		if (spec!=null && spec.getLanguage()!=null && !spec.getLanguage().equals(getCorpus().getLanguage())) {
			throw new IllegalArgumentException("incompatible language requirement: local thesaurus for "+getCorpus().getLanguage()+" can not search for terms in "+spec.getLanguage());
		}
		
		try {
			String sql = " JOIN "+meaningTable.getSQLName()+" as M ON C.id = M.concept ";
			sql += " WHERE M.term_text = "+database.quoteString(term)+" ";
			
			if (spec!=null && spec.getRequireTypes()!=null && !spec.getRequireTypes().isEmpty())  { 
				sql += " AND C.type IN "+getTypeCodeSet(spec.getRequireTypes())+" ";
			}
			
			sql += " ORDER BY freq DESC";
			
			if (spec!=null && spec.getLimit()>0) sql += " LIMIT "+spec.getLimit();
			return sql;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	@Override
	protected LocalConcept newConcept(int id, String name, ConceptType type, int card, double relevance) {
		LocalConcept concept = new LocalConcept(getCorpus(), id, type, name); 
		concept.setCardinality(card);
		concept.setRelevance(relevance);
		return concept;
	}
	
	@Override
	protected LocalConcept[] newConceptArray(int n) {
		return new LocalConcept[n];
	}
	
	public ConceptType getConceptType(int type) {
		return corpus.getConceptTypes().getType(type);
	}

	public Corpus getCorpus() {
		return corpus;
	}
	
	/*
	public DataSet<LocalConcept> listMeanings(String term, ConceptType t)
		throws PersistenceException { 
			
		String sql = conceptSelect("M.freq") + meaningWhere(term, t);
		
		return new QueryDataSet<LocalConcept>(database, getRowConceptFactory(), "listMeanings", sql, false);
	}
    */
	
	public LocalConcept getConceptByName(String name, ConceptQuerySpec spec) throws PersistenceException {
		return ((DatabaseLocalConceptInfoStore)getConceptInfoStore()).getConceptByName(name, spec);
	}

	public DataSet<LocalConcept> getMeanings(String term, ConceptQuerySpec spec) throws PersistenceException {
		return ((DatabaseLocalConceptInfoStore)getConceptInfoStore()).getMeanings(term, spec);
	}
		
	public TermReference pickRandomTerm(int top) throws PersistenceException {
		return ((LocalStatisticsStore<LocalConcept>)getStatisticsStore()).pickRandomTerm(top);
	}
	
	public DataSet<TermMeaning> getAllTerms() throws PersistenceException {
		return ((LocalStatisticsStore<LocalConcept>)getStatisticsStore()).getAllTerms();
	}

	public int getNumberOfTerms() throws PersistenceException {
		return ((LocalStatisticsStore<LocalConcept>)getStatisticsStore()).getNumberOfTerms();
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////

	protected final DatabaseDataSet.Factory<TermMeaning> termFactory = new DatabaseDataSet.Factory<TermMeaning>() {
		public TermMeaning newInstance(ResultSet row) throws SQLException, PersistenceException {
			return newTerm(row);
		}
	};
	
	protected TermMeaning newTerm(ResultSet row) throws SQLException, PersistenceException {
		int id = row.getInt("id");
		String name = asString(row.getObject("name"));
		int card = row.getInt("cardinality");
		double relevance = row.getInt("relevance");
		
		LocalConcept concept = newConcept(id, name, null, card, relevance);
		return new TermMeaning(name, concept, relevance);
	}

	protected class DatabaseLocalStatisticsStore extends DatabaseStatisticsStore implements LocalStatisticsStore<LocalConcept> {
		protected EntityTable termTable;
		protected int numberOfTerms = -1;

		protected DatabaseLocalStatisticsStore(StatisticsStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			termTable =   (EntityTable)database.getTable("term"); 
		}

		public DataSet<TermMeaning> getAllTerms() throws PersistenceException {
			try {
				String sql = "SELECT rank as id, term, freq as cardinality, -1 as relevance FROM "+termTable.getSQLName()+" as T";
				return new ChunkedQueryDataSet<TermMeaning>(database, termFactory, "getAllTerms", "query", sql, null, null, termTable, "rank", queryChunkSize);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	
		public int getNumberOfTerms() throws PersistenceException {
			if (numberOfTerms<0) {
				String sql = "select count(*) from "+termTable.getSQLName();
				try {
					numberOfTerms = asInt(database.executeSingleValueQuery("getNumberOfTerms", sql));
				} catch (SQLException e) {
					throw new PersistenceException(e);
				}
			}
			
			return numberOfTerms;
		}
		
		public TermReference pickRandomTerm(int top)
			throws PersistenceException {
			
			if (top==0) top = getNumberOfTerms(); //if 0, use all
			if (top<0) top = getNumberOfTerms() * top / -100; //if negative, interpret as percent
			
			int r = (int)Math.floor(Math.random() * top) +1;
				
			String sql = "SELECT rank as id, term, freq as cardinality, -1 as relevance " +
						" FROM "+database.getSQLTableName("term")+" as T" +
						" WHERE rank = "+r;
			
			try {
				TermReference pick = null;
				ResultSet rs = executeQuery("pickRandomTerm", sql);
				if (rs.next()) pick = newTerm(rs); 
				rs.close();
				return pick;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
	}

	/////////////////////////////////////////////////////////////////////////////////////////////
	
	protected class DatabaseLocalConceptInfoStore extends DatabaseConceptInfoStore<LocalConcept> {
		
		protected EntityTable conceptDescriptionTable;
		
		protected DatabaseLocalConceptInfoStore(ConceptInfoStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			
			conceptDescriptionTable = (EntityTable)database.getTable("concept_description");

		}
	
		@Override
		protected String conceptSelect(ConceptQuerySpec spec, String card, String relev) {
			boolean useDistrib = (relev!=null || (spec!=null && spec.getIncludeStatistics())) && areStatsComplete();
			
			String fields = "C.id as cId, C.name as cName, C.type as cType";
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
			
			if (spec!=null && spec.getIncludeResource()) {
				fields += ", R.id as rcId, R.name as rcName, R.type as rcType ";
				tables += " LEFT JOIN "+aboutTable.getSQLName()+" as A ON A.concept = C.id "+
								" LEFT JOIN "+resourceTable.getSQLName()+" as R ON R.id = A.resource ";
			}
			
			if (spec!=null && spec.getIncludeDefinition()) {
				fields += ", F.definition as fDefinition ";
				
				tables += " LEFT JOIN "+definitionTable.getSQLName()+" as F ON F.concept = C.id ";
			}
			
			if (spec!=null && spec.getIncludeRelations()) {
				fields += ", I.inlinks as rInlinks, I.outlinks as rOutlinks, " +
								" I.broader as rBroader, I.narrower as rNarrower, I.langlinks as rLanglinks, " +
								" I.similar as rSimilar, I.related as rRelated ";
				
				tables += " LEFT JOIN "+conceptInfoTable.getSQLName()+" as I ON I.concept = C.id ";
			}
			
			if (spec!=null && spec.getIncludeTerms()) {
				fields += ", D.terms as dTerms";
				
				tables += " LEFT JOIN "+conceptDescriptionTable.getSQLName()+" as D ON D.concept = C.id ";
			}
			
			//TODO: include features!
			
			String sql =  "SELECT " + fields + " FROM " + tables;
			return sql;
		}
			
		@Override
		protected LocalConcept newConcept(Map<String, Object> m, ConceptQuerySpec spec) throws PersistenceException {
			int id = asInt(m.get("cId"));
			String name = asString(m.get("cName"));
			ConceptType type = corpus.getConceptTypes().getType(asInt(m.get("cType")));
			
			int cardinality = m.get("qFreq") != null ? asInt(m.get("qFreq")) : -1;
			double relevance = m.get("qConf") != null ? asDouble(m.get("qConf")) : -1;
			
			int rcId = m.get("rcId") != null ? asInt(m.get("rcId")) : 0;
			String rcName = asString(m.get("rcName"));
			ResourceType rcType = m.get("rcType") != null ? ResourceType.getType(asInt(m.get("rcType"))) : null;

			LocalConcept concept = new LocalConcept(getCorpus(), id, null, name);
			concept.setCardinality(cardinality);
			concept.setRelevance(relevance);
			concept.setType(type);
			
			if (spec!=null && spec.getIncludeRelations()) {
				LocalConcept[] inlinks = LocalConcept.parseList( asString(m.get("rInlinks")), getConceptFactory(), ((ConceptInfoStoreSchema)database).inLinksReferenceListEntry ); 
				LocalConcept[] outlinks = LocalConcept.parseList( asString(m.get("rOutlinks")), getConceptFactory(), ((ConceptInfoStoreSchema)database).outLinksReferenceListEntry ); 
				LocalConcept[] broader = LocalConcept.parseList( asString(m.get("rBroader")), getConceptFactory(), ((ConceptInfoStoreSchema)database).broaderReferenceListEntry ); 
				LocalConcept[] narrower = LocalConcept.parseList( asString(m.get("rNarrower")), getConceptFactory(), ((ConceptInfoStoreSchema)database).narrowerReferenceListEntry ); 
				LocalConcept[] langlinks = LocalConcept.parseList( asString(m.get("rLanglinks")), getConceptFactory(), ((ConceptInfoStoreSchema)database).langlinkReferenceListEntry ); 
				LocalConcept[] similar = LocalConcept.parseList( asString(m.get("rSimilar")), getConceptFactory(), ((ConceptInfoStoreSchema)database).similarReferenceListEntry ); 
				LocalConcept[] related = LocalConcept.parseList( asString(m.get("rRelated")), getConceptFactory(), ((ConceptInfoStoreSchema)database).relatedReferenceListEntry ); 
				
				ConceptRelations<LocalConcept> relations = new ConceptRelations<LocalConcept>(broader, narrower, inlinks, outlinks, similar, related, langlinks);
				concept.setRelations(relations);
			}
			
			if (spec!=null && spec.getIncludeResource()) {
				WikiWordResource resource = rcId <= 0 ? null : new WikiWordResource(corpus, rcId, rcName, rcType);
				concept.setResource(resource);
			}
			
			if (spec!=null && spec.getIncludeDefinition()) {
				String definition = asString(m.get("fDefinition"));
				concept.setDefinition(definition);
			}
			
			if (spec!=null && spec.getIncludeTerms()) {
				TermReference[] terms = TermMeaning.parseList( asString(m.get("dTerms")), getConceptFactory(), ((ConceptInfoStoreSchema)database).termReferenceListEntry );
				concept.setTerms(terms);
			}
			
			return concept;
		}
		

		public DataSet<LocalConcept> getMeanings(String term, ConceptQuerySpec spec) 
			throws PersistenceException {
			String sql = conceptSelect(spec, "M.freq") + meaningWhere(term, spec);
			
			return new QueryDataSet<LocalConcept>(database, new ConceptFactory(spec), "getMeanins", sql, false);
		}
		

		public LocalConcept getConceptByName(String name, ConceptQuerySpec spec) throws PersistenceException {
			
			String sql = conceptSelect(spec, "-1") + " WHERE C.name = "+database.quoteString(name);
			
			try {
				ResultSet row = executeQuery("getConcept", sql);
				if (!row.next()) throw new PersistenceException("no concept found with name = "+name);
					
				Map<String, Object> data = DatabaseSchema.rowMap(row); 
				LocalConcept c = newConcept(data, spec);
				
				return c;
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		
	}

	//////////////////////////////////////////////////////////////////////////////
	
	@Override
	protected DatabaseConceptInfoStore<LocalConcept> newConceptInfoStore() throws SQLException {
		ConceptInfoStoreSchema schema = new ConceptInfoStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), true, tweaks, false, true);
		return new DatabaseLocalConceptInfoStore(schema, tweaks);
	}

	@Override
	protected DatabaseStatisticsStore newStatisticsStore() throws SQLException {
		StatisticsStoreSchema schema = new LocalStatisticsStoreSchema(getDatasetIdentifier(), getDatabaseAccess().getConnection(), tweaks, false); 
		return new DatabaseLocalStatisticsStore(schema, tweaks);
	}
}
