package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.Association;

public interface ConceptAssociationProcessor extends WikiWordProcessor {
		public void processAssociations(DataCursor<Association> cursor) throws PersistenceException;
}
