package de.brightbyte.wikiword.integrator;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class MappingPassThrough implements MappingProcessor {

	public void processMappings(DataCursor<MappingCandidates> cursor,
			MappingStore store) throws PersistenceException {
		
		MappingCandidates m;
		while ((m = cursor.next()) != null ) {
			for (FeatureSet f: m.getCandidates()) {
				store.storeMapping(m.getSubject(), f, f);
			}
		}

	}

}
