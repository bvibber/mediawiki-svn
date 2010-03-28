package de.brightbyte.wikiword.rdf;

import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import de.brightbyte.rdf.AbstractProperties;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.vocab.OWL;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.WikiWordConcept;

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
			Map<String, LocalConcept> local = concept.getLocalConcepts();
			for (LocalConcept lc: local.values()) {
				setReferenceProperty(about, owl.sameAs, identifiers.localConceptBaseURI(lc.getCorpus()), lc.getName());
				localProps.addLocalProperties(lc, about);
			}
		} catch (PersistenceException e) {
			throw new RdfException(e);
		}
		
		Set<WikiWordConcept> stop = new HashSet<WikiWordConcept>();
		WikiWordConcept[] broader = concept.getRelations().getBroader();
		for (WikiWordConcept c: broader) {
			if (stop.add(c)) {
				setReferenceProperty(about, skos.broader, ds, (GlobalConcept)c);
			}
		}
		
		WikiWordConcept[] narrower = concept.getRelations().getNarrower();
		for (WikiWordConcept c: narrower) {
			if (stop.add(c)) {
				setReferenceProperty(about, skos.narrower, ds, (GlobalConcept)c);
			}
		}
		
		WikiWordConcept[] similar = concept.getRelations().getSimilar();
		if (similar!=null) {
			for (WikiWordConcept c: similar) {
				if (stop.add(c)) {
					setReferenceProperty(about, ww.similar, ds, (GlobalConcept)c);
				}
			}
		}
		
		WikiWordConcept[] related = concept.getRelations().getRelated();
		if (related!=null) {
			for (WikiWordConcept c: related) {
				if (stop.add(c)) {
					setReferenceProperty(about, skos.related, ds, (GlobalConcept)c);
				}
			}
		}
		
		WikiWordConcept[] out = concept.getRelations().getOutLinks();
		for (WikiWordConcept c: out) {
			if (!stop.add(c)) {
				setReferenceProperty(about, ww.assoc, ds, (GlobalConcept)c);
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

	protected void setReferenceProperty(A about, R assoc, DatasetIdentifier dataset, GlobalConcept c) throws RdfException {
		setReferenceProperty(about, assoc, identifiers.globalConceptBaseURI(dataset), WikiWordIdentifiers.globalConceptID(c.getId()));
	}

}
