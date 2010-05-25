package de.brightbyte.wikiword.disambig;

import java.io.IOException;
import java.util.List;
import java.util.Map;

import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.Disambiguator.Disambiguation;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;

public class SlidingCoherenceDisambiguatorTest extends DisambiguatorTestBase {

	public SlidingCoherenceDisambiguatorTest() throws IOException, PersistenceException {
		super();
	}
	
	public void testDisambiguateTerms() throws PersistenceException {
		SlidingCoherenceDisambiguator disambiguator = new SlidingCoherenceDisambiguator(meaningFetcher, featureFetcher, 10);
		disambiguator.setInitialWindow(1);
		disambiguator.setWindow(3);
		
		List<Term> sequence = terms("UK", "London", "Underground", "Bank");

		Disambiguation<Term, LocalConcept> result = disambiguator.disambiguate(sequence, null);
		
		Map<? extends Term, ? extends LocalConcept> meanings = result.getMeanings();
		
		assertNotNull( meanings.get( sequence.get(0) ) );
		assertNotNull( meanings.get( sequence.get(1) ) );
		assertNotNull( meanings.get( sequence.get(2) ) );
		assertNotNull( meanings.get( sequence.get(3) ) );
		
		assertEquals("United_Kingdom", meanings.get( sequence.get(0) ).getName() );
		assertEquals("Greater_London", meanings.get( sequence.get(1) ).getName() );
		assertEquals("London_Underground", meanings.get( sequence.get(2) ).getName() );
		assertEquals("Bank_of_England", meanings.get( sequence.get(3) ).getName() );
	}
	
	public void testDisambiguatePhraseNode() throws PersistenceException {
		PhraseOccuranceSet set = getBankAndMonumentPhrases();
		
		SlidingCoherenceDisambiguator disambiguator = new SlidingCoherenceDisambiguator(meaningFetcher, featureFetcher, 10);
		disambiguator.setTrace(traceOutput);
		disambiguator.setInitialWindow(1);
		disambiguator.setWindow(3);

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
	}

}
