package de.brightbyte.wikiword.extract;

import java.io.IOException;
import java.text.ParseException;
import java.util.Collections;
import java.util.List;
import java.util.regex.Pattern;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.data.measure.Measure;
import de.brightbyte.io.LineCursor;
import de.brightbyte.io.LineSink;
import de.brightbyte.text.Chunker;
import de.brightbyte.text.RegularExpressionChunker;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.analyzer.PlainTextAnalyzer;
import de.brightbyte.wikiword.disambig.Disambiguator;
import de.brightbyte.wikiword.disambig.SlidingCoherenceDisambiguator;
import de.brightbyte.wikiword.disambig.StoredFeatureFetcher;
import de.brightbyte.wikiword.disambig.StoredMeaningFetcher;
import de.brightbyte.wikiword.disambig.Term;
import de.brightbyte.wikiword.disambig.Disambiguator.Disambiguation;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.FeatureStore;
import de.brightbyte.wikiword.store.LocalConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore;

public class WordSenseIndexer extends StreamProcessorApp<String, String, WikiWordConceptStore> {
	protected Disambiguator<TermReference, LocalConcept> disambiguator;
	protected PlainTextAnalyzer analyzer;
	private int phraseLength;
	protected Chunker chunker;
	protected boolean flip = false;

	public WordSenseIndexer() {
		super(false, true);
	}

	@Override
	protected DataSink<String> openSink(int paramIndex) throws PersistenceException {
		try {
			return new LineSink(getOutputWriter(paramIndex));
		} catch (IOException e) {
			throw new PersistenceException(e);
		} 
	}

	@Override
	protected DataCursor<String> openCursor(int paramIndex) throws PersistenceException {
		try {
			return new LineCursor(getInputReader(paramIndex));
		} catch (IOException e) {
			throw new PersistenceException(e);
		}  
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
		WikiWordConceptStore.ConceptQuerySpec spec = new WikiWordConceptStore.ConceptQuerySpec();
		//spec.setRequireType(ConceptType.PLACE); //FIXME: config! //NOTE: type tags are currently too bad, need to rebuild; use soft boost instead.
		
			StoredMeaningFetcher meaningFetcher = new StoredMeaningFetcher(getLocalConceptStore(), spec);
			StoredFeatureFetcher<LocalConcept, Integer> featureFetcher = new StoredFeatureFetcher<LocalConcept, Integer>(getFeatureStore());
			disambiguator = new SlidingCoherenceDisambiguator( meaningFetcher, featureFetcher, 10 ); //FIXME: cache depth from config
			
			Measure<WikiWordConcept> popularityMeasure = new Measure<WikiWordConcept>(){ //boost locations //FIXME: configure! 
				public double measure(WikiWordConcept concept) {
					double score = concept.getCardinality();
					
					if (concept.getType().equals(ConceptType.PLACE))
						score *= 10; //XXX: magic number...
					
					return score;
				}
			};
			
			((SlidingCoherenceDisambiguator)disambiguator).setPopularityMeasure(popularityMeasure);
			
			analyzer = PlainTextAnalyzer.getPlainTextAnalyzer(getCorpus(), tweaks);
			analyzer.initialize();
			
			phraseLength = args.getIntOption("phrase-length", tweaks.getTweak("wikiSenseIndexer.phraseLength", 6));
			
			chunker = new RegularExpressionChunker(Pattern.compile("\\s*[,;|]\\s*")); //TODO: configure!
			flip = true; //FIXME: parameter
			//TODO: parameter for limiting concept type
	}

	@Override
	protected void process(String line) throws PersistenceException, ParseException {
		//TODO: logic for handling overlapping phrases in a PhraseOccuranceSet
		/*
		PhraseOccuranceSet sequence = analyzer.extractPhrases(line, phraseLength); //TODO: alternative tokenizer/splitter //TODO: split by sentence first.
		List<PhraseOccurance> phrases = sequence.getDisjointPhraseSequence(null);
		Disambiguator.Result<PhraseOccurance, LocalConcept> result = disambiguator.disambiguate(phrases);
		return result.toString(); //FIXME: annotate!
		*/
		
		List<Term> terms =  Term.asTerms(chunker.chunk(line.trim()));
		if (flip) Collections.reverse(terms);
		
		Disambiguator.Disambiguation<Term, LocalConcept> result = disambiguator.disambiguate(terms, null);
		if (flip) Collections.reverse(terms);
		
		String s = assembleMeanings(terms, result); //TODO: use proper TSV or something
		commit(s);
	}

	private String assembleMeanings(List<Term> terms, Disambiguation<Term, LocalConcept> result) {
		StringBuilder s = new StringBuilder();
		
		for (Term t: terms) {
			LocalConcept concept = result.getMeanings().get(t);
			
			if (s.length()>0) s.append(';');
			s.append(t.getTerm()); //FIXME: escape!
			s.append('=');
			if (concept!=null) s.append(concept.getName()); //FIXME: escape!
		}
		
		return s.toString();
	}

	public static void main(String[] argv) throws Exception {
		WordSenseIndexer q = new WordSenseIndexer();
		q.launch(argv);
	}
	
}
