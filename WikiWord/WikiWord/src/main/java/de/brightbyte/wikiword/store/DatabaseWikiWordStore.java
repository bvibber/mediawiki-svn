package de.brightbyte.wikiword.store;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.TimeZone;

import javax.sql.DataSource;

import de.brightbyte.application.Agenda;
import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.ChunkedQueryDataSet;
import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.StatementBasedInserter;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.Processor;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;

public class DatabaseWikiWordStore implements WikiWordStore {
	
	public static final SimpleDateFormat timestampFormatter;
	
	static {
		timestampFormatter = new SimpleDateFormat( "yyyyMMddHHmmss" );
		timestampFormatter.setTimeZone(TimeZone.getTimeZone("GMT"));
	}
	
	protected WikiWordStoreSchema database;
	protected EntityTable warningTable;
	protected TweakSet tweaks;
	protected int queryChunkSize;
	
	public DatabaseWikiWordStore(DatasetIdentifier dataset, DataSource dataSource, TweakSet tweaks) throws SQLException {
		this(new WikiWordStoreSchema(dataset, dataSource, tweaks, true), tweaks);
	}
	
	public DatabaseWikiWordStore(DatasetIdentifier dataset, Connection conn, TweakSet tweaks) throws SQLException {
		this(new WikiWordStoreSchema(dataset, conn, tweaks, true), tweaks);
	}
	
	public DatabaseWikiWordStore(WikiWordStoreSchema database, TweakSet tweaks) throws SQLException {
		if (database==null) throw new NullPointerException();

		this.database = database;
		this.tweaks = tweaks;
		
		queryChunkSize = tweaks.getTweak("dbstore.queryChunkSize", 100000);

		warningTable = (EntityTable)database.getTable("warning");
		warningTable.setInserterFactory(StatementBasedInserter.factory);
	}
	
	public DatasetIdentifier getDatasetIdentifier() {
		return database.getDataset();
	}

	/**
	 * @see de.brightbyte.db.DatabaseSchema#getTable(String)
	 */
	protected DatabaseTable getTable(String name) {
		return database.getTable(name);
	}
	
	protected String getSQLName(String name) {
		return getTable(name).getSQLName();
	}
	
	/**
	 * @see de.brightbyte.db.DatabaseSchema#close()
	 * @see de.brightbyte.wikiword.store.LocalConceptStore#close()
	 */
	public void close(boolean flush) throws PersistenceException {
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
	
	
	protected Map<String, ? extends Number> getTableGroupStats(DatabaseTable t, String groupby, GroupNameTranslator translator) throws PersistenceException {
		try {
			Map<String, Integer> groups = database.getGroupSizes(t.getName(), groupby);
			
			Map<String, Integer> m;
			m = new HashMap<String, Integer>(groups.size());
			for (Map.Entry<String, Integer> e : groups.entrySet()) {
				String v = e.getKey();
				String s = v.toString();
				if (translator!=null) s += " ("+translator.translate(v)+")";
				m.put(t.getName()+": " + groupby + " = " + s, e.getValue());
			}
			
			return m;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}


	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStore#dumpTableStats(de.brightbyte.io.Output)
	 */
	public void dumpTableStats(Output out) throws PersistenceException {
		dumpStats(getTableStats(), out);
	}

	/**
	 * @see de.brightbyte.wikiword.store.LocalConceptStore#getTableStats()
	 */
	public Map<String, ? extends Number> getTableStats() throws PersistenceException {
		try {
			Map<String, Number> stats = new HashMap<String, Number>();
			
			for (DatabaseTable t: database.getTables()) {
				stats.put(t.getName(), database.getTableSize(t.getName()));
			}
			
			for (WikiWordStoreSchema.GroupStatsSpec g: database.getGroupStatsSpecs()) {
				stats.putAll( getTableGroupStats(database.getTable(g.table), g.field, g.translator) );
			}
					
			return stats;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected static void dumpStats(Map<String, ? extends Number> stats, Output out) {
		List<String> keys = new ArrayList<String>(stats.keySet());
		Collections.sort(keys);
		
		for (String n: keys) {
			Number v = stats.get(n);
			out.println("* "+n+" "+v);
		}
	}

	/**
	 * @see de.brightbyte.wikiword.LocalConceptQuerior#checkConsistency()
	 */
	public void checkConsistency() throws PersistenceException {
		try {
			database.checkConsistency();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected ResultSet executeQuery(String name, String sql) throws PersistenceException {
		try {
			return database.executeQuery(name, sql);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	protected ResultSet executeBigQuery(String name, String sql) throws PersistenceException {
		try {
			return database.executeBigQuery(name, sql);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	/*
	protected ResultSet executeBigQuery(String name, String sql) throws PersistenceException {
		try {
			return database.executeBigQuery(name, sql);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	*/
	
	protected Agenda getAgenda() throws PersistenceException {
		return null;
	}
	
	protected Agenda createAgenda() throws PersistenceException {
		return null;
	}

	public <T>DataSet<T> executeChunkedQuery(String context, String name, String sql, String where, String rest, DatabaseTable chunkTable, String chunkField, int factor, DatabaseDataSet.Factory<T> factory) throws PersistenceException {
		
		DatabaseAccess.SimpleChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(getDatabaseAccess(), context, name, sql, where, rest, chunkTable, chunkField);
		return executeChunkedQuery(query, factor, factory);
	}
	
	public <T>DataSet<T> executeChunkedQuery(DatabaseAccess.ChunkedQuery query, int factor, DatabaseDataSet.Factory<T> factory) throws PersistenceException {
		try {
			int chunkSize = queryChunkSize / factor;
			DataSet<T> ds = new ChunkedQueryDataSet<T>(getDatabaseAccess(), factory, query, chunkSize);
			return ds;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public int executeChunkedQuery(String context, String name, String sql, String where, String rest, DatabaseTable chunkTable, String chunkField, int factor, Processor<ResultSet> processor) throws PersistenceException {
		DatabaseAccess.SimpleChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(getDatabaseAccess(), context, name, sql, where, rest, chunkTable, chunkField);
		return executeChunkedQuery(query, factor, processor);
	}
	
	public int executeChunkedQuery(DatabaseAccess.ChunkedQuery query, int factor, Processor<ResultSet> processor) throws PersistenceException {
		try {
			int chunk = queryChunkSize / factor;
			return database.executeChunkedQuery(query, chunk, getAgenda(), processor);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public <T>int executeChunkedQuery(String context, String name, String sql, String where, String rest, DatabaseTable chunkTable, String chunkField, int factor, final DatabaseDataSet.Factory<T> factory, final CursorProcessor<T> processor) throws PersistenceException {
		DatabaseAccess.SimpleChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(getDatabaseAccess(), context, name, sql, where, rest, chunkTable, chunkField);
		return executeChunkedQuery(query, factor, factory, processor);
	}
		
	public <T>int executeChunkedQuery(DatabaseAccess.ChunkedQuery query, int factor, final DatabaseDataSet.Factory<T> factory, final CursorProcessor<T> processor) throws PersistenceException {
		Processor<ResultSet> p = new Processor<ResultSet>() {
			public void process(ResultSet data) throws Exception {
				DatabaseDataSet.Cursor<T> c = new DatabaseDataSet.Cursor<T>(data, factory);
				processor.process(c);
			}
		};
		
		return executeChunkedQuery(query, factor, p);
	}
	
	protected Statement createStatement() throws PersistenceException {
		try {
			return database.createStatement();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected Statement createStatement(int resultSetType, int resultSetConcurrency, int resultSetHoldability) throws PersistenceException {
		try {
			return database.createStatement(resultSetType, resultSetConcurrency, resultSetHoldability);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected Statement createStatement(int resultSetType, int resultSetConcurrency) throws PersistenceException {
		try {
			return database.createStatement(resultSetType, resultSetConcurrency);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	protected void log(String msg) {
		database.info(msg);
	}

	protected void trace(String msg) {
		database.trace(msg);
	}

	public void setLogLevel(int level) {
		database.setLogLevel(level);
	}

	public DatabaseAccess getDatabaseAccess() {
		return database;
	}

	/*
	public boolean isComplete(String task) throws PersistenceException {
		try {
			String sql = "SELECT id FROM "+database.getSQLTableName("log")+" " +
					"WHERE level = 0 AND task = " + database.quoteString(task) +
					"ORDER BY id DESC LIMIT 1";
			Integer id = asInt( database.executeSingleValueQuery("last agenda record", sql) );
			return id != null;
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}		
	}
	*/
	
	public int getNumberOfWarnings() throws PersistenceException {
		try {
			return database.getTableSize("warning");
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public final boolean isComplete() throws PersistenceException {
		try {
			return database.isComplete();
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

}
