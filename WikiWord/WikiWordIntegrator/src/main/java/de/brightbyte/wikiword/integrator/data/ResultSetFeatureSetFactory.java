/**
 * 
 */
package de.brightbyte.wikiword.integrator.data;

import java.sql.ResultSet;

import de.brightbyte.db.DatabaseDataSet.Factory;

public class ResultSetFeatureSetFactory implements Factory<FeatureSet> {
	protected String[] fields;
	
	public ResultSetFeatureSetFactory(String[] fields) {
		super();
		this.fields = fields;
	}

	public FeatureSet newInstance(ResultSet row) throws Exception {
		FeatureSet f = new DefaultFeatureSet();
		
		for (String k: fields) {
			Object v = row.getObject(k);
			f.put(k, v);
		}
		
		return f;
	}

}