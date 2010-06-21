package de.brightbyte.wikiword.rdf;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.rdf.AbstractProperties;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.model.ConceptResources;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;
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

		ConceptResources<? extends WikiWordConcept> resources = concept.getResources();
		for (WikiWordResource page: resources.getResources()) {
			if (page.getType().equals(ResourceType.ARTICLE)) {
				setReferenceProperty(about, skos.definition, corpus.getURL().toString(), page.getName());
			}
		}
		
		//TODO: threshold! or at least, record freq. 
		TermReference[] tt = concept.getTerms();
		for (TermReference t: tt) {
			//if (t.getName().equals(name)) continue; //NOTE: for prefLabel
			setLiteralProperty(about, skos.altLabel, t.getTerm(), lang);
		}
		
		//TODO: idf, lhs
	}
	
	public void addProperties(LocalConcept concept, A about) throws RdfException {
		
		Corpus corpus = concept.getCorpus();
		String ucorpus = identifiers.localConceptBaseURI(corpus);

		setReferenceProperty(about, skos.inScheme, ucorpus, "");

		addLocalProperties(concept, about); //////////////////////////////////
		
		setLiteralProperty(about, ww.type, concept.getType().getName(), (String)null); //FIXME: resource!
		
		Set<WikiWordConcept> burned = new HashSet<WikiWordConcept>();
		WikiWordConcept[] broader = concept.getRelations().getBroader();
		for (WikiWordConcept c: broader) {
			setReferenceProperty(about, skos.broader, ucorpus, c.getName());
			burned.add(c);
		}
		
		WikiWordConcept[] narrower = concept.getRelations().getNarrower();
		for (WikiWordConcept c: narrower) {
			setReferenceProperty(about, skos.narrower, ucorpus, c.getName());
			burned.add(c);
		}
		
		WikiWordConcept[] similar = concept.getRelations().getSimilar();
		for (WikiWordConcept r: similar) {
			if (burned.add(r)) {
				setReferenceProperty(about, ww.similar, ucorpus, r.getName());
			}
		}
		
		//FIXME: names are null, causing exceptions! 
		WikiWordConcept[] related = concept.getRelations().getRelated();
		for (WikiWordConcept r: related) {
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
		
		WikiWordConcept[] out = concept.getRelations().getOutLinks();
		for (WikiWordConcept c: out) {
			if (!burned.contains(c)) {
				setReferenceProperty(about, ww.assoc, ucorpus, c.getName());
			}
		}
	}

}
