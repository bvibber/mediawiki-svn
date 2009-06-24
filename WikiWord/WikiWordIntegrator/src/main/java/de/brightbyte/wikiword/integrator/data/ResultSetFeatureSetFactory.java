/**
 * 
 */
package de.brightbyte.wikiword.integrator.data;

import java.sql.ResultSet;

import de.brightbyte.db.DatabaseDataSet.Factory;
import de.brightbyte.util.CollectionUtils;

public class ResultSetFeatureSetFactory implements Factory<FeatureSet> {
	protected String[] fields;
	
	public ResultSetFeatureSetFactory(String... fields) {
		this.fields = CollectionUtils.toCleanArray(fields, String.class, false, false);
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