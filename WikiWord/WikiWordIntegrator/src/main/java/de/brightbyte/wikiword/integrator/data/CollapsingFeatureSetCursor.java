package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class CollapsingFeatureSetCursor implements DataCursor<FeatureSet> {

	protected DataCursor<FeatureSet> cursor;
	protected FeatureSet prev;
	
	protected String recordIdField;
	
	public CollapsingFeatureSetCursor(DataCursor<FeatureSet> cursor,  String sourceKeyField) {
		if (cursor==null) throw new NullPointerException();
		if (sourceKeyField==null) throw new NullPointerException();
		
		this.cursor = cursor;
		this.recordIdField = sourceKeyField;
	}

	public void close() {
		cursor.close();
	}

	public FeatureSet next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		FeatureSet a = prev;
		
		while (true) {
			prev = cursor.next();
			if (prev==null) break;
			
			if (!prev.overlaps(a, recordIdField)) break;
			a = FeatureSets.merge(a, prev);
		}
		
		return a;
	}

}
