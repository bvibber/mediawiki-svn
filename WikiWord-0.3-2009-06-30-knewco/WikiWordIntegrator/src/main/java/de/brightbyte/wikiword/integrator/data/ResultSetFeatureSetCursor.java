package de.brightbyte.wikiword.integrator.data;

import java.sql.ResultSet;
import java.sql.SQLException;

import de.brightbyte.db.DatabaseDataSet;
import de.brightbyte.db.DatabaseUtil;

public class ResultSetFeatureSetCursor extends DatabaseDataSet.Cursor<FeatureSet> {

	public ResultSetFeatureSetCursor(ResultSet resultSet) throws SQLException {
		this(resultSet, null);
	}
	
	public ResultSetFeatureSetCursor(ResultSet resultSet, String[] fields) throws SQLException {
		super(resultSet, new ResultSetFeatureSetFactory(fields == null ? DatabaseUtil.getFieldNames(resultSet): fields));
	}

}
