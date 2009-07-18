package de.brightbyte.wikiword.integrator.data;

import java.sql.ResultSet;
import java.sql.SQLException;

import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseUtil;

public class ResultSetRecordCursor extends DatabaseDataSet.Cursor<Record> {

	public ResultSetRecordCursor(ResultSet resultSet) throws SQLException {
		this(resultSet, null);
	}
	
	public ResultSetRecordCursor(ResultSet resultSet, String[] fields) throws SQLException {
		super(resultSet, new ResultSetRecordFactory(fields == null ? DatabaseUtil.getFieldNames(resultSet): fields));
	}

}
