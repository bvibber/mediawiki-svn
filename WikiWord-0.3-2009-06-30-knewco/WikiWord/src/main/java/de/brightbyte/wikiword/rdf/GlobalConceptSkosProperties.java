package de.brightbyte.wikiword.rdf;

import java.util.HashSet;
import java.util.List;
import java.util.Set;

import de.brightbyte.rdf.AbstractProperties;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.vocab.OWL;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.model.GlobalConceptReference;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public class GlobalConceptSkosProperties<V, R extends V, A> extends AbstractProperties<V, R, A, GlobalConcept> {

	protected SKOS<V, R> skos;
	protected WW<V, R> ww;
	protected OWL<V, R> owl;

	protected LocalConceptSkosProperties<V, R, A> localProps;
	protected WikiWordIdentifiers identifiers;
	
	@SuppressWarnings("unchecked")
	public GlobalConceptSkosProperties(WikiWordIdentifiers identifiers, RdfPlatform<V, R, A, ?> platform) throws RdfException {
		super(platform);
		this.identifiers = identifiers;
		
		localProps = new LocalConceptSkosProperties<V, R, A>(identifiers, platform);
		
		skos = platform.aquireNamespace(SKOS.class);
		ww = platform.aquireNamespace(WW.class);
		owl = platform.aquireNamespace(OWL.class);
	}
	
	public void addProperties(GlobalConcept concept, A about) throws RdfException {
		DatasetIdentifier ds = concept.getDatasetIdentifier();
		
		setReferenceProperty(about, skos.inScheme, identifiers.datasetURI(concept.getDatasetIdentifier()), "");

		setLiteralProperty(about, ww.type, concept.getType().getName(), (String)null); //FIXME: resource!
		
		try {
			List<LocalConcept> local = concept.getLocalConcepts();
			for (LocalConcept lc: local) {
				setReferenceProperty(about, owl.sameAs, identifiers.localConceptBaseURI(lc.getCorpus()), lc.getName());
				localProps.addLocalProperties(lc, about);
			}
		} catch (PersistenceException e) {
			throw new RdfException(e);
		}
		
		Set<WikiWordConceptReference> stop = new HashSet<WikiWordConceptReference>();
		GlobalConceptReference[] broader = concept.getBroader();
		for (GlobalConceptReference c: broader) {
			if (stop.add(c)) {
				setReferenceProperty(about, skos.broader, ds, c);
			}
		}
		
		GlobalConceptReference[] narrower = concept.getNarrower();
		for (GlobalConceptReference c: narrower) {
			if (stop.add(c)) {
				setReferenceProperty(about, skos.narrower, ds, c);
			}
		}
		
		GlobalConceptReference[] similar = concept.getSimilar();
		if (similar!=null) {
			for (GlobalConceptReference c: similar) {
				if (stop.add(c)) {
					setReferenceProperty(about, ww.similar, ds, c);
				}
			}
		}
		
		GlobalConceptReference[] related = concept.getRelated();
		if (related!=null) {
			for (GlobalConceptReference c: related) {
				if (stop.add(c)) {
					setReferenceProperty(about, skos.related, ds, c);
				}
			}
		}
		
		GlobalConceptReference[] out = concept.getOutLinks();
		for (GlobalConceptReference c: out) {
			if (!stop.add(c)) {
				setReferenceProperty(about, ww.assoc, ds, c);
			}
		}
		
		/*
		GlobalConceptReference[] in = concept.getInLinks();
		for (GlobalConceptReference c: in) {
			if (!stop.add(c)) {
				setReferenceProperty(about, ww.assoc, ds, c);
			}
		}*/
	}

	protected void setReferenceProperty(A about, R assoc, DatasetIdentifier dataset, GlobalConceptReference c) throws RdfException {
		setReferenceProperty(about, assoc, identifiers.globalConceptBaseURI(dataset), WikiWordIdentifiers.globalConceptID(c.getId()));
	}

}
