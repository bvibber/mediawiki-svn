package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;

public abstract class AbstractConceptMappingProcessor extends AbstractProcessor<MappingCandidates> implements ConceptMappingProcessor {
	
	public void processMappings(DataCursor<MappingCandidates> cursor) throws PersistenceException {
		process(cursor);
	}
	
	protected void processEntry(MappingCandidates e) throws PersistenceException {
		processMappingCandidates(e);
	}

	protected abstract  void processMappingCandidates(MappingCandidates m) throws PersistenceException;

}
