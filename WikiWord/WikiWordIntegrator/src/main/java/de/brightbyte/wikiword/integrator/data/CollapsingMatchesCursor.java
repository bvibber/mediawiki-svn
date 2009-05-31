package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class CollapsingMatchesCursor implements DataCursor<MappingCandidates> {

	protected DataCursor<Association> cursor;
	protected Association prev;
	
	protected String sourceKeyField;
	protected String targetKeyField;
	
	public CollapsingMatchesCursor(DataCursor<Association> cursor, String sourceKeyField, String targetKeyField) {
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
				
				if (!prev.getSourceItem().overlaps(s, sourceKeyField)) break;
				if (!prev.getTargetItem().overlaps(t, targetKeyField)) break;
				
				t = FeatureSets.merge(t, prev.getTargetItem(), prev.getProperties());
			}
			
			candidates.add(t);
			
			if (prev==null || !prev.getSourceItem().overlaps(s, sourceKeyField)) break;
			
			s = FeatureSets.merge(s, prev.getSourceItem());
		}
		
		return new MappingCandidates(s, candidates);
	}

}
