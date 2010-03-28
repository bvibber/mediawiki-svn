package de.brightbyte.wikiword.rdf;

import de.brightbyte.rdf.AbstractProperties;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.wikiword.model.LocalConcept;

public class LocalConceptReferenceSkosProperties<V, R extends V, A> extends AbstractProperties<V, R, A, LocalConcept> {

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
	
	public void addLocalProperties(LocalConcept concept, A about) throws RdfException {
		String name = concept.getName();
		setLiteralProperty(about, ww.displayLabel, name, (String)null);
	}
	
	public void addProperties(LocalConcept concept, A about) throws RdfException {
		addLocalProperties(concept, about); //////////////////////////////////
	}

}
