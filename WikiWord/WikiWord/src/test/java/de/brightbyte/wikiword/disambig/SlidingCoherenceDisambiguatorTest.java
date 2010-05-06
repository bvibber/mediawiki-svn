package de.brightbyte.wikiword.disambig;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.Pair;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.Disambiguator.Interpretation;
import de.brightbyte.wikiword.disambig.Disambiguator.Result;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;

public class SlidingCoherenceDisambiguatorTest extends DisambiguatorTestBase {

	private Output traceOutput = ConsoleIO.output;

	public SlidingCoherenceDisambiguatorTest() throws IOException, PersistenceException {
		super();
	}

	public void testGetSequenceInterpretations() throws PersistenceException {
		SlidingCoherenceDisambiguator disambiguator = new SlidingCoherenceDisambiguator(meaningFetcher, featureFetcher);
		
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
		SlidingCoherenceDisambiguator disambiguator = new SlidingCoherenceDisambiguator(meaningFetcher, featureFetcher);
		
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
		
		///////////////////////////////////////////////////////////////////////////////////////////////
	}
	
	public void testDisambiguatePhraseNode() throws PersistenceException {
		PhraseOccuranceSet set = getBankAndMonumentPhrases();
		
		SlidingCoherenceDisambiguator disambiguator = new SlidingCoherenceDisambiguator(meaningFetcher, featureFetcher);
		disambiguator.setTrace(traceOutput);
		disambiguator.setInitialWindow(1);
		disambiguator.setWindow(3);

		Result<PhraseOccurance, LocalConcept> result = disambiguator.disambiguate(set.getRootNode(), null);
		
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

		///////////////////////////////////////////////////////////////////////////
		disambiguator.setTrace(traceOutput);
		disambiguator.setInitialWindow(2);
		disambiguator.setWindow(3);

		result = disambiguator.disambiguate(set.getRootNode(), null);
		
		sequence = result.getSequence();
		meanings = result.getMeanings();
		
		assertEquals("Bank and Monument", sequence.get(0).getTerm());
		assertEquals("Underground", sequence.get(1).getTerm());
		assertEquals("station", sequence.get(2).getTerm());

		assertNotNull( meanings.get( sequence.get(0) ) );
		assertNotNull( meanings.get( sequence.get(1) ) );
		assertNotNull( meanings.get( sequence.get(2) ) );
		
		assertEquals("Bank_and_Monument_Underground_stations", meanings.get( sequence.get(0) ).getName() );
		assertEquals("London_Underground", meanings.get( sequence.get(1) ).getName() );
		assertEquals("Metro_station", meanings.get( sequence.get(2) ).getName() );

		///////////////////////////////////////////////////////////////////////////
		disambiguator.setTrace(traceOutput);
		disambiguator.setInitialWindow(3);
		disambiguator.setWindow(3);

		result = disambiguator.disambiguate(set.getRootNode(), null);
		
		sequence = result.getSequence();
		meanings = result.getMeanings();
		
		assertEquals("Bank and Monument", sequence.get(0).getTerm());
		assertEquals("Underground", sequence.get(1).getTerm());
		assertEquals("station", sequence.get(2).getTerm());

		assertNotNull( meanings.get( sequence.get(0) ) );
		assertNotNull( meanings.get( sequence.get(1) ) );
		assertNotNull( meanings.get( sequence.get(2) ) );
		
		assertEquals("Bank_and_Monument_Underground_stations", meanings.get( sequence.get(0) ).getName() );
		assertEquals("London_Underground", meanings.get( sequence.get(1) ).getName() );
		assertEquals("Metro_station", meanings.get( sequence.get(2) ).getName() );
		
		throw new UnsupportedOperationException("todo: window 1, 2, ...");
	}
	
	public void testDisambiguateTerms() throws PersistenceException {
		SlidingCoherenceDisambiguator disambiguator = new SlidingCoherenceDisambiguator(meaningFetcher, featureFetcher);
		disambiguator.setInitialWindow(1);
		disambiguator.setWindow(3);
		
		String[] sequence = {"UK", "London", "Underground", "Bank"};

		Result<Term, LocalConcept> result = disambiguator.disambiguate(terms(sequence), null);

		//// .............. ///
	}

}
