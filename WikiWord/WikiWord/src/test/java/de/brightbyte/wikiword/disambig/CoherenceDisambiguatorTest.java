package de.brightbyte.wikiword.disambig;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.LabeledMatrix;
import de.brightbyte.data.MapLabeledMatrix;
import de.brightbyte.data.Pair;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.CoherenceDisambiguator.CoherenceDisambiguation;
import de.brightbyte.wikiword.disambig.Disambiguator.Interpretation;
import de.brightbyte.wikiword.disambig.Disambiguator.Disambiguation;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;

public class CoherenceDisambiguatorTest extends DisambiguatorTestBase {

	private Output traceOutput = ConsoleIO.output;

	public CoherenceDisambiguatorTest() throws IOException, PersistenceException {
		super();
	}

	public void testGetScore() throws PersistenceException {
		CoherenceDisambiguator disambiguator = new CoherenceDisambiguator(meaningFetcher, featureFetcher, 10);
		
		LabeledMatrix<LocalConcept, LocalConcept> similarities = new MapLabeledMatrix<LocalConcept, LocalConcept>(true);
		
		LocalConcept city_of_London = getConcept("City_of_London");
		LocalConcept united_Kingdom = getConcept("United_Kingdom");
		
		//united_Kingdom.setCardinality(100000);
		
		Pair<Term, LocalConcept> uk_as_United_Kingdom = new Pair<Term, LocalConcept>(new Term("UK"), united_Kingdom);
		Pair<Term, LocalConcept> london_as_City_of_London = new Pair<Term, LocalConcept>(new Term("London"), city_of_London);

		CoherenceDisambiguator.Interpretation interp = new CoherenceDisambiguator.Interpretation(uk_as_United_Kingdom, london_as_City_of_London);
		CoherenceDisambiguation r1 = disambiguator.getScore(interp, null, similarities, featureFetcher);
		
		int oldPop = city_of_London.getCardinality();
		city_of_London.setCardinality(oldPop*2);

		CoherenceDisambiguation r2 = disambiguator.getScore(interp, null, similarities, featureFetcher);
		city_of_London.setCardinality(oldPop);
		
		double score1 = r1.getScore();
		double score2 = r2.getScore();
		assertTrue("More popularity implies better score", score1 < score2 );
	}
	
	public void testGetSequenceInterpretations() throws PersistenceException {
		CoherenceDisambiguator disambiguator = new CoherenceDisambiguator(meaningFetcher, featureFetcher, 10);
		
		Term uk = new Term("UK");
		Pair<Term, LocalConcept> uk_as_United_Kingdom = new Pair<Term, LocalConcept>(uk, getConcept("United_Kingdom"));
		Pair<Term, LocalConcept> uk_as_Great_Britain = new Pair<Term, LocalConcept>(uk, getConcept("Great_Britain"));
		Pair<Term, LocalConcept> uk_as_England = new Pair<Term, LocalConcept>(uk, getConcept("England"));
		
		Term london = new Term("London");
		Pair<Term, LocalConcept> london_as_City_of_London = new Pair<Term, LocalConcept>(london, getConcept("City_of_London"));
		Pair<Term, LocalConcept> london_as_Greater_London = new Pair<Term, LocalConcept>(london, getConcept("Greater_London"));
		Pair<Term, LocalConcept> london_as_London_city_council = new Pair<Term, LocalConcept>(london, getConcept("London_city_council"));

		List<Term> sequence = new ArrayList<Term>();
		sequence.add(uk);

		Collection<Interpretation<Term, LocalConcept>> interpretations = disambiguator.getSequenceInterpretations(sequence, meaningFetcher.getMeanings(sequence));
		
		assertEquals("number of interpretations", 3, interpretations.size());
		assertTrue("UK as United_Kingdom", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_United_Kingdom )) );
		assertTrue("UK as Great_Britain", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_Great_Britain )) );
		assertTrue("UK as England", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_England )) );

		///////////////////////////////////////////////////////////////////////////////////
		Term freak = new Term("Freak");
		Pair<Term, LocalConcept> freak_as_nothing = new Pair<Term, LocalConcept>(freak, null);
		
		sequence = new ArrayList<Term>();
		sequence.add(freak);
		sequence.add(london);

		interpretations = disambiguator.getSequenceInterpretations(sequence, meaningFetcher.getMeanings(sequence));
		
		assertEquals("number of interpretations", 3, interpretations.size());
		
		Interpretation<Term, LocalConcept> first = interpretations.iterator().next();
		
		assertEquals( first.getSequence(), sequence );
		Interpretation<Term, LocalConcept> interp = new Disambiguator.Interpretation<Term, LocalConcept>( freak_as_nothing, london_as_City_of_London );
		assertTrue("London as City_of_London", interpretations.contains( interp) );
		
		///////////////////////////////////////////////////////////////////////////////////
		
		sequence = new ArrayList<Term>();
		sequence.add(uk);
		sequence.add(london);

		interpretations = disambiguator.getSequenceInterpretations(sequence, meaningFetcher.getMeanings(sequence));
		
		assertEquals("number of interpretations", 9, interpretations.size());
		
		assertTrue("UK as United_Kingdom; London as City_of_London", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_United_Kingdom, london_as_City_of_London )) );
		assertTrue("UK as Great_Britain; London as City_of_London", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_Great_Britain, london_as_City_of_London )) );
		assertTrue("UK as England; London as City_of_London", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_England, london_as_City_of_London )) );
		
		assertTrue("UK as United_Kingdom; London as Greater_London", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_United_Kingdom, london_as_Greater_London )) );
		assertTrue("UK as Great_Britain; London as Greater_London", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_Great_Britain, london_as_Greater_London )) );
		assertTrue("UK as England; London as Greater_London", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_England, london_as_Greater_London )) );
		
		assertTrue("UK as United_Kingdom; London as London_city_council", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_United_Kingdom, london_as_London_city_council )) );
		assertTrue("UK as Great_Britain; London as London_city_council", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_Great_Britain, london_as_London_city_council )) );
		assertTrue("UK as England; London as London_city_council", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_England, london_as_London_city_council )) );
	}

	public void testGetInterpretations() throws PersistenceException {
		CoherenceDisambiguator disambiguator = new CoherenceDisambiguator(meaningFetcher, featureFetcher, 10);
		
		Term uk = new Term("UK");
		Pair<Term, LocalConcept> uk_as_United_Kingdom = new Pair<Term, LocalConcept>(uk, getConcept("United_Kingdom"));
		Pair<Term, LocalConcept> uk_as_Great_Britain = new Pair<Term, LocalConcept>(uk, getConcept("Great_Britain"));
		Pair<Term, LocalConcept> uk_as_England = new Pair<Term, LocalConcept>(uk, getConcept("England"));
		
		Term london = new Term("London");
		Pair<Term, LocalConcept> london_as_City_of_London = new Pair<Term, LocalConcept>(london, getConcept("City_of_London"));
		Pair<Term, LocalConcept> london_as_Greater_London = new Pair<Term, LocalConcept>(london, getConcept("Greater_London"));
		Pair<Term, LocalConcept> london_as_London_city_council = new Pair<Term, LocalConcept>(london, getConcept("London_city_council"));
		
		Term underground = new Term("Underground");
		Pair<Term, LocalConcept> underground_as_Subway = new Pair<Term, LocalConcept>(underground, getConcept("Subway"));
		Pair<Term, LocalConcept> underground_as_London_Undrerground = new Pair<Term, LocalConcept>(underground, getConcept("London_Underground"));
		
		List<Term> sequence = new ArrayList<Term>();
		sequence.add(uk);
		
		List<List<Term>> sequences = new ArrayList<List<Term>>();
		sequences.add(sequence);

		Collection<Interpretation<Term, LocalConcept>> interpretations = disambiguator.getInterpretations(sequences, meaningFetcher.getMeanings(sequence));
		
		assertEquals("number of interpretations", 3, interpretations.size());
		assertTrue("UK as United_Kingdom", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_United_Kingdom )) );
		assertTrue("UK as Great_Britain", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_Great_Britain )) );
		assertTrue("UK as England", interpretations.contains( new Disambiguator.Interpretation<Term, LocalConcept>( uk_as_England )) );
	}
	
	public void testDisambiguateTerms() throws PersistenceException {
		CoherenceDisambiguator disambiguator = new CoherenceDisambiguator(meaningFetcher, featureFetcher, 10);
		disambiguator.setTrace(traceOutput);
		
		List<Term> sequence = terms("UK", "London", "Underground", "Bank");

		Disambiguation<Term, LocalConcept> result = disambiguator.disambiguate(sequence, null);
		
		Map<? extends Term, ? extends LocalConcept> meanings = result.getMeanings();
		
		assertNotNull( meanings.get( sequence.get(0) ) );
		assertNotNull( meanings.get( sequence.get(1) ) );
		assertNotNull( meanings.get( sequence.get(2) ) );
		assertNotNull( meanings.get( sequence.get(3) ) );
		
		assertEquals("United_Kingdom", meanings.get( sequence.get(0) ).getName() );
		assertEquals("City_of_London", meanings.get( sequence.get(1) ).getName() );
		assertEquals("London_Underground", meanings.get( sequence.get(2) ).getName() );
		assertEquals("Bank_of_England", meanings.get( sequence.get(3) ).getName() );
	}

	public void testDisambiguatePhraseNode() throws PersistenceException {
		PhraseOccuranceSet set = getBankAndMonumentPhrases();
		
		CoherenceDisambiguator disambiguator = new CoherenceDisambiguator(meaningFetcher, featureFetcher, 10);
		disambiguator.setTrace(traceOutput);

		Disambiguation<PhraseOccurance, LocalConcept> result = disambiguator.disambiguate(set.getRootNode(), null);
		
		List<? extends PhraseOccurance> sequence = result.getSequence();
		Map<? extends PhraseOccurance, ? extends LocalConcept> meanings = result.getMeanings();
		
		assertEquals("Bank and Monument", sequence.get(0).getTerm());
		assertEquals("Underground", sequence.get(1).getTerm());
		assertEquals("station", sequence.get(2).getTerm());

		assertNotNull( meanings.get( sequence.get(0) ) );
		assertNotNull( meanings.get( sequence.get(1) ) );
		assertNotNull( meanings.get( sequence.get(2) ) );
		
		assertEquals("Bank_and_Monument_Underground_stations", meanings.get( sequence.get(0) ).getName() );
		assertEquals("London_Underground", meanings.get( sequence.get(1) ).getName() );
		assertEquals("Metro_station", meanings.get( sequence.get(2) ).getName() );
	}

}
