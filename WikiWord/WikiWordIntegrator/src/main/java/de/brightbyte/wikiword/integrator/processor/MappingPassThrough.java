package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.store.MappingFeatureStoreBuilder;

public class MappingPassThrough implements MappingProcessor {

	public void processMappings(DataCursor<MappingCandidates> cursor,
			MappingFeatureStoreBuilder store) throws PersistenceException {
		
		MappingCandidates m;
		while ((m = cursor.next()) != null ) {
			for (FeatureSet f: m.getCandidates()) {
				store.storeMapping(m.getSubject(), f, f);
			}
		}

	}

}
