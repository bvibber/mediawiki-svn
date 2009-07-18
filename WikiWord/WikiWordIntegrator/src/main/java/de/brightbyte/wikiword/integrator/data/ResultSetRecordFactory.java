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

public class ResultSetRecordFactory implements Factory<Record> {
	protected String[] fields;
	
	public ResultSetRecordFactory(String... fields) {
		this.fields = CollectionUtils.toCleanArray(fields, String.class, false, false);
	}

	public Record newInstance(ResultSet row) throws Exception {
		Record rec = new DefaultRecord();
		
		for (String k: fields) {
			Object v = row.getObject(k);

			//XXX: ugly hack, use type hinting!
			if (v instanceof byte[] || v instanceof char[] || v instanceof Clob || v instanceof Blob) {
				v = DatabaseUtil.asString(v, "UTF-8");
			}
			
			rec.add(k, v);
		}
		
		return rec;
	}

}