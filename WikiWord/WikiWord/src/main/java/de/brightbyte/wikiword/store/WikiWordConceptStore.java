package de.brightbyte.wikiword.store;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.model.WikiWordConcept;


public interface WikiWordConceptStore<T extends WikiWordConcept> extends WikiWordConceptStoreBase {
	
	public static class ConceptQuerySpec {
		private boolean includeRelations;
		private boolean includeStatistics;
		private boolean includeResource;
		private boolean includeTerms;
		private boolean includeDefinition;
		private ConceptType requireType;
		private int limit;
		
		public boolean getIncludeRelations() {
			return includeRelations;
		}
		public void setIncludeRelations(boolean includeRelations) {
			this.includeRelations = includeRelations;
		}
		
		public ConceptType getRequireType() {
			return requireType;
		}
		
		public void setRequireType(ConceptType requireType) {
			this.requireType = requireType;
		}
		
		public boolean getIncludeStatistics() {
			return includeStatistics;
		}
		
		public void setIncludeStatistics(boolean includeStatistics) {
			this.includeStatistics = includeStatistics;
		}
		
		public boolean getIncludeDefinition() {
			return includeDefinition;
		}
		
		public void setIncludeDefinition(boolean includeDefinition) {
			this.includeDefinition = includeDefinition;
		}
		
		public boolean getIncludeResource() {
			return includeResource;
		}
		
		public void setIncludeResource(boolean includeResource) {
			this.includeResource = includeResource;
		}
		
		public boolean getIncludeTerms() {
			return includeTerms;
		}
		
		public void setIncludeTerms(boolean includeTerms) {
			this.includeTerms = includeTerms;
		}
		
		public int getLimit() {
			return limit;
		}
		
		public void setLimit(int limit) {
			this.limit = limit;
		}
	}

	public DataSet<? extends T> getAllConcepts(ConceptQuerySpec spec) throws PersistenceException;
	
	public ConceptType getConceptType(int type) throws PersistenceException;
	
	public StatisticsStore getStatisticsStore() throws PersistenceException;
	//public ConceptInfoStore<T> getConceptInfoStore() throws PersistenceException;
	public FeatureStore<T, Integer> getFeatureStore() throws PersistenceException;
	public ProximityStore<T, Integer> getProximityStore() throws PersistenceException;

	public T getConcept(int id, ConceptQuerySpec spec) throws PersistenceException;
	
	public DataSet<? extends T> getConcepts(int[] ids, ConceptQuerySpec spec) throws PersistenceException;
	
	/**
	 * Returns a WikiWordConcept for a random concept from the top-n 
	 * concepts with repect to in-degree.
	 * @param top the maximum rank of the concept to be returned. If top is 0, 
	 *        any concept from the full range may be returned. If it is negative,
	 *        it's interpreted as a percentage of the total number of concepts.
	 * @return a random concept from the range specified by the top argument.
	 */
	public T getRandomConcept(int top, ConceptQuerySpec spec) throws PersistenceException;
	
	/**
	 * Returns the number of concepts in the store. Since the content of a concept
	 * store is not expected to change, this value may be cached. 
	 */
	public int getNumberOfConcepts() throws PersistenceException;
}
