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
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.io.ChunkingCursor;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.GroupingCursor;
import de.brightbyte.io.LineCursor;
import de.brightbyte.io.Output;
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
import de.brightbyte.wikiword.model.WikiWordConcept;

public class DisambiguatorTestBase extends TestCase {

	protected Map<String, List<LocalConcept>> meanings = new HashMap<String, List<LocalConcept>>();
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
	
	protected static void readMeanings(Corpus corpus, InputStream in, Map<String, List<LocalConcept>> meanings) throws IOException, PersistenceException {
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
			LabeledVector<Integer> v = ConceptFeatures.newIntFeaturVector(group.size());
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
				v.scale(1.0/len); //normalize
				
				LocalConcept c = new LocalConcept(corpus, id, ConceptType.UNKNOWN, name);
				ConceptFeatures<LocalConcept, Integer> f = new ConceptFeatures<LocalConcept, Integer>(c, v);
				features.put(id, f);
			}
		}
		
		cursor.close();
	}
	
	protected MeaningFetcher<LocalConcept> meaningFetcher = new MeaningFetcher<LocalConcept>() {
	
		public <X extends TermReference> Map<X, List<LocalConcept>> getMeanings(
				Collection<X> terms) throws PersistenceException {
			Map<X, List<LocalConcept>> m = new HashMap<X, List<LocalConcept>>();
			
			for (X t: terms) {
				List<LocalConcept> n = getMeanings(t.getTerm());
				if (n!=null) m.put(t, n);
			}
			
			return m;
		}
	
		public List<LocalConcept> getMeanings(String term)
				throws PersistenceException {
			return meanings.get(term);
		}
	
	};
	
	protected FeatureFetcher<LocalConcept, Integer> featureFetcher = new FeatureFetcher<LocalConcept, Integer>() {
	
		public boolean getFeaturesAreNormalized() {
			return false;
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
	protected Output traceOutput = ConsoleIO.output;

	public DisambiguatorTestBase() throws IOException, PersistenceException {
		tweaks = new TweakSet();
		corpus = Corpus.forName("TEST", "en", tweaks);
		
		URL meaningFile = getClass().getResource("DisambiguatorTest-meanings.csv");
		URL featureFile = getClass().getResource("DisambiguatorTest-features.csv");
		
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

	protected <X extends TermReference>Map<X, List<LocalConcept>> getMeanings(Collection<List<X>> sequences) throws PersistenceException {
		Map<X, List<LocalConcept>> m = new HashMap<X, List<LocalConcept>>();
		
		for (List<X> seq: sequences) {
			Map<X, List<LocalConcept>> meanings = meaningFetcher.getMeanings(seq);
			m.putAll(meanings);
		}
		
		return m;
	}

	private String bankAndMonumentText = "The Bank and Monument Underground station";

	protected List<PhraseOccurance> getBankAndMonumentTerms(int depth) {
		List<PhraseOccurance> phrases = new ArrayList<PhraseOccurance>();

		if (depth==0) return phrases;
		
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 0, 8 ), 1, 0, 8 ) ); //The Bank
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 0, 21 ), 2, 0, 21 ) ); //The Bank and Monument
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 0, 33 ), 3, 0, 33 ) ); //The Bank and Monument Underground
		
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 4, 8 ), 1, 4, 8-4 ) ); //Bank
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 4, 21 ), 2, 4, 21-4 ) ); //Bank and Monument
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 4, 33 ), 3, 4, 33-4 ) ); //Bank and Monument Underground
		//phrases.add( new PhraseOccurance( text.substring( 4, 41 ), 4, 4, 41-4 ) ); //Bank and Monument Underground station

		if (depth==1) return phrases;
		
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 21 ), 1, 13, 21-13 ) ); //Monument
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 33 ), 2, 13, 33-13 ) ); //Monument Underground
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 41 ), 3, 13, 41-13 ) ); //Monument Underground station
		
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 33 ), 1, 22, 33-22 ) ); //Underground
		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 41 ), 2, 22, 41-22 ) ); //Underground stations

		phrases.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
		
		return phrases;
	}

	protected Collection<List<PhraseOccurance>> getBankAndMonumentSequences(int depth) {
		ArrayList<List<PhraseOccurance>> sequences = new ArrayList<List<PhraseOccurance>>();

		if (depth==0) return sequences;
		
		List<PhraseOccurance> seq1 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq11 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq111 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq1111 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq112 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq12 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq121 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq13 = new ArrayList<PhraseOccurance>();

		List<PhraseOccurance> seq2 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq21 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq211 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq22 = new ArrayList<PhraseOccurance>();

		List<PhraseOccurance> seq3 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq31 = new ArrayList<PhraseOccurance>();

		List<PhraseOccurance> seq5 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq51 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq511 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq5111 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq512 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq52 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq521 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq53 = new ArrayList<PhraseOccurance>();

		List<PhraseOccurance> seq6 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq61 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq611 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq62 = new ArrayList<PhraseOccurance>();

		List<PhraseOccurance> seq7 = new ArrayList<PhraseOccurance>();
		List<PhraseOccurance> seq71 = new ArrayList<PhraseOccurance>();

		seq1.add( new PhraseOccurance( bankAndMonumentText.substring( 0, 8 ), 1, 0, 8 ) ); //The Bank
		seq2.add( new PhraseOccurance( bankAndMonumentText.substring( 0, 21 ), 1, 0, 21 ) ); //The Bank and Monument
		seq3.add( new PhraseOccurance( bankAndMonumentText.substring( 0, 33 ), 3, 0, 33 ) ); //The Bank and Monument Underground
		seq5.add( new PhraseOccurance( bankAndMonumentText.substring( 4, 8 ), 1, 4, 8-4 ) ); //Bank
		seq6.add( new PhraseOccurance( bankAndMonumentText.substring( 4, 21 ), 2, 4, 21-4 ) ); //Bank and Monument
		seq7.add( new PhraseOccurance( bankAndMonumentText.substring( 4, 33 ), 3, 4, 33-4 ) ); //Bank and Monument Underground

		if (depth==1) {
			sequences.add(seq1);
			sequences.add(seq2);
			sequences.add(seq3);
			sequences.add(seq5);
			sequences.add(seq6);
			sequences.add(seq7);
			
			return sequences;
		} 
		
		seq11.addAll(seq1);
		seq11.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 21 ), 1, 13, 21-13 ) ); //Monument
		seq12.addAll(seq1);
		seq12.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 33 ), 2, 13, 33-13 ) ); //Monument Underground
		seq13.addAll(seq1);
		seq13.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 41 ), 3, 13, 41-13 ) ); //Monument Underground station
		seq21.addAll(seq2);
		seq21.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 33 ), 1, 22, 33-22 ) ); //Underground
		seq22.addAll(seq2);
		seq22.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 41 ), 2, 22, 41-22 ) ); //Underground stations
		seq31.addAll(seq3);
		seq31.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
		seq51.addAll(seq5);
		seq51.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 21 ), 1, 13, 21-13 ) ); //Monument
		seq52.addAll(seq5);
		seq52.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 33 ), 2, 13, 33-13 ) ); //Monument Underground
		seq53.addAll(seq5);
		seq53.add( new PhraseOccurance( bankAndMonumentText.substring( 13, 41 ), 3, 13, 41-13 ) ); //Monument Underground station
		seq61.addAll(seq6);
		seq61.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 33 ), 1, 22, 33-22 ) ); //Underground
		seq62.addAll(seq6);
		seq62.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 41 ), 2, 22, 41-22 ) ); //Underground stations
		seq71.addAll(seq7);
		seq71.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station

		sequences.add(seq13);
		sequences.add(seq22);
		sequences.add(seq31);
		sequences.add(seq53);
		sequences.add(seq62);
		sequences.add(seq71);

		if (depth==2) {
			sequences.add(seq11);
			sequences.add(seq12);
			sequences.add(seq21);
			sequences.add(seq51);
			sequences.add(seq52);
			sequences.add(seq61);
			
			return sequences;
		} 

		seq111.addAll(seq11);
		seq111.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 33 ), 1, 22, 33-22 ) ); //Underground
		seq112.addAll(seq11);
		seq112.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 41 ), 2, 22, 41-22 ) ); //Underground stations
		seq121.addAll(seq12);
		seq121.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
		seq211.addAll(seq21);
		seq211.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
		seq511.addAll(seq51);
		seq511.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 33 ), 1, 22, 33-22 ) ); //Underground
		seq512.addAll(seq51);
		seq512.add( new PhraseOccurance( bankAndMonumentText.substring( 22, 41 ), 2, 22, 41-22 ) ); //Underground stations
		seq521.addAll(seq52);
		seq521.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
		seq611.addAll(seq61);
		seq611.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station

		sequences.add(seq112);
		sequences.add(seq121);
		sequences.add(seq211);
		sequences.add(seq512);
		sequences.add(seq521);
		sequences.add(seq611);

		if (depth==3) {
			sequences.add(seq111);
			sequences.add(seq511);
			return sequences;
		} 

		seq1111.addAll(seq111);
		seq1111.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
		seq5111.addAll(seq511);
		seq5111.add( new PhraseOccurance( bankAndMonumentText.substring( 34, 41 ), 1, 34, 41-34 ) ); //station
				
		sequences.add(seq1111);
		sequences.add(seq5111);

		return sequences;
	}
	
	protected PhraseOccuranceSet getBankAndMonumentPhrases() {
		List<PhraseOccurance> phrases = getBankAndMonumentTerms(1000);
		
		PhraseOccuranceSet set = new PhraseOccuranceSet(bankAndMonumentText, phrases);
		return set;
	}
	

	public static boolean sameElements(Collection a, Collection b) {
		if (a==b) return true;
		if (a==null || b==null) return false;
		if (a.size() != b.size()) return false;
		if (a.equals(b)) return true;
		
		for (Object x: a) {
			if (!b.contains(x)) return false;
		}
		
		return true;
	}
}
