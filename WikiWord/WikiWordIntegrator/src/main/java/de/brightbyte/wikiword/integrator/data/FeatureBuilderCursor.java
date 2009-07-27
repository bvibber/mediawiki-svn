package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class FeatureBuilderCursor<R> implements DataCursor<FeatureSet> {

	protected DataCursor<R> cursor;
	protected R prev;
	
	protected FeatureBuilder<R> mapping;
	
	public FeatureBuilderCursor(DataCursor<R> cursor,  FeatureBuilder<R> mapping) {
		if (cursor==null) throw new NullPointerException();
		if (mapping==null) throw new NullPointerException();
		
		this.cursor = cursor;
		this.mapping = mapping;
	}

	public void close() {
		cursor.close();
	}

	public FeatureSet next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		FeatureSet f = new DefaultFeatureSet();
		
		while (prev!=null) {
			mapping.addFeatures(prev, f);
			
			R last = prev; 
			prev = cursor.next();
			if (prev==null) break;
			
			if (!mapping.hasSameSubject(last, prev)) break;
		}
		
		return f;
	}

}
