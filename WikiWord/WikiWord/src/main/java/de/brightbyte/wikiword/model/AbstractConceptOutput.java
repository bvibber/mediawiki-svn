package de.brightbyte.wikiword.model;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;

public abstract class AbstractConceptOutput implements ConceptOutput {

	public void writeConcept(WikiWordConcept concept) throws PersistenceException {
		if (concept instanceof LocalConcept) {
			writeLocalConcept((LocalConcept)concept);
		}
		else {
			writeGlobalConcept((GlobalConcept)concept);			
		}
	}
	
	public void writeConcepts(DataSet<? extends WikiWordConcept> concepts) throws PersistenceException {
		DataCursor<? extends WikiWordConcept> cursor = concepts.cursor();
		WikiWordConcept concept;
		while ((concept = cursor.next()) != null) {
			writeConcept(concept);
		}
	}

	public void writeConceptReferences(DataSet<? extends WikiWordConceptReference<? extends WikiWordConcept>> references) throws PersistenceException {
		DataCursor<? extends WikiWordConceptReference<? extends WikiWordConcept>> cursor = references.cursor();
		WikiWordConceptReference<? extends WikiWordConcept> r;
		while ((r = cursor.next()) != null) {
			writeConceptReference(r);
		}
	}
	
}
