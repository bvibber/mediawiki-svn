package de.brightbyte.wikiword.disambig;

import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import junit.framework.TestCase;
import de.brightbyte.abstraction.ListAbstractor;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.io.ChunkingCursor;
import de.brightbyte.io.GroupingCursor;
import de.brightbyte.io.LineCursor;
import de.brightbyte.text.CsvLineChunker;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;
import de.brightbyte.wikiword.model.TermReference;

public class DisambiguatorTestBase extends TestCase {

	protected Map<String, List<? extends LocalConcept>> meanings = new HashMap<String, List<? extends LocalConcept>>();
	protected Map<Integer, ConceptFeatures<LocalConcept, Integer>> features = new HashMap<Integer, ConceptFeatures<LocalConcept, Integer>>();
	protected Map<Integer, LocalConcept> conceptsById = new HashMap<Integer, LocalConcept>();
	protected Map<String, LocalConcept> conceptsByName = new HashMap<String, LocalConcept>();
	
	protected static DataCursor<List<String>> openTableCursor(InputStream in, String enc) throws IOException {
			ChunkingCursor cursor =  new ChunkingCursor(new LineCursor(in, enc), CsvLineChunker.tsv);
			return cursor;
	}

	protected static DataCursor<List<List<String>>> openGroupedTableCursor(InputStream in, String enc, int groupBy, boolean skipHeader) throws IOException, PersistenceException {
		DataCursor<List<String>> c = openTableCursor(in, enc);
		if (skipHeader) c.next(); //skip first line
		
		return new GroupingCursor<List<String>, String>(c, new ListAbstractor.Accessor<String>(groupBy));
	}
	
	protected static void readMeanings(Corpus corpus, InputStream in, Map<String, List<? extends LocalConcept>> meanings) throws IOException, PersistenceException {
		DataCursor<List<List<String>>> cursor = openGroupedTableCursor(in, "UTF-8", 0, true);
		
		List<List<String>> group;
		while ((group = cursor.next()) != null) {
			List<LocalConcept> concepts = new ArrayList<LocalConcept>(group.size());
			String term = null;
			
			for (List<String> row: group) {
				term = row.get(0);
				int id = Integer.parseInt(row.get(1));
				String name = row.get(2);
				int freq = Integer.parseInt(row.get(3));
				int rule = Integer.parseInt(row.get(4));
				
				int score = ((rule==10 || rule==30) && freq<2) ? 0 : freq*rule;
				
				LocalConcept c = new LocalConcept(corpus, id, ConceptType.UNKNOWN, name);
				c.setCardinality(freq);
				c.setRelevance(score);
				
				concepts.add(c);
			}
			
			if (term!=null) meanings.put(term, concepts);
		}
		
		cursor.close();
	}
	
	protected static void readFeatures(Corpus corpus, InputStream in, Map<Integer, ConceptFeatures<LocalConcept, Integer>> features) throws IOException, PersistenceException {
		DataCursor<List<List<String>>> cursor = openGroupedTableCursor(in, "UTF-8", 0, true);
		
		List<List<String>> group;
		while ((group = cursor.next()) != null) {
			LabeledVector<Integer> v = new MapLabeledVector<Integer>();
			Integer id = null;
			String name = null;
			
			for (List<String> row: group) {
				id = new Integer(row.get(0));
				name = row.get(1);
				
				int feature = Integer.parseInt(row.get(2));
				double value = Double.parseDouble(row.get(3));

				v.set(feature, value);
			}
			
			if (id!=null) {
				double len = v.getLength();
				v = v.scaled(len); //normalize
				
				LocalConcept c = new LocalConcept(corpus, id, ConceptType.UNKNOWN, name);
				ConceptFeatures<LocalConcept, Integer> f = new ConceptFeatures<LocalConcept, Integer>(c, v);
				features.put(id, f);
			}
		}
		
		cursor.close();
	}
	
	protected MeaningFetcher<LocalConcept> meaningFetcher = new MeaningFetcher<LocalConcept>() {
	
		public <X extends TermReference> Map<X, List<? extends LocalConcept>> getMeanings(
				Collection<X> terms) throws PersistenceException {
			Map<X, List<? extends LocalConcept>> m = new HashMap<X, List<? extends LocalConcept>>();
			
			for (X t: terms) {
				List<? extends LocalConcept> n = getMeanings(t.getTerm());
				if (n!=null) m.put(t, n);
			}
			
			return m;
		}
	
		public List<? extends LocalConcept> getMeanings(String term)
				throws PersistenceException {
			return meanings.get(term);
		}
	
	};
	
	protected FeatureFetcher<LocalConcept, Integer> featureFetcher = new FeatureFetcher<LocalConcept, Integer>() {
	
		public boolean getFeaturesAreNormalized() {
			return true;
		}
	
		public Map<Integer, ConceptFeatures<LocalConcept, Integer>> getFeatures(
				Collection<? extends LocalConcept> concepts) throws PersistenceException {
			Map<Integer, ConceptFeatures<LocalConcept, Integer>> m = new HashMap<Integer, ConceptFeatures<LocalConcept, Integer>>();
			
			for (LocalConcept c: concepts) {
				ConceptFeatures<LocalConcept, Integer> f = getFeatures(c);
				m.put(c.getId(), f);
			}
			
			return m;
		}
	
		public ConceptFeatures<LocalConcept, Integer> getFeatures(LocalConcept c)
				throws PersistenceException {
			return features.get(c.getId());
		}
	
	};
	
	protected Corpus corpus;
	protected TweakSet tweaks;
	
	public DisambiguatorTestBase() throws IOException, PersistenceException {
		tweaks = new TweakSet();
		corpus = Corpus.forName("TEST", "en", tweaks);
		
		URL meaningFile = getClass().getResource("SlidingCoherenceDisambiguatorTest-meanings.csv");
		URL featureFile = getClass().getResource("SlidingCoherenceDisambiguatorTest-features.csv");
		
		readMeanings(corpus, meaningFile.openStream(), meanings);
		readFeatures(corpus, featureFile.openStream(), features);
		
		for (List<? extends LocalConcept> concepts: meanings.values()) {
			for (LocalConcept c: concepts) {
				conceptsById.put(c.getId(), c);
				conceptsByName.put(c.getName(), c);
			}
		}
	}

	protected List<Term> terms(String... terms) {
		 List<Term> list = new ArrayList<Term>();
		 for (String t: terms) list.add(new Term(t));
		 return list;
	}

	protected LocalConcept getConcept(String name) {
		LocalConcept c = conceptsByName.get(name);
		return c;
	}

	protected LocalConcept getConcept(int id) {
		LocalConcept c = conceptsById.get(id);
		return c;
	}

	protected <X extends TermReference>Map<X, List<? extends LocalConcept>> getMeanings(Collection<List<X>> sequences) throws PersistenceException {
		Map<X, List<? extends LocalConcept>> m = new HashMap<X, List<? extends LocalConcept>>();
		
		for (List<X> seq: sequences) {
			Map<X, List<? extends LocalConcept>> meanings = meaningFetcher.getMeanings(seq);
			m.putAll(meanings);
		}
		
		return m;
	}

	protected PhraseOccuranceSet getBankAndMonumentPhrases() {
		String text = "The Bank and Monument Underground station";
		List<PhraseOccurance> phrases = new ArrayList<PhraseOccurance>();
		
		phrases.add( new PhraseOccurance( text.substring( 0, 8 ), 1, 0, 8 ) ); //The Bank
		phrases.add( new PhraseOccurance( text.substring( 0, 21 ), 2, 0, 21 ) ); //The Bank and Monument
		phrases.add( new PhraseOccurance( text.substring( 0, 33 ), 3, 0, 33 ) ); //The Bank and Monument Underground
		
		phrases.add( new PhraseOccurance( text.substring( 4, 8 ), 1, 4, 8-4 ) ); //Bank
		phrases.add( new PhraseOccurance( text.substring( 4, 21 ), 2, 4, 21-4 ) ); //Bank and Monument
		phrases.add( new PhraseOccurance( text.substring( 4, 33 ), 3, 4, 33-4 ) ); //Bank and Monument Underground
		//phrases.add( new PhraseOccurance( text.substring( 4, 41 ), 4, 4, 41-4 ) ); //Bank and Monument Underground station
		
		phrases.add( new PhraseOccurance( text.substring( 13, 21 ), 1, 13, 21-13 ) ); //Monument
		phrases.add( new PhraseOccurance( text.substring( 13, 33 ), 2, 13, 33-13 ) ); //Monument Underground
		phrases.add( new PhraseOccurance( text.substring( 13, 41 ), 3, 13, 41-13 ) ); //Monument Underground station
		
		phrases.add( new PhraseOccurance( text.substring( 22, 33 ), 1, 22, 33-22 ) ); //Underground
		phrases.add( new PhraseOccurance( text.substring( 22, 41 ), 2, 22, 41-22 ) ); //Underground stations

		phrases.add( new PhraseOccurance( text.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
		
		PhraseOccuranceSet set = new PhraseOccuranceSet(text, phrases);
		return set;
	}
	
}
