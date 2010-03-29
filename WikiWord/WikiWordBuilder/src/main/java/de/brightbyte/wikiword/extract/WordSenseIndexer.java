package de.brightbyte.wikiword.extract;

import java.io.IOException;

import sun.net.dns.ResolverConfiguration.Options;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.LineCursor;
import de.brightbyte.io.OutputSink;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.PhraseOccuranceSequence;
import de.brightbyte.wikiword.analyzer.PlainTextAnalyzer;
import de.brightbyte.wikiword.disambig.Disambiguator;
import de.brightbyte.wikiword.disambig.SlidingCoherenceDisambiguator;
import de.brightbyte.wikiword.disambig.StoredFeatureFetcher;
import de.brightbyte.wikiword.disambig.StoredMeaningFetcher;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.FeatureStore;
import de.brightbyte.wikiword.store.LocalConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore;

public class WordSenseIndexer extends StreamProcessorApp<String, String, WikiWordConceptStore> {
	protected Disambiguator disambiguator;
	protected PlainTextAnalyzer analyzer;
	private int phraseLength;

	public WordSenseIndexer(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
	}

	@Override
	protected DataSink<String> openSink() {
		return new OutputSink(ConsoleIO.output); //FIXME: open stream as required
	}

	@Override
	protected DataCursor<String> openCursor() {
		return new LineCursor(ConsoleIO.newReader());  //FIXME: open stream as required
	}

	@Override
	protected void createStores() throws IOException, PersistenceException {
		conceptStore = DatabaseConceptStores.createConceptStore(getConfiguredDataSource(), getConfiguredDataset(), tweaks, true, true);
		registerStore(conceptStore);
	}
	
	protected FeatureStore<LocalConcept, Integer> getFeatureStore() throws PersistenceException {
		return conceptStore.getFeatureStore();
	}

	protected LocalConceptStore getLocalConceptStore() {
		return (LocalConceptStore)(Object)conceptStore; //XXX: FUGLY! generic my ass.
	}
	
	protected void init() throws PersistenceException, InstantiationException {
			StoredMeaningFetcher meaningFetcher = new StoredMeaningFetcher(getLocalConceptStore());
			StoredFeatureFetcher<LocalConcept, Integer> featureFetcher = new StoredFeatureFetcher<LocalConcept, Integer>(getFeatureStore());
			disambiguator = new SlidingCoherenceDisambiguator<Integer>( meaningFetcher, featureFetcher, true );
			
			analyzer = PlainTextAnalyzer.getPlainTextAnalyzer(getCorpus(), tweaks);
			
			phraseLength = args.getIntOption("phrase-length", tweaks.getTweak("wikiSenseIndexer.phraseLength", 6)); 
	}

	@Override
	protected String process(String line) {
		PhraseOccuranceSequence sequence = analyzer.extractPhrases(line, phraseLength);
		return null;
	}

}
