package de.brightbyte.wikiword.rdf;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.rdf.AbstractProperties;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.model.WikiWordResource;

public class LocalConceptSkosProperties<V, R extends V, A> extends AbstractProperties<V, R, A, LocalConcept> {

	protected SKOS<V, R> skos;
	protected WW<V, R> ww;

	protected WikiWordIdentifiers identifiers;
	
	@SuppressWarnings("unchecked")
	public LocalConceptSkosProperties(WikiWordIdentifiers identifiers, RdfPlatform<V, R, A, ?> platform) throws RdfException {
		super(platform);
		this.identifiers = identifiers;
		
		skos = platform.aquireNamespace(SKOS.class);
		ww = platform.aquireNamespace(WW.class);
	}
	
	public void addLocalProperties(LocalConcept concept, A about) throws RdfException {
		Corpus corpus = concept.getCorpus();
		String lang = corpus.getLanguage();
		
		String def = concept.getDefinition();

		String name = concept.getName();
		
		//setLiteralProperty(about, skos.prefLabel, name, lang);
		setLiteralProperty(about, ww.displayLabel, name, lang);

		if (def!=null) setLiteralProperty(about, skos.definition, def, lang);

		WikiWordResource page = concept.getResource();
		if (page!=null) setReferenceProperty(about, skos.definition, corpus.getURL().toString(), page.getName());
		
		//TODO: threshold! or at least, record freq. 
		TermReference[] tt = concept.getTerms();
		for (TermReference t: tt) {
			//if (t.getName().equals(name)) continue; //NOTE: for prefLabel
			setLiteralProperty(about, skos.altLabel, t.getName(), lang);
		}
		
		//TODO: idf, lhs
	}
	
	public void addProperties(LocalConcept concept, A about) throws RdfException {
		
		Corpus corpus = concept.getCorpus();
		String ucorpus = identifiers.localConceptBaseURI(corpus);

		setReferenceProperty(about, skos.inScheme, ucorpus, "");

		addLocalProperties(concept, about); //////////////////////////////////
		
		setLiteralProperty(about, ww.type, concept.getType().getName(), (String)null); //FIXME: resource!
		
		Set<WikiWordConceptReference> burned = new HashSet<WikiWordConceptReference>();
		WikiWordConceptReference[] broader = concept.getBroader();
		for (WikiWordConceptReference c: broader) {
			setReferenceProperty(about, skos.broader, ucorpus, c.getName());
			burned.add(c);
		}
		
		WikiWordConceptReference[] narrower = concept.getNarrower();
		for (WikiWordConceptReference c: narrower) {
			setReferenceProperty(about, skos.narrower, ucorpus, c.getName());
			burned.add(c);
		}
		
		WikiWordConceptReference[] similar = concept.getSimilar();
		for (WikiWordConceptReference r: similar) {
			if (burned.add(r)) {
				setReferenceProperty(about, ww.similar, ucorpus, r.getName());
			}
		}
		
		//FIXME: names are null, causing exceptions! 
		WikiWordConceptReference[] related = concept.getRelated();
		for (WikiWordConceptReference r: related) {
			if (burned.add(r)) {
				setReferenceProperty(about, skos.related, ucorpus, r.getName());
			}
		}
				
		/*WikiWordConceptReference[] in = concept.getInLinks();
		for (WikiWordConceptReference c: in) {
			if (!burned.contains(c)) {
				setReferenceProperty(about, ww.assoc, ucorpus, c.getName());
			}
		}*/
		
		WikiWordConceptReference[] out = concept.getOutLinks();
		for (WikiWordConceptReference c: out) {
			if (!burned.contains(c)) {
				setReferenceProperty(about, ww.assoc, ucorpus, c.getName());
			}
		}
	}

}
