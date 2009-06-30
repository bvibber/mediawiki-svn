/**
 * 
 */
package de.brightbyte.wikiword.integrator.data;

import java.sql.Blob;
import java.sql.Clob;
import java.sql.ResultSet;

import de.brightbyte.db.DatabaseUtil;
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

			//XXX: ugly hack, use type hinting!
			if (v instanceof byte[] || v instanceof char[] || v instanceof Clob || v instanceof Blob) {
				v = DatabaseUtil.asString(v, "UTF-8");
			}
			
			f.put(k, v);
		}
		
		return f;
	}

}