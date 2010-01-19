package de.brightbyte.wikiword.rdf;

import de.brightbyte.rdf.AbstractProperties;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.wikiword.model.GlobalConceptReference;

public class GlobalConceptReferenceSkosProperties<V, R extends V, A> extends AbstractProperties<V, R, A, GlobalConceptReference> {

	protected SKOS<V, R> skos;
	protected WW<V, R> ww;

	protected LocalConceptSkosProperties<V, R, A> localProps;
	protected WikiWordIdentifiers identifiers;
	
	@SuppressWarnings("unchecked")
	public GlobalConceptReferenceSkosProperties(WikiWordIdentifiers identifiers, RdfPlatform<V, R, A, ?> platform) throws RdfException {
		super(platform);
		this.identifiers = identifiers;
		
		localProps = new LocalConceptSkosProperties<V, R, A>(identifiers, platform);
		
		skos = platform.aquireNamespace(SKOS.class);
		ww = platform.aquireNamespace(WW.class);
	}
	
	public void addProperties(GlobalConceptReference concept, A about) throws RdfException {
		//noop
	}

}
