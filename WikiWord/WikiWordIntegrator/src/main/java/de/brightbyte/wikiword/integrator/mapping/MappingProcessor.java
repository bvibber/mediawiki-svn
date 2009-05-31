package de.brightbyte.wikiword.integrator.mapping;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.store.MappingStore;

public interface MappingProcessor {
		public void processMappings(DataCursor<MappingCandidates> cursor, MappingStore store) throws PersistenceException;
}
