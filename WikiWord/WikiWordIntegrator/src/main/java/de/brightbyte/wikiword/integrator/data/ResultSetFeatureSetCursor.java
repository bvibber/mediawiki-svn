package de.brightbyte.wikiword.integrator.data;

import java.sql.ResultSet;

import de.brightbyte.db.DatabaseDataSet;

public class ResultSetFeatureSetCursor extends DatabaseDataSet.Cursor<FeatureSet> {

	public ResultSetFeatureSetCursor(ResultSet resultSet, String[] fields) {
		super(resultSet, new ResultSetFeatureSetFactory(fields));
	}

}
