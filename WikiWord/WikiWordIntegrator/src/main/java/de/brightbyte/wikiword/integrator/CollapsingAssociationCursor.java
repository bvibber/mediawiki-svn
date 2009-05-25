package de.brightbyte.wikiword.integrator;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class CollapsingAssociationCursor implements DataCursor<Association> {

	protected DataCursor<Association> cursor;
	protected Association prev;
	
	protected String sourceKeyField;
	protected String targetKeyField;
	
	public CollapsingAssociationCursor(String sourceKeyField, String targetKeyField) {
		if (sourceKeyField==null) throw new NullPointerException();
		if (targetKeyField==null) throw new NullPointerException();
		
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
