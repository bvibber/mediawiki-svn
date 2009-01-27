package de.brightbyte.wikiword.model;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.rdf.RdfOutput.Relation;

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
	
}
