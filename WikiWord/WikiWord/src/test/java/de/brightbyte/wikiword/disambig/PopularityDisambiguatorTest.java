package de.brightbyte.wikiword.disambig;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.HashSet;
import java.util.List;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.disambig.Disambiguator.Result;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseOccurance;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;
import de.brightbyte.wikiword.model.TermListNode;

public class PopularityDisambiguatorTest extends DisambiguatorTestBase {

	public PopularityDisambiguatorTest() throws IOException, PersistenceException {
		super();
	}
	
	public void testGetTermsForList() throws PersistenceException {
		PopularityDisambiguator disambiguator = new PopularityDisambiguator(meaningFetcher);
		
		Term uk = new Term("UK");
		Term london = new Term("London");
		Term underground = new Term("Underground");

		ArrayList<Term> terms = new ArrayList<Term>();
		terms.add(uk);
		terms.add(london);
		terms.add(underground);

		Collection<Term> res = disambiguator.getTerms(new TermListNode<Term>(terms, 0), 1);
		assertEquals("depth 1", new HashSet<Term>( terms.subList(0, 1) ), res);
		
		res = disambiguator.getTerms(new TermListNode<Term>(terms, 0), 2);
		assertEquals("depth 2", new HashSet<Term>( terms.subList(0, 2) ), res);
		
		res = disambiguator.getTerms(new TermListNode<Term>(terms, 0), 1000);
		assertEquals("depth 1000", new HashSet<Term>( terms ), res);		
	}

	public void testGetTermsForNode() throws PersistenceException {
		PhraseOccuranceSet set = getBankAndMonumentPhrases();

		PopularityDisambiguator disambiguator = new PopularityDisambiguator(meaningFetcher);
		Collection<PhraseOccurance> terms = disambiguator.getTerms(set.getRootNode(), 0);
		assertEquals("empty term set", Collections.emptySet(), terms);
		
		//FIXME: Test case for getHorizon
		
		terms = disambiguator.getTerms(set.getRootNode(), 1);
		assertEquals("terms from depth 1", Collections.emptySet() /* fixme */, terms);
	}
	
	public void testGetMeaningsForList() throws PersistenceException {
		PopularityDisambiguator disambiguator = new PopularityDisambiguator(meaningFetcher);
		
		Term uk = new Term("UK");
		Term london = new Term("London");
		Term underground = new Term("Underground");

		ArrayList<Term> terms = new ArrayList<Term>();
		terms.add(uk);
		terms.add(london);
		terms.add(underground);
		
		Map<Term, List<? extends LocalConcept>> meanings = disambiguator.getMeanings(terms);
		
		assertEquals(uk.getTerm(), meanings.get(uk.getTerm()), meanings.get(uk));
		assertEquals(london.getTerm(), meanings.get(london.getTerm()), meanings.get(london));
		assertEquals(underground.getTerm(), meanings.get(underground.getTerm()), meanings.get(underground));
	}
	
	public void testGetMeaningsForNode() throws PersistenceException {
		throw new UnsupportedOperationException("not yet implemented");
		//PopularityDisambiguator disambiguator = new PopularityDisambiguator(meaningFetcher);
		//disambiguator.getMeanings(terms);
	}
	
	public void testGetSequences() throws PersistenceException {
		throw new UnsupportedOperationException("not yet implemented");
		//PopularityDisambiguator disambiguator = new PopularityDisambiguator(meaningFetcher);
		//disambiguator.getSequences(root, depth);
	}
	
	public void testDisambiguateTerms() throws PersistenceException {
		throw new UnsupportedOperationException("not yet implemented");
		/*PopularityDisambiguator disambiguator = new PopularityDisambiguator(meaningFetcher);
		
		String[] sequence = {"UK", "London", "Underground", "Bank"};

		Result<Term, LocalConcept> result = disambiguator.disambiguate(terms(sequence), null);
		 */
		//// .............. ///
	}
	
	public void testDisambiguateNode() throws PersistenceException {
		throw new UnsupportedOperationException("not yet implemented");
		/*PopularityDisambiguator disambiguator = new PopularityDisambiguator(meaningFetcher);
		
		String[] sequence = {"UK", "London", "Underground", "Bank"};

		Result<Term, LocalConcept> result = disambiguator.disambiguate(terms(sequence), null);
		*/
		//// .............. ///
	}

}
