package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class AggregatingAssociationCursor implements DataCursor<Association> {

	protected DataCursor<Association> cursor;
	protected Association prev;
	
	protected String sourceKeyField;
	protected String targetKeyField;
	
	public AggregatingAssociationCursor(DataCursor<Association> cursor,  String sourceKeyField, String targetKeyField) {
		if (cursor==null) throw new NullPointerException();
		if (sourceKeyField==null) throw new NullPointerException();
		if (targetKeyField==null) throw new NullPointerException();
		
		this.cursor = cursor;
		this.sourceKeyField = sourceKeyField;
		this.targetKeyField = targetKeyField;
	}

	public void close() {
		cursor.close();
	}

	public Association next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		Association a = prev;
		
		while (true) {
			prev = cursor.next();
			if (prev==null) break;
			
			if (!prev.getSourceItem().overlaps(a.getSourceItem(), sourceKeyField)) break;
			if (!prev.getTargetItem().overlaps(a.getTargetItem(), targetKeyField)) break;
			
			a = Association.merge(a, prev);
		}
		
		return a;
	}

}
