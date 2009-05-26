package de.brightbyte.wikiword.integrator;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public interface MappingProcessor {
		public void processMappings(DataCursor<MappingCandidates> cursor, MappingStore store) throws PersistenceException;
}
