package de.brightbyte.wikiword.model;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;

public interface ConceptOutput {

	public void writeLocalConcept(LocalConcept concept) throws PersistenceException;
	public void writeConcepts(DataSet<? extends WikiWordConcept> concepts) throws PersistenceException;
	public void writeConcept(WikiWordConcept concept) throws PersistenceException;
	public void writeGlobalConcept(GlobalConcept concept) throws PersistenceException;
	
	public void writeConceptReference(WikiWordConceptReference<? extends WikiWordConcept> concept) throws PersistenceException;
	public void writeConceptReferences(DataSet<? extends WikiWordConceptReference<? extends WikiWordConcept>> concepts) throws PersistenceException;

	public void flush() throws PersistenceException;
	public void close() throws PersistenceException;

}
