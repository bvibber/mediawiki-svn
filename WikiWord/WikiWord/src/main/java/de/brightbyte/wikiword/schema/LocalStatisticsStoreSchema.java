package de.brightbyte.wikiword.schema;

import java.sql.Connection;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.db.DatabaseField;
import de.brightbyte.db.EntityTable;
import de.brightbyte.db.KeyType;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;

public class LocalStatisticsStoreSchema extends StatisticsStoreSchema {
	
	protected EntityTable termTable;

	public LocalStatisticsStoreSchema(DatasetIdentifier dataset, Connection connection, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connection, false, tweaks, useFlushQueue );
		init(tweaks);
	}

	public LocalStatisticsStoreSchema(DatasetIdentifier dataset, DataSource connectionInfo, TweakSet tweaks, boolean useFlushQueue) throws SQLException {
		super(dataset, connectionInfo, false, tweaks, useFlushQueue);
		init(tweaks);
	}
	
	private void init(TweakSet tweaks) {
		termTable = new EntityTable(this, "term", getDefaultTableAttributes());
		termTable.addField( new DatabaseField(this, "rank", "INT", "AUTO_INCREMENT", true, KeyType.PRIMARY ) );
		termTable.addField( new DatabaseField(this, "term", getTextType(255), null, true, KeyType.UNIQUE ) );
		termTable.addField( new DatabaseField(this, "freq", "INT", null, true, KeyType.INDEX ) );
		termTable.addField( new DatabaseField(this, "random", "REAL UNSIGNED", null, true, KeyType.INDEX ) );
		termTable.setAutomaticField(null);
		addTable(termTable);
	}

	@Override
	public boolean isComplete() throws SQLException {
		if (!super.isComplete()) return false;
		
		//NOTE: term stats may have been skipped.
		//      they are not needed for most kinds of processing.
		/*
		if (!this.tableExists("term")) return false;

		String sql = "select count(*) from "+this.getSQLTableName("term");
		int c = ((Number)this.executeSingleValueQuery("isComplete", sql)).intValue();
		if (c==0) return false;
		*/
		
		return true;
	}
	
}
