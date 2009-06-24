package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class CollapsingMappingCandidateCursor implements DataCursor<MappingCandidates> {

	protected DataCursor<Association> cursor;
	protected Association prev;
	
	protected String foreignKeyField;
	protected String conceptKeyField;
	
	public CollapsingMappingCandidateCursor(DataCursor<Association> cursor, String foreignKeyField, String conceptKeyField) {
		if (cursor==null) throw new NullPointerException();
		if (foreignKeyField==null) throw new NullPointerException();
		if (conceptKeyField==null) throw new NullPointerException();
		
		this.cursor = cursor;
		this.foreignKeyField = foreignKeyField;
		this.conceptKeyField = conceptKeyField;
	}

	public void close() {
		cursor.close();
	}

	public MappingCandidates next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		FeatureSet s = prev.getSourceItem();
		Collection<FeatureSet> candidates = new ArrayList<FeatureSet>();
		
		while (true) {
			FeatureSet t = FeatureSets.merge(prev.getTargetItem(), prev.getProperties());
			
			while (true) {
				prev = cursor.next();
				if (prev==null) break;
				
				if (!prev.getSourceItem().overlaps(s, foreignKeyField)) break;
				if (!prev.getTargetItem().overlaps(t, conceptKeyField)) break;
				
				t = FeatureSets.merge(t, prev.getTargetItem(), prev.getProperties());
			}
			
			candidates.add(t);
			
			if (prev==null || !prev.getSourceItem().overlaps(s, foreignKeyField)) break;
			
			s = FeatureSets.merge(s, prev.getSourceItem());
		}
		
		return new MappingCandidates(s, candidates);
	}

}
