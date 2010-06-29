package de.brightbyte.wikiword.store.builder;

import java.util.Date;

import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ExtractionRule;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.schema.AliasScope;

/**
 * A WikiStoreBuilder is something that information extracted from a wiki
 * (generally by a WikiTextAnalyzer) may be written to. It may be backed by
 * a RDBMS, or some other way of storing the data. 
 */
public interface LocalConceptStoreBuilder extends WikiWordConceptStoreBuilder<LocalConcept>, IncrementalStoreBuilder, ConceptBasedStoreBuilder  {
	
	public abstract void storeDefinition(int rcId, int conceptId, String definition)
			throws PersistenceException;

	public abstract int storeResource(int pageId, int revId, String name, ResourceType ptype,
			Date time) throws PersistenceException;

	public abstract int storeResourceAbout(int pageId, int revId, String name, ResourceType ptype,
			Date time, int concept, String conceptName) throws PersistenceException;

	public abstract int storeConcept(int rcId, String name, ConceptType ctype)
			throws PersistenceException;

	public abstract void storeLink(int rcId, int anchorId, String anchorName, 
			String term, String targetName, ExtractionRule rule)
			throws PersistenceException;

	public abstract void storeReference(int rcId, String term, int targetId, String targetName, 
			ExtractionRule rule) throws PersistenceException;

	public abstract void storeSection(int rcId, String name, String page)
			throws PersistenceException;

	public abstract void storeConceptBroader(int rcId, 
			String narrowName, String broadName, ExtractionRule rule)
			throws PersistenceException;

	public abstract void storeConceptBroader(int rcId, int narrowId,
			String narrowName, String broadName, ExtractionRule rule)
			throws PersistenceException;

	public abstract void storeConceptAlias(int rcId, int source,
			String sourceName, int target, String targetName, AliasScope scope)
			throws PersistenceException;

	/* returns concept ID, of known; -1 otherwise */
	public abstract int storeAbout(int resource, String rcName, String conceptName)
		throws PersistenceException;

	/* returns concept ID, of known; -1 otherwise */
	public abstract int storeAbout(int resource, String rcName, int concept, String conceptName)
			throws PersistenceException;

	//public abstract void storeConceptReference(int rcId, int source,
	//		String sourceName, String target) throws PersistenceException;

	public abstract void storeLanguageLink(int rcId, int concept,
			String conceptName, String lang, String target) throws PersistenceException;

	//public abstract void storeLogPoint(int status, int rcId, String resource) throws SQLException;

	//public abstract void checkConsistency() throws PersistenceException;

	public abstract void deleteDataFrom(int rcId) throws PersistenceException;

	public abstract void deleteDataAfter(int rcId, boolean inclusive) throws PersistenceException;
	

	//public abstract Agenda getAgenda() throws PersistenceException;

	//public abstract void optimize() throws PersistenceException;

	//public abstract void buildStatistics() throws PersistenceException;

	//public abstract int getNumberOfWarnings() throws PersistenceException;
	
	//public LogPoint getLastLogPoint() throws SQLException;
	
	public void finishSections() throws PersistenceException;
	public void finishBadLinks() throws PersistenceException;
	public void finishMissingConcepts() throws PersistenceException;
	public void finishRelations() throws PersistenceException;
	public void finishMeanings() throws PersistenceException;
	//public void finishConceptInfo() throws PersistenceException;
	public void finishFinish() throws PersistenceException;

	public Corpus getCorpus();

	public void resetTermsForUnknownConcepts() throws PersistenceException;
	public DataSet<LocalConcept> listUnknownConcepts() throws PersistenceException;
	public int processUnknownConcepts(CursorProcessor<LocalConcept> processor) throws PersistenceException;

	public TextStoreBuilder getTextStoreBuilder() throws PersistenceException;
	public LocalPropertyStoreBuilder getPropertyStoreBuilder() throws PersistenceException;
	
}