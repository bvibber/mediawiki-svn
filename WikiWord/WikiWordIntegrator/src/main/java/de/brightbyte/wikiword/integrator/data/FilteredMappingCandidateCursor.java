package de.brightbyte.wikiword.integrator.data;

import java.util.Collection;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.filter.BestMappingCandidateSelector;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateFilter;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateFeatureScorer;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateScorer;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateSelector;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateSelectorFilter;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateThresholdFilter;

public class FilteredMappingCandidateCursor implements DataCursor<MappingCandidates> {

	protected DataCursor<MappingCandidates> cursor;
	protected MappingCandidateFilter filter;
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, String field) {
		this(cursor, new Record.Accessor<Integer>(field, Integer.class));
	}
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, String field, int threshold) {
		this(cursor,  new Record.Accessor<Integer>(field, Integer.class), threshold);
	}
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, PropertyAccessor<Record, ? extends Number> accessor) {
		this(cursor, new MappingCandidateFeatureScorer(accessor));
	}
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, PropertyAccessor<Record, ? extends Number> accessor, int threshold) {
		this(cursor, new MappingCandidateFeatureScorer(accessor), threshold);
	}
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, MappingCandidateScorer scorer) {
		this(cursor, new BestMappingCandidateSelector(scorer));
	}
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, MappingCandidateScorer scorer, int threshold) {
		this(cursor, new MappingCandidateThresholdFilter(scorer, threshold));
	}
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, MappingCandidateSelector selector) {
		this(cursor, new MappingCandidateSelectorFilter(selector));
	}
	
	public FilteredMappingCandidateCursor(DataCursor<MappingCandidates> cursor, MappingCandidateFilter filter) {
		if (filter==null) throw new NullPointerException();
		if (cursor==null) throw new NullPointerException();
		
		this.filter = filter;
		this.cursor = cursor;
	}

	public void close() {
		cursor.close();
	}

	public MappingCandidates next() throws PersistenceException {
		MappingCandidates m = null;
		while (true) {
			m = cursor.next();
			if (m==null) return null;

			Collection<ConceptEntityRecord> filtered = filter.filterCandidates(m);
			if (filtered==null || filtered.size()==0) continue;
			
			if (filtered!=m.getCandidates()) 
				m = new MappingCandidates(m.getSubject(), filtered);
			
			break;
		}
		
		return m;
	}

}
