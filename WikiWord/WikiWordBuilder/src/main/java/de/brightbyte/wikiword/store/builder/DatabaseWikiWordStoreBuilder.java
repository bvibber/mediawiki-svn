package de.brightbyte.wikiword.store.builder;

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
import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseKey;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.InserterFactory;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.db.RelationTable;
import de.brightbyte.db.StatementBasedInserter;
import de.brightbyte.util.ErrorHandler;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;
import de.brightbyte.wikiword.store.DatabaseWikiWordStore;

public class DatabaseWikiWordStoreBuilder 
	extends DatabaseWikiWordStore 
	implements WikiWordStoreBuilder {
	
	private Agenda agenda;
	
	protected Map<String, Inserter> inserters = new HashMap<String, Inserter>();
	
	protected Inserter warningInserter;
	
	private boolean inStoreWarning = false; //recursion stop
	protected InserterFactory entityInserterFactory;
	protected InserterFactory relationInserterFactory;
	
	protected int updateChunkSize;
	
	protected boolean useEntityBuffer;
	protected boolean useRelationBuffer;
		
	protected DatabaseWikiWordStoreBuilder(WikiWordStoreSchema database, TweakSet tweaks, Agenda agenda) throws SQLException {
		super(database, tweaks);
		
		this.agenda = agenda;
		
		int bufferScale = database.getBufferScale();
		
		useEntityBuffer   = bufferScale > 0 && tweaks.getTweak("dbstore.useEntityBuffer", Boolean.TRUE);
		useRelationBuffer = bufferScale > 0 && tweaks.getTweak("dbstore.useRelationBuffer", Boolean.TRUE);
		
		entityInserterFactory =   useEntityBuffer ? BufferBasedInserter.factory : StatementBasedInserter.factory;
		relationInserterFactory = useRelationBuffer ? BufferBasedInserter.factory : StatementBasedInserter.factory;

		updateChunkSize = tweaks.getTweak("dbstore.updateChunkSize", queryChunkSize);
		
		warningInserter = configureTable(warningTable, 0, 0);
		warningInserter.disableAutomaticField();
	}
	
	public void setBackgroundErrorHandler(ErrorHandler<Runnable, Throwable, Error> handler) {
		database.setBackgroundErrorHandler(handler);
	}
	
	public ErrorHandler<Runnable, Throwable, Error> getBackgroundErrorHandler() {
		return database.getBackgroundErrorHandler();
	}
	
	protected Inserter configureTable(String name,  int bufferLength, int bufferWidth) throws SQLException {
		DatabaseTable t = database.getTable(name);
		return configureTable(t, bufferLength, bufferWidth);
	}
	
	protected Inserter configureTable(DatabaseTable table,  int bufferLength, int bufferWidth) throws SQLException {
		if (table.getInserterFactory()==null) {
			if (table instanceof RelationTable) table.setInserterFactory(relationInserterFactory);
			else table.setInserterFactory(entityInserterFactory);
		}
		
		table.setBufferDimensions(bufferLength, bufferWidth);
		Inserter inserter =  table.getInserter();
		inserters.put(table.getName(), inserter);
		
		return inserter;
	}


	protected Inserter getInserter(String name) {
		Inserter inserter = inserters.get(name);
		if (inserter==null) throw new IllegalArgumentException("no inserter for table "+name);
		return inserter;
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#flush()
	 */
	public void flush() throws PersistenceException {
			for (Inserter inserter: inserters.values()) {
				try {
					inserter.flush();
				} catch (SQLException e) {
					throw new PersistenceException("error flushing table "+inserter.getTable().getName(), e);
				} 
			}
			
			try {
				database.flush(); //probably redundant
			} catch (InterruptedException e) {
				throw new PersistenceException("interrupted while waiting for flusde.brightbyte.dbh to complete", e);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			} 
	}

	/**
	 * @see de.brightbyte.db.DatabaseSchema#close()
	 * @see de.brightbyte.wikiword.store.LocalConceptStore#close()
	 */
	@Override
	public void close(boolean flush) throws PersistenceException {
			if (flush) flush();
			
			closeInserters();

			super.close(flush);
	}
	
	protected void closeInserters() {
		for (Inserter inserter: inserters.values()) {
			inserter.close();
		}
		
		inserters.clear();
	}

	@Override
	public void open() throws PersistenceException {
		super.open();
	}
	
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
		if (name.length()==0) {
			descr = MessageFormat.format(descr, ctxId);
			throw new IllegalArgumentException(descr+" must not be empty");
		}
		
		if (name.indexOf(' ')>=0) {
			descr = MessageFormat.format(descr, ctxId);
			throw new IllegalArgumentException(descr+" must not contain spaces ("+name+")");
		}
		
		return clipString(rcId, name, 255, descr, ctxId);
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
		String t = StringUtils.clipStringUtf8(s, max);
		if (t!=s) {
			descr = MessageFormat.format(descr, ctxId);
			
			if (inStoreWarning) database.warn("truncated long "+descr+" after "+max+" characters; new text: "+t); //NOTE: avoid recursion
			else warning(rcId, "truncated", "long "+descr+" after "+max+" characters; new text: "+t, null);
		}
		
		return t;
	}

	public void warning(int rcId, String problem, String details, Exception ex) {
		String msg = problem+": "+details;
		if (rcId>0) msg += " (in resource #"+rcId+")";
		
		database.warn(msg);
		
		if (ex!=null) details += "\n" + ex;

		try {
			storeWarning(rcId, problem, details);
		} catch (PersistenceException e) {
			database.error("failed to store warning!", e);
		}
	}
		
	protected void enableKeys() throws PersistenceException {
		try {
			database.enableKeys();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#storeWarning(int, java.lang.String, java.lang.String)
	 */
	public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
		if (inStoreWarning) { //stop recursion dead.
			database.error("recursive call to storeWarning!", new RuntimeException("dummy exception for stack trace"));
			return;
		}
		
		inStoreWarning = true;
		
		try {
			if (warningInserter==null) warningInserter = getInserter("warning");
			
			problem = clipString(rcId, problem, 255, "problem name, resource #{0}", rcId);
			details = clipString(rcId, details, 1024, "problem details, resource #{0}", rcId);
			
			if (rcId<0) warningInserter.updateObject("resource", null);
			else warningInserter.updateInt("resource", rcId);

			warningInserter.updateString("timestamp", DatabaseWikiWordStoreBuilder.timestampFormatter.format(new Date()));
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

	/*
	public boolean isComplete(String task) throws PersistenceException {
		try {
			String sql = "SELECT id FROM "+database.getSQLTableName("log")+" " +
					"WHERE level = 0 AND task = " + database.quoteString(task) +
					"ORDER BY id DESC LIMIT 1";
			Integer id = (Integer)database.executeSingleValueQuery("last agenda record", sql);
			return id != null;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}		
	}
	*/

	protected void deleteDataFrom(int rcId, String op, DatabaseTable rel, String field) throws PersistenceException {
		deleteDataFrom(rcId, op, rel, field, null, null, null, false);
	}
	
	protected void deleteDataFrom(int rcId, String op, DatabaseTable rel, String field, DatabaseTable via, String viaJoinField, String viaField, boolean deleteVia) throws PersistenceException {
		String sql;
		
		if (via!=null) {
			DatabaseKey k = via.findKeyForField(viaField);
			String forceCIndex = k==null ? null : k.getSQLName();

			k = rel.findKeyForField(field);
			String forceTIndex = k==null ? null : k.getSQLName();
			
			sql = "DELETE ";
			if (deleteVia) sql += " T, C FROM "; 
			else sql += " FROM T USING ";
			sql += rel.getSQLName()+" AS T ";
			if (forceTIndex!=null) sql += " FORCE INDEX(" + forceTIndex  + ") ";
			sql += " LEFT JOIN "+via.getSQLName()+" AS C";
			if (forceCIndex!=null) sql += " FORCE INDEX(" + forceCIndex  + ") ";
			sql += " ON T."+field+" = C."+viaJoinField+" ";
			sql += " WHERE C."+viaField+" "+op+" "+rcId;
		}
		else {
			sql = "DELETE FROM "+rel.getSQLName();
			sql += " WHERE "+field+" "+op+" "+rcId;
		}
		
		long t = System.currentTimeMillis();
		int c = executeUpdate("deleteDataFrom", sql);
		trace("deleted "+c+" rows from "+rel.getName()+" where "+field+" "+op+" "+rcId+(via!=null?" via "+via.getName():"")+", took "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected void deleteOrphansFrom(int rcId, String op, DatabaseTable table, RelationTable ref, String refField) throws PersistenceException {
		String sql;
		
		ReferenceField f = (ReferenceField)ref.getField(refField);
		String field = f.getTargetField();

		/*
		sql = "DELETE FROM " + table.getSQLName() + " ";
		sql += " WHERE NOT EXISTS ( ";
		sql += "   SELECT * FROM " + ref.getSQLName() + " AS R ";
		sql += "       WHERE R." + refField + " = "+ table.getSQLName() + "." + field;
		sql += " )";
		*/
		
		DatabaseKey k = table.findKeyForField(field);
		String forceEIndex = k==null ? null : k.getSQLName();

		k = ref.findKeyForField(refField);
		String forceRIndex = k==null ? null : k.getSQLName();
		
		sql = "DELETE FROM E ";
		sql += " USING " + table.getSQLName() + " AS E ";
		if (forceEIndex!=null) sql += " FORCE INDEX(" + forceEIndex  + ") ";
		sql += " LEFT JOIN " + ref.getSQLName() + " AS R ";
		if (forceRIndex!=null) sql += " FORCE INDEX(" + forceRIndex  + ") ";
		sql += "       ON R." + refField + " = E." + field;
		sql += " WHERE R." + refField + " IS NULL;";
		
		long t = System.currentTimeMillis();
		int c = executeUpdate("deleteOrphansFrom", sql);
		trace("deleted "+c+" orphan rows from "+table.getName()+" where no reference exists from "+ref.getSQLName()+"."+refField+", took "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected int executeUpdate(String name, String sql) throws PersistenceException {
		try {
			return database.executeUpdate(name, sql);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected int executeChunkedUpdate(String context, String name, String sql, String suffix, DatabaseTable chunkTable, String chunkField) throws PersistenceException {
		return executeChunkedUpdate(context, name, sql, suffix, chunkTable, chunkField, 1);
	}
	
	private static final Pattern suffixRestPattern = Pattern.compile("(.*)(((\\s+|^)on\\s+duplicate\\s+key\\s+|\\s*group\\s+by\\s+).*)", Pattern.CASE_INSENSITIVE);
	
	protected int executeChunkedUpdate(String context, String name, String sql, String suffix, DatabaseTable chunkTable, String chunkField, int factor) throws PersistenceException {
		if (updateChunkSize<=0) {
			if (suffix!=null && !suffix.matches("^(?i:\\s*on\\s+duplicate\\s+key\\s+|\\s*group\\s+by\\s+|\\s*where\\s+).*")) {
				suffix = " WHERE " + suffix;
			}
			
			if (suffix!=null) sql += " " + suffix;
			
			return executeUpdate(context+"::"+name, sql);
		}
	
		String where = null;
		String rest = null;
		
		if (suffix!=null) {
			//XXX: nasty hacks! suffix used for different stuff!
			
			Matcher m = suffixRestPattern.matcher(suffix);
			
			if (m.matches()) {
				rest = m.group(2);
				suffix = m.group(1);
			}
			
			suffix = suffix.trim();
			if (suffix.length()>0) {
				where = suffix.replaceAll("^(?i:\\s*where\\s+)?(.*)$", " $1");
			}
		}
		
		DatabaseAccess.SimpleChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(database, context, name, sql, where, rest, chunkTable, chunkField);
		
		return executeChunkedUpdate(query, factor);
	}
	
	protected int executeChunkedUpdate(DatabaseAccess.ChunkedQuery query, int factor) throws PersistenceException {
		try {
			int chunk = factor < 0 ? updateChunkSize * -factor : updateChunkSize / factor;
			return database.executeChunkedUpdate(query, chunk, getAgenda());
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected int deleteLoops(DatabaseTable t, String id1, String id2) throws SQLException {
		String sql = "DELETE FROM "+t.getSQLName()+" WHERE "+id1+" = "+id2;
		
		return database.executeUpdate("deleteLoops", sql);
	}

	/*
	public void purge() throws PersistenceException {
		dropTables(true);
		createTables(false);
	}
	*/
	
	public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
		if (purge) {
			dropTables(true, dropAll);
			createTables(false);
		}
		else {			
			createTables(true);
		}
		
		try {
			//NOTE: init id early! Would be nasty if we try this while looping through a streamed result set.
			warningInserter.getLastId();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}
	
	/**
	 * @see de.brightbyte.db.DatabaseSchema#dropTables(boolean)
	 */
	public void dropTables(boolean opt, boolean all) throws PersistenceException {
		log("dropping all tables!");
		
		try {
			for (String t: inserters.keySet()) {
				if (!all) {
					if (t.equals("warning")) continue;
				}
				
				database.dropTable(t, opt);
			}
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/**
	 * @see de.brightbyte.db.DatabaseSchema#createTables(boolean)
	 */
	public void createTables(boolean opt) throws PersistenceException {
		try {
			for (String t: inserters.keySet()) {
				boolean optional = opt;
				if (t.equals("warning")) optional = true;
				
				database.createTable(t, optional, false);
			}
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}		
	}

	/**
	 * @see de.brightbyte.wikiword.store.builder.LocalConceptStoreBuilder#getAgenda()
	 */
	@Override
	public Agenda getAgenda() {
		return agenda;
	}
	
	@Override
	public Agenda createAgenda() throws PersistenceException {
		if (agenda!=null) return agenda;

		try {
			DatabaseAgendaPersistor log = new DatabaseAgendaPersistor(database.getTable("log")); 
			agenda = new Agenda(log);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
		
		return agenda;
	}

	@Deprecated
	protected boolean beginPrimitiveTask(String context, String task) throws PersistenceException {
		if (agenda==null) getAgenda();
		return agenda.beginPrimitiveTask(context, task);
	}

	protected boolean beginTask(String context, String task) throws PersistenceException {
		if (agenda==null) getAgenda();
		return agenda.beginTask(context, task);
	}

	protected boolean beginTask(String context, String task, String params) throws PersistenceException {
		if (agenda==null) getAgenda();
		return agenda.beginTask(context, task, params);
	}

	protected void endTask(String context, String task) throws PersistenceException {
		if (agenda==null) getAgenda();
		agenda.endTask(context, task);
	}

	protected void endTask(String context, String task, String result) throws PersistenceException {
		if (agenda==null) getAgenda();
		agenda.endTask(context, task, result);
	}

	public void optimize() throws PersistenceException {
		//XXX: needed? useful?
		//if (shouldRun("finish.optimizeIndexes")) database.optimizeIndexes(); //optimizeTables should already do that 
		try {
			if (beginTask("optimize", "optimizeTables")) {
				database.optimizeTables();
				endTask("optimize", "optimizeTables");
			}
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}

	protected int resolveRedirects(RelationTable aliasTable, DatabaseTable table, String relNameField, String relIdField, AliasScope scope, int chunkFactor, String forceRIndex, String forceEIndex) throws PersistenceException {
		if (relIdField==null && relNameField==null) throw new IllegalArgumentException("relNameFields and relIdField can't both be null");
		
		if (forceRIndex==null) {
			DatabaseKey k = table.findKeyForField(relIdField);
			forceRIndex = k==null ? null : k.getSQLName();
		}
		
		if (forceRIndex==null && relIdField==null) {
			DatabaseKey k = table.findKeyForField(relNameField);
			forceRIndex = k==null ? null : k.getSQLName();
		}
		
		if (forceRIndex!=null && table.getKey(forceRIndex)==null) throw new IllegalArgumentException("unknown key: "+forceRIndex);
		
		if (forceEIndex==null) forceEIndex = "source";
		if (forceEIndex!=null && aliasTable.getKey(forceEIndex)==null) throw new IllegalArgumentException("unknown key: "+forceEIndex);
		
		DatabaseField nmField = relNameField ==null ? null : table.getField(relNameField);
		DatabaseField idField = relIdField == null ? null : table.getField(relIdField);
		
		if (nmField!=null && !(nmField instanceof ReferenceField)) throw new IllegalArgumentException(relNameField+" is not a reference field in table "+table.getName());
		if (idField!=null && !(idField instanceof ReferenceField)) throw new IllegalArgumentException(relIdField+" is not a reference field in table "+table.getName());
		
		String nmTable = nmField == null ? null : ((ReferenceField)nmField).getTargetTable();
		String idTable = idField == null ? null : ((ReferenceField)idField).getTargetTable();
		
		if (idTable!=null && nmTable!=null && !nmTable.equals(idTable)) throw new IllegalArgumentException(relNameField+" and "+relIdField+" in table "+table.getName()+" do not reference the same table: "+nmTable+" != "+idTable);
	
		
		String sql = "UPDATE "+table.getSQLName()+" as R "
					+ (forceRIndex!=null ? " force index ("+forceRIndex+") " : "")
					+ " JOIN "+aliasTable.getSQLName()+" as E "
					+ (forceEIndex!=null ? " force index ("+forceEIndex+") " : "")
					+ ((idField!=null) ? " ON R."+relIdField+" = E.source " : " ON R."+relNameField+" = E.source_name ")
					+ " SET ";
		
		if (nmField!=null) sql += " R."+relNameField+" = E.target_name";
		if (nmField!=null && idField!=null) sql += ", ";
		if (idField!=null) sql += " R."+relIdField+" = E.target"; 
		
		String where = scope == null ? null : " scope = "+scope.ordinal();
		
		return executeChunkedUpdate("resolveRedirects", table.getName()+"."+relNameField+"+"+relIdField, sql, where, aliasTable, "source", chunkFactor);
	}
	
	
	/**
	 * Builds id-references from name-references
	 */
	protected int buildIdLinks(DatabaseTable table, String relNameField, String relIdField, int chunkFactor) throws PersistenceException {
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
					+ " SET R."+relIdField+" = E."+targetIdField+" ";
		String where = " R."+relIdField+" IS NULL";
		
		return executeChunkedUpdate("buildIdLinks", table.getName()+"."+relNameField, sql, where, target, targetIdField, chunkFactor);
	}

	public void prepareImport() throws PersistenceException {
		//noop
	}

	public void finalizeImport() throws PersistenceException {
		flush();
	}
	
}
