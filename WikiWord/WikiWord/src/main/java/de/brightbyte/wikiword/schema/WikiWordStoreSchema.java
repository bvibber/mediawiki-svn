package de.brightbyte.wikiword.schema;

import static de.brightbyte.util.LogLevels.LOG_INFO;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseAgendaPersistor;
import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.db.ReferenceField;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.ExtractionRule;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.store.GroupNameTranslator;

public class WikiWordStoreSchema extends DatabaseSchema {
	
	public static class GroupStatsSpec {
		public final String table;
		public final String field;
		public final GroupNameTranslator translator;
		
		public GroupStatsSpec(final String table, final String field, final GroupNameTranslator translator) {
			super();
			this.table = table;
			this.field = field;
			this.translator = translator;
		}
	}

	protected static final GroupNameTranslator resourceTypeCodeTranslator = new GroupNameTranslator() {
		public String translate(String s) {
			int type = Integer.parseInt(s);
			return ResourceType.getType(type).name();
		}
	};
	
	protected final GroupNameTranslator conceptTypeCodeTranslator = new GroupNameTranslator() {
			public String translate(String s) throws PersistenceException {
				int type = Integer.parseInt(s);
				try {
					return getConceptType(type).getName();
				} catch (SQLException e) {
					throw new PersistenceException(e);
				}
			}

		};
		
	protected static final GroupNameTranslator extractionRulCodeTranslator = new GroupNameTranslator() {
			public String translate(String s) {
				int type = Integer.parseInt(s);
				return ExtractionRule.getRule(type).name();
			}
		};
	
	
	protected List<GroupStatsSpec> groupStats = new ArrayList<GroupStatsSpec>();
	
	protected EntityTable logTable;
	protected EntityTable warningTable;
	
	protected boolean useBinaryText = true;

	private DatasetIdentifier dataset;
	
	public WikiWordStoreSchema(DatasetIdentifier dataset, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset.getDbPrefix(), connection, useFlushQueue ? tweaks.getTweak("dbstore.backgroundFlushQueue", 4) : 0 ); //XXX: doc tweak!
		init(dataset, tweaks);
	}

	public WikiWordStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset.getDbPrefix(), connectionInfo, useFlushQueue ? tweaks.getTweak("dbstore.backgroundFlushQueue", 4) : 0); //XXX: doc tweak!
		init(dataset, tweaks);
	}
	
	private void init(DatasetIdentifier dataset, TweakSet tweaks) throws SQLException {
		this.setLogLevel(tweaks.getTweak("dbstore.logLevel", LOG_INFO));
		
		this.dataset = dataset;
		this.setExplainSQLThreashold(tweaks.getTweak("dbstore.explainSQLThreashold", 0));
		this.setSlowSQLThreashold(tweaks.getTweak("dbstore.slowSQLThreashold", 0));
		this.setTraceSQL(tweaks.getTweak("dbstore.traceSQL", false));
		
		this.setMaxStatementSize(tweaks.getTweak("dbstore.maxStatementSize", -1)); 
		
		this.setBufferScale(tweaks.getTweak("dbstore.insertionBufferFactor", 16));
		
		String sqlMode = tweaks.getTweak("dbstore.sqlMode", null);
		if (sqlMode!=null) this.setSqlMode(sqlMode);
		
		String dbengine = tweaks.getTweak("dbstore.engine", "MyISAM");
		String defaultTableAttributes = "ENGINE="+dbengine+" CHARSET utf8 COLLATE utf8_bin";
		defaultTableAttributes = tweaks.getTweak("dbstore.table.attributes", defaultTableAttributes);
		
		this.setDefaultTableAttributes(defaultTableAttributes);
		this.useBinaryText = tweaks.getTweak("dbstore.useBinaryText", true);

		logTable = DatabaseAgendaPersistor.makeTableSpec(this, "log");
		addTable(logTable);
		
		warningTable = new EntityTable(this, "warning", this.getDefaultTableAttributes());
		warningTable.addField( new DatabaseField(this, "id", "INT", "AUTO_INCREMENT", true, KeyType.PRIMARY ) );
		warningTable.addField( new DatabaseField(this, "timestamp", "CHAR(14)", null, true, null ) ); //TODO: which type in which db?
		warningTable.addField( new DatabaseField(this, "problem", getTextType(255), null, true, KeyType.INDEX ) ); 
		warningTable.addField( new DatabaseField(this, "details", getTextType(1024), null, false, null ) ); 
		warningTable.addField( new ReferenceField(this, "resource", "INT", null, false, KeyType.INDEX, "resource", "id", null ) );
		warningTable.setAutomaticField("id");
		addTable(warningTable);		
		
		groupStats.add( new GroupStatsSpec("warning", "problem", null));
	}
	
	public String getCollectionName() {
		return dataset.getCollection();
	}
	
	public String getTextType(int length) {
		//TODO: dialects
		//NOTE: for MySQL: VARBINARY != VARCHAR BINARY
		
		if (length<=256-1) return useBinaryText ? "VARBINARY("+length+")" : "VARCHAR("+length+")"; 
		else if (length<=256*4-2) return useBinaryText ? "VARBINARY("+length+")" : "VARCHAR("+length+")"; 
		else if (length<=256*256-2) return useBinaryText ? "BLOB" : "TEXT"; 
		else if (length<=256*256*256-3) return useBinaryText ? "MEDIUMBLOB" : "MEDIUMTEXT"; 
		else return useBinaryText ? "LONGBLOB" : "LONGTEXT";
	}

	public List<GroupStatsSpec> getGroupStatsSpecs() {
		return groupStats;
	}

	public ConceptType getConceptType(int type) throws SQLException {
		return ConceptType.canonicalConceptTypes.getType(type);
	}
	
	public void checkIdSanity(DatabaseTable t, String field) throws SQLException {
			DatabaseField r = (DatabaseField)t.getField(field);
			
			String sql = "SELECT * "
				+" FROM "+t.getSQLName()+" "
				+" WHERE "+r.getName()+" IS NULL"
				+" OR "+r.getName()+" = 0";
	
			Statement st = createStatement();
			try {
				info("checking id sanity of "+t.getName()+"."+field);
				long ts = System.currentTimeMillis();
				ResultSet res = st.executeQuery(sql);
				try {
					if (res.next()) {
						throw new SQLException("insane id found in "+t.getSQLName()+"."+r.getName()+"; try "+sql);
						//TODO: list all? some?
					}
				}
				finally {
					res.close();
				}
	
				info("sanity ok for "+t.getName()+"."+field+", took "+(System.currentTimeMillis()-ts)/1000+" sec");
			}
			finally {
				st.close();
			}
	}

	public void checkReferentialIntegrity(DatabaseTable t, String field, boolean nullable) throws SQLException {
			ReferenceField r = (ReferenceField)t.getField(field);
			
			Statement st = createStatement();
			try {
				String sql;
				long ts;
				ResultSet res;
				
				if (!nullable) {
					sql = "SELECT * "
					+" FROM "+t.getSQLName()+" "
					+" WHERE "+r.getName()+" IS NULL"
					+" OR "+r.getName()+" = 0";
	
					info("checking reference sanity of "+t.getName()+"."+field);
					ts = System.currentTimeMillis();
					res = st.executeQuery(sql);
					try {
						if (res.next()) {
							throw new SQLException("insane references found in "+t.getSQLName()+"."+r.getName()+"; try "+sql);
							//TODO: list all? some?
						}
					}
					finally {
						res.close();
					}
	
					info("sanity ok for "+t.getName()+"."+field+", took "+(System.currentTimeMillis()-ts)/1000+" sec");
				}
	
				DatabaseTable tt = getTable(r.getTargetTable());
				DatabaseField tf = tt.getField(r.getTargetField());
				
				sql = "SELECT * "
							+" FROM "+t.getSQLName()+" as R"
							+" LEFT JOIN "+tt.getSQLName()+" as T"
							+" ON R."+r.getName()+" = T."+tf.getName()+" "
							+" WHERE T."+tf.getName()+" IS NULL"
							+" AND R."+r.getName()+" IS NOT NULL";
	
				
				info("checking referential integrity of "+t.getName()+"."+field);
				ts = System.currentTimeMillis();
				res = st.executeQuery(sql);
				try {
					if (res.next()) {
						throw new SQLException("orphan references found in "+t.getSQLName()+"."+r.getName()+"; try "+sql);
						//TODO: list all? some?
					}
				}
				finally {
					res.close();
				}
				
				info("integrity ok for "+t.getName()+"."+field+", took "+(System.currentTimeMillis()-ts)/1000+" sec");
			}
			finally {
				st.close();
			}
		
	}

	public void checkReferencePairConsistency(DatabaseTable t, String idField, String nameField) throws SQLException {		
			ReferenceField r = (ReferenceField)t.getField(idField);
			ReferenceField n = (ReferenceField)t.getField(nameField);
			
			DatabaseTable tt = getTable(r.getTargetTable());
			DatabaseField tf = tt.getField(r.getTargetField());
			DatabaseField tn = tt.getField(n.getTargetField());
			
			String sql = "SELECT * "
						+" FROM "+t.getSQLName()+" as R"
						+" JOIN "+tt.getSQLName()+" as T ON R."+r.getName()+" = T."+tf.getName()+" "
						+" WHERE R."+n.getName()+" != T."+tn.getName()
						+" AND R."+r.getName()+" IS NOT NULL"
						+" AND R."+n.getName()+" IS NOT NULL";
			
			info("checking reference pair consistency "+t.getName()+"."+idField+" with "+nameField);
	
			long ts = System.currentTimeMillis();
			Statement st = createStatement();
			try {
				ResultSet res = st.executeQuery(sql);
				try {
					if (res.next()) {
						throw new SQLException("inconsistance references pair found in "+t.getSQLName()+"."+r.getName()+" + "+n.getName()+"; try "+sql);
						//TODO: list all? some?
					}
				}
				finally {
					res.close();
				}
			}
			finally {
				st.close();
			}
			
			info("integrity ok for "+t.getName()+"."+idField+" with "+nameField+", took "+(System.currentTimeMillis()-ts)/1000+" sec");
	}


	public void checkConsistency() throws SQLException {
		//noop
	}

	public DatasetIdentifier getDataset() {
		return dataset;
	}

	public boolean isComplete() throws SQLException {
		if (!tableExists("warning")) return false;
		if (!tableExists("log")) return false;
		return true;
	}
	
	
	public String encodeSet(ConceptType[] values) {
		int[] vv = new int[values.length];
		for (int i=0; i<values.length; i++) vv[i] = values[i].getCode();
		return encodeSet(vv);
	}
	
	public String encodeSet(ResourceType[] values) {
		int[] vv = new int[values.length];
		for (int i=0; i<values.length; i++) vv[i] = values[i].getCode();
		return encodeSet(vv);
	}
	
	public String encodeSet(Namespace[] values) {
		int[] vv = new int[values.length];
		for (int i=0; i<values.length; i++) vv[i] = values[i].getNumber();
		return encodeSet(vv);
	}
	
}
