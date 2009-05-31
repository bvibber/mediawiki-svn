package de.brightbyte.wikiword.integrator.data;

import java.util.List;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class AssemblingFeatureSetCursor implements DataCursor<FeatureSet> {

	protected DataCursor<FeatureSet> cursor;
	protected FeatureSet prev;
	
	protected String recordIdField;
	protected String propertyNameField;
	protected String propertyValueField;
	
	public AssemblingFeatureSetCursor(DataCursor<FeatureSet> cursor,  String recordIdField, String propertyNameField, String propertyValueField) {
		if (cursor==null) throw new NullPointerException();
		if (recordIdField==null) throw new NullPointerException();
		if (propertyNameField==null) throw new NullPointerException();
		if (propertyValueField==null) throw new NullPointerException();
		
		this.cursor = cursor;
		this.recordIdField = recordIdField;
		this.propertyNameField = propertyNameField;
		this.propertyValueField = propertyValueField;
	}

	public void close() {
		cursor.close();
	}

	public FeatureSet next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		FeatureSet a = new DefaultFeatureSet();;
		a.putAll(recordIdField, prev.get(recordIdField));
		
		while (prev!=null) {
			List<Object> keys = prev.get(propertyNameField);
			List<Object> values = prev.get(propertyValueField);

			for (Object k: keys) {
				a.putAll(k.toString(), values);
			}
			
			prev = cursor.next();
			if (prev==null || !prev.overlaps(a, recordIdField)) break;
		}
		
		return a;
	}

}
