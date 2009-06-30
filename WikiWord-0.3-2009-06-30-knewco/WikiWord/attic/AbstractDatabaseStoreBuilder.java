package de.brightbyte.wikiword.store;

import java.sql.SQLException;
import java.text.MessageFormat;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;

import de.brightbyte.application.Agenda;
import de.brightbyte.db.BufferBasedInserter;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseAgendaPersistor;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.Inserter;
import de.brightbyte.db.InserterFactory;
import de.brightbyte.db.RelationTable;
import de.brightbyte.db.StatementBasedInserter;
import de.brightbyte.util.PersistenceException;
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
public abstract class AbstractDatabaseStoreBuilder 
{
	
	protected DatabaseAccess database;
	
	protected Inserter warningInserter;
	//protected Inserter logInserter;
	
	protected Agenda agenda;
	
	protected Map<String, Inserter> inserters = new HashMap<String, Inserter>();
	
	private boolean inStoreWarning = false; //recursion stop
	protected InserterFactory entityInserterFactory;
	protected InserterFactory relationInserterFactory;
	
	/*
	public AbstractDatabaseStoreBuilder(Corpus corpus, DatabaseConnectionInfo dbInfo, TweakSet tweaks) {
		this(corpus, new DatabaseSchema(corpus.getDbPrefix(), dbInfo, tweaks.getTweak("dbstore.backgroundFlushQueue", 4)), tweaks);
	}
	
	public AbstractDatabaseStoreBuilder(Corpus corpus, Connection db, TweakSet tweaks) {
		this(corpus, new DatabaseSchema(corpus.getDbPrefix(), db, tweaks.getTweak("dbstore.backgroundFlushQueue", 4)), tweaks);
	}
	
	public AbstractDatabaseStoreBuilder(Corpus corpus, DatabaseSchema db, TweakSet tweaks) {
		this(new WikiWordStore(corpus, db, tweaks));
	}
	*/
	
	public AbstractDatabaseStoreBuilder(DatabaseAccess database, TweakSet tweaks) throws SQLException {
		if (database==null) throw new NullPointerException();
		this.database = database;

		entityInserterFactory = tweaks.getTweak("dbstore.useEntityBuffer", Boolean.TRUE) ? BufferBasedInserter.factory : StatementBasedInserter.factory;
		relationInserterFactory = tweaks.getTweak("dbstore.useRelationBuffer", Boolean.TRUE) ? BufferBasedInserter.factory : StatementBasedInserter.factory;
		
		DatabaseTable warningTable = database.getTable("warning");
		warningTable.setInserterFactory(StatementBasedInserter.factory);
		addTable(warningTable, 0, 0);

		if (database instanceof DatabaseSchema) {
			((DatabaseSchema)database).setBufferScale(tweaks.getTweak("dbstore.insertionBufferFactor", 16));
		}
	}
	
	protected void addTable(String name,  int bufferLength, int bufferWidth) throws SQLException {
		addTable(database.getTable(name), bufferLength, bufferWidth);
	}
	protected void addTable(DatabaseTable table,  int bufferLength, int bufferWidth) throws SQLException {
		if (table.getInserterFactory()==null) {
			if (table instanceof RelationTable) table.setInserterFactory(relationInserterFactory);
			else table.setInserterFactory(entityInserterFactory);
		}
		
		table.setBufferDimensions(bufferLength, bufferWidth);
		inserters.put(table.getName(), table.getInserter());
	}

	protected String getSQLName(String name) {
		return getTable(name).getSQLName();
	}
	
	protected DatabaseTable getTable(String name) {
		return getInserter(name).getTable();
	}
	
	protected Inserter getInserter(String name) {
		Inserter inserter = inserters.get(name);
		if (inserter==null) throw new IllegalArgumentException("no inserter for table "+name);
		return inserter;
	}
	
	protected void log(String msg) {
		database.log(msg);
	}

	protected void trace(String msg) {
		database.trace(msg);
	}

	public void setLogLevel(int level) {
		if (database instanceof DatabaseSchema) {
			((DatabaseSchema)database).setLogLevel(level);
		}
	}

	public DatabaseAccess getDatabaseAccess() {
		return database;
	}

	
	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStoreBuilder#flush()
	 */
	public void flush() throws PersistenceException {
		try {
			//if (logInserter!=null) logInserter.flush();
			if (warningInserter!=null) warningInserter.flush();
			
			for (Inserter inserter: inserters.values()) {
				inserter.flush();
			}
			
			try {
				database.flush(); //probably redundant
			} catch (InterruptedException e) {
				throw new PersistenceException("interrupted while waiting for flusde.brightbyte.dbh to complete", e);
			}
		} catch (SQLException e) {
			throw new PersistenceException(e);
		} 
	}

	/*
	public void clearStatistics() throws PersistenceException {
		try {
			database.truncateTable("stats");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	*/
	
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
		
		for (Inserter inserter: inserters.values()) {
			inserter.close();
		}
		
		inserters.clear();

		try {
			database.close();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void open() throws PersistenceException {
		try {
			database.open();
		} catch (SQLException e) {
			throw new PersistenceException(e);
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
			if (warningInserter==null) warningInserter = getInserter("warning");
			
			problem = clipString(rcId, problem, 255, "problem name, resource #{0}", rcId);
			details = clipString(rcId, details, 1024, "problem details, resource #{0}", rcId);
			
			if (rcId<0) warningInserter.updateObject("resource", null);
			else warningInserter.updateInt("resource", rcId);

			warningInserter.updateString("timestamp", DatabaseWikiWordStore.timestampFormatter.format(new Date()));
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

	protected void deleteDataFrom(String idField, int rcId, String op, DatabaseTable rel, String field, DatabaseTable via) throws PersistenceException {
		String sql;
		
		if (via!=null) {
			sql = "DELETE FROM T";
			sql += " USING "+rel.getSQLName()+" AS T ";
			sql += " JOIN "+via.getSQLName()+" AS C";
			sql += " ON T."+field+" = C.id";
			sql += " WHERE C."+idField+" "+op+" "+rcId;
		}
		else {
			sql = "DELETE FROM "+rel.getSQLName();
			sql += " WHERE "+field+" "+op+" "+rcId;
		}
		
		long t = System.currentTimeMillis();
		int c = executeUpdate("deleteDataFrom", sql);
		trace("deleted "+c+" rows from "+rel.getName()+" where "+field+" "+op+" "+rcId+(via!=null?" via "+via.getName():"")+", took "+(System.currentTimeMillis()-t)/1000+" sec");
	}
	
	protected int executeUpdate(String name, String sql) throws PersistenceException {
		try {
			return database.executeUpdate(name, sql);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected abstract void deleteDataFrom(int rcId, String op) throws PersistenceException;

	public void deleteDataFrom(int rcId) throws PersistenceException {
		log("deleting data from "+rcId);
		deleteDataFrom(rcId, "=");
	}

	public void deleteDataAfter(int rcId) throws PersistenceException {
		log("deleting data from with id > "+rcId);
		deleteDataFrom(rcId, ">");
	}
	
	/**
	 * @see de.brightbyte.db.DatabaseSchema#dropTables(boolean)
	 */
	public void dropTables(boolean opt) throws PersistenceException {
		try {
			for (String t: inserters.keySet()) {
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
				database.createTable(t, opt);
			}
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
				DatabaseAgendaPersistor log = new DatabaseAgendaPersistor(database.getTable("log")); 
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

}
