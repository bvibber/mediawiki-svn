package de.brightbyte.wikiword.integrator.data;

import java.util.Iterator;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class MappingAsAssociationsCursor implements DataCursor<Association> {
	
	protected DataCursor<MappingCandidates> source;
	protected MappingCandidates current;
	protected Iterator<ConceptEntityRecord> state;

	public void close() {
		source.close();
	}

	protected void finalize() {
		close();
	}

	public Association next() throws PersistenceException {
			while (current==null) {
				current = source.next();
				if (current==null) return null;
				
				state = current.getCandidates().iterator();
				if (!state.hasNext()) current = null;
			}

			ForeignEntityRecord subj = current.getSubject();
			ConceptEntityRecord obj = state.next();
			
			if (!state.hasNext()) current = null;
			
			return new GenericAssociation(subj, obj, obj);
	}

}
