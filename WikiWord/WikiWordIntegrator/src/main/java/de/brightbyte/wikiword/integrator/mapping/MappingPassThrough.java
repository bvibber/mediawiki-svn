package de.brightbyte.wikiword.integrator.mapping;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.store.MappingFeatureStore;

public class MappingPassThrough implements MappingProcessor {

	public void processMappings(DataCursor<MappingCandidates> cursor,
			MappingFeatureStore store) throws PersistenceException {
		
		MappingCandidates m;
		while ((m = cursor.next()) != null ) {
			for (FeatureSet f: m.getCandidates()) {
				store.storeMapping(m.getSubject(), f, f);
			}
		}

	}

}
