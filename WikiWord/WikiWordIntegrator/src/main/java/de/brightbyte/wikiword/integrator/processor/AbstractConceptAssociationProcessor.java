package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;

public abstract class AbstractConceptAssociationProcessor extends AbstractProcessor<Association> implements ConceptAssociationProcessor {
	
	public void processAssociations(DataCursor<Association> cursor) throws PersistenceException {
		process(cursor);
	}
	
	protected void processEntry(Association e) throws PersistenceException {
		processAssociation(e);
	}

	protected abstract  void processAssociation(Association m) throws PersistenceException;

}
