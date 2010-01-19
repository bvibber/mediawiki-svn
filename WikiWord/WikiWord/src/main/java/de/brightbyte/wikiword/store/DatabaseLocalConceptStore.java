package de.brightbyte.wikiword.store;

import static de.brightbyte.db.DatabaseUtil.asDouble;
import static de.brightbyte.db.DatabaseUtil.asInt;
import static de.brightbyte.db.DatabaseUtil.asString;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
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
import de.brightbyte.wikiword.model.ConceptDescription;
import de.brightbyte.wikiword.model.ConceptRelations;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.LocalConceptReference;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.TranslationReference;
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
public class DatabaseLocalConceptStore extends DatabaseWikiWordConceptStore<LocalConcept, LocalConceptReference> 
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
		
	protected String meaningWhere(String term) {
		return " JOIN "+meaningTable.getSQLName()+" as M ON C.id = M.concept " +
		" WHERE M.term_text = "+database.quoteString(term)+" " +
		" ORDER BY freq DESC";
	}
	
	@Override
	protected LocalConceptReference newReference(int id, String name, int card, double relevance) {
		return new LocalConceptReference(id, name, card, relevance);
	}
	
	@Override
	protected LocalConceptReference[] newReferenceArray(int n) {
		return new LocalConceptReference[n];
	}
	
	public ConceptType getConceptType(int type) {
		return corpus.getConceptTypes().getType(type);
	}

	public Corpus getCorpus() {
		return corpus;
	}
	
	public DataSet<LocalConceptReference> listMeanings(String term)
		throws PersistenceException { 
			
		String sql = referenceSelect("M.freq") + meaningWhere(term);
		
		return new QueryDataSet<LocalConceptReference>(database, getRowReferenceFactory(), "listMeanings", sql, false);
	}

	public LocalConcept getConceptByName(String name) throws PersistenceException {
		return ((DatabaseLocalConceptInfoStore)getConceptInfoStore()).getConceptByName(name);
	}

	public DataSet<LocalConcept> getMeanings(String term) throws PersistenceException {
		return ((DatabaseLocalConceptInfoStore)getConceptInfoStore()).getMeanings(term);
	}
		
	public TermReference pickRandomTerm(int top) throws PersistenceException {
		return ((LocalStatisticsStore<LocalConcept, LocalConceptReference>)getStatisticsStore()).pickRandomTerm(top);
	}
	
	public DataSet<TermReference> getAllTerms() throws PersistenceException {
		return ((LocalStatisticsStore<LocalConcept, LocalConceptReference>)getStatisticsStore()).getAllTerms();
	}

	public int getNumberOfTerms() throws PersistenceException {
		return ((LocalStatisticsStore<LocalConcept, LocalConceptReference>)getStatisticsStore()).getNumberOfTerms();
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////

	protected static final DatabaseDataSet.Factory<TermReference> termFactory = new DatabaseDataSet.Factory<TermReference>() {
		public TermReference newInstance(ResultSet row) throws SQLException, PersistenceException {
			return newTerm(row);
		}
	};
	
	protected static TermReference newTerm(ResultSet row) throws SQLException, PersistenceException {
		int id = row.getInt("id");
		String name = asString(row.getObject("name"));
		int card = row.getInt("cardinality");
		double relevance = row.getInt("relevance");
		
		return new TermReference(id, name, card, relevance);
	}

	protected class DatabaseLocalStatisticsStore extends DatabaseStatisticsStore implements LocalStatisticsStore<LocalConcept, LocalConceptReference> {
		protected EntityTable termTable;
		protected int numberOfTerms = -1;

		protected DatabaseLocalStatisticsStore(StatisticsStoreSchema database, TweakSet tweaks) throws SQLException {
			super(database, tweaks);
			termTable =   (EntityTable)database.getTable("term"); 
		}

		public DataSet<TermReference> getAllTerms() throws PersistenceException {
			try {
				String sql = "SELECT rank as id, term, freq as cardinality, -1 as relevance FROM "+termTable.getSQLName()+" as T";
				return new ChunkedQueryDataSet<TermReference>(database, termFactory, "getAllTerms", "query", sql, null, null, termTable, "rank", queryChunkSize);
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
		protected String conceptSelect(String card, String relev, boolean useDistrib) {
			String distribJoin = !useDistrib ? "" : " JOIN " + database.getSQLTableName("degree", true) + " as DT ON DT.concept = C.id";
			
			String sql =  "SELECT C.id as cId, C.name as cName, C.type as cType, " +
					" "+card+" as qFreq, "+relev+" as qConf, " +
					" R.id as rcId, R.name as rcName, R.type as rcType, " +
					" F.definition as fDefinition, " +
					" I.inlinks as rInlinks, I.outlinks as rOutlinks, " +
					" I.broader as rBroader, I.narrower as rNarrower, I.langlinks as rLanglinks, " +
					" I.similar as rSimilar, I.related as rRelated, " + 
					" D.terms as dTerms" +
					" FROM "+conceptTable.getSQLName()+" as C "+
					distribJoin+
					" LEFT JOIN "+aboutTable.getSQLName()+" as A ON A.concept = C.id "+
					" LEFT JOIN "+resourceTable.getSQLName()+" as R ON R.id = A.resource "+
					" LEFT JOIN "+definitionTable.getSQLName()+" as F ON F.concept = C.id "+
					" LEFT JOIN "+conceptInfoTable.getSQLName()+" as I ON I.concept = C.id "+
					" LEFT JOIN "+conceptDescriptionTable.getSQLName()+" as D ON D.concept = C.id ";
			
			return sql;
		}
			
		@Override
		protected LocalConcept newConcept(Map<String, Object> m) {
			int id = asInt(m.get("cId"));
			String name = asString(m.get("cName"));
			ConceptType type = corpus.getConceptTypes().getType(asInt(m.get("cType")));
			
			int cardinality = m.get("qFreq") != null ? asInt(m.get("qFreq")) : -1;
			double relevance = m.get("qConf") != null ? asDouble(m.get("qConf")) : -1;
			
			int rcId = m.get("rcId") != null ? asInt(m.get("rcId")) : 0;
			String rcName = asString(m.get("rcName"));
			ResourceType rcType = m.get("rcType") != null ? ResourceType.getType(asInt(m.get("rcType"))) : null;

			String definition = asString(m.get("fDefinition"));
			
			LocalConceptReference reference = new LocalConceptReference(id, name, cardinality, relevance);
			LocalConceptReference[] inlinks = LocalConceptReference.parseList( asString(m.get("rInlinks")), ((ConceptInfoStoreSchema)database).inLinksReferenceListEntry ); 
			LocalConceptReference[] outlinks = LocalConceptReference.parseList( asString(m.get("rOutlinks")), ((ConceptInfoStoreSchema)database).outLinksReferenceListEntry ); 
			LocalConceptReference[] broader = LocalConceptReference.parseList( asString(m.get("rBroader")), ((ConceptInfoStoreSchema)database).broaderReferenceListEntry ); 
			LocalConceptReference[] narrower = LocalConceptReference.parseList( asString(m.get("rNarrower")), ((ConceptInfoStoreSchema)database).narrowerReferenceListEntry ); 
			TranslationReference[] langlinks = TranslationReference.parseList( asString(m.get("rLanglinks")), ((ConceptInfoStoreSchema)database).langlinkReferenceListEntry ); 
			LocalConceptReference[] similar = LocalConceptReference.parseList( asString(m.get("rSimilar")), ((ConceptInfoStoreSchema)database).similarReferenceListEntry ); 
			LocalConceptReference[] related = LocalConceptReference.parseList( asString(m.get("rRelated")), ((ConceptInfoStoreSchema)database).relatedReferenceListEntry ); 
			TermReference[] terms = TermReference.parseList( asString(m.get("dTerms")), ((ConceptInfoStoreSchema)database).termReferenceListEntry );
			
			WikiWordResource resource = rcId <= 0 ? null : new WikiWordResource(corpus, rcId, rcName, rcType);
			ConceptDescription description = new ConceptDescription(corpus, resource, type, definition, terms); 
			ConceptRelations<LocalConceptReference> relations = new ConceptRelations<LocalConceptReference>(broader, narrower, inlinks, outlinks, similar, related, langlinks);
			
			return new LocalConcept(reference, corpus, type, relations, description);
		}
		

		public DataSet<LocalConcept> getMeanings(String term)
			throws PersistenceException {
		
			String sql = conceptSelect("M.freq") + meaningWhere(term);
			
			return new QueryDataSet<LocalConcept>(database, new ConceptFactory(), "getMeanins", sql, false);
		}
		

		public LocalConcept getConceptByName(String name) throws PersistenceException {
			
			String sql = conceptSelect("-1") + " WHERE C.name = "+database.quoteString(name);
			
			try {
				ResultSet row = executeQuery("getConcept", sql);
				if (!row.next()) throw new PersistenceException("no concept found with name = "+name);
					
				Map<String, Object> data = DatabaseSchema.rowMap(row); 
				LocalConcept c = newConcept(data);
				
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
