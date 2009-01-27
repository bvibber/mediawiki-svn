package de.brightbyte.wikiword.store;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.MessageFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.BufferBasedInserter;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseAgendaPersistor;
import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.InserterFactory;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.StatementBasedInserter;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ExtractionRule;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;

/**
 * A WikiStoreBuilder implemented based upon a {@link de.brightbyte.db.DatabaseSchema} object,
 * that is, based upon a relational database.
 * 
 * The TweakSet supplied to the constructur is used to determine options, including:
 * <ul>
 * <li>dbstore.backgroundFlushQueue: int, default 4: the number of background flushes that may be queued at a given time. See {@link de.brightbyte.db.DatabaseSchema}</li>
 * <li>dbstore.useEntityBuffer: boolean, default true: wether inserts into entity tables should be buffered.</li>
 * <li>dbstore.useRelationBuffer: boolean, default true: wether inserts into entity tables should be buffered.</li>
 * <li>dbstore.insertionBufferFactor: int, default 16: scale for buffer sizes. Should be adapted to available heap size.</li>
 * <li><i>more options are used by {@link de.brightbyte.wikiword.store.DatabaseLocalConceptStore}, see there</i></li>
 * </ul>
 * 
 * @see DatabaseLocalConceptStore
 * @see LocalConceptStoreBuilder
 */
public class DatabaseLocalConceptStoreBuilder 
		extends AbstractDatabaseStoreBuilder 
		implements LocalConceptStoreBuilder
{
	
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database defined by the DatabaseConnectionInfo.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param dbInfo database connection info, used to connect to the database
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 * @throws SQLException 
	 */
	public DatabaseLocalConceptStoreBuilder(Corpus corpus, DatabaseConnectionInfo dbInfo, TweakSet tweaks) throws SQLException {
		this(new DatabaseSchema(corpus.getDbPrefix(), dbInfo, tweaks.getTweak("dbstore.backgroundFlushQueue", 4)), tweaks);
	}
	
	/**
	 * Constructs a DatabaseWikiStore, soring information from/about the given Corpus
	 * into the database accessed by the given database connection.
	 * 
	 * @param corpus the Corpus from which the data is extracted. 
	 *        Used to determin the table names (from Corpus.getDbPrefix) and to generate URIs.
	 * @param db a database connection
	 * @param tweaks a tweak set from which additional options can be taken (see description at the top).
	 * @throws SQLException 
	 */
	public DatabaseLocalConceptStoreBuilder(Corpus corpus, Connection db, TweakSet tweaks) throws SQLException {
		this(new DatabaseSchema(corpus.getDbPrefix(), db, tweaks.getTweak("dbstore.backgroundFlushQueue", 4)), tweaks);
	}
	
	public DatabaseLocalConceptStoreBuilder(DatabaseAccess database, TweakSet tweaks) throws SQLException {
		super(database, tweaks);

		InserterFactory entityInserterFactory = tweaks.getTweak("dbstore.useEntityBuffer", Boolean.TRUE) ? BufferBasedInserter.factory : StatementBasedInserter.factory;
		InserterFactory relationInserterFactory = tweaks.getTweak("dbstore.useRelationBuffer", Boolean.TRUE) ? BufferBasedInserter.factory : StatementBasedInserter.factory;
		
		database.getTable("warning").setInserterFactory(StatementBasedInserter.factory);

		addTable("rawtext", 512, 1024);
		addTable("plaintext", 512, 1024);
		addTable("section", 64, 64);
		addTable("definition", 1024, 256);
		addTable("resource", 256, 32);
		addTable("concept", 256, 32);
		addTable("link", 8*1024, 64);
		addTable("term", 8*1024, 64);
		addTable("meaning", 8*1024, 64);
		addTable("broader", 1024, 64);
		addTable("alias", 1024, 64);
		addTable("langlink", 2*1024, 64);
	}

	/**
	 * @throws PersistenceException 
	 * @see de.brightbyte.wikiword.store.LocalConceptStore#finish()
	 */
	public void finish() throws PersistenceException {
		//TODO: split up!
		try {
			flush();
			log("finishing");
			
			//TODO: this analysis logic should go into the importer! <----------------------------

			if (shouldRun("finish.enableKeys")) database.enableKeys();
				
			//TODO: try this: enable keys, select into temp tables, 
			//                then disable keys, copy into real tables, enable keys. 

			//FIXME: check that this actually WORKS!
			if (shouldRun("finish.buildSectionConcepts")) buildSectionConcepts();      
			if (shouldRun("finish.buildSectionBroader")) buildSectionBroader(0.9);      
			if (shouldRun("finish.truncateTable:section")) database.truncateTable(sectionTable.getName());
			
			if (shouldRun("finish.buildMissingConcepts:link")) buildMissingConcepts(linkTable, "target_name");     
			//if (shouldRun("finish.buildMissingConcepts:reference")) buildMissingConcepts(referenceTable, "target_name"); 
			if (shouldRun("finish.buildMissingConcepts:broader")) buildMissingConcepts(broaderTable, "broad_name");  //XXX: needed? after redirs are resolved?
			if (shouldRun("finish.buildMissingConcepts:alias")) buildMissingConcepts(aliasTable, "target_name"); //XXX: needed? 
			if (shouldRun("finish.buildMissingConcepts:section")) buildMissingConcepts(sectionTable, "page"); 
			
			//XXX: if (shouldRun("finish.buildIdLinks:link.term_text")) buildIdLinks(useTable, "term_text", "term");           //Uses index _use.term (and unique key _term.term)
			//NOTE: don't need this, anchor-id is only null if anchor_name is null too. //XXX: really?! if (shouldRun("finish.buildIdLinks:link.anchor_name")) buildIdLinks(linkTable, "anchor_name", "anchor");     //Uses index _use.target (and unique key _concept.name)
			if (shouldRun("finish.buildIdLinks:link.target_name")) buildIdLinks(linkTable, "target_name", "target");     //Uses index _use.target (and unique key _concept.name)
			if (shouldRun("finish.buildIdLinks:broader")) buildIdLinks(broaderTable, "broad_name", "broad");     //Uses index _broader.broad (and unique key _concept.name)
			//XXX: do we really need to resolve this? wouldn't it be faster to resolve redirects directly?
			if (shouldRun("finish.buildIdLinks:alias")) buildIdLinks(aliasTable, "target_name", "target");  //Uses index _alias.target (and unique key _concept.name)
			//if (shouldRun("finish.buildIdLinks:reference")) buildIdLinks(referenceTable, "target_name", "target"); //Uses index _reference.target (and unique key _concept.name)

			//NOTE: resolve redirects TWICE for each table, to catch double redirects. ignore tripple redirects
			//XXX: we could try to continue until no more redirects are resolved - but there might be cycles!
			if (shouldRun("finish.resolveRedirects:link#1")) resolveRedirects(linkTable, "target_name", "target");     //uses index _use.usage 
			if (shouldRun("finish.resolveRedirects:link#2")) resolveRedirects(linkTable, "target_name", "target");     //uses index _use.usage 
			//if (shouldRun("finish.resolveRedirects:reference#1")) resolveRedirects(referenceTable, "target_name", "target"); //uses index _reference.target
			//if (shouldRun("finish.resolveRedirects:reference#2")) resolveRedirects(referenceTable, "target_name", "target"); //uses index _reference.target
			if (shouldRun("finish.resolveRedirects:broader#1")) resolveRedirects(broaderTable, "broad_name", "broad");     //uses index _broader.broad
			if (shouldRun("finish.resolveRedirects:broader#2")) resolveRedirects(broaderTable, "broad_name", "broad");     //uses index _broader.broad
			
			if (shouldRun("finish.reportBadLinks:link,ALIAS")) reportBadLinks(linkTable, "target", ConceptType.ALIAS);        //uses index _use.concept, _concept.type
			//if (shouldRun("finish.reportBadLinks:reference,ALIAS")) reportBadLinks(referenceTable, "target", ConceptType.ALIAS);   //uses index _reference.target, _concept.type
			if (shouldRun("finish.reportBadLinks:broader,ALIAS")) reportBadLinks(broaderTable, "broad", ConceptType.ALIAS);      //uses index _broader.broader, _concept.type
			
			if (shouldRun("finish.deleteBadLinks:link,ALIAS")) deleteBadLinks(linkTable, "target", ConceptType.ALIAS);        //uses index _use.concept, _concept.type
			//if (shouldRun("finish.deleteBadLinks:reference,ALIAS")) deleteBadLinks(referenceTable, "target", ConceptType.ALIAS);   //uses index _reference.target, _concept.type
			if (shouldRun("finish.deleteBadLinks:broader,ALIAS")) deleteBadLinks(broaderTable, "broad", ConceptType.ALIAS);      //uses index _broader.broader, _concept.type

			if (shouldRun("finish.truncateTable:alias")) database.truncateTable(aliasTable.getName());
			if (shouldRun("finish.deleteBadConcepts:ALIAS")) deleteBadConcepts(ConceptType.ALIAS); //uses index _concept.type
			
			/*
			if (shouldRun("finish.deleteBadLinks:link,NONE")) deleteBadLinks(useTable, "concept", ConceptType.NONE);        //uses index _use.concept, _concept.type
			//if (shouldRun("finish.deleteBadLinks:reference,NONE")) deleteBadLinks(referenceTable, "target", ConceptType.NONE);   //uses index _reference.target, _concept.type
			if (shouldRun("finish.deleteBadLinks:broader,NONE")) deleteBadLinks(broaderTable, "broad", ConceptType.NONE);      //uses index _broader.broader, _concept.type
			if (shouldRun("finish.deleteBadConcepts:NONE")) deleteBadConcepts(ConceptType.NONE);     //uses index _concept.type
			*/
			
			if (shouldRun("finish.buildMeanings")) buildMeanings(); 
			
			/*
			if (shouldRun("finish.prepareConceptCache:concept_info")) prepareConceptCache(conceptInfoTable, "concept");
			if (shouldRun("finish.buildConceptPropertyCache:concept_info,narrower")) buildConceptPropertyCache(conceptInfoTable, "concept", "narrower", broaderTable, "broad", narrowerReferenceListEntry.sqlField);
			if (shouldRun("finish.buildConceptPropertyCache:concept_info,broader")) buildConceptPropertyCache(conceptInfoTable, "concept", "broader", broaderTable, "narrow", broaderReferenceListEntry.sqlField);
			if (shouldRun("finish.buildConceptPropertyCache:concept_info,langlinks")) buildConceptPropertyCache(conceptInfoTable, "concept", "langlinks", langlinkTable, "concept", langlinkReferenceListEntry.sqlField);
			
			if (shouldRun("finish.prepareConceptCache:concept_description")) prepareConceptCache(conceptDescriptionTable, "concept"); 
			if (shouldRun("finish.buildConceptPropertyCache:concept_description,terms")) buildConceptPropertyCache(conceptDescriptionTable, "concept", "terms", meaningTable, "concept", termReferenceListEntry.sqlField);
			*/
			
			//TODO: test: redirects to bad pages, especially redirects to disambig pages. 
			//      should be ignored in use table...
			
			database.finish();
			//getAgenda().logComplete("finish");
			
			log("finished");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} catch (InterruptedException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void clearStatistics() throws PersistenceException {
		try {
			database.truncateTable("stats");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void buildStatistics() throws PersistenceException {		
		try {
			if (shouldRun("stats.prepareDegreeTable")) prepareDegreeTable();
			if (shouldRun("stats.buildDegreeInfo#in")) buildDegreeInfo("anchor", "target", "in");
			if (shouldRun("stats.buildDegreeInfo#out")) buildDegreeInfo("target", "anchor", "out");
			if (shouldRun("stats.combineDegreeInfo")) combineDegreeInfo("in", "out", "link");

			if (shouldRun("stats.inDegreeDistribution")) buildDistributionStats("in-degree distr", degreeTable, "in_rank", "in_degree");
			if (shouldRun("stats.outDegreeDistribution")) buildDistributionStats("out-degree distr", degreeTable, "out_rank", "out_degree");
			if (shouldRun("stats.linkDegreeDistribution")) buildDistributionStats("link-degree distr", degreeTable, "link_rank", "link_degree");
			
			if (shouldRun("stats.termRank")) buildTerms();
			if (shouldRun("stats.termZipf")) buildDistributionStats("term zipf", termTable, "rank", "freq");
			
			//TODO: characteristic path length and cluster coef
			//TODO: stats about collocations
			if (shouldRun("stats.table")) storeStatsEntries( "table", getTableStats() );
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public void buildDistributionStats(String name, DatabaseTable table, String rankField, String valueField) throws PersistenceException {
		try {
			log("building distribution statistics \""+name+"\" from "+table.getName()+"."+rankField+" x "+table.getName()+"."+valueField);
			
			String sql = "SELECT count(*) as t, sum("+valueField+") as N FROM "+table.getSQLName();
			Map<String, Object> m = database.executeSingleRowQuery("buildZipfStats", sql);
			
			double t = ((Number)m.get("t")).doubleValue(); //number of types (different terms)
			double N = ((Number)m.get("N")).doubleValue(); //number of tokens (term occurrances)
			
			sql = "SELECT avg(rank*value) as k, stddev(rank*value) as kd " +
					"FROM (SELECT MAX("+rankField+") as rank, "+valueField+" as value " +
							"FROM "+table.getSQLName()+" " +
							"GROUP BY "+valueField+") as G";
			
			m = database.executeSingleRowQuery("buildDistributionStats", sql);
			
			double k = ((Number)m.get("k")).doubleValue();   //average of maxrank * freq
			double kd = ((Number)m.get("kd")).doubleValue(); //deviation of maxrank * freq
			double c = k / N; 								 //characteristic fitting constant (normalized k) 
			
			Map<String, Number> stats = new HashMap<String, Number>(10);
			stats.put("total distinct types", t);
			stats.put("total token occurrences", N);
			stats.put("average type value-x-rank", k);
			stats.put("average type value", N/t);
			stats.put("deviation of type value-x-rank", kd);
			stats.put("norm. avg. type value-x-rank", c);
			
			storeStatsEntries(name, stats);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected void storeStatsEntry(String block, String name, double value) throws PersistenceException {
		//TODO: inserter?...
		String sql = "REPLACE INTO "+statsTable.getSQLName()+" (block, name, value) VALUES (" 
				+database.quoteLiteral(block)+", " 
				+database.quoteLiteral(name)+", " 
				+database.quoteValue(value)+") ";
		
		executeUpdate("storeStatsEntry", sql);
	}
	
	protected void storeStatsEntries(String block, ResultSet rs, GroupNameTranslator translator) throws PersistenceException {
		try {
			StringBuilder sql = new StringBuilder();
			sql.append( "REPLACE INTO " );
			sql.append( statsTable.getSQLName() );
			sql.append( " (block, name, value) VALUES" );
			
			boolean first = true;
			while (rs.next()) {
				if (first) first = false;
				else sql.append(", ");
					
				String name = rs.getString("name");
				double value = rs.getDouble("value");
				
				if (translator!=null) name = translator.translate(name);
				
				sql.append( "(" ); 
				sql.append(database.quoteLiteral(block));
				sql.append(", "); 
				sql.append(database.quoteLiteral(name));
				sql.append(", "); 
				sql.append(database.quoteValue(value));
				sql.append( ")" ); 
			}
			
			executeUpdate("storeStatsEntries", sql.toString());
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected void storeStatsEntries(String block, Map<String, ? extends Number> stats) throws PersistenceException {
		StringBuilder sql = new StringBuilder();
		sql.append( "REPLACE INTO " );
		sql.append( statsTable.getSQLName() );
		sql.append( " (block, name, value) VALUES" );
		
		boolean first = true;
		for (Map.Entry<String, ? extends Number> e: stats.entrySet()) {
			if (first) first = false;
			else sql.append(", ");
				
			String name = e.getKey();
			double value = e.getValue().doubleValue();
			
			sql.append( "(" ); 
			sql.append(database.quoteLiteral(block));
			sql.append(", "); 
			sql.append(database.quoteLiteral(name));
			sql.append(", "); 
			sql.append(database.quoteValue(value));
			sql.append( ")" ); 
		}
		
		executeUpdate("storeStatsEntries", sql.toString());
	}
	
	public void optimize() throws PersistenceException {
		//XXX: needed? useful?
		//if (shouldRun("finish.optimizeIndexes")) database.optimizeIndexes(); //optimizeTables should already do that 
		try {
			if (shouldRun("finish.optimizeTables")) database.optimizeTables();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}
	
	/**
	 * @see de.brightbyte.db.DatabaseSchema#close()
	 */
	public void close() throws PersistenceException {
		flush();
		
		if (resourceInserter!=null) resourceInserter.close();
		if (conceptInserter!=null) conceptInserter.close();
		//if (termInserter!=null) termInserter.close();
		
		if (definitionInserter!=null) definitionInserter.close();
		if (rawTextInserter!=null) rawTextInserter.close();
		if (plainTextInserter!=null) plainTextInserter.close();
		
		if (linkInserter!=null) linkInserter.close();
		if (broaderInserter!=null) broaderInserter.close();
		if (aliasInserter!=null) aliasInserter.close();
		//if (referenceInserter!=null) referenceInserter.close();
		
		if (langlinkInserter!=null) langlinkInserter.close();

		super.close();
	}
	
	public void open() throws PersistenceException {
		super.open();
	}
	
	//TODO: make smellsLikeWiki optional/configurable
	protected Matcher smellsLikeWiki = Pattern.compile("\\{\\{|\\}\\}|\\[\\[|\\]\\]|<[a-zA-Z]+.*>").matcher("");
	
	/**
	 * Check and sanitize name
	 * 
	 * @param rcId resource ID of text where the name occurred
	 * @param name the name to check & sanitize
	 * @param descr a description of what the name represents, for use in error messages. 
	 *        May contain the placeholder <tt>{0}</tt> to be replaced by the value of the
	 *        ctxId parameter.
	 * @param ctxId an id to be inserted into the descr string using MessageFormat.format
	 * @throws IllegalArgumentException if the name is invalid, especially if it is null, 
	 *         empty or contains speaces
	 * @return the given name, possibly truncated to be no longer than 255 bytes when
	 *         encoded as UTF-8 (enforced using clipString)
	 */
	protected String checkName(int rcId, String name, String descr, int ctxId) {
		if (name.length()==0) throw new IllegalArgumentException(descr+" must not be empty");
		if (name.indexOf(' ')>=0) throw new IllegalArgumentException(descr+"must not contain spaces");
		
		checkSmellsLikeWiki(rcId, name, descr, ctxId);
		return clipString(rcId, name, 255, descr, ctxId);
	}
	
	/**
	 * Check if text "smalls" like wikitext. Sanity check to assist in finding problems
	 * with the analysis methods.
	 * 
	 * @param rcId resource ID of the origin of the text 
	 * @param text the text to check 
	 * @param descr a description of what the name represents, for use in error messages. 
	 *        May contain the placeholder <tt>{0}</tt> to be replaced by the value of the
	 *        ctxId parameter.
	 * @param ctxId an id to be inserted into the descr string using MessageFormat.format
	 */
	protected void checkSmellsLikeWiki(int rcId, String text, String descr, int ctxId) {
		if (smellsLikeWiki==null) return; //FIXME: configure! speed!
		
		smellsLikeWiki.reset(text);
		if (smellsLikeWiki.find()) {
			descr = MessageFormat.format(descr, ctxId);
			String s = StringUtils.describeLocation(text, smellsLikeWiki.start(), smellsLikeWiki.end());
			warning(rcId, "smells like wiki text", descr + ": " + s, null);
		}
	}
	
	/**
	 * Truncates the given string so that it will not exceed the given max number of bytes
	 * when encoded as UTF-8. Avoids actually encoding the string.
	 * 
	 * @param rcId resource ID of the origin of the string 
	 * @param s the string to truncate
	 * @param max the maximum number of bytes the string may use in UTF-8 encoded form
	 * @param ctxId an id to be inserted into the descr string using MessageFormat.format
	 * @return the given name, possibly truncated to be no longer than 255 bytes when
	 *         encoded as UTF-8
	 */
	protected String clipString(int rcId, String s, int max, String descr, int ctxId) {
		//NOTE: determine length in bytes when encoded as utf-8.
		char[] chars = s.toCharArray();
		int len = chars.length;
		if (len*4<max) return s; //#bytes can't be more than #chars * 4
		
		//int top = 0;
		int bytes = 0;
		for (int i = 0; i<len; i++) { //TODO: iterate codepoints instead of utf-16 chars.
			int ch = chars[i];
			//if (ch>top) top = ch;

			//XXX: hm, nice idea, but escape chars arn't counted anyway.
			//if (ch=='\'') bytes += 2; //plain ascii, but becomes 2 bytes by escaping
			//else if (ch=='\\') bytes += 2; //plain ascii, but becomes 2 bytes by escaping
			
			if (ch<128) bytes += 1; //plain ascii, 1 byte
			else if (ch<2048) bytes += 2; //2-byte encoding
			else if (ch<65536) bytes += 3; //3-byte encoding
			else bytes += 4; //3-byte encoding

			if (bytes>max) {
				s = s.substring(0, i);
				descr = MessageFormat.format(descr, ctxId);
				warning(rcId, "truncated", "long "+descr+" after "+max+" characters; new text: "+s, null); 
				break;
			}
		}
		
		return s;
	}
	
	/* (non-Javadoc)
	 * @see de.brightbyte.wikiword.WikiStoreBuilder#storeCorpus(java.net.URI)
	 */
	/*@Deprecated
	public int storeCorpus(String name, URI uri) throws PersistenceException { //FIXME: kill it!
		Inserter ins = new StatementBasedInserter(corpusTable, "id");
		ins.open(connection);
		ins.updateString("uri", snipString(uri.toString(), 255, "corpus uri ({0})", ins.getLastId()));
		ins.updateRow();
		ins.flush();
		ins.close();
		
		return ins.getLastId();
	}*/

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeRawText(int, java.lang.String)
	 */
	public int storeRawText(int rcId, String text) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (rawTextInserter==null) rawTextInserter = rawTextTable.getInserter();

			rawTextInserter.updateInt("resource", rcId);
			rawTextInserter.updateString("text", text);
			rawTextInserter.updateRow();
			
			return rcId;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storePlainText(int, java.lang.String)
	 */
	public int storePlainText(int rcId, String text) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (plainTextInserter==null) plainTextInserter = plainTextTable.getInserter();

			checkSmellsLikeWiki(rcId, text, "plain text of resource #{0}", rcId);
			
			plainTextInserter.updateInt("resource", rcId);
			plainTextInserter.updateString("text", text);
			plainTextInserter.updateRow();
			
			return rcId;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeSection(int, java.lang.String, java.lang.String)
	 */
	public void storeSection(int rcId, String name, String page) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (sectionInserter==null) sectionInserter = sectionTable.getInserter();

			name = checkName(rcId, name, "section name (from section link in resource #{0})", rcId);
			page = checkName(rcId, page, "concept name (from section link in resource #{0})", rcId);
			
			sectionInserter.updateInt("resource", rcId);
			sectionInserter.updateString("name", name);
			sectionInserter.updateString("page", page);
			sectionInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	private boolean inStoreWarning = false; //recursion stop

	public void warning(int rcId, String problem, String details, Exception ex) {
		String msg = problem+": "+details;
		if (rcId>0) msg += " (in resource #"+rcId+")";
		
		database.warning(msg);
		
		if (ex!=null) details += "\n" + ex;

		try {
			storeWarning(rcId, problem, details);
		} catch (PersistenceException e) {
			database.error("failed to store warning!", e);
		}
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeWarning(int, java.lang.String, java.lang.String)
	 */
	public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
		if (inStoreWarning) { //stop recursion dead.
			database.error("recursive call to storeWarning!", new RuntimeException("dummy exception for stack trace"));
			return;
		}
		
		inStoreWarning = true;
		
		try {
			if (warningInserter==null) warningInserter = warningTable.getInserter();
			
			problem = clipString(rcId, problem, 255, "problem name, resource #{0}", rcId);
			details = clipString(rcId, details, 1024, "problem details, resource #{0}", rcId);
			
			if (rcId<0) warningInserter.updateObject("resource", null);
			else warningInserter.updateInt("resource", rcId);

			warningInserter.updateString("timestamp", timestampFormatter.format(new Date()));
			warningInserter.updateString("problem", problem);
			warningInserter.updateString("details", details);
			warningInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
		finally {
			inStoreWarning = false;
		}
	}	

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeDefinition(int, java.lang.String)
	 */
	public int storeDefinition(int rcId, int conceptId, String definition) throws PersistenceException {
		try {
			if (conceptId<0) throw new IllegalArgumentException("bad concept id "+conceptId);
			if (definitionInserter==null) definitionInserter = definitionTable.getInserter();

			checkSmellsLikeWiki(rcId, definition, "definition of concept #{0}", conceptId);

			definitionInserter.updateInt("concept", conceptId);
			definitionInserter.updateString("definition", clipString(rcId, definition, 1024 * 8, "definition text (concept {0})", conceptId));
			definitionInserter.updateRow();
			
			return conceptId;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}	

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeResource(java.lang.String, de.brightbyte.wikiword.ResourceType, java.util.Date)
	 */
	public int storeResource(String name, ResourceType ptype, Date time) throws PersistenceException {
		try {
			if (resourceInserter==null) resourceInserter = resourceTable.getInserter();
			
			name = checkName(resourceInserter.getLastId()+1, name, "resource name ({0})", resourceInserter.getLastId()+1);
			
			resourceInserter.updateString("name", name );
			resourceInserter.updateInt("type", ptype.getCode());
			resourceInserter.updateString("timestamp", timestampFormatter.format(time));
			resourceInserter.updateRow();

			return resourceInserter.getLastId();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeConcept(int, java.lang.String, de.brightbyte.wikiword.ConceptType)
	 */
	public int storeConcept(int rcId, String name, ConceptType ctype) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (conceptInserter==null) conceptInserter = conceptTable.getInserter();
			
			name = checkName(rcId, name, "concept name (resource #{0})", rcId);
			
			conceptInserter.updateInt("resource", rcId);
			conceptInserter.updateString("name", name);
			conceptInserter.updateInt("type", ctype.getCode());
			conceptInserter.updateRow();

			return conceptInserter.getLastId();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected void reportBadLinks(DatabaseTable table, String conceptIdField, ConceptType type) throws PersistenceException {
		try {
			log("reporting bad links from "+table.getName());
			
			ReferenceField refField = (ReferenceField)table.getField(conceptIdField);
			DatabaseTable tgtTable = getTable(refField.getTargetTable());
			DatabaseField tgtField = tgtTable.getField(refField.getTargetField());
			
			if (warningInserter!=null) warningInserter.flush(); //flush first!
			
			//FIXME: sometimes, this is very slow. running the alias query first seems to help. odd :(
			String sql = "INSERT INTO " + warningTable.getSQLName()+ " (timestamp, problem, details, resource) " +
					" SELECT "+ database.quoteLiteral(timestampFormatter.format(new Date())) +", " +
							database.quoteLiteral("broken redirected link ("+type.getName()+")") + ", " +
							"CONCAT("+database.quoteLiteral(table.getName()+"."+conceptIdField+"->")+", "+conceptIdField+"), " + 
							table.getSQLName()+".resource " +
					" FROM "+table.getSQLName()
						+" JOIN "+conceptTable.getSQLName()
						+" ON "+table.getSQLName()+"."+conceptIdField+" = "+tgtTable.getSQLName()+"."+tgtField.getName()
						+" WHERE "+conceptTable.getSQLName()+".type = " + type.getCode();

			//System.out.println("*** "+sql+" ***");
			long t = System.currentTimeMillis();
			int n = executeUpdate("reportBadLinks", sql);
			log("reported "+n+" bad links from "+table.getName()+" in "+(System.currentTimeMillis()-t)/1000+" sec");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected void deleteBadLinks(DatabaseTable table, String conceptIdField, ConceptType type) throws PersistenceException {
		log("removing bad links from "+table.getName());
		
		ReferenceField refField = (ReferenceField)table.getField(conceptIdField);
		DatabaseTable tgtTable = getTable(refField.getTargetTable());
		DatabaseField tgtField = tgtTable.getField(refField.getTargetField());
		
		//FIXME: sometimes, this is very slow. running the alias query first seems to help. odd :(
		String sql = "DELETE FROM "+table.getSQLName()
					+" USING "+table.getSQLName()
						+" JOIN "+conceptTable.getSQLName()
							+" ON "+table.getSQLName()+"."+conceptIdField+" = "+tgtTable.getSQLName()+"."+tgtField.getName()
					+" WHERE "+conceptTable.getSQLName()+".type = " + type.getCode();

		//System.out.println("*** "+sql+" ***");
		long t = System.currentTimeMillis();
		int n = executeUpdate("deleteBadLinks", sql);
		log("removed "+n+" bad links from "+table.getName()+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void deleteBadConcepts(ConceptType type) throws PersistenceException {
		log("deleting non-concepts ("+type+") from "+conceptTable.getName());
		
		//FIXME: sometimes, this is very slow. running the alias query first seems to help. odd :(
		String sql = "DELETE FROM "+conceptTable.getSQLName()
					+" WHERE type = " + type.getCode();

		long t = System.currentTimeMillis();
		int n = executeUpdate("deleteBadConcepts", sql);
		log("removed "+n+" non-concepts ("+type+") from "+conceptTable+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}

	protected void buildSectionConcepts() throws PersistenceException {
		log("building concepts for sections");
		
		//NOTE: we shouldn't need the "ignore" bit. Let'S keep it for robustness
		String sql = "INSERT ignore INTO "+conceptTable.getSQLName()+" ( name, type, resource ) "
					+"SELECT S.name, "+ConceptType.UNKNOWN.getCode()+", C.resource "
					+"FROM "+sectionTable.getSQLName()+" AS S "
					+"LEFT JOIN "+conceptTable.getSQLName()+" AS C "
					+"ON S.page = C.name "
					+"WHERE C.type IS NULL "
					//+"OR C.type != "+ConceptType.NONE.getCode()+" " //XXX: really allow type ALIAS? //XXX: check for NONE/BAD?!
					+"GROUP BY S.name";

		long t = System.currentTimeMillis();
		int n = executeUpdate("buildSectionConcepts", sql);
		log("created "+n+" concepts for sections"+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void buildSectionBroader(double confidence) throws PersistenceException {
		log("building broader-links for sections");
		
		String sql = "INSERT INTO "+broaderTable.getSQLName()+" ( resource, narrow_name, broad_name, confidence ) "
					+"SELECT NULL, name, page, "+confidence+" " //XXX: could determin resource from the concept associated with the section. but that would be massive overhead for little gain
					+"FROM "+sectionTable.getSQLName()+" "  
					+"GROUP BY name"; //GROUP to avoid redundancy. there's only one "broader" relation, even if the section is mentioned multiple times.  

		long t = System.currentTimeMillis();
		int n = executeUpdate("buildSectionBroader", sql);
		log("created "+n+" broader-links for sections"+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void buildMissingConcepts(DatabaseTable table, String conceptNameField) throws PersistenceException {
		log("building missing concepts from "+table.getName());
		
		//NOTE: we shouldn't need the "ignore" bit. Let'S keep it for robustness
		String sql = "INSERT ignore INTO "+conceptTable.getSQLName()+" ( name, type, resource ) "
					+"SELECT "+conceptNameField+", "+ConceptType.UNKNOWN.getCode()+", NULL "
					+"FROM "+table.getSQLName()+" "
					+"LEFT JOIN "+conceptTable.getSQLName()+" "
					+"ON "+conceptNameField+" = "+conceptTable.getSQLName()+".name "
					+"WHERE "+conceptTable.getSQLName()+".name  IS NULL "
					+"GROUP BY "+conceptNameField;

		long t = System.currentTimeMillis();
		int n = executeUpdate("buildMissingConcepts", sql);
		log("created "+n+" missing concepts from "+table.getName()+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	/**
	 * Builds term frequency statistics. 
	 */
	/*protected void buildDegree() throws PersistenceException {
		log("building degree table");
		
		String sql = "INSERT INTO "+termTable.getSQLName()+" ( term, freq ) " +
				"SELECT term_text as term, sum(freq) as freq " +
				"FROM "+meaningTable.getSQLName()+" " +
				"GROUP BY term_text " +
				"ORDER BY freq DESC";
		
		long t = System.currentTimeMillis();
		int n = executeUpdate("buildTerms", sql);
		log("created "+n+" degree"+" entries in "+(System.currentTimeMillis()-t)/1000+" sec");
	}*/
	
	protected void prepareDegreeTable() throws PersistenceException {
		log("preparing degree table");
		
		String sql = "INSERT ignore INTO "+degreeTable.getSQLName()+" ( concept, concept_name ) "
			+" SELECT id, name "
			+" FROM "+conceptTable.getSQLName();

		long t = System.currentTimeMillis();
		int n = executeUpdate("prepareDegreeTable", sql);
		
		log("prepared "+n+" rows in degree table in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void buildDegreeInfo(String linkField, String groupField, String statsField) throws PersistenceException {
		log("building degree info for "+linkField+" into field "+statsField);
		
		String sql = "UPDATE "+degreeTable.getSQLName()+" AS D "
			+" JOIN ( SELECT "+groupField+" as concept, count("+linkField+") as degree " 
					+" FROM "+linkTable.getSQLName()+" " 
					+" WHERE anchor IS NOT NULL " 
					+" GROUP BY "+groupField+") AS X "
			+" ON X.concept = D.concept"
			+" SET "+statsField+"_degree = X.degree";

		//System.out.println("*** "+sql+" ***");
		long t = System.currentTimeMillis();
		int n = executeUpdate("buildDegreeInfo", sql);
		
		log("built degree info for "+linkField+" into field "+statsField+" on "+n+" rows "+(System.currentTimeMillis()-t)/1000+" sec");
		
		buildRank(degreeTable, statsField+"_degree", statsField+"_rank");
	}
	
	protected void buildRank(DatabaseTable table, String valueField, String rankField) throws PersistenceException {
		log("building ranks in "+table.getName()+"."+rankField+" based on "+table.getName()+"."+valueField);
		
		executeUpdate("buildDegreeInfo#init", "set @num = 0;");
		
		String sql = "UPDATE "+degreeTable.getSQLName()
			+" SET "+rankField+" = (@num := @num + 1)" 
			+" ORDER BY "+valueField+" DESC ";

		//System.out.println("*** "+sql+" ***");
		
		long t = System.currentTimeMillis();
		int n = executeUpdate("buildRank", sql);
	
		log("built ranks info in "+table.getName()+"."+rankField+" based on "+table.getName()+"."+valueField+" on "+n+" rows "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void combineDegreeInfo(String firstField, String secondField, String sumField) throws PersistenceException {
		log("building combined degree info for "+sumField);
		
		String sql = "UPDATE "+degreeTable.getSQLName()
			+" SET "+sumField+"_degree = "+firstField+"_degree + "+secondField+"_degree";

		//System.out.println("*** "+sql+" ***");
		long t = System.currentTimeMillis();
		int n = executeUpdate("buildDegreeInfo", sql);
		
		log("built combined degree info for "+sumField+" in "+n+" rows "+(System.currentTimeMillis()-t)/1000+" sec");
		
		buildRank(degreeTable, sumField+"_degree", sumField+"_rank");
	}
	
	protected void prepareConceptCache(DatabaseTable cacheTable, String conceptIdField) throws PersistenceException {
		log("preparing concept cache table "+cacheTable.getName());
		
		String sql = "INSERT ignore INTO "+cacheTable.getSQLName()+" ( " + conceptIdField + " ) "
			+" SELECT id "
			+" FROM "+conceptTable.getSQLName();

		long t = System.currentTimeMillis();
		int n = executeUpdate("prepareConceptCache", sql);
		
		log("prepared "+n+" rows in concept cache table "+cacheTable.getName()+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void buildConceptPropertyCache(DatabaseTable cacheTable, String cacheIdField, String propertyField, DatabaseTable relationTable, String relConceptField, String fieldPattern) throws PersistenceException {
		log("building concept property cache from "+relationTable+" into "+cacheTable.getName()+"."+propertyField);
		
		//XXX: if no frequency-field, evtl use inner grouping by nameField to determin frequency! (expensive, though)
		
		String sql = "UPDATE "+cacheTable.getSQLName()+" AS C "
			+" JOIN ( SELECT "+relConceptField+", group_concat("+fieldPattern+" separator '"+referenceSeparator+"' ) as s" +
					"	 FROM "+relationTable.getSQLName()+" group by "+relConceptField+" )" +
					" AS R " 
			+" ON R."+relConceptField+" = C."+cacheIdField
			+" SET "+propertyField+" = s";

		long t = System.currentTimeMillis();
		int n = executeUpdate("buildConceptPropertyCache", sql);
		
		log("updated "+n+" rows in "+cacheTable.getName()+" from "+relationTable+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeLink(int, int, java.lang.String, java.lang.String, java.lang.String, de.brightbyte.wikiword.ExtractionRule)
	 */
	public void storeLink(int rcId, int anchorId, String anchorName, 
			String term, String targetName, ExtractionRule rule) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (linkInserter==null) linkInserter = linkTable.getInserter();
			
			checkSmellsLikeWiki(rcId, term, "term", -1);
			
			linkInserter.updateInt("resource", rcId);
			if (anchorId>0) linkInserter.updateInt("anchor", anchorId);
			if (anchorName!=null) linkInserter.updateString("anchor_name", checkName(rcId, anchorName, "concept name (anchor ~ resource #{0})", rcId));
			linkInserter.updateString("term_text", clipString(rcId, term, 255, "term text (resource #{0})", rcId));
			linkInserter.updateString("target_name", checkName(rcId, targetName, "concept name (target in resource #{0})", rcId));
			linkInserter.updateInt("rule", rule.getCode());
			linkInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeReference(int, java.lang.String, int, java.lang.String, de.brightbyte.wikiword.ExtractionRule)
	 */
	public void storeReference(int rcId, String term, int targetId, String targetName, 
			ExtractionRule rule) throws PersistenceException {		
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (linkInserter==null) linkInserter = linkTable.getInserter();
			
			checkSmellsLikeWiki(rcId, term, "term", -1);
			
			linkInserter.updateInt("resource", rcId);
			if (targetId>0) linkInserter.updateInt("target", targetId);
			linkInserter.updateString("target_name", checkName(rcId, targetName, "concept name (target, resource #{0})", rcId));
			linkInserter.updateString("term_text", term);
			linkInserter.updateInt("rule", rule.getCode());
			linkInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeConceptBroader(int, int, java.lang.String, java.lang.String, float)
	 */
	public void storeConceptBroader(int rcId, int narrowId, String narrowName, String broadName, float confidence, ExtractionRule rule) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (broaderInserter==null) broaderInserter = broaderTable.getInserter();
			
			broaderInserter.updateInt("resource", rcId);
			broaderInserter.updateInt("narrow", narrowId);
			broaderInserter.updateString("narrow_name", checkName(rcId, narrowName, "concept name (resource #{0})", rcId));
			broaderInserter.updateString("broad_name", checkName(rcId, broadName, "concept name (resource #{0})", rcId));
			broaderInserter.updateFloat("confidence", confidence);
			broaderInserter.updateInt("rule", rule.getCode());
			broaderInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeConceptAlias(int, int, java.lang.String, java.lang.String, float)
	 */
	public void storeConceptAlias(int rcId, int source, String sourceName, String targetName, float confidence) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (aliasInserter==null) aliasInserter = aliasTable.getInserter();
			
			aliasInserter.updateInt("resource", rcId);
			aliasInserter.updateInt("source", source);
			aliasInserter.updateString("source_name", checkName(rcId, sourceName, "concept name (resource #{0})", rcId));
			aliasInserter.updateString("target_name", checkName(rcId, targetName, "concept name (resource #{0})", rcId));
			aliasInserter.updateFloat("confidence", confidence);
			aliasInserter.updateRow();
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
			if (referenceInserter==null) referenceInserter = referenceTable.getInserter();
			
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
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#storeLanguageLink(int, int, java.lang.String, java.lang.String, java.lang.String)
	 */
	public void storeLanguageLink(int rcId, int concept, String conceptName, String lang, String target) throws PersistenceException {
		try {
			if (rcId<0) throw new IllegalArgumentException("bad resource id "+rcId);
			if (langlinkInserter==null) langlinkInserter = langlinkTable.getInserter();
			
			langlinkInserter.updateInt("resource", rcId);
			langlinkInserter.updateInt("concept", concept);
			langlinkInserter.updateString("concept_name", checkName(rcId, conceptName, "concept name (resource #{0})", rcId));
			langlinkInserter.updateString("language", lang);
			langlinkInserter.updateString("target", checkName(rcId, target, "external concept name (resource #{0})", rcId));
			langlinkInserter.updateRow();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	/**
	 * Builds term frequency statistics. 
	 */
	protected void buildTerms() throws PersistenceException {
		log("building term table");
		
		String sql = "INSERT INTO "+termTable.getSQLName()+" ( term, freq ) " +
				"SELECT term_text as term, sum(freq) as freq " +
				"FROM "+meaningTable.getSQLName()+" " +
				"GROUP BY term_text " +
				"ORDER BY freq DESC";
		
		long t = System.currentTimeMillis();
		int n = executeUpdate("buildTerms", sql);
		log("created "+n+" terms"+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	/**
	 * Builds term->concept relation with frequency;
	 * condenses use table. 
	 */
	protected void buildMeanings() throws PersistenceException {
		log("building meaning table");
		
		String sql = "INSERT INTO "+meaningTable.getSQLName()+" ( concept, concept_name, term_text, freq ) " +
				"SELECT target, target_name, term_text, count(*) as freq " +
				"FROM "+linkTable.getSQLName()+" " +
				"GROUP BY target, term_text ";
		
		long t = System.currentTimeMillis();
		int n = executeUpdate("buildMeanings", sql);
		log("created "+n+" meaning"+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	/**
	 * Builds id-references from name-references
	 */
	protected void buildIdLinks(DatabaseTable table, String relNameField, String relIdField) throws PersistenceException {
		DatabaseField nmField = table.getField(relNameField);
		DatabaseField idField = table.getField(relIdField);
		
		if (!(nmField instanceof ReferenceField)) throw new IllegalArgumentException(relNameField+" is not a reference field in table "+table.getName());
		if (!(idField instanceof ReferenceField)) throw new IllegalArgumentException(relIdField+" is not a reference field in table "+table.getName());
		
		String nmTable = ((ReferenceField)nmField).getTargetTable();
		String idTable = ((ReferenceField)idField).getTargetTable();
		
		if (!nmTable.equals(idTable)) throw new IllegalArgumentException(relNameField+" and "+relIdField+" in table "+table.getName()+" do not reference the same table: "+nmTable+" != "+idTable);
		DatabaseTable target = getTable(nmTable);

		String targetNameField = ((ReferenceField)nmField).getTargetField();
		String targetIdField = ((ReferenceField)idField).getTargetField();
		
		String sql = "UPDATE "+table.getSQLName()+" as R JOIN "+target.getSQLName()+" as E "
					+ " ON R."+relNameField+" = E."+targetNameField+" "
					+ " SET R."+relIdField+" = E."+targetIdField+" "
					+ " WHERE  R."+relIdField+" IS NULL";
		
		log("building id links from "+table.getName()+"."+relIdField+" to "+target.getName()+"."+targetIdField); //TODO: LOGGER!
		long t = System.currentTimeMillis();
		int n = executeUpdate("buildIdLinks", sql);
		log("updated "+n+" references"+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void resolveRedirects(DatabaseTable table, String relNameField, String relIdField) throws PersistenceException {
		DatabaseField nmField = table.getField(relNameField);
		DatabaseField idField = table.getField(relIdField);
		
		if (!(nmField instanceof ReferenceField)) throw new IllegalArgumentException(relNameField+" is not a reference field in table "+table.getName());
		if (!(idField instanceof ReferenceField)) throw new IllegalArgumentException(relIdField+" is not a reference field in table "+table.getName());
		
		String nmTable = ((ReferenceField)nmField).getTargetTable();
		String idTable = ((ReferenceField)idField).getTargetTable();
		
		if (!nmTable.equals(idTable)) throw new IllegalArgumentException(relNameField+" and "+relIdField+" in table "+table.getName()+" do not reference the same table: "+nmTable+" != "+idTable);

		String sql = "UPDATE "+table.getSQLName()+" as R JOIN "+aliasTable.getSQLName()+" as E "
					+ " ON R."+relIdField+" = E.source "
					+ " SET R."+relIdField+" = E.target, "
					+ " R."+relNameField+" = E.target_name";
		
		log("resolving redirects on "+table.getName()+"."+relIdField); 
		long t = System.currentTimeMillis();
		int n = executeUpdate("resolveRedirects", sql);
		log("updated "+n+" references"+" in "+(System.currentTimeMillis()-t)/1000+" sec");
	}

	/**
	 * @throws PersistenceException 
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#prepare()
	 */
	public void prepare() throws PersistenceException, PersistenceException {
		if (getAgenda().shouldRun("prepare")) {
			try {
				database.disableKeys();
				getAgenda().logComplete("prepare", false);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	}

	protected void deleteDataFrom(int rcId, String op, DatabaseTable table, String field, boolean viaConcept) throws PersistenceException {
		String sql;
		
		if (viaConcept) {
			sql = "DELETE FROM T";
			sql += " USING "+table.getSQLName()+" AS T ";
			sql += " JOIN "+conceptTable.getSQLName()+" AS C";
			sql += " ON T."+field+" = C.id";
			sql += " WHERE C.resource "+op+" "+rcId;
		}
		else {
			sql = "DELETE FROM "+table.getSQLName();
			sql += " WHERE "+field+" "+op+" "+rcId;
		}
		
		long t = System.currentTimeMillis();
		int c = executeUpdate("deleteDataFrom", sql);
		trace("deleted "+c+" rows from "+table.getName()+" where "+field+" "+op+" "+rcId+(viaConcept?" via concept":"")+", took "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected int executeUpdate(String name, String sql) throws PersistenceException {
		try {
			return database.executeUpdate(name, sql);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected void deleteDataFrom(int rcId, String op) throws PersistenceException {
		//XXX: delete derived terms. (if using terms table)
		//FIXME: get corresponding concept id, remove definition accordingly 
		
		deleteDataFrom(rcId, op, definitionTable, "concept", true);
		
		deleteDataFrom(rcId, op, langlinkTable, "resource", false);
		deleteDataFrom(rcId, op, linkTable, "resource", false);
		deleteDataFrom(rcId, op, broaderTable, "resource", false);
		deleteDataFrom(rcId, op, aliasTable, "resource", false);
		//deleteDataFrom(rcId, op, referenceTable, "resource", false);
		deleteDataFrom(rcId, op, plainTextTable, "resource", false);
		deleteDataFrom(rcId, op, sectionTable, "resource", false);
		deleteDataFrom(rcId, op, rawTextTable, "resource", false);
		deleteDataFrom(rcId, op, conceptTable, "resource", false);
		deleteDataFrom(rcId, op, resourceTable, "id", false);
	}

	public void deleteDataFrom(int rcId) throws PersistenceException {
		log("deleting data from resource "+rcId);
		deleteDataFrom(rcId, "=");
	}

	public void deleteDataAfter(int rcId) throws PersistenceException {
		log("deleting data from resource with id > "+rcId);
		deleteDataFrom(rcId, ">");
	}
	
	/**
	 * @see de.brightbyte.db.DatabaseSchema#dropTables(boolean)
	 */
	public void dropTables(boolean opt) throws PersistenceException {
		try {
			database.dropTables(opt);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.db.DatabaseSchema#createTables(boolean)
	 */
	public void createTables(boolean opt) throws PersistenceException {
		try {
			database.createTables(opt);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}		
	}

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#getAgenda()
	 */
	public Agenda getAgenda() throws PersistenceException {
		if (agenda==null) {
			try {
				DatabaseAgendaPersistor log = new DatabaseAgendaPersistor(logTable); 
				agenda = new Agenda(log);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
		return agenda;
	}

	protected boolean shouldRun(String task) throws PersistenceException, SQLException {
		if (agenda==null) getAgenda();
		return agenda.shouldRun(task);
	}

	public int getNumberOfWarnings() throws PersistenceException {
		try {
			return database.getTableSize("warning");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public int getConceptId(String name) throws PersistenceException {
		try {
			String sql = "select id from "+conceptTable.getSQLName()+" where name = "+database.quoteLiteral(name);
			Integer id = (Integer)database.executeSingleValueQuery("getConceptId", sql);
			return id == null ? 0 : id;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public int getResourceId(String name) throws PersistenceException {
		try {
			String sql = "select id from "+resourceTable.getSQLName()+" where name = "+database.quoteLiteral(name);
			Integer id = (Integer)database.executeSingleValueQuery("getConceptId", sql);
			return id == null ? 0 : id;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public int storeDefinition(int rcId, String name, int conceptId, ResourceType ptype, String definition) throws PersistenceException {
		return storeDefinition(rcId, conceptId, definition);
	}

	public int storePlainText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		return storePlainText(rcId, text);
	}

	public int storeRawText(int rcId, String name, ResourceType ptype, String text) throws PersistenceException {
		return storeRawText(rcId, text);
	}

}
