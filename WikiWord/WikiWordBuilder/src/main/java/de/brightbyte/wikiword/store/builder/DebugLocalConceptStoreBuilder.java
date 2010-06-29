package de.brightbyte.wikiword.store.builder;

import java.io.PrintStream;
import java.util.Collections;
import java.util.Date;
import java.util.Map;
import java.util.logging.Level;

import de.brightbyte.application.Agenda;
import de.brightbyte.application.Agenda.Record;
import de.brightbyte.application.Agenda.State;
import de.brightbyte.data.cursor.CursorProcessor;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.ExtractionRule;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.schema.AliasScope;
import de.brightbyte.wikiword.store.GroupNameTranslator;
import de.brightbyte.wikiword.store.WikiWordConceptStore;

/**
 * Dummy implementation of WikiStoreBuilder for testing and debugging
 */
public class DebugLocalConceptStoreBuilder implements LocalConceptStoreBuilder {
	
	public class DebugTextStoreBuilder implements TextStoreBuilder {

		public void storePlainText(int rcId, String name, String text) throws PersistenceException {
			log("* storePlainText("+rcId+", "+name+") *");
		}

		public void storeRawText(int rcId, String name, String text) throws PersistenceException {
			log("* storeRawText("+rcId+", "+name+") *");
		}

		public void finishAliases() throws PersistenceException {
			log("* finishAliases *");
		}
		
		public void finishIdReferences() throws PersistenceException {
			log("* finishIdReferences *");
		}
		
		public void checkConsistency() throws PersistenceException {
			log("* checkConsistency *");
		}

		public void close(boolean flush) throws PersistenceException {
			log("* close("+flush+") *");
		}

		public void deleteDataAfter(int lastId, boolean inclusive) throws PersistenceException {
			log("* deleteDataAfter("+lastId+", "+inclusive+") *");
		}

		public void deleteDataFrom(int lastId) throws PersistenceException {
			log("* deleteDataFrom *");
		}

		public void dumpTableStats(Output out) throws PersistenceException {
			log("* dumpTableStats *");
		}

		public void flush() throws PersistenceException {
			log("* flush *");
		}

		public Agenda getAgenda() throws PersistenceException {
			return null;
		}

		public Agenda createAgenda() throws PersistenceException {
			return null;
		}

		public int getNumberOfWarnings() throws PersistenceException {
			return 0;
		}

		public void open() throws PersistenceException {
			log("* open *");
		}

		public void optimize() throws PersistenceException {
			log("* optimize *");
		}

		public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
			log("* prepare("+purge+", "+dropAll+") *");
		}

		public void setLogLevel(int loglevel) {
			//noop
		}

		public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
			log("* storeWarning("+rcId+", "+problem+", "+details+") *");
		}

		public Map<String, ? extends Number> getTableStats() throws PersistenceException {
			log("* getTableStats *");
			return null;
		}

		public boolean isComplete() throws PersistenceException {
			return true;
		}

		public void finalizeImport() throws PersistenceException {
			log("* finalizeImport *");			
		}

		public void prepareImport() throws PersistenceException {
			log("* prepareImport *");
		}
		
		public DatasetIdentifier getDatasetIdentifier() {
			return dataset;
		}
		
		public void preparePostProcessing() throws PersistenceException {
			log("* preparePostProcessing *");
		}

		public void prepareMassInsert() throws PersistenceException {
			log("* prepareMassInsert *");
		}

		public void prepareMassProcessing() throws PersistenceException {
			log("* prepareMassProcessing *");
		}
		
	}

	public class DebugPropertyStoreBuilder implements LocalPropertyStoreBuilder {

		public void finishAliases() throws PersistenceException {
			log("* finishAliases *");
		}

		public void finalizeImport() throws PersistenceException {
			log("* finalizeImport *");			
		}

		public void finishIdReferences() throws PersistenceException {
			log("* finishIdReferences *");			
		}

		public void prepareImport() throws PersistenceException {
			log("* prepareImport *");
		}
		
		public void storeProperty(int resourceId, int conceptId, String concept, String property, String value) throws PersistenceException {
			log("* storeProperty("+resourceId+", "+conceptId+", "+concept+", "+property+", "+value+") *");
		}

		public void checkConsistency() throws PersistenceException {
			log("* checkConsistency *");
		}

		public void close(boolean flush) throws PersistenceException {
			log("* close *");
		}

		public void deleteDataAfter(int lastId, boolean inclusive) throws PersistenceException {
			log("* deleteDataAfter("+lastId+", "+inclusive+") *");
		}

		public void deleteDataFrom(int lastId) throws PersistenceException {
			log("* deleteDataFrom("+lastId+") *");
		}

		public void dumpTableStats(Output out) throws PersistenceException {
			log("* dumpTableStats *");
		}

		public void flush() throws PersistenceException {
			log("* flush *");
		}

		public Agenda getAgenda() throws PersistenceException {
			return null;
		}

		public Agenda createAgenda() throws PersistenceException {
			return null;
		}

		public int getNumberOfWarnings() throws PersistenceException {
			return 0;
		}

		public void open() throws PersistenceException {
			log("* open *");
		}

		public void optimize() throws PersistenceException {
			log("* optimize *");
		}

		public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
			log("* prepare *");
		}

		public void setLogLevel(int loglevel) {
			// noop
		}

		public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
			log("+ warning: rcId = "+rcId+", problem = "+problem+", details = "+details);
		}

		public Map<String, ? extends Number> getTableStats() throws PersistenceException {
			return null;
		}

		public boolean isComplete() throws PersistenceException {
			return false;
		}

		public Corpus getCorpus() {
			return null;
		}
		
		public DatasetIdentifier getDatasetIdentifier() {
			return dataset;
		}
	
		public void preparePostProcessing() throws PersistenceException {
			log("* preparePostProcessing *");
		}

		public void prepareMassInsert() throws PersistenceException {
			log("* prepareMassInsert *");
		}

		public void prepareMassProcessing() throws PersistenceException {
			log("* prepareMassProcessing *");
		}
	}
	
	public class DebugStatisticsStoreBuilder implements StatisticsStoreBuilder {


		public void finalizeImport() throws PersistenceException {
			log("* finalizeImport *");			
		}

		public void prepareImport() throws PersistenceException {
			log("* prepareImport *");
		}
		
		public void buildTermStatistics() throws PersistenceException {
			log("* buildTermStatistics *");
		}

		public void buildConceptStatistics() throws PersistenceException {
			log("* buildConceptStatistics *");
		}

		public void clear() throws PersistenceException {
			log("* clearStatistics *");
		}

		public void checkConsistency() throws PersistenceException {
			log("* checkConsistency *");
		}

		public void close(boolean flush) throws PersistenceException {
			log("* close *");
		}

		public void deleteDataAfter(int lastId, boolean inclusive)
				throws PersistenceException {
			log("* deleteDataAfter("+lastId+", "+inclusive+") *");
		}

		public void deleteDataFrom(int lastId) throws PersistenceException {
			log("* deleteDataFrom("+lastId);
		}

		public void dumpTableStats(Output out) throws PersistenceException {
			log("* no table stats *");
		}

		public void flush() throws PersistenceException {
			log("* flush *");
		}

		public Agenda getAgenda() throws PersistenceException {
			return agenda;
		}

		public Agenda createAgenda() throws PersistenceException {
			return null;
		}

		public int getNumberOfWarnings() throws PersistenceException {
			return 0;
		}

		public void open() throws PersistenceException {
			log("* open *");
		}

		public void optimize() throws PersistenceException {
			log("* optimize *");
		}

		public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
			log("* prepare *");
		}

		public void setLogLevel(int loglevel) {
			//noop
		}

		public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
			log("+ warning: rcId = "+rcId+", problem = "+problem+", details = "+details);
		}


		public Map<String, ? extends Number> getTableStats() throws PersistenceException {
			return Collections.emptyMap(); //TODO: counters
		}

		public boolean isComplete() throws PersistenceException {
			return true;
		}
		public DatasetIdentifier getDatasetIdentifier() {
			return dataset;
		}

	}

	public class DebugProximityStoreBuilder implements ProximityStoreBuilder {


		public void finalizeImport() throws PersistenceException {
			log("* finalizeImport *");			
		}

		public void prepareImport() throws PersistenceException {
			log("* prepareImport *");
		}
		
		public void clear() throws PersistenceException {
			log("* clearStatistics *");
		}

		public void checkConsistency() throws PersistenceException {
			log("* checkConsistency *");
		}

		public void close(boolean flush) throws PersistenceException {
			log("* close *");
		}

		public void deleteDataAfter(int lastId, boolean inclusive)
				throws PersistenceException {
			log("* deleteDataAfter("+lastId+", "+inclusive+") *");
		}

		public void deleteDataFrom(int lastId) throws PersistenceException {
			log("* deleteDataFrom("+lastId);
		}

		public void dumpTableStats(Output out) throws PersistenceException {
			log("* no table stats *");
		}

		public void flush() throws PersistenceException {
			log("* flush *");
		}

		public Agenda getAgenda() throws PersistenceException {
			return agenda;
		}

		public Agenda createAgenda() throws PersistenceException {
			return null;
		}

		public int getNumberOfWarnings() throws PersistenceException {
			return 0;
		}

		public void open() throws PersistenceException {
			log("* open *");
		}

		public void optimize() throws PersistenceException {
			log("* optimize *");
		}

		public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
			log("* prepare *");
		}

		public void setLogLevel(int loglevel) {
			//noop
		}

		public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
			log("+ warning: rcId = "+rcId+", problem = "+problem+", details = "+details);
		}


		public Map<String, ? extends Number> getTableStats() throws PersistenceException {
			return Collections.emptyMap(); //TODO: counters
		}

		public boolean isComplete() throws PersistenceException {
			return true;
		}
		
		public DatasetIdentifier getDatasetIdentifier() {
			return dataset;
		}

		public void buildFeatures() throws PersistenceException {
			log("* buildFeatures *");
		}

		public void buildBaseProximity() throws PersistenceException {
			log("* buildBaseProximity *");
		}

		public void buildExtendedProximity() throws PersistenceException {
			log("* buildExtendedProximity *");
		}

	}

	public class DebugConceptInfoStoreBuilder implements
			ConceptInfoStoreBuilder<LocalConcept> {


		public void finalizeImport() throws PersistenceException {
			log("* finalizeImport *");			
		}

		public void prepareImport() throws PersistenceException {
			log("* prepareImport *");
		}
		
		public void checkConsistency() throws PersistenceException {
			log("* checkConsistency *");
		}

		public void close(boolean flush) throws PersistenceException {
			log("* close *");
		}

		public void deleteDataAfter(int lastId, boolean inclusive)
				throws PersistenceException {
			log("* deleteDataAfter("+lastId+", "+inclusive+") *");
		}

		public void deleteDataFrom(int lastId) throws PersistenceException {
			log("* deleteDataFrom("+lastId);
		}

		public void dumpTableStats(Output out) throws PersistenceException {
			log("* no table stats *");
		}

		public void flush() throws PersistenceException {
			log("* flush *");
		}

		public Agenda getAgenda() throws PersistenceException {
			return agenda;
		}

		public Agenda createAgenda() throws PersistenceException {
			return null;
		}

		public int getNumberOfWarnings() throws PersistenceException {
			return 0;
		}

		public void open() throws PersistenceException {
			log("* open *");
		}

		public void optimize() throws PersistenceException {
			log("* optimize *");
		}

		public void initialize(boolean purge, boolean dropAll) throws PersistenceException {
			log("* prepare *");
		}

		public void setLogLevel(int loglevel) {
			//noop
		}

		public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
			log("+ warning: rcId = "+rcId+", problem = "+problem+", details = "+details);
		}

		public Map<String, ? extends Number> getTableStats() throws PersistenceException {
			return Collections.emptyMap(); //TODO: counters
		}

		public boolean isComplete() throws PersistenceException {
			return true;
		}

		public DatasetIdentifier getDatasetIdentifier() {
			return dataset;
		}

		public void buildConceptRelationCache() throws PersistenceException {
			log("* buildConceptRelationCache *");
		}

		public void buildConceptDescriptionCache() throws PersistenceException {
			log("* buildConceptDescriptionCache *");
		}

		public void buildConceptFeatureCache() throws PersistenceException {
			log("* buildConceptFeatureCache *");
		}

		public void buildConceptProximetyCache() throws PersistenceException {
			log("* buildConceptProximetyCache *");
		}

	}

	public class DebugAgendaPersistor extends Agenda.TransientPersistor {

		protected int id = 0;
		protected Agenda.Record record;
		
		@Override
		public Record logStart(int level, String context, String task, Map<String, Object> parameters, boolean complex) {
			Record rec = super.logStart(level, context, task, parameters, complex);
			log("+ logStart: level = "+level+", task = "+task+", parameters = "+parameters+", complex = "+complex);
			return rec;
		}

		@Override
		public void logTerminated(int start, int end, long duration, State state, String result) {
			super.logTerminated(start, end, duration, state, result);
			log("+ logStart: start = "+start+", end = "+end+", duration = "+duration+", state = "+state+", result = "+result);
		}

	}

	protected Output out;
	protected int logLevel = Level.INFO.intValue();
	
	protected int conceptCounter = 0;
	protected int conceptBroaderCounter = 0;
	protected int conceptEquivalentCounter  = 0;
	protected int conceptReferenceCounter  = 0;
	protected int rawTextCounter = 0;
	protected int plainTextCounter = 0;
	protected int definitionCounter = 0;
	protected int resourceCounter = 0;
	protected int languageLinkCounter = 0;
	protected int linkCounter = 0;
	protected int sectionCounter = 0;
	
	private Agenda agenda;
	private DatasetIdentifier dataset;
	
	public DebugLocalConceptStoreBuilder(Corpus corpus, Output out) {
		super();
		this.out = out;
		this.dataset = corpus;
		
		try {
			this.agenda = new Agenda( new DebugAgendaPersistor() );
		} catch (PersistenceException e) {
			throw new Error("unexpected exception", e);
		}
	}
	
	protected void trace(String s) {
		if (logLevel<=Level.FINER.intValue()) 
			out.println(s);
	}

	protected void log(String s) {
		if (logLevel<=Level.INFO.intValue()) 
			out.println(s);
	}
	
	public void setLogLevel(int logLevel) {
		this.logLevel = logLevel;
	}

	public void close(boolean flush)  {
		log("* close *");
	}

	public void initialize(boolean purge, boolean dropAll) {
		log("* prepare *");
	}

	public void dumpTableStats(Output out)  {
		log("* no table stats *"); //TODO: counters!
	}

	public void dumpStatistics(Output out)  {
		dumpTableStats(out);
	}

	public Map<String, ? extends Number> getTableStats()  {
		return Collections.emptyMap(); //TODO: counters!
	}

	public Map<String, ? extends Number>  getStatistics()  {
		return getTableStats();
	}

	public void open()  {
		log("* open *");
	}

	public void prepareImport()  {
		log("* prepare *");
	}

	public int storeConcept(int rcId, String name, ConceptType ctype)  {
		conceptCounter++;
		log("+ storeConcept: rc = "+rcId+", name = "+name+", type = "+ctype);
		return conceptCounter;
	}
	
	public int storeResource(int pageId, int revId, String name, ResourceType ptype, Date time)  {
		resourceCounter++;
		log("+ storeResource: page_id = "+pageId+", revision_id = "+revId+", id = "+resourceCounter+", name = "+name+", type = "+ptype+", timestamp = "+time);
		return resourceCounter;
	}

	public int storeResourceAbout(int pageId, int revId, String name, ResourceType ptype, Date time, int conceptId, String conceptName)  {
		int resourceId = storeResource(pageId, revId, name, ptype, time);
		storeAbout(resourceId, name, conceptId, conceptName);
		return resourceId;
	}


	public void storeDefinition(int rcId, int conceptId, String definition)  {
		definitionCounter++;
		log("+ storeDefinition: conceptId = "+conceptId+": "+definition);
	}

	public int storePlainText(int rcId, String text)  {
		plainTextCounter++;
		log("+ storePlainText: resource = "+rcId+": ");
		log("---------------------------------");
		log(text);
		log("\n---------------------------------");
		return plainTextCounter;
	}

	public int storeRawText(int rcId, String text)  {
		rawTextCounter++;
		log("+ storeRawText: resource = "+rcId+": ");
		log("---------------------------------");
		log(text);
		log("\n---------------------------------");
		return rawTextCounter;
	}


	public void storeConceptBroader(int rcId, int narrowId, String narrowName, String broadName, ExtractionRule rule)  {
		conceptBroaderCounter++;
		log("+ storeConceptBroader: rc = "+rcId+", narrow ("+narrowId+") =  "+narrowName+", broad = "+broadName+", rule = "+rule);
	}

	public void storeConceptBroader(int rcId, String narrowName, String broadName, ExtractionRule rule)  {
		conceptBroaderCounter++;
		log("+ storeConceptBroader: rc = "+rcId+", narrow =  "+narrowName+", broad = "+broadName+", rule = "+rule);
	}

	public void storeConceptAlias(int rcId, int left, String leftName, int right, String rightName, AliasScope scope)  {
		conceptEquivalentCounter++;
		log("+ storeConceptEquivalent: rc = "+rcId+", left ("+left+") =  "+leftName+", right ("+right+") = "+rightName+", scope = "+scope);
	}

	public void storeConceptReference(int rcId, int source, String sourceName, String target)  {
		conceptReferenceCounter++;
		log("+ storeConceptReference: rc = "+rcId+", source ("+source+") =  "+sourceName+", target = "+target+"");
	}

	public void storeLanguageLink(int rcId, int concept, String conceptName, String lang, String target)  {
		languageLinkCounter++;
		log("+ storeLanguageLink: rc = "+rcId+", concept ("+concept+") =  "+conceptName+", language = "+lang+", target = "+target+"");
	}

	public void storeLink(int rcId, int anchorId, String anchorName, 
			String term, String targetName, ExtractionRule rule)  {
		linkCounter++;
		log("+ storeTermUse: rc = "+rcId+", anchor ("+anchorId+") =  "+anchorName+", term = "+term+", target =  "+targetName+", rule = "+rule+"");
	}

	public void storeReference(int rcId, String term, int targetId, String targetName, 
			ExtractionRule rule)  {
		linkCounter++;
		log("+ storeTermUse: rc = "+rcId+", target ("+targetId+") =  "+targetName+", term = "+term+", rule = "+rule+"");
	}

	public void storeSection(int rcId, String name, String page)  {
		sectionCounter++;
		log("+ section: rc = "+rcId+", name ("+name+") =  "+page);
	}

	public void checkConsistency()  {
		log("* checkConsistency *");
	}

	public void flush()  {
		log("* flush *");
	}

	public void deleteDataFrom(int rcId)  {
		log("- delete data from resource "+rcId);
	}

	public void deleteDataAfter(int rcId, boolean inclusive)  {
		log("- delete data after resource "+rcId);
	}

	public Agenda getAgenda() {
		return agenda;
	}

	public Agenda createAgenda() throws PersistenceException {
		return null;
	}

	public void optimize() {
		log("- optimize");
	}

	public void dumpTableStats(PrintStream out, String table)  {
		log("* no stats *");
	}

	public void dumpTableStats(PrintStream out, String table, String groupby, GroupNameTranslator translator)  {
		log("* no stats *");
	}

	public Corpus getCorpus() {
		return null;
	}

	public void buildStatistics() {
		log("- build stats");
	}
	public void clearStatistics() {
		log("- clear stats");
	}

	public int getNumberOfWarnings()  {
		return 0; //TODO: counter
	}

	public boolean isComplete() throws PersistenceException {
		return true;
	}

	public void finishAliases() throws PersistenceException {
		log("* finishAliases *");
	}

	public void finishFinish() throws PersistenceException {
		log("* finishFinish *");
	}

	public void finishIdReferences() throws PersistenceException {
		log("* finishIdReferences *");
	}

	public void finalizeImport() throws PersistenceException {
		log("* finishImport *");
	}

	public void finishRelations() throws PersistenceException {
		log("* finishRelations *");
	}

	public void finishMeanings() throws PersistenceException {
		log("* finishMeanings *");
	}

	public void buildConceptInfo() throws PersistenceException {
		log("* finishConceptInfo *");
	}

	public void finishBadLinks() throws PersistenceException {
		log("* finishBadLinks *");
	}

	public void finishMissingConcepts() throws PersistenceException {
		log("* finishMissingConcpets *");
	}

	public void finishSections() throws PersistenceException {
		log("* finishSections *");
	}

	public ConceptInfoStoreBuilder<LocalConcept> getConceptInfoStoreBuilder() {
		return new DebugConceptInfoStoreBuilder();
	}

	public StatisticsStoreBuilder getStatisticsStoreBuilder() {
		return new DebugStatisticsStoreBuilder();
	}

	public ProximityStoreBuilder getProximityStoreBuilder() {
		return new DebugProximityStoreBuilder();
	}

	public TextStoreBuilder getTextStoreBuilder() {
		return new DebugTextStoreBuilder();
	}

	public LocalPropertyStoreBuilder getPropertyStoreBuilder() {
		return new DebugPropertyStoreBuilder();
	}

	public void storeWarning(int rcId, String problem, String details) throws PersistenceException {
		log("+ storeWarning: rcId="+rcId+", problem="+problem+", details="+details);
	}

	public WikiWordConceptStore<LocalConcept> getConceptStore() throws PersistenceException {
		return null; //XXX...
	}

	public DataSet<LocalConcept> listUnknownConcepts() throws PersistenceException {
		return null; //XXX...
	}

	public void resetTermsForUnknownConcepts() throws PersistenceException {
		//noop
	}

	public int processUnknownConcepts(CursorProcessor<LocalConcept> processor) throws PersistenceException {
		//noop
		return 0;
	}

	public int storeAbout(int resource, String rcName, String conceptName)  {
		log("+ storeAbout: resource = "+resource+", resourceName = "+rcName+", conceptName =  "+conceptName);
		return -1;
	}

	public int storeAbout(int resource, String rcName, int concept, String conceptName) {
		log("+ storeAbout: resource = "+resource+", resourceName = "+rcName+", concept =  "+concept+", conceptName =  "+conceptName);
		return -1;
	}

	public DatasetIdentifier getDatasetIdentifier() {
		return dataset;
	}
	
	public void prepareMassInsert() throws PersistenceException {
		log("* prepareMassInsert *");
	}

	public void prepareMassProcessing() throws PersistenceException {
		log("* prepareMassProcessing *");
	}
	
}