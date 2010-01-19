package de.brightbyte.wikiword.rdf;

import java.util.HashSet;
import java.util.Set;

import de.brightbyte.rdf.AbstractProperties;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.LocalConceptReference;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.model.WikiWordResource;

public class LocalConceptReferenceSkosProperties<V, R extends V, A> extends AbstractProperties<V, R, A, LocalConceptReference> {

	protected SKOS<V, R> skos;
	protected WW<V, R> ww;

	protected WikiWordIdentifiers identifiers;
	
	@SuppressWarnings("unchecked")
	public LocalConceptReferenceSkosProperties(WikiWordIdentifiers identifiers, RdfPlatform<V, R, A, ?> platform) throws RdfException {
		super(platform);
		this.identifiers = identifiers;
		
		skos = platform.aquireNamespace(SKOS.class);
		ww = platform.aquireNamespace(WW.class);
	}
	
	public void addLocalProperties(LocalConceptReference concept, A about) throws RdfException {
		String name = concept.getName();
		setLiteralProperty(about, ww.displayLabel, name, (String)null);
	}
	
	public void addProperties(LocalConceptReference concept, A about) throws RdfException {
		addLocalProperties(concept, about); //////////////////////////////////
	}

}
