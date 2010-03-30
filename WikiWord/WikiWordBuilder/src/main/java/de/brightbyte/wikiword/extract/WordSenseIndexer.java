package de.brightbyte.wikiword.extract;

import java.io.IOException;
import java.util.List;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.LineCursor;
import de.brightbyte.io.OutputSink;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.PlainTextAnalyzer;
import de.brightbyte.wikiword.disambig.Disambiguator;
import de.brightbyte.wikiword.disambig.SlidingCoherenceDisambiguator;
import de.brightbyte.wikiword.disambig.StoredFeatureFetcher;
import de.brightbyte.wikiword.disambig.StoredMeaningFetcher;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSequence;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.FeatureStore;
import de.brightbyte.wikiword.store.LocalConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore;

public class WordSenseIndexer extends StreamProcessorApp<String, String, WikiWordConceptStore> {
	protected Disambiguator<TermReference, LocalConcept> disambiguator;
	protected PlainTextAnalyzer analyzer;
	private int phraseLength;

	public WordSenseIndexer() {
		super(false, true);
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
			disambiguator = new SlidingCoherenceDisambiguator( meaningFetcher, featureFetcher, true );
			
			analyzer = PlainTextAnalyzer.getPlainTextAnalyzer(getCorpus(), tweaks);
			analyzer.initialize();
			
			phraseLength = args.getIntOption("phrase-length", tweaks.getTweak("wikiSenseIndexer.phraseLength", 6)); 
	}

	@Override
	protected String process(String line) throws PersistenceException {
		PhraseOccuranceSequence sequence = analyzer.extractPhrases(line, phraseLength); //TODO: alternative tokenizer/splitter //TODO: split by sentence first.
		List<PhraseOccurance> phrases = sequence.getDisjointPhraseSequence(null);
		Disambiguator.Result<PhraseOccurance, LocalConcept> result = disambiguator.disambiguate(phrases);
		return result.toString(); //FIXME: annotate!
	}

	public static void main(String[] argv) throws Exception {
		WordSenseIndexer q = new WordSenseIndexer();
		q.launch(argv);
	}
	
}
