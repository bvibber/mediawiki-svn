package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class MappingCandidateCursor implements DataCursor<MappingCandidates> {

	protected DataCursor<Association> cursor;
	protected Association prev;
	
	public MappingCandidateCursor(DataCursor<Association> cursor) {
		if (cursor==null) throw new NullPointerException();
		
		this.cursor = cursor;
	}

	public void close() {
		cursor.close();
	}

	public MappingCandidates next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		ForeignEntityRecord fe = prev.getForeignEntity();
		Collection<ConceptEntityRecord> candidates = new ArrayList<ConceptEntityRecord>();
		candidates.add(prev.getConceptEntity());
		
		while (true) {
			prev = cursor.next();
			if (prev==null) break;
			
			if (!prev.getForeignEntity().getAuthority().equals( fe.getAuthority())) break;
			if (!prev.getForeignEntity().getID().equals( fe.getID())) break;

			candidates.add(prev.getConceptEntity());
		}
		
		return new MappingCandidates(fe, candidates);
	}

}
