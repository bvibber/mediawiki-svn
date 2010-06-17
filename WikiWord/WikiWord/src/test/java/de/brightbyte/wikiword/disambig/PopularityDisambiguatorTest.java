package de.brightbyte.wikiword.disambig;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.Disambiguator.Disambiguation;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;
import de.brightbyte.wikiword.model.TermListNode;
import de.brightbyte.wikiword.model.TermReference;

public class PopularityDisambiguatorTest extends DisambiguatorTestBase {

	public PopularityDisambiguatorTest() throws IOException, PersistenceException {
		super();
	}
	
	public void testGetTermsForList() throws PersistenceException {
		PopularityDisambiguator<TermReference, LocalConcept> disambiguator = new PopularityDisambiguator<TermReference, LocalConcept>(meaningFetcher, 10);
		
		Term uk = new Term("UK");
		Term london = new Term("London");
		Term underground = new Term("Underground");

		ArrayList<Term> terms = new ArrayList<Term>();
		terms.add(uk);
		terms.add(london);
		terms.add(underground);

		Collection<Term> res = disambiguator.getTerms(new TermListNode<Term>(terms, 0), 1);
		assertTrue("depth 1", sameElements( terms.subList(0, 1), res) );
		
		res = disambiguator.getTerms(new TermListNode<Term>(terms, 0), 2);
		assertTrue("depth 2", sameElements( terms.subList(0, 2), res) );
		
		res = disambiguator.getTerms(new TermListNode<Term>(terms, 0), 1000);
		assertTrue("depth 1000", sameElements( terms, res) );		
	}

	public void testGetTermsForNode() throws PersistenceException {
		PhraseOccuranceSet set = getBankAndMonumentPhrases();

		//FIXME: Test case for getHorizon

		PopularityDisambiguator<TermReference, LocalConcept> disambiguator = new PopularityDisambiguator<TermReference, LocalConcept>(meaningFetcher, 10);
		
		Collection<PhraseOccurance> terms = disambiguator.getTerms(set.getRootNode(), 0);
		assertTrue("empty term set", sameElements( getBankAndMonumentTerms(0), terms) );
		
		terms = disambiguator.getTerms(set.getRootNode(), 1);
		assertTrue("terms from depth 1", sameElements( getBankAndMonumentTerms(1), terms) );
		
		terms = disambiguator.getTerms(set.getRootNode(), 1000);
		assertTrue("terms from depth 1000", sameElements( getBankAndMonumentTerms(1000), terms) );
	}
	
	public void testGetMeaningsForList() throws PersistenceException {
		PopularityDisambiguator<TermReference, LocalConcept> disambiguator = new PopularityDisambiguator<TermReference, LocalConcept>(meaningFetcher, 10);
		
		Term uk = new Term("UK");
		Term london = new Term("London");
		Term underground = new Term("Underground");

		ArrayList<Term> terms = new ArrayList<Term>();
		terms.add(uk);
		terms.add(london);
		terms.add(underground);
		
		Map<Term, List<? extends LocalConcept>> res = disambiguator.getMeanings(terms);
		
		assertEquals(uk.getTerm(), meanings.get(uk.getTerm()), res.get(uk));
		assertEquals(london.getTerm(), meanings.get(london.getTerm()), res.get(london));
		assertEquals(underground.getTerm(), meanings.get(underground.getTerm()), res.get(underground));
	}
	
	public void testGetMeaningsForNode() throws PersistenceException {
		PopularityDisambiguator<TermReference, LocalConcept> disambiguator = new PopularityDisambiguator<TermReference, LocalConcept>(meaningFetcher, 10);

		PhraseOccuranceSet set = getBankAndMonumentPhrases();
		Map<PhraseOccurance, List<? extends LocalConcept>> res = disambiguator.getMeanings(set.getRootNode());
		List<PhraseOccurance> terms = getBankAndMonumentTerms(1000);
		
		for (PhraseOccurance t: terms) {
			List<? extends LocalConcept> m = res.get(t);
			List<? extends LocalConcept> n = meanings.get(t.getTerm());
			
			assertEquals("meanings for "+t, n, m);
		}
	}
	
	public void testGetSequences() throws PersistenceException {
		PopularityDisambiguator<TermReference, LocalConcept> disambiguator = new PopularityDisambiguator<TermReference, LocalConcept>(meaningFetcher, 10);
		PhraseOccuranceSet set = getBankAndMonumentPhrases();
		
		Collection<List<PhraseOccurance>> res = disambiguator.getSequences(set.getRootNode(), 1);
		assertTrue("depth 1", sameElements(getBankAndMonumentSequences(1), res));

		res = disambiguator.getSequences(set.getRootNode(), 2);
		assertTrue("depth 2", sameElements(getBankAndMonumentSequences(2), res));

		res = disambiguator.getSequences(set.getRootNode(), 1000);
		assertTrue("depth 1000", sameElements(getBankAndMonumentSequences(1000), res));
	}
	
	public void testDisambiguateTerms() throws PersistenceException {
		PopularityDisambiguator<TermReference, LocalConcept> disambiguator = new PopularityDisambiguator<TermReference, LocalConcept>(meaningFetcher, 10);
		
		Term uk = new Term("UK");
		Term london = new Term("London");
		Term underground = new Term("Underground");
		
		List<Term> sequence = Arrays.asList(new Term[] {uk, london, underground});
		Disambiguator.Disambiguation<Term, LocalConcept> result = disambiguator.disambiguate(sequence, null);

		assertEquals("sequence", sequence, result.getSequence());
		
		assertEquals(uk.getTerm(), getConcept("United_Kingdom"), result.getMeanings().get(uk));
		assertEquals(london.getTerm(), getConcept("City_of_London"), result.getMeanings().get(london));
		assertEquals(underground.getTerm(), getConcept("London_Underground"), result.getMeanings().get(underground));
	}
	
	public void testDisambiguateNode() throws PersistenceException {
		PhraseOccuranceSet set = getBankAndMonumentPhrases();
		
		PopularityDisambiguator<TermReference, LocalConcept> disambiguator = new PopularityDisambiguator<TermReference, LocalConcept>(meaningFetcher, 10);
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
		assertEquals("Bus_station", meanings.get( sequence.get(2) ).getName() );
	}

}
