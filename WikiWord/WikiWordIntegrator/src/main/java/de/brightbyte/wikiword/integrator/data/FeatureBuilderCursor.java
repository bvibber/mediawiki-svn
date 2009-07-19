package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class FeatureBuilderCursor<R> implements DataCursor<FeatureSet> {

	protected DataCursor<R> cursor;
	protected R prev;
	
	protected String recordIdField;
	protected FeatureBuilder<R> mapping;
	
	public FeatureBuilderCursor(DataCursor<R> cursor,  FeatureBuilder<R> mapping, String recordIdField) {
		if (cursor==null) throw new NullPointerException();
		if (mapping==null) throw new NullPointerException();
		
		this.cursor = cursor;
		this.mapping = mapping;
		this.recordIdField = recordIdField;
	}

	public void close() {
		cursor.close();
	}

	public FeatureSet next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		FeatureSet f = new DefaultFeatureSet();
		Object id = null;
		
		if (recordIdField!=null) {
			id = mapping.requireValue(prev, recordIdField, Object.class);
			if (id==null) throw new PersistenceException("id field "+id+" must have non-null value!");
		}
			
		while (prev!=null) {
			mapping.addFeatures(prev, f);
			
			prev = cursor.next();
			if (prev==null) break;
			
			if (recordIdField!=null) {
				Object nextId = mapping.requireValue(prev, recordIdField, Object.class);
				if (nextId==null) throw new PersistenceException("id field "+id+" must have non-null value!");
				
				if (!nextId.equals(id)) break;
			} else {
				break;
			}
		}
		
		return f;
	}

}
