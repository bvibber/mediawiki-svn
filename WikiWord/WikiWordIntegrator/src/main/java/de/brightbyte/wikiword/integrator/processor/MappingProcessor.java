package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.store.MappingFeatureStoreBuilder;

public interface MappingProcessor {
		public void processMappings(DataCursor<MappingCandidates> cursor, MappingFeatureStoreBuilder store) throws PersistenceException;
}
